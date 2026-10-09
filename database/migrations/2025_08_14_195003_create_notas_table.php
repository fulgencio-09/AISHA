<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('asignatura_id')->nullable();
            $table->unsignedTinyInteger('semestre'); // 1 a 5
            $table->string('materia_id')->nullable();
            $table->string('materia')->nullable();
            $table->decimal('corte1', 5, 2)->nullable();
            $table->decimal('corte2', 5, 2)->nullable();
            $table->decimal('corte3', 5, 2)->nullable();
            $table->boolean('habilitada_recuperacion')->default(false);
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
        Schema::dropIfExists('notas');
    }
}
