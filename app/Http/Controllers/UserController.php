<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function view(Request $request)
    {
        $user = $request->user()->load([
        'country:id,name',
        'state:id,name',
        'location:id,name'
    ]);
    
        return response()->json($user);
    }

    public function store(UserStoreRequest $request)
    {
        try {
            $user = $request->all();
            $user['password'] = bcrypt($user['password']);
            $newUser = User::create($user);

            return response()->json($newUser, 201);
        } catch (\Throwable $th) {
            Log::info('Create User', [
                'error' => $th->getMessage()
            ]);
            return response()->json([
                'message' => 'No se pudo crear el usuario -[U-001]',
                'errors' => [
                    'error' => 'Internal error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }

    public function update(UserUpdateRequest $request)
    {
        try {
            $data = $request->except(['role_id', 'deleted', 'id']);
            $user = $request->user();
            $user->update($data);

            return response()->json($user, 200);
        } catch (\Throwable $th) {
            Log::info('Update User', [
                'error' => $th->getMessage()
            ]);
            return response()->json([
                'message' => 'No se pudo actualizar el usuario -[U-002]',
                'errors' => [
                    'error' => 'Internal error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }

    public function updateAdmin(UserUpdateRequest $request)
    {
        try {
            $data = $request->all();
            $user = $request->user();
            $user->update($data);

            return response()->json($user, 200);
        } catch (\Throwable $th) {
            Log::info('Update User', [
                'error' => $th->getMessage()
            ]);
            return response()->json([
                'message' => 'No se pudo actualizar el usuario -[U-002]',
                'errors' => [
                    'error' => 'Internal error',
                    'statusCode' => 500
                ]
            ], 500);
        }
    }
}
