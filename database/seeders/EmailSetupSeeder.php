<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailSetup;

class EmailSetupSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $filePath = database_path('seeders/IFTMIN_EMAIL_TEMPLATES.csv');
        $csv = array_map('str_getcsv', file($filePath));

        $maxRecords = count($csv);

        if ($maxRecords > 0) {
            for ($i = 1; $i < $maxRecords; $i++) {
                $data[] = array(
                    "status" => $csv[$i][0],
                    "hide" => $csv[$i][1],
                    "type_id" => $csv[$i][2],
                    "template_title" => $csv[$i][3],
                    "subject" => $csv[$i][4],
                    "email_to" => $csv[$i][5],
                    "email_cc" => $csv[$i][6],
                    "email_bcc" => $csv[$i][7],
                    "message" => $csv[$i][8],
                );
            }
        }

        EmailSetup::insert($data);
    }

}
