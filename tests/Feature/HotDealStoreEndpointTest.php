<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\HotDeal;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotDealStoreEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_products_endpoint_includes_hot_deal_pricing_for_visible_deals(): void
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'status' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Brand One',
            'slug' => 'brand-one',
            'status' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Deal Product',
            'slug' => 'deal-product',
            'short_description' => 'Short description',
            'regular_price' => 60,
            'sale_price' => 50,
            'status' => true,
        ]);

        $hotDeal = HotDeal::create([
            'product_id' => $product->id,
            'deal_price' => 45,
            'discount_percentage' => 25,
            'title' => 'Visible Deal',
            'subtitle' => 'Available soon',
            'start_time' => now()->addHour(),
            'end_time' => now()->addDay(),
            'status' => true,
        ]);

        $response = $this->getJson('/api/store/products');

        $response->assertOk()
            ->assertJsonPath('data.0.hot_deal.id', $hotDeal->id)
            ->assertJsonPath('data.0.deal_price', 45);
    }

    public function test_store_hot_deals_endpoint_returns_non_expired_deals_including_upcoming_ones(): void
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'status' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Brand One',
            'slug' => 'brand-one',
            'status' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'short_description' => 'Short description',
            'regular_price' => 50,
            'sale_price' => 40,
            'status' => true,
        ]);

        $activeDeal = HotDeal::create([
            'product_id' => $product->id,
            'deal_price' => 40,
            'discount_percentage' => 20,
            'title' => 'Active Deal',
            'subtitle' => 'Live now',
            'start_time' => now()->subHour(),
            'end_time' => now()->addDay(),
            'status' => true,
        ]);

        $upcomingDeal = HotDeal::create([
            'product_id' => $product->id,
            'deal_price' => 35,
            'discount_percentage' => 30,
            'title' => 'Upcoming Deal',
            'subtitle' => 'Starts soon',
            'start_time' => now()->addHour(),
            'end_time' => now()->addWeek(),
            'status' => true,
        ]);

        $response = $this->getJson('/api/store/hot-deals');

        $response->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment(['id' => $activeDeal->id])
            ->assertJsonFragment(['id' => $upcomingDeal->id]);
    }
}
