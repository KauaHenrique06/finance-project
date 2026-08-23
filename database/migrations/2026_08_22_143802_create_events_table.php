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
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title');
            $table->text('description');

            $table->uuid('owner_id');
            $table->uuid('instance_id')->nullable();

            $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('instance_id')->references('id')->on('whatsapp_instances')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
