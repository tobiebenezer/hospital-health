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
        Schema::create('diagnostics', function (Blueprint $table) {
            $table->id();
            $table->text('prescription')->nullable();
            $table->foreignId('report_id')->constrained('lab_reports')->nullable();
            $table->text('diagnostics')->nullable();
            $table->foreignId('patient_id')->constrained('patients')->nullable();
            $table->foreignId('doctor_id')->constrained('doctors')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostics');
    }
};
