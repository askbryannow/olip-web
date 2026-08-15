<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitment_records', function (Blueprint $table) {
            $table->id();
            $table->string('month_key');
            $table->string('outlet_id');
            $table->string('bu');
            $table->string('department');
            $table->string('state');
            $table->decimal('applied', 8, 2);
            $table->decimal('screened', 8, 2);
            $table->decimal('interviewed', 8, 2);
            $table->decimal('offered', 8, 2);
            $table->decimal('joined', 8, 2);
            $table->decimal('open_vacancies', 8, 2);
            $table->decimal('avg_vacancy_age_days', 8, 2);
            $table->decimal('avg_time_to_hire_days', 8, 2);
            $table->decimal('cost_per_hire', 10, 2);
            $table->timestamps();
            $table->unique(['month_key', 'outlet_id', 'department']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_records');
    }
};
