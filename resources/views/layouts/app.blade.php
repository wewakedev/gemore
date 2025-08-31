<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Essential meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Ge More Nutralife - Premium Sports Supplements')</title>
    <meta name="description" content="@yield('description', 'Ge More Nutralife offers premium quality sports supplements including whey protein, pre-workout, creatine, and kesar supplements. GMP & FSSAI certified for your fitness journey.')">
    <meta name="keywords" content="@yield('keywords', 'whey protein, sports supplements, pre-workout, creatine, kesar supplements, fitness nutrition, GMP certified, FSSAI certified, muscle building, protein powder')">
    <meta name="author" content="Ge More Nutralife" />
    <meta name="robots" content="index, follow" />

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('og_title', 'Ge More Nutralife - Premium Sports Supplements')" />
    <meta property="og:description" content="@yield('og_description', 'Premium quality sports supplements including whey protein, pre-workout, and creatine. GMP & FSSAI certified for your fitness journey.')" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ asset('images/aboutgemore.jpg') }}" />
    <meta property="og:site_name" content="Ge More Nutralife" />

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/fevicon.png') }}" type="image/png" />

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/hero-custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/navbar-custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/icomoon.css') }}" />
    
    @yield('additional_css')
</head>
<body>
    <!-- Navigation -->
    <nav class="custom-navbar">
        <div class="navbar-container">
            <div class="navbar-logo">Ge More Nutralife</div>

            <!-- Mobile Icons (Always Visible on Mobile) -->
            <div class="mobile-nav-icons">
                <a href="#" class="cart-toggle">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="mobile-cart-count">0</span>
                </a>
                <a href="#" class="wishlist-toggle">
                    <i class="fas fa-heart"></i>
                    <span class="wishlist-count" id="mobile-wishlist-count">0</span>
                </a>
            </div>

            <div class="navbar-toggle" id="navbar-toggle">
                <span></span><span></span><span></span>
            </div>

            <ul class="navbar-links" id="navbar-links">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ route('store') }}" class="{{ request()->routeIs('store') ? 'active' : '' }}">Store</a></li>
                <li><a href="{{ route('cart') }}" class="{{ request()->routeIs('cart') ? 'active' : '' }}">Cart</a></li>
                <li><a href="{{ route('home') }}#products">Products</a></li>
                <li><a href="{{ route('home') }}#about">About Us</a></li>
                <li><a href="{{ route('home') }}#contact">Contact Us</a></li>
                <li class="cart-nav">
                    <a href="#" class="cart-toggle">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count" id="cart-count">0</span>
                    </a>
                </li>
                <li class="wishlist-nav">
                    <a href="#" class="wishlist-toggle">
                        <i class="fas fa-heart"></i>
                        <span class="wishlist-count" id="wishlist-count">0</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Ge More Nutralife</h5>
                    <p>Premium quality sports supplements for your fitness journey. GMP & FSSAI certified.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('store') }}">Store</a></li>
                        <li><a href="{{ route('home') }}#about">About Us</a></li>
                        <li><a href="{{ route('home') }}#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Info</h5>
                    <p><i class="fas fa-phone"></i> +91 92117 98913</p>
                    <p><i class="fas fa-envelope"></i> info@gemorenutralife.com</p>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; {{ date('Y') }} Ge More Nutralife. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript Files -->
    <script src="{{ asset('js/jquery-3.0.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/optimized.js') }}"></script>
    <script src="{{ asset('js/store.js') }}"></script>
    <script src="{{ asset('js/cart.js') }}"></script>
    
    @yield('additional_js')
</body>
</html> 