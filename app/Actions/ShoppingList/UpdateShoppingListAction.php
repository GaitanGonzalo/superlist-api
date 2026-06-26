<?php

namespace App\Actions\ShoppingList;

use App\Actions\Price\RegisterPriceAction;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Support\Facades\DB;

class UpdateShoppingListAction
{
    protected $registerPriceAction;

    public function __construct(RegisterPriceAction $registerPriceAction)
    {
        $this->registerPriceAction = $registerPriceAction;
    }

    /**
     * Update an existing shopping list and sync its items.
     *
     * @param int $userId
     * @param ShoppingList $shoppingList
     * @param array $data
     * @return ShoppingList
     */
    public function execute(int $userId, ShoppingList $shoppingList, array $data): ShoppingList
    {
        return DB::transaction(function () use ($userId, $shoppingList, $data) {
            // Update Shopping List Details
            $shoppingList->update([
                'store_id' => array_key_exists('store_id', $data) ? $data['store_id'] : $shoppingList->store_id,
                'store_name' => array_key_exists('store_name', $data) 
                    ? (!empty($data['store_name']) ? $data['store_name'] : 'Sin comercio definido') 
                    : $shoppingList->store_name,
                'store_address' => array_key_exists('store_address', $data) ? $data['store_address'] : $shoppingList->store_address,
                'is_finished' => isset($data['is_finished']) ? ($data['is_finished'] ? 1 : 0) : $shoppingList->is_finished,
                'finished_at' => $data['finished_at'] ?? $shoppingList->finished_at,
                'total_spent' => $data['total_spent'] ?? $shoppingList->total_spent,
                'actual_total' => $data['actual_total'] ?? $shoppingList->actual_total,
            ]);

            $productList = $data['product_list'] ?? [];

            // Get existing active items of this list
            $existingItems = ShoppingListItem::where('shopping_list_id', $shoppingList->id)
                ->where('deleted', 0)
                ->get()
                ->keyBy('uuid');

            $processedUuids = [];

            foreach ($productList as $itemData) {
                $uuid = $itemData['uuid'] ?? null;
                if (empty($uuid)) {
                    continue;
                }

                $processedUuids[] = $uuid;

                if ($existingItems->has($uuid)) {
                    // Update existing item
                    $item = $existingItems->get($uuid);
                    $item->update([
                        'product_id' => $itemData['product_id'],
                        'name' => $itemData['name'],
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'],
                        'is_purchased' => $itemData['is_purchased'] ? 1 : 0,
                        'deleted' => 0, // Ensure it's active
                    ]);
                } else {
                    // Create new item
                    ShoppingListItem::create([
                        'shopping_list_id' => $shoppingList->id,
                        'uuid' => $uuid,
                        'product_id' => $itemData['product_id'],
                        'name' => $itemData['name'],
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'],
                        'is_purchased' => $itemData['is_purchased'] ? 1 : 0,
                        'deleted' => 0,
                    ]);
                }
            }

            // Soft-delete items that were in DB but are not in the updated request
            foreach ($existingItems as $uuid => $item) {
                if (!in_array($uuid, $processedUuids)) {
                    $item->update(['deleted' => 1]);
                }
            }

            // Register prices if store is defined
            $storeId = $shoppingList->store_id;
            if (!empty($storeId) && !empty($productList)) {
                $this->registerPriceAction->execute($userId, $storeId, $productList);
            }

            // Reload items relation
            $shoppingList->load(['items' => function ($query) {
                $query->where('deleted', 0);
            }]);

            return $shoppingList;
        });
    }
}
