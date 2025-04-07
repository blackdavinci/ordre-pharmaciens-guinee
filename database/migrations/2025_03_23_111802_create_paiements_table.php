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
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['inscription', 'reinscription'])->default('inscription');
            $table->string('code')->nullable();
            $table->dateTime('transaction_date')->nullable();
            $table->enum('status', ['new', 'pending','success','failed','canceled','error'])->default('new');
            $table->string('status_description')->nullable();
            $table->string('error_message')->nullable();
            $table->string('payment_method')->nullable(); // ex: orange money, playcard, etc.
            $table->string('payment_description')->nullable();
            $table->decimal('payment_amount', 10, 2);
            $table->string('payment_reference')->nullable(); // référence de transaction
            $table->string('merchant_name')->nullable();
            $table->string('token')->nullable()->index();
            $table->foreignId('inscription_id')->constrained('inscriptions')->onDelete('cascade'); // La clé étrangère fait référence à la table inscriptions
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
