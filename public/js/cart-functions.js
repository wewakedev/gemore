/**
 * Shared Cart Functions for Ge More Nutralife
 * This file contains all cart-related functionality that can be used across different views
 */

// Global cart functions
window.CartFunctions = {
    /**
     * Add product to cart
     * @param {number} productId - The product ID to add
     * @param {number} quantity - Quantity to add (default: 1)
     * @param {string} variantId - Optional variant ID
     */
    addToCart: function (productId, quantity = 1, variantId = null) {
        const requestBody = { quantity: quantity };
        if (variantId) {
            requestBody.variant_id = variantId;
        }

        fetch(`/cart/add/${productId}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify(requestBody),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    this.showNotification(data.message, "success");

                    // Update all cart UI components with fresh data
                    this.refreshCartState();
                } else {
                    this.showNotification(
                        data.message || "Error adding product to cart",
                        "error"
                    );
                }
            })
            .catch((error) => {
                console.error("Error adding to cart:", error);
                this.showNotification("Error adding product to cart", "error");
            });
    },

    /**
     * Update product quantity in cart
     * @param {number} productId - The product ID to update
     * @param {number} quantity - New quantity
     */
    updateQuantity: function (productId, quantity) {
        if (quantity <= 0) {
            this.removeFromCart(productId);
            return;
        }

        fetch(`/cart/update/${productId}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify({ quantity: quantity }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    this.showNotification(data.message, "success");
                    // Update all cart UI components with fresh data
                    this.refreshCartState();
                } else {
                    this.showNotification(
                        data.message || "Error updating quantity",
                        "error"
                    );
                }
            })
            .catch((error) => {
                console.error("Error updating quantity:", error);
                this.showNotification("Error updating quantity", "error");
            });
    },

    /**
     * Remove product from cart
     * @param {number} productId - The product ID to remove
     */
    removeFromCart: function (productId) {
        fetch(`/cart/remove/${productId}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    this.showNotification(data.message, "success");
                    // Update all cart UI components with fresh data
                    this.refreshCartState();
                } else {
                    this.showNotification(
                        data.message || "Error removing item",
                        "error"
                    );
                }
            })
            .catch((error) => {
                console.error("Error removing item:", error);
                this.showNotification("Error removing item", "error");
            });
    },

    /**
     * Clear entire cart
     */
    clearCart: function () {
        if (confirm('Are you sure you want to clear your entire cart?')) {
            fetch('/cart/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showNotification(data.message, 'success');
                    this.refreshCartState();
                } else {
                    this.showNotification(data.message || 'Error clearing cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error clearing cart:', error);
                this.showNotification('Error clearing cart', 'error');
            });
        }
    },

    /**
     * Update cart count in header
     */
    updateCartCount: function () {
        this.getCartData()
            .then((data) => {
                if (data.success) {
                    this.updateCartCountWithData(data.data);
                }
            })
            .catch((error) => {
                console.error("Error fetching cart count:", error);
                // Fallback to 0 if there's an error
                const cartCountElements =
                    document.querySelectorAll(".cart-count");
                cartCountElements.forEach((element) => {
                    element.textContent = "0";
                });
            });
    },

    /**
     * Update cart count with provided data (no API call)
     */
    updateCartCountWithData: function (cartData) {
        const totalItems = cartData.item_count || 0;

        // Update cart count in header
        const cartCountElements = document.querySelectorAll(".cart-count");
        cartCountElements.forEach((element) => {
            element.textContent = totalItems;
        });
    },

    /**
     * Show notification message
     * @param {string} message - Message to display
     * @param {string} type - Type of notification (success, error, info, warning)
     */
    showNotification: function (message, type = "info") {
        // Create notification element
        const notification = document.createElement("div");
        notification.className = `alert alert-${type} notification`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease;
            background: ${this.getNotificationColor(type)};
            color: white;
            font-weight: 500;
        `;
        notification.textContent = message;

        // Add to page
        document.body.appendChild(notification);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = "slideOut 0.3s ease";
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    },

    /**
     * Get notification color based on type
     * @param {string} type - Notification type
     * @returns {string} CSS color value
     */
    getNotificationColor: function (type) {
        const colors = {
            success: "#28a745",
            error: "#dc3545",
            warning: "#ffc107",
            info: "#17a2b8",
        };
        return colors[type] || colors.info;
    },

    /**
     * Buy now functionality - add to cart and open checkout modal
     * @param {number} productId - The product ID to buy
     * @param {number} quantity - Quantity to buy (default: 1)
     */
    buyNow: function (productId, quantity = 1) {
        // First add to cart
        this.addToCart(productId, quantity);

        // Then open checkout modal after a short delay
        setTimeout(() => {
            this.openCheckoutModal();
        }, 1000);
    },

    /**
     * Open the checkout modal
     */
    openCheckoutModal: function() {
        const checkoutModal = document.getElementById('checkout-modal');
        if (checkoutModal) {
            checkoutModal.classList.add('active');
            document.body.classList.add('modal-open');
            
            // Update checkout form with current cart data
            this.updateCheckoutForm();
            
            // Set up close button event listener
            this.setupCheckoutModalEvents();
        } else {
            // Fallback: redirect to cart page
            window.location.href = '/cart';
        }
    },

    /**
     * Set up checkout modal event listeners
     */
    setupCheckoutModalEvents: function() {
        const closeBtn = document.getElementById('close-checkout');
        const checkoutModal = document.getElementById('checkout-modal');
        
        if (closeBtn) {
            closeBtn.onclick = () => this.closeCheckoutModal();
        }
        
        // Close modal when clicking outside
        if (checkoutModal) {
            checkoutModal.onclick = (e) => {
                if (e.target === checkoutModal) {
                    this.closeCheckoutModal();
                }
            };
        }
    },

    /**
     * Close the checkout modal
     */
    closeCheckoutModal: function() {
        const checkoutModal = document.getElementById('checkout-modal');
        if (checkoutModal) {
            checkoutModal.classList.remove('active');
            document.body.classList.remove('modal-open');
        }
    },

    /**
     * Update checkout form with current cart data
     */
    updateCheckoutForm: function() {
        // This will be called to populate the checkout form with cart items
        // The actual implementation depends on your checkout form structure
        this.getCartData().then(data => {
            if (data.success && data.data.items) {
                // Update order summary in checkout modal
                this.updateOrderSummary(data.data);
            }
        });
    },

    /**
     * Update order summary in checkout modal
     */
    updateOrderSummary: function(cartData) {
        const orderSummary = document.getElementById('order-summary');
        if (orderSummary && cartData.items) {
            let summaryHTML = '<h4>Order Summary</h4>';
            let subtotal = 0;
            
            cartData.items.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                summaryHTML += `
                    <div class="order-item">
                        <span class="item-name">${item.product_name}</span>
                        <span class="item-quantity">x${item.quantity}</span>
                        <span class="item-price">₹${itemTotal}</span>
                    </div>
                `;
            });
            
            summaryHTML += `
                <div class="order-total">
                    <div class="subtotal">
                        <span>Subtotal:</span>
                        <span>₹${subtotal}</span>
                    </div>
                    <div class="total">
                        <span>Total:</span>
                        <span>₹${subtotal}</span>
                    </div>
                </div>
            `;
            
            orderSummary.innerHTML = summaryHTML;
        }
    },
    // Add cached fetch for cart data
    cartDataPromise: null,
    getCartData: function () {
        if (!this.cartDataPromise) {
            this.cartDataPromise = fetch("/cart/data").then((response) =>
                response.json()
            );
        }
        return this.cartDataPromise;
    },

    /**
     * Refresh cart state - clears cache and updates all cart UI components
     */
    refreshCartState: function () {
        // Clear cached cart data to force fresh fetch
        this.cartDataPromise = null;

        // Fetch cart data once and update all components
        fetch('/cart/data')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update all cart-related UI components with the fetched data
                    this.updateCartCountWithData(data.data);
                    this.updateCartSidebarWithData(data.data);
                    this.renderProductCartButtonsWithData(data.data);

                    // Update cart page if we're on it
                    if (typeof updateCartDisplay === "function") {
                        updateCartDisplay(data.data);
                    }
                }
            })
            .catch(error => {
                console.error('Error refreshing cart state:', error);
                // Fallback to individual API calls if the unified approach fails
                this.updateCartCount();
                this.updateCartSidebar();
                this.renderProductCartButtons();
            });
    },
};

// Add function to update cart sidebar overlay
window.CartFunctions.updateCartSidebar = function () {
    this.getCartData()
        .then((data) => {
            if (data.success) {
                this.updateCartSidebarWithData(data.data);
            }
        })
        .catch((error) => console.error("Error updating cart sidebar:", error));
};

// Add function to update cart sidebar with provided data (no API call)
window.CartFunctions.updateCartSidebarWithData = function (cartData) {
    const itemsContainer = document.getElementById("cart-items");
    const emptyContainer = document.getElementById("cart-empty");
    const totalEl = document.getElementById("cart-total");
    if (!itemsContainer || !emptyContainer || !totalEl) return;
    console.log('cartData', cartData);
    if (cartData.items.length > 0) {
        emptyContainer.style.display = "none";
        itemsContainer.innerHTML = cartData.items
            .map(
                (item) =>
                    `<div class="cart-item-sidebar" data-id="${item.product_id}">
                        <div class="cart-item-image">
                            <img src="/images/${item.product.images[0] || "placeholder.svg"}" alt="${item.product.name}">
                        </div>
                        <div class="cart-item-details">
                            <div class="cart-item-title">${item.product.name}</div>
                            <div class="cart-item-subtitle">${item.product.description || ''}</div>
                            <div class="cart-item-price">₹${(item.product.default_variant?.price || item.product.price) * item.quantity}</div>
                            <div class="quantity-controls">
                                <button class="quantity-btn minus" data-id="${item.product_id}">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="quantity-display">${item.quantity}</span>
                                <button class="quantity-btn plus" data-id="${item.product_id}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <button class="remove-item" data-id="${item.product_id}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>`
            )
            .join("");
        
        // Update cart total with detailed breakdown
        const subtotal = cartData.subtotal || cartData.total;
        const shipping = cartData.shipping || 0;
        const tax = cartData.tax || Math.round(subtotal * 0.18);
        const discount = cartData.discount || 0;
        const total = cartData.total;
        
        totalEl.innerHTML = `
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>₹${subtotal}</span>
            </div>
            <div class="summary-row">
                <span>Shipping:</span>
                <span>₹${shipping}</span>
            </div>
            <div class="summary-row">
                <span>Tax:</span>
                <span>₹${tax}</span>
            </div>
            
            <div class="coupon-section-sidebar">
                <div class="coupon-input">
                    <input type="text" id="sidebar-coupon-code" placeholder="Coupon code">
                    <button class="btn btn-secondary btn-sm" id="sidebar-apply-coupon">Apply</button>
                </div>
            </div>
            <div class="summary-row total-row">
                <span><strong>Total:</strong></span>
                <span><strong>₹${total}</strong></span>
            </div>
        `;
    } else {
        itemsContainer.innerHTML = "";
        emptyContainer.style.display = "block";
        totalEl.innerHTML = `
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
};

// Add function to open cart sidebar
window.CartFunctions.openCartSidebar = function () {
    const sidebar = document.getElementById("cart-sidebar");
    sidebar.classList.toggle("active");
};

// Add function to change quantity by delta on product cards and sidebar
window.CartFunctions.changeQuantity = function (productId, change) {
    // For sidebar items, we don't need to find inputs - just make the API call directly
    const sidebarItem = document.querySelector(`.cart-item-sidebar[data-id="${productId}"]`);
    
    if (sidebarItem) {
        // Get current quantity from the display span
        const quantityDisplay = sidebarItem.querySelector('.quantity-display');
        const currentQty = quantityDisplay ? parseInt(quantityDisplay.textContent) || 0 : 0;
        const newQty = currentQty + change;
        
        if (newQty <= 0) {
            this.removeFromCart(productId);
            return;
        }
        
        // Make API call to update quantity
        this.updateQuantity(productId, newQty);
        return;
    }

    // For product listing pages and cart page - try to find input
    let input = document.querySelector(
        `.quantity-controls .quantity-input[data-product-id="${productId}"]`
    );

    // If not found, try to find input in cart page (within cart item)
    if (!input) {
        const cartItem = document.querySelector(
            `[data-product-id="${productId}"]`
        );
        if (cartItem) {
            input = cartItem.querySelector(
                ".quantity-controls .quantity-input"
            );
        }
    }

    if (!input) return;

    let current = parseInt(input.value) || 0;
    let newQty = current + change;
    if (newQty <= 0) {
        this.removeFromCart(productId);
        // Only remove quantity controls on product listing pages, not cart page
        if (input.hasAttribute("data-product-id")) {
            this.removeQuantityControls(productId);
        }
        return;
    }
    input.value = newQty;
    this.updateQuantity(productId, newQty);
};

// Add function to revert quantity controls back to Add to Cart button
window.CartFunctions.removeQuantityControls = function (productId) {
    const control = document.querySelector(
        `.quantity-controls .quantity-input[data-product-id="${productId}"]`
    );
    if (!control) return;
    const controlDiv = control.closest(".quantity-controls");
    if (!controlDiv) return;
    // Create add to cart button
    const btn = document.createElement("button");
    btn.className = "btn btn-add-to-cart";
    btn.setAttribute("onclick", `addToCart(${productId})`);
    btn.textContent = "Add to Cart";
    controlDiv.parentNode.replaceChild(btn, controlDiv);
};

// Render quantity controls for products in cart on listing pages
window.CartFunctions.renderProductCartButtons = function () {
    this.getCartData().then((data) => {
        if (data.success) {
            this.renderProductCartButtonsWithData(data.data);
        }
    });
};

// Render quantity controls with provided data (no API call)
window.CartFunctions.renderProductCartButtonsWithData = function (cartData) {
    cartData.items.forEach((item) => {
        // find add-to-cart button
        const btn = document.querySelector(
            `.btn-add-to-cart[onclick*="${item.product_id}"]`
        );
        if (btn) {
            const control = document.createElement("div");
            control.className = "quantity-controls";
            control.innerHTML = `
                    <button class="quantity-btn" onclick="CartFunctions.changeQuantity(${item.product_id}, -1)"><i class="fas fa-minus"></i></button>
                    <input type="number" class="quantity-input" data-product-id="${item.product_id}" value="${item.quantity}" min="1" onchange="CartFunctions.updateQuantity(${item.product_id}, this.value)">
                    <button class="quantity-btn" onclick="CartFunctions.changeQuantity(${item.product_id}, 1)"><i class="fas fa-plus"></i></button>`;
            btn.parentNode.replaceChild(control, btn);
        }
    });
};

// Modify addToCart to open sidebar
(function () {
    const originalAdd = window.CartFunctions.addToCart;
    window.CartFunctions.addToCart = function (productId, quantity, variantId) {
        // Call original addToCart (which now calls refreshCartState on success)
        originalAdd.call(this, productId, quantity, variantId);
        
        // Open sidebar immediately for better UX
        this.openCartSidebar();
    };
})();

// Add CSS for notifications
const cartNotificationStyle = document.createElement("style");
cartNotificationStyle.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    .notification {
        animation: slideIn 0.3s ease;
    }
`;
document.head.appendChild(cartNotificationStyle);

// Global wrapper functions for backward compatibility
window.updateQuantity = function (productId, delta) {
    // Check if this is a small delta value (likely relative change from cart page buttons)
    if (typeof delta === "number" && (delta === 1 || delta === -1)) {
        // This is a relative change from cart page buttons
        CartFunctions.changeQuantity(productId, delta);
    } else {
        // This is an absolute quantity setting
        CartFunctions.updateQuantity(productId, delta);
    }
};

window.setQuantity = function (productId, quantity) {
    CartFunctions.updateQuantity(productId, parseInt(quantity));
};

window.removeFromCart = function (productId) {
    CartFunctions.removeFromCart(productId);
};

window.addToCart = function (productId, quantity = 1, variantId = null) {
    CartFunctions.addToCart(productId, quantity, variantId);
};

window.clearCart = function () {
    CartFunctions.clearCart();
};

// Add coupon functionality for sidebar
window.CartFunctions.applySidebarCoupon = function() {
    const couponInput = document.getElementById("sidebar-coupon-code");
    if (!couponInput) return;
    
    const couponCode = couponInput.value.trim().toUpperCase();
    
    // This would typically make an API call to apply the coupon
    // For now, we'll just show a message
    if (couponCode) {
        fetch('/cart/apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ coupon_code: couponCode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification(data.message, 'success');
                this.refreshCartState();
                couponInput.value = '';
            } else {
                this.showNotification(data.message || 'Invalid coupon code', 'error');
            }
        })
        .catch(error => {
            console.error('Error applying coupon:', error);
            this.showNotification('Error applying coupon', 'error');
        });
    } else {
        this.showNotification('Please enter a coupon code', 'warning');
    }
};

// Initialize cart count on page load
document.addEventListener("DOMContentLoaded", function () {
    if (typeof CartFunctions !== "undefined") {
        CartFunctions.updateCartCount();
        CartFunctions.updateCartSidebar();
        CartFunctions.renderProductCartButtons();
        
        // Close cart sidebar
        document
            .getElementById("close-cart")
            ?.addEventListener("click", function () {
                document
                    .getElementById("cart-sidebar")
                    .classList.remove("active");
            });
            
        // Sidebar event handlers
        document.addEventListener("click", function(e) {
            // Sidebar coupon apply button
            if (e.target && e.target.id === "sidebar-apply-coupon") {
                e.preventDefault();
                CartFunctions.applySidebarCoupon();
            }
            
            // Sidebar quantity buttons
            if (e.target.matches('.cart-item-sidebar .quantity-btn.minus') || 
                e.target.closest('.cart-item-sidebar .quantity-btn.minus')) {
                e.preventDefault();
                const btn = e.target.closest('.quantity-btn.minus');
                const productId = btn.dataset.id;
                if (productId) {
                    CartFunctions.changeQuantity(productId, -1);
                }
            }
            
            if (e.target.matches('.cart-item-sidebar .quantity-btn.plus') || 
                e.target.closest('.cart-item-sidebar .quantity-btn.plus')) {
                e.preventDefault();
                const btn = e.target.closest('.quantity-btn.plus');
                const productId = btn.dataset.id;
                if (productId) {
                    CartFunctions.changeQuantity(productId, 1);
                }
            }
            
            // Sidebar remove item buttons
            if (e.target.matches('.cart-item-sidebar .remove-item') || 
                e.target.closest('.cart-item-sidebar .remove-item')) {
                e.preventDefault();
                const btn = e.target.closest('.remove-item');
                const productId = btn.dataset.id;
                if (productId) {
                    CartFunctions.removeFromCart(productId);
                }
            }
        });
        
        // Open sidebar on load if cart has items (using cached data)
        CartFunctions.getCartData().then((data) => {
            if (data.success && data.data.items.length > 0) {
                // CartFunctions.openCartSidebar();
            }
        });
        
        // Bind cart-toggle buttons to open sidebar
        document.querySelectorAll(".cart-toggle").forEach((btn) => {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                CartFunctions.updateCartSidebar();
                CartFunctions.openCartSidebar();
            });
        });
    }
});
