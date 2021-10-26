<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('creator_user_id');
            $table->unsignedBigInteger('assigned_user_id');
            $table->unsignedBigInteger('assigned_client_id');
            $table->string('status');
            $table->timestamp('deadline');
            $table->timestamps();

            $table->foreign('creator_user_id')->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('assigned_user_id')->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('assigned_client_id')->references('id')->on('clients')
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
        Schema::dropIfExists('projects');
    }
}
