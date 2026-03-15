<?php

namespace Modules\Checkout\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerPortalAuthService
{
    private const SESSION_KEY = 'checkout_customer_portal_user_id';

    public function attempt(string $phone, string $password): ?User
    {
        $normalizedInput = $this->comparablePhone($phone);
        if ($normalizedInput === '') {
            return null;
        }

        /** @var User|null $user */
        $user = User::query()
            ->where('is_active', true)
            ->whereNotNull('phone')
            ->get()
            ->first(fn (User $candidate) => $this->comparablePhone((string) $candidate->phone) === $normalizedInput);

        if (! $user || ! Hash::check($password, (string) $user->password)) {
            return null;
        }

        return $user;
    }

    public function login(Request $request, User $user): void
    {
        $request->session()->put(self::SESSION_KEY, $user->id);
        $request->session()->regenerate();
    }

    public function logout(Request $request): void
    {
        $request->session()->forget(self::SESSION_KEY);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function currentUser(Request $request): ?User
    {
        $userId = $request->session()->get(self::SESSION_KEY);
        if (! is_numeric($userId)) {
            return null;
        }

        return User::query()
            ->where('is_active', true)
            ->find((int) $userId);
    }

    private function comparablePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', trim($phone)) ?? '';
        if ($digits === '') {
            return '';
        }

        if (strlen($digits) >= 10) {
            return substr($digits, -10);
        }

        return $digits;
    }
}
