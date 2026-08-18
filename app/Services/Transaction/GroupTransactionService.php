<?php

namespace App\Services\Transaction;

use App\Exceptions\ApiException;
use App\Models\GroupTransaction;
use App\Models\Transaction;
use App\Models\TransactionUser;
use App\Models\WhatsappInstance;
use Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class GroupTransactionService
{

    public function index(array $data) 
    {
        $authUserId = Auth::id();

        return GroupTransaction::with([
            'transaction', 
            'owner', 
            'participant'
        ])
        ->where('owner_id', $authUserId)
        ->orWhereHas('participant', fn ($query) => $query->where('participant_id', $authUserId))
        ->paginate($data['perPage'], ['*'], 'page', $data['page']);
    }

    public function store(array $data): GroupTransaction
    {
        $authUserId = Auth::id();

        return DB::transaction(function () use ($data, $authUserId) {

            $quantityInstallment = $data['has_installment']
                ? $data['quantity_installment']
                : 1;

            $nextDueDate = Carbon::parse($data['due_date']);

            $group = GroupTransaction::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'owner_id' => $authUserId,
                'total_amount' => $data['total_amount'],
            ]);

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

            return $group->load(['owner', 'participant', 'transaction']);
        });
    }

    public function indexTransactionByGroupId(array $data): LengthAwarePaginator
    {
        $group = GroupTransaction::findOrFail($data['id']);
        Gate::authorize('view', $group);

        return Transaction::where('group_id', $data['id'])
            ->with(['groupTransaction.owner', 'groupTransaction.participant', 'payer'])
            ->paginate($data['perPage'], ['*'], 'page', $data['page']);
    }

    public function destroy(array $data): void
    {
        $group = GroupTransaction::with('participant')->findOrFail($data['id']);
        Gate::authorize('delete', $group);

        DB::transaction(function () use ($group) {
            $group->transaction()->delete();
            $group->participant()->detach();
            $group->delete();
        });
    }

    public function update(array $data): GroupTransaction
    {
        $group = GroupTransaction::findOrFail($data['id']);
        Gate::authorize('update', $group);

        $current = $group->transaction()->orderBy('installment_number')->first();

        $hasInstallment = $data['has_installment'] ?? $current->has_installment;

        $quantityInstallment = $hasInstallment
            ? ($data['quantity_installment'] ?? $current->quantity_installment)
            : 1;

        $totalAmount = $data['total_amount'] ?? $group->total_amount;
        $nextDueDate = Carbon::parse($data['due_date'] ?? $current->due_date);

        if ($quantityInstallment !== $current->quantity_installment
            && $group->transaction()->where('is_paid', true)->exists())
        {
            throw new ApiException("This group already has paid instalments, you can't change the instalment count!", 409);
        }

        return DB::transaction(function () use ($data, $group, $quantityInstallment, $hasInstallment, $totalAmount, $nextDueDate) {

            $group->update($data);

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
        $group = GroupTransaction::findOrFail($data['id']);

        Gate::authorize('assignParticipant', $group);

        $participants = collect($data['user'])
            ->keyBy('id')
            ->map(function ($user) {
                return ['can_edit' => $user['can_edit']];
            })
            ->toArray();

        DB::transaction(function () use ($group, $participants) {
            $group->participant()->syncWithoutDetaching($participants);
        });
    }

    public function assignInstanceToGroup(array $data): void
    {
        $group = GroupTransaction::findOrFail($data['id']);
        $instance = WhatsappInstance::select('id', 'user_id', 'status')->findOrFail($data['instance_id']);
        Gate::authorize('assignInstance', $group);

        $usersIdPresentInGroup = TransactionUser::select('participant_id')
            ->where('group_id', $group->id)
            ->pluck('participant_id')
            ->flatten()
            ->toArray();

        $usersIdPresentInGroup = array_merge($usersIdPresentInGroup, [$group->owner_id]);

        if (!in_array($instance->user_id, $usersIdPresentInGroup)) 
        {
            throw new ApiException("This instance doesn't belong to any member of the group!");
        }

        if ($instance->status !== 'connected')
        {
            throw new ApiException("This instance is not connected!");
        }

        $group->update([
            'instance_id' => $instance->id
        ]);
    }

    protected function calcTransactionAmount(float $totalAmount, int $quantityInstallment): array
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
