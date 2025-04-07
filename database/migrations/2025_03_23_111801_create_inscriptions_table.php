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
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('numero_inscription')->unique()->nullable();
            $table->string('rpgm')->nullable();
            $table->string('prenom');
            $table->string('nom');
            $table->enum('genre', ['homme', 'femme'])->nullable();
            $table->date('date_naissance');
            $table->string('lieu_naissance');
            $table->string('pays_naissance');
            $table->string('nationalite');
            $table->boolean('citoyen_guineen')->default(false);

            $table->string('inscription_token')->nullable()->index();

            // Localisation
            $table->string('pays_residence');
            $table->string('ville_residence');
            $table->string('adresse_residence');
            $table->string('telephone_mobile');
            $table->string('email')->unique();

            // Identité
            $table->enum('type_piece_identite', ['cin', 'passeport'])->nullable();

            // Profil professionnel
            $table->string('profil'); // assistant, biologiste, délégué médical, etc.
            $table->enum('section', ['section a', 'section b'])->nullable();
            $table->year('annee_obtention_diplome');
            $table->boolean('diplome_etranger')->default(false);
            $table->boolean('salarie')->default(false);

            // Suivi de validation
            $table->enum('statut', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('motif_rejet')->nullable();
            $table->boolean('frais_paiement')->default(false);
            $table->dateTime('date_validation')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamp('expiration_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
