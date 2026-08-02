<?php

namespace App\Services\Permission;

use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionService {

    /**
     * Falta criar uma função para vincular a permission a uma role
     */

    public function index() {

        return Permission::all();

    }

    public function store(array $data) {

        return DB::transaction(function () use ($data) {

            $permission = Permission::create([
                'name' => $data['name'],
                'guard_name' => 'api'
            ]);

            return $permission;

        });

    }

    public function show(Permission $permission) {

        return $permission;

    }

    public function update(array $data, Permission $permission) {

        return DB::transaction(function() use ($data, $permission) {

            $permission->update([
                'name' => $data['name'],
                'guard_name' => 'api'
            ]);

            /**
             * A função update retorna true/false, para retornar o model uso
             * o fresh() para retornar o model carregado novamente
             */
            return $permission->fresh();


            // Colocar para atualizar automaticamente na role após alterar o nome
        });
    }

    public function destroy(Permission $permission) {

        return $permission->delete();

    }
}
