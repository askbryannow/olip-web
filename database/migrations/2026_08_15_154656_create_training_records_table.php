<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_records', function (Blueprint $table) {
            $table->id();
            $table->string('month_key');
            $table->string('outlet_id');
            $table->string('bu');
            $table->string('department');
            $table->string('state');
            $table->string('employment_type');
            $table->decimal('headcount', 8, 2);
            $table->decimal('training_hours', 10, 2);
            $table->decimal('mandatory_due', 8, 2);
            $table->decimal('mandatory_completed', 8, 2);
            $table->decimal('hrdc_claim_eligible', 12, 2);
            $table->decimal('hrdc_claim_submitted', 12, 2);
            $table->timestamps();
            $table->unique(['month_key', 'outlet_id', 'department', 'employment_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_records');
    }
};
