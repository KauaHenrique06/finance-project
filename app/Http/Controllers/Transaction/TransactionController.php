<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\MarkTransactionAsPaidRequest;
use App\Http\Resources\Transaction\TransactionResource;
use App\Services\Transaction\TransactionService;
use App\Support\ApiResponse;

class TransactionController extends Controller
{
    public function __construct(protected TransactionService $transactionService) {}

    public function markTransactionAsPaid(MarkTransactionAsPaidRequest $request)
    {
        $data = $this->transactionService->markTransactionAsPaid($request->validated());
        return ApiResponse::success(
            new TransactionResource($data),
            'Transaction mark as paid with success!',
            200
        );
    }

}
