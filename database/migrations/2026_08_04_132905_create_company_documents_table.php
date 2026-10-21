<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('document_type_id')
                ->constrained('document_types')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('file_path', 255);
            $table->date('issued_at')->nullable();
            $table->date('valid_until')->index(); // suivi des échéances
            $table->enum('status', ['valid', 'expired', 'to_renew'])->default('valid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_documents');
    }
};
