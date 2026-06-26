<?php

namespace App\Actions\ShoppingListItem;

use App\Models\ShoppingListItem;

class DeleteListItemAction
{
    /**
     * Soft delete a shopping list item.
     *
     * @param ShoppingListItem $item
     * @return void
     */
    public function execute(ShoppingListItem $item): void
    {
        $item->update(['deleted' => 1]);
    }
}
