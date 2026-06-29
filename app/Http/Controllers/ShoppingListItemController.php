<?php

namespace App\Http\Controllers;

use App\Actions\ShoppingListItem\CreateListItemAction;
use App\Actions\ShoppingListItem\UpdateListItemAction;
use App\Actions\ShoppingListItem\DeleteListItemAction;
use App\Http\Requests\StoreListItemRequest;
use App\Http\Resources\ShoppingListItemResource;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShoppingListItemController extends Controller
{
    /**
     * Store a newly created item in the shopping list.
     *
     * @param int|string $listId
     * @param StoreListItemRequest $request
     * @param CreateListItemAction $action
     * @return JsonResponse
     */
    public function store($listId, StoreListItemRequest $request, CreateListItemAction $action): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            // Find list and verify ownership
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

            $item = $action->execute($userId, $shoppingList, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Elemento agregado a la lista exitosamente',
                'data' => ShoppingListItemResource::make($item)
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Error creating shopping list item: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo agregar el elemento a la lista',
                'errors' => [
                    'error' => 'Internal Server Error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }

    /**
     * Update the specified item in the shopping list.
     *
     * @param int|string $listId
     * @param int|string $itemId
     * @param StoreListItemRequest $request
     * @param UpdateListItemAction $action
     * @return JsonResponse
     */
    public function update($listId, $itemId, StoreListItemRequest $request, UpdateListItemAction $action): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            // Find list and verify ownership
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

            // Find item and verify it belongs to the list
            $item = ShoppingListItem::where('shopping_list_id', $shoppingList->id)
                ->where('deleted', 0)
                ->where(function ($query) use ($itemId) {
                    $query->where('id', $itemId)
                        ->orWhere('uuid', $itemId);
                })
                ->first();

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Elemento de lista no encontrado o no pertenece a esta lista'
                ], 404);
            }

            $updatedItem = $action->execute($userId, $shoppingList, $item, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Elemento de lista actualizado exitosamente',
                'data' => ShoppingListItemResource::make($updatedItem)
            ]);
        } catch (\Throwable $th) {
            Log::error('Error updating shopping list item: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar el elemento de la lista',
                'errors' => [
                    'error' => 'Internal Server Error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }

    /**
     * Remove the specified item from the shopping list (soft-delete).
     *
     * @param int|string $listId
     * @param int|string $itemId
     * @param DeleteListItemAction $action
     * @return JsonResponse
     */
    public function destroy($listId, $itemId, DeleteListItemAction $action, Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            // Find list and verify ownership
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

            // Find item and verify it belongs to the list
            $item = ShoppingListItem::where('shopping_list_id', $shoppingList->id)
                ->where('deleted', 0)
                ->where(function ($query) use ($itemId) {
                    $query->where('id', $itemId)
                        ->orWhere('uuid', $itemId);
                })
                ->first();

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Elemento de lista no encontrado o no pertenece a esta lista'
                ], 404);
            }

            $action->execute($item);

            return response()->json([
                'success' => true,
                'message' => 'Elemento de lista eliminado exitosamente'
            ]);
        } catch (\Throwable $th) {
            Log::error('Error deleting shopping list item: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar el elemento de la lista',
                'errors' => [
                    'error' => 'Internal Server Error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }
}
