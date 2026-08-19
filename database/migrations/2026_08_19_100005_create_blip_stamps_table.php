<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blip_stamps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('blip_enrollments');
            $table->string('embassy_id')->constrained('blip_embassies');
            $table->string('status')->default('scheduled');
            $table->date('visit_date')->nullable();
            $table->timestamp('earned_at')->nullable();
            $table->string('awarded_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['enrollment_id', 'embassy_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blip_stamps');
    }
};
