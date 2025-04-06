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
        Schema::create('manga_teams', function (Blueprint $table) {
            $table->foreignId('manga_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('translation_teams')->cascadeOnDelete();
            $table->primary(['manga_id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manga_teams');
    }
};
