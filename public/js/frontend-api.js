// Frontend API Client for Ge More Nutralife E-commerce
class FrontendAPI {
  constructor() {
    this.apiBase = "/api";
    this.authToken = localStorage.getItem("authToken");
    this.isLoggedIn = !!this.authToken;
    this.currentUser = null;

    this.init();
  }

  async init() {
    if (this.authToken) {
      try {
        await this.verifyAuth();
      } catch (error) {
        console.log("Auth verification failed:", error);
        this.logout();
      }
    }
  }

  async apiCall(endpoint, options = {}) {
    const defaultOptions = {
      headers: {
        "Content-Type": "application/json",
      },
    };

    if (this.authToken) {
      defaultOptions.headers["Authorization"] = `Bearer ${this.authToken}`;
    }

    const finalOptions = { ...defaultOptions, ...options };

    try {
      const response = await fetch(`${this.apiBase}${endpoint}`, finalOptions);
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || "API call failed");
      }

      return data;
    } catch (error) {
      console.error("API Error:", error);
      throw error;
    }
  }

  async verifyAuth() {
    const data = await this.apiCall("/auth/me");
    this.currentUser = data.user;
    this.isLoggedIn = true;
    this.updateUIForLoggedInUser();
    return data.user;
  }

  async login(email, password) {
    const data = await this.apiCall("/auth/login", {
      method: "POST",
      body: JSON.stringify({ email, password }),
    });

    this.authToken = data.token;
    this.currentUser = data.user;
    this.isLoggedIn = true;
    localStorage.setItem("authToken", data.token);

    this.updateUIForLoggedInUser();
    return data;
  }

  async register(userData) {
    const data = await this.apiCall("/auth/register", {
      method: "POST",
      body: JSON.stringify(userData),
    });

    this.authToken = data.token;
    this.currentUser = data.user;
    this.isLoggedIn = true;
    localStorage.setItem("authToken", data.token);

    this.updateUIForLoggedInUser();
    return data;
  }

  logout() {
    this.authToken = null;
    this.currentUser = null;
    this.isLoggedIn = false;
    localStorage.removeItem("authToken");

    this.updateUIForLoggedOutUser();
  }

  async loadProducts(filters = {}) {
    try {
      const params = new URLSearchParams(filters);
      const data = await this.apiCall(`/products?${params}`);
      return data.data;
    } catch (error) {
      console.error("Failed to load products:", error);
      return { products: [], pagination: {} };
    }
  }

  async searchProducts(query) {
    try {
      const data = await this.apiCall(
        `/products/search/query?q=${encodeURIComponent(query)}`
      );
      return data.data;
    } catch (error) {
      console.error("Failed to search products:", error);
      return [];
    }
  }

  async validateCoupon(couponCode, orderAmount) {
    try {
      const data = await this.apiCall("/orders/validate-coupon", {
        method: "POST",
        body: JSON.stringify({ couponCode, orderAmount }),
      });
      return data.data;
    } catch (error) {
      console.error("Failed to validate coupon:", error);
      throw error;
    }
  }

  async createOrder(orderData) {
    try {
      const data = await this.apiCall("/orders/create", {
        method: "POST",
        body: JSON.stringify(orderData),
      });
      return data.data;
    } catch (error) {
      console.error("Failed to create order:", error);
      throw error;
    }
  }

  async getUserOrders() {
    try {
      const data = await this.apiCall("/orders/my-orders");
      return data.data;
    } catch (error) {
      console.error("Failed to get user orders:", error);
      return { orders: [], pagination: {} };
    }
  }

  renderFeaturedProducts(products) {
    const container = document.getElementById("productSlider");
    if (!container) return;

    // Clear existing content
    container.innerHTML = "";

    // Render only first 6 products
    const limitedProducts = products.slice(0, 6);
    container.innerHTML = limitedProducts
      .map((product) => this.createProductCard(product))
      .join("");

    // Initialize product sliders
    this.initializeProductSliders();

    // Reinitialize cart event listeners
    if (window.cartManager) {
      window.cartManager.setupEventListeners();
    }
  }

  createProductCard(product) {
    const minPrice = Math.min(...product.variants.map((v) => v.price));
    const maxPrice = Math.max(...product.variants.map((v) => v.price));
    const hasDiscount = product.variants.some(
      (v) => v.originalPrice && v.originalPrice > v.price
    );
    const mainVariant = product.variants[0];

    return `
            <div class="product-slide">
                <div class="product-showcase-item" data-product-id="${
                  product._id
                }">
                    <div class="product-image-slider" data-product="${
                      product.seo.slug
                    }">
                        <div class="product-tags">
                            ${product.tags
                              .map(
                                (tag) =>
                                  `<span class="product-tag tag-${tag
                                    .toLowerCase()
                                    .replace(/\s+/g, "-")}">${tag}</span>`
                              )
                              .join("")}
                            ${
                              hasDiscount
                                ? '<span class="product-tag tag-discount">Sale</span>'
                                : ""
                            }
                        </div>
                        <button class="wishlist-btn" aria-label="Add to wishlist" data-product-id="${
                          product._id
                        }">
                            <i class="fas fa-heart"></i>
                        </button>
                        ${(mainVariant.images || product.images)
                          .map(
                            (img, index) => `
                            <img src="${img}" alt="${product.name} - View ${
                              index + 1
                            }" 
                                 class="${
                                   index === 0 ? "active" : ""
                                 }" loading="lazy" />
                        `
                          )
                          .join("")}
                        <div class="slider-nav">
                            ${(mainVariant.images || product.images)
                              .map(
                                (_, index) => `
                                <span class="slider-dot ${
                                  index === 0 ? "active" : ""
                                }"></span>
                            `
                              )
                              .join("")}
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">${product.name}</h3>
                        <p class="product-subtitle">${mainVariant.name}</p>
                        <div class="product-rating">
                            ${this.createStarRating(product.ratings.average)}
                            <span class="rating-count">(${
                              product.ratings.count
                            })</span>
                        </div>
                        <div class="product-pricing">
                            ${
                              hasDiscount
                                ? `
                                <span class="original-price" data-price="${
                                  mainVariant.originalPrice || 0
                                }">₹${(
                                    mainVariant.originalPrice || 0
                                  ).toLocaleString("en-IN")}</span>
                                <span class="discount-price" data-price="${minPrice}">₹${minPrice.toLocaleString(
                                    "en-IN"
                                  )}</span>
                            `
                                : `
                                <span class="product-price" data-price="${minPrice}">₹${minPrice.toLocaleString(
                                    "en-IN"
                                  )}</span>
                            `
                            }
                            ${
                              minPrice !== maxPrice
                                ? `<span class="price-range">- ₹${maxPrice.toLocaleString(
                                    "en-IN"
                                  )}</span>`
                                : ""
                            }
                        </div>
                        <div class="product-variants">
                            ${product.variants
                              .map(
                                (variant, index) => `
                                <label class="variant-option">
                                    <input type="radio" name="variant-${
                                      product._id
                                    }" value="${variant.size}" 
                                           data-variant-name="${variant.name}" 
                                           data-variant-price="${Number(
                                             variant.price
                                           )}"
                                           data-variant-original="${
                                             Number(variant.originalPrice) || 0
                                           }"
                                           ${index === 0 ? "checked" : ""}>
                                    <span class="variant-label">${
                                      variant.size
                                    }</span>
                                </label>
                            `
                              )
                              .join("")}
                        </div>
                        <div class="product-actions">
                            <button class="btn btn-primary add-to-cart-btn" data-product-id="${
                              product._id
                            }">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                            <button class="btn btn-secondary buy-now-btn" data-product-id="${
                              product._id
                            }">
                                Buy Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
  }

  createStarRating(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

    return `
            ${'<i class="fas fa-star"></i>'.repeat(fullStars)}
            ${hasHalfStar ? '<i class="fas fa-star-half-alt"></i>' : ""}
            ${'<i class="far fa-star"></i>'.repeat(emptyStars)}
        `;
  }

  renderCategories(categories) {
    // This can be used to populate category filters or navigation
    const categoryFilter = document.getElementById("categoryFilter");
    if (categoryFilter) {
      categoryFilter.innerHTML = `
                <option value="">All Categories</option>
                ${categories
                  .map(
                    (cat) => `<option value="${cat.slug}">${cat.name}</option>`
                  )
                  .join("")}
            `;
    }
  }

  initializeProductSliders() {
    // Initialize image sliders for products
    document.querySelectorAll(".product-image-slider").forEach((slider) => {
      const images = slider.querySelectorAll("img");
      const dots = slider.querySelectorAll(".slider-dot");
      let currentIndex = 0;

      const showImage = (index) => {
        images.forEach((img, i) => {
          img.classList.toggle("active", i === index);
        });
        dots.forEach((dot, i) => {
          dot.classList.toggle("active", i === index);
        });
        currentIndex = index;
      };

      dots.forEach((dot, index) => {
        dot.addEventListener("click", () => showImage(index));
      });

      // Auto-rotate every 3 seconds
      setInterval(() => {
        const nextIndex = (currentIndex + 1) % images.length;
        showImage(nextIndex);
      }, 3000);
    });
  }

  updateUIForLoggedInUser() {
    // Update UI elements for logged-in state
    const loginBtn = document.getElementById("loginBtn");
    const userProfile = document.getElementById("userProfile");

    if (loginBtn) loginBtn.style.display = "none";
    if (userProfile) {
      userProfile.style.display = "block";
      userProfile.querySelector(".user-name").textContent =
        this.currentUser.name;
    }
  }

  updateUIForLoggedOutUser() {
    // Update UI elements for logged-out state
    const loginBtn = document.getElementById("loginBtn");
    const userProfile = document.getElementById("userProfile");

    if (loginBtn) loginBtn.style.display = "block";
    if (userProfile) userProfile.style.display = "none";
  }

  // Utility method to show notifications
  showNotification(message, type = "success") {
    const notification = document.createElement("div");
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === "success" ? "#28a745" : "#dc3545"};
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        `;

    document.body.appendChild(notification);

    setTimeout(() => {
      notification.remove();
    }, 3000);
  }
}

// Initialize the API client
const frontendAPI = new FrontendAPI();

// Export for use in other scripts
window.frontendAPI = frontendAPI;
