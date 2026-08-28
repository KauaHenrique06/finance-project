<?php

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
        Schema::table('transactions', function (Blueprint $table) {
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->boolean('has_installment')->default(false)->change();
            $table->tinyInteger('quantity_installment')->nullable()->change();
            $table->tinyInteger('installment_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            $table->boolean('has_installment')->change();
            $table->tinyInteger('quantity_installment')->nullable(false)->change();
            $table->tinyInteger('installment_number')->nullable(false)->change();
        });
    }
};
