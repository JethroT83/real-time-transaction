<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class TokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Delete existing tokens for this device name if they exist
        $user->tokens()->where('name', $request->device_name)->delete();

        $token = $user->createToken($request->device_name);

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Token revoked successfully']);
    }

    public function destroyAll(Request $request): JsonResponse
    {
        // Revoke all tokens for the authenticated user
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'All tokens revoked successfully']);
    }

}
