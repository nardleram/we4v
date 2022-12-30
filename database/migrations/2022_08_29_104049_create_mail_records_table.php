<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailRecordsTable extends Migration
{
    public function up() : void
    {
        Schema::create('mail_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('read')->default(false);
            $table->text('folder')->default('inbox');
            $table->boolean('forwarded')->default(false);
            $table->boolean('replied')->default(false);
            $table->timestamps();

            $table->foreignUuid('user_id')->references('id')->on('users');
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('mail_records');
    }
}
