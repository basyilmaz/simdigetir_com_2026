<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@simdigetir.com')],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'change-me-in-env')),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $role = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);

        if (! $admin->hasRole($role)) {
            $admin->assignRole($role);
        }
    }
}
