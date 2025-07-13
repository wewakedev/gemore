// Store JavaScript - E-commerce Functionality
class Store {
  constructor() {
    this.products = [];
    this.cart = JSON.parse(localStorage.getItem("cart")) || [];
    this.wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
    this.currentFilter = "all";
    this.currentSort = "default";
    this.currentStep = 1;
    this.orderData = {};

    this.init();
  }

  init() {
    this.loadProducts();
    this.setupEventListeners();
    this.updateCartCount();
    this.updateWishlistCount();
    this.renderProducts();
  }

  // Product Data
  loadProducts() {
    this.products = [
      {
        id: 1,
        name: "Nutralife Whey Protein",
        description:
          "Premium quality whey protein isolate for muscle building and recovery",
        price: 2499,
        originalPrice: 2999,
        image: "images/WHEY PROTEIN 2 KG CHOCOLATE.jpg",
        category: "protein",
        stock: 50,
        rating: 4.8,
        reviews: 256,
        badge: "Best Seller",
        flavor: "Chocolate",
        size: "2kg",
      },
      {
        id: 2,
        name: "Nutralife Pre-Workout",
        description: "Energy boost supplement for enhanced workout performance",
        price: 1299,
        originalPrice: 1599,
        image: "images/PREWORKOUT TANGY ORANGE.jpg",
        category: "preworkout",
        stock: 30,
        rating: 4.6,
        reviews: 189,
        badge: "Popular",
        flavor: "Tangy Orange",
        size: "300g",
      },
      {
        id: 3,
        name: "Nutralife Pre-Workout",
        description: "Energy boost supplement for enhanced workout performance",
        price: 1299,
        originalPrice: 1599,
        image: "images/PREWORKOUT FRUIT PUNCH.jpg",
        category: "preworkout",
        stock: 25,
        rating: 4.7,
        reviews: 145,
        badge: "New",
        flavor: "Fruit Punch",
        size: "300g",
      },
      {
        id: 4,
        name: "Nutralife Creatine",
        description: "Premium creatine monohydrate for strength and power",
        price: 899,
        originalPrice: 1199,
        image: "images/CREATINE TANGY ORANGE.jpg",
        category: "creatine",
        stock: 40,
        rating: 4.5,
        reviews: 98,
        badge: "Sale",
        flavor: "Tangy Orange",
        size: "250g",
      },
      {
        id: 5,
        name: "Nutralife Premium Kesar",
        description: "Premium kesar supplement for enhanced wellness",
        price: 1899,
        originalPrice: 2299,
        image: "images/product_kesar.png",
        category: "premium",
        stock: 15,
        rating: 4.9,
        reviews: 67,
        badge: "Premium",
        flavor: "Kesar",
        size: "500g",
      },
      {
        id: 6,
        name: "Nutralife Pure Unflavoured",
        description: "Pure unflavoured protein for versatile use",
        price: 2199,
        originalPrice: 2599,
        image: "images/product_unflavoured.png",
        category: "protein",
        stock: 20,
        rating: 4.4,
        reviews: 134,
        badge: "Pure",
        flavor: "Unflavoured",
        size: "1kg",
      },
    ];
  }

  // Event Listeners
  setupEventListeners() {
    // Filter buttons
    document.querySelectorAll(".filter-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        document
          .querySelectorAll(".filter-btn")
          .forEach((b) => b.classList.remove("active"));
        e.target.classList.add("active");
        this.currentFilter = e.target.dataset.filter;
        this.renderProducts();
      });
    });

    // Sort dropdown
    document.getElementById("sort-select").addEventListener("change", (e) => {
      this.currentSort = e.target.value;
      this.renderProducts();
    });

    // Cart toggle
    document.getElementById("cart-toggle").addEventListener("click", (e) => {
      e.preventDefault();
      this.toggleCart();
    });

    // Wishlist toggle
    document
      .getElementById("wishlist-toggle")
      .addEventListener("click", (e) => {
        e.preventDefault();
        this.toggleWishlist();
      });

    // Close buttons
    document.getElementById("close-cart").addEventListener("click", () => {
      this.closeCart();
    });

    document.getElementById("close-wishlist").addEventListener("click", () => {
      this.closeWishlist();
    });

    // Cart actions
    document.getElementById("clear-cart").addEventListener("click", () => {
      this.clearCart();
    });

    document.getElementById("checkout-btn").addEventListener("click", () => {
      this.openCheckout();
    });

    // Wishlist actions
    document.getElementById("clear-wishlist").addEventListener("click", () => {
      this.clearWishlist();
    });

    // Checkout actions
    document.getElementById("close-checkout").addEventListener("click", () => {
      this.closeCheckout();
    });

    document
      .getElementById("continue-to-payment")
      .addEventListener("click", () => {
        this.goToStep(2);
      });

    document
      .getElementById("back-to-shipping")
      .addEventListener("click", () => {
        this.goToStep(1);
      });

    document.getElementById("place-order").addEventListener("click", () => {
      this.placeOrder();
    });

    document
      .getElementById("continue-shopping")
      .addEventListener("click", () => {
        this.closeCheckout();
        this.clearCart();
      });

    // Payment method selection
    document.addEventListener("click", (e) => {
      if (e.target.closest(".payment-method")) {
        document
          .querySelectorAll(".payment-method")
          .forEach((pm) => pm.classList.remove("active"));
        e.target.closest(".payment-method").classList.add("active");
      }
    });

    // Overlay click
    document.getElementById("overlay").addEventListener("click", () => {
      this.closeCart();
      this.closeWishlist();
    });

    // Mobile nav toggle
    document.getElementById("navbar-toggle").addEventListener("click", () => {
      document.getElementById("navbar-links").classList.toggle("active");
    });
  }

  // Product Rendering
  renderProducts() {
    const container = document.getElementById("products-container");
    let filteredProducts = this.getFilteredProducts();
    let sortedProducts = this.getSortedProducts(filteredProducts);

    if (sortedProducts.length === 0) {
      container.innerHTML = `
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-box-open" style="font-size: 3rem; color: #ccc; margin-bottom: 20px;"></i>
                        <h3>No products found</h3>
                        <p>Try adjusting your filters or search criteria.</p>
                    </div>
                </div>
            `;
      return;
    }

    const productsHTML = sortedProducts
      .map((product) => this.createProductCard(product))
      .join("");
    container.innerHTML = productsHTML;

    // Add event listeners for product actions
    this.setupProductEventListeners();
  }

  getFilteredProducts() {
    if (this.currentFilter === "all") {
      return this.products;
    }
    return this.products.filter(
      (product) => product.category === this.currentFilter
    );
  }

  getSortedProducts(products) {
    switch (this.currentSort) {
      case "price-low":
        return products.sort((a, b) => a.price - b.price);
      case "price-high":
        return products.sort((a, b) => b.price - a.price);
      case "name":
        return products.sort((a, b) => a.name.localeCompare(b.name));
      case "popular":
        return products.sort((a, b) => b.rating - a.rating);
      default:
        return products;
    }
  }

  createProductCard(product) {
    const discount = Math.round(
      ((product.originalPrice - product.price) / product.originalPrice) * 100
    );
    const isInWishlist = this.wishlist.some((item) => item.id === product.id);
    const stockStatus = this.getStockStatus(product.stock);
    const rating = this.createRatingStars(product.rating);

    return `
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="product-card fade-in">
                    <div class="product-image">
                        <img src="${product.image}" alt="${product.name} - ${
      product.flavor
    } ${product.size}" loading="lazy">
                        ${
                          product.badge
                            ? `<span class="product-badge">${product.badge}</span>`
                            : ""
                        }
                        <button class="product-wishlist ${
                          isInWishlist ? "active" : ""
                        }" data-id="${product.id}">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">${product.name}</h3>
                        <p class="product-description">${
                          product.description
                        }</p>
                        <div class="rating">
                            ${rating}
                            <span class="reviews-count">(${
                              product.reviews
                            } reviews)</span>
                        </div>
                        <div class="stock-status ${stockStatus.class}">${
      stockStatus.text
    }</div>
                        <div class="product-price">
                            <span class="current-price">₹${product.price}</span>
                            ${
                              product.originalPrice > product.price
                                ? `<span class="original-price">₹${product.originalPrice}</span>`
                                : ""
                            }
                            ${
                              discount > 0
                                ? `<span class="discount-badge">${discount}% OFF</span>`
                                : ""
                            }
                        </div>
                        <div class="product-actions">
                            <button class="add-to-cart-btn" data-id="${
                              product.id
                            }" ${product.stock === 0 ? "disabled" : ""}>
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                            <button class="buy-now-btn" data-id="${
                              product.id
                            }" ${product.stock === 0 ? "disabled" : ""}>
                                <i class="fas fa-bolt"></i> Buy Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
  }

  getStockStatus(stock) {
    if (stock === 0) {
      return { class: "out-of-stock", text: "Out of Stock" };
    } else if (stock < 10) {
      return { class: "low-stock", text: `Only ${stock} left` };
    } else {
      return { class: "in-stock", text: "In Stock" };
    }
  }

  createRatingStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

    let stars = "";
    for (let i = 0; i < fullStars; i++) {
      stars += '<i class="fas fa-star"></i>';
    }
    if (hasHalfStar) {
      stars += '<i class="fas fa-star-half-alt"></i>';
    }
    for (let i = 0; i < emptyStars; i++) {
      stars += '<i class="far fa-star empty"></i>';
    }
    return stars;
  }

  setupProductEventListeners() {
    // Add to cart buttons
    document.querySelectorAll(".add-to-cart-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const productId = parseInt(e.target.dataset.id);
        this.addToCart(productId);
      });
    });

    // Buy now buttons
    document.querySelectorAll(".buy-now-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const productId = parseInt(e.target.dataset.id);
        this.addToCart(productId);
        this.openCheckout();
      });
    });

    // Wishlist buttons
    document.querySelectorAll(".product-wishlist").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const productId = parseInt(e.target.dataset.id);
        this.toggleWishlistItem(productId);
      });
    });
  }

  // Cart Management
  addToCart(productId, quantity = 1) {
    const product = this.products.find((p) => p.id === productId);
    if (!product) return;

    const existingItem = this.cart.find((item) => item.id === productId);

    if (existingItem) {
      existingItem.quantity += quantity;
    } else {
      this.cart.push({
        id: product.id,
        name: product.name,
        price: product.price,
        image: product.image,
        quantity: quantity,
        flavor: product.flavor,
        size: product.size,
      });
    }

    this.saveCart();
    this.updateCartCount();
    this.renderCart();
    this.showSuccessMessage(`${product.name} added to cart!`);
  }

  removeFromCart(productId) {
    this.cart = this.cart.filter((item) => item.id !== productId);
    this.saveCart();
    this.updateCartCount();
    this.renderCart();
  }

  updateCartQuantity(productId, quantity) {
    const item = this.cart.find((item) => item.id === productId);
    if (item) {
      item.quantity = Math.max(1, quantity);
      this.saveCart();
      this.updateCartCount();
      this.renderCart();
    }
  }

  clearCart() {
    this.cart = [];
    this.saveCart();
    this.updateCartCount();
    this.renderCart();
  }

  saveCart() {
    localStorage.setItem("cart", JSON.stringify(this.cart));
  }

  updateCartCount() {
    const count = this.cart.reduce((total, item) => total + item.quantity, 0);
    document.getElementById("cart-count").textContent = count;
  }

  renderCart() {
    const cartItems = document.getElementById("cart-items");
    const cartEmpty = document.getElementById("cart-empty");

    if (this.cart.length === 0) {
      cartItems.style.display = "none";
      cartEmpty.style.display = "block";
      this.updateCartTotals();
      return;
    }

    cartItems.style.display = "block";
    cartEmpty.style.display = "none";

    const cartHTML = this.cart
      .map(
        (item) => `
            <div class="cart-item">
                <div class="item-image">
                    <img src="${item.image}" alt="${item.name}">
                </div>
                <div class="item-info">
                    <div class="item-title">${item.name}</div>
                    <div class="item-price">₹${item.price}</div>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="store.updateCartQuantity(${
                          item.id
                        }, ${item.quantity - 1})">-</button>
                        <input type="number" class="quantity-input" value="${
                          item.quantity
                        }" min="1" onchange="store.updateCartQuantity(${
          item.id
        }, this.value)">
                        <button class="quantity-btn" onclick="store.updateCartQuantity(${
                          item.id
                        }, ${item.quantity + 1})">+</button>
                    </div>
                </div>
                <button class="remove-item" onclick="store.removeFromCart(${
                  item.id
                })">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `
      )
      .join("");

    cartItems.innerHTML = cartHTML;
    this.updateCartTotals();
  }

  updateCartTotals() {
    const subtotal = this.cart.reduce(
      (total, item) => total + item.price * item.quantity,
      0
    );
    const shipping = subtotal > 1500 ? 0 : 99;
    const total = subtotal + shipping;

    document.getElementById("cart-subtotal").textContent = subtotal;
    document.getElementById("cart-shipping").textContent = shipping;
    document.getElementById("cart-total").textContent = total;

    // Update checkout totals
    document.getElementById("order-subtotal").textContent = subtotal;
    document.getElementById("order-shipping").textContent = shipping;
    document.getElementById("order-total").textContent = total;
  }

  toggleCart() {
    const cartSidebar = document.getElementById("cart-sidebar");
    const overlay = document.getElementById("overlay");

    cartSidebar.classList.toggle("active");
    overlay.classList.toggle("active");

    if (cartSidebar.classList.contains("active")) {
      this.renderCart();
    }
  }

  closeCart() {
    document.getElementById("cart-sidebar").classList.remove("active");
    document.getElementById("overlay").classList.remove("active");
  }

  // Wishlist Management
  toggleWishlistItem(productId) {
    const product = this.products.find((p) => p.id === productId);
    if (!product) return;

    const existingIndex = this.wishlist.findIndex(
      (item) => item.id === productId
    );

    if (existingIndex > -1) {
      this.wishlist.splice(existingIndex, 1);
      this.showSuccessMessage(`${product.name} removed from wishlist!`);
    } else {
      this.wishlist.push({
        id: product.id,
        name: product.name,
        price: product.price,
        image: product.image,
        flavor: product.flavor,
        size: product.size,
      });
      this.showSuccessMessage(`${product.name} added to wishlist!`);
    }

    this.saveWishlist();
    this.updateWishlistCount();
    this.renderWishlist();
    this.renderProducts(); // Update wishlist button states
  }

  removeFromWishlist(productId) {
    this.wishlist = this.wishlist.filter((item) => item.id !== productId);
    this.saveWishlist();
    this.updateWishlistCount();
    this.renderWishlist();
    this.renderProducts();
  }

  clearWishlist() {
    this.wishlist = [];
    this.saveWishlist();
    this.updateWishlistCount();
    this.renderWishlist();
    this.renderProducts();
  }

  saveWishlist() {
    localStorage.setItem("wishlist", JSON.stringify(this.wishlist));
  }

  updateWishlistCount() {
    const count = this.wishlist.length;
    document.getElementById("wishlist-count").textContent = count;
  }

  renderWishlist() {
    const wishlistItems = document.getElementById("wishlist-items");
    const wishlistEmpty = document.getElementById("wishlist-empty");

    if (this.wishlist.length === 0) {
      wishlistItems.style.display = "none";
      wishlistEmpty.style.display = "block";
      return;
    }

    wishlistItems.style.display = "block";
    wishlistEmpty.style.display = "none";

    const wishlistHTML = this.wishlist
      .map(
        (item) => `
            <div class="wishlist-item">
                <div class="item-image">
                    <img src="${item.image}" alt="${item.name}">
                </div>
                <div class="item-info">
                    <div class="item-title">${item.name}</div>
                    <div class="item-price">₹${item.price}</div>
                    <button class="btn btn-primary" onclick="store.addToCart(${item.id})">Add to Cart</button>
                </div>
                <button class="remove-item" onclick="store.removeFromWishlist(${item.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `
      )
      .join("");

    wishlistItems.innerHTML = wishlistHTML;
  }

  toggleWishlist() {
    const wishlistSidebar = document.getElementById("wishlist-sidebar");
    const overlay = document.getElementById("overlay");

    wishlistSidebar.classList.toggle("active");
    overlay.classList.toggle("active");

    if (wishlistSidebar.classList.contains("active")) {
      this.renderWishlist();
    }
  }

  closeWishlist() {
    document.getElementById("wishlist-sidebar").classList.remove("active");
    document.getElementById("overlay").classList.remove("active");
  }

  // Checkout Process
  openCheckout() {
    if (this.cart.length === 0) {
      this.showErrorMessage("Your cart is empty!");
      return;
    }

    const checkoutModal = document.getElementById("checkout-modal");
    checkoutModal.classList.add("active");
    this.closeCart();
    this.renderOrderSummary();
  }

  closeCheckout() {
    document.getElementById("checkout-modal").classList.remove("active");
    this.currentStep = 1;
    this.goToStep(1);
  }

  goToStep(step) {
    // Validate current step before proceeding
    if (step > this.currentStep) {
      if (this.currentStep === 1 && !this.validateShippingForm()) {
        return;
      }
    }

    this.currentStep = step;

    // Update step indicators
    document.querySelectorAll(".step").forEach((stepEl, index) => {
      stepEl.classList.toggle("active", index + 1 <= step);
    });

    // Show current step content
    document
      .querySelectorAll(".checkout-step-content")
      .forEach((content, index) => {
        content.classList.toggle("active", index + 1 === step);
      });

    if (step === 2) {
      this.renderOrderSummary();
    }
  }

  validateShippingForm() {
    const form = document.getElementById("shipping-form");
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    // Check if all required fields are filled
    const requiredFields = [
      "fullName",
      "email",
      "phone",
      "address",
      "city",
      "state",
      "pincode",
      "country",
    ];
    for (let field of requiredFields) {
      if (!data[field] || data[field].trim() === "") {
        this.showErrorMessage(
          `Please fill in the ${field
            .replace(/([A-Z])/g, " $1")
            .toLowerCase()} field.`
        );
        return false;
      }
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(data.email)) {
      this.showErrorMessage("Please enter a valid email address.");
      return false;
    }

    // Validate phone number
    const phoneRegex = /^[0-9]{10}$/;
    if (!phoneRegex.test(data.phone.replace(/\D/g, ""))) {
      this.showErrorMessage("Please enter a valid 10-digit phone number.");
      return false;
    }

    // Validate pincode
    const pincodeRegex = /^[0-9]{6}$/;
    if (!pincodeRegex.test(data.pincode)) {
      this.showErrorMessage("Please enter a valid 6-digit pincode.");
      return false;
    }

    this.orderData.shipping = data;
    return true;
  }

  renderOrderSummary() {
    const orderItems = document.getElementById("order-items");
    const orderHTML = this.cart
      .map(
        (item) => `
            <div class="order-item">
                <span>${item.name} x ${item.quantity}</span>
                <span>₹${item.price * item.quantity}</span>
            </div>
        `
      )
      .join("");

    orderItems.innerHTML = orderHTML;
    this.updateCartTotals();
  }

  placeOrder() {
    // Get payment method
    const selectedPayment = document.querySelector(".payment-method.active");
    if (!selectedPayment) {
      this.showErrorMessage("Please select a payment method.");
      return;
    }

    this.orderData.payment = {
      method: selectedPayment.dataset.method,
      timestamp: new Date().toISOString(),
    };

    this.orderData.items = [...this.cart];
    this.orderData.orderNumber = this.generateOrderNumber();

    // Simulate order processing
    this.showSuccessMessage("Processing your order...");

    setTimeout(() => {
      this.goToStep(3);
      this.renderOrderConfirmation();
      this.sendOrderEmail();
    }, 2000);
  }

  generateOrderNumber() {
    return "GMN" + Date.now().toString().slice(-6);
  }

  renderOrderConfirmation() {
    const orderDetails = document.getElementById("final-order-details");
    const subtotal = this.cart.reduce(
      (total, item) => total + item.price * item.quantity,
      0
    );
    const shipping = subtotal > 1500 ? 0 : 99;
    const total = subtotal + shipping;

    orderDetails.innerHTML = `
            <div class="order-summary">
                <h4>Order #${this.orderData.orderNumber}</h4>
                <p><strong>Payment Method:</strong> ${this.getPaymentMethodName(
                  this.orderData.payment.method
                )}</p>
                <p><strong>Items:</strong> ${this.cart.length} items</p>
                <p><strong>Total Amount:</strong> ₹${total}</p>
                <p><strong>Delivery Address:</strong> ${
                  this.orderData.shipping.address
                }, ${this.orderData.shipping.city}, ${
      this.orderData.shipping.state
    } - ${this.orderData.shipping.pincode}</p>
            </div>
        `;
  }

  getPaymentMethodName(method) {
    const methods = {
      cod: "Cash on Delivery",
      upi: "UPI Payment",
      card: "Credit/Debit Card",
    };
    return methods[method] || method;
  }

  sendOrderEmail() {
    const orderData = {
      orderNumber: this.orderData.orderNumber,
      customerName: this.orderData.shipping.fullName,
      customerEmail: this.orderData.shipping.email,
      customerPhone: this.orderData.shipping.phone,
      shippingAddress: this.orderData.shipping,
      items: this.cart,
      paymentMethod: this.orderData.payment.method,
      timestamp: this.orderData.payment.timestamp,
    };

    fetch("/send-order-confirmation", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(orderData),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          console.log("Order confirmation email sent successfully");
        } else {
          console.error("Failed to send order confirmation email");
        }
      })
      .catch((error) => {
        console.error("Error sending order confirmation email:", error);
      });
  }

  // Utility functions
  showSuccessMessage(message) {
    this.showMessage(message, "success");
  }

  showErrorMessage(message) {
    this.showMessage(message, "error");
  }

  showMessage(message, type) {
    const messageDiv = document.createElement("div");
    messageDiv.className = `${type}-message`;
    messageDiv.innerHTML = `
            <i class="fas fa-${
              type === "success" ? "check-circle" : "exclamation-triangle"
            }"></i>
            <span>${message}</span>
        `;

    document.body.appendChild(messageDiv);

    setTimeout(() => {
      messageDiv.remove();
    }, 3000);
  }
}

// Initialize store when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  window.store = new Store();
});

// Export for global access
window.Store = Store;
