<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Stores;
use App\Models\ProductsPricesStores;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ProductsController extends Controller
{
    /**
     * Search and suggest products, optionally including the latest price for a specific store.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $search = $request->query('search');
            $storeIdInput = $request->query('store_id');

            // Find products matching the search query
            $products = !empty($search) 
                ? Products::getFirstSearch($search) 
                : Products::where(['deleted' => 0])->limit(50)->get();

            // Resolve store to get primary key ID (bigInteger) if store_id was provided
            $storeId = null;
            if (!empty($storeIdInput)) {
                $store = Stores::where('id', $storeIdInput)
                    ->orWhere('uuid', $storeIdInput)
                    ->first();
                if ($store) {
                    $storeId = $store->id;
                }
            }

            // If a valid store was resolved, fetch latest product prices for this store in a single query
            if ($storeId && $products->isNotEmpty()) {
                $productIds = $products->pluck('id')->toArray();
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

                foreach ($products as $product) {
                    // Set latest_price dynamically on the model so ProductResource can output it
                    $product->latest_price = $latestPrices[$product->id] ?? null;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Productos obtenidos exitosamente',
                'data' => ProductResource::collection($products)
            ]);
        } catch (\Throwable $th) {
            Log::error('Error searching products: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo obtener Productos -[P-001]',
                'errors' => [
                    'error' => 'Internal Server Error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }
}
