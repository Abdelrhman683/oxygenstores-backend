<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShowroomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing showrooms
        DB::table('showrooms')->delete();

        // The 19 showrooms from WordPress
        $showrooms = [
            [
                'city_name' => 'الرياض',
                'address' => 'الرياض / أشبيلية',
                'phone' => '0559301713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+أشبيلية',
                'branch_id' => 14
            ],
            [
                'city_name' => 'الرياض',
                'address' => 'الرياض / حراج بن قاسم',
                'phone' => '0531215141',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+حراج+بن+قاسم',
                'branch_id' => 14
            ],
            [
                'city_name' => 'الرياض',
                'address' => 'الرياض / حي العارض',
                'phone' => '0558601713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+حي+العارض',
                'branch_id' => 14
            ],
            [
                'city_name' => 'الدمام',
                'address' => 'الدمام / شارع الخزان',
                'phone' => '0551691713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+الدمام+شارع+الخزان',
                'branch_id' => 21
            ],
            [
                'city_name' => 'الأحساء',
                'address' => 'الأحساء',
                'phone' => '0551691713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+الأحساء',
                'branch_id' => 21
            ],
            [
                'city_name' => 'حفر الباطن',
                'address' => 'حفر الباطن',
                'phone' => '0556541713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+حفر+الباطن',
                'branch_id' => 21
            ],
            [
                'city_name' => 'جدة',
                'address' => 'جدة/حي الحمدانية',
                'phone' => '0551801713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+جدة+الحمدانية',
                'branch_id' => 22
            ],
            [
                'city_name' => 'جدة',
                'address' => 'جدة / سوق7',
                'phone' => '0536531713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+جدة+سوق7',
                'branch_id' => 22
            ],
            [
                'city_name' => 'القنفذة',
                'address' => 'القنفدة',
                'phone' => '0553891713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+القنفذة',
                'branch_id' => 22
            ],
            [
                'city_name' => 'المدينة المنورة',
                'address' => 'المدينة المنورة / شارع السيح',
                'phone' => '0551661713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+المدينة+شارع+السيح',
                'branch_id' => 20
            ],
            [
                'city_name' => 'المدينة المنورة',
                'address' => 'المدينة المنورة / العزيزية',
                'phone' => '0536341713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+المدينة+العزيزية',
                'branch_id' => 20
            ],
            [
                'city_name' => 'حائل',
                'address' => 'حائل',
                'phone' => '0552301713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+حائل',
                'branch_id' => 18
            ],
            [
                'city_name' => 'بريدة',
                'address' => 'بريدة',
                'phone' => '0550891713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+بريدة',
                'branch_id' => 19
            ],
            [
                'city_name' => 'الرس',
                'address' => 'الرس',
                'phone' => '0551541713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+الرس',
                'branch_id' => 19
            ],
            [
                'city_name' => 'الدوادمى',
                'address' => 'الدوادمي',
                'phone' => '0551911713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+الدوادمي',
                'branch_id' => 14
            ],
            [
                'city_name' => 'جازان',
                'address' => 'جيزان',
                'phone' => '0551231713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+جيزان',
                'branch_id' => 26
            ],
            [
                'city_name' => 'أبها',
                'address' => 'أبها / خميس مشيط',
                'phone' => '0551651713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+أبها',
                'branch_id' => 24
            ],
            [
                'city_name' => 'الخرج',
                'address' => 'الخرج',
                'phone' => '0536141713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+الخرج',
                'branch_id' => 14
            ],
            [
                'city_name' => 'المجمعة',
                'address' => 'المجمعه',
                'phone' => '0507721713',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=ذرة+أكسجين+المجمعة',
                'branch_id' => 14
            ],
        ];

        foreach ($showrooms as $showroom) {
            DB::table('showrooms')->insert([
                'city_name' => $showroom['city_name'],
                'address' => $showroom['address'],
                'phone' => $showroom['phone'],
                'maps_url' => $showroom['maps_url'],
                'branch_id' => $showroom['branch_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
