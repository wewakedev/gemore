<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::active()->orderBy('sort_order')->get();
        
        return view('store', compact('categories'));
    }

    public function apiIndex(Request $request)
    {
        $query = Product::with(['category', 'activeVariants'])
            ->active();

        // Category filter
        if ($request->has('category') && $request->category !== 'all') {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        // Price filter
        if ($request->has('minPrice') || $request->has('maxPrice')) {
            $query->whereHas('activeVariants', function ($q) use ($request) {
                if ($request->has('minPrice')) {
                    $q->where('price', '>=', $request->minPrice);
                }
                if ($request->has('maxPrice')) {
                    $q->where('price', '<=', $request->maxPrice);
                }
            });
        }

        // Featured filter
        if ($request->has('featured') && $request->featured === 'true') {
            $query->featured();
        }

        // Sorting
        $sortBy = $request->get('sortBy', 'created_at');
        $sortOrder = $request->get('sortOrder', 'desc');
        
        if ($sortBy === 'price') {
            // Sort by minimum variant price
            $query->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                  ->where('product_variants.is_active', true)
                  ->orderBy('product_variants.price', $sortOrder)
                  ->select('products.*')
                  ->groupBy('products.id');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 12);
        
        $products = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'total_pages' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'has_next_page' => $products->hasMorePages(),
            ]
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'activeVariants', 'approvedReviews.user']);
        
        $relatedProducts = Product::with(['category', 'activeVariants'])
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
} 