<?php

use App\Enum\TransactionStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->boolean('has_installment');
            $table->boolean('is_paid')->default(false);
            $table->tinyInteger('quantity_installment');
            $table->tinyInteger('installment_number');
            $table->date('due_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('status')->default(TransactionStatusEnum::PENDING->value);

            $table->uuid('group_id');

            $table->uuid('payer_id')->nullable();
            $table->foreign('payer_id')->references('id')->on('users')->cascadeOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
