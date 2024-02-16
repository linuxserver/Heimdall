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
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->default(0);
            $table->string('key');
            $table->string('type')->default('text');
            $table->text('options')->nullable();
            $table->string('label');
            $table->string('value')->nullable();
            $table->string('order')->default(0);
            $table->boolean('system')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
