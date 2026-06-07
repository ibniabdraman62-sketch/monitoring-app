<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Qui ?
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();  // Snapshot du nom au moment de l'action
            $table->string('user_role')->nullable();  // Snapshot du rôle
            
            // Quoi ?
            $table->string('action', 50)->index();         // ex: site_created, login
            $table->string('category', 30)->index();       // auth, site, user, report, profile
            $table->string('description');                  // texte lisible
            
            // Sur quoi ?
            $table->string('model_type')->nullable();      // App\Models\Site
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_name')->nullable();      // ex: "exemple.com" — pour garder une trace même si le modèle est supprimé
            
            // Détails (pour les modifications)
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            // Contexte technique
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            // Statut (success / failure)
            $table->string('status', 20)->default('success');
            
            $table->timestamp('created_at')->useCurrent();
            
            // Index pour accélérer les filtres
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
            $table->index(['category', 'action']);
            
            // Clé étrangère (avec set null si user supprimé pour préserver l'historique)
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};