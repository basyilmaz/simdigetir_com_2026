<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ]);

        $normalizedPhone = $this->normalizePhone((string) $request->input('phone'));
        if ($normalizedPhone === '') {
            throw ValidationException::withMessages([
                'phone' => ['Invalid phone number.'],
            ]);
        }

        if (User::query()->where('phone', $normalizedPhone)->exists()) {
            throw ValidationException::withMessages([
                'phone' => ['Phone number already exists.'],
            ]);
        }

        $email = (string) ($request->input('email') ?? '');
        if ($email === '') {
            $email = $this->syntheticEmailFromPhone($normalizedPhone);
        }

        $user = User::query()->create([
            'name' => (string) $request->input('name'),
            'email' => $email,
            'phone' => $normalizedPhone,
            'password' => (string) $request->input('password'),
            'is_active' => true,
        ]);

        $token = $user->createToken((string) $request->input('device_name', 'api-client'))->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'user' => $this->transformUser($user),
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['nullable', 'email', 'required_without:phone'],
            'phone' => ['nullable', 'string', 'max:30', 'required_without:email'],
            'password' => ['required', 'string'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ]);

        $user = $this->resolveLoginUser(
            email: $request->input('email'),
            phone: $request->input('phone')
        );

        if (! $user || ! Hash::check((string) $request->input('password'), (string) $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $token = $user->createToken((string) $request->input('device_name', 'api-client'))->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'user' => $this->transformUser($user),
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => $this->transformUser($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();

        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out.',
        ]);
    }

    private function resolveLoginUser(mixed $email, mixed $phone): ?User
    {
        if (is_string($email) && trim($email) !== '') {
            return User::query()->where('email', trim($email))->first();
        }

        $normalizedPhone = $this->normalizePhone((string) $phone);
        if ($normalizedPhone === '') {
            return null;
        }

        return User::query()->where('phone', $normalizedPhone)->first();
    }

    /**
     * @return array<string, mixed>
     */
    private function transformUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ];
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if ($digits === '') {
            return '';
        }

        if (strlen($digits) === 10) {
            return '90'.$digits;
        }

        if (strlen($digits) === 11 && Str::startsWith($digits, '0')) {
            return '9'.$digits;
        }

        return $digits;
    }

    private function syntheticEmailFromPhone(string $phone): string
    {
        return 'customer+'.$phone.'@simdigetir.local';
    }
}
