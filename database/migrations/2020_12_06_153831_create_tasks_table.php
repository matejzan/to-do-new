<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('taskname');
            $table->string('taskdescription')->nullable();
            $table->string('taskduedate')->nullable();
            $table->integer('importance')->nullable();
            $table->string('taskcolor');
            $table->string('status');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->foreignId('user_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
