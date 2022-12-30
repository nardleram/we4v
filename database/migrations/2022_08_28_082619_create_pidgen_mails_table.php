<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePidgenMailsTable extends Migration
{
    public function up() : void
    {
        Schema::create('pidgen_mails', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('body');
            $table->text('subject');
            $table->timestamps();

            $table->foreignUuid('sender_user')->references('id')->on('users')->nullable();
            $table->foreignUuid('sender_team')->references('id')->on('teams')->nullable();
            $table->foreignUuid('sender_group')->references('id')->on('groups')->nullable();
            $table->foreignUuid('sender_network')->references('id')->on('networks')->nullable();
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('pidgen_mails');
    }
}
