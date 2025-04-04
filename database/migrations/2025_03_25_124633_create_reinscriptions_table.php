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
        Schema::create('reinscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->year('annee');
            $table->boolean('paiement_effectue')->default(false);
            $table->string('fichier_justificatif')->nullable(); // preuve activité ou autre doc
            $table->string('attestation_generee')->nullable(); // chemin du PDF
            $table->enum('statut', ['en attente', 'validé', 'rejeté'])->default('en attente');
            $table->text('motif_rejet')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
            // Empêche les doublons pour un même user et une même année
            $table->unique(['user_id', 'annee']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reinscriptions');
    }
};
