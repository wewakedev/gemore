<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Str;

class GenerateProductSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:generate-slugs {--force : Force regenerate all slugs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SEO slugs for products that don\'t have them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating product slugs...');
        
        $force = $this->option('force');
        $updated = 0;
        
        $query = Product::query();
        
        if (!$force) {
            // Only update products without slugs
            $query->where(function($q) {
                $q->whereNull('seo->slug')
                  ->orWhere('seo->slug', '');
            });
        }
        
        $products = $query->get();
        
        if ($products->isEmpty()) {
            $this->info('No products need slug generation.');
            return;
        }
        
        $this->info("Found {$products->count()} products to process.");
        
        $progressBar = $this->output->createProgressBar($products->count());
        $progressBar->start();
        
        foreach ($products as $product) {
            $seo = $product->seo ?? [];
            
            if ($force || empty($seo['slug'])) {
                $baseSlug = Str::slug($product->name);
                $slug = $baseSlug;
                $counter = 1;
                
                // Ensure slug is unique
                while (Product::where('seo->slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $seo['slug'] = $slug;
                $product->seo = $seo;
                $product->save();
                
                $updated++;
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        $this->info("Successfully generated slugs for {$updated} products.");
    }
}
