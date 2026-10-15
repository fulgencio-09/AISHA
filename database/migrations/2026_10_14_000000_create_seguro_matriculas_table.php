<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguro_matriculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asignatura_id')
                ->unique()
                ->constrained('asignaturas')
                ->cascadeOnDelete();
            $table->decimal('valor', 12, 2);
            $table->boolean('estado')->default(false);
            $table->date('fecha_pago')->nullable();
            $table->string('forma_pago', 100)->nullable();
            $table->string('referencia', 150)->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguro_matriculas');
    }
};
