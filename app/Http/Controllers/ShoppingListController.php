<?php

namespace App\Http\Controllers;

use App\Actions\ShoppingList\CreateShoppingListAction;
use App\Actions\ShoppingList\UpdateShoppingListAction;
use App\Http\Requests\StoreListRequest;
use App\Http\Resources\ShoppingListResource;
use App\Models\ShoppingList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShoppingListController extends Controller
{
    /**
     * Display a listing of the user's shopping lists.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            $lists = ShoppingList::where('user_id', $userId)
                ->where('deleted', 0)
                ->with(['items' => function ($query) {
                    $query->where('deleted', 0);
                }])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Listas obtenidas exitosamente',
                'data' => ShoppingListResource::collection($lists)->response()->getData(true)
            ]);
        } catch (\Throwable $th) {
            Log::error('Error fetching shopping lists: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudieron obtener las listas',
                'errors' => [
                    'error' => 'Internal Server Error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }

    /**
     * Store a newly created shopping list in storage.
     *
     * @param StoreListRequest $request
     * @param CreateShoppingListAction $action
     * @return JsonResponse
     */
    public function store(StoreListRequest $request, CreateShoppingListAction $action): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $shoppingList = $action->execute($userId, $request->validated());

            // Load items relationship to return it in resource
            $shoppingList->load(['items' => function ($query) {
                $query->where('deleted', 0);
            }]);

            return response()->json([
                'success' => true,
                'message' => 'Lista de compras creada exitosamente',
                'data' => ShoppingListResource::make($shoppingList)
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error creating shopping list: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo crear la lista de compras',
                'errors' => [
                    'error' => 'Internal Server Error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }

    /**
     * Display the specified shopping list.
     *
     * @param int|string $listId
     * @return JsonResponse
     */
    public function show($listId, Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            $shoppingList = ShoppingList::where('user_id', $userId)
                ->where('deleted', 0)
                ->with(['items' => function ($query) {
                    $query->where('deleted', 0);
                }])
                ->where(function ($query) use ($listId) {
                    $query->where('id', $listId)
                        ->orWhere('uuid', $listId);
                })
                ->first();

            if (!$shoppingList) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lista de compras no encontrada o no autorizada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detalle de la lista obtenido exitosamente',
                'data' => ShoppingListResource::make($shoppingList)
            ]);
        } catch (\Throwable $th) {
            Log::error('Error showing shopping list: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo obtener el detalle de la lista',
                'errors' => [
                    'error' => 'Internal Server Error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }

    /**
     * Update the specified shopping list in storage.
     *
     * @param StoreListRequest $request
     * @param int|string $listId
     * @param UpdateShoppingListAction $action
     * @return JsonResponse
     */
    public function update(StoreListRequest $request, $listId, UpdateShoppingListAction $action): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            $shoppingList = ShoppingList::where('user_id', $userId)
                ->where('deleted', 0)
                ->where(function ($query) use ($listId) {
                    $query->where('id', $listId)
                        ->orWhere('uuid', $listId);
                })
                ->first();

            if (!$shoppingList) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lista de compras no encontrada o no autorizada'
                ], 404);
            }

            $updatedList = $action->execute($userId, $shoppingList, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Lista de compras actualizada exitosamente',
                'data' => ShoppingListResource::make($updatedList)
            ]);
        } catch (\Throwable $th) {
            Log::error('Error updating shopping list: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar la lista de compras',
                'errors' => [
                    'error' => 'Internal Server Error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }
}
