<?php

namespace App\Imports;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
class PacienteImport implements ToModel ,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row )
    {
        return new Paciente([
            'ips_id'=>$row['ips_id']??null,
            'eps_id'=>$row['eps_id']??null,
            'munin_id'=>$row['munin_id']??null,
            'depan_id'=>$row['depan_id']??null,
            'munir_id'=>$row['munir_id']??null,
            'depar_id'=>$row['depar_id']??null,
            'fechanacimiento'=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fechanacimiento'])?? null,
            'tipoi'=>$row['tipoi']?? null,
            'identidad'=>$row['identidad']?? null,
            'pn'=>$row['pn']?? null,
            'sn'=>$row['sn']?? null,
            'pa'=>$row['pa']?? null,
            'sa'=>$row['sa']?? null,            
            'sexo'=>$row['sexo']?? null,           
            'telefono'=>$row['telefono']?? null,
            'pais'=>$row['pais']?? null,       
            'area'=>$row['area']?? null,
            'grupo'=>$row['grupo']?? null,           
            'direccion'=>$row['direccion']?? null,
            'barrio'=>$row['barrio']?? null,
            'hora'=>$row['hora']?? null,
            'regimen'=>$row['regimen']?? null,
            'causas'=>$row['causas']?? null,
            'gestional'=>$row['gestional']?? null,
            'peso'=>$row['peso']?? null,
            'fechaparto'=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fechaparto'])?? null,
            'genero'=>$row['genero']?? null,
            'orientacion'=>$row['orientacion']?? null,
            'paisr'=>$row['paisr']?? null,
            'tident'=>$row['tident']?? null,
            'nidenmadre'=>$row['nidenmadre']?? null,            
            'pnombremama'=>$row['pnombremama']?? null,
            'snombremama'=>$row['snombremama']?? null,
            'papellidomama'=>$row['papellidomama']?? null,
            'sapellidomama'=>$row['sapellidomama']?? null,   
            'esquema'=>$row['esquema']?? null,        
            'condicion'=>$row['condicion']?? null,
            'conflicto'=>$row['conflicto']?? null,
            'desplazado'=>$row['desplazado']?? null,
            'discapacidad'=>$row['discapacidad']?? null,
            'carnet'=>$row['carnet']?? null,            
            'fallecido'=>$row['fallecido']?? null,
            'estudia'=>$row['estudia']?? null,                                
           
           
        ]);
    }
    public function chunkSize(): int
    {
        return 1000;
    }
}
