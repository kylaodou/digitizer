<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Chaque colonne n'est ajoutée que si elle n'existe pas encore : la colonne
     * « document_type » est déjà créée par la migration de la table documents,
     * et une installation neuve échouait sur ce doublon.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'ai_summary')) {
                $table->text('ai_summary')->nullable()->after('ocr_text');
            }
            if (!Schema::hasColumn('documents', 'ai_analysis_completed')) {
                $table->boolean('ai_analysis_completed')->default(false)->after('ai_summary');
            }
            if (!Schema::hasColumn('documents', 'document_type')) {
                $table->string('document_type')->nullable()->after('ai_analysis_completed');
            }
            if (!Schema::hasColumn('documents', 'extracted_info')) {
                $table->json('extracted_info')->nullable()->after('document_type');
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
            $table->dropColumn([
                'ai_summary',
                'ai_analysis_completed',
                'document_type',
                'extracted_info',
                'ai_analyzed_at'
            ]);
        });
    }
};
