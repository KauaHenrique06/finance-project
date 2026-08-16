<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => PermissionSeeder::GUARD,
        ]);
        $admin->syncPermissions(Permission::where('guard_name', PermissionSeeder::GUARD)->get());

        $client = Role::firstOrCreate([
            'name' => 'client',
            'guard_name' => PermissionSeeder::GUARD,
        ]);
        $client->syncPermissions(PermissionSeeder::CLIENT_PERMISSIONS);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
