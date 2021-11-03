<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CountryDocument;

class CountryDocumentsSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $filePath = database_path('seeders/country_documents.csv');
        $csv = array_map('str_getcsv', file($filePath));

        $maxRecords = count($csv);

        if ($maxRecords > 0) {
            for ($i = 1; $i < $maxRecords; $i++) {
                if (!empty(trim($csv[$i][2]))) {
                    $data[] = array(
                        "country_id" => $csv[$i][0],
                        "country_code" => trim($csv[$i][1]),
                        "country_name" => trim($csv[$i][2]),
                        "documents" => trim($csv[$i][3]),
                        "status" => 1
                    );
                }
            }
        }

        CountryDocument::insert($data);
    }

}
