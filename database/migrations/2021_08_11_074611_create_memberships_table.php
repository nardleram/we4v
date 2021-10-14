<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('role')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->boolean('confirmed')->default(false);
            $table->uuid('membershipable_id');
            $table->string('membershipable_type');
            $table->softDeletes();
            $table->timestamps();

            $table->foreignUuid('user_id')->references('id')->on('users')->nullable();
            $table->foreignUuid('group_id')->references('id')->on('groups')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('memberships');
    }
}
