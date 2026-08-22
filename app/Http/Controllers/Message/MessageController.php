<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\IndexMessageRequest;
use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Resources\Message\MessageCollection;
use App\Http\Resources\Message\MessageResource;
use App\Services\Message\MessageService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{

    public function __construct(protected MessageService $messageService) {}

    /**
     * Store a sent message
     * 
     * Store message and your users and groups
     */
    public function store(StoreMessageRequest $request)
    {
        $data = $this->messageService->store($request->validated());
        return ApiResponse::success(
            new MessageResource($data),
            'Message sent with success!',
            201
        );
    }

    public function index(IndexMessageRequest $request)
    {
        $data = $this->messageService->index($request->validated());
        return ApiResponse::success(
            new MessageCollection($data),
            'Message indexed with success!',
            200
        );
    }
}
