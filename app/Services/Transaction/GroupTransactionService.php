<?php

namespace App\Services\Transaction;

use App\Exceptions\ApiException;
use App\Models\GroupTransaction;
use App\Models\Transaction;
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

            $count = $data['quantity_installment'];
            $installmentNumber = 1;
            $nextDueDate = Carbon::parse($data['due_date']);

            $group = GroupTransaction::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'owner_id' => $authUserId,
            ]);

            for ($count; $count > 0; $count--)
            {
                $transaction[] = Transaction::create([
                    'has_installment' => $data['has_installment'],
                    'quantity_installment' => $data['quantity_installment'],
                    'due_date' => $nextDueDate,
                    'installment_number' => $installmentNumber,
                    'group_id' => $group->id,
                ])->load(['groupTransaction.owner']);

                $nextDueDate->addMonth();
                $installmentNumber++;
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

    public function assignInstance(array $data)
    {

    }
}
