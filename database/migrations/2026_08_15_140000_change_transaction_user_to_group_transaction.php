<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('transaction_user')->truncate();

        Schema::table('transaction_user', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropColumn('transaction_id');

            $table->uuid('group_id')->after('participant_id');
            $table->foreign('group_id')->references('id')->on('group_transaction')->cascadeOnDelete();

            $table->unique(['group_id', 'participant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('transaction_user')->truncate();

        Schema::table('transaction_user', function (Blueprint $table) {
            $table->dropUnique(['group_id', 'participant_id']);
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');

            $table->uuid('transaction_id')->after('participant_id');
            $table->foreign('transaction_id')->references('id')->on('transactions')->cascadeOnDelete();
        });
    }
};
