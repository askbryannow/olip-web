<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gender');
            $table->string('bu');
            $table->string('department');
            $table->string('outlet_id');
            $table->string('state');
            $table->string('employment_type');
            $table->string('level');
            $table->date('join_date');
            $table->date('exit_date')->nullable();
            $table->string('exit_type')->nullable();
            $table->decimal('base_salary', 10, 2);
            $table->timestamps();
            $table->foreign('outlet_id')->references('id')->on('outlets');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
