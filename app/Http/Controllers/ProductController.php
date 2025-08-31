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
            ->where('products.is_active', true);

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
        $sort = $request->get('sort', 'newest');
        
        // Parse sort parameter
        switch ($sort) {
            case 'price-low':
                $sortBy = 'price';
                $sortOrder = 'asc';
                break;
            case 'price-high':
                $sortBy = 'price';
                $sortOrder = 'desc';
                break;
            case 'name-asc':
                $sortBy = 'name';
                $sortOrder = 'asc';
                break;
            case 'name-desc':
                $sortBy = 'name';
                $sortOrder = 'desc';
                break;
            case 'oldest':
                $sortBy = 'created_at';
                $sortOrder = 'asc';
                break;
            case 'newest':
            default:
                $sortBy = 'created_at';
                $sortOrder = 'desc';
                break;
        }
        
        if ($sortBy === 'price') {
            // Sort by minimum variant price using a subquery
            $query->orderByRaw("(
                SELECT MIN(pv.price) 
                FROM product_variants pv 
                WHERE pv.product_id = products.id 
                AND pv.is_active = 1
            ) {$sortOrder}");
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

    public function apiShow(Product $product)
    {
        $product->load(['category', 'activeVariants']);
        
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function apiFeatured(Request $request)
    {
        $limit = $request->get('limit', 6);
        
        $products = Product::with(['category', 'activeVariants'])
            ->active()
            ->featured()
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function apiByCategory(Request $request, $category)
    {
        $categoryModel = Category::where('slug', $category)->first();
        
        if (!$categoryModel) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $limit = $request->get('limit', 12);
        
        $products = Product::with(['category', 'activeVariants'])
            ->active()
            ->where('category_id', $categoryModel->id)
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
            'category' => $categoryModel
        ]);
    }
} 