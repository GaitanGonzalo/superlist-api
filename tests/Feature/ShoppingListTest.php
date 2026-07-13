<?php

namespace Tests\Feature;

use App\Models\Products;
use App\Models\ProductsPricesStores;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\Stores;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShoppingListTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function getAuthHeaderForUser(User $user): array
    {
        $token = JWTAuth::fromUser($user);
        return ['Authorization' => 'Bearer ' . $token];
    }

    /**
     * Test list creation along with its items and price logging.
     */
    public function test_user_can_create_shopping_list_with_items(): void
    {
        $user = User::first();
        $store = Stores::create([
            'name' => 'Supermercado Test',
            'uuid' => (string) Str::uuid(),
            'address' => 'Av. de Prueba 123',
            'is_subsidiary' => false,
        ]);

        $product1 = Products::create([
            'name' => 'Gaseosa Coca-Cola 1.5L',
            'ean_code' => '7790070411234',
            'user_creator_id' => $user->id,
        ]);

        $product2 = Products::create([
            'name' => 'Fideos Tallarines 500g',
            'ean_code' => '7790070511234',
            'user_creator_id' => $user->id,
        ]);

        $data = [
            'uuid' => (string) Str::uuid(),
            'store_id' => $store->uuid, // Passing UUID
            'store_name' => $store->name,
            'store_address' => $store->address,
            'is_finished' => false,
            'total_spent' => 1500.50,
            'actual_total' => 1500.50,
            'product_list' => [
                [
                    'uuid' => (string) Str::uuid(),
                    'product_id' => $product1->id,
                    'name' => $product1->name,
                    'quantity' => 2,
                    'price' => 500.00,
                    'is_purchased' => true,
                ],
                [
                    'uuid' => (string) Str::uuid(),
                    'product_id' => $product2->id,
                    'name' => $product2->name,
                    'quantity' => 1,
                    'price' => 500.50,
                    'is_purchased' => false,
                ]
            ]
        ];

        $response = $this->postJson(
            route('api.lists.store'),
            $data,
            $this->getAuthHeaderForUser($user)
        );

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.store_name', 'Supermercado Test');

        // Check shopping list was saved to database
        $this->assertDatabaseHas('shopping_lists', [
            'user_id' => $user->id,
            'store_name' => 'Supermercado Test',
        ]);

        $list = ShoppingList::where('user_id', $user->id)->first();

        // Check items were saved to database
        $this->assertDatabaseHas('shopping_list_items', [
            'shopping_list_id' => $list->id,
            'product_id' => $product1->id,
            'price' => 500.00,
        ]);

        // Check prices were logged in products_prices_stores
        $this->assertDatabaseHas('products_prices_stores', [
            'user_id' => $user->id,
            'store_id' => $store->id,
            'product_id' => $product1->id,
            'price' => 500.00,
        ]);
    }

    /**
     * Test creating a shopping list without selecting a store or totals.
     */
    public function test_user_can_create_shopping_list_without_store_and_totals(): void
    {
        $user = User::first();

        $data = [
            'uuid' => (string) Str::uuid(),
            'store_id' => null,
            'store_name' => null,
            'store_address' => null,
            'is_finished' => false,
        ];

        $response = $this->postJson(
            route('api.lists.store'),
            $data,
            $this->getAuthHeaderForUser($user)
        );

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.store_name', 'Sin comercio definido');
        $response->assertJsonPath('data.total_spent', 0);
        $response->assertJsonPath('data.actual_total', 0);
        $response->assertJsonPath('data.user_id', $user->id);

        // Check shopping list was saved to database
        $this->assertDatabaseHas('shopping_lists', [
            'user_id' => $user->id,
            'store_name' => 'Sin comercio definido',
            'store_id' => null,
            'total_spent' => 0.00,
            'actual_total' => 0.00,
        ]);
    }

    /**
     * Test index authorization (a user can only see their own lists).
     */
    public function test_user_cannot_view_other_users_shopping_lists(): void
    {
        $user1 = User::first();

        // Create second user
        $user2 = User::create([
            'name' => 'User Two',
            'last_name' => 'Test',
            'email' => 'user.two@test.com',
            'password' => bcrypt('Prueba123'),
            'role_id' => 1,
        ]);

        // Create list for user 2
        $listUser2 = ShoppingList::create([
            'user_id' => $user2->id,
            'store_name' => 'Comercio User 2',
            'total_spent' => 100,
            'actual_total' => 100,
            'deleted' => 0,
        ]);

        // Query user 1 lists
        $response = $this->getJson(
            route('api.lists.index'),
            $this->getAuthHeaderForUser($user1)
        );

        $response->assertStatus(200);

        // Response should not contain User 2's list
        $data = $response->json('data');
        $this->assertCount(0, $data);

        // Attempting to show user 2 list directly from user 1 should return 404
        $showResponse = $this->getJson(
            route('api.lists.show', ['list_id' => $listUser2->id]),
            $this->getAuthHeaderForUser($user1)
        );

        $showResponse->assertStatus(404);
    }

    /**
     * Test list update including item additions, edits, and deletions.
     */
    public function test_user_can_update_shopping_list_items(): void
    {
        $user = User::first();
        $store = Stores::create([
            'name' => 'Supermercado Test',
            'uuid' => (string) Str::uuid(),
            'is_subsidiary' => false,
        ]);

        $product1 = Products::create(['name' => 'P1', 'ean_code' => '111', 'user_creator_id' => $user->id]);
        $product2 = Products::create(['name' => 'P2', 'ean_code' => '222', 'user_creator_id' => $user->id]);
        $product3 = Products::create(['name' => 'P3', 'ean_code' => '333', 'user_creator_id' => $user->id]);
        // Create initial shopping list with P1 and P2
        $list = ShoppingList::create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'store_name' => $store->name,
            'total_spent' => 200,
            'actual_total' => 200,
            'deleted' => 0,
        ]);

        $item1Uuid = (string) Str::uuid();
        $item2Uuid = (string) Str::uuid();

        $item1 = ShoppingListItem::create([
            'shopping_list_id' => $list->id,
            'uuid' => $item1Uuid,
            'product_id' => $product1->id,
            'name' => $product1->name,
            'quantity' => 1,
            'price' => 100.00,
            'is_purchased' => 0,
            'deleted' => 0,
        ]);

        $item2 = ShoppingListItem::create([
            'shopping_list_id' => $list->id,
            'uuid' => $item2Uuid,
            'product_id' => $product2->id,
            'name' => $product2->name,
            'quantity' => 1,
            'price' => 100.00,
            'is_purchased' => 0,
            'deleted' => 0,
        ]);

        // We update the list:
        // - item1 (P1): quantity updated, price updated
        // - item2 (P2): omitted from request (should be soft-deleted)
        // - item3 (P3): new item added
        $item3Uuid = (string) Str::uuid();

        $updateData = [
            'store_id' => $store->uuid,
            'store_name' => $store->name,
            'total_spent' => 450,
            'actual_total' => 450,
            'product_list' => [
                [
                    'uuid' => $item1Uuid,
                    'product_id' => $product1->id,
                    'name' => $product1->name,
                    'quantity' => 3, // updated
                    'price' => 150.00, // updated
                    'is_purchased' => true,
                ],
                [
                    'uuid' => $item3Uuid, // new
                    'product_id' => $product3->id,
                    'name' => $product3->name,
                    'quantity' => 1,
                    'price' => 300.00,
                    'is_purchased' => false,
                ]
            ]
        ];

        $response = $this->putJson(
            route('api.lists.update', ['list_id' => $list->id]),
            $updateData,
            $this->getAuthHeaderForUser($user)
        );

        $response->assertStatus(200);

        // Verify changes in DB
        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $item1->id,
            'quantity' => 3,
            'price' => 150.00,
            'deleted' => 0,
        ]);

        // Verify soft delete of item 2
        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $item2->id,
            'deleted' => 1,
        ]);

        // Verify creation of item 3
        $this->assertDatabaseHas('shopping_list_items', [
            'uuid' => $item3Uuid,
            'product_id' => $product3->id,
            'price' => 300.00,
            'deleted' => 0,
        ]);
    }

    /**
     * Test price logging performance condition: prices are ONLY logged if they are new or different.
     */
    public function test_price_is_only_logged_if_different_from_last_recorded(): void
    {
        $user = User::first();
        $store = Stores::create(['name' => 'Store S', 'uuid' => (string) Str::uuid(), 'is_subsidiary' => false]);
        $product = Products::create(['name' => 'Prod P', 'ean_code' => '999', 'user_creator_id' => $user->id]);

        // Log first price
        ProductsPricesStores::create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'product_id' => $product->id,
            'price' => 100.00,
        ]);

        $this->assertDatabaseCount('products_prices_stores', 1);

        // Call store method on list with the SAME price (100.00). It should NOT insert a new row.
        $data = [
            'uuid' => (string) Str::uuid(),
            'store_id' => $store->uuid,
            'store_name' => $store->name,
            'total_spent' => 100.00,
            'actual_total' => 100.00,
            'product_list' => [
                [
                    'uuid' => (string) Str::uuid(),
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'quantity' => 1,
                    'price' => 100.00, // same price
                    'is_purchased' => true,
                ]
            ]
        ];

        $response = $this->postJson(
            route('api.lists.store'),
            $data,
            $this->getAuthHeaderForUser($user)
        );

        $response->assertStatus(201);
        $this->assertDatabaseCount('products_prices_stores', 1); // still 1!

        // Now, update list/create with DIFFERENT price (120.00). It SHOULD log a new price.
        $data['product_list'][0]['price'] = 120.00;
        $response2 = $this->postJson(
            route('api.lists.store'),
            $data,
            $this->getAuthHeaderForUser($user)
        );

        $response2->assertStatus(201);
        $this->assertDatabaseCount('products_prices_stores', 2); // now 2!
    }

    /**
     * Test that product search suggestions return the latest store price.
     */
    public function test_latest_price_is_loaded_on_product_search_suggestions(): void
    {
        $user = User::first();
        $store = Stores::create(['name' => 'Store S', 'uuid' => (string) Str::uuid(), 'is_subsidiary' => false]);
        $product = Products::create(['name' => 'UniqueSpecialCoca', 'ean_code' => '555', 'user_creator_id' => $user->id]);

        // Record a price history for the product at the store
        ProductsPricesStores::create(['user_id' => $user->id, 'store_id' => $store->id, 'product_id' => $product->id, 'price' => 200.00, 'created_at' => now()->subDay()]);
        ProductsPricesStores::create(['user_id' => $user->id, 'store_id' => $store->id, 'product_id' => $product->id, 'price' => 250.00, 'created_at' => now()]);

        // Query Products search with store_id
        $response = $this->getJson(
            route('api.products.search') . "?search=UniqueSpecialCoca&store_id=" . $store->uuid,
            $this->getAuthHeaderForUser($user)
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.latest_price', 250); // returns the latest price!

        // Query Store Products search
        $storeProductsResponse = $this->getJson(
            route('api.stores.search_products', ['store_id' => $store->uuid]) . "?search=UniqueSpecialCoca",
            $this->getAuthHeaderForUser($user)
        );

        $storeProductsResponse->assertStatus(200);
        $storeProductsResponse->assertJsonPath('data.0.latest_price', 250); // returns the latest price!
    }

    /**
     * Test adding a single item to a shopping list and checking if price is logged.
     */
    public function test_user_can_add_item_to_shopping_list(): void
    {
        $user = User::first();
        $store = Stores::create(['name' => 'Store Item Test', 'uuid' => (string) Str::uuid(), 'is_subsidiary' => false]);
        $list = ShoppingList::create(['user_id' => $user->id, 'store_id' => $store->id, 'store_name' => $store->name, 'total_spent' => 0, 'actual_total' => 0, 'deleted' => 0]);
        $product = Products::create(['name' => 'Single Item P', 'ean_code' => '779', 'user_creator_id' => $user->id]);

        $itemData = [
            'uuid' => (string) Str::uuid(),
            'product_id' => $product->id,
            'name' => 'Single Item P Name',
            'quantity' => 5,
            'price' => 125.50,
            'is_purchased' => false,
        ];

        $response = $this->postJson(
            route('api.list.item.store', ['list_id' => $list->id]),
            $itemData,
            $this->getAuthHeaderForUser($user)
        );

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.price', 125.5);

        // Check it was added to shopping_list_items
        $this->assertDatabaseHas('shopping_list_items', [
            'shopping_list_id' => $list->id,
            'product_id' => $product->id,
            'price' => 125.50,
            'deleted' => 0,
        ]);

        // Check price was logged in products_prices_stores
        $this->assertDatabaseHas('products_prices_stores', [
            'store_id' => $store->id,
            'product_id' => $product->id,
            'price' => 125.50,
        ]);
    }

    /**
     * Test updating a single item in a shopping list.
     */
    public function test_user_can_update_single_shopping_list_item(): void
    {
        $user = User::first();
        $store = Stores::create(['name' => 'Store Item Test', 'uuid' => (string) Str::uuid(), 'is_subsidiary' => false]);
        $list = ShoppingList::create(['user_id' => $user->id, 'store_id' => $store->id, 'store_name' => $store->name, 'total_spent' => 100, 'actual_total' => 100, 'deleted' => 0]);
        $product = Products::create(['name' => 'Single Item P', 'ean_code' => '779', 'user_creator_id' => $user->id]);

        $item = ShoppingListItem::create([
            'shopping_list_id' => $list->id,
            'uuid' => (string) Str::uuid(),
            'product_id' => $product->id,
            'name' => 'Initial Name',
            'quantity' => 1,
            'price' => 100.00,
            'is_purchased' => 0,
            'deleted' => 0,
        ]);

        $updateData = [
            'uuid' => $item->uuid,
            'product_id' => $product->id,
            'name' => 'Updated Name',
            'quantity' => 2,
            'price' => 150.00, // updated price
            'is_purchased' => true,
        ];

        $response = $this->patchJson(
            route('api.list.item.update', ['list_id' => $list->id, 'item_id' => $item->id]),
            $updateData,
            $this->getAuthHeaderForUser($user)
        );

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.name', 'Updated Name');

        // Check update in database
        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $item->id,
            'name' => 'Updated Name',
            'price' => 150.00,
            'quantity' => 2,
            'is_purchased' => 1,
        ]);

        // Check updated price was logged in products_prices_stores
        $this->assertDatabaseHas('products_prices_stores', [
            'store_id' => $store->id,
            'product_id' => $product->id,
            'price' => 150.00,
        ]);
    }

    /**
     * Test soft deleting a single item.
     */
    public function test_user_can_delete_single_shopping_list_item(): void
    {
        $user = User::first();
        $list = ShoppingList::create(['user_id' => $user->id, 'store_name' => 'Store Test', 'total_spent' => 100, 'actual_total' => 100, 'deleted' => 0]);
        $product = Products::create(['name' => 'Single Item P', 'ean_code' => '779', 'user_creator_id' => $user->id]);

        $item = ShoppingListItem::create([
            'shopping_list_id' => $list->id,
            'uuid' => (string) Str::uuid(),
            'product_id' => $product->id,
            'name' => 'Item to Delete',
            'quantity' => 1,
            'price' => 100.00,
            'is_purchased' => 0,
            'deleted' => 0,
        ]);

        $response = $this->deleteJson(
            route('api.list.item.destroy', ['list_id' => $list->id, 'item_id' => $item->id]),
            [],
            $this->getAuthHeaderForUser($user)
        );

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        // Check soft deleted state
        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $item->id,
            'deleted' => 1,
        ]);
    }
}
