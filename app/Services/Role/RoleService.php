<?php

namespace App\Services\Role;

use App\Models\Role;

class RoleService {

    public function index() {

        return Role::with(['permissions'])->get();

    }
}
