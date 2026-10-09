<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
     
     /*  $this->call(RolesSeeder::class);
        $this->call(PermissionSeeder::class);*/
        $this->call(Permisos::class);
        $this->call(UserSeeders::class);
        $this->call(NotasSeeder::class);
    }
}
