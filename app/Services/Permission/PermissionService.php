<?php

namespace App\Services\Permission;

use App\Models\Permission;

class PermissionService {

    public function index() {

        return Permission::all();

    }
}
