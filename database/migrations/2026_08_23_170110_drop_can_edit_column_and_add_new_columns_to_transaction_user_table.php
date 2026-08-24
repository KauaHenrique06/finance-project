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
        Schema::table('transaction_user', function (Blueprint $table) {
            $table->dropColumn('can_edit');

            $table->decimal('amount', 8,2)->nullable();
            $table->boolean('is_paid')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_user', function (Blueprint $table) {
            $table->boolean('can_edit')->default(false);

            $table->dropColumn('amount');
            $table->dropColumn('is_paid');
        });
    }
};
