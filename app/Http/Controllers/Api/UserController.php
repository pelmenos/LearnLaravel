<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;


class UserController extends Controller
{
    public function register(RegisterUserRequest $request) {
        $validated = $request->validated();
        $user = User::create($validated);
        return new UserResource($user);
    }

    public function login(Request $request) {
        if (Auth::attempt($request->all())){
            $user = Auth::user();
            return new UserResource($user);

        } else {
            $response = [
                'success' => false,
                'message' => 'Authorization failed',
                'code' => 401
            ];
            return response()->json($response, 401);
        }
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response(status: 204);
    }
}
