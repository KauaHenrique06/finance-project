<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\MarkTransactionAsPaidRequest;
use App\Http\Resources\Transaction\TransactionResource;
use App\Services\Transaction\TransactionService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * @tags Transaction
 */
class TransactionController extends Controller
{
    public function __construct(protected TransactionService $transactionService) {}

    /**
     * Mark a transaction as paid
     *
     * Settles a single instalment and records the payer and the payment date.
     */
    public function markTransactionAsPaid(MarkTransactionAsPaidRequest $request): JsonResponse
    {
        $data = $this->transactionService->markTransactionAsPaid($request->validated());
        return ApiResponse::success(
            new TransactionResource($data),
            'Transaction mark as paid with success!',
            200
        );
    }
}
