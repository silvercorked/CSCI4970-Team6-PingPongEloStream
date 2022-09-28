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
        Schema::create('modes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('win_score');
            $table->unsignedInteger('set_count');
            $table->unsignedInteger('serve_switch');
            $table->enum('tie_serve_switch_override', ['low-score', 'by-ones']);
            // 'low-score' => 'player with low score (or if tie, player who didn't last score)'
            // 'by-ones' => 'players operate under serve_switch = 1 rules'
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
        Schema::dropIfExists('modes');
    }
};
