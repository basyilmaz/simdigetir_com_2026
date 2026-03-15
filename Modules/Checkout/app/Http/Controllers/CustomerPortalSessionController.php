<?php

namespace Modules\Checkout\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Checkout\Services\CustomerPortalAuthService;

class CustomerPortalSessionController extends Controller
{
    public function showLogin(Request $request, CustomerPortalAuthService $authService): View|RedirectResponse
    {
        if ($authService->currentUser($request)) {
            return redirect()->route('checkout.customer.dashboard');
        }

        return view('checkout::customer-login');
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
