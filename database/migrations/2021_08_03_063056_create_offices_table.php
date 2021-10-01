<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id')->index()->nullable();
            $table->string('name');
            $table->string('short_name');
            $table->string('unit');

            $table->foreign('office_id')->references('id')->on('offices');
            $table->engine = 'InnoDB';
        });

        DB::statement('ALTER TABLE offices ADD FULLTEXT(name, short_name)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offices');
    }
}
