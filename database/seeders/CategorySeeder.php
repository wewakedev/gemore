<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
                    'meta_keywords' => 'whey protein, protein powder, muscle building, workout supplement',
                ],
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
                    'meta_keywords' => 'pre workout, energy supplement, workout booster, gym supplement',
                ],
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
                    'meta_keywords' => 'creatine, strength supplement, power booster, muscle supplement',
                ],
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 