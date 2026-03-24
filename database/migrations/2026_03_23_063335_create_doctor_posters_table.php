<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctor_posters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
//            $table->foreignId('doctor_id')->constrained('msl_doctor')->onDelete('cascade');

            $table->string('name')->nullable();
            $table->string('degree')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            $table->string('photo')->nullable();
            $table->string('banner_path')->nullable();
            $table->string('video_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_posters');
    }
};
