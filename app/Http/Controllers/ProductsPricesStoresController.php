<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Stores;
use App\Models\ProductsPricesStores;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ProductsPricesStoresController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Ingresa al metodo index'
        ]);
    }

    /**
     * Search products and append their latest price at a specific store.
     *
     * @param int|string $storeIdInput
     * @param Request $request
     * @return JsonResponse
     */
    public function searchProducts($storeIdInput, Request $request): JsonResponse
    {
        try {
            $search = $request->query('search');

            // Find products matching the search query
            $products = !empty($search) 
                ? Products::getFirstSearch($search) 
                : Products::where(['deleted' => 0])->limit(50)->get();

            // Resolve store to get primary key ID (bigInteger)
            $store = Stores::where('id', $storeIdInput)
                ->orWhere('uuid', $storeIdInput)
                ->first();

            $storeId = $store ? $store->id : null;

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
                    $product->latest_price = $latestPrices[$product->id] ?? null;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Productos del comercio obtenidos exitosamente',
                'data' => ProductResource::collection($products)
            ]);
        } catch (\Throwable $th) {
            Log::error('Error searching store products: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudieron obtener los productos del comercio',
                'errors' => [
                    'error' => 'Internal Server Error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }
}
