<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wheyProteinCategory = Category::where('slug', 'whey-protein')->first();
        $preWorkoutCategory = Category::where('slug', 'pre-workout')->first();
        $creatineCategory = Category::where('slug', 'creatine')->first();

        // Whey Protein Products
        $wheyProtein = Product::create([
            'name' => 'Premium Whey Protein',
            'description' => 'High-quality whey protein isolate for muscle building and recovery. Each serving provides 24g of protein with essential amino acids.',
            'category_id' => $wheyProteinCategory->id,
            'brand' => 'Ge More Nutralife',
            'sku' => 'GMP-WP-001',
            'images' => [
                'WHEY PROTEIN CHOCOLATE.png',
                'WHEY PROTEIN CHOCOLATE 2.png',
                'WHEY PROTEIN CHOCOLATE 3.png',
            ],
            'tags' => ['protein', 'muscle building', 'recovery', 'post workout'],
            'features' => [
                '24g protein per serving',
                'Low in carbs and fat',
                'Easy to mix and digest',
                'Great taste',
            ],
            'specifications' => [
                'servingSize' => '30g',
                'servingsPerContainer' => '33 (1kg)',
                'nutritionFacts' => [
                    ['nutrient' => 'Protein', 'amount' => '24g', 'dailyValue' => '48%'],
                    ['nutrient' => 'Carbohydrates', 'amount' => '3g', 'dailyValue' => '1%'],
                    ['nutrient' => 'Fat', 'amount' => '1.5g', 'dailyValue' => '2%'],
                ],
            ],
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
            'seo' => [
                'meta_title' => 'Premium Whey Protein | Ge More Nutralife',
                'meta_description' => 'High-quality whey protein isolate with 24g protein per serving. Perfect for muscle building and recovery.',
                'meta_keywords' => 'whey protein, protein powder, muscle building, workout supplement',
            ],
        ]);

        // Whey Protein Variants
        $wheyProteinVariants = [
            [
                'name' => 'Chocolate',
                'size' => '1kg',
                'price' => 2499,
                'original_price' => 2999,
                'stock' => 100,
                'images' => [
                    'WHEY PROTEIN CHOCOLATE 1KG 1.png',
                    'WHEY PROTEIN CHOCOLATE 1KG 2.png',
                    'WHEY PROTEIN CHOCOLATE 1KG 3.png',
                ],
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'name' => 'Kesar Kulfi',
                'size' => '1kg',
                'price' => 2499,
                'original_price' => 2999,
                'stock' => 100,
                'images' => [
                    'WHEY PROTEIN KESAR KULFI 1KG 1.png',
                    'WHEY PROTEIN KESAR KULFI 1KG 2.png',
                    'WHEY PROTEIN KESAR KULFI 1KG 3.png',
                ],
                'is_active' => true,
                'is_default' => false,
            ],
        ];

        foreach ($wheyProteinVariants as $variant) {
            $wheyProtein->variants()->create($variant);
        }

        // Pre Workout Products
        $preWorkout = Product::create([
            'name' => 'Advanced Pre Workout',
            'description' => 'Energy-boosting pre-workout formula for enhanced performance and focus during workouts.',
            'category_id' => $preWorkoutCategory->id,
            'brand' => 'Ge More Nutralife',
            'sku' => 'GMP-PW-001',
            'images' => [
                'PREWORKOUT FRUIT PUNCH.jpg',
                'PRE WORKOUT FRUIT PUNCH 1.png',
                'PRE WORKOUT FRUIT PUNCH 2.png',
            ],
            'tags' => ['pre workout', 'energy', 'focus', 'pump'],
            'features' => [
                'Explosive energy',
                'Enhanced focus',
                'Better pumps',
                'No crash',
            ],
            'specifications' => [
                'servingSize' => '7.5g',
                'servingsPerContainer' => '30',
                'nutritionFacts' => [
                    ['nutrient' => 'Caffeine', 'amount' => '200mg', 'dailyValue' => '-'],
                    ['nutrient' => 'Beta Alanine', 'amount' => '3.2g', 'dailyValue' => '-'],
                    ['nutrient' => 'L-Citrulline', 'amount' => '6g', 'dailyValue' => '-'],
                ],
            ],
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
            'seo' => [
                'meta_title' => 'Advanced Pre Workout | Ge More Nutralife',
                'meta_description' => 'Energy-boosting pre-workout formula for enhanced performance and focus.',
                'meta_keywords' => 'pre workout, energy, focus, pump, workout supplement',
            ],
        ]);

        // Pre Workout Variants
        $preWorkoutVariants = [
            [
                'name' => 'Fruit Punch',
                'size' => '225g',
                'price' => 1499,
                'original_price' => 1799,
                'stock' => 100,
                'images' => [
                    'PRE WORKOUT FRUIT PUNCH 1.png',
                    'PRE WORKOUT FRUIT PUNCH 2.png',
                    'PRE WORKOUT FRUIT PUNCH 3.png',
                ],
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'name' => 'Tangy Orange',
                'size' => '225g',
                'price' => 1499,
                'original_price' => 1799,
                'stock' => 100,
                'images' => [
                    'PRE WORKOUT TANGY ORANGE 1.png',
                    'PRE WORKOUT TANGY ORANGE 2.png',
                    'PRE WORKOUT TANGY ORANGE 3.png',
                ],
                'is_active' => true,
                'is_default' => false,
            ],
        ];

        foreach ($preWorkoutVariants as $variant) {
            $preWorkout->variants()->create($variant);
        }

        // Creatine Products
        $creatine = Product::create([
            'name' => 'Pure Creatine Monohydrate',
            'description' => 'Pure micronized creatine monohydrate for enhanced strength and power output.',
            'category_id' => $creatineCategory->id,
            'brand' => 'Ge More Nutralife',
            'sku' => 'GMP-CR-001',
            'images' => [
                'CREATINE TANGY ORANGE.jpg',
                'CREATINE TANGY ORANGE 1.png',
                'CREATINE TANGY ORANGE 2.png',
            ],
            'tags' => ['creatine', 'strength', 'power', 'muscle'],
            'features' => [
                'Pure micronized creatine',
                'Enhanced strength',
                'Better recovery',
                'Improved performance',
            ],
            'specifications' => [
                'servingSize' => '5g',
                'servingsPerContainer' => '60',
                'nutritionFacts' => [
                    ['nutrient' => 'Creatine Monohydrate', 'amount' => '5g', 'dailyValue' => '-'],
                ],
            ],
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 3,
            'seo' => [
                'meta_title' => 'Pure Creatine Monohydrate | Ge More Nutralife',
                'meta_description' => 'Pure micronized creatine monohydrate for enhanced strength and power.',
                'meta_keywords' => 'creatine, strength, power, muscle, workout supplement',
            ],
        ]);

        // Creatine Variants
        $creatineVariants = [
            [
                'name' => 'Tangy Orange',
                'size' => '300g',
                'price' => 999,
                'original_price' => 1299,
                'stock' => 100,
                'images' => [
                    'CREATINE TANGY ORANGE 1.png',
                    'CREATINE TANGY ORANGE 2.png',
                    'CREATINE TANGY ORANGE 3.png',
                ],
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'name' => 'Unflavored',
                'size' => '300g',
                'price' => 899,
                'original_price' => 1199,
                'stock' => 100,
                'images' => [
                    'CREATINE UNFLAVORED 1.png',
                    'CREATINE UNFLAVORED 2.png',
                    'CREATINE UNFLAVORED 3.png',
                ],
                'is_active' => true,
                'is_default' => false,
            ],
        ];

        foreach ($creatineVariants as $variant) {
            $creatine->variants()->create($variant);
        }
    }
} 