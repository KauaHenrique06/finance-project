<?php 

namespace App\Services\Dashboard;

use App\Enum\TransactionStatusEnum;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class GroupDashboardService
{
    public function dashboard(array $data)
    {
        $authUserId = Auth::id();

        $start = !empty($data['start_date'])
            ? $data['start_date'] 
            : now()->subDays(7);

        $end = !empty($data['end_date'])
            ? $data['end_date'] 
            : now();

        return [
            'total' => $this->calcGroupTotalAmount($authUserId),
            'week' => $this->calcGroupTotalAmount($authUserId, now()->startOfWeek(), now()->endOfWeek()),
            'month' => $this->calcGroupTotalAmount($authUserId, now()->startOfMonth(), now()->endOfMonth()),
            'range' => $this->calcGroupTotalAmount($authUserId, $start, $end),
        ];
    }

    private function calcGroupTotalAmount(string $id, ?string $start = null, ?string $end = null)
    {
        $pending = Transaction::visibleTo($id)
            ->when($start && $end, function ($query) use ($start, $end) {
                $query->whereBetween('due_date', [$start, $end]);
            })
            ->where('status', TransactionStatusEnum::PENDING->value)
            ->sum('amount');

        $paid = Transaction::visibleTo($id)
            ->when($start && $end, function ($query) use ($start, $end) {
                $query->whereBetween('due_date', [$start, $end]);
            })
            ->where('status', TransactionStatusEnum::PAID->value)
            ->sum('amount');

        return [
            'pending' => $pending,
            'paid' => $paid,
        ];
    }
}