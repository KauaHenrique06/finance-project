<?php

namespace App\Services\Transaction;

use App\Exceptions\ApiException;
use App\Models\GroupTransaction;
use App\Models\Transaction;
use App\Models\TransactionUser;
use App\Models\WhatsappInstance;
use App\Support\Ownership;
use Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class GroupTransactionService
{

    public function store(array $data)
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

            $totalInCents = (int) round($data['total_amount'] * 100);
            $installmentInCents = intdiv($totalInCents, $quantityInstallment);
            $remainderInCents = $totalInCents - ($installmentInCents * $quantityInstallment);

            $transaction = [];

            for ($installmentNumber = 1; $installmentNumber <= $quantityInstallment; $installmentNumber++)
            {
                $amountInCents = $installmentNumber === 1
                    ? $installmentInCents + $remainderInCents
                    : $installmentInCents;

                $transaction[] = Transaction::create([
                    'has_installment' => $data['has_installment'],
                    'quantity_installment' => $quantityInstallment,
                    'due_date' => $nextDueDate,
                    'installment_number' => $installmentNumber,
                    'amount' => $amountInCents / 100,
                    'group_id' => $group->id,
                ])->load(['groupTransaction.owner']);

                $nextDueDate->addMonth();
            }

            return $transaction;
        });
    }

    public function show(array $data): LengthAwarePaginator
    {
        return Transaction::where('group_id', $data['id'])
            ->with(['groupTransaction.owner', 'groupTransaction.participant', 'payer'])
            ->paginate($data['perPage'], ['*'], 'page', $data['page']);
    }

    public function destroy(array $data): void
    {
        $group = GroupTransaction::with('participant')->findOrFail($data['id']);

        $permittedUserId = $group->participant
            ->pluck('id')
            ->push($group->owner_id)
            ->unique()
            ->values()
            ->toArray();

        if (!in_array(Auth::id(), $permittedUserId, false))
        {
            throw new ApiException("You can't delete this transaction group!", 403);
        }

        DB::transaction(function () use ($group) {
            $group->transaction()->delete();
            $group->participant()->detach();
            $group->delete();
        });
    }

    public function assignParticipant(array $data): void
    {
        $group = GroupTransaction::findOrFail($data['id']);
        Ownership::verify($group->owner_id, "You can't assign users to this transaction group!");

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
        Ownership::verify($group->owner_id, "You can't assign instance to this transaction group!");

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
}
