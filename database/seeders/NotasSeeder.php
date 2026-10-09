<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materia;
class NotasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $materiasPorSemestre = [
            1 => [
                'Psicología General',
                'Metodología de la Educación a Distancia y Virtual',
                'Matemática Básica',
                'Inglés Básico',
                'Proceso Lectoescriturales',
                'Ayudas Educativas',
                'Didáctica y Pedagogía General',
                'Prácticas Pedagógicas e Investigativas',
                'Investigación y Lectura de Contexto'
            ],
            2 => [
                'Psicología del Desarrollo',
                'Didáctica de la Educación Física',
                'Didáctica de la Matemática',
                'Didáctica de la Lengua Castellana',
                'Didáctica de las Ciencias Sociales',
                'Didáctica de la Educación Artística',
                'Didáctica de las Ciencias Naturales',
                'Modelos Educativos Flexibles (MEF)',
                'Prácticas Pedagógicas e Investigativas'
            ],
            3 => [
                'Psicología Cognitiva',
                'Lúdica y Recreación',
                'Pensamiento Matemático',
                'Inglés Básico',
                'Competencia Educativa de la Lengua Castellana',
                'Didáctica Educación Inicial',
                'MEF Componente Comunitario (PPP)',
                'Prácticas Pedagógicas e Investigativas',
                'Investigación Educativa'
            ],
            4 => [
                'Psicología del Aprendizaje',
                'Legislación Escolar',
                'Inglés Técnico de la Pedagogía',
                'Uso Pedagógico de las TICs',
                'Sociología General',
                'Control y Participación Ciudadana',
                'MEF Componente Pedagógico y de Gestión Administrativa',
                'Prácticas Pedagógicas e Investigación Formativa',
                'Investigación Educativa'
            ],
            5 => [
                'Pedagogía Especial',
                'Pensamiento Científico y Ambiental',
                'Competencia Lectoescriturales del Inglés',
                'Uso Pedagógico de las TICs',
                'Etnoeducación y Legislación',
                'Tendencia Pedagógica Contemporánea',
                'MEF Construcción Curricular y PPP',
                'Prácticas Pedagógicas e Investigativas',
                'Investigación y Lectura de Contexto'
            ]
        ];

        foreach ($materiasPorSemestre as $semestre => $materias) {
            foreach ($materias as $materia) {
                Materia::create([
                    'semestre' => $semestre,
                    'materia' => $materia,                   
                    
                ]);
            }
        }
    }
}
