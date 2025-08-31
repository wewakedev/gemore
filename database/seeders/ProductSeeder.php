<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories first
        $categories = [
            [
                'name' => 'Whey Protein',
                'slug' => 'whey-protein',
                'description' => 'Premium quality whey protein supplements for muscle building and recovery.',
                'image' => 'WHEY PROTEIN CHOCOLATE.png',
                'icon' => 'fas fa-dumbbell',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'seo' => [
                    'meta_title' => 'Whey Protein Supplements | Ge More Nutralife',
                    'meta_description' => 'High-quality whey protein supplements for muscle building and recovery. Available in delicious flavors.',
                    'meta_keywords' => 'whey protein, protein powder, muscle building, workout supplement'
                ]
            ],
            [
                'name' => 'Pre Workout',
                'slug' => 'pre-workout',
                'description' => 'Energy-boosting pre-workout supplements for enhanced performance.',
                'image' => 'PREWORKOUT FRUIT PUNCH.jpg',
                'icon' => 'fas fa-bolt',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'seo' => [
                    'meta_title' => 'Pre Workout Supplements | Ge More Nutralife',
                    'meta_description' => 'Energy-boosting pre-workout supplements for enhanced workout performance.',
                    'meta_keywords' => 'pre workout, energy supplement, workout booster, gym supplement'
                ]
            ],
            [
                'name' => 'Creatine',
                'slug' => 'creatine',
                'description' => 'Pure creatine supplements for strength and power.',
                'image' => 'CREATINE TANGY ORANGE.jpg',
                'icon' => 'fas fa-fire',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 3,
                'seo' => [
                    'meta_title' => 'Creatine Supplements | Ge More Nutralife',
                    'meta_description' => 'Pure creatine supplements for enhanced strength and power.',
                    'meta_keywords' => 'creatine, strength supplement, power booster, muscle supplement'
                ]
            ],
            [
                'name' => 'Mass Gainer',
                'slug' => 'mass-gainer',
                'description' => 'High-calorie mass gainers for lean muscle mass building.',
                'image' => 'MASS GAINER CHOCOLATE.png',
                'icon' => 'fas fa-chart-line',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 4,
                'seo' => [
                    'meta_title' => 'Mass Gainer Supplements | Ge More Nutralife',
                    'meta_description' => 'High-calorie mass gainers for lean muscle mass building.',
                    'meta_keywords' => 'mass gainer, weight gainer, muscle mass, bulking supplement'
                ]
            ],
            [
                'name' => 'BCAA',
                'slug' => 'bcaa',
                'description' => 'Branched-chain amino acids for muscle recovery and endurance.',
                'image' => 'BCAA WATERMELON.png',
                'icon' => 'fas fa-leaf',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 5,
                'seo' => [
                    'meta_title' => 'BCAA Supplements | Ge More Nutralife',
                    'meta_description' => 'Branched-chain amino acids for muscle recovery and endurance.',
                    'meta_keywords' => 'BCAA, amino acids, muscle recovery, endurance supplement'
                ]
            ],
            [
                'name' => 'Glutamine',
                'slug' => 'glutamine',
                'description' => 'Pure L-Glutamine for muscle recovery and immune support.',
                'image' => 'GLUTAMINE UNFLAVORED.png',
                'icon' => 'fas fa-shield-alt',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 6,
                'seo' => [
                    'meta_title' => 'Glutamine Supplements | Ge More Nutralife',
                    'meta_description' => 'Pure L-Glutamine for muscle recovery and immune support.',
                    'meta_keywords' => 'glutamine, L-glutamine, muscle recovery, immune support'
                ]
            ]
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Get created categories
        $wheyProtein = Category::where('slug', 'whey-protein')->first();
        $preWorkout = Category::where('slug', 'pre-workout')->first();
        $creatine = Category::where('slug', 'creatine')->first();
        $massGainer = Category::where('slug', 'mass-gainer')->first();
        $bcaa = Category::where('slug', 'bcaa')->first();
        $glutamine = Category::where('slug', 'glutamine')->first();

        // Create products for all categories
        $newProducts = [
            // Whey Protein
            [
                'name' => 'Premium Whey Protein',
                'description' => 'High-quality whey protein isolate for muscle building and recovery.',
                'category_id' => $wheyProtein->id,
                'brand' => 'Ge More Nutralife',
                'sku' => 'GMP-WP-001',
                'images' => ['WHEY PROTEIN CHOCOLATE.png', 'WHEY PROTEIN CHOCOLATE 2.png', 'WHEY PROTEIN CHOCOLATE 3.png'],
                'tags' => ['whey protein', 'protein powder', 'muscle building', 'recovery'],
                'features' => ['High protein content', 'Low fat', 'Instant mixing', 'Great taste'],
                'specifications' => [
                    'servingSize' => '30g',
                    'servingsPerContainer' => '33 (1kg)',
                    'nutritionFacts' => [
                        ['nutrient' => 'Calories', 'amount' => '120', 'dailyValue' => '6%'],
                        ['nutrient' => 'Protein', 'amount' => '24g', 'dailyValue' => '48%'],
                        ['nutrient' => 'Carbohydrates', 'amount' => '3g', 'dailyValue' => '1%']
                    ]
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'seo' => [
                    'meta_title' => 'Premium Whey Protein | Ge More Nutralife',
                    'meta_description' => 'High-quality whey protein for muscle building and recovery.',
                    'meta_keywords' => 'whey protein, protein powder, muscle building, workout supplement'
                ],
                'variants' => [
                    [
                        'name' => 'Chocolate',
                        'size' => '1kg',
                        'price' => 2499.00,
                        'original_price' => 2999.00,
                        'stock' => 100,
                        'images' => ['WHEY PROTEIN CHOCOLATE 1.png', 'WHEY PROTEIN CHOCOLATE 2.png', 'WHEY PROTEIN CHOCOLATE 3.png'],
                        'is_active' => true,
                        'is_default' => true
                    ],
                    [
                        'name' => 'Kesar Kulfi',
                        'size' => '1kg',
                        'price' => 2499.00,
                        'original_price' => 2999.00,
                        'stock' => 100,
                        'images' => ['WHEY PROTEIN KESAR KULFI 1.png', 'WHEY PROTEIN KESAR KULFI 2.png', 'WHEY PROTEIN KESAR KULFI 3.png'],
                        'is_active' => true,
                        'is_default' => false
                    ]
                ]
            ],
            // Pre Workout
            [
                'name' => 'Premium Pre Workout',
                'description' => 'Energy-boosting pre-workout supplement for enhanced performance and focus.',
                'category_id' => $preWorkout->id,
                'brand' => 'Ge More Nutralife',
                'sku' => 'GMP-PW-001',
                'images' => ['PREWORKOUT FRUIT PUNCH.jpg', 'PREWORKOUT FRUIT PUNCH 2.png', 'PREWORKOUT FRUIT PUNCH 3.png'],
                'tags' => ['pre workout', 'energy', 'focus', 'performance'],
                'features' => ['Energy boost', 'Mental focus', 'Endurance', 'Great taste'],
                'specifications' => [
                    'servingSize' => '15g',
                    'servingsPerContainer' => '20',
                    'nutritionFacts' => [
                        ['nutrient' => 'Calories', 'amount' => '60', 'dailyValue' => '3%'],
                        ['nutrient' => 'Caffeine', 'amount' => '200mg', 'dailyValue' => '-'],
                        ['nutrient' => 'Creatine', 'amount' => '3g', 'dailyValue' => '-']
                    ]
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'seo' => [
                    'meta_title' => 'Premium Pre Workout | Ge More Nutralife',
                    'meta_description' => 'Energy-boosting pre-workout for enhanced performance.',
                    'meta_keywords' => 'pre workout, energy supplement, workout booster, gym supplement'
                ],
                'variants' => [
                    [
                        'name' => 'Fruit Punch',
                        'size' => '300g',
                        'price' => 1999.00,
                        'original_price' => 2499.00,
                        'stock' => 80,
                        'images' => ['PRE WORKOUT FRUIT PUNCH 1.png', 'PRE WORKOUT FRUIT PUNCH 2.png', 'PRE WORKOUT FRUIT PUNCH 3.png'],
                        'is_active' => true,
                        'is_default' => true
                    ],
                    [
                        'name' => 'Tangy Orange',
                        'size' => '300g',
                        'price' => 1999.00,
                        'original_price' => 2499.00,
                        'stock' => 80,
                        'images' => ['PRE WORKOUT TANGY ORANGE 1.png', 'PRE WORKOUT TANGY ORANGE 2.png', 'PRE WORKOUT TANGY ORANGE 3.png'],
                        'is_active' => true,
                        'is_default' => false
                    ]
                ]
            ],
            // Creatine
            [
                'name' => 'Premium Creatine Monohydrate',
                'description' => 'Pure creatine monohydrate for enhanced strength and power.',
                'category_id' => $creatine->id,
                'brand' => 'Ge More Nutralife',
                'sku' => 'GMP-CRE-001',
                'images' => ['CREATINE TANGY ORANGE.jpg', 'CREATINE TANGY ORANGE 2.png', 'CREATINE TANGY ORANGE 3.png'],
                'tags' => ['creatine', 'strength', 'power', 'muscle'],
                'features' => ['Pure creatine', 'Strength boost', 'Power enhancement', 'Muscle growth'],
                'specifications' => [
                    'servingSize' => '5g',
                    'servingsPerContainer' => '60',
                    'nutritionFacts' => [
                        ['nutrient' => 'Creatine Monohydrate', 'amount' => '5g', 'dailyValue' => '-'],
                        ['nutrient' => 'Calories', 'amount' => '0', 'dailyValue' => '0%']
                    ]
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 3,
                'seo' => [
                    'meta_title' => 'Premium Creatine Monohydrate | Ge More Nutralife',
                    'meta_description' => 'Pure creatine for enhanced strength and power.',
                    'meta_keywords' => 'creatine, strength supplement, power booster, muscle supplement'
                ],
                'variants' => [
                    [
                        'name' => 'Tangy Orange',
                        'size' => '300g',
                        'price' => 1499.00,
                        'original_price' => 1899.00,
                        'stock' => 90,
                        'images' => ['CREATINE TANGY ORANGE 1.png', 'CREATINE TANGY ORANGE 2.png', 'CREATINE TANGY ORANGE 3.png'],
                        'is_active' => true,
                        'is_default' => true
                    ],
                    [
                        'name' => 'Unflavored',
                        'size' => '300g',
                        'price' => 1499.00,
                        'original_price' => 1899.00,
                        'stock' => 90,
                        'images' => ['CREATINE UNFLAVORED 1.png', 'CREATINE UNFLAVORED 2.png', 'CREATINE UNFLAVORED 3.png'],
                        'is_active' => true,
                        'is_default' => false
                    ]
                ]
            ],
            // Mass Gainer
            // Mass Gainer
            [
                'name' => 'Premium Mass Gainer',
                'description' => 'High-calorie mass gainer with quality proteins and carbohydrates for lean muscle mass building.',
                'category_id' => $massGainer->id,
                'brand' => 'Ge More Nutralife',
                'sku' => 'GMP-MG-001',
                'images' => ['MASS GAINER CHOCOLATE.png', 'MASS GAINER CHOCOLATE 2.png', 'MASS GAINER CHOCOLATE 3.png'],
                'tags' => ['mass gainer', 'weight gain', 'muscle mass', 'bulking'],
                'features' => ['High calorie formula', 'Quality proteins', 'Complex carbohydrates', 'Added vitamins'],
                'specifications' => [
                    'servingSize' => '100g',
                    'servingsPerContainer' => '30 (3kg)',
                    'nutritionFacts' => [
                        ['nutrient' => 'Calories', 'amount' => '380', 'dailyValue' => '19%'],
                        ['nutrient' => 'Protein', 'amount' => '20g', 'dailyValue' => '40%'],
                        ['nutrient' => 'Carbohydrates', 'amount' => '65g', 'dailyValue' => '22%']
                    ]
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 4,
                'seo' => [
                    'meta_title' => 'Premium Mass Gainer | Ge More Nutralife',
                    'meta_description' => 'High-calorie mass gainer for lean muscle mass building and weight gain.',
                    'meta_keywords' => 'mass gainer, weight gainer, muscle mass, bulking supplement'
                ],
                'variants' => [
                    [
                        'name' => 'Chocolate',
                        'size' => '3kg',
                        'price' => 3499.00,
                        'original_price' => 3999.00,
                        'stock' => 50,
                        'images' => ['MASS GAINER CHOCOLATE 1.png', 'MASS GAINER CHOCOLATE 2.png', 'MASS GAINER CHOCOLATE 3.png'],
                        'is_active' => true,
                        'is_default' => true
                    ],
                    [
                        'name' => 'Vanilla',
                        'size' => '3kg',
                        'price' => 3499.00,
                        'original_price' => 3999.00,
                        'stock' => 50,
                        'images' => ['MASS GAINER VANILLA 1.png', 'MASS GAINER VANILLA 2.png', 'MASS GAINER VANILLA 3.png'],
                        'is_active' => true,
                        'is_default' => false
                    ]
                ]
            ],
            // BCAA
            [
                'name' => 'Premium BCAA 2:1:1',
                'description' => 'Branched-chain amino acids in optimal 2:1:1 ratio for muscle recovery and endurance.',
                'category_id' => $bcaa->id,
                'brand' => 'Ge More Nutralife',
                'sku' => 'GMP-BCAA-001',
                'images' => ['BCAA WATERMELON.png', 'BCAA WATERMELON 2.png', 'BCAA WATERMELON 3.png'],
                'tags' => ['BCAA', 'amino acids', 'recovery', 'endurance'],
                'features' => ['2:1:1 ratio', 'Instant mixing', 'Great taste', 'Zero sugar'],
                'specifications' => [
                    'servingSize' => '10g',
                    'servingsPerContainer' => '30',
                    'nutritionFacts' => [
                        ['nutrient' => 'L-Leucine', 'amount' => '5g', 'dailyValue' => '-'],
                        ['nutrient' => 'L-Isoleucine', 'amount' => '2.5g', 'dailyValue' => '-'],
                        ['nutrient' => 'L-Valine', 'amount' => '2.5g', 'dailyValue' => '-']
                    ]
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 5,
                'seo' => [
                    'meta_title' => 'Premium BCAA 2:1:1 | Ge More Nutralife',
                    'meta_description' => 'Branched-chain amino acids in optimal ratio for muscle recovery.',
                    'meta_keywords' => 'BCAA, amino acids, muscle recovery, endurance supplement'
                ],
                'variants' => [
                    [
                        'name' => 'Watermelon',
                        'size' => '300g',
                        'price' => 1799.00,
                        'original_price' => 2199.00,
                        'stock' => 75,
                        'images' => ['BCAA WATERMELON 1.png', 'BCAA WATERMELON 2.png', 'BCAA WATERMELON 3.png'],
                        'is_active' => true,
                        'is_default' => true
                    ],
                    [
                        'name' => 'Green Apple',
                        'size' => '300g',
                        'price' => 1799.00,
                        'original_price' => 2199.00,
                        'stock' => 75,
                        'images' => ['BCAA GREEN APPLE 1.png', 'BCAA GREEN APPLE 2.png', 'BCAA GREEN APPLE 3.png'],
                        'is_active' => true,
                        'is_default' => false
                    ]
                ]
            ],
            // Glutamine
            [
                'name' => 'Pure L-Glutamine',
                'description' => 'Pure L-Glutamine for muscle recovery, immune support, and gut health.',
                'category_id' => $glutamine->id,
                'brand' => 'Ge More Nutralife',
                'sku' => 'GMP-GLU-001',
                'images' => ['GLUTAMINE UNFLAVORED.png', 'GLUTAMINE UNFLAVORED 2.png', 'GLUTAMINE UNFLAVORED 3.png'],
                'tags' => ['glutamine', 'recovery', 'immune support', 'gut health'],
                'features' => ['Pure L-Glutamine', 'Muscle recovery', 'Immune support', 'Unflavored'],
                'specifications' => [
                    'servingSize' => '5g',
                    'servingsPerContainer' => '60',
                    'nutritionFacts' => [
                        ['nutrient' => 'L-Glutamine', 'amount' => '5g', 'dailyValue' => '-']
                    ]
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 6,
                'seo' => [
                    'meta_title' => 'Pure L-Glutamine | Ge More Nutralife',
                    'meta_description' => 'Pure L-Glutamine for muscle recovery and immune support.',
                    'meta_keywords' => 'glutamine, L-glutamine, muscle recovery, immune support'
                ],
                'variants' => [
                    [
                        'name' => 'Unflavored',
                        'size' => '300g',
                        'price' => 1299.00,
                        'original_price' => 1599.00,
                        'stock' => 80,
                        'images' => ['GLUTAMINE UNFLAVORED 1.png', 'GLUTAMINE UNFLAVORED 2.png', 'GLUTAMINE UNFLAVORED 3.png'],
                        'is_active' => true,
                        'is_default' => true
                    ]
                ]
            ]
        ];

        // Create new products and their variants
        foreach ($newProducts as $productData) {
            // Check if product already exists
            $existingProduct = Product::where('sku', $productData['sku'])->first();
            if ($existingProduct) {
                continue; // Skip if product already exists
            }

            $variants = $productData['variants'];
            unset($productData['variants']);
            
            $product = Product::create($productData);
            
            foreach ($variants as $variantData) {
                $variantData['product_id'] = $product->id;
                ProductVariant::create($variantData);
            }
        }

        $this->command->info('Products seeded successfully!');
    }
}
