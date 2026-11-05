<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->longText('face_descriptor')
                ->nullable()
                ->after('status');

            $table->timestamp('face_registered_at')
                ->nullable()
                ->after('face_descriptor');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'face_descriptor',
                'face_registered_at',
            ]);
        });
    }
};