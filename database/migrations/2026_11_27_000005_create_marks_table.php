<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('quarter');                    // 1 to 4
            $table->decimal('score', 5, 2);                            // 0.00 to 100.00
            $table->timestamps();
            $table->unique(['student_id', 'subject_id', 'quarter']);   // one mark per student, subject, quarter
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};
