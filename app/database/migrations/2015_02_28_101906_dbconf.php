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
		Schema::dropIfExists('Env');
		Schema::dropIfExists('Response');
		Schema::dropIfExists('KeyStates');
		Schema::dropIfExists('Questions');
		Schema::dropIfExists('Quiz');
		Schema::dropIfExists('Instructor');
		Schema::dropIfExists('Logs');

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
			$table->text('keyset')->nullable();
			$table->text('key')->nullable();
			$table->integer('time')->default(0);
			$table->integer('skip_auth')->default(0);
			$table->timestamps();
		});
		Schema::create('KeyStates', function(Blueprint $table)
		{
			$table->string('id',200);
			$table->integer('quiz')->unsigned();
			$table->foreign('quiz')->references('id')->on('Quiz')->onDelete('cascade');
			$table->string('stduent_roll', 200);
			$table->string('stduent_name', 200);
			$table->integer('symbol_verify')->default(0);
			$table->integer('question_get')->default(0);
			$table->primary('id');
			$table->timestamps();
		});
		Schema::create('Questions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('question_no')->nullable();
			$table->integer('quiz')->unsigned();
			$table->foreign('quiz')->references('id')->on('Quiz')->onDelete('cascade');
			$table->double('marks')->nullable()->default(0.0);
			$table->text('question');
			$table->text('options')->nullable()->default("{}");
			$table->text('answer')->nullable();
			$table->enum('type', ['1','2','3','4','5','6']);
			$table->timestamps();
		});
		Schema::create('Response', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('quiz')->unsigned();
			$table->foreign('quiz')->references('id')->on('Quiz')->onDelete('cascade');
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
		Schema::create('Logs', function(Blueprint $table){
			$table->increments('id');
			$table->string('keystate',200);
			$table->foreign('keystate')->references('id')->on('KeyStates')->onDelete('cascade');
			$table->integer('quiz')->unsigned();
			$table->foreign('quiz')->references('id')->on('Quiz')->onDelete('cascade');
			$table->text('message');
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
		//
	}

}
