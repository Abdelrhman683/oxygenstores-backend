<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing data
        DB::table('cities')->delete();

        // City mappings extracted from WordPress postmeta order records
        $cities = [
            ['id' => 1, 'term_id' => 16, 'name' => 'حائل'],
            ['id' => 2, 'term_id' => 28, 'name' => 'بريدة'],
            ['id' => 3, 'term_id' => 29, 'name' => 'عنيزة'],
            ['id' => 4, 'term_id' => 30, 'name' => 'الرس'],
            ['id' => 5, 'term_id' => 41, 'name' => 'المدينة المنورة'],
            ['id' => 6, 'term_id' => 42, 'name' => 'ينبع'],
            ['id' => 7, 'term_id' => 49, 'name' => 'الرياض'],
            ['id' => 8, 'term_id' => 51, 'name' => 'المجمعة'],
            ['id' => 9, 'term_id' => 53, 'name' => 'الخرج'],
            ['id' => 10, 'term_id' => 54, 'name' => 'الدوادمى'],
            ['id' => 11, 'term_id' => 72, 'name' => 'الدمام'],
            ['id' => 12, 'term_id' => 73, 'name' => 'الأحساء'],
            ['id' => 13, 'term_id' => 74, 'name' => 'حفر الباطن'],
            ['id' => 14, 'term_id' => 75, 'name' => 'الجبيل'],
            ['id' => 15, 'term_id' => 84, 'name' => 'مكة المكرمة'],
            ['id' => 16, 'term_id' => 85, 'name' => 'جدة'],
            ['id' => 17, 'term_id' => 86, 'name' => 'الطائف'],
            ['id' => 18, 'term_id' => 89, 'name' => 'القنفذة'],
            ['id' => 19, 'term_id' => 101, 'name' => 'الباحة'],
            ['id' => 20, 'term_id' => 111, 'name' => 'أبها'],
            ['id' => 21, 'term_id' => 112, 'name' => 'خميس مشيط'],
            ['id' => 22, 'term_id' => 136, 'name' => 'جازان'],
        ];

        foreach ($cities as $city) {
            DB::table('cities')->insert([
                'id' => $city['id'],
                'term_id' => $city['term_id'],
                'name' => $city['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
