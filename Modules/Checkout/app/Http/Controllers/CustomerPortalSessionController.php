<?php

namespace Modules\Checkout\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Checkout\Services\CustomerPortalAuthService;
use Modules\Checkout\Services\CheckoutContentResolver;

class CustomerPortalSessionController extends Controller
{
    public function showLogin(
        Request $request,
        CustomerPortalAuthService $authService,
        CheckoutContentResolver $contentResolver
    ): View|RedirectResponse
    {
        if ($authService->currentUser($request)) {
            return redirect()->route('checkout.customer.dashboard');
        }

        return view('checkout::customer-login', [
            'pageCopy' => $contentResolver->loginCopy(),
            'support' => $contentResolver->supportChannels(),
        ]);
    }

    public function showRegister(
        Request $request,
        CustomerPortalAuthService $authService,
        CheckoutContentResolver $contentResolver
    ): View|RedirectResponse
    {
        if ($authService->currentUser($request)) {
            return redirect()->route('checkout.customer.dashboard');
        }

        return view('checkout::customer-register', [
            'pageCopy' => $contentResolver->registerCopy(),
            'support' => $contentResolver->supportChannels(),
        ]);
    }

    public function register(Request $request, CustomerPortalAuthService $authService): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'legal_acceptance' => ['accepted'],
        ]);

        $normalizedPhone = $authService->normalizePhone((string) $request->input('phone'));
        if ($normalizedPhone === '') {
            return back()
                ->withErrors(['phone' => 'Gecerli bir telefon numarasi girin.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        if (\App\Models\User::query()->where('phone', $normalizedPhone)->exists()) {
            return back()
                ->withErrors(['phone' => 'Bu telefon numarasi ile kayitli bir hesap var.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $user = $authService->register([
            'name' => (string) $request->input('name'),
            'phone' => $normalizedPhone,
            'email' => $request->input('email'),
            'password' => (string) $request->input('password'),
        ]);

        $authService->login($request, $user);

        return redirect()
            ->intended(route('checkout.customer.dashboard'))
            ->with('status', 'Hesabiniz olusturuldu ve giris yapildi.');
    }

    public function login(Request $request, CustomerPortalAuthService $authService): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $user = $authService->attempt(
            phone: (string) $validated['phone'],
            password: (string) $validated['password']
        );

        if (! $user) {
            return back()
                ->withErrors(['phone' => 'Telefon veya sifre eslesmedi.'])
                ->withInput($request->only('phone'));
        }

        $authService->login($request, $user);

        return redirect()
            ->route('checkout.customer.dashboard')
            ->with('status', 'Musteri paneline giris yapildi.');
    }

    public function logout(Request $request, CustomerPortalAuthService $authService): RedirectResponse
    {
        $authService->logout($request);

        return redirect()
            ->route('checkout.customer.login')
            ->with('status', 'Oturum kapatildi.');
    }
}
