<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password; // <--- Imported for password reset broker

class AuthController extends Controller
{
    /**
     * Handle an incoming registration request.
     */
           public function register(Request $request)
    {
        // Step 4: Validation
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        // Step 5: Password hashing & database insertion
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => $validatedData['password'], // Will be hashed automatically by Eloquent cast
        ]);

        // Generate an API token for the user
        $token = $user->createToken('auth_token')->plainTextToken;
        
        // Step 6: JSON response
        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Handle an incoming login request.
     */
    public function login(Request $request)
    {
        // 1. Validate incoming credentials
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 2. Find user by email
        $user = User::where('email', $credentials['email'])->first();

        // 3. Verify user exists and password is correct
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'errors' => [
                    'email' => ['The provided credentials do not match our records.']
                ]
            ], 422); // Return 422 Unprocessable Entity for validation failure
        }

        // 4. Generate a new API token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Return JSON response with user and token
        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Send a password reset link to the user.
     */
    public function forgotPassword(Request $request)
    {
        // 1. Validate the email
        $request->validate([
            'email' => ['required', 'email']
        ]);

        // 2. Ask the Password broker to generate a token and send the reset link email
        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        // 3. Return a success response if sent, otherwise return a validation error
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['errors' => ['email' => [__($status)]]], 422);
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(Request $request)
    {
        // 1. Validate incoming reset data
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 2. Attempt to reset the password via the Password broker
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                // Update password (hashed automatically by the 'password' => 'hashed' model cast)
                $user->forceFill([
                    'password' => $password
                ])->save();
            }
        );

        // 3. Return success response if reset, otherwise return a validation error
        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['errors' => ['email' => [__($status)]]], 422);
    }

    /**
     * Log the user out (revoke their token).
     */
    public function logout(Request $request)
    {
        // 1. Revoke the user's current access token
        $request->user()->currentAccessToken()->delete();

        // 2. Return a 204 No Content response indicating successful deletion
        return response()->noContent();
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // 1. Validate name, email (ignoring current user), and optional password
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // 2. Update name and email
        $user->name = $data['name'];
        $user->email = $data['email'];

        // 3. Update password only if a new one was provided
        if (!empty($data['password'])) {
            $user->password = $data['password']; // Hashed automatically by the model cast
        }

        $user->save();

        // 4. Return the updated user object
        return response()->json([
            'user' => $user,
            'message' => 'Profile updated successfully.'
        ], 200);
    }
}

