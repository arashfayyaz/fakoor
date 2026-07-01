<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quiz_package_quiz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_package_id')->constrained('quiz_packages')->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['quiz_package_id', 'quiz_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('quiz_package_quiz');
    }
};
