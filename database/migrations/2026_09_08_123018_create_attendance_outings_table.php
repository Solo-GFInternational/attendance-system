<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_outings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attendance_id')
                ->constrained('attendances')
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->noActionOnDelete();

            $table->rawColumn('out_time', 'time(0)');

            $table->rawColumn('return_time', 'time(0)')->nullable();

            $table->unsignedInteger('duration_minutes')->nullable();

            $table->string('reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_outings');
    }
};