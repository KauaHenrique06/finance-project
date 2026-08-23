<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\DeleteEventRequest;
use App\Http\Requests\Event\IndexEventRequest;
use App\Http\Requests\Event\ShowEventRequest;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Resources\Event\EventCollection;
use App\Http\Resources\Event\EventResource;
use App\Services\Event\EventService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{

    public function __construct(protected EventService $eventService) {}

    public function store(StoreEventRequest $request)
    {
        $data = $this->eventService->store($request->validated());
        return ApiResponse::success(
            new EventResource($data),
            'Event was created with success!',
            201
        );
    }

    public function index(IndexEventRequest $request)
    {
        $data = $this->eventService->index($request->validated());
        return ApiResponse::success(
            new EventCollection($data),
            'Events was indexed with success!',
            200
        );
    }

    public function show(ShowEventRequest $request)
    {
        $data = $this->eventService->show($request->validated());
        return ApiResponse::success(
            new EventResource($data),
            'Event was indexed with success!',
            200
        );
    }

    public function delete(DeleteEventRequest $request)
    {
        $this->eventService->delete($request->validated());
        return ApiResponse::success(
            null,
            'Event was deleted with success!',
            200
        );
    }

    public function update(UpdateEventRequest $request)
    {
        $data = $this->eventService->update($request->validated());
        return ApiResponse::success(
            new EventResource($data),
            'Event was updated with success!',
            200
        );
    }
}
