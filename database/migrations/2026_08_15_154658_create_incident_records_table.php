<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_records', function (Blueprint $table) {
            $table->id();
            $table->string('month_key');
            $table->string('outlet_id');
            $table->string('bu');
            $table->string('state');
            $table->string('severity');
            $table->decimal('count', 8, 2);
            $table->decimal('hours_worked', 12, 2);
            $table->decimal('days_lost', 8, 2)->default(0);
            $table->decimal('compliance_audit_score', 6, 2);
            $table->timestamps();
            $table->unique(['month_key', 'outlet_id', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_records');
    }
};
