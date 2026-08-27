<?php 

namespace App\Services\Event;

use App\Exceptions\ApiException;
use App\Models\Event;
use App\Models\WhatsappInstance;
use App\Services\Group\GroupService;
use Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EventService
{

    public function __construct(protected GroupService $groupService) {}

    public function store(array $data): Event
    {
        $authUserId = Auth::id();

        $groups = !empty($data['group'])
            ? $data['group']
            : null;

        if (!empty($data['instance_id']))
        {
            $instance = WhatsappInstance::where('status', 'connected')->find($data['instance_id']);

            if (!$instance)
            {
                throw new ApiException('The instance must be connnected!');
            }
        }

        return DB::transaction(function () use ($data, $groups, $authUserId) {
            $event = Event::create([
                'title' => $data['title'],
                'owner_id' => $authUserId,
                'description' => $data['description'] ?? null,
                'instance_id' => $data['instance_id'] ?? null,
            ]);
                
            $groupData = collect($groups)->map(
                fn ($group) => array_merge(
                    $group, ['event_id' => $event->id]
                ))->toArray();

            $quantityGroups = collect($groupData)->count();

            if (!empty($groupData) && is_array($groupData)) 
            {
                if ($quantityGroups > 1)
                {
                    foreach ($groupData as $group) 
                    {
                        $this->groupService->store($group);
                    }
                } else {
                    $this->groupService->store($groupData);
                }
            }
            
            return $event->load(['group.transaction', 'instance', 'owner']);
        });
    }

    public function index(array $data): LengthAwarePaginator
    {
        // Policy
        return Event::with(['group', 'instance', 'owner'])
            ->when($data['search'], function ($query, $search) {
                $query->whereAny(['title'], 'ILIKE', "%$search%");
            })
            ->paginate($data['perPage'], ['*'], 'page', $data['page']);
    }

    public function show(array $data): Event
    {
        // Policy
        return Event::with(['group', 'instance', 'owner'])->findOrFail($data['id']);
    }

    public function delete(array $data): void
    {
        // Policy
        Event::findOrFail($data['id'])->delete();
    }

    public function update(array $data): Event
    {
        // Policy
        $event = Event::findOrFail($data['id']);

        return DB::transaction(function () use ($event, $data) {
            $event->update($data);
            $event->refresh();
            return $event->load(['owner', 'instance', 'group']);
        });
    }

    public function assignInstanceToEvent(array $data): void
    {
        $event = Event::findOrFail($data['id']);
        $instance = WhatsappInstance::select('id', 'user_id', 'status')->findOrFail($data['instance_id']);

        if ($instance->user_id !== $event->owner_id) 
        {
            throw new ApiException("This instance doesn't belong to any member of the group!");
        }

        if ($instance->status !== 'connected')
        {
            throw new ApiException("This instance is not connected!");
        }

        $event->update([
            'instance_id' => $instance->id
        ]);
    }
}