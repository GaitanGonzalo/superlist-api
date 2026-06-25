<?php

namespace App\Actions\Price;

use App\Models\ProductsPricesStores;
use App\Models\Stores;
use Illuminate\Support\Facades\Log;

class RegisterPriceAction
{
    /**
     * Register product prices at a store if they are different from the latest recorded prices.
     *
     * @param int $userId
     * @param string|int|null $storeIdInput
     * @param array $productList
     * @return void
     */
    public function execute(int $userId, $storeIdInput, array $productList): void
    {
        if (empty($storeIdInput)) {
            return;
        }

        // Resolve store to get primary key ID (bigInteger)
        $store = Stores::where('id', $storeIdInput)
            ->orWhere('uuid', $storeIdInput)
            ->first();

        if (!$store) {
            Log::warning("RegisterPriceAction: Store not found for input '{$storeIdInput}'. Price logging skipped.");
            return;
        }

        $storeId = $store->id;

        // Filter items that have product_id and a valid price (> 0)
        $itemsWithPrices = array_filter($productList, function ($item) {
            return !empty($item['product_id']) && isset($item['price']) && $item['price'] > 0;
        });

        if (empty($itemsWithPrices)) {
            return;
        }

        $productIds = array_unique(array_column($itemsWithPrices, 'product_id'));

        // Query the latest price for each product at this store in a single query
        $latestPrices = ProductsPricesStores::where('store_id', $storeId)
            ->whereIn('product_id', $productIds)
            ->whereIn('id', function ($query) use ($storeId) {
                $query->selectRaw('MAX(id)')
                    ->from('products_prices_stores')
                    ->where('store_id', $storeId)
                    ->groupBy('product_id');
            })
            ->pluck('price', 'product_id')
            ->toArray();

        $recordsToInsert = [];
        $now = now();

        foreach ($itemsWithPrices as $item) {
            $productId = $item['product_id'];
            $price = (float)$item['price'];

            // If price doesn't exist in DB, or it exists but is different
            if (!array_key_exists($productId, $latestPrices) || (float)$latestPrices[$productId] !== $price) {
                $recordsToInsert[] = [
                    'user_id' => $userId,
                    'store_id' => $storeId,
                    'product_id' => $productId,
                    'price' => $price,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert in bulk to maximize performance!
        if (!empty($recordsToInsert)) {
            ProductsPricesStores::insert($recordsToInsert);
        }
    }
}
