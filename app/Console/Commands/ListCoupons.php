<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Illuminate\Console\Command;

class ListCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupons:list {--active : Show only active coupons}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available coupons with their details';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Coupon::query();
        
        if ($this->option('active')) {
            $query->active()->valid();
        }
        
        $coupons = $query->orderBy('code')->get();
        
        if ($coupons->isEmpty()) {
            $this->info('No coupons found.');
            return;
        }
        
        $headers = [
            'Code',
            'Name',
            'Type',
            'Value',
            'Min Order',
            'Max Discount',
            'Usage Limit',
            'Used',
            'Valid Until',
            'Status'
        ];
        
        $rows = $coupons->map(function ($coupon) {
            return [
                $coupon->code,
                $coupon->name,
                ucfirst($coupon->type),
                $coupon->type === 'percentage' ? $coupon->value . '%' : '₹' . $coupon->value,
                '₹' . $coupon->minimum_order_amount,
                $coupon->maximum_discount ? '₹' . $coupon->maximum_discount : 'No limit',
                $coupon->usage_limit_total ?? 'Unlimited',
                $coupon->usage_count,
                $coupon->valid_until->format('Y-m-d'),
                $coupon->is_active ? '✅ Active' : '❌ Inactive'
            ];
        });
        
        $this->table($headers, $rows);
        
        $this->info("\nTotal coupons: " . $coupons->count());
        
        if (!$this->option('active')) {
            $activeCoupons = $coupons->where('is_active', true)->count();
            $this->info("Active coupons: " . $activeCoupons);
        }
        
        $this->newLine();
        $this->info('💡 Tip: Use --active flag to show only active and valid coupons');
        $this->info('💡 Example usage in frontend: WELCOME10, SAVE100, NEWUSER15, etc.');
    }
}
