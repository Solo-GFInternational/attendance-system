<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->date('attendance_date');

            $table->enum('status', [
                'Present',
                'Absent',
                'Late',
                'Leave'
            ])->default('Present');

            $table->rawColumn('check_in', 'time(0)')->nullable();

            $table->rawColumn('check_out', 'time(0)')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique([
                'employee_id',
                'attendance_date'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};