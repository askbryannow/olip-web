<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overtime_records', function (Blueprint $table) {
            $table->id();
            $table->string('month_key');
            $table->string('outlet_id');
            $table->string('bu');
            $table->string('department');
            $table->string('state');
            $table->decimal('overtime_hours', 10, 2);
            $table->decimal('overtime_pay', 12, 2);
            $table->decimal('budget_hours', 10, 2);
            $table->timestamps();
            $table->unique(['month_key', 'outlet_id', 'department']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overtime_records');
    }
};
