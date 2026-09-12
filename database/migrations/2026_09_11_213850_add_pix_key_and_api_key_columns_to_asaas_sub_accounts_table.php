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
        Schema::table('asaas_sub_accounts', function (Blueprint $table) {
            $table->string('asaas_pix_key')->unique()->nullable();
            $table->string('asaas_pix_key_id')->unique()->nullable();
            $table->string('asaas_access_token_api_key')->nullable()->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asaas_sub_accounts', function (Blueprint $table) {
            $table->dropColumn('asaas_pix_key');
            $table->dropColumn('asaas_pix_key_id');
        });
    }
};
