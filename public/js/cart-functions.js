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
    addToCart: function(productId, quantity = 1, variantId = null) {
        const requestBody = { quantity: quantity };
        if (variantId) {
            requestBody.variant_id = variantId;
        }

        fetch(`/cart/add/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(requestBody)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification(data.message, 'success');
                
                // Update cart count in header if it exists
                this.updateCartCount();
                
                // Update cart sidebar if it exists
                if (typeof updateCartSidebar === 'function') {
                    updateCartSidebar();
                }
            } else {
                this.showNotification(data.message || 'Error adding product to cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            this.showNotification('Error adding product to cart', 'error');
        });
    },

    /**
     * Update product quantity in cart
     * @param {number} productId - The product ID to update
     * @param {number} quantity - New quantity
     */
    updateQuantity: function(productId, quantity) {
        if (quantity <= 0) {
            this.removeFromCart(productId);
            return;
        }

        fetch(`/cart/update/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification(data.message, 'success');
                this.updateCartCount();
                this.updateCartSidebar();
                // Reload cart if on cart page
                if (typeof loadCart === 'function') {
                    loadCart();
                }
            } else {
                this.showNotification(data.message || 'Error updating quantity', 'error');
            }
        })
        .catch(error => {
            console.error('Error updating quantity:', error);
            this.showNotification('Error updating quantity', 'error');
        });
    },

    /**
     * Remove product from cart
     * @param {number} productId - The product ID to remove
     */
    removeFromCart: function(productId) {
        fetch(`/cart/remove/${productId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification(data.message, 'success');
                this.updateCartCount();
                this.updateCartSidebar();
                // Reload cart if on cart page
                if (typeof loadCart === 'function') {
                    loadCart();
                }
            } else {
                this.showNotification(data.message || 'Error removing item', 'error');
            }
        })
        .catch(error => {
            console.error('Error removing item:', error);
            this.showNotification('Error removing item', 'error');
        });
    },

    /**
     * Update cart count in header
     */
    updateCartCount: function() {
        fetch('/cart/data')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const totalItems = data.data.item_count || 0;
                
                // Update cart count in header
                const cartCountElements = document.querySelectorAll('.cart-count');
                cartCountElements.forEach(element => {
                    element.textContent = totalItems;
                });
            }
        })
        .catch(error => {
            console.error('Error fetching cart count:', error);
            // Fallback to 0 if there's an error
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(element => {
                element.textContent = '0';
            });
        });
    },

    /**
     * Show notification message
     * @param {string} message - Message to display
     * @param {string} type - Type of notification (success, error, info, warning)
     */
    showNotification: function(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
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
            notification.style.animation = 'slideOut 0.3s ease';
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
    getNotificationColor: function(type) {
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };
        return colors[type] || colors.info;
    },

    /**
     * Buy now functionality - add to cart and redirect to checkout
     * @param {number} productId - The product ID to buy
     * @param {number} quantity - Quantity to buy (default: 1)
     */
    buyNow: function(productId, quantity = 1) {
        // First add to cart
        this.addToCart(productId, quantity);
        
        // Then redirect to checkout after a short delay
        setTimeout(() => {
            window.location.href = '/checkout';
        }, 1000);
    }
};

// Add function to update cart sidebar overlay
window.CartFunctions.updateCartSidebar = function() {
    const itemsContainer = document.getElementById('cart-items');
    const emptyContainer = document.getElementById('cart-empty');
    const totalEl = document.getElementById('cart-total');
    if (!itemsContainer || !emptyContainer || !totalEl) return;
    fetch('/cart/data')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.items.length > 0) {
                emptyContainer.style.display = 'none';
                itemsContainer.innerHTML = data.data.items.map(item => 
                    `<div class="cart-item" data-product-id="${item.product_id}">
                        <div class="item-image"><img src="/images/${item.product.images[0]||'placeholder.png'}" /></div>
                        <div class="item-details">
                            <div class="item-name">${item.product.name}</div>
                            <div class="item-quantity">Qty: ${item.quantity}</div>
                        </div>
                    </div>`
                ).join('');
                totalEl.innerHTML = `₹${data.data.total}`;
            } else {
                itemsContainer.innerHTML = '';
                emptyContainer.style.display = 'block';
                totalEl.innerHTML = '';
            }
        })
        .catch(error => console.error('Error updating cart sidebar:', error));
};

// Add function to open cart sidebar
window.CartFunctions.openCartSidebar = function() {
    const sidebar = document.getElementById('cart-sidebar');
    sidebar.classList.toggle('active');
};

// Add function to change quantity by delta on product cards
window.CartFunctions.changeQuantity = function(productId, change) {
    const input = document.querySelector(`.quantity-controls .quantity-input[data-product-id="${productId}"]`);
    if (!input) return;
    let current = parseInt(input.value) || 0;
    let newQty = current + change;
    if (newQty <= 0) {
        this.removeFromCart(productId);
        this.removeQuantityControls(productId);
        return;
    }
    input.value = newQty;
    this.updateQuantity(productId, newQty);
};

// Add function to revert quantity controls back to Add to Cart button
window.CartFunctions.removeQuantityControls = function(productId) {
    const control = document.querySelector(`.quantity-controls .quantity-input[data-product-id="${productId}"]`);
    if (!control) return;
    const controlDiv = control.closest('.quantity-controls');
    if (!controlDiv) return;
    // Create add to cart button
    const btn = document.createElement('button');
    btn.className = 'btn btn-add-to-cart';
    btn.setAttribute('onclick', `addToCart(${productId})`);
    btn.textContent = 'Add to Cart';
    controlDiv.parentNode.replaceChild(btn, controlDiv);
};

// Render quantity controls for products in cart on listing pages
window.CartFunctions.renderProductCartButtons = function() {
    fetch('/cart/data')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                data.data.items.forEach(item => {
                    // find add-to-cart button
                    const btn = document.querySelector(`.btn-add-to-cart[onclick*="${item.product_id}"]`);
                    if (btn) {
                        const control = document.createElement('div');
                        control.className = 'quantity-controls';
                        control.innerHTML = `
                            <button class="quantity-btn" onclick="CartFunctions.changeQuantity(${item.product_id}, -1)"><i class="fas fa-minus"></i></button>
                            <input type="number" class="quantity-input" data-product-id="${item.product_id}" value="${item.quantity}" min="1" onchange="CartFunctions.updateQuantity(${item.product_id}, this.value)">
                            <button class="quantity-btn" onclick="CartFunctions.changeQuantity(${item.product_id}, 1)"><i class="fas fa-plus"></i></button>`;
                        btn.parentNode.replaceChild(control, btn);
                    }
                });
            }
        });
};

// Modify addToCart to open sidebar
(function() {
    const originalAdd = window.CartFunctions.addToCart;
    window.CartFunctions.addToCart = function(productId, quantity, variantId) {
        originalAdd.call(this, productId, quantity, variantId);
        this.openCartSidebar();
        this.updateCartSidebar();
        this.renderProductCartButtons();
    };
})();

// Add CSS for notifications
const cartNotificationStyle = document.createElement('style');
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

// Initialize cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    if (typeof CartFunctions !== 'undefined') {
        CartFunctions.updateCartCount();
        CartFunctions.updateCartSidebar();
        CartFunctions.renderProductCartButtons();
        document.getElementById('close-cart').addEventListener('click', function() {
            document.getElementById('cart-sidebar').classList.remove('active');
        });
        // Open sidebar on load if cart has items
        fetch('/cart/data')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.items.length > 0) {
                    CartFunctions.openCartSidebar();
                }
            });
        // Bind cart-toggle buttons to open sidebar
        document.querySelectorAll('.cart-toggle').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                CartFunctions.updateCartSidebar();
                CartFunctions.openCartSidebar();
            });
        });
    }
});
