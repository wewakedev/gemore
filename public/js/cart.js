/*
 * GeMore Nutralife - Cart Management System
 * Complete ecommerce functionality with CRUD operations
 */

class CartManager {
  constructor() {
    this.cart = this.loadCart();
    this.coupons = {
      WELCOME10: { discount: 10, type: "percentage", minAmount: 1000 },
      SAVE20: { discount: 20, type: "percentage", minAmount: 2000 },
      FLAT100: { discount: 100, type: "fixed", minAmount: 1500 },
      NEWUSER: { discount: 15, type: "percentage", minAmount: 500 },
    };
    this.appliedCoupon = this.loadAppliedCoupon();
    this.shippingRate = 0; // Free shipping
    this.taxRate = 0.18; // 18% GST
    this.init();
  }

  init() {
    this.updateCartUI();
    this.updateCartSidebar(); // Also update sidebar
    this.setupEventListeners();
    this.updateNavCartCount();

    // Make cart manager globally available
    window.cartManager = this;
  }

  setupEventListeners() {
    // Cart page specific events
    if (document.getElementById("apply-coupon")) {
      document
        .getElementById("apply-coupon")
        .addEventListener("click", () => this.applyCoupon());
      document
        .getElementById("coupon-code")
        .addEventListener("keypress", (e) => {
          if (e.key === "Enter") this.applyCoupon();
        });
    }

    if (document.getElementById("clear-cart-btn")) {
      document
        .getElementById("clear-cart-btn")
        .addEventListener("click", () => this.clearCart());
    }

    if (document.getElementById("checkout-btn")) {
      document
        .getElementById("checkout-btn")
        .addEventListener("click", () => this.showCheckout());
    }

    // Checkout modal events
    if (document.getElementById("close-checkout")) {
      document
        .getElementById("close-checkout")
        .addEventListener("click", () => this.hideCheckout());
    }

    if (document.getElementById("back-to-cart")) {
      document
        .getElementById("back-to-cart")
        .addEventListener("click", () => this.hideCheckout());
    }

    if (document.getElementById("checkout-modal-form")) {
      document
        .getElementById("checkout-modal-form")
        .addEventListener("submit", (e) => {
          e.preventDefault();
          this.processOrder();
        });
    }

    // Variant price change listeners
    document.addEventListener("change", (e) => {
      if (e.target.matches('input[type="radio"][name*="size"]')) {
        this.updateProductPrice(e.target);
      }

      if (e.target.matches(".quantity-input")) {
        const id = e.target.dataset.id;
        const quantity = parseInt(e.target.value) || 1;
        this.updateQuantity(id, quantity);
      }
    });

    // Global cart events (for all pages)
    document.addEventListener("click", (e) => {
      if (
        e.target.matches(".btn-add-to-cart") ||
        e.target.matches(".add-to-cart-btn") ||
        e.target.closest(".add-to-cart-btn")
      ) {
        e.preventDefault();
        this.addToCartFromButton(
          e.target.closest(".add-to-cart-btn") || e.target
        );
      }

      if (e.target.matches(".btn-buy-now")) {
        e.preventDefault();
        this.buyNow(e.target);
      }

      // Quantity controls for cart page only (sidebar handled by CartFunctions)
      if (e.target.matches(".quantity-btn.minus") && !e.target.closest('.cart-sidebar')) {
        e.preventDefault();
        const productId =
          e.target.dataset.id || e.target.closest("[data-id]")?.dataset.id;
        if (productId) {
          this.updateQuantity(productId, -1);
        }
      }

      if (e.target.matches(".quantity-btn.plus") && !e.target.closest('.cart-sidebar')) {
        e.preventDefault();
        const productId =
          e.target.dataset.id || e.target.closest("[data-id]")?.dataset.id;
        if (productId) {
          this.updateQuantity(productId, 1);
        }
      }

      // Remove item from cart (cart page only, sidebar handled by CartFunctions)
      if (
        (e.target.matches(".remove-item") || e.target.closest(".remove-item")) &&
        !e.target.closest('.cart-sidebar')
      ) {
        e.preventDefault();
        this.removeFromCart(
          e.target.dataset.id || e.target.closest(".remove-item").dataset.id
        );
      }

      // Cart modal toggles
      if (
        e.target.matches(".cart-toggle") ||
        e.target.closest(".cart-toggle")
      ) {
        e.preventDefault();
        // Only show modal if we're not on the cart page
        if (!window.location.pathname.includes("cart.html")) {
          this.showCartModal();
        }
      }

      // Cart sidebar close
      if (e.target.matches("#close-cart") || e.target.matches(".close-cart")) {
        e.preventDefault();
        this.hideCartModal();
      }

      // Overlay click to close
      if (e.target.matches("#overlay")) {
        e.preventDefault();
        this.hideCartModal();
      }

      // Wishlist modal toggles
      if (
        e.target.matches(".wishlist-toggle") ||
        e.target.closest(".wishlist-toggle")
      ) {
        e.preventDefault();
        this.showWishlistModal();
      }

      // Wishlist sidebar close
      if (
        e.target.matches("#close-wishlist") ||
        e.target.matches(".close-wishlist")
      ) {
        e.preventDefault();
        this.hideWishlistModal();
      }

      // Checkout from modal
      if (
        e.target.matches("#checkout-btn") &&
        !window.location.pathname.includes("cart.html")
      ) {
        e.preventDefault();
        window.location.href = "cart.html";
      }

      // Clear cart from sidebar
      if (e.target.matches("#clear-cart")) {
        e.preventDefault();
        this.clearCart();
      }

      // Wishlist add to cart
      if (e.target.matches(".wishlist-add-to-cart")) {
        e.preventDefault();
        this.addToCartFromWishlist(e.target.dataset.id);
      }

      // Remove from wishlist
      if (e.target.matches(".remove-from-wishlist")) {
        e.preventDefault();
        this.removeFromWishlist(e.target.dataset.id);
      }

      // Sidebar coupon apply
      if (e.target.matches("#sidebar-apply-coupon")) {
        e.preventDefault();
        this.applyCouponFromSidebar();
      }
    });
  }

  // Wishlist functionality
  showWishlistModal() {
    const modal = document.getElementById("wishlist-sidebar");
    const overlay = document.getElementById("overlay");

    if (modal) {
      this.updateWishlistSidebar();
      modal.classList.add("active");
      if (overlay) overlay.style.display = "block";
      document.body.style.overflow = "hidden";
    }
  }

  hideWishlistModal() {
    const modal = document.getElementById("wishlist-sidebar");
    const overlay = document.getElementById("overlay");

    if (modal) {
      modal.classList.remove("active");
      if (overlay) overlay.style.display = "none";
      document.body.style.overflow = "";
    }
  }

  updateWishlistSidebar() {
    const wishlistContent = document.querySelector(".wishlist-content");
    if (!wishlistContent) return;

    const wishlist = JSON.parse(
      localStorage.getItem("gemore_wishlist") || "[]"
    );

    if (wishlist.length === 0) {
      wishlistContent.innerHTML = `
        <div class="empty-wishlist text-center">
          <i class="fas fa-heart empty-wishlist-icon"></i>
          <h3>Your wishlist is empty</h3>
          <p>Save products you love for later</p>
        </div>
      `;
      return;
    }

    wishlistContent.innerHTML = wishlist
      .map(
        (item) => `
      <div class="wishlist-item" data-id="${item.id}">
        <div class="wishlist-item-image">
          <img src="${item.image}" alt="${item.title}">
        </div>
        <div class="wishlist-item-details">
          <div class="wishlist-item-title">${item.title}</div>
          <div class="wishlist-item-subtitle">${item.subtitle} (${
          item.variant
        })</div>
          <div class="wishlist-item-price">₹${item.price.toLocaleString(
            "en-IN"
          )}</div>
          <div class="wishlist-item-actions">
            <button class="btn btn-primary btn-sm wishlist-add-to-cart" data-id="${
              item.id
            }">
              Add to Cart
            </button>
            <button class="btn btn-outline btn-sm remove-from-wishlist" data-id="${
              item.id
            }">
              Remove
            </button>
          </div>
        </div>
      </div>
    `
      )
      .join("");
  }

  addToCartFromWishlist(wishlistItemId) {
    const wishlist = JSON.parse(
      localStorage.getItem("gemore_wishlist") || "[]"
    );
    const item = wishlist.find((w) => w.id === wishlistItemId);

    if (item) {
      this.addToCart(item);
      this.showAddToCartMessage(item.title);
    }
  }

  removeFromWishlist(wishlistItemId) {
    let wishlist = JSON.parse(localStorage.getItem("gemore_wishlist") || "[]");
    wishlist = wishlist.filter((item) => item.id !== wishlistItemId);
    localStorage.setItem("gemore_wishlist", JSON.stringify(wishlist));
    this.updateWishlistSidebar();
    this.updateNavCartCount(); // This also updates wishlist count
  }

  // Update product price based on variant selection
  updateProductPrice(variantInput) {
    const productCard = variantInput.closest(".product-showcase-item");
    if (!productCard) return;

    const priceContainer = productCard.querySelector(".product-price");
    const productTitle = productCard.querySelector("h3").textContent;
    const variant = variantInput.value;

    // Base prices for products (1kg prices)
    const basePrices = {
      "Whey Protein": {
        "Chocolate Flavor": { original: 2999, discounted: 2499 },
        "Kesar Kulfi Flavor": { original: 2499, discounted: null },
      },
      "Pre-Workout": {
        "Tangy Orange Flavor": { original: 1799, discounted: 1499 },
        "Fruit Punch Flavor": { original: 1499, discounted: null },
      },
      Creatine: {
        "Tangy Orange Flavor": { original: 999, discounted: null },
        Unflavored: { original: 999, discounted: null },
      },
    };

    const subtitle = productCard.querySelector(".product-subtitle").textContent;
    const productInfo = basePrices[productTitle];

    if (productInfo && productInfo[subtitle]) {
      let { original, discounted } = productInfo[subtitle];

      // Adjust price for 2kg variant (1.8x multiplier)
      if (variant === "2kg") {
        original = Math.round(original * 1.8);
        if (discounted) discounted = Math.round(discounted * 1.8);
      }

      // Update price display
      if (discounted) {
        priceContainer.innerHTML = `
          <span class="original-price">₹${original.toLocaleString(
            "en-IN"
          )}</span>
          <span class="discount-price">₹${discounted.toLocaleString(
            "en-IN"
          )}</span>
        `;
      } else {
        priceContainer.innerHTML = `₹${original.toLocaleString("en-IN")}`;
      }
    }
  }

  // Product data
  getProductData(productElement) {
    const productCard = productElement.closest(".product-showcase-item");
    if (!productCard) return null;

    const imageSlider = productCard.querySelector(".product-image-slider");
    const productInfo = productCard.querySelector(".product-info");

    // Get active image
    const activeImage = imageSlider.querySelector("img.active");
    const image = activeImage ? activeImage.src : "";

    // Get product details
    const title = productInfo.querySelector("h3")?.textContent || "";
    const subtitle =
      productInfo.querySelector(".product-subtitle")?.textContent || "";

    // Get selected variant
    const selectedVariant = productInfo.querySelector(
      'input[type="radio"]:checked'
    );
    const variant = selectedVariant ? selectedVariant.value : "1kg";

    // Get price with improved logic
    let price = this.getCorrectPrice(productInfo, title, subtitle, variant);

    return {
      id: `${title.toLowerCase().replace(/\s+/g, "-")}-${subtitle
        .toLowerCase()
        .replace(/\s+/g, "-")}-${variant}`,
      title,
      subtitle,
      variant,
      price,
      image,
      quantity: 1,
    };
  }

  // Improved price calculation method
  getCorrectPrice(productInfo, title, subtitle, variant) {
    // Base prices for all products
    const productPrices = {
      "Whey Protein": {
        "Chocolate Flavor": { original: 2999, discounted: 2499 },
        "Kesar Kulfi Flavor": { original: 2499, discounted: null },
      },
      "Pre-Workout": {
        "Tangy Orange Flavor": { original: 1799, discounted: 1499 },
        "Fruit Punch Flavor": { original: 1499, discounted: null },
      },
      Creatine: {
        "Tangy Orange Flavor": { original: 999, discounted: null },
        Unflavored: { original: 999, discounted: null },
      },
    };

    // Get base price from our predefined prices
    let basePrice = 0;
    if (productPrices[title] && productPrices[title][subtitle]) {
      const priceInfo = productPrices[title][subtitle];
      basePrice = Number(priceInfo.discounted || priceInfo.original);
    } else {
      // Fallback to DOM parsing
      const discountPrice = productInfo.querySelector(".discount-price");
      const originalPrice = productInfo.querySelector(".original-price");
      const singlePrice = productInfo.querySelector(".product-price");

      if (discountPrice) {
        basePrice = this.extractPrice(discountPrice.textContent);
      } else if (originalPrice) {
        basePrice = this.extractPrice(originalPrice.textContent);
      } else if (singlePrice) {
        basePrice = this.extractPrice(singlePrice.textContent);
      }
    }

    // Ensure basePrice is a number
    basePrice = Number(basePrice) || 0;

    // Adjust for 2kg variant (typically 1.8x for whey protein, 1x for others since they don't have 2kg variants)
    if (variant === "2kg" && title === "Whey Protein") {
      basePrice = Math.round(basePrice * 1.8);
    }

    return basePrice;
  }

  extractPrice(priceText) {
    if (typeof priceText === "number") return priceText;
    const match = String(priceText).match(/₹?(\d+(?:,\d+)*)/);
    const price = match ? parseInt(match[1].replace(/,/g, "")) : 0;
    return isNaN(price) ? 0 : price;
  }

  addToCartFromButton(button) {
    const productData = this.getProductData(button);
    if (productData) {
      this.addToCart(productData);
      this.showAddToCartMessage(productData.title);
    }
  }

  addToCart(product) {
    console.log("Adding to cart:", product); // Debug log
    const existingItem = this.cart.find((item) => item.id === product.id);

    if (existingItem) {
      existingItem.quantity += product.quantity;
    } else {
      this.cart.push({ ...product });
    }

    this.saveCart();
    this.updateCartUI();
    this.updateCartSidebar();
    this.updateNavCartCount();
  }

  removeFromCart(productId) {
    this.cart = this.cart.filter((item) => item.id !== productId);
    this.saveCart();
    this.updateCartUI();
    this.updateCartSidebar();
    this.updateNavCartCount();
  }

  updateQuantity(productId, delta) {
    const item = this.cart.find((item) => item.id === productId);
    if (item) {
      const newQuantity = item.quantity + delta;
      if (newQuantity > 0) {
        item.quantity = newQuantity;
        this.saveCart();
        this.updateCartUI();
        this.updateCartSidebar();
        this.updateNavCartCount();
      } else {
        // Remove item if quantity becomes 0
        this.removeFromCart(productId);
      }
    }
  }

  increaseQuantity(productId) {
    const item = this.cart.find((item) => item.id === productId);
    if (item) {
      item.quantity++;
      this.saveCart();
      this.updateCartUI();
      this.updateCartSidebar();
      this.updateNavCartCount();
    }
  }

  decreaseQuantity(productId) {
    const item = this.cart.find((item) => item.id === productId);
    if (item && item.quantity > 1) {
      item.quantity--;
      this.saveCart();
      this.updateCartUI();
      this.updateCartSidebar();
      this.updateNavCartCount();
    }
  }

  clearCart() {
    if (confirm("Are you sure you want to clear your cart?")) {
      this.cart = [];
      this.appliedCoupon = null;
      this.saveCart();
      this.saveAppliedCoupon();
      this.updateCartUI();
      this.updateCartSidebar();
      this.updateNavCartCount();
    }
  }

  buyNow(button) {
    const productData = this.getProductData(button);
    if (productData) {
      // Clear cart and add only this product
      this.cart = [productData];
      this.saveCart();
      this.updateCartUI();
      this.updateCartSidebar();
      this.updateNavCartCount();

      // Redirect to cart page or show checkout
      if (window.location.pathname.includes("cart.html")) {
        this.showCheckout();
      } else {
        window.location.href = "cart.html";
      }
    }
  }

  calculateTotals() {
    const subtotal = this.cart.reduce(
      (sum, item) => sum + item.price * item.quantity,
      0
    );
    const shipping = subtotal > 1000 ? 0 : 100; // Free shipping above ₹1000
    const tax = Math.round(subtotal * this.taxRate);

    let discount = 0;
    if (this.appliedCoupon && this.coupons[this.appliedCoupon]) {
      const coupon = this.coupons[this.appliedCoupon];
      if (subtotal >= coupon.minAmount) {
        if (coupon.type === "percentage") {
          discount = Math.round(subtotal * (coupon.discount / 100));
        } else {
          discount = coupon.discount;
        }
      }
    }

    const total = subtotal + shipping + tax - discount;

    return { subtotal, shipping, tax, discount, total };
  }

  updateCartUI() {
    const cartContainer = document.getElementById("cart-items-container");
    const emptyCart = document.getElementById("empty-cart");

    if (!cartContainer) return;

    if (this.cart.length === 0) {
      cartContainer.style.display = "none";
      if (emptyCart) emptyCart.style.display = "block";
      this.updateCartSummary();
      return;
    }

    cartContainer.style.display = "block";
    if (emptyCart) emptyCart.style.display = "none";

    cartContainer.innerHTML = this.cart
      .map(
        (item) => `
      <div class="cart-item" data-id="${item.id}">
        <div class="cart-item-image">
          <img src="${item.image}" alt="${item.title}">
        </div>
        <div class="cart-item-details">
          <div class="cart-item-title">${item.title}</div>
          <div class="cart-item-subtitle">${item.subtitle}</div>
          <div class="cart-item-variant">${item.variant}</div>
          <div class="cart-item-price">₹${item.price.toLocaleString(
            "en-IN"
          )}</div>
          <div class="quantity-controls">
            <button class="quantity-btn minus" data-id="${item.id}">
              <i class="fas fa-minus"></i>
            </button>
            <input type="number" class="quantity-input" value="${
              item.quantity
            }" min="1" data-id="${item.id}">
            <button class="quantity-btn plus" data-id="${item.id}">
              <i class="fas fa-plus"></i>
            </button>
          </div>
        </div>
        <div class="cart-item-actions">
          <div class="cart-item-total">₹${(
            item.price * item.quantity
          ).toLocaleString("en-IN")}</div>
          <button class="remove-item" data-id="${item.id}">
            <i class="fas fa-trash"></i> Remove
          </button>
        </div>
      </div>
    `
      )
      .join("");

    this.updateCartSummary();
  }

  updateCartSummary() {
    const totals = this.calculateTotals();

    // Update summary elements
    const elements = {
      "cart-subtotal": totals.subtotal,
      "cart-shipping": totals.shipping,
      "cart-tax": totals.tax,
      "cart-discount": totals.discount,
      "cart-total": totals.total,
    };

    Object.entries(elements).forEach(([id, value]) => {
      const element = document.getElementById(id);
      if (element) {
        element.textContent = `₹${value.toLocaleString("en-IN")}`;
      }
    });

    // Show/hide discount row
    const discountRow = document.getElementById("discount-row");
    if (discountRow) {
      discountRow.style.display = totals.discount > 0 ? "flex" : "none";
    }

    // Update checkout button state
    const checkoutBtn = document.getElementById("checkout-btn");
    if (checkoutBtn) {
      checkoutBtn.disabled = this.cart.length === 0;
    }
  }

  updateNavCartCount() {
    const cartCount = document.getElementById("cart-count");
    const mobileCartCount = document.getElementById("mobile-cart-count");
    const wishlistCount = document.getElementById("wishlist-count");
    const mobileWishlistCount = document.getElementById(
      "mobile-wishlist-count"
    );

    if (cartCount || mobileCartCount) {
      const totalItems = this.cart.reduce(
        (sum, item) => sum + item.quantity,
        0
      );
      if (cartCount) cartCount.textContent = totalItems;
      if (mobileCartCount) mobileCartCount.textContent = totalItems;
    }

    // Update wishlist count too (will be handled by wishlist manager)
    const wishlistItems = JSON.parse(
      localStorage.getItem("gemore_wishlist") || "[]"
    ).length;
    if (wishlistCount) wishlistCount.textContent = wishlistItems;
    if (mobileWishlistCount) mobileWishlistCount.textContent = wishlistItems;
  }

  // Show cart modal (bring back the old functionality)
  showCartModal() {
    const modal = document.getElementById("cart-sidebar");
    const overlay = document.getElementById("overlay");

    if (modal) {
      this.updateCartSidebar();
      modal.classList.add("active");
      if (overlay) overlay.style.display = "block";
      document.body.style.overflow = "hidden";
    }
  }

  hideCartModal() {
    const modal = document.getElementById("cart-sidebar");
    const overlay = document.getElementById("overlay");

    if (modal) {
      modal.classList.remove("active");
      if (overlay) overlay.style.display = "none";
      document.body.style.overflow = "";
    }
  }

  // Update cart sidebar for modal
  updateCartSidebar() {
    const cartItems = document.getElementById("cart-items"); // This matches the HTML
    const cartEmpty = document.getElementById("cart-empty");
    const cartTotal = document.getElementById("cart-total");

    if (!cartItems) return;

    if (this.cart.length === 0) {
      if (cartEmpty) cartEmpty.style.display = "block";
      cartItems.innerHTML = "";
      if (cartTotal) {
        cartTotal.innerHTML = `
          <div class="summary-row">
            <span>Subtotal:</span>
            <span>₹0</span>
          </div>
          <div class="summary-row">
            <span>Shipping:</span>
            <span>₹0</span>
          </div>
          <div class="summary-row">
            <span>Tax:</span>
            <span>₹0</span>
          </div>
          
          <div class="coupon-section-sidebar">
            <div class="coupon-input">
              <input type="text" id="sidebar-coupon-code" placeholder="Coupon code">
              <button class="btn btn-secondary btn-sm" id="sidebar-apply-coupon">Apply</button>
            </div>
          </div>
          <div class="summary-row total-row">
            <span><strong>Total:</strong></span>
            <span><strong>₹0</strong></span>
          </div>
        `;
      }
      return;
    }

    if (cartEmpty) cartEmpty.style.display = "none";

    cartItems.innerHTML = this.cart
      .map(
        (item) => `
      <div class="cart-item-sidebar" data-id="${item.id}">
        <div class="cart-item-image">
          <img src="${item.image}" alt="${item.title}">
        </div>
        <div class="cart-item-details">
          <div class="cart-item-title">${item.title}</div>
          <div class="cart-item-subtitle">${item.subtitle} (${
          item.variant
        })</div>
          <div class="cart-item-price">₹${item.price.toLocaleString(
            "en-IN"
          )}</div>
          <div class="quantity-controls">
            <button class="quantity-btn minus" data-id="${item.id}">
              <i class="fas fa-minus"></i>
            </button>
            <span class="quantity-display">${item.quantity}</span>
            <button class="quantity-btn plus" data-id="${item.id}">
              <i class="fas fa-plus"></i>
            </button>
          </div>
        </div>
        <button class="remove-item" data-id="${item.id}">
          <i class="fas fa-times"></i>
        </button>
      </div>
    `
      )
      .join("");

    // Update cart total in sidebar
    const totals = this.calculateTotals();
    if (cartTotal) {
      cartTotal.innerHTML = `
        <div class="summary-row">
          <span>Subtotal:</span>
          <span>₹${totals.subtotal.toLocaleString("en-IN")}</span>
        </div>
        <div class="summary-row">
          <span>Shipping:</span>
          <span>₹${totals.shipping.toLocaleString("en-IN")}</span>
        </div>
        <div class="summary-row">
          <span>Tax:</span>
          <span>₹${totals.tax.toLocaleString("en-IN")}</span>
        </div>
        ${
          totals.discount > 0
            ? `
          <div class="summary-row discount-row">
            <span>Discount:</span>
            <span>-₹${totals.discount.toLocaleString("en-IN")}</span>
          </div>
        `
            : ""
        }
        <div class="coupon-section-sidebar">
          <div class="coupon-input">
            <input type="text" id="sidebar-coupon-code" placeholder="Coupon code">
            <button class="btn btn-secondary btn-sm" id="sidebar-apply-coupon">Apply</button>
          </div>
          ${
            this.appliedCoupon
              ? `<div class="applied-coupon">Applied: ${this.appliedCoupon}</div>`
              : ""
          }
        </div>
        <div class="summary-row total-row">
          <span><strong>Total:</strong></span>
          <span><strong>₹${totals.total.toLocaleString("en-IN")}</strong></span>
        </div>
      `;
    }
  }

  applyCoupon() {
    const couponInput = document.getElementById("coupon-code");
    const couponMessage = document.getElementById("coupon-message");

    if (!couponInput || !couponMessage) return;

    const couponCode = couponInput.value.trim().toUpperCase();
    const totals = this.calculateTotals();

    if (!couponCode) {
      this.showCouponMessage("Please enter a coupon code", "error");
      return;
    }

    if (this.appliedCoupon === couponCode) {
      this.showCouponMessage("This coupon is already applied", "error");
      return;
    }

    if (!this.coupons[couponCode]) {
      this.showCouponMessage("Invalid coupon code", "error");
      return;
    }

    const coupon = this.coupons[couponCode];
    if (totals.subtotal < coupon.minAmount) {
      this.showCouponMessage(
        `Minimum order amount ₹${coupon.minAmount} required`,
        "error"
      );
      return;
    }

    this.appliedCoupon = couponCode;
    this.saveAppliedCoupon();
    this.updateCartSummary();

    const discountText =
      coupon.type === "percentage"
        ? `${coupon.discount}% off`
        : `₹${coupon.discount} off`;
    this.showCouponMessage(
      `Coupon applied! You saved ${discountText}`,
      "success"
    );
    couponInput.value = "";
  }

  applyCouponFromSidebar() {
    const couponInput = document.getElementById("sidebar-coupon-code");

    if (!couponInput) return;

    const couponCode = couponInput.value.trim().toUpperCase();
    const totals = this.calculateTotals();

    if (!couponCode) {
      alert("Please enter a coupon code");
      return;
    }

    if (this.appliedCoupon === couponCode) {
      alert("This coupon is already applied");
      return;
    }

    if (!this.coupons[couponCode]) {
      alert("Invalid coupon code");
      return;
    }

    const coupon = this.coupons[couponCode];
    if (totals.subtotal < coupon.minAmount) {
      alert(`Minimum order amount ₹${coupon.minAmount} required`);
      return;
    }

    this.appliedCoupon = couponCode;
    this.saveAppliedCoupon();
    this.updateCartSidebar();

    const discountText =
      coupon.type === "percentage"
        ? `${coupon.discount}% off`
        : `₹${coupon.discount} off`;
    alert(`Coupon applied! You saved ${discountText}`);
    couponInput.value = "";
  }

  showCouponMessage(message, type) {
    const couponMessage = document.getElementById("coupon-message");
    if (couponMessage) {
      couponMessage.textContent = message;
      couponMessage.className = `coupon-message ${type}`;

      setTimeout(() => {
        couponMessage.textContent = "";
        couponMessage.className = "coupon-message";
      }, 5000);
    }
  }

  showCheckout() {
    const modal = document.getElementById("checkout-modal-form");
    const overlay = document.getElementById("overlay");

    if (modal && this.cart.length > 0) {
      this.updateCheckoutSummary();
      modal.classList.add("active");
      if (overlay) overlay.style.display = "block";
      document.body.style.overflow = "hidden";
    }
  }

  hideCheckout() {
    const modal = document.getElementById("checkout-modal-form");
    const overlay = document.getElementById("overlay");

    if (modal) {
      modal.classList.remove("active");
      if (overlay) overlay.style.display = "none";
      document.body.style.overflow = "";
    }
  }

  updateCheckoutSummary() {
    const checkoutSummary = document.getElementById("checkout-summary");
    if (!checkoutSummary) return;

    const totals = this.calculateTotals();

    checkoutSummary.innerHTML = `
      <div class="summary-row">
        <span>Items (${this.cart.reduce(
          (sum, item) => sum + item.quantity,
          0
        )}):</span>
        <span>₹${totals.subtotal.toLocaleString("en-IN")}</span>
      </div>
      <div class="summary-row">
        <span>Shipping:</span>
        <span>₹${totals.shipping.toLocaleString("en-IN")}</span>
      </div>
      <div class="summary-row">
        <span>Tax (GST):</span>
        <span>₹${totals.tax.toLocaleString("en-IN")}</span>
      </div>
      ${
        totals.discount > 0
          ? `
        <div class="summary-row" style="color: #28a745;">
          <span>Discount:</span>
          <span>-₹${totals.discount.toLocaleString("en-IN")}</span>
        </div>
      `
          : ""
      }
      <hr>
      <div class="summary-row" style="font-weight: 600; font-size: 16px;">
        <span>Total:</span>
        <span>₹${totals.total.toLocaleString("en-IN")}</span>
      </div>
    `;
  }

  async processOrder() {
    const form = document.getElementById("checkout-modal-form");
    const formData = new FormData(form);
    const orderData = Object.fromEntries(formData);

    // Add cart and totals to order data
    orderData.items = this.cart;
    orderData.totals = this.calculateTotals();
    orderData.appliedCoupon = this.appliedCoupon;
    orderData.orderDate = new Date().toISOString();
    orderData.orderId = this.generateOrderId();

    try {
      // Show loading
      const placeOrderBtn = document.getElementById("place-order-modal");
      const originalText = placeOrderBtn.innerHTML;
      placeOrderBtn.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Processing...';
      placeOrderBtn.disabled = true;

      // Send order confirmation email
      const response = await fetch("/send-order-confirmation", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(orderData),
      });

      if (response.ok) {
        // Clear cart after successful order
        this.cart = [];
        this.appliedCoupon = null;
        this.saveCart();
        this.saveAppliedCoupon();

        // Show success message
        alert(
          `Order placed successfully! Order ID: ${orderData.orderId}\nYou will receive a confirmation email shortly.`
        );

        // Redirect to home page
        window.location.href = "index.html";
      } else {
        throw new Error("Failed to process order");
      }
    } catch (error) {
      console.error("Order processing error:", error);
      alert("There was an error processing your order. Please try again.");
    } finally {
      // Reset button
      const placeOrderBtn = document.getElementById("place-order");
      placeOrderBtn.innerHTML = "Place Order";
      placeOrderBtn.disabled = false;
    }
  }

  generateOrderId() {
    const timestamp = Date.now();
    const random = Math.random().toString(36).substr(2, 9);
    return `GN${timestamp}${random}`.toUpperCase();
  }

  showAddToCartMessage(productName) {
    // Simple notification - you can enhance this with a better notification system
    const message = document.createElement("div");
    message.className = "add-to-cart-notification";
    message.innerHTML = `
      <i class="fas fa-check-circle"></i>
      "${productName}" added to cart!
    `;
    message.style.cssText = `
      position: fixed;
      top: 100px;
      right: 20px;
      background: #28a745;
      color: white;
      padding: 12px 20px;
      border-radius: 4px;
      z-index: 1000;
      animation: slideIn 0.3s ease;
    `;

    document.body.appendChild(message);

    setTimeout(() => {
      message.remove();
    }, 3000);
  }

  // Storage methods
  saveCart() {
    localStorage.setItem("gemore_cart", JSON.stringify(this.cart));
  }

  loadCart() {
    try {
      return JSON.parse(localStorage.getItem("gemore_cart")) || [];
    } catch {
      return [];
    }
  }

  saveAppliedCoupon() {
    localStorage.setItem("gemore_applied_coupon", this.appliedCoupon || "");
  }

  loadAppliedCoupon() {
    return localStorage.getItem("gemore_applied_coupon") || null;
  }
}

// Initialize cart manager when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  window.cartManager = new CartManager();
});

// Add CSS for notification animation
const style = document.createElement("style");
style.textContent = `
  @keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
  }
`;
document.head.appendChild(style);
