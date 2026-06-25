<?php

namespace App\Actions\ShoppingList;

use App\Actions\Price\RegisterPriceAction;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Support\Facades\DB;

class CreateShoppingListAction
{
    protected $registerPriceAction;

    public function __construct(RegisterPriceAction $registerPriceAction)
    {
        $this->registerPriceAction = $registerPriceAction;
    }

    /**
     * Create a new shopping list with its items.
     *
     * @param int $userId
     * @param array $data
     * @return ShoppingList
     */
    public function execute(int $userId, array $data): ShoppingList
    {
        return DB::transaction(function () use ($userId, $data) {
            // Create Shopping List
            $shoppingList = ShoppingList::create([
                'user_id' => $userId,
                'uuid' => $data['uuid'] ?? null,
                'store_id' => $data['store_id'] ?? null,
                'store_name' => $data['store_name'] ?? 'Sin comercio definido',
                'store_address' => $data['store_address'] ?? null,
                'is_finished' => isset($data['is_finished']) ? ($data['is_finished'] ? 1 : 0) : 0,
                'finished_at' => $data['finished_at'] ?? null,
                'total_spent' => $data['total_spent'] ?? 0.00,
                'actual_total' => $data['actual_total'] ?? 0.00,
                'deleted' => 0,
            ]);

            $productList = $data['product_list'] ?? [];

            if (!empty($productList)) {
                $now = now();
                $itemsToInsert = [];

                foreach ($productList as $item) {
                    $itemsToInsert[] = [
                        'shopping_list_id' => $shoppingList->id,
                        'uuid' => $item['uuid'],
                        'product_id' => $item['product_id'],
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'is_purchased' => $item['is_purchased'] ? 1 : 0,
                        'deleted' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Insert items using ShoppingListItem model insert for bulk timestamps
                ShoppingListItem::insert($itemsToInsert);

                // Register prices if store is defined
                if (!empty($shoppingList->store_id)) {
                    $this->registerPriceAction->execute($userId, $shoppingList->store_id, $productList);
                }
            }

            return $shoppingList;
        });
    }
}
