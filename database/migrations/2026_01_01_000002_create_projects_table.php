<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('client')->nullable();
            $table->string('industry')->nullable();
            $table->string('role')->nullable();
            $table->string('summary', 500);
            $table->text('challenge')->nullable();
            $table->text('solution')->nullable();
            $table->text('outcome')->nullable();
            $table->json('metrics')->nullable();
            $table->json('highlights')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('external_url')->nullable();
            $table->string('repository_url')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('duration')->nullable();
            $table->string('team_size')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->timestamps();

            $table->index(['is_published', 'is_featured']);
            $table->index('sort_order');
        });

        Schema::create('project_technology', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained()->cascadeOnDelete();

            $table->unique(['project_id', 'technology_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_technology');
        Schema::dropIfExists('projects');
    }
};
