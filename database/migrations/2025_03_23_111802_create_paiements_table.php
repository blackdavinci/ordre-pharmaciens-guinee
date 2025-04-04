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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['inscription', 'reinscription'])->default('inscription');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable(); // ex: orange money, playcard, etc.
            $table->string('reference')->nullable(); // référence de transaction
            $table->string('inscription_token')->nullable()->index();
            $table->enum('status', ['failed', 'success','pending'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
