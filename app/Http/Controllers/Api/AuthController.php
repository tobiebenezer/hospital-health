<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseController
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     * path="/api/auth/register",
     * summary="Register a new user",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"first_name", "last_name", "email", "password"},
     *       @OA\Property(property="first_name", type="string", format="string", example="John"),
     *       @OA\Property(property="last_name", type="string", format="string", example="Doe"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=201,
     *    description="User created successfully",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User created successfully")
     *    )
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Unprocessable Entity",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="The given data was invalid.")
     *    )
     * )
     * )
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = $this->authService->register($request->all());

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User created successfully',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'The given data was invalid',
                    'details' => $e->errors(),
                ],
                'meta' => [
                    'timestamp' => now(),
                ],
            ], 422);
        }
    }

    /**
     * @OA\Post(
     * path="/api/auth/login",
     * summary="Login a user",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email", "password"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Login successful",
     *    @OA\JsonContent(
     *       @OA\Property(property="access_token", type="string", example="token")
     *    )
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated.")
     *    )
     * )
     * )
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'INVALID_CREDENTIALS',
                        'message' => 'Invalid login details',
                    ],
                    'meta' => [
                        'timestamp' => now(),
                    ],
                ], 401);
            }

            $user = User::where('email', $request['email'])->firstOrFail();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
                'message' => 'Login successful',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'The given data was invalid',
                    'details' => $e->errors(),
                ],
                'meta' => [
                    'timestamp' => now(),
                ],
            ], 422);
        }
    }

    /**
     * @OA\Post(
     * path="/api/auth/logout",
     * summary="Logout a user",
     * tags={"Authentication"},
     * security={{"sanctum":{}}},
     * @OA\Response(
     *    response=200,
     *    description="Logout successful",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Logged out")
     *    )
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated.")
     *    )
     * )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/auth/refresh",
     * summary="Refresh authentication token",
     * tags={"Authentication"},
     * security={{"sanctum":{}}},
     * @OA\Response(
     *    response=200,
     *    description="Token refreshed successfully",
     *    @OA\JsonContent(
     *       @OA\Property(property="access_token", type="string", example="new_token")
     *    )
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated.")
     *    )
     * )
     * )
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
            ],
            'message' => 'Token refreshed successfully',
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/auth/forgot-password",
     * summary="Request a password reset link",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user email",
     *    @OA\JsonContent(
     *       required={"email"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Password reset link sent successfully",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Password reset link sent successfully")
     *    )
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Unprocessable Entity",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="The given data was invalid.")
     *    )
     * )
     * )
     */
    public function forgotPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            $this->authService->forgotPassword($request->input('email'));

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent successfully',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'The given data was invalid',
                    'details' => $e->errors(),
                ],
                'meta' => [
                    'timestamp' => now(),
                ],
            ], 422);
        }
    }

    /**
     * @OA\Post(
     * path="/api/auth/reset-password",
     * summary="Reset user password",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass token, email and new password",
     *    @OA\JsonContent(
     *       required={"token", "email", "password", "password_confirmation"},
     *       @OA\Property(property="token", type="string", example="reset_token"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="NewPassWord12345"),
     *       @OA\Property(property="password_confirmation", type="string", format="password", example="NewPassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Password reset successfully",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Password reset successfully")
     *    )
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Unprocessable Entity",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="The given data was invalid.")
     *    )
     * )
     * )
     */
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:8',
            ]);

            $this->authService->resetPassword(
                $request->input('email'),
                $request->input('password'),
                $request->input('token')
            );

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'The given data was invalid',
                    'details' => $e->errors(),
                ],
                'meta' => [
                    'timestamp' => now(),
                ],
            ], 422);
        }
    }

    /**
     * @OA\Post(
     * path="/api/auth/verify-email",
     * summary="Verify user email address",
     * tags={"Authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass email and OTP",
     *    @OA\JsonContent(
     *       required={"email", "otp"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="otp", type="string", example="123456"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Email verified successfully",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Email verified successfully")
     *    )
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Unprocessable Entity",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="The given data was invalid.")
     *    )
     * )
     * )
     */
    public function verifyEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|string',
            ]);

            $this->authService->verifyEmail($request->input('email'), $request->input('otp'));

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'The given data was invalid',
                    'details' => $e->errors(),
                ],
                'meta' => [
                    'timestamp' => now(),
                ],
            ], 422);
        }
    }

    /**
     * @OA\Post(
     * path="/api/auth/resend-verification",
     * summary="Resend email verification link",
     * tags={"Authentication"},
     * security={{"sanctum":{}}},
     * @OA\Response(
     *    response=200,
     *    description="Verification link sent",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Verification link sent")
     *    )
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated.")
     *    )
     * )
     * )
     */
    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ALREADY_VERIFIED',
                    'message' => 'This email has already been verified',
                ],
                'meta' => [
                    'timestamp' => now(),
                ],
            ], 400);
        }

        $this->authService->resendVerification($request->user()->email);

        return response()->json([
            'success' => true,
            'message' => 'Verification link sent successfully',
        ]);
    }
}
