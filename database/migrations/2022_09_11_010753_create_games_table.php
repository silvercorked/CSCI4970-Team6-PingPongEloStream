<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->boolean('first_server'); // false = team2, true = team1
            $table->unsignedBigInteger('team1_first_server_id');
            $table->foreign('team1_first_server_id')->references('id')->on('users');
            $table->unsignedBigInteger('team2_first_server_id');
            $table->foreign('team2_first_server_id')->references('id')->on('users');
            $table->unsignedBigInteger('mode_id');
            $table->foreign('mode_id')->references('id')->on('modes');
            $table->unsignedBigInteger('season_id');
            $table->foreign('season_id')->references('id')->on('seasons');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
};
