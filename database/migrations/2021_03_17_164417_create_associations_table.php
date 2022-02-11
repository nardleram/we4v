<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssociationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('associations', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(0);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            
            $table->foreignUuid('requested_of')->references('id')->on('users');
            $table->foreignUuid('requested_by')->references('id')->on('users');
            $table->unique(['requested_by', 'requested_of']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('associations');
    }
}
