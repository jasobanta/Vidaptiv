<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RulesTitle;

class RulesStatusSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $data = [
            [
                'title' => 'Default rule',
                'status_code' => 1,
                'status' => 1
            ],
            [
                'title' => 'Carrier Specific',
                'status_code' => 2,
                'status' => 1
            ],
            [
                'title' => 'Text Rules',
                'status_code' => 3,
                'status' => 1
            ], [
                'title' => 'Skip Rules',
                'status_code' => 4,
                'status' => 1
            ],
            [
                'title' => 'Forward to AI',
                'status_code' => 5,
                'status' => 1
            ],
        ];
        RulesTitle::insert($data);
    }

}
