<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
}
