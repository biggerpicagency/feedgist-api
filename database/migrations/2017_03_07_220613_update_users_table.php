<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('remind_me')->after('token')->unsigned()->nullable();
            $table->boolean('reminder_type')->after('remind_me')->unsigned()->nullable();
            $table->string('reminder_first_at', 5)->after('reminder_type')->default('11:00');
            $table->string('reminder_second_at', 5)->after('reminder_first_at')->default('19:00');
            $table->string('onesignal_player_id')->after('reminder_second_at')->nullable();
            $table->string('last_visit_first_post_date')->after('onesignal_player_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('reminder_type');
            $table->dropColumn('reminder_first_at');
            $table->dropColumn('reminder_second_at');
            $table->dropColumn('onesignal_player_id');
            $table->dropColumn('last_visit_first_post_date');
        });
    }
}
