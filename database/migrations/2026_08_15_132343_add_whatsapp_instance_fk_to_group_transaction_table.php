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
        Schema::table('group_transaction', function (Blueprint $table) {
            $table->uuid('instance_id')->nullable();
            $table->foreign('instance_id')->references('id')->on('whatsapp_instances')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_transaction', function (Blueprint $table) {
            $table->dropForeign(['instance_id']);
            $table->dropColumn('instance_id');
        });
    }
};
