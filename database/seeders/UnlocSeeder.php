<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unloc;

class UnlocSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        
        $filePath = database_path('seeders/UNLOC.csv');
        $csv = array_map('str_getcsv', file($filePath));

        $maxRecords = count($csv);

        if ($maxRecords > 0) {
            for ($i = 1; $i < $maxRecords; $i++) {
                Unloc::insert([
                    "lo_code" => $csv[$i][0],
                    "country" => $csv[$i][1],
                    "code" => $csv[$i][2],
                    "country_name" => $csv[$i][3],
                    "rules" => $csv[$i][4],
                ]);
            }
        }
        
    }

}
