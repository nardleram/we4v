<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up() : void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description');
            $table->date('start_date');
            $table->date('end_date');
            $table->uuid('taskable_id');
            $table->string('taskable_type');
            $table->softDeletes();
            $table->timestamps();

            $table->foreignUuid('owner')->references('id')->on('users');
            $table->foreignUuid('user_id')->references('id')->on('users')->nullable();
            $table->foreignUuid('project_id')->references('id')->on('projects');
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('tasks');
    }
}
