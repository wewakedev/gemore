<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Ge More Nutralife - Premium Sports Supplements')</title>
    <meta name="description" content="@yield('description', 'Ge More Nutralife offers premium quality sports supplements including whey protein, pre-workout, creatine, and kesar supplements. GMP & FSSAI certified for your fitness journey.')" />
    <meta name="keywords" content="@yield('keywords', 'whey protein, sports supplements, pre-workout, creatine, kesar supplements, fitness nutrition, GMP certified, FSSAI certified, muscle building, protein powder')" />
    <meta name="author" content="Ge More Nutralife" />
    <meta name="robots" content="index, follow" />

    <!-- Favicon -->
    <link rel="icon" href="images/fevicon.png" type="image/png" />
    <link rel="apple-touch-icon" href="images/fevicon.png" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Critical CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/responsive.css" />
    <link rel="stylesheet" href="css/hero-custom.css" />
    <link rel="stylesheet" href="css/navbar-custom.css" />
    <link rel="stylesheet" href="css/theme.css" />
    <link rel="stylesheet" href="css/font-awesome.min.css" />
    <link rel="stylesheet" href="css/icomoon.css" />

    <!-- Additional CSS files -->
    <link rel="stylesheet" href="css/animate.min.css" />
    <link rel="stylesheet" href="css/normalize.css" />
    <link rel="stylesheet" href="css/meanmenu.css" />
    <link rel="stylesheet" href="css/owl.carousel.min.css" />
    <link rel="stylesheet" href="css/slick.css" />
    <link rel="stylesheet" href="css/jquery-ui.css" />
    <link rel="stylesheet" href="css/nice-select.css" />

    <!-- Slick Slider CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    @yield('additional_css')
</head>

<body class="main-layout">
    <div id="myNav" class="menu_sid">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
        <div class="menu_sid-content">
            <a href="#products">Our Products</a>
            <a href="#about">About Us</a>
            <a href="#contact">Contact Us</a>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="custom-navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Ge More Nutralife" class="logo">
                </a>
            </div>
            <div class="navbar-toggle" id="navbar-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <ul class="navbar-links" id="navbar-links">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ route('store') }}" class="{{ request()->routeIs('store') ? 'active' : '' }}">Store</a></li>
                <li><a href="{{ route('cart') }}" class="{{ request()->routeIs('cart') ? 'active' : '' }}">Cart</a></li>
                <li><a href="{{ route('home') }}#products">Products</a></li>
                <li><a href="{{ route('home') }}#about">About Us</a></li>
                <li><a href="{{ route('home') }}#contact">Contact Us</a></li>
                <li class="cart-nav">
                    <a href="#" class="cart-toggle" id="cart-toggle">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count" id="cart-count">0</span>
                    </a>
                </li>
                <li class="wishlist-nav">
                    <a href="#" class="wishlist-toggle" id="wishlist-toggle">
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
                    <div class="footer-section">
                        <h3>Ge More Nutralife</h3>
                        <p>Premium quality sports supplements for your fitness journey. GMP & FSSAI certified products.</p>
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="footer-section">
                        <h3>Quick Links</h3>
                        <ul class="footer-links">
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li><a href="{{ route('store') }}">Products</a></li>
                            <li><a href="{{ route('home') }}#about">About Us</a></li>
                            <li><a href="{{ route('home') }}#contact">Contact</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="footer-section">
                        <h3>Contact Info</h3>
                        <div class="contact-info">
                            <p><i class="fas fa-phone"></i> +91 1234567890</p>
                            <p><i class="fas fa-envelope"></i> info@gemorenutrallife.com</p>
                            <p><i class="fas fa-map-marker-alt"></i> India</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="row">
                    <div class="col-12 text-center">
                        <p>&copy; {{ date('Y') }} Ge More Nutralife. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar" id="cart-sidebar">
        <div class="cart-header">
            <h3>Shopping Cart</h3>
            <button class="close-cart" id="close-cart">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-content">
            <div class="cart-items" id="cart-items">
                <!-- Cart items will be dynamically loaded here -->
            </div>
            <div class="cart-empty" id="cart-empty" style="display: none">
                <i class="fas fa-shopping-cart"></i>
                <p>Your cart is empty</p>
            </div>
        </div>
        <div class="cart-footer">
            <div class="cart-total" id="cart-total">
                <!-- Cart total will be displayed here -->
            </div>
            <div class="cart-actions">
                <a href="{{ route('cart') }}" class="btn btn-primary">View Cart</a>
                <button class="btn btn-success" id="checkout-btn">Checkout</button>
            </div>
        </div>
    </div>

    <!-- Wishlist Sidebar -->
    <div class="wishlist-sidebar" id="wishlist-sidebar">
        <div class="wishlist-header">
            <h3>Wishlist</h3>
            <button class="close-wishlist" id="close-wishlist">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="wishlist-content">
            <div class="wishlist-items" id="wishlist-items">
                <!-- Wishlist items will be dynamically loaded here -->
            </div>
            <div class="wishlist-empty" id="wishlist-empty" style="display: none">
                <i class="fas fa-heart"></i>
                <p>Your wishlist is empty</p>
            </div>
        </div>
    </div>

    <!-- Essential JavaScript -->
    <script src="js/jquery-3.0.0.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/cart-functions.js') }}"></script>
    
    <!-- Slick Slider JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    
    <script src="js/frontend-api.js"></script>
    <script src="js/optimized.js"></script>

    @yield('additional_js')
</body>
</html> 