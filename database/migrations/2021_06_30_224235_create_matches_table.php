<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tournament_id')->unsigned();
            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('first_player_score');
            $table->integer('second_player_score');
            $table->enum('status', ['win', 'lose', 'draw']);
            $table->softDeletes();
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
        Schema::dropIfExists('matches');
    }
}
