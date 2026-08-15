<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Whatsapp\StoreWhatsappInstanceRequest;
use App\Services\Whatsapp\WhatsappInstanceService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class WhatsappInstanceController extends Controller
{
    public function __construct(protected WhatsappInstanceService $whatsappInstanceService) {}

    public function store(StoreWhatsappInstanceRequest $request) 
    {
        $data = $this->whatsappInstanceService->store($request->validated());
        return ApiResponse::success(
            $data,
            'Instance created with success!',
            201
        );
    }
}
