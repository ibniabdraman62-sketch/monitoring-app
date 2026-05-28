<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute la valeur 'client' à l'ENUM 'role' de la table users.
 *
 * AVANT : ENUM('super_admin', 'agent')
 * APRÈS : ENUM('super_admin', 'agent', 'client')
 *
 * À exécuter avec : php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        // Ajoute 'client' à l'enum 'role' en gardant les valeurs existantes
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM('super_admin', 'agent', 'client')
            NOT NULL DEFAULT 'agent'
        ");
    }

    public function down(): void
    {
        // Restaure l'enum sans 'client'
        // ⚠️ Attention : si des utilisateurs ont déjà le rôle 'client', le rollback échouera.
        // Les supprimer ou changer leur rôle avant rollback.
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM('super_admin', 'agent')
            NOT NULL DEFAULT 'agent'
        ");
    }
};