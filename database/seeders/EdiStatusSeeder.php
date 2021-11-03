<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EdiTitle;

class EdiStatusSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $data = [
            [
                'title' => 'Waiting for Comparision',
                'status_code' => 0,
                'status' => 1
            ],
            [
                'title' => 'Completed comparision',
                'status_code' => 1,
                'status' => 1
            ],
            [
                'title' => 'Received',
                'status_code' => 2,
                'status' => 1
            ], [
                'title' => 'Pending comparison',
                'status_code' => 3,
                'status' => 1
            ],
            [
                'title' => 'Completed comparision',
                'status_code' => 4,
                'status' => 1
            ],
            [
                'title' => 'Sent to Meridian',
                'status_code' => 5,
                'status' => 1
            ],
            [
                'title' => 'Error reading file',
                'status_code' => 6,
                'status' => 1
            ],
        ];
        EdiTitle::insert($data);
    }

}
