<?php

namespace App\Services\Group;

use App\Exceptions\ApiException;
use App\Models\Event;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Transaction;
use App\Models\WhatsappInstance;
use Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class GroupService
{

    public function index(array $data)
    {
        $authUserId = Auth::id();

        return Group::with([
            'transaction',
            'owner',
            'participant'
        ])
        ->where('owner_id', $authUserId)
        ->orWhereHas('participant', fn ($query) => $query->where('participant_id', $authUserId))
        ->paginate($data['perPage'], ['*'], 'page', $data['page']);
    }

    public function store(array $data): Group
    {
        $authUserId = Auth::id();

        $participantId = !empty($data['participant'])
            ? $data['participant']
            : [];

        return DB::transaction(function () use ($data, $authUserId, $participantId) {

            $event = Event::find($data['event_id']);

            if (!$event)
            {
                throw new ApiException('This event is invalid!');
            }

            $group = Group::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'owner_id' => $authUserId,
                'total_amount' => $data['total_amount'],
                'event_id' => $data['event_id']
            ]);

            if ($data['is_split'])
            {
                $this->storeSplitTransaction($data, $group, $participantId);
            } else {
                $groupWithUser = array_merge($group->toArray(), ['user' => $participantId]);
                $this->assignParticipant($groupWithUser);
                $this->storeTransaction($data, $group);
            }

            return $group->load(['owner', 'participant', 'transaction']);
        });
    }

    public function indexTransactionByGroupId(array $data): LengthAwarePaginator
    {
        $group = Group::findOrFail($data['id']);
        Gate::authorize('view', $group);

        return Transaction::where('group_id', $data['id'])
            ->with(['group.owner', 'group.participant', 'payer'])
            ->orderBy('installment_number')
            ->paginate($data['perPage'], ['*'], 'page', $data['page']);
    }

    public function destroy(array $data): void
    {
        $group = Group::with('participant')->findOrFail($data['id']);
        Gate::authorize('delete', $group);

        DB::transaction(function () use ($group) {
            $group->transaction()->delete();
            $group->participant()->detach();
            $group->delete();
        });
    }

    public function update(array $data): Group
    {
        $group = Group::findOrFail($data['id']);
        Gate::authorize('update', $group);

        $current = $group->transaction()->orderBy('installment_number')->first();

        $hasInstallment = $data['has_installment'] ?? $current->has_installment;

        $quantityInstallment = $hasInstallment
            ? ($data['quantity_installment'] ?? $current->quantity_installment)
            : 1;

        $totalAmount = $data['total_amount'] ?? $group->total_amount;
        $nextDueDate = Carbon::parse($data['due_date'] ?? $current->due_date);

        $planChanged = $hasInstallment !== (bool) $current->has_installment
            || $quantityInstallment !== (int) $current->quantity_installment
            || (int) round($totalAmount * 100) !== (int) round($group->total_amount * 100)
            || !$nextDueDate->isSameDay($current->due_date);

        if ($planChanged && $group->transaction()->where('is_paid', true)->exists())
        {
            throw new ApiException("This group already has paid instalments, you can't change the instalment plan!", 409);
        }

        return DB::transaction(function () use ($data, $group, $quantityInstallment, $hasInstallment, $totalAmount, $nextDueDate, $planChanged) {

            $group->update($data);

            if (!$planChanged)
            {
                return $group->load(['owner', 'participant', 'transaction']);
            }

            $amountData = $this->calcTransactionAmount($totalAmount, $quantityInstallment);

            for ($installmentNumber = 1; $installmentNumber <= $quantityInstallment; $installmentNumber++)
            {
                $amountInCents = $installmentNumber === 1
                    ? $amountData['installmentInCents'] + $amountData['remainderInCents']
                    : $amountData['installmentInCents'];

                Transaction::updateOrCreate(
                    [
                        'group_id' => $group->id,
                        'installment_number' => $installmentNumber,
                    ],
                    [
                        'has_installment' => $hasInstallment,
                        'quantity_installment' => $quantityInstallment,
                        'due_date' => $nextDueDate->copy(),
                        'amount' => $amountInCents / 100,
                    ]
                );

                $nextDueDate->addMonth();
            }

            $group->transaction()
                ->where('installment_number', '>', $quantityInstallment)
                ->delete();

            return $group->load(['owner', 'participant', 'transaction']);
        });
    }


    public function assignParticipant(array $data): void
    {
        $group = Group::findOrFail($data['id']);

        Gate::authorize('assignParticipant', $group);

        $participants = collect($data['user'])
            ->toArray();

        DB::transaction(function () use ($group, $participants) {
            $group->participant()->syncWithoutDetaching($participants);
        });
    }

    private function storeTransaction(array $data, Group $group)
    {
        $nextDueDate = Carbon::parse($data['due_date']);
        $quantityInstallment = $data['has_installment']
            ? $data['quantity_installment']
            : 1;

        return DB::transaction(function () use ($nextDueDate, $quantityInstallment, $data, $group) {

            $amountData = $this->calcTransactionAmount($data['total_amount'], $quantityInstallment);

            for ($installmentNumber = 1; $installmentNumber <= $quantityInstallment; $installmentNumber++)
            {
                $amountInCents = $installmentNumber === 1
                    ? $amountData['installmentInCents'] + $amountData['remainderInCents']
                    : $amountData['installmentInCents'];

                Transaction::create([
                    'has_installment' => $data['has_installment'],
                    'quantity_installment' => $quantityInstallment,
                    'due_date' => $nextDueDate,
                    'installment_number' => $installmentNumber,
                    'amount' => $amountInCents / 100,
                    'group_id' => $group->id,
                ]);

                $nextDueDate->addMonth();
            }
        });
    }

    private function storeSplitTransaction(array $data, Group $group, array $participantId)
    {
        $participantId = array_merge($participantId, [$group->owner_id]);
        $amountData = $this->calcTransactionAmount($data['total_amount'], collect($participantId)->count());

        return DB::transaction(function () use ($data, $group, $participantId, $amountData) {
            foreach ($participantId as $id)
            {
                $amountInCents = $group->owner_id === $id
                    ? $amountData['installmentInCents'] + $amountData['remainderInCents']
                    : $amountData['installmentInCents'];

                Transaction::create([
                    'due_date' => $data['due_date'],
                    'amount' => $amountInCents / 100,
                    'group_id' => $group->id,
                    'user_id' => $id,
                ]);
            }
            return;
        });
    }

    private function calcTransactionAmount(float $totalAmount, int $quantityInstallment): array
    {
        $totalInCents = (int) round($totalAmount * 100);
        $installmentInCents = intdiv($totalInCents, $quantityInstallment);
        $remainderInCents = $totalInCents - ($installmentInCents * $quantityInstallment);

        return [
            'totalInCents' => $totalInCents,
            'installmentInCents' => $installmentInCents,
            'remainderInCents' => $remainderInCents
        ];
    }
}
