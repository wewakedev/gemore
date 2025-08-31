/*
 * GeMore Nutrients - Optimized JavaScript
 * Consolidated and optimized scripts for better performance
 */

// Navigation functions
function openNav() {
  document.getElementById("myNav").style.width = "100%";
}

function closeNav() {
  document.getElementById("myNav").style.width = "0%";
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  // Navbar mobile toggle
  const toggle = document.getElementById("navbar-toggle");
  const links = document.getElementById("navbar-links");

  if (toggle && links) {
    toggle.addEventListener("click", function () {
      links.classList.toggle("active");
      toggle.classList.toggle("active");
    });
  }

  // Enhanced Contact Form Handler
  const contactForm = document.getElementById("contactForm");
  const formMessages = document.getElementById("form-messages");

  if (contactForm && formMessages) {
    contactForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const submitBtn = contactForm.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.innerHTML;

      // Show loading state
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
      submitBtn.disabled = true;
      formMessages.style.display = "none";

      try {
        const formData = new FormData(contactForm);
        const data = Object.fromEntries(formData);

        const response = await fetch("/send-contact", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data),
        });

        const result = await response.json();

        formMessages.style.display = "block";

        if (result.success) {
          formMessages.className = "alert alert-success mt-3";
          formMessages.innerHTML =
            '<i class="fas fa-check-circle"></i> ' + result.message;
          contactForm.reset();
        } else {
          formMessages.className = "alert alert-danger mt-3";
          formMessages.innerHTML =
            '<i class="fas fa-exclamation-triangle"></i> ' +
            (result.error || "Failed to send message. Please try again.");
        }
      } catch (error) {
        console.error("Form submission error:", error);
        formMessages.style.display = "block";
        formMessages.className = "alert alert-danger mt-3";
        formMessages.innerHTML =
          '<i class="fas fa-exclamation-triangle"></i> Network error. Please check your connection and try again.';
      } finally {
        // Reset button state
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;

        // Scroll to message
        formMessages.scrollIntoView({ behavior: "smooth", block: "center" });

        // Hide message after 5 seconds if success
        if (formMessages.classList.contains("alert-success")) {
          setTimeout(() => {
            formMessages.style.display = "none";
          }, 5000);
        }
      }
    });
  }

  // Smooth scrolling for anchor links
  const anchorLinks = document.querySelectorAll('a[href^="#"]');
  anchorLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      }
    });
  });

  // Lazy loading fallback for older browsers
  if ("IntersectionObserver" in window) {
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.classList.add("loaded");
          observer.unobserve(img);
        }
      });
    });

    lazyImages.forEach((img) => imageObserver.observe(img));
  }
});

// Performance optimization - minimize reflows
window.addEventListener("load", function () {
  // Add loaded class to body for CSS animations
  document.body.classList.add("loaded");
});

// Product Image Slider
document.addEventListener("DOMContentLoaded", function () {
  // Product Image Slider functionality
  const sliders = document.querySelectorAll(".product-image-slider");

  sliders.forEach((slider) => {
    const images = slider.querySelectorAll("img");
    const dots = slider.querySelectorAll(".slider-dot");
    let currentIndex = 0;

    // Initialize first image
    images[0].classList.add("active");
    dots[0].classList.add("active");

    // Hide other images
    for (let i = 1; i < images.length; i++) {
      images[i].classList.remove("active");
    }

    // Add click handlers to dots
    dots.forEach((dot, index) => {
      dot.addEventListener("click", () => {
        images[currentIndex].classList.remove("active");
        dots[currentIndex].classList.remove("active");

        currentIndex = index;

        images[currentIndex].classList.add("active");
        dots[currentIndex].classList.add("active");
      });
    });

    // Auto-rotate images - REMOVED for manual control only
  });

  // Product Slider functionality
  const slider = document.getElementById("productSlider");
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  const slides = document.querySelectorAll(".product-slide");

  if (slider && slides.length > 0) {
    let currentSlide = 0;

    // Function to get slides to show based on screen size
    function getSlidesToShow() {
      if (window.innerWidth <= 576) return 1;
      if (window.innerWidth <= 992) return 2;
      return 3; // Desktop and large tablets: 3 items
    }

    let slidesToShow = getSlidesToShow();
    const totalSlides = Math.max(1, slides.length - slidesToShow + 1);

    function updateSlider() {
      const slideWidth = 100 / slidesToShow;
      const offset = currentSlide * slideWidth;
      slider.style.transform = `translateX(-${offset}%)`;

      // Update button states
      if (prevBtn) prevBtn.style.opacity = currentSlide === 0 ? "0.5" : "1";
      if (nextBtn)
        nextBtn.style.opacity = currentSlide >= totalSlides - 1 ? "0.5" : "1";
    }

    function nextSlide() {
      if (currentSlide < totalSlides - 1) {
        currentSlide++;
        updateSlider();
      }
    }

    function prevSlide() {
      if (currentSlide > 0) {
        currentSlide--;
        updateSlider();
      }
    }

    if (nextBtn) nextBtn.addEventListener("click", nextSlide);
    if (prevBtn) prevBtn.addEventListener("click", prevSlide);

    // Update slider on window resize
    window.addEventListener("resize", () => {
      const newSlidesToShow = getSlidesToShow();
      if (newSlidesToShow !== slidesToShow) {
        slidesToShow = newSlidesToShow;
        currentSlide = 0; // Reset to first slide
        updateSlider();
      }
    });

    // Initialize slider
    updateSlider();
  }
});

// Product Variant Selection
document.addEventListener("DOMContentLoaded", function () {
  const variantOptions = document.querySelectorAll(
    '.variant-option input[type="radio"]'
  );

  variantOptions.forEach((option) => {
    option.addEventListener("change", function () {
      const productCard = this.closest(".product-showcase-item");
      const priceElement = productCard.querySelector(".product-price");
      const basePrice = this.value === "1kg" ? 2499 : 4499;

      priceElement.textContent = `₹${basePrice.toLocaleString("en-IN")}`;
    });
  });
});

// Add to Cart Animation
document.addEventListener("DOMContentLoaded", function () {
  const addToCartButtons = document.querySelectorAll(".btn-add-to-cart");

  addToCartButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const originalText = button.textContent;
      button.classList.add("added");
      button.textContent = "Added ✓";
      button.disabled = true;

      setTimeout(() => {
        button.classList.remove("added");
        button.textContent = originalText;
        button.disabled = false;
      }, 2000);
    });
  });
});

// Wishlist functionality
document.addEventListener("DOMContentLoaded", function () {
  const wishlistButtons = document.querySelectorAll(".wishlist-btn");
  const wishlistCount = document.getElementById("wishlist-count");
  let wishlist = [];

  wishlistButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const productCard = this.closest(".product-showcase-item");
      const product = {
        name: productCard.querySelector("h3").textContent,
        variant:
          productCard.querySelector(".variant-option input:checked")?.value ||
          "default",
        price: parseInt(
          productCard
            .querySelector(".product-price")
            .textContent.replace(/[^0-9]/g, "")
        ),
        image: productCard.querySelector("img.active").src,
      };

      const isInWishlist = wishlist.some(
        (item) => item.name === product.name && item.variant === product.variant
      );

      if (isInWishlist) {
        wishlist = wishlist.filter(
          (item) =>
            !(item.name === product.name && item.variant === product.variant)
        );
        this.classList.remove("active");
      } else {
        wishlist.push(product);
        this.classList.add("active");
      }

      updateWishlist();
    });
  });

  function updateWishlist() {
    // Update wishlist count
    wishlistCount.textContent = wishlist.length;

    // Update wishlist items display if sidebar exists
    const wishlistItems = document.getElementById("wishlist-items");
    if (wishlistItems) {
      wishlistItems.innerHTML = wishlist
        .map(
          (item) => `
        <div class="wishlist-item">
          <img src="${item.image}" alt="${item.name}" width="60" height="60">
          <div class="wishlist-item-details">
            <h4>${item.name}</h4>
            ${
              item.variant !== "default"
                ? `<span class="variant">${item.variant}</span>`
                : ""
            }
            <div class="price">₹${item.price.toLocaleString("en-IN")}</div>
          </div>
          <button class="remove-wishlist-item" data-name="${
            item.name
          }" data-variant="${item.variant}">×</button>
        </div>
      `
        )
        .join("");

      // Show/hide empty wishlist message
      const emptyWishlist = document.getElementById("wishlist-empty");
      if (emptyWishlist) {
        if (wishlist.length === 0) {
          emptyWishlist.style.display = "block";
          wishlistItems.style.display = "none";
        } else {
          emptyWishlist.style.display = "none";
          wishlistItems.style.display = "block";
        }
      }

      // Add event listeners to remove buttons
      document.querySelectorAll(".remove-wishlist-item").forEach((btn) => {
        btn.addEventListener("click", function () {
          const name = this.getAttribute("data-name");
          const variant = this.getAttribute("data-variant");

          // Remove from wishlist array
          wishlist = wishlist.filter(
            (item) => !(item.name === name && item.variant === variant)
          );

          // Update wishlist button state
          const productCard = document
            .querySelector(`.product-showcase-item h3:contains('${name}')`)
            .closest(".product-showcase-item");
          if (productCard) {
            productCard
              .querySelector(".wishlist-btn")
              .classList.remove("active");
          }

          updateWishlist();
        });
      });
    }
  }

  // Clear wishlist functionality
  const clearWishlist = document.getElementById("clear-wishlist");
  if (clearWishlist) {
    clearWishlist.addEventListener("click", () => {
      wishlist = [];
      document.querySelectorAll(".wishlist-btn").forEach((btn) => {
        btn.classList.remove("active");
      });
      updateWishlist();
    });
  }
});

// Cart Functionality
document.addEventListener("DOMContentLoaded", function () {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const cartToggle = document.querySelector(".cart-toggle");
  const cartSidebar = document.querySelector(".cart-sidebar");
  const closeCart = document.querySelector(".close-cart");
  const cartCount = document.querySelector(".cart-count");
  const cartContent = document.querySelector(".cart-content");
  const cartTotal = document.querySelector(".cart-total");
  const clearCartBtn = document.querySelector(".clear-cart");
  const checkoutBtn = document.querySelector(".checkout");

  // Add to Cart buttons
  document.querySelectorAll(".btn-add-to-cart").forEach((button) => {
    button.addEventListener("click", function () {
      const productCard = this.closest(".product-showcase-item");
      const product = {
        id: Date.now(), // Unique ID for cart item
        name: productCard.querySelector("h3").textContent,
        subtitle: productCard.querySelector(".product-subtitle").textContent,
        price: parseInt(
          productCard
            .querySelector(".product-price")
            .textContent.replace(/[^0-9]/g, "")
        ),
        image: productCard.querySelector("img.active").src,
        quantity: 1,
      };

      // Check for variant selection
      const variantInput = productCard.querySelector(
        'input[type="radio"]:checked'
      );
      if (variantInput) {
        product.variant = variantInput.value;
      }

      addToCart(product);
      updateCartUI();
      showCartSidebar();

      // Show added animation
      button.textContent = "Added ✓";
      button.disabled = true;
      setTimeout(() => {
        button.textContent = "Add to Cart";
        button.disabled = false;
      }, 2000);
    });
  });

  // Buy Now buttons
  document.querySelectorAll(".btn-buy-now").forEach((button) => {
    button.addEventListener("click", function () {
      const productCard = this.closest(".product-showcase-item");
      const product = {
        id: Date.now(),
        name: productCard.querySelector("h3").textContent,
        subtitle: productCard.querySelector(".product-subtitle").textContent,
        price: parseInt(
          productCard
            .querySelector(".product-price")
            .textContent.replace(/[^0-9]/g, "")
        ),
        image: productCard.querySelector("img.active").src,
        quantity: 1,
      };

      const variantInput = productCard.querySelector(
        'input[type="radio"]:checked'
      );
      if (variantInput) {
        product.variant = variantInput.value;
      }

      // Clear cart and add this item
      cart = [product];
      saveCart();
      updateCartUI();
      window.location.href = "/checkout.html";
    });
  });

  // Cart toggle
  if (cartToggle) {
    cartToggle.addEventListener("click", showCartSidebar);
  }

  // Close cart
  if (closeCart) {
    closeCart.addEventListener("click", hideCartSidebar);
  }

  // Clear cart
  if (clearCartBtn) {
    clearCartBtn.addEventListener("click", () => {
      cart = [];
      saveCart();
      updateCartUI();
    });
  }

  // Checkout
  if (checkoutBtn) {
    checkoutBtn.addEventListener("click", () => {
      window.location.href = "/checkout.html";
    });
  }

  function addToCart(product) {
    const existingItem = cart.find(
      (item) =>
        item.name === product.name &&
        (!item.variant || item.variant === product.variant)
    );

    if (existingItem) {
      existingItem.quantity++;
    } else {
      cart.push(product);
    }

    saveCart();
  }

  function removeFromCart(productId) {
    cart = cart.filter((item) => item.id !== productId);
    saveCart();
    updateCartUI();
  }

  function updateQuantity(productId, delta) {
    const item = cart.find((item) => item.id === productId);
    if (item) {
      item.quantity = Math.max(1, item.quantity + delta);
      saveCart();
      updateCartUI();
    }
  }

  function saveCart() {
    localStorage.setItem("cart", JSON.stringify(cart));
  }

  function showCartSidebar() {
    cartSidebar.classList.add("active");
    document.body.style.overflow = "hidden";
  }

  function hideCartSidebar() {
    cartSidebar.classList.remove("active");
    document.body.style.overflow = "";
  }

  function updateCartUI() {
    // Update cart count
    cartCount.textContent = cart.reduce(
      (total, item) => total + item.quantity,
      0
    );

    // Update cart content
    if (cartContent) {
      if (cart.length === 0) {
        cartContent.innerHTML = `
          <div class="empty-cart text-center py-4">
            <i class="fas fa-shopping-cart fa-3x mb-3" style="color: var(--gray);"></i>
            <p>Your cart is empty</p>
          </div>
        `;
      } else {
        cartContent.innerHTML = cart
          .map(
            (item) => `
          <div class="cart-item" data-id="${item.id}">
            <div class="cart-item-image">
              <img src="${item.image}" alt="${item.name}">
            </div>
            <div class="cart-item-details">
              <h4 class="cart-item-title">${item.name}</h4>
              <div class="cart-item-variant">${item.subtitle}${
              item.variant ? ` - ${item.variant}` : ""
            }</div>
              <div class="cart-item-price">₹${item.price.toLocaleString(
                "en-IN"
              )}</div>
              <div class="cart-item-quantity">
                <button class="quantity-btn minus" onclick="updateQuantity(${
                  item.id
                }, -1)">-</button>
                <span>${item.quantity}</span>
                <button class="quantity-btn plus" onclick="updateQuantity(${
                  item.id
                }, 1)">+</button>
                <button class="cart-item-remove" onclick="removeFromCart(${
                  item.id
                })">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
        `
          )
          .join("");
      }
    }

    // Update cart total
    if (cartTotal) {
      const subtotal = cart.reduce(
        (total, item) => total + item.price * item.quantity,
        0
      );
      const shipping = subtotal > 1500 ? 0 : 99;
      const total = subtotal + shipping;

      cartTotal.innerHTML = `
        <div class="cart-total-row">
          <span>Subtotal:</span>
          <span>₹${subtotal.toLocaleString("en-IN")}</span>
        </div>
        <div class="cart-total-row">
          <span>Shipping:</span>
          <span>₹${shipping.toLocaleString("en-IN")}</span>
        </div>
        <div class="cart-total-row final">
          <span>Total:</span>
          <span>₹${total.toLocaleString("en-IN")}</span>
        </div>
      `;
    }
  }

  // Initialize cart UI
  updateCartUI();
});

// Wishlist Functionality
document.addEventListener("DOMContentLoaded", function () {
  let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];
  const wishlistToggle = document.querySelector(".wishlist-toggle");
  const wishlistSidebar = document.querySelector(".wishlist-sidebar");
  const closeWishlist = document.querySelector(".close-wishlist");
  const wishlistCount = document.querySelector(".wishlist-count");
  const wishlistContent = document.querySelector(".wishlist-content");

  // Initialize wishlist buttons state
  document.querySelectorAll(".wishlist-btn").forEach((button) => {
    const productCard = button.closest(".product-showcase-item");
    const productName = productCard.querySelector("h3").textContent;
    const productSubtitle =
      productCard.querySelector(".product-subtitle").textContent;

    if (
      wishlist.some(
        (item) => item.name === productName && item.subtitle === productSubtitle
      )
    ) {
      button.classList.add("active");
    }

    button.addEventListener("click", function (e) {
      e.preventDefault();
      toggleWishlistItem(productCard, button);
    });
  });

  // Wishlist toggle
  if (wishlistToggle) {
    wishlistToggle.addEventListener("click", (e) => {
      e.preventDefault();
      showWishlistSidebar();
    });
  }

  // Close wishlist
  if (closeWishlist) {
    closeWishlist.addEventListener("click", hideWishlistSidebar);
  }

  function toggleWishlistItem(productCard, button) {
    const product = {
      name: productCard.querySelector("h3").textContent,
      subtitle: productCard.querySelector(".product-subtitle").textContent,
      price: parseInt(
        productCard
          .querySelector(".product-price")
          .textContent.replace(/[^0-9]/g, "")
      ),
      image: productCard.querySelector("img.active").src,
    };

    const variantInput = productCard.querySelector(
      'input[type="radio"]:checked'
    );
    if (variantInput) {
      product.variant = variantInput.value;
    }

    const existingItem = wishlist.find(
      (item) =>
        item.name === product.name &&
        (!item.variant || item.variant === product.variant)
    );

    if (existingItem) {
      wishlist = wishlist.filter(
        (item) =>
          !(
            item.name === product.name &&
            (!item.variant || item.variant === product.variant)
          )
      );
      button.classList.remove("active");
    } else {
      wishlist.push(product);
      button.classList.add("active");

      // Show added animation
      const icon = button.querySelector("i");
      icon.classList.remove("fa-heart");
      icon.classList.add("fa-heart-circle-check");
      setTimeout(() => {
        icon.classList.remove("fa-heart-circle-check");
        icon.classList.add("fa-heart");
      }, 1000);
    }

    saveWishlist();
    updateWishlistUI();
  }

  function removeFromWishlist(productName, productVariant) {
    wishlist = wishlist.filter(
      (item) =>
        !(
          item.name === productName &&
          (!item.variant || item.variant === productVariant)
        )
    );

    // Update wishlist button state in product cards
    document.querySelectorAll(".wishlist-btn").forEach((button) => {
      const productCard = button.closest(".product-showcase-item");
      const name = productCard.querySelector("h3").textContent;
      const variant = productCard.querySelector(
        'input[type="radio"]:checked'
      )?.value;

      if (name === productName && (!variant || variant === productVariant)) {
        button.classList.remove("active");
      }
    });

    saveWishlist();
    updateWishlistUI();
  }

  function saveWishlist() {
    localStorage.setItem("wishlist", JSON.stringify(wishlist));
  }

  function showWishlistSidebar() {
    wishlistSidebar.classList.add("active");
    document.body.style.overflow = "hidden";
  }

  function hideWishlistSidebar() {
    wishlistSidebar.classList.remove("active");
    document.body.style.overflow = "";
  }

  function updateWishlistUI() {
    // Update wishlist count
    if (wishlistCount) {
      wishlistCount.textContent = wishlist.length;
    }

    // Update wishlist content
    if (wishlistContent) {
      if (wishlist.length === 0) {
        wishlistContent.innerHTML = `
          <div class="wishlist-empty">
            <i class="fas fa-heart"></i>
            <p>Your wishlist is empty</p>
          </div>
        `;
      } else {
        wishlistContent.innerHTML = wishlist
          .map(
            (item) => `
          <div class="wishlist-item">
            <div class="wishlist-item-image">
              <img src="${item.image}" alt="${item.name}">
            </div>
            <div class="wishlist-item-details">
              <h4 class="wishlist-item-title">${item.name}</h4>
              <div class="wishlist-item-subtitle">${item.subtitle}${
              item.variant ? ` - ${item.variant}` : ""
            }</div>
              <div class="wishlist-item-price">₹${item.price.toLocaleString(
                "en-IN"
              )}</div>
              <div class="wishlist-item-actions">
                <button class="btn btn-secondary btn-sm add-to-cart-from-wishlist" 
                  onclick="addToCartFromWishlist('${item.name}', '${
              item.variant || ""
            }')">
                  Add to Cart
                </button>
                <button class="remove-wishlist-item" 
                  onclick="removeFromWishlist('${item.name}', '${
              item.variant || ""
            }')">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
        `
          )
          .join("");
      }
    }
  }

  // Initialize wishlist UI
  updateWishlistUI();

  // Make functions globally available
  window.removeFromWishlist = removeFromWishlist;
  window.addToCartFromWishlist = function (productName, productVariant) {
    const item = wishlist.find(
      (item) =>
        item.name === productName &&
        (!item.variant || item.variant === productVariant)
    );

    if (item) {
      addToCart({
        ...item,
        quantity: 1,
      });
      removeFromWishlist(productName, productVariant);
      updateCartUI();
    }
  };
});
