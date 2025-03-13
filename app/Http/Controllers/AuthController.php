<?php

namespace App\Http\Controllers;

use App\Models\roles;
use App\Models\User;
use App\Models\user_roles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate Token
        $token = $user->createToken('auth_token',['access'], Carbon::now()->addHours(5))->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', ['refresh'], Carbon::now()->addDays(2))->plainTextToken;

        // Find User Role
        $userRolename = User::with('roles')->find($user->id);
        // $userRolename->roles->makeHidden('pivot');
        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            "user" => $userRolename,
            'token_type' => 'Bearer'
        ]);
    }

    // User Logout API
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    // Get Authenticated User
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    // Get Authenticated User
    public function allUsers(Request $request)
    {
        $usersList = User::all();
        return response()->json($usersList);
    }

    public function signUp(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4',
        ]);

        $superAdminRole = Roles::whereRaw('LOWER(role_name) = ?', ['super admin'])->first();

        if(isset($superAdminRole)){
            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $userRole = user_roles::create([
                'userid' => $user->id,
                'roleid' => $superAdminRole->id,
            ]);

            // Return response
            return response()->json([
                'message' => 'Registration successful',
                // 'user' => $user,
            ], 201);
        }else{
             // Return response
             return response()->json([
                'message' => 'Admin Privilleges not found'
            ], 424);
        }

    }

    public function refreshToken(Request $request){
        $request->validate([
            'refresh_token' => 'required'
        ]);

        $refreshToken = $request->refresh_token;

        // Find the refresh token in the database
        $token = PersonalAccessToken::findToken($refreshToken);

        // Validate if it's a refresh token and check expiration
        if (!$token || !$token->can('refresh') || Carbon::now()->greaterThan($token->expires_at)) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }

        $user = $token->tokenable;

        // Revoke the old refresh token
        $token->delete();

        // Generate new Access Token (expires in 1 hour)
        $newAccessToken = $user->createToken('auth_token', ['access'], Carbon::now()->addHours(5))->plainTextToken;

        // Generate new Refresh Token (expires in 30 days)
        $newRefreshToken = $user->createToken('refresh_token', ['refresh'], Carbon::now()->addDays(3))->plainTextToken;

        return response()->json([
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'Bearer'
        ]);
    }

}
