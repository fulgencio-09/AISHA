<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()    {
       
        $user=User::create([          
            'estado'=>'1',
            'name'=>'Fulgencio Quintero Brito',
            'username'=>'admin',
            'especialidad'=>'admin',
            'email'=>'Faquintero@uniguajira.edu.co',
            'password'=>bcrypt('Fito0911*'),
        ]);
   
    }
}
