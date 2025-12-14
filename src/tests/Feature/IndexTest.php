<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Sold_item;
use App\Models\Category;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 購入済み商品は「Sold」と表示される
     */
    public function purchased_products_are_marked_as_sold(): void
    {
        $user = User::factory()->create();
        $product1 = Item::factory()->create([
            'item' => 'Sold Product Name', // 商品名
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'image' => 'test-image-1.jpg',
            'status' => '良好',
            'detail' => '詳細1',
            'money' => 1000,
        ]);
        $product2 = Item::factory()->create([
            'item' => 'Available Product Name', // 商品名
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'image' => 'test-image-2.jpg',
            'status' => '状態が悪い',
            'detail' => '詳細2',
            'money' => 2000,
        ]);

        // product1 を購入済みにする
        Sold_item::factory()->create([
            'user_id' => $user->id,
            'item_id' => $product1->id,
            'method' => 'クレジットカード支払い',
        ]);

        $response = $this->actingAs($user)->get('/'); // 商品一覧ページにアクセス

        $response->assertStatus(200);

        // 「Sold Product Name」という名前が表示されていることを確認
        $response->assertSee('Sold Product Name');
        
        // 「Available Product Name」という名前が表示されていることを確認
        $response->assertSee('Available Product Name');

        // 購入済み商品に「Sold」が表示されることを確認
        $response->assertSee('Sold');

        // 未購入商品には「Sold」というテキストがないことを確認
        $response->assertDontSee('Available Product Name Sold');
    }
}
