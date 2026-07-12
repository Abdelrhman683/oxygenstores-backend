<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
         require_once __DIR__ . '/CitiesTableSeeder.php';
         require_once __DIR__ . '/ShowroomsTableSeeder.php';
         $this->call([
             AdminRoleTable::class,
             AdminTable::class,
             SellerTableSeeder::class,
             BranchTableSeeder::class,
             CitiesTableSeeder::class,
             ShowroomsTableSeeder::class,
         ]);
    }
}
