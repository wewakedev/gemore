@extends('layouts.app')

@section('title', 'Shopping Cart - Ge More Nutralife')

@section('content')
<!-- Cart Header -->
<section class="cart-header py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1>Shopping Cart</h1>
                <p>Review your selected items</p>
            </div>
        </div>
    </div>
</section>

<!-- Cart Content -->
<section class="cart-content py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div id="cart-container">
                    <!-- Cart items will be loaded here via JavaScript -->
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h4>Your cart is empty</h4>
                        <p>Add some products to get started!</p>
                        <a href="{{ route('store') }}" class="btn btn-primary">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('additional_js')
<script>
// Cart functionality will be implemented here
// For now, it's just a placeholder
console.log('Cart page loaded');
</script>
@endsection 