<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $filePath = database_path('seeders/countries_new.csv');
        $file = fopen($filePath, "r");

        while (!feof($file)) {
            $csv[] = (fgetcsv($file));
        }

        fclose($file);

        $maxRecords = count($csv);

        if ($maxRecords > 0) {
            for ($i = 1; $i < $maxRecords; $i++) {
                $country_code = isset($csv[$i][0]) ? $csv[$i][0] : '';
                $country_name = isset($csv[$i][1]) ? $csv[$i][1] : '';
                if (!empty($country_code)) {
                    $data[] = array(
                        "country_code" => $country_code,
                        "country_name" => $country_name,
                    );
                }
            }
        }

        if (!empty($data)) {
            Country::insert($data);
        }
    }

}
