<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('given_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('name_suffix')->nullable();
            $table->unsignedBigInteger('office_id')->index();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('office_id')->references('id')->on('offices');
            $table->engine = 'InnoDB';
        });

        DB::statement('ALTER users ADD FULLTEXT(given_name, middle_namee, last_name, name_suffix, username');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
