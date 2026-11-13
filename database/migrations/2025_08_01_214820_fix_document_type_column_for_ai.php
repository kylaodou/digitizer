<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Colonnes ajoutées seulement si elles n'existent pas encore
     * (la migration précédente en crée déjà une partie).
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Ajouter toutes les colonnes nécessaires pour l'IA
            if (!Schema::hasColumn('documents', 'ai_summary')) {
                $table->text('ai_summary')->nullable()->after('ocr_text');
            }
            if (!Schema::hasColumn('documents', 'ai_analysis_completed')) {
                $table->boolean('ai_analysis_completed')->default(false)->after('ai_summary');
            }
            if (!Schema::hasColumn('documents', 'ai_document_type')) {
                $table->string('ai_document_type', 50)->nullable()->after('ai_analysis_completed');
            }
            if (!Schema::hasColumn('documents', 'extracted_info')) {
                $table->json('extracted_info')->nullable()->after('ai_document_type');
            }
            if (!Schema::hasColumn('documents', 'ai_analyzed_at')) {
                $table->timestamp('ai_analyzed_at')->nullable()->after('extracted_info');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Supprimer toutes les colonnes ajoutées
            $table->dropColumn([
                'ai_summary',
                'ai_analysis_completed',
                'ai_document_type',
                'extracted_info',
                'ai_analyzed_at'
            ]);
        });
    }
};
