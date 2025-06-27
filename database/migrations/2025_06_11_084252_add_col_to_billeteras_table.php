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
        Schema::table('billeteras', function (Blueprint $table) {
            $table->string('nombre')->nullable()->after('user_id');
            $table->string('descripcion')->nullable()->after('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billeteras', function (Blueprint $table) {
            $table->dropColumn('nombre');
            $table->dropColumn('descripcion');
        });
    }
};
