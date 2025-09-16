<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\LoginUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponses;

    /**
     * Login a user and create a token
     *
     * Authenticates a user using their email and password, and generates an API token for subsequent requests.
     * @unauthenticated
     * @group Authentication
     * @response 200 {
            "data": {
                "token": "{YOUR_AUTH_KEY_HERE}"
            },
            "message": "Authenticated",
            "status": 200
        }
     */

    public function login(LoginUserRequest $request)
    {
        // $request->validate($request->all());

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->ok(
            'Authenticated',
            [
                'token' => $user->createToken(
                    'api_token' . $user->email,
                    ['*'],
                    now()->addHour()
                )->plainTextToken
            ]
        );
    }

    /**
     * Logout a user and delete the token
     *
     * Signs out a user by deleting their current API token, effectively revoking access to protected endpoints.
     * @group Authentication
     * @response 200 {}
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok('Logged out');
    }

    /**
     * Register a new user and create a token
     *
     * Creates a new user account and generates an API token for subsequent requests.
     * @unauthenticated
     * @group Authentication
     * @response 200 {
            "data": {
                "token": "{YOUR_AUTH_KEY_HERE}"
            },
            "message": "User registered successfully",
            "status": 200
        }
     */
    public function register(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        return $this->ok(
            'User registered successfully',
            [
                'token' => $user->createToken(
                    'api_token' . $user->email,
                    ['*'],
                    now()->addHour()
                )->plainTextToken
            ]
        );
    }
}
