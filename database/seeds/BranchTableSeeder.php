<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear tables first
        DB::table('branches')->delete();
        DB::table('branchmeta')->delete();

        // Seed branches
        $branches = [
            ['id' => 14, 'name' => 'فرع الرياض', 'vendor_id' => 64, 'delegate_id' => 0],
            ['id' => 18, 'name' => 'فرع حائل', 'vendor_id' => 64, 'delegate_id' => 0],
            ['id' => 19, 'name' => 'فرع القصيم', 'vendor_id' => 64, 'delegate_id' => 0],
            ['id' => 20, 'name' => 'فرع المدينة', 'vendor_id' => 64, 'delegate_id' => 0],
            ['id' => 21, 'name' => 'فرع المنطقة الشرقية', 'vendor_id' => 64, 'delegate_id' => 0],
            ['id' => 22, 'name' => 'فرع جدة', 'vendor_id' => 64, 'delegate_id' => 0],
            ['id' => 23, 'name' => 'فرع الباحة', 'vendor_id' => 64, 'delegate_id' => 0],
            ['id' => 24, 'name' => 'فرع ابها وخميس مشيط', 'vendor_id' => 64, 'delegate_id' => 0],
            ['id' => 26, 'name' => 'فرع جازان', 'vendor_id' => 64, 'delegate_id' => 0],
        ];

        foreach ($branches as $branch) {
            DB::table('branches')->insert([
                'id' => $branch['id'],
                'name' => $branch['name'],
                'vendor_id' => $branch['vendor_id'],
                'delegate_id' => $branch['delegate_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seed branchmeta
        $branchmeta = [
            ['meta_id' => 118, 'branch_id' => 14, 'meta_key' => 'branch_address_1', 'meta_value' => 'MMG3+FM - Umm Al Hamam Al Gharbi - Riyadh Principality - Riyadh Province - 12322'],
            ['meta_id' => 119, 'branch_id' => 14, 'meta_key' => 'branch_city', 'meta_value' => 'Riyadh'],
            ['meta_id' => 120, 'branch_id' => 14, 'meta_key' => 'branch_postcode', 'meta_value' => '12322'],
            ['meta_id' => 121, 'branch_id' => 14, 'meta_key' => 'branch_state', 'meta_value' => 'Riyadh Province'],
            ['meta_id' => 122, 'branch_id' => 14, 'meta_key' => 'branch_country', 'meta_value' => 'SA'],
            ['meta_id' => 123, 'branch_id' => 14, 'meta_key' => 'branch_address_lat', 'meta_value' => '24.676193'],
            ['meta_id' => 124, 'branch_id' => 14, 'meta_key' => 'branch_address_lng', 'meta_value' => '46.653144'],
            ['meta_id' => 125, 'branch_id' => 14, 'meta_key' => 'branch_address_place_id', 'meta_value' => 'ChIJ188XW74cLz4ROhcT8cREfZA'],
            ['meta_id' => 126, 'branch_id' => 14, 'meta_key' => 'branch_shipto', 'meta_value' => '{"49":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}},"53":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}},"54":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}},"51":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}}}'],

            ['meta_id' => 154, 'branch_id' => 18, 'meta_key' => 'branch_address_1', 'meta_value' => 'GP63+2H - Az Zibarah - Hail Principality - Hail Province - 55425'],
            ['meta_id' => 155, 'branch_id' => 18, 'meta_key' => 'branch_city', 'meta_value' => 'Hail'],
            ['meta_id' => 156, 'branch_id' => 18, 'meta_key' => 'branch_postcode', 'meta_value' => '55425'],
            ['meta_id' => 157, 'branch_id' => 18, 'meta_key' => 'branch_state', 'meta_value' => 'Hail Province'],
            ['meta_id' => 158, 'branch_id' => 18, 'meta_key' => 'branch_country', 'meta_value' => 'SA'],
            ['meta_id' => 159, 'branch_id' => 18, 'meta_key' => 'branch_address_lat', 'meta_value' => '27.509964'],
            ['meta_id' => 160, 'branch_id' => 18, 'meta_key' => 'branch_address_lng', 'meta_value' => '41.70401'],
            ['meta_id' => 161, 'branch_id' => 18, 'meta_key' => 'branch_address_place_id', 'meta_value' => 'ChIJaWlYrxlHdhURxMEY8XfF5D8'],
            ['meta_id' => 162, 'branch_id' => 18, 'meta_key' => 'branch_shipto', 'meta_value' => '{"16":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}}}'],

            ['meta_id' => 163, 'branch_id' => 19, 'meta_key' => 'branch_address_1', 'meta_value' => '9XM5+3R - حي الورود - Buraydah Principality - Al Qassim Province - 52385'],
            ['meta_id' => 164, 'branch_id' => 19, 'meta_key' => 'branch_city', 'meta_value' => 'Buraydah'],
            ['meta_id' => 165, 'branch_id' => 19, 'meta_key' => 'branch_postcode', 'meta_value' => '52385'],
            ['meta_id' => 166, 'branch_id' => 19, 'meta_key' => 'branch_state', 'meta_value' => 'Al Qassim Province'],
            ['meta_id' => 167, 'branch_id' => 19, 'meta_key' => 'branch_country', 'meta_value' => 'SA'],
            ['meta_id' => 168, 'branch_id' => 19, 'meta_key' => 'branch_address_lat', 'meta_value' => '26.382472'],
            ['meta_id' => 169, 'branch_id' => 19, 'meta_key' => 'branch_address_lng', 'meta_value' => '43.959876'],
            ['meta_id' => 170, 'branch_id' => 19, 'meta_key' => 'branch_address_place_id', 'meta_value' => 'ChIJeV4NJcFZfxUR7fiGDizdu-I'],
            ['meta_id' => 171, 'branch_id' => 19, 'meta_key' => 'branch_shipto', 'meta_value' => '{"28":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}},"30":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}},"29":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}}}'],

            ['meta_id' => 172, 'branch_id' => 20, 'meta_key' => 'branch_address_1', 'meta_value' => 'FJ82+9F - Al Suqya - Madinah Principality - Al Madinah Province - 42315'],
            ['meta_id' => 173, 'branch_id' => 20, 'meta_key' => 'branch_city', 'meta_value' => 'Madinah'],
            ['meta_id' => 174, 'branch_id' => 20, 'meta_key' => 'branch_postcode', 'meta_value' => '42315'],
            ['meta_id' => 175, 'branch_id' => 20, 'meta_key' => 'branch_state', 'meta_value' => 'Al Madinah Province'],
            ['meta_id' => 176, 'branch_id' => 20, 'meta_key' => 'branch_country', 'meta_value' => 'SA'],
            ['meta_id' => 177, 'branch_id' => 20, 'meta_key' => 'branch_address_lat', 'meta_value' => '24.466005'],
            ['meta_id' => 178, 'branch_id' => 20, 'meta_key' => 'branch_address_lng', 'meta_value' => '39.601189'],
            ['meta_id' => 179, 'branch_id' => 20, 'meta_key' => 'branch_address_place_id', 'meta_value' => 'ChIJFeq7FQC_vRURp4HnZFE8h4k'],
            ['meta_id' => 180, 'branch_id' => 20, 'meta_key' => 'branch_shipto', 'meta_value' => '{"41":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}},"42":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}}}'],

            ['meta_id' => 181, 'branch_id' => 21, 'meta_key' => 'branch_address_1', 'meta_value' => 'C39Q+RC - Al Itisalat - Dammam Principality - Eastern Province - 32257'],
            ['meta_id' => 182, 'branch_id' => 21, 'meta_key' => 'branch_city', 'meta_value' => 'Dammam'],
            ['meta_id' => 183, 'branch_id' => 21, 'meta_key' => 'branch_postcode', 'meta_value' => '32257'],
            ['meta_id' => 184, 'branch_id' => 21, 'meta_key' => 'branch_state', 'meta_value' => 'Eastern Province'],
            ['meta_id' => 185, 'branch_id' => 21, 'meta_key' => 'branch_country', 'meta_value' => 'SA'],
            ['meta_id' => 186, 'branch_id' => 21, 'meta_key' => 'branch_address_lat', 'meta_value' => '26.419538'],
            ['meta_id' => 187, 'branch_id' => 21, 'meta_key' => 'branch_address_lng', 'meta_value' => '50.088484'],
            ['meta_id' => 188, 'branch_id' => 21, 'meta_key' => 'branch_address_place_id', 'meta_value' => 'ChIJh5ojfAD9ST4RM8KwDbNDNRY'],
            ['meta_id' => 189, 'branch_id' => 21, 'meta_key' => 'branch_shipto', 'meta_value' => '{"72":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}},"73":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}},"74":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}},"75":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}}}'],

            ['meta_id' => 190, 'branch_id' => 22, 'meta_key' => 'branch_address_1', 'meta_value' => 'https://maps.app.goo.gl/ibaPhyFH7MqeiB1h7'],
            ['meta_id' => 191, 'branch_id' => 22, 'meta_key' => 'branch_city', 'meta_value' => 'Jeddah'],
            ['meta_id' => 192, 'branch_id' => 22, 'meta_key' => 'branch_postcode', 'meta_value' => '23743'],
            ['meta_id' => 193, 'branch_id' => 22, 'meta_key' => 'branch_state', 'meta_value' => 'Makkah Province'],
            ['meta_id' => 194, 'branch_id' => 22, 'meta_key' => 'branch_country', 'meta_value' => 'SA'],
            ['meta_id' => 195, 'branch_id' => 22, 'meta_key' => 'branch_address_lat', 'meta_value' => '21.750458'],
            ['meta_id' => 196, 'branch_id' => 22, 'meta_key' => 'branch_address_lng', 'meta_value' => '39.195258'],
            ['meta_id' => 197, 'branch_id' => 22, 'meta_key' => 'branch_address_place_id', 'meta_value' => 'ChIJK-j1dQB9wRURN2k0lmir4QU'],
            ['meta_id' => 198, 'branch_id' => 22, 'meta_key' => 'branch_shipto', 'meta_value' => '{"85":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}},"86":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}},"89":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}},"84":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}}}'],

            ['meta_id' => 199, 'branch_id' => 23, 'meta_key' => 'branch_address_1', 'meta_value' => '2F36+29 - حي الشفا - Bahah Principality - Al Bahah Province - 65524'],
            ['meta_id' => 200, 'branch_id' => 23, 'meta_key' => 'branch_city', 'meta_value' => 'Bahah'],
            ['meta_id' => 201, 'branch_id' => 23, 'meta_key' => 'branch_postcode', 'meta_value' => '65524'],
            ['meta_id' => 202, 'branch_id' => 23, 'meta_key' => 'branch_state', 'meta_value' => 'Al Bahah Province'],
            ['meta_id' => 203, 'branch_id' => 23, 'meta_key' => 'branch_country', 'meta_value' => 'SA'],
            ['meta_id' => 204, 'branch_id' => 23, 'meta_key' => 'branch_address_lat', 'meta_value' => '20.00249'],
            ['meta_id' => 205, 'branch_id' => 23, 'meta_key' => 'branch_address_lng', 'meta_value' => '41.461022'],
            ['meta_id' => 206, 'branch_id' => 23, 'meta_key' => 'branch_address_place_id', 'meta_value' => 'ChIJdY-Wg5hF7xURC_b33Z1FEKA'],
            ['meta_id' => 207, 'branch_id' => 23, 'meta_key' => 'branch_shipto', 'meta_value' => '{"101":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}}}'],

            ['meta_id' => 208, 'branch_id' => 24, 'meta_key' => 'branch_address_1', 'meta_value' => '6GG4+GH - Al Muftaha - Asir Principality - Aseer Province - 62521'],
            ['meta_id' => 209, 'branch_id' => 24, 'meta_key' => 'branch_city', 'meta_value' => 'Asir'],
            ['meta_id' => 210, 'branch_id' => 24, 'meta_key' => 'branch_postcode', 'meta_value' => '62521'],
            ['meta_id' => 211, 'branch_id' => 24, 'meta_key' => 'branch_state', 'meta_value' => 'Aseer Province'],
            ['meta_id' => 212, 'branch_id' => 24, 'meta_key' => 'branch_country', 'meta_value' => 'SA'],
            ['meta_id' => 213, 'branch_id' => 24, 'meta_key' => 'branch_address_lat', 'meta_value' => '18.226265'],
            ['meta_id' => 214, 'branch_id' => 24, 'meta_key' => 'branch_address_lng', 'meta_value' => '42.506353'],
            ['meta_id' => 215, 'branch_id' => 24, 'meta_key' => 'branch_address_place_id', 'meta_value' => 'ChIJFRTW0GZU4xUR79uCQbn0EMc'],
            ['meta_id' => 216, 'branch_id' => 24, 'meta_key' => 'branch_shipto', 'meta_value' => '{"111":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}},"112":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}}}'],

            ['meta_id' => 226, 'branch_id' => 26, 'meta_key' => 'branch_address_1', 'meta_value' => 'VHPG+57 - Al Safa - Jazan Principality - Jazan Province - 82721'],
            ['meta_id' => 227, 'branch_id' => 26, 'meta_key' => 'branch_city', 'meta_value' => 'Jazan'],
            ['meta_id' => 228, 'branch_id' => 26, 'meta_key' => 'branch_postcode', 'meta_value' => '82721'],
            ['meta_id' => 229, 'branch_id' => 26, 'meta_key' => 'branch_state', 'meta_value' => 'Jazan Province'],
            ['meta_id' => 230, 'branch_id' => 26, 'meta_key' => 'branch_country', 'meta_value' => 'SA'],
            ['meta_id' => 231, 'branch_id' => 26, 'meta_key' => 'branch_address_lat', 'meta_value' => '16.885588'],
            ['meta_id' => 232, 'branch_id' => 26, 'meta_key' => 'branch_address_lng', 'meta_value' => '42.575755'],
            ['meta_id' => 233, 'branch_id' => 26, 'meta_key' => 'branch_address_place_id', 'meta_value' => 'ChIJqbJWS5vjBxYR0CjLjpbP75w'],
            ['meta_id' => 234, 'branch_id' => 26, 'meta_key' => 'branch_shipto', 'meta_value' => '{"136":{"small":{"price":0,"time":7},"medium":{"price":0,"time":7},"large":{"price":0,"time":7}}}']
        ];

        foreach ($branchmeta as $meta) {
            DB::table('branchmeta')->insert([
                'meta_id' => $meta['meta_id'],
                'branch_id' => $meta['branch_id'],
                'meta_key' => $meta['meta_key'],
                'meta_value' => $meta['meta_value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
