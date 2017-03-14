<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersWebPushPlayers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_web_push_players', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->default(null)->unsigned();
            $table->string('player_id')->nullable();
            $table->boolean('reminder_type')->unsigned()->nullable();
            $table->string('reminder_first_at', 5)->default('11:00');
            $table->string('reminder_second_at', 5)->default('19:00');
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_web_push_players', function(Blueprint $table) {
            $table->dropForeign('users_web_push_players_user_id');
        });

        Schema::dropIfExists('users_web_push_players');
    }
}
