<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['company_documents', 'fleet_documents'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('review_status', 20)->default('pending')->after('status')->index();
                $table->foreignId('reviewed_by')->nullable()->after('review_status')->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
                $table->text('review_comment')->nullable()->after('reviewed_at');
            });
        }
    }

    public function down(): void
    {
        foreach (['company_documents', 'fleet_documents'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('reviewed_by');
                $table->dropColumn(['review_status', 'reviewed_at', 'review_comment']);
            });
        }
    }
};
