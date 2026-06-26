<?php

namespace App\Actions\ShoppingListItem;

use App\Actions\Price\RegisterPriceAction;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Support\Facades\DB;

class UpdateListItemAction
{
    protected $registerPriceAction;

    public function __construct(RegisterPriceAction $registerPriceAction)
    {
        $this->registerPriceAction = $registerPriceAction;
    }

    /**
     * Update a shopping list item and register its price if applicable.
     *
     * @param int $userId
     * @param ShoppingList $shoppingList
     * @param ShoppingListItem $item
     * @param array $data
     * @return ShoppingListItem
     */
    public function execute(int $userId, ShoppingList $shoppingList, ShoppingListItem $item, array $data): ShoppingListItem
    {
        return DB::transaction(function () use ($userId, $shoppingList, $item, $data) {
            $item->update([
                'product_id' => $data['product_id'] ?? $item->product_id,
                'name' => $data['name'] ?? $item->name,
                'quantity' => $data['quantity'] ?? $item->quantity,
                'price' => $data['price'] ?? $item->price,
                'is_purchased' => isset($data['is_purchased']) ? ($data['is_purchased'] ? 1 : 0) : $item->is_purchased,
                'deleted' => 0, // Reactivate if updated
            ]);

            // Register price if list has store_id defined
            if (!empty($shoppingList->store_id)) {
                $this->registerPriceAction->execute($userId, $shoppingList->store_id, [$data]);
            }

            return $item;
        });
    }
}
