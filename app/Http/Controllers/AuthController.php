<?php

namespace App\Http\Controllers;

use App\Events\ResendVerification;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerificationRequest;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function verifyEmail(Request $request): JsonResponse
    {
        try {
            $user = User::findOrFail($request->route('id'));
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'User is already verified',
            ], 400);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            'success' => true,
        ], 200);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'data' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (config('features.auth.email_verification_required')) {
            if (empty($user->email_verified_at)) {
                throw ValidationException::withMessages([
                    'data' => ['The provided account has not been verified'],
                ]);
            }
        }

        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->event('login')
            ->log('logged in');

        return response()->json([
            'success' => 'true',
            'data' => [
                'user' => $user,
                'token' => $user->createToken($request->device_name)->plainTextToken,
            ],
        ], 200);
    }

    protected function create(RegisterRequest $request): JsonResponse
    {
        try {
            $user = new User();
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->save();

            event(new Registered($user));

            $response = [
                'success' => 'true',
                'data' => [
                    'user' => $user,
                ],
            ];

            // @codeCoverageIgnoreStart
            if (config('features.auth.token_on_register')) {
                $response['data']['token'] = $user->createToken('api')->plainTextToken;
            }
            // @codeCoverageIgnoreEnd

            return response()->json($response, 200);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            return response()->json([
                'success' => 'false',
                'data' => [
                    'error' => $e->getMessage(),
                ],
            ], 500);
            // @codeCoverageIgnoreEnd
        }
    }

    protected function resendVerification(VerificationRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->where('email_verified_at', null)->first();

        if ($user) {
            event(new ResendVerification($user));
        }

        return response()->json([
            'success' => 'true',
            'data' => [],
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        $user->tokens()->delete();

        return response()->json([
            'success' => 'true',
            'data' => 'Logged out',
        ], 200);
    }

    public function forgottenPassword(Request $request): JsonResponse | RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        Password::sendResetLink(
            $request->only('email')
        );

        return response()->json([
            'success' => 'true',
            'data' => '',
        ], 200);
    }

    public function resetForgottenPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'string', 'confirmed', 'min:6'],
        ]);

        $user = User::where('email', $request->get('email'))->first();
        $user->password = Hash::make($request->get('password'));
        $user->save();
        event(new PasswordReset($user));

        return response()->json([
            'success' => 'true',
            'data' => [],
        ], 200);
    }

    public function checkToken(): ?Authenticatable
    {
        return auth('sanctum')->user();
    }

    public function me(): JsonResponse
    {
        $user = auth('sanctum')->user();

        return response()->json([
            'success' => 'true',
            'data' => $user,
        ], 200);
    }
}
