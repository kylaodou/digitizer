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
            $table->text('ai_summary')->nullable()->after('ocr_text');
            $table->boolean('ai_analysis_completed')->default(false)->after('ai_summary');
            $table->string('document_type')->nullable()->after('ai_analysis_completed');
            $table->json('extracted_info')->nullable()->after('document_type');
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
                'ai_summary',
                'ai_analysis_completed',
                'document_type',
                'extracted_info',
                'ai_analyzed_at'
            ]);
        });
    }
};
