<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('engagement_records', function (Blueprint $table) {
            $table->id();
            $table->string('quarter_key');
            $table->string('month_key');
            $table->string('outlet_id');
            $table->string('bu');
            $table->string('state');
            $table->decimal('respondents', 8, 2);
            $table->decimal('headcount', 8, 2);
            $table->decimal('engagement_score', 6, 2);
            $table->decimal('e_nps', 6, 2);
            $table->decimal('grievance_compensation', 8, 2)->default(0);
            $table->decimal('grievance_conduct', 8, 2)->default(0);
            $table->decimal('grievance_workload', 8, 2)->default(0);
            $table->decimal('grievance_facilities', 8, 2)->default(0);
            $table->decimal('grievance_other', 8, 2)->default(0);
            $table->decimal('recognitions_given', 8, 2);
            $table->timestamps();
            $table->unique(['quarter_key', 'outlet_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engagement_records');
    }
};
