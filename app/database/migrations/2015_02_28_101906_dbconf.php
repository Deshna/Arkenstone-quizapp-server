<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Dbconf extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('Env');
		Schema::drop('Response');
		Schema::drop('Questions');
		Schema::drop('Quiz');
		Schema::drop('Instructor');

		Schema::create('Instructor', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('email')->unique();
			$table->string('password', 200)->nullable();
			$table->string('name', 200)->nullable();
			$table->timestamps();
			$table->rememberToken();
		});
		Schema::create('Quiz', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('instructor')->unsigned();
			$table->foreign('instructor')->references('id')->on('Instructor')->onDelete('cascade');
			$table->string('course_code', 200)->nullable();
			$table->text('description')->nullable();
			$table->integer('skip_auth')->default(0);
			$table->timestamps();
		});
		Schema::create('Questions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('question_no')->nullable();
			$table->integer('quiz')->unsigned();
			$table->foreign('quiz')->references('id')->on('Quiz')->onDelete('cascade');
			$table->double('marks')->nullable();
			$table->text('question');
			$table->text('options')->nullable();
			$table->text('answer')->nullable();
			$table->enum('type', ['1','2','3','4','5','6']);
			$table->timestamps();
		});
		Schema::create('Response', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('quiz')->unsigned();
			$table->foreign('quiz')->references('id')->on('Quiz')->onDelete('cascade');
			$table->string('stuent_roll', 200);
			$table->string('stuent_name', 200)->nullable();
			$table->text('responses');
			$table->double('marks');
			$table->timestamps();
		});
		Schema::create('Env', function(Blueprint $table)
		{
			$table->string('key',200);
			$table->primary('key');
			$table->string('val',1000);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
