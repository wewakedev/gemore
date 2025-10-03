<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantSize;
use App\Models\Category;

class TestProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a category
        $category = Category::firstOrCreate(
            ['name' => 'Supplements'],
            [
                'description' => 'Nutritional supplements',
                'is_active' => true,
                'sort_order' => 1
            ]
        );

        // Create a test product
        $product = Product::create([
            'name' => 'Whey Protein Premium',
            'description' => 'High-quality whey protein isolate with great taste and mixability',
            'category_id' => $category->id,
            'brand' => 'Ge More Nutralife',
            'sku' => 'GM-WHEY-001',
            'images' => ['product_choco.jpeg', 'product_fruit.jpeg', 'product_kesar.png'],
            'tags' => ['protein', 'whey', 'muscle building', 'recovery'],
            'features' => [
                '25g protein per serving',
                'Low fat and low carb',
                'Easy to digest',
                'Great taste',
                'Quick absorption'
            ],
            'specifications' => [
                'serving_size' => '30g',
                'servings_per_container' => '33',
                'protein_per_serving' => '25g'
            ],
            'ratings_average' => 4.5,
            'ratings_count' => 120,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
            'seo' => [
                'slug' => 'whey-protein-premium',
                'meta_title' => 'Whey Protein Premium - Ge More Nutralife',
                'meta_description' => 'Premium whey protein isolate for muscle building and recovery'
            ]
        ]);

        // Create Chocolate variant
        $chocolateVariant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Chocolate',
            'price' => 2999.00, // Default price (will be overridden by sizes)
            'original_price' => 3499.00,
            'stock' => 100,
            'images' => ['WHEY PROTEIN CHOCOLATE 1KG 1.png', 'WHEY PROTEIN CHOCOLATE 1KG 2.png'],
            'is_active' => true,
            'is_default' => true,
            'sort_order' => 1
        ]);

        // Add sizes for Chocolate variant
        VariantSize::create([
            'product_variant_id' => $chocolateVariant->id,
            'size_name' => '1kg',
            'size_display_name' => '1 Kilogram',
            'price' => 2999.00,
            'original_price' => 3499.00,
            'stock' => 50,
            'is_active' => true,
            'is_default' => true,
            'sort_order' => 1
        ]);

        VariantSize::create([
            'product_variant_id' => $chocolateVariant->id,
            'size_name' => '2kg',
            'size_display_name' => '2 Kilogram',
            'price' => 5499.00,
            'original_price' => 6499.00,
            'stock' => 30,
            'is_active' => true,
            'is_default' => false,
            'sort_order' => 2
        ]);

        // Create Vanilla variant
        $vanillaVariant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Vanilla',
            'price' => 2999.00,
            'original_price' => 3499.00,
            'stock' => 80,
            'images' => ['product_unflavoured.png'],
            'is_active' => true,
            'is_default' => false,
            'sort_order' => 2
        ]);

        // Add sizes for Vanilla variant
        VariantSize::create([
            'product_variant_id' => $vanillaVariant->id,
            'size_name' => '1kg',
            'size_display_name' => '1 Kilogram',
            'price' => 2999.00,
            'original_price' => 3499.00,
            'stock' => 40,
            'is_active' => true,
            'is_default' => true,
            'sort_order' => 1
        ]);

        VariantSize::create([
            'product_variant_id' => $vanillaVariant->id,
            'size_name' => '2kg',
            'size_display_name' => '2 Kilogram',
            'price' => 5499.00,
            'original_price' => 6499.00,
            'stock' => 40,
            'is_active' => true,
            'is_default' => false,
            'sort_order' => 2
        ]);

        // Create Strawberry variant
        $strawberryVariant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Strawberry',
            'price' => 3199.00,
            'original_price' => 3699.00,
            'stock' => 60,
            'images' => ['product_fruit.jpeg'],
            'is_active' => true,
            'is_default' => false,
            'sort_order' => 3
        ]);

        // Add sizes for Strawberry variant
        VariantSize::create([
            'product_variant_id' => $strawberryVariant->id,
            'size_name' => '1kg',
            'size_display_name' => '1 Kilogram',
            'price' => 3199.00,
            'original_price' => 3699.00,
            'stock' => 30,
            'is_active' => true,
            'is_default' => true,
            'sort_order' => 1
        ]);

        VariantSize::create([
            'product_variant_id' => $strawberryVariant->id,
            'size_name' => '2kg',
            'size_display_name' => '2 Kilogram',
            'price' => 5799.00,
            'original_price' => 6899.00,
            'stock' => 30,
            'is_active' => true,
            'is_default' => false,
            'sort_order' => 2
        ]);

        VariantSize::create([
            'product_variant_id' => $strawberryVariant->id,
            'size_name' => '500g',
            'size_display_name' => '500 Grams',
            'price' => 1699.00,
            'original_price' => 1999.00,
            'stock' => 0, // Out of stock
            'is_active' => true,
            'is_default' => false,
            'sort_order' => 3
        ]);

        // Create Mango variant WITHOUT sizes (to test optional size functionality)
        $mangoVariant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Mango',
            'price' => 2799.00,
            'original_price' => 3299.00,
            'stock' => 50,
            'images' => ['product_kesar.png'],
            'is_active' => true,
            'is_default' => false,
            'sort_order' => 4
        ]);
        // Note: No sizes added for Mango variant - can be added directly to cart

        $this->command->info('Test product created successfully!');
        $this->command->info('Product: ' . $product->name);
        $this->command->info('Variants: Chocolate, Vanilla, Strawberry, Mango');
        $this->command->info('Sizes: 1kg, 2kg for Chocolate/Vanilla, 1kg/2kg/500g for Strawberry');
        $this->command->info('Note: Mango variant has NO sizes - can be added directly to cart');
    }
}

