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
        Schema::table('campaign_contributions', function (Blueprint $table) {
            $table->dropForeign(['contributor_id']);
            $table->dropColumn('contributor_id');
            $table->string('status')->default('received')->change();
            $table->timestamp('paid_at')->nullable(false)->change();
            $table->string('asaas_payment_id')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign_contributions', function (Blueprint $table) {
            $table->uuid('contributor_id');
            $table->foreign('contributor_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('status')->default('pending')->change();
            $table->date('paid_at')->nullable()->change();
            $table->dropUnique(['asaas_payment_id']);
            $table->dropColumn('asaas_payment_id');
        });
    }
};
