<?php

namespace App\Services\Transaction;

use App\Enum\TransactionStatusEnum;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\DB;

class TransactionService
{

    public function markTransactionAsPaid(array $data): Transaction
    {

        $authUserId = Auth::id();
        $transaction = Transaction::findOrFail($data['id']);

        $usersToNotificate = User::where(function ($query) use ($transaction) {
                $query->where('id', $transaction->groupTransaction->owner_id)
                    ->orWhereHas('participantGroupTransaction', function ($query) use ($transaction) {
                        $query->where('group_transaction.id', $transaction->group_id);
                    });
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

            TransactionService::sendNotify($notifyData);

            return $transaction->refresh();
        });
    }

    protected static function sendNotify(array $data): void
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
