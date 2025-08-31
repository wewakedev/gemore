@extends('layouts.app')

@section('title', 'Ge More Nutralife - Premium Sports Supplements | Whey Protein, Pre-Workout & More')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="hero-title">Premium Sports Supplements</h1>
                    <p class="hero-subtitle">Fuel your fitness journey with our GMP & FSSAI certified supplements</p>
                    <div class="hero-buttons">
                        <a href="{{ route('store') }}" class="btn btn-primary">Shop Now</a>
                        <a href="#about" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image">
                    <img src="{{ asset('images/aboutgemore.jpg') }}" alt="Ge More Nutralife Products" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
@if($featuredProducts->count() > 0)
<section id="products" class="products-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2>Featured Products</h2>
                <p>Discover our premium range of sports supplements</p>
            </div>
        </div>
        <div class="row">
            @foreach($featuredProducts as $product)
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="product-card">
                    @if($product->first_image)
                    <img src="{{ asset('images/' . $product->first_image) }}" alt="{{ $product->name }}" class="product-image">
                    @endif
                    <div class="product-info">
                        <h5>{{ $product->name }}</h5>
                        <p class="product-category">{{ $product->category->name }}</p>
                        @if($product->activeVariants->count() > 0)
                        <p class="product-price">₹{{ number_format($product->min_price) }}</p>
                        @endif
                        <div class="product-actions">
                            <button class="btn btn-primary btn-sm">Add to Cart</button>
                            <button class="btn btn-outline-secondary btn-sm">♡</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('store') }}" class="btn btn-primary">View All Products</a>
        </div>
    </div>
</section>
@endif

<!-- About Section -->
<section id="about" class="about-section py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2>About Ge More Nutralife</h2>
                <p>We are committed to providing premium quality sports supplements that help you achieve your fitness goals. Our products are GMP & FSSAI certified, ensuring the highest standards of quality and safety.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> GMP Certified Manufacturing</li>
                    <li><i class="fas fa-check text-success"></i> FSSAI Approved Products</li>
                    <li><i class="fas fa-check text-success"></i> Premium Quality Ingredients</li>
                    <li><i class="fas fa-check text-success"></i> Scientifically Formulated</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('images/aboutgemore.jpg') }}" alt="About Ge More" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="contact-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2>Get In Touch</h2>
                <p>Have questions? We'd love to hear from you.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <form id="contact-form" class="contact-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input type="tel" class="form-control" name="phone" placeholder="Your Phone">
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="text" class="form-control" name="subject" placeholder="Subject">
                        </div>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" name="message" rows="5" placeholder="Your Message" required></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('additional_js')
<script>
document.getElementById('contact-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    fetch('/api/contact', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            this.reset();
        } else {
            alert(data.error || 'Something went wrong');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong. Please try again.');
    });
});
</script>
@endsection 