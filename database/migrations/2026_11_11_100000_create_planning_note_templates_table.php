<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planning_note_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('color')->default('#facc15');
            $table->boolean('blocks_day')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planning_note_templates');
    }
};
