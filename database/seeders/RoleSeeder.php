<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $normalUser = Role::firstOrCreate(['name' => 'client']);
        $normalUser->givePermissionTo([
            'view',
            'store',
            'delete',
            'update',
            'assignRole'
        ]);
    }
}
