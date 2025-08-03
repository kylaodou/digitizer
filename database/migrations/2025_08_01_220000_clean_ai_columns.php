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
        Schema::table('documents', function (Blueprint $table) {
            // Supprimer les colonnes existantes si elles existent
            if (Schema::hasColumn('documents', 'ai_summary')) {
                $table->dropColumn('ai_summary');
            }
            if (Schema::hasColumn('documents', 'ai_analysis_completed')) {
                $table->dropColumn('ai_analysis_completed');
            }
            if (Schema::hasColumn('documents', 'document_type')) {
                $table->dropColumn('document_type');
            }
            if (Schema::hasColumn('documents', 'extracted_info')) {
                $table->dropColumn('extracted_info');
            }
            if (Schema::hasColumn('documents', 'ai_analyzed_at')) {
                $table->dropColumn('ai_analyzed_at');
            }
        });

        Schema::table('documents', function (Blueprint $table) {
            // Ajouter les colonnes proprement
            $table->string('scan_type', 20)->default('recto')->after('page_count'); // Type de scan (recto/recto_verso)
            $table->text('ai_summary')->nullable()->after('ocr_text');
            $table->boolean('ai_analysis_completed')->default(false)->after('ai_summary');
            $table->string('ai_document_type', 50)->nullable()->after('ai_analysis_completed');
            $table->json('extracted_info')->nullable()->after('ai_document_type');
            $table->timestamp('ai_analyzed_at')->nullable()->after('extracted_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'scan_type',
                'ai_summary',
                'ai_analysis_completed',
                'ai_document_type',
                'extracted_info',
                'ai_analyzed_at'
            ]);
        });
    }
};
