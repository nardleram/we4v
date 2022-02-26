<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetworksTable extends Migration
{
    public function up() : void
    {
        Schema::create('networks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->softDeletes();
            $table->timestamps();

            $table->foreignUuid('owner')->references('id')->on('users');
        });
    }
    
    public function down() : void
    {
        Schema::dropIfExists('networks');
    }
}
