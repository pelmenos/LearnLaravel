<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;


class UserController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'first_name' => 'required|min:3',
            'last_name' => 'required',
            'password' => ['required', Password::min(3)->mixedCase()->numbers()],
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors(),
                'code' => 401
            ];
            return response()->json($response, 401);
        }

        $user = User::create($request->all());

        $response['token'] = $user->createToken('MyApp')->plainTextToken;
        $response['success'] = true;
        $response['message'] = 'Success';
        $response['code'] = 200;
        return response()->json($response, 200);
    }

    public function login(Request $request) {
        if (Auth::attempt($request->all())){
            $user = Auth::user();
            $response['token'] = $user->createToken('MyApp')->plainTextToken;
            $response['success'] = true;
            $response['message'] = 'Success';
            $response['code'] = 200;
            return response()->json($response, 200);
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
