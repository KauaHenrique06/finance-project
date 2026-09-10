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
        Schema::create('asaas_sub_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->string('asaas_account_id')->unique();      
            $table->string('asaas_wallet_id');                 
            $table->text('asaas_api_key');                      
            $table->string('asaas_access_token_id')->nullable();

            $table->string('login_email');                     
            $table->string('person_type')->nullable();         
            $table->string('account_agency')->nullable();      
            $table->string('account_number')->nullable();      
            $table->string('account_digit')->nullable();       

            $table->string('status')->default('pending');
            $table->date('commercial_info_expiration')->nullable(); 

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asaas_sub_accounts');
    }
};
