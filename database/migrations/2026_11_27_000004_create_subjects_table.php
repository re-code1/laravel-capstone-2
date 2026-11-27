<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20);                   // e.g. MATH7
            $table->string('name');
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users');
            $table->timestamps();
            $table->unique(['code', 'section_id']);       // one MATH7 per section
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
