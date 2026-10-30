<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\PromotionalBanner;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShopCatalogTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('promotional_banners');
        Schema::dropIfExists('products');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('role')->default('customer');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->default('General');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image_url')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->string('stock_status')->default('In Stock');
            $table->boolean('is_active')->default(true);
            $table->json('attributes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('promotional_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('body')->nullable();
            $table->string('link_url', 2048)->nullable();
            $table->string('image_path')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('background_color')->default('#310669');
            $table->string('text_color')->default('#FFFFFF');
            $table->string('alignment')->default('left');
            $table->string('layout')->default('split');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function test_shop_displays_only_active_products_and_detail_page_is_public(): void
    {
        $product = Product::create([
            'name' => 'Ube Loaf',
            'slug' => 'ube-loaf',
            'category' => 'Loaves',
            'description' => 'Freshly baked.',
            'price' => 180,
            'stock_quantity' => 3,
            'stock_status' => 'In Stock',
            'is_active' => true,
            'attributes' => ['features' => ['Ube filling']],
        ]);
        Product::create([
            'name' => 'Hidden Cake',
            'slug' => 'hidden-cake',
            'category' => 'Cakes',
            'price' => 450,
            'stock_quantity' => 0,
            'stock_status' => 'Out of Stock',
            'is_active' => false,
        ]);

        $this->get(route('shop'))
            ->assertOk()
            ->assertSee('Ube Loaf')
            ->assertDontSee('Hidden Cake')
            ->assertSee('globalLoader')
            ->assertSee('footer-inner')
            ->assertSee('catalog-category-list');
        $this->get(route('shop.product', $product->slug))
            ->assertOk()
            ->assertSee('Ube filling')
            ->assertSee('Continue to order request')
            ->assertSee('globalLoader')
            ->assertSee('footer-inner');
    }

    public function test_product_lookup_is_public_and_returns_only_active_public_fields(): void
    {
        Product::create([
            'name' => 'Ube Loaf',
            'slug' => 'ube-loaf',
            'category' => 'Loaves',
            'description' => 'Freshly baked.',
            'price' => 180,
            'stock_quantity' => 3,
            'stock_status' => 'In Stock',
            'is_active' => true,
        ]);

        $this->getJson(route('shop.products.lookup', ['q' => 'ube']))
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'ube-loaf')
            ->assertJsonPath('data.0.available', true)
            ->assertJsonMissingPath('data.0.stock_quantity');
    }

    public function test_product_detail_can_continue_into_the_existing_order_request_flow(): void
    {
        $product = Product::create([
            'name' => 'Mango Cake',
            'slug' => 'mango-cake',
            'category' => 'Cakes',
            'price' => 650,
            'stock_quantity' => 4,
            'stock_status' => 'In Stock',
            'is_active' => true,
        ]);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('orders.create', ['product' => $product->slug, 'quantity' => 2]))
            ->assertOk()
            ->assertSee('Catalog selection: Mango Cake × 2')
            ->assertSee('Catalog product: Mango Cake (quantity 2)');
    }

    public function test_only_portal_superusers_can_open_store_management(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer)->get(route('admin.store.index'))->assertForbidden();

        $developer = User::factory()->create(['role' => 'developer']);
        $this->actingAs($developer)->get(route('admin.store.index'))->assertOk();
        $this->get(route('admin.store.products.create'))->assertOk()->assertSee('Add product');
        $this->get(route('admin.store.banners.create'))->assertOk()->assertSee('Create hero banner');
    }

    public function test_admin_can_create_product_with_publicly_stored_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.store.products.store'), [
            'name' => 'Mango Cake',
            'category' => 'Cakes',
            'description' => 'Celebration cake',
            'price' => '650.00',
            'stock_quantity' => 4,
            'stock_status' => 'In Stock',
            'is_active' => 1,
            'features' => "Fresh mango\nWhipped cream",
            'image' => UploadedFile::fake()->create('cake.jpg', 100)->mimeType('image/jpeg'),
        ])->assertRedirect(route('admin.store.index'));

        $product = Product::where('name', 'Mango Cake')->firstOrFail();
        $this->assertSame(['Fresh mango', 'Whipped cream'], $product->attributes['features']);
        Storage::disk('public')->assertExists($product->image_url);
    }

    public function test_admin_can_update_and_remove_a_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::create([
            'name' => 'Original Cake',
            'slug' => 'original-cake',
            'category' => 'Cakes',
            'price' => 180,
            'stock_quantity' => 5,
            'stock_status' => 'In Stock',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.store.products.update', $product), [
            'name' => 'Updated Cake',
            'category' => 'Celebration Cakes',
            'description' => 'Updated details',
            'price' => '225.00',
            'stock_quantity' => 2,
            'stock_status' => 'Low Stock',
            'is_active' => 1,
            'features' => "Freshly baked\nMade to order",
        ])->assertRedirect(route('admin.store.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Cake',
            'price' => '225.00',
            'stock_quantity' => 2,
        ]);

        $this->delete(route('admin.store.products.destroy', $product))
            ->assertRedirect();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_banner_editor_rejects_non_https_external_links_and_accepts_site_paths(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $payload = [
            'title' => 'Weekend treats',
            'body' => 'A sweet offer',
            'link_url' => 'javascript:alert(1)',
            'cta_label' => 'Shop',
            'background_color' => '#310669',
            'text_color' => '#FFFFFF',
            'alignment' => 'center',
            'layout' => 'overlay',
            'is_active' => 1,
            'sort_order' => 0,
            'image' => UploadedFile::fake()->create('banner.jpg', 100)->mimeType('image/jpeg'),
        ];

        $this->actingAs($admin)->post(route('admin.store.banners.store'), $payload)
            ->assertSessionHasErrors('link_url');

        $payload['link_url'] = '/\\evil.example';
        $this->post(route('admin.store.banners.store'), $payload)
            ->assertSessionHasErrors('link_url');

        $payload['link_url'] = '/shop';
        $this->post(route('admin.store.banners.store'), $payload)
            ->assertRedirect(route('admin.store.index'));

        $this->assertDatabaseHas('promotional_banners', ['title' => 'Weekend treats', 'link_url' => '/shop']);
    }

    public function test_admin_can_edit_and_remove_a_banner(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $banner = PromotionalBanner::create([
            'title' => 'Old banner',
            'image_path' => UploadedFile::fake()->create('old.png', 100, 'image/png')->store('banners', 'public'),
            'background_color' => '#310669',
            'text_color' => '#FFFFFF',
            'alignment' => 'left',
            'layout' => 'split',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->actingAs($admin)->put(route('admin.store.banners.update', $banner), [
            'title' => 'Updated promotion',
            'body' => 'New offer',
            'link_url' => '/shop',
            'cta_label' => 'Browse',
            'background_color' => '#8A38F5',
            'text_color' => '#FFFFFF',
            'alignment' => 'center',
            'layout' => 'overlay',
            'is_active' => 1,
            'sort_order' => 3,
        ])->assertRedirect(route('admin.store.index'));

        $this->assertDatabaseHas('promotional_banners', [
            'id' => $banner->id,
            'title' => 'Updated promotion',
            'alignment' => 'center',
            'layout' => 'overlay',
            'sort_order' => 3,
        ]);

        $this->delete(route('admin.store.banners.destroy', $banner))
            ->assertRedirect();
        $this->assertDatabaseMissing('promotional_banners', ['id' => $banner->id]);
    }
}
