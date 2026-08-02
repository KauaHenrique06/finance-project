<?php

namespace App\Services\Role;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoleService {

    public function index() {

        return Role::with(['permissions'])->get();

    }

    public function store(array $data) {

        return DB::transaction(function () use ($data) {

            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'api'
            ]);

            return $role->fresh();

        });

    }

    public function show(Role $role) {

        return $role->load(['permissions']);

    }

    public function update(array $data, Role $role) {

        return DB::transaction(function () use ($data, $role) {

            $role->update([
                'name' => $data['name'],
                'guard_name' => 'api'
            ]);

            $role->fresh();

            return $role->load(['permissions']);

        });

    }

    public function destroy(Role $role) {

        return $role->delete();

    }

    public function storePermissionToRole(array $data, Role $role) {

        return DB::transaction(function() use ($data, $role) {

            foreach($data['permissions'] as $permission) {
                $role->permissions()->syncWithoutDetaching($permission);
            }

            return $role->load(['permissions']);

            // $role->firstOrFail();
            // // dd($role);
            // foreach($data['permissions'] ?? [] as $permission) {

            //     if(isset($permission['permission_id'])) {
            //         $role->permissions()->syncWithoutDetaching($permission['permission_id'],
            //         [
            //             'role_id' => $role->id,
            //             'permission_id' => $permission['permission_id']
            //         ]);
            //     }
            // }

            // return $role->load(['permissions']);

        });
    }

    public function storeRoleToUser(User $user, Role $role) {

        return DB::transaction(function() use ($user, $role) {

            /**
             * Retira a role antiga e atualiza pra nova,
             * se quiser que tenha mais roles usar o assignRole()
             *  */
            $user->syncRoles($role);

            return $user->load(['roles']);

        });

    }
}
