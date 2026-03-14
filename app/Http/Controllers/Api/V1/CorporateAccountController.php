<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CorporateAccount;
use App\Models\CorporateAccountAddress;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CorporateAccountController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:corporate_accounts,slug'],
            'tax_no' => ['nullable', 'string', 'max:40'],
            'tax_office' => ['nullable', 'string', 'max:120'],
            'invoice_email' => ['nullable', 'email', 'max:255'],
            'billing_address' => ['nullable', 'string'],
            'owner_user_id' => ['required', 'integer', 'exists:users,id'],
            'addresses' => ['nullable', 'array'],
            'addresses.*.label' => ['required_with:addresses', 'string', 'max:80'],
            'addresses.*.address' => ['required_with:addresses', 'string'],
            'addresses.*.lat' => ['nullable', 'numeric'],
            'addresses.*.lng' => ['nullable', 'numeric'],
            'addresses.*.is_default' => ['nullable', 'boolean'],
        ]);

        $account = DB::transaction(function () use ($validated) {
            $account = CorporateAccount::query()->create([
                'name' => $validated['name'],
                'slug' => $validated['slug'] ?? Str::slug($validated['name']).'-'.Str::lower(Str::random(4)),
                'tax_no' => $validated['tax_no'] ?? null,
                'tax_office' => $validated['tax_office'] ?? null,
                'invoice_email' => $validated['invoice_email'] ?? null,
                'billing_address' => $validated['billing_address'] ?? null,
                'status' => 'active',
                'contract_meta' => [],
            ]);

            /** @var User $owner */
            $owner = User::query()->findOrFail((int) $validated['owner_user_id']);
            $account->users()->attach($owner->id, ['role' => 'owner']);

            foreach ((array) ($validated['addresses'] ?? []) as $address) {
                CorporateAccountAddress::query()->create([
                    'corporate_account_id' => $account->id,
                    'label' => $address['label'],
                    'address' => $address['address'],
                    'lat' => $address['lat'] ?? null,
                    'lng' => $address['lng'] ?? null,
                    'is_default' => (bool) ($address['is_default'] ?? false),
                ]);
            }

            return $account;
        });

        return response()->json([
            'success' => true,
            'data' => $account->load(['users', 'addresses']),
        ], 201);
    }
}

