<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // --- Patient info ---
            $table->string('patient_first_name');
            $table->string('patient_last_name');
            $table->string('patient_ssn');
            $table->enum('patient_contact_method', ['email', 'call', 'sms']);
            $table->string('patient_contact_value');

            // --- Doctor info ---
            $table->string('doctor_first_name');
            $table->string('doctor_last_name');

            // --- Prescription info ---
            $table->date('prescribed_at');
            $table->integer('validity_duration_in_months')->default(12);
            $table->integer('dispense_interval_days')->default(28);
            $table->integer('renewable_count');
            $table->integer('dispensed_count')->default(0);
            $table->date('last_dispensed_at')->nullable();
            $table->date('next_dispense_at')->nullable();

            // --- Status & notes ---
            $table->enum('status', ['to_prepare', 'to_deliver', 'closed'])->default('to_prepare');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
