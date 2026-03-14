<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            \Modules\Settings\Database\Seeders\SettingsDatabaseSeeder::class,
            Sprint2FoundationSeeder::class,
        ]);
    }
}
