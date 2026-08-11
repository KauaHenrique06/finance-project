<?php

namespace App\Services\Transaction;

use App\Enum\TransactionStatusEnum;
use App\Exceptions\ApiException;
use App\Models\GroupTransaction;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Str;

class TransactionService
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

    public function markTransactionAsPaid(array $data): Transaction
    {

        $authUserId = Auth::id();
        $transaction = Transaction::findOrFail($data['id']);

        $usersToNotificate = User::where('id', $transaction->owner_id)
            ->whereHas('participant', function ($query) use ($transaction) {
                $query->where('transasction_id', $transaction->id);
            })
            ->get();

        $payerId = !empty($data['payer_id']) 
            ? $data['payer_id']
            : $authUserId;

        return DB::transaction(function () use ($data, $transaction, $usersToNotificate, $payerId) {

            $transaction->update([
                'is_paid' => $data['is_paid'],
                'status' => TransactionStatusEnum::PAID->value,
                'payer_id' => $payerId,
                'payment_date' => now()->toDateString(),
            ]);

            $notifyData = [
                'usersToNotificate' => $usersToNotificate,
                'message' => "User {{name}} mark transaction as paid!",
                'data' => ['transaction_id' => $transaction->id, 'transaction_title' => $transaction->title]
            ];

            $this->sendNotify($notifyData);

            return $transaction->refresh();
        });
    }

    public function indexTransactionByGroupId(array $data): LengthAwarePaginator
    {
        return GroupTransaction::with(['transaction', 'owner'])
            ->findOrFail($data['id'])        
            ->paginate($data['perPage'], ['*'], 'page', $data['page']);
    }

    public function assignUserToTransaction(array $data): void
    {

        $transactions = Transaction::whereHas('group_id', fn ($query) => $query->where('group_id', $data['id']))->get();
        $participants = collect($data['user'])
            ->keyBy('id') 
            ->map(function ($user) {
                return ['can_edit' => $user['can_edit']];
            })
            ->toArray();

        DB::transaction(function () use ($transactions, $participants) {

            foreach ($transactions as $transaction) 
            {
                $transaction->participant()->syncWithoutDetaching($participants);
            }
        });
    }

    public function deleteGroup(array $data): void 
    {
        $authUserId = Auth::id();
        $transactions = Transaction::whereHas('groupTransaction', fn ($query) => $query->where('group_id', $data['id']))->get();

        $permittedUserId = $transactions->pluck('groupParticipant')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->values()
            ->toArray();
            
        $permittedUserId = array_merge($permittedUserId, [$authUserId]);

        if (in_array($permittedUserId, [$authUserId], false))
        {
            throw new ApiException("You can't delete this transaction group!");
        }

        $transactions->map(function($transaction) {
            $transaction->groupTransaction()->delete();
            $transaction->participant()->delete();
            $transaction->delete();
        });
    }

    protected function sendNotify(array $data): void 
    {
        foreach ($data['usersToNotificate'] as $user) 
        {

            $message = preg_replace('/\{\{\s*name\s*\}\}/', $user->name, $data['message']);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'transaction',
                'message' => $message,
                'read_at' => false,
                'data' => $data['data']
            ]);
        }
    }

}