<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = ([
            ['name' => 'view', 'guard_name' => 'api'],
            ['name' => 'store', 'guard_name' => 'api'],
            ['name' => 'update', 'guard_name' => 'api'],
            ['name' => 'delete', 'guard_name' => 'api'],

            ['name' => 'role.view', 'guard_name' => 'api'],
            ['name' => 'role.store', 'guard_name' => 'api'],
            ['name' => 'role.update', 'guard_name' => 'api'],
            ['name' => 'role.delete', 'guard_name' => 'api'],
            ['name' => 'role.assignPermission', 'guard_name' => 'api'],
            ['name' => 'assignRole', 'guard_name' => 'api'],

            ['name' => 'permission.view', 'guard_name' => 'api'],
            ['name' => 'permission.store', 'guard_name' => 'api'],
            ['name' => 'permission.update', 'guard_name' => 'api'],
            ['name' => 'permission.delete', 'guard_name' => 'api'],
        ]);

        foreach($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => $permission['guard_name']
            ]);
        }
    }
}
