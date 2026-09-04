<?php

namespace App\Services\Group;

use App\Exceptions\ApiException;
use App\Models\Event;
use App\Models\Group;
use App\Models\Transaction;
use Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

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
            Gate::authorize('create', [Group::class, $event]);

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

            $groupWithUser = array_merge($group->toArray(), ['user' => $participantId]);
            $this->assignParticipant($groupWithUser);

            if ($data['is_split'])
            {
                $this->storeSplitTransaction($data, $group, $participantId);
            } else {
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

        if ($group->transaction()->where('is_paid', true)->exists())
        {
            throw new ApiException("This group already has paid transactions, you can't update it!", 409);
        }

        return DB::transaction(function () use ($data, $group) {

            $group->update(Arr::only($data, ['title', 'description', 'total_amount']));

            if ($this->shouldRebuildTransaction($data))
            {
                $transactionData = $this->mergeTransactionData($data, $group);

                $group->transaction()->forceDelete();

                $data['is_split']
                    ? $this->storeSplitTransaction(
                        $transactionData,
                        $group,
                        $group->participant()->pluck('participant_id')->all()
                    )
                    : $this->storeTransaction($transactionData, $group);
            }

            return $group->load(['owner', 'participant', 'transaction.user']);
        });
    }

    public function assignParticipant(array $data): void
    {
        $group = Group::findOrFail($data['id']);
        Gate::authorize('assignParticipant', $group);

        $isSplit = $group->transaction()->whereNotNull('user_id')->exists();

        if ($isSplit && $group->transaction()->where('is_paid', true)->exists())
        {
            throw new ApiException("You can't assign a participant in a split transaction already paid!", 409);
        }

        $participants = collect($data['user'])
            ->toArray();

        DB::transaction(function () use ($group, $participants, $isSplit) {
            $group->participant()->syncWithoutDetaching($participants);

            if (!$isSplit)
            {
                return;
            }

            // The divisor changed, so every share has to be calculated again
            $transactionData = $this->mergeTransactionData([], $group);
            $group->transaction()->delete();

            $this->storeSplitTransaction(
                $transactionData,
                $group,
                $group->participant()->pluck('participant_id')->all()
            );
        });
    }

    private function shouldRebuildTransaction(array $data): bool
    {
        return (bool) array_intersect_key(
            $data,
            array_flip(['is_split', 'has_installment', 'quantity_installment', 'total_amount', 'due_date'])
        );
    }

    private function mergeTransactionData(array $data, Group $group): array
    {
        $dueDate = $data['due_date']
            ?? $group->transaction()->orderBy('installment_number')->value('due_date');

        if (!$dueDate)
        {
            throw new ApiException('This group has no due date, send due_date to rebuild its transactions!', 422);
        }

        return [
            'total_amount' => (float) $group->total_amount,
            'due_date' => $dueDate,
            'has_installment' => $data['has_installment'] ?? false,
            'quantity_installment' => $data['quantity_installment'] ?? null,
        ];
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
        $participantId = array_values(array_unique(array_merge($participantId, [$group->owner_id])));
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
