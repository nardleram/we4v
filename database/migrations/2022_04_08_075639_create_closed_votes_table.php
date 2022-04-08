<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClosedVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('closed_votes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('voter_group_team');
            $table->string('votable_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->json('voters');
            $table->json('elements');
            $table->tinyInteger('turnout');
            $table->timestamps();

            $table->foreignUuid('vote_id')->references('id')->on('votes')->nullable();
            $table->foreignUuid('owner')->references('id')->on('users')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('closed_votes');
    }
}
