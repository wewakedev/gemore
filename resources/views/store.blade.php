@extends('layouts.app')

@section('title', 'Store - Ge More Nutralife | Premium Sports Supplements Online')

@section('additional_css')
<link rel="stylesheet" href="{{ asset('css/store.css') }}" />
@endsection

@section('content')
<!-- Store Header -->
<section class="store-header py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1>Our Store</h1>
                <p>Premium sports supplements for your fitness journey</p>
            </div>
        </div>
    </div>
</section>

<!-- Store Content -->
<section class="store-content py-5">
    <div class="container">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="filters-sidebar">
                    <h5>Filter Products</h5>
                    
                    <!-- Category Filter -->
                    <div class="filter-group">
                        <h6>Categories</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="category" value="all" id="cat-all" checked>
                            <label class="form-check-label" for="cat-all">All Products</label>
                        </div>
                        @foreach($categories as $category)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="category" value="{{ $category->slug }}" id="cat-{{ $category->id }}">
                            <label class="form-check-label" for="cat-{{ $category->id }}">{{ $category->name }}</label>
                        </div>
                        @endforeach
                    </div>

                    <!-- Price Filter -->
                    <div class="filter-group">
                        <h6>Price Range</h6>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm" id="min-price" placeholder="Min">
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm" id="max-price" placeholder="Max">
                            </div>
                        </div>
                    </div>

                    <!-- Featured Filter -->
                    <div class="filter-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="featured-only">
                            <label class="form-check-label" for="featured-only">Featured Only</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <!-- Search and Sort -->
                <div class="store-controls mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="search-box">
                                <input type="text" class="form-control" id="search-input" placeholder="Search products...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="sort-controls">
                                <select class="form-select" id="sort-select">
                                    <option value="created_at:desc">Newest First</option>
                                    <option value="created_at:asc">Oldest First</option>
                                    <option value="name:asc">Name A-Z</option>
                                    <option value="name:desc">Name Z-A</option>
                                    <option value="price:asc">Price Low to High</option>
                                    <option value="price:desc">Price High to Low</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div id="products-grid" class="row">
                    <!-- Products will be loaded here via JavaScript -->
                </div>

                <!-- Loading Spinner -->
                <div id="loading" class="text-center py-4" style="display: none;">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- Pagination -->
                <div id="pagination" class="d-flex justify-content-center mt-4">
                    <!-- Pagination will be loaded here via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('additional_js')
<script>
class StoreManager {
    constructor() {
        this.currentPage = 1;
        this.filters = {
            category: 'all',
            search: '',
            minPrice: '',
            maxPrice: '',
            featured: false,
            sortBy: 'created_at',
            sortOrder: 'desc'
        };
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadProducts();
    }

    setupEventListeners() {
        // Category filters
        document.querySelectorAll('input[name="category"]').forEach(input => {
            input.addEventListener('change', () => {
                this.filters.category = input.value;
                this.currentPage = 1;
                this.loadProducts();
            });
        });

        // Price filters
        document.getElementById('min-price').addEventListener('input', (e) => {
            this.filters.minPrice = e.target.value;
            this.debounceLoadProducts();
        });

        document.getElementById('max-price').addEventListener('input', (e) => {
            this.filters.maxPrice = e.target.value;
            this.debounceLoadProducts();
        });

        // Featured filter
        document.getElementById('featured-only').addEventListener('change', (e) => {
            this.filters.featured = e.target.checked;
            this.currentPage = 1;
            this.loadProducts();
        });

        // Search
        document.getElementById('search-input').addEventListener('input', (e) => {
            this.filters.search = e.target.value;
            this.debounceLoadProducts();
        });

        // Sort
        document.getElementById('sort-select').addEventListener('change', (e) => {
            const [sortBy, sortOrder] = e.target.value.split(':');
            this.filters.sortBy = sortBy;
            this.filters.sortOrder = sortOrder;
            this.currentPage = 1;
            this.loadProducts();
        });
    }

    debounceLoadProducts() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.currentPage = 1;
            this.loadProducts();
        }, 500);
    }

    async loadProducts() {
        const loading = document.getElementById('loading');
        const grid = document.getElementById('products-grid');
        
        loading.style.display = 'block';

        try {
            const params = new URLSearchParams({
                page: this.currentPage,
                category: this.filters.category,
                search: this.filters.search,
                sortBy: this.filters.sortBy,
                sortOrder: this.filters.sortOrder,
                featured: this.filters.featured
            });

            if (this.filters.minPrice) params.append('minPrice', this.filters.minPrice);
            if (this.filters.maxPrice) params.append('maxPrice', this.filters.maxPrice);

            const response = await fetch(`/api/products?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderProducts(data.data);
                this.renderPagination(data.pagination);
            } else {
                grid.innerHTML = '<div class="col-12"><p class="text-center">Failed to load products.</p></div>';
            }
        } catch (error) {
            console.error('Error loading products:', error);
            grid.innerHTML = '<div class="col-12"><p class="text-center">Error loading products.</p></div>';
        } finally {
            loading.style.display = 'none';
        }
    }

    renderProducts(products) {
        const grid = document.getElementById('products-grid');
        
        if (products.length === 0) {
            grid.innerHTML = '<div class="col-12"><p class="text-center">No products found.</p></div>';
            return;
        }

        grid.innerHTML = products.map(product => `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card">
                    ${product.first_image ? `<img src="/images/${product.first_image}" alt="${product.name}" class="product-image">` : ''}
                    <div class="product-info">
                        <h5>${product.name}</h5>
                        <p class="product-category">${product.category.name}</p>
                        ${product.active_variants && product.active_variants.length > 0 ? 
                            `<p class="product-price">₹${new Intl.NumberFormat('en-IN').format(product.min_price)}</p>` : ''
                        }
                        <div class="product-actions">
                            <button class="btn btn-primary btn-sm" onclick="addToCart(${product.id})">Add to Cart</button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="toggleWishlist(${product.id})">♡</button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    renderPagination(pagination) {
        const paginationEl = document.getElementById('pagination');
        
        if (pagination.total_pages <= 1) {
            paginationEl.innerHTML = '';
            return;
        }

        let paginationHTML = '<nav><ul class="pagination">';
        
        // Previous button
        if (pagination.current_page > 1) {
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="storeManager.goToPage(${pagination.current_page - 1})">Previous</a></li>`;
        }

        // Page numbers
        for (let i = 1; i <= pagination.total_pages; i++) {
            if (i === pagination.current_page) {
                paginationHTML += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="storeManager.goToPage(${i})">${i}</a></li>`;
            }
        }

        // Next button
        if (pagination.has_next_page) {
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="storeManager.goToPage(${pagination.current_page + 1})">Next</a></li>`;
        }

        paginationHTML += '</ul></nav>';
        paginationEl.innerHTML = paginationHTML;
    }

    goToPage(page) {
        this.currentPage = page;
        this.loadProducts();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// Initialize store manager
const storeManager = new StoreManager();

// Placeholder functions for cart and wishlist
function addToCart(productId) {
    alert('Add to cart functionality will be implemented');
}

function toggleWishlist(productId) {
    alert('Wishlist functionality will be implemented');
}
</script>
@endsection 