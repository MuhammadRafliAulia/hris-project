<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Surveys
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->string('token', 64)->unique(); // shareable link token
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->boolean('is_anonymous')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Survey Questions
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['scale', 'multiple_choice', 'text']); // scale=1-5, multiple_choice, text
            $table->text('question');
            $table->json('options')->nullable(); // for multiple_choice: ["option1","option2",...]
            $table->boolean('is_required')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Survey Responses (one per respondent per survey)
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->string('respondent_name')->nullable(); // if not anonymous
            $table->string('respondent_department')->nullable();
            $table->string('respondent_nik')->nullable();
            $table->timestamps();
        });

        // Survey Answers (one per question per response)
        Schema::create('survey_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_response_id')->constrained()->cascadeOnDelete();
            $table->foreignId('survey_question_id')->constrained()->cascadeOnDelete();
            $table->integer('scale_value')->nullable();       // for scale type (1-5)
            $table->string('choice_value')->nullable();       // for multiple_choice
            $table->text('text_value')->nullable();            // for text type
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_answers');
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('surveys');
    }
};
