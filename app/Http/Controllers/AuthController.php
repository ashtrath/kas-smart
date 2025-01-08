<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $user = $request->user();
        $token = $user->createToken($user->username.'-'.now()->timestamp);

        return $this->sendResponse('Logged in successfully', [
            'user' => $user,
            'token' => $token->plainTextToken,
        ]);
    }

    public function destroy(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->sendResponse('Logged out successfully');
    }
}
