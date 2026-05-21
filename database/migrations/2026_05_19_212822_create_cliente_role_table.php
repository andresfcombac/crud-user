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
    Schema::create('cliente_role', function (Blueprint $table) {
        $table->id();
        // Conecta con tu tabla de clientes
        $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
        // Conecta con tu tabla de roles
        $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_role');
    }
};
