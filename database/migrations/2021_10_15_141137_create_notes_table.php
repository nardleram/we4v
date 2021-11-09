<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesTable extends Migration
{
    public function up() : void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('body');
            $table->uuid('noteable_id');
            $table->string('noteable_type');
            $table->softDeletes();
            $table->timestamps();

            $table->foreignUuid('user_id')->references('id')->on('users')->nullable();
        });
    }
    
    public function down() : void
    {
        Schema::dropIfExists('notes');
    }
}
