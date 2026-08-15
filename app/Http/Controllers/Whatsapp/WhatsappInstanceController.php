<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Whatsapp\DeleteWhatsappInstanceRequest;
use App\Http\Requests\Whatsapp\IndexWhatsappInstanceRequest;
use App\Http\Requests\Whatsapp\StoreWhatsappInstanceRequest;
use App\Services\Whatsapp\WhatsappInstanceService;
use App\Support\ApiResponse;

class WhatsappInstanceController extends Controller
{
    public function __construct(protected WhatsappInstanceService $whatsappInstanceService) {}

    public function index(IndexWhatsappInstanceRequest $request) 
    {
        $data = $this->whatsappInstanceService->index($request->validated());
        return ApiResponse::success(
            $data,
            'Instances indexed with success!',
            200
        );
    }

    public function store(StoreWhatsappInstanceRequest $request) 
    {
        $data = $this->whatsappInstanceService->store($request->validated());
        return ApiResponse::success(
            $data,
            'Instance created with success!',
            201
        );
    }

    public function delete(DeleteWhatsappInstanceRequest $request) 
    {
        $this->whatsappInstanceService->delete($request->validated());
        return ApiResponse::success(
            null,
            'Instance deleted with success!',
            201
        );
    }
}
