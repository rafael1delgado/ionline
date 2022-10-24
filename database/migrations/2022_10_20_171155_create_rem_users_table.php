<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rem_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')                
                ->constrained('users');            
            $table->unsignedInteger('establishment_id');
            $table->foreign('establishment_id')->references('id')->on('establishments');            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rem_users');
    }
}
