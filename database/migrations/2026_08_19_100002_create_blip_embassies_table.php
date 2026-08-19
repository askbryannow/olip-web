<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blip_embassies', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedTinyInteger('sequence');
            $table->string('name');
            $table->string('stamp_name');
            $table->string('theme');
            $table->text('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blip_embassies');
    }
};
