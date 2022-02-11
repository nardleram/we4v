<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('status')->default(0);
            $table->timestamp('confirmed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreignUuid('requested_by')->references('id')->on('groups');
            $table->foreignUuid('requested_of')->references('id')->on('groups');
            $table->foreignUuid('network_id')->references('id')->on('networks');
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
        Schema::dropIfExists('affiliations');
    }
}
