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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('billeteras')->onDelete('cascade');
            $table->string('mes', 7);
            $table->string('detalle_ingreso')->nullable();
            $table->float('ingreso')->nullable();
            $table->string('detalle_gasto')->nullable();
            $table->float('gasto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
