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
        Schema::create('campaign_contributions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('status')->default('pending');
            $table->decimal('amount', 8,2);
            $table->date('paid_at');
            
            $table->uuid('contributor_id');
            $table->uuid('campaign_id');

            $table->foreign('contributor_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('campaign_id')->references('id')->on('campaigns')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_contributions');
    }
};
