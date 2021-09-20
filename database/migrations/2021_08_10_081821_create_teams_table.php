<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    public function up() : void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('function')->nullable();
            $table->timestamps();

            $table->foreignUuid('owner')->references('id')->on('users');
            $table->foreignUuid('group_id')->references('id')->on('groups');
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('teams');
    }
}
