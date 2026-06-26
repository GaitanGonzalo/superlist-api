<?php

namespace App\Actions\ShoppingListItem;

use App\Actions\Price\RegisterPriceAction;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Support\Facades\DB;

class CreateListItemAction
{
    protected $registerPriceAction;

    public function __construct(RegisterPriceAction $registerPriceAction)
    {
        $this->registerPriceAction = $registerPriceAction;
    }

    /**
     * Create a shopping list item and register its price if applicable.
     *
     * @param int $userId
     * @param ShoppingList $shoppingList
     * @param array $data
     * @return ShoppingListItem
     */
    public function execute(int $userId, ShoppingList $shoppingList, array $data): ShoppingListItem
    {
        return DB::transaction(function () use ($userId, $shoppingList, $data) {
            $item = ShoppingListItem::create([
                'shopping_list_id' => $shoppingList->id,
                'uuid' => $data['uuid'],
                'product_id' => $data['product_id'],
                'name' => $data['name'],
                'quantity' => $data['quantity'],
                'price' => $data['price'],
                'is_purchased' => $data['is_purchased'] ? 1 : 0,
                'deleted' => 0,
            ]);

            // Register price if list has store_id defined
            if (!empty($shoppingList->store_id)) {
                $this->registerPriceAction->execute($userId, $shoppingList->store_id, [$data]);
            }

            return $item;
        });
    }
}
