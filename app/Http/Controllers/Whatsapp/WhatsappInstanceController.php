<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Whatsapp\DeleteWhatsappInstanceRequest;
use App\Http\Requests\Whatsapp\IndexWhatsappInstanceRequest;
use App\Http\Requests\Whatsapp\StoreWhatsappInstanceRequest;
use App\Http\Resources\Whatsapp\WhatsappInstanceCollection;
use App\Http\Resources\Whatsapp\WhatsappInstanceResource;
use App\Services\Whatsapp\WhatsappInstanceService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * @tags WhatsApp
 */
class WhatsappInstanceController extends Controller
{
    public function __construct(protected WhatsappInstanceService $whatsappInstanceService) {}

    /**
     * List WhatsApp instances
     *
     * Returns the paginated list of WhatsApp instances.
     */
    public function index(IndexWhatsappInstanceRequest $request): JsonResponse
    {
        $data = $this->whatsappInstanceService->index($request->validated());
        return ApiResponse::success(
            new WhatsappInstanceCollection($data),
            'Instances indexed with success!',
            200
        );
    }

    /**
     * Create a WhatsApp instance
     *
     * Creates the instance on the Evolution API and stores it locally. The
     * response carries the first QR code, which expires quickly — further
     * codes arrive through the `whatsapp-instance.{userId}` broadcast channel.
     */
    public function store(StoreWhatsappInstanceRequest $request): JsonResponse
    {
        $data = $this->whatsappInstanceService->store($request->validated());
        return ApiResponse::success(
            new WhatsappInstanceResource($data),
            'Instance created with success!',
            201
        );
    }

    /**
     * Delete a WhatsApp instance
     *
     * Removes the instance from the Evolution API and from the database.
     */
    public function delete(DeleteWhatsappInstanceRequest $request): JsonResponse
    {
        $this->whatsappInstanceService->delete($request->validated());
        return ApiResponse::success(
            null,
            'Instance deleted with success!',
            200
        );
    }
}
