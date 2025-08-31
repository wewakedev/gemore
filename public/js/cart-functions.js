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
     * @param {string} variantId - Optional variant ID
     */
    updateQuantity: function (productId, quantity, variantId = null) {
        if (quantity <= 0) {
            this.removeFromCart(productId, variantId);
            return;
        }

        // Convert string "null" to actual null
        if (variantId === "null" || variantId === "") {
            variantId = null;
        }

        const requestBody = { quantity: quantity };
        if (variantId) {
            requestBody.variant_id = variantId;
        }

        fetch(`/cart/update/${productId}`, {
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
     * @param {string} variantId - Optional variant ID
     */
    removeFromCart: function (productId, variantId = null) {
        // Convert string "null" to actual null
        if (variantId === "null" || variantId === "") {
            variantId = null;
        }

        const requestBody = {};
        if (variantId) {
            requestBody.variant_id = variantId;
        }

        fetch(`/cart/remove/${productId}`, {
            method: "DELETE",
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
     * @param {string} variantId - Optional variant ID
     */
    buyNow: function (productId, quantity = 1, variantId = null) {
        // First add to cart
        this.addToCart(productId, quantity, variantId);

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
        // Clear cache to get fresh data
        this.cartDataPromise = null;
        
        // Fetch fresh cart data with totals
        fetch('/cart/data')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    // Update order summary in checkout modal
                    this.updateOrderSummary(data.data);
                } else {
                    console.error('Failed to load cart data for checkout:', data);
                }
            })
            .catch(error => {
                console.error('Error fetching cart data for checkout:', error);
            });
    },

    /**
     * Update order summary in checkout modal
     */
    updateOrderSummary: function(cartData) {
        this.updateCheckoutItems(cartData);
        this.updateCheckoutTotals(cartData.totals || {});
    },

    /**
     * Update checkout items list
     */
    updateCheckoutItems: function(cartData) {
        const orderItemsContainer = document.getElementById('checkout-order-items');
        if (!orderItemsContainer || !cartData.items) return;

        if (cartData.items.length === 0) {
            orderItemsContainer.innerHTML = '<p class="text-muted">No items in cart</p>';
            return;
        }

        let itemsHTML = '';
        cartData.items.forEach(item => {
            const itemPrice = item.product?.default_variant?.price || item.product?.price || 0;
            const itemTotal = itemPrice * item.quantity;
            
            itemsHTML += `
                <div class="checkout-item">
                    <div class="item-info">
                        <span class="item-name">${item.product?.name || 'Product'}</span>
                        <span class="item-quantity">Qty: ${item.quantity}</span>
                    </div>
                    <span class="item-total">₹${itemTotal.toFixed(2)}</span>
                </div>
            `;
        });
        
        orderItemsContainer.innerHTML = itemsHTML;
    },

    /**
     * Update checkout totals with coupon information
     */
    updateCheckoutTotals: function(totals) {
        // Update individual total elements
        const subtotalEl = document.getElementById('checkout-subtotal');
        const shippingEl = document.getElementById('checkout-shipping');
        const taxEl = document.getElementById('checkout-tax');
        const totalEl = document.getElementById('checkout-total');
        const couponRow = document.getElementById('checkout-coupon-row');
        const couponNameEl = document.getElementById('checkout-coupon-name');
        const couponCodeEl = document.getElementById('checkout-coupon-code');
        const couponDiscountEl = document.getElementById('checkout-coupon-discount');
        const freeDeliveryInfo = document.getElementById('checkout-free-delivery-info');

        if (subtotalEl) subtotalEl.textContent = `₹${(totals.subtotal || 0).toFixed(2)}`;
        if (shippingEl) shippingEl.textContent = totals.shipping === 0 ? 'Free' : `₹${(totals.shipping || 0).toFixed(2)}`;
        if (taxEl) taxEl.textContent = `₹${(totals.tax || 0).toFixed(2)}`;
        if (totalEl) totalEl.textContent = `₹${(totals.total || 0).toFixed(2)}`;

        // Update coupon information
        if (couponRow) {
            if (totals.discount && totals.discount > 0) {
                // Show applied coupon information
                const appliedCoupon = this.getAppliedCouponInfo();
                
                if (couponNameEl && appliedCoupon) {
                    couponNameEl.textContent = appliedCoupon.name || 'Coupon';
                }
                if (couponCodeEl && appliedCoupon) {
                    couponCodeEl.textContent = `(${appliedCoupon.code})`;
                }
                if (couponDiscountEl) {
                    couponDiscountEl.textContent = `-₹${totals.discount.toFixed(2)}`;
                }
                couponRow.style.display = 'flex';
            } else {
                couponRow.style.display = 'none';
            }
        }

        // Update free delivery info
        if (freeDeliveryInfo) {
            const freeDeliveryAmount = totals.free_delivery_amount || 5000;
            const currentSubtotal = totals.discounted_subtotal || totals.subtotal || 0;
            
            if (currentSubtotal >= freeDeliveryAmount) {
                freeDeliveryInfo.innerHTML = '<small class="text-muted"><i class="fa fa-truck"></i> You qualify for free delivery!</small>';
            } else {
                const remaining = freeDeliveryAmount - currentSubtotal;
                freeDeliveryInfo.innerHTML = `<small class="text-muted"><i class="fa fa-truck"></i> Add ₹${remaining.toFixed(2)} more for free delivery</small>`;
            }
        }
    },

    /**
     * Get applied coupon information from cart page or sidebar
     */
    getAppliedCouponInfo: function() {
        // Try to get from global storage first
        if (this.globalAppliedCoupon) {
            return this.globalAppliedCoupon;
        }
        
        // Try to get from cart page
        if (typeof appliedCoupon !== 'undefined' && appliedCoupon) {
            return appliedCoupon;
        }
        
        // Try to get from sidebar
        if (this.sidebarAppliedCoupon) {
            return this.sidebarAppliedCoupon;
        }
        
        // Fallback: try to extract from DOM
        const couponNameEl = document.getElementById('applied-coupon-name') || document.getElementById('sidebar-applied-coupon-name');
        const couponCodeEl = document.getElementById('applied-coupon-code') || document.getElementById('sidebar-applied-coupon-code');
        
        if (couponNameEl && couponNameEl.textContent && couponNameEl.textContent.trim() !== '') {
            return {
                name: couponNameEl.textContent,
                code: couponCodeEl ? couponCodeEl.textContent.replace(/[()]/g, '') : 'COUPON'
            };
        }
        
        return null;
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
                    // Restore coupon state first
                    this.restoreCouponState(data.data);
                    
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
                (item) => {
                    const variantPrice = item.product_variant?.price || item.product.default_variant?.price || item.product.price || 0;
                    const variantName = item.product_variant?.name || '';
                    const variantId = item.product_variant_id || '';
                    
                    const productSlug = item.product.slug || (item.product.seo && item.product.seo.slug) || null;
                    const productUrl = productSlug ? `/products/${productSlug}` : `/products/${item.product_id}`;
                    console.log('Cart sidebar product URL:', { productId: item.product_id, slug: productSlug, url: productUrl });
                    
                    return `<div class="cart-item-sidebar" data-id="${item.product_id}" data-variant-id="${variantId}">
                        <div class="cart-item-image">
                            <a href="${productUrl}" style="display: block; width: 100%; height: 100%;">
                                <img src="/images/${item.product.images[0] || "placeholder.svg"}" alt="${item.product.name}">
                            </a>
                        </div>
                        <div class="cart-item-details">
                            <div class="cart-item-title">
                                <a href="${productUrl}" style="color: inherit; text-decoration: none;">
                                    ${item.product.name}
                                </a>
                            </div>
                            <div class="cart-item-subtitle">${variantName ? `${variantName} - ` : ''}${item.product.description || ''}</div>
                            <div class="cart-item-price">₹${(variantPrice * item.quantity).toFixed(2)}</div>
                            <div class="quantity-controls">
                                <button class="quantity-btn minus" data-id="${item.product_id}" data-variant-id="${variantId}">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="quantity-display">${item.quantity}</span>
                                <button class="quantity-btn plus" data-id="${item.product_id}" data-variant-id="${variantId}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <button class="remove-item" data-id="${item.product_id}" data-variant-id="${variantId}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>`;
                }
            )
            .join("");
        
        // Update cart totals using API response
        this.updateSidebarTotals(cartData.totals || {});
        
        // Restore coupon state in sidebar
        this.restoreCouponState(cartData);
    } else {
        itemsContainer.innerHTML = "";
        emptyContainer.style.display = "block";
        this.updateSidebarTotals({
            subtotal: 0,
            shipping: 0,
            tax: 0,
            discount: 0,
            total: 0,
            free_delivery_amount: 5000
        });
    }
};

// Add function to open cart sidebar
window.CartFunctions.openCartSidebar = function () {
    const sidebar = document.getElementById("cart-sidebar");
    sidebar.classList.toggle("active");
};

// Add function to change quantity by delta on product cards and sidebar
window.CartFunctions.changeQuantity = function (productId, change, variantId = null) {
    // Convert string "null" to actual null
    if (variantId === "null" || variantId === "") {
        variantId = null;
    }

    // For sidebar items, we need to find the specific variant
    let sidebarSelector = `.cart-item-sidebar[data-id="${productId}"]`;
    if (variantId) {
        sidebarSelector += `[data-variant-id="${variantId}"]`;
    }
    const sidebarItem = document.querySelector(sidebarSelector);
    
    if (sidebarItem) {
        // Get variant ID from the sidebar item if not provided
        const itemVariantId = variantId || sidebarItem.dataset.variantId || null;
        
        // Get current quantity from the display span
        const quantityDisplay = sidebarItem.querySelector('.quantity-display');
        const currentQty = quantityDisplay ? parseInt(quantityDisplay.textContent) || 0 : 0;
        const newQty = currentQty + change;
        
        if (newQty <= 0) {
            this.removeFromCart(productId, itemVariantId);
            return;
        }
        
        // Make API call to update quantity
        this.updateQuantity(productId, newQty, itemVariantId);
        return;
    }

    // For product listing pages and cart page - try to find input
    let input = document.querySelector(
        `.quantity-controls .quantity-input[data-product-id="${productId}"]`
    );

    // If not found, try to find input in cart page (within cart item)
    if (!input) {
        // For cart page, we need to find the specific variant
        let cartItemSelector = `[data-product-id="${productId}"]`;
        if (variantId) {
            cartItemSelector += `[data-variant-id="${variantId}"]`;
        }
        
        const cartItem = document.querySelector(cartItemSelector);
        if (cartItem) {
            input = cartItem.querySelector(
                ".quantity-controls .quantity-input"
            );
            // Get variant ID from cart item if not provided
            if (!variantId) {
                variantId = cartItem.dataset.variantId || null;
            }
        }
    }

    if (!input) return;

    let current = parseInt(input.value) || 0;
    let newQty = current + change;
    if (newQty <= 0) {
        this.removeFromCart(productId, variantId);
        // Only remove quantity controls on product listing pages, not cart page
        if (input.hasAttribute("data-product-id")) {
            this.removeQuantityControls(productId);
        }
        return;
    }
    input.value = newQty;
    this.updateQuantity(productId, newQty, variantId);
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
    btn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
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
    // First, update existing quantity controls
    cartData.items.forEach((item) => {
        // Look for existing quantity input for this product
        const existingInput = document.querySelector(
            `.quantity-input[data-product-id="${item.product_id}"]`
        );
        if (existingInput && parseInt(existingInput.value) !== item.quantity) {
            // Update the input value to match cart quantity
            existingInput.value = item.quantity;
        }
    });

    // Then, replace add-to-cart buttons with quantity controls for items in cart
    cartData.items.forEach((item) => {
        // Check if there's already a static quantity selector for this product (like on product detail pages)
        const existingQuantitySelector = document.querySelector('.quantity-selector');
        const existingQuantityInput = document.getElementById('quantity');
        
        // If we're on a product detail page with static quantity controls, don't add dynamic ones
        if (existingQuantitySelector && existingQuantityInput) {
            // Update the existing quantity input to match cart quantity if it's for the same product
            const selectedVariantInput = document.getElementById('selected-variant-id');
            if (selectedVariantInput && parseInt(selectedVariantInput.value) === item.variant_id) {
                existingQuantityInput.value = item.quantity;
            }
            return; // Skip adding dynamic controls
        }
        
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

    // Finally, revert quantity controls back to add-to-cart buttons for items no longer in cart
    const allQuantityInputs = document.querySelectorAll('.quantity-input[data-product-id]');
    allQuantityInputs.forEach((input) => {
        const productId = parseInt(input.getAttribute('data-product-id'));
        const isInCart = cartData.items.some(item => item.product_id === productId);
        
        if (!isInCart) {
            // This product is no longer in cart, revert to add-to-cart button
            this.removeQuantityControls(productId);
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
window.updateQuantity = function (productId, delta, variantId = null) {
    // Check if this is a small delta value (likely relative change from cart page buttons)
    if (typeof delta === "number" && (delta === 1 || delta === -1)) {
        // This is a relative change from cart page buttons
        CartFunctions.changeQuantity(productId, delta, variantId);
    } else {
        // This is an absolute quantity setting
        CartFunctions.updateQuantity(productId, delta, variantId);
    }
};

window.setQuantity = function (productId, quantity, variantId = null) {
    CartFunctions.updateQuantity(productId, parseInt(quantity), variantId);
};

window.removeFromCart = function (productId, variantId = null) {
    CartFunctions.removeFromCart(productId, variantId);
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
                const variantId = btn.dataset.variantId || null;
                if (productId) {
                    CartFunctions.changeQuantity(productId, -1, variantId);
                }
            }
            
            if (e.target.matches('.cart-item-sidebar .quantity-btn.plus') || 
                e.target.closest('.cart-item-sidebar .quantity-btn.plus')) {
                e.preventDefault();
                const btn = e.target.closest('.quantity-btn.plus');
                const productId = btn.dataset.id;
                const variantId = btn.dataset.variantId || null;
                if (productId) {
                    CartFunctions.changeQuantity(productId, 1, variantId);
                }
            }
            
            // Sidebar remove item buttons
            if (e.target.matches('.cart-item-sidebar .remove-item') || 
                e.target.closest('.cart-item-sidebar .remove-item')) {
                e.preventDefault();
                const btn = e.target.closest('.remove-item');
                const productId = btn.dataset.id;
                const variantId = btn.dataset.variantId || null;
                if (productId) {
                    CartFunctions.removeFromCart(productId, variantId);
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
        
        // Sidebar coupon event listeners
        const sidebarApplyBtn = document.getElementById('sidebar-apply-coupon-btn');
        const sidebarRemoveBtn = document.getElementById('sidebar-remove-coupon-btn');
        const sidebarCouponInput = document.getElementById('sidebar-coupon-code');
        
        if (sidebarApplyBtn) {
            sidebarApplyBtn.addEventListener('click', function() {
                CartFunctions.applySidebarCoupon();
            });
        }
        
        if (sidebarRemoveBtn) {
            sidebarRemoveBtn.addEventListener('click', function() {
                CartFunctions.removeSidebarCoupon();
            });
        }
        
        if (sidebarCouponInput) {
            sidebarCouponInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    CartFunctions.applySidebarCoupon();
                }
            });
        }
    }
});

// Add function to update sidebar totals
window.CartFunctions.updateSidebarTotals = function(totals) {
    // Update individual total elements
    const subtotalEl = document.getElementById('sidebar-subtotal');
    const shippingEl = document.getElementById('sidebar-shipping');
    const taxEl = document.getElementById('sidebar-tax');
    const discountEl = document.getElementById('sidebar-discount');
    const totalEl = document.getElementById('sidebar-total');
    const discountRow = document.getElementById('sidebar-discount-row');
    const freeDeliveryInfo = document.getElementById('sidebar-free-delivery-info');

    if (subtotalEl) subtotalEl.textContent = `₹${(totals.subtotal || 0).toFixed(2)}`;
    if (shippingEl) shippingEl.textContent = totals.shipping === 0 ? 'Free' : `₹${(totals.shipping || 0).toFixed(2)}`;
    if (taxEl) taxEl.textContent = `₹${(totals.tax || 0).toFixed(2)}`;
    if (totalEl) totalEl.textContent = `₹${(totals.total || 0).toFixed(2)}`;

    // Update discount row
    if (discountRow && discountEl) {
        if (totals.discount && totals.discount > 0) {
            discountEl.textContent = `-₹${totals.discount.toFixed(2)}`;
            discountRow.style.display = 'flex';
        } else {
            discountRow.style.display = 'none';
        }
    }

    // Update free delivery info
    if (freeDeliveryInfo) {
        const freeDeliveryAmount = totals.free_delivery_amount || 1000;
        const currentSubtotal = totals.discounted_subtotal || totals.subtotal || 0;
        
        if (currentSubtotal >= freeDeliveryAmount) {
            freeDeliveryInfo.innerHTML = '<small class="text-muted"><i class="fa fa-truck"></i> You qualify for free delivery!</small>';
        } else {
            const remaining = freeDeliveryAmount - currentSubtotal;
            freeDeliveryInfo.innerHTML = `<small class="text-muted"><i class="fa fa-truck"></i> Add ₹${remaining.toFixed(2)} more for free delivery</small>`;
        }
    }
};

// Sidebar coupon functionality
window.CartFunctions.sidebarAppliedCoupon = null;

// Global applied coupon storage for checkout modal
window.CartFunctions.globalAppliedCoupon = null;

// Coupon persistence functions
window.CartFunctions.saveCouponToStorage = function(coupon) {
    try {
        if (coupon) {
            localStorage.setItem('applied_coupon', JSON.stringify(coupon));
        } else {
            localStorage.removeItem('applied_coupon');
        }
    } catch (error) {
        console.warn('Failed to save coupon to localStorage:', error);
    }
};

window.CartFunctions.loadCouponFromStorage = function() {
    try {
        const stored = localStorage.getItem('applied_coupon');
        return stored ? JSON.parse(stored) : null;
    } catch (error) {
        console.warn('Failed to load coupon from localStorage:', error);
        return null;
    }
};

window.CartFunctions.restoreCouponState = function(cartData) {
    if (cartData && cartData.applied_coupon) {
        // Server has coupon data, use that and sync to localStorage
        this.globalAppliedCoupon = cartData.applied_coupon;
        this.sidebarAppliedCoupon = cartData.applied_coupon;
        this.saveCouponToStorage(cartData.applied_coupon);
        
        // Update UI to show applied coupon
        this.showAppliedCouponInUI(cartData.applied_coupon);
        
        return cartData.applied_coupon;
    } else {
        // No server coupon, check localStorage
        const storedCoupon = this.loadCouponFromStorage();
        if (storedCoupon) {
            // Validate stored coupon is still applicable
            this.validateStoredCoupon(storedCoupon);
        }
        return null;
    }
};

window.CartFunctions.validateStoredCoupon = function(storedCoupon) {
    // Check if stored coupon is still valid by attempting to apply it
    fetch('/coupon/validate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            coupon_code: storedCoupon.code
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Coupon is still valid, apply it
            this.applyCouponSilently(storedCoupon.code);
        } else {
            // Coupon is no longer valid, remove from storage
            this.saveCouponToStorage(null);
        }
    })
    .catch(error => {
        console.warn('Failed to validate stored coupon:', error);
        this.saveCouponToStorage(null);
    });
};

window.CartFunctions.applyCouponSilently = function(couponCode) {
    // Apply coupon without showing loading states
    fetch('/coupon/apply', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            coupon_code: couponCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            this.globalAppliedCoupon = data.data.coupon;
            this.sidebarAppliedCoupon = data.data.coupon;
            this.saveCouponToStorage(data.data.coupon);
            this.showAppliedCouponInUI(data.data.coupon);
            
            // Update all UI components
            this.updateSidebarTotals(data.data.totals);
            this.updateCheckoutTotals(data.data.totals);
            
            // Update main cart page if it's open
            if (typeof updateCartTotals === 'function') {
                updateCartTotals({ totals: data.data.totals });
            }
        }
    })
    .catch(error => {
        console.warn('Failed to silently apply coupon:', error);
    });
};

window.CartFunctions.showAppliedCouponInUI = function(coupon) {
    // Show in sidebar with detailed information
    this.showSidebarAppliedCoupon(coupon);
    
    // Show in main cart page if elements exist
    const mainCouponName = document.getElementById('applied-coupon-name');
    const mainCouponCode = document.getElementById('applied-coupon-code');
    const mainInputSection = document.getElementById('coupon-input-section');
    const mainAppliedSection = document.getElementById('applied-coupon-section');
    
    if (mainCouponName && mainCouponCode && mainInputSection && mainAppliedSection) {
        mainCouponName.textContent = coupon.name || 'Coupon';
        mainCouponCode.textContent = `(${coupon.code})`;
        mainInputSection.style.display = 'none';
        mainAppliedSection.style.display = 'flex';
        
        // Clear coupon input
        const couponInput = document.getElementById('coupon-code');
        if (couponInput) couponInput.value = '';
    }
};

window.CartFunctions.applySidebarCoupon = function() {
    const couponCode = document.getElementById('sidebar-coupon-code').value.trim();
    const applyBtn = document.getElementById('sidebar-apply-coupon-btn');

    if (!couponCode) {
        this.showSidebarCouponMessage('Please enter a coupon code', 'error');
        return;
    }

    // Disable button and show loading
    applyBtn.disabled = true;
    applyBtn.textContent = 'Applying...';
    this.clearSidebarCouponMessage();

    fetch('/coupon/apply', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            coupon_code: couponCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            this.sidebarAppliedCoupon = data.data.coupon;
            this.globalAppliedCoupon = data.data.coupon; // Store globally for checkout
            this.saveCouponToStorage(data.data.coupon); // Save to localStorage
            this.updateSidebarTotals(data.data.totals);
            this.showSidebarAppliedCoupon(this.sidebarAppliedCoupon);
            this.showSidebarCouponMessage(`Coupon applied! You saved ₹${this.sidebarAppliedCoupon.discount_amount.toFixed(2)}`, 'success');
            
            // Update main cart page if it's open
            if (typeof updateCartTotals === 'function') {
                updateCartTotals({ totals: data.data.totals });
            }
            
            // Update checkout modal if it exists
            this.updateCheckoutTotals(data.data.totals);
        } else {
            this.showSidebarCouponMessage(data.message || 'Invalid coupon code', 'error');
        }
    })
    .catch(error => {
        console.error('Error applying coupon:', error);
        this.showSidebarCouponMessage('An error occurred while applying the coupon', 'error');
    })
    .finally(() => {
        applyBtn.disabled = false;
        applyBtn.textContent = 'Apply';
    });
};

window.CartFunctions.removeSidebarCoupon = function() {
    const removeBtn = document.getElementById('sidebar-remove-coupon-btn');
    
    // Show confirmation for better UX
    if (!confirm('Remove applied coupon?')) {
        return;
    }
    
    // Disable button and show loading
    removeBtn.disabled = true;
    removeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    this.clearSidebarCouponMessage();

    fetch('/coupon/remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            this.sidebarAppliedCoupon = null;
            this.globalAppliedCoupon = null; // Clear globally for checkout
            this.saveCouponToStorage(null); // Remove from localStorage
            this.updateSidebarTotals(data.data.totals);
            this.hideSidebarAppliedCoupon();
            this.showSidebarCouponMessage('Coupon removed successfully', 'success');
            
            // Update main cart page if it's open
            if (typeof updateCartTotals === 'function') {
                updateCartTotals({ totals: data.data.totals });
            }
            
            // Update checkout modal if it exists
            this.updateCheckoutTotals(data.data.totals);
        } else {
            this.showSidebarCouponMessage(data.message || 'Error removing coupon', 'error');
        }
    })
    .catch(error => {
        console.error('Error removing coupon:', error);
        this.showSidebarCouponMessage('An error occurred while removing the coupon', 'error');
    })
    .finally(() => {
        removeBtn.disabled = false;
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
    });
};

window.CartFunctions.showSidebarAppliedCoupon = function(coupon) {
    const nameEl = document.getElementById('sidebar-applied-coupon-name');
    const codeEl = document.getElementById('sidebar-applied-coupon-code');
    const savingsEl = document.getElementById('sidebar-applied-coupon-savings');
    const inputSection = document.getElementById('sidebar-coupon-input-section');
    const appliedSection = document.getElementById('sidebar-applied-coupon-section');
    
    if (nameEl) {
        nameEl.textContent = coupon.name || 'Coupon Applied';
    }
    
    if (codeEl) {
        codeEl.textContent = `Code: ${coupon.code}`;
    }
    
    if (savingsEl && coupon.discount_amount) {
        savingsEl.textContent = `Saved ₹${parseFloat(coupon.discount_amount).toFixed(2)}`;
    }
    
    if (inputSection) inputSection.style.display = 'none';
    if (appliedSection) appliedSection.style.display = 'block';
    
    // Clear coupon input
    const codeInput = document.getElementById('sidebar-coupon-code');
    if (codeInput) codeInput.value = '';
};

window.CartFunctions.hideSidebarAppliedCoupon = function() {
    const inputSection = document.getElementById('sidebar-coupon-input-section');
    const appliedSection = document.getElementById('sidebar-applied-coupon-section');
    
    // Clear coupon details
    const nameEl = document.getElementById('sidebar-applied-coupon-name');
    const codeEl = document.getElementById('sidebar-applied-coupon-code');
    const savingsEl = document.getElementById('sidebar-applied-coupon-savings');
    
    if (nameEl) nameEl.textContent = '';
    if (codeEl) codeEl.textContent = '';
    if (savingsEl) savingsEl.textContent = '';
    
    if (inputSection) inputSection.style.display = 'flex';
    if (appliedSection) appliedSection.style.display = 'none';
};

window.CartFunctions.showSidebarCouponMessage = function(message, type) {
    const messageDiv = document.getElementById('sidebar-coupon-message');
    if (messageDiv) {
        messageDiv.textContent = message;
        messageDiv.className = `coupon-message-sidebar ${type}`;
        messageDiv.style.display = 'block';
    }
};

window.CartFunctions.clearSidebarCouponMessage = function() {
    const messageDiv = document.getElementById('sidebar-coupon-message');
    if (messageDiv) {
        messageDiv.textContent = '';
        messageDiv.className = 'coupon-message-sidebar';
        messageDiv.style.display = 'none';
    }
};
