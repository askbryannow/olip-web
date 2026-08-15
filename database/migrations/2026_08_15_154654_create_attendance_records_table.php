<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->string('month_key');
            $table->string('outlet_id');
            $table->string('bu');
            $table->string('department');
            $table->string('state');
            $table->decimal('working_days', 6, 2);
            $table->decimal('absent_days', 8, 2);
            $table->decimal('late_clock_ins', 8, 2);
            $table->decimal('headcount', 8, 2);
            $table->decimal('leave_annual', 8, 2)->default(0);
            $table->decimal('leave_medical', 8, 2)->default(0);
            $table->decimal('leave_emergency', 8, 2)->default(0);
            $table->decimal('leave_unpaid', 8, 2)->default(0);
            $table->decimal('leave_maternity_paternity', 8, 2)->default(0);
            $table->timestamps();
            $table->unique(['month_key', 'outlet_id', 'department']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
