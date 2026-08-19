<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blip_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->unique()->constrained('employees');
            $table->string('status')->default('interest_submitted');
            $table->text('reason')->nullable();
            $table->unsignedSmallInteger('tenure_months_at_registration')->nullable();

            $table->string('endorsed_by')->nullable();
            $table->timestamp('endorsed_at')->nullable();
            $table->text('endorsement_note')->nullable();

            $table->foreignId('cohort_id')->nullable()->constrained('blip_cohorts');
            $table->timestamp('confirmed_at')->nullable();

            $table->timestamp('promotion_ready_at')->nullable();
            $table->text('promotion_note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blip_enrollments');
    }
};
