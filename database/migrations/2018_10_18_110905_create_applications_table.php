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
        Schema::create('applications', function (Blueprint $table) {
            $table->string('appid')->unique();
            $table->string('name')->unique();
            $table->string('sha')->unique()->nullable();
            $table->string('icon')->nullable();
            $table->string('website')->nullable();
            $table->string('license')->nullable();
            $table->mediumText('description')->nullable();
            $table->boolean('enhanced')->default(false);
            $table->string('tile_background')->default('dark');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
