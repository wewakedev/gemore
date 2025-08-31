@extends('layouts.app')

@section('title', 'Store - Ge More Nutralife | Premium Sports Supplements Online')

@section('additional_css')
<link rel="stylesheet" href="{{ asset('css/store.css') }}" />
<style>
/* Store Page Enhanced Styles */
.store-header {
    background: linear-gradient(135deg, #8b0000 0%, #0d0000eb 50%, #3e1c06 100%);
    color: white;
    padding: 80px 0 60px 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.store-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.store-header h1 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.store-header p {
    font-size: 1.2rem;
    opacity: 0.9;
}

.filter-section {
    background: #f8f9fa;
    padding: 30px 0;
    border-bottom: 1px solid #dee2e6;
}

.filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.filter-left h3 {
    color: #333;
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 15px;
}

.filter-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 10px 20px;
    border: 2px solid #dee2e6;
    background: white;
    color: #333;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
}

.filter-btn:hover, .filter-btn.active {
    background: #8b0000;
    color: white;
    border-color: #8b0000;
    transform: translateY(-2px);
}

.filter-right {
    display: flex;
    gap: 15px;
    align-items: center;
}

.search-box {
    position: relative;
}

.search-box input {
    padding: 12px 45px 12px 15px;
    border: 2px solid #dee2e6;
    border-radius: 25px;
    width: 250px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.search-box input:focus {
    border-color: #8b0000;
    outline: none;
    box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
}

.search-box i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.sort-dropdown select {
    padding: 12px 15px;
    border: 2px solid #dee2e6;
    border-radius: 25px;
    background: white;
    color: #333;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.sort-dropdown select:focus {
    border-color: #8b0000;
    outline: none;
}

.products-section {
    padding: 60px 0;
    background: #fff;
}

.loading-spinner {
    text-align: center;
    padding: 60px 0;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #8b0000;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.product-item {
    background: white;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.product-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.product-card {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-item:hover .product-image img {
    transform: scale(1.1);
}

.no-image {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    background: #f8f9fa;
    color: #ccc;
    font-size: 3rem;
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(139, 0, 0, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-item:hover .product-overlay {
    opacity: 1;
}

.btn-quick-view {
    background: white;
    color: #8b0000;
    border: none;
    padding: 12px 25px;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-quick-view:hover {
    background: #f8f9fa;
    transform: scale(1.05);
}

.product-badges {
    position: absolute;
    top: 15px;
    left: 15px;
    z-index: 2;
}

.badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-right: 8px;
    margin-bottom: 5px;
}

.badge-featured {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.badge-sale {
    background: linear-gradient(135deg, #dc3545, #e74c3c);
    color: white;
}

.wishlist-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255,255,255,0.9);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    transition: all 0.3s ease;
}

.wishlist-btn:hover {
    background: #dc3545;
    color: white;
    transform: scale(1.1);
}

.product-info {
    padding: 25px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-category {
    color: #8b0000;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.product-name {
    font-size: 1.3rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
    line-height: 1.3;
}

.product-description {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 15px;
    flex-grow: 1;
}

.product-variants {
    display: flex;
    gap: 8px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.variant-option {
    background: #f8f9fa;
    color: #333;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.variant-more {
    background: #8b0000;
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.product-price {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 20px;
}

.original-price {
    color: #999;
    text-decoration: line-through;
    margin-right: 10px;
    font-size: 1rem;
}

.current-price {
    color: #8b0000;
}

.product-actions {
    display: flex;
    gap: 10px;
}

.btn-add-to-cart, .btn-buy-now {
    flex: 1;
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    font-size: 0.85rem;
}

.btn-add-to-cart {
    background: #f8f9fa;
    color: #333;
    border: 2px solid #dee2e6;
}

.btn-add-to-cart:hover {
    background: #8b0000;
    color: white;
    border-color: #8b0000;
}

.btn-buy-now {
    background: linear-gradient(135deg, #8b0000, #a52a2a);
    color: white;
    border: none;
}

.btn-buy-now:hover {
    background: linear-gradient(135deg, #a52a2a, #b22222);
    transform: translateY(-2px);
}

.no-products {
    text-align: center;
    padding: 80px 20px;
    color: #666;
}

.no-products i {
    font-size: 4rem;
    margin-bottom: 20px;
    color: #ccc;
}

.no-products h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
}

.load-more-container {
    text-align: center;
    margin-top: 40px;
}

.btn-outline-primary {
    border: 2px solid #8b0000;
    color: #8b0000;
    background: transparent;
    padding: 15px 40px;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: #8b0000;
    color: white;
    transform: translateY(-2px);
}

.btn-outline-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.error-message {
    text-align: center;
    padding: 80px 20px;
    color: #666;
}

.error-message i {
    font-size: 4rem;
    margin-bottom: 20px;
    color: #dc3545;
}

.error-message h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: #333;
}

/* Responsive Design */
@media (max-width: 992px) {
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-right {
        justify-content: center;
    }
    
    .search-box input {
        width: 200px;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .store-header h1 {
        font-size: 2rem;
    }
    
    .filter-buttons {
        justify-content: center;
    }
    
    .filter-right {
        flex-direction: column;
        gap: 10px;
    }
    
    .search-box input {
        width: 100%;
    }
    
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .product-actions {
        flex-direction: column;
    }
}
</style>
@endsection

@section('content')
<!-- Store Header -->
<section class="store-header">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>Premium Sports Supplements Store</h1>
                <p>Shop high-quality supplements for your fitness journey</p>
            </div>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section class="filter-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="filter-bar">
                    <div class="filter-left">
                        <h3>Filter Products</h3>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-filter="all">
                                All Products
                            </button>
                            @foreach($categories as $category)
                            <button class="filter-btn" data-filter="{{ $category->slug }}">
                                {{ $category->name }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="filter-right">
                        <div class="search-box">
                            <input type="text" id="search-input" placeholder="Search products..." />
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="sort-dropdown">
                            <select id="sort-select">
                                <option value="default">Sort by</option>
                                <option value="price-low">Price: Low to High</option>
                                <option value="price-high">Price: High to Low</option>
                                <option value="name">Name A-Z</option>
                                <option value="popular">Most Popular</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="products-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Loading Spinner -->
                <div id="loading" class="loading-spinner" style="display: none;">
                    <div class="spinner"></div>
                    <p>Loading products...</p>
                </div>

                <!-- Products Grid -->
                <div class="products-grid" id="products-grid">
                    <!-- Products will be loaded here via JavaScript -->
                </div>

                <!-- No Products Message -->
                <div id="no-products" class="no-products" style="display: none;">
                    <i class="fas fa-search"></i>
                    <h3>No products found</h3>
                    <p>Try adjusting your search or filter criteria</p>
                </div>

                <!-- Load More Button -->
                <div class="load-more-container" id="load-more-container" style="display: none;">
                    <button class="btn btn-outline-primary" id="load-more-btn">
                        Load More Products
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('additional_js')
<script src="{{ asset('js/cart-functions.js') }}"></script>
<script>
class StoreManager {
    constructor() {
        this.currentPage = 1;
        this.totalPages = 1;
        this.isLoading = false;
        this.filters = {
            category: 'all',
            search: '',
            sortBy: 'default'
        };
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadProducts();
    }

    setupEventListeners() {
        // Filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Update active filter button
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                
                this.filters.category = e.target.dataset.filter;
                this.currentPage = 1;
                this.loadProducts();
            });
        });

        // Search input
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.filters.search = e.target.value;
                    this.currentPage = 1;
                    this.loadProducts();
                }, 500);
            });
        }

        // Sort dropdown
        const sortSelect = document.getElementById('sort-select');
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                this.filters.sortBy = e.target.value;
                this.currentPage = 1;
                this.loadProducts();
            });
        }

        // Load more button
        const loadMoreBtn = document.getElementById('load-more-btn');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', () => {
                this.loadMoreProducts();
            });
        }
    }

    async loadProducts(append = false) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.showLoading();

        try {
            const params = new URLSearchParams({
                page: this.currentPage,
                category: this.filters.category,
                search: this.filters.search,
                sort: this.filters.sortBy,
                per_page: 12
            });

            const response = await fetch(`/api/products?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderProducts(data.data, append);
                this.updatePagination(data.pagination);
            } else {
                this.showError('Failed to load products');
            }
        } catch (error) {
            console.error('Error loading products:', error);
            this.showError('Error loading products');
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }

    async loadMoreProducts() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            await this.loadProducts(true);
        }
    }

    renderProducts(products, append = false) {
        const grid = document.getElementById('products-grid');
        const noProducts = document.getElementById('no-products');
        
        if (products.length === 0 && !append) {
            grid.innerHTML = '';
            noProducts.style.display = 'block';
            this.hideLoadMore();
            return;
        }

        noProducts.style.display = 'none';

        const productsHTML = products.map(product => `
            <div class="product-item" data-category="${product.category.slug}">
                <div class="product-card">
                    <div class="product-image">
                        ${product.images && product.images.length > 0 ? 
                            `<img src="/images/${product.images[0]}" alt="${product.name}" loading="lazy" />` : 
                            '<div class="no-image"><i class="fas fa-image"></i></div>'
                        }
                        <div class="product-overlay">
                            <button class="btn btn-quick-view" onclick="quickView(${product.id})">
                                <i class="fas fa-eye"></i> Quick View
                            </button>
                        </div>
                        <div class="product-badges">
                            ${product.is_featured ? '<span class="badge badge-featured">Featured</span>' : ''}
                            ${product.discount_percentage > 0 ? `<span class="badge badge-sale">${product.discount_percentage}% Off</span>` : ''}
                        </div>
                        <button class="wishlist-btn" onclick="toggleWishlist(${product.id})" aria-label="Add to wishlist">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="product-info">
                        <div class="product-category">${product.category.name}</div>
                        <h3 class="product-name">${product.name}</h3>
                        <p class="product-description">${product.description ? product.description.substring(0, 80) + '...' : ''}</p>
                        
                        ${product.active_variants && product.active_variants.length > 0 ? `
                            <div class="product-variants">
                                ${product.active_variants.slice(0, 2).map(variant => `
                                    <span class="variant-option">${variant.name}</span>
                                `).join('')}
                                ${product.active_variants.length > 2 ? `<span class="variant-more">+${product.active_variants.length - 2} more</span>` : ''}
                            </div>
                        ` : ''}
                        
                        <div class="product-price">
                            ${product.discount_price != null && product.discount_price < product.min_price ? `
                                <span class="original-price">₹${new Intl.NumberFormat('en-IN').format(product.min_price || 0)}</span>
                                <span class="current-price">₹${new Intl.NumberFormat('en-IN').format(product.discount_price || product.min_price || 0)}</span>
                            ` : `
                                <span class="current-price">₹${new Intl.NumberFormat('en-IN').format(product.min_price || 0)}</span>
                            `}
                        </div>
                        
                        <div class="product-actions">
                            <button class="btn btn-add-to-cart" onclick="addToCart(${product.id})">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                            <button class="btn btn-buy-now" onclick="buyNow(${product.id})">
                                Buy Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        if (append) {
            grid.innerHTML += productsHTML;
        } else {
            grid.innerHTML = productsHTML;
        }
    }

    updatePagination(pagination) {
        this.totalPages = pagination.total_pages;
        this.currentPage = pagination.current_page;
        
        const loadMoreContainer = document.getElementById('load-more-container');
        const loadMoreBtn = document.getElementById('load-more-btn');
        
        if (pagination.has_next_page) {
            loadMoreContainer.style.display = 'block';
            loadMoreBtn.disabled = false;
            loadMoreBtn.innerHTML = 'Load More Products';
        } else {
            if (this.currentPage > 1) {
                loadMoreContainer.style.display = 'block';
                loadMoreBtn.disabled = true;
                loadMoreBtn.innerHTML = 'No More Products';
            } else {
                loadMoreContainer.style.display = 'none';
            }
        }
    }

    showLoading() {
        document.getElementById('loading').style.display = 'block';
    }

    hideLoading() {
        document.getElementById('loading').style.display = 'none';
    }

    hideLoadMore() {
        document.getElementById('load-more-container').style.display = 'none';
    }

    showError(message) {
        const grid = document.getElementById('products-grid');
        grid.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Oops! Something went wrong</h3>
                <p>${message}</p>
                <button class="btn btn-primary" onclick="storeManager.loadProducts()">Try Again</button>
            </div>
        `;
    }
}

// Initialize store manager
const storeManager = new StoreManager();

// Utility functions
function addToCart(productId) {
    // Use shared cart functions
    if (typeof CartFunctions !== 'undefined') {
        CartFunctions.addToCart(productId, 1);
    } else {
        // Fallback to direct implementation
        fetch(`/cart/add/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Error adding product to cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            showNotification('Error adding product to cart', 'error');
        });
    }
}

function buyNow(productId) {
    // Use shared cart functions
    if (typeof CartFunctions !== 'undefined') {
        CartFunctions.buyNow(productId, 1);
    } else {
        // Fallback to direct implementation
        alert('Buy now functionality will be implemented');
    }
}

function toggleWishlist(productId) {
    // This will be implemented with proper wishlist functionality
    console.log('Toggle wishlist for product:', productId);
    alert('Wishlist functionality will be implemented');
}

function quickView(productId) {
    // This will be implemented with proper product modal
    console.log('Quick view for product:', productId);
    alert('Quick view functionality will be implemented');
}

function showNotification(message, type = 'info') {
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
    `;
    notification.textContent = message;

    // Add to page
    document.body.appendChild(notification);

    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Add CSS for notifications
const style = document.createElement('style');
style.textContent = `
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
document.head.appendChild(style);
</script>
@endsection 