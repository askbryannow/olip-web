<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_performance_records', function (Blueprint $table) {
            $table->id();
            $table->string('month_key');
            $table->string('outlet_id');
            $table->string('bu');
            $table->string('state');
            $table->decimal('revenue', 14, 2);
            $table->decimal('visitors', 10, 2);
            $table->timestamps();
            $table->unique(['month_key', 'outlet_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_performance_records');
    }
};
