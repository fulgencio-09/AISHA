<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsignaturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asignaturas', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('estudiante_id')->nullable();
            $table->string('año')->nullable();
            $table->string('semestre')->nullable();
            $table->string('periodo')->nullable();
            $table->string('valor')->nullable();
            $table->string('total')->nullable();
            $table->string('mat_matr')->nullable();
            $table->string('sede')->nullable();
            $table->string('estado')->nullable();
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
        Schema::dropIfExists('asignaturas');
    }
}
