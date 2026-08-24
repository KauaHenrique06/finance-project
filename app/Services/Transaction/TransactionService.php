<?php

namespace App\Services\Transaction;

use App\Enum\TransactionStatusEnum;
use App\Exceptions\ApiException;
use App\Models\Group;
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
        $group = Group::where('id', $transaction->group_id)->firstOrFail();

        $usersCanPay = $group->participant()
            ->pluck('participant_id')
            ->push($group->owner_id)
            ->unique()
            ->values();

        $payerId = !empty($data['payer_id'])
            ? $data['payer_id']
            : $authUserId;

        if (!$usersCanPay->contains($authUserId))
        {
            throw new ApiException("You can't mark this transaction as paid!", 403);
        }

        if (!$usersCanPay->contains($payerId))
        {
            throw new ApiException("The payer must be a member of the group!", 403);
        }

        $usersToNotificate = User::whereIn('id', $usersCanPay)->get();

        return DB::transaction(function () use ($data, $transaction, $group, $usersToNotificate, $payerId) {

            $transaction->update([
                'is_paid' => $data['is_paid'],
                'status' => TransactionStatusEnum::PAID->value,
                'payer_id' => $payerId,
                'payment_date' => now()->toDateString(),
            ]);

            $notifyData = [
                'usersToNotificate' => $usersToNotificate,
                'message' => "User {{name}} mark transaction as paid!",
                'data' => ['transaction_id' => $transaction->id, 'transaction_title' => $group->title]
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
                'read_at' => null,
                'data' => $data['data']
            ]);
        }
    }

}
