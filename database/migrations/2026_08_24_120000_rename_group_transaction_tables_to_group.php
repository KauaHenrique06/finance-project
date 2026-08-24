<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('group_transaction', 'groups');
        Schema::rename('transaction_user', 'group_user');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('group_user', 'transaction_user');
        Schema::rename('groups', 'group_transaction');
    }
};
