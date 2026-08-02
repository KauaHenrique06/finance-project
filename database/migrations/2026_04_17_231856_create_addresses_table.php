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
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('street');
            $table->mediumInteger('number');
            $table->string('neighborhood');
            $table->char('state', 2);
            $table->string('city');
            $table->string('reference')->nullable();
            $table->string('complement')->nullable();
            $table->char('zip_code', 8);
            $table->decimal('latitude', 10,2)->nullable();
            $table->decimal('longitude', 11,2)->nullable();

            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
