<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Edi_data;
use App\Http\Controllers\library\EdiReaderController;
use App\Models\Country;
use Illuminate\Support\Facades\Log;

class CommonMigrationController extends Controller {

    public function updateEdiData(Request $request) {
        /* if ($request->q == 'email') {
          $this->updateEmail($request);
          } */

        if ($request->q == 'country') {
            $this->updateCountry($request);
        }
    }

    public function updateCountry($request) {
        try {
            $country_code_list = array_flip((new Country())->getCountryCodeList());

            $edi_obj = new Edi_data();
            $edi_data_rows = $edi_obj->get();
            $updated_count = 0;
            if (!empty($edi_data_rows)) {
                foreach ($edi_data_rows as $row) {
                    $id = $row->id;
                    $reader = new EdiReaderController($row->data);
                    $country_code = $reader->getCountryCode('NAD+HI');
                    $country_id = !empty($country_code_list[$country_code]) ? $country_code_list[$country_code] : 0;

                    if ($country_code != "") {
                        if ($edi_obj->where('id', $id)->update(['country_id' => $country_id, 'country_code' => $country_code])) {
                            $updated_count++;
                        }
                    }
                }
            }

            echo "Total Updated country count: " . $updated_count;
        } catch (\Exception $e) {
            Log::info("SERVER_ERROR: updateCountry(): " . $e->getMessage() . ' Line NO:' . $e->getLine());
        }
    }

    public function updateEmail($request) {
        $edi_obj = new Edi_data();
        $edi_data_rows = $edi_obj->get();
        $updated_count = 0;
        if (!empty($edi_data_rows)) {
            foreach ($edi_data_rows as $row) {
                $id = $row->id;
                $reader = new EdiReaderController($row->data);
                $comm = $reader->getComValue('COM');

                if ($row->in_or_out == 1) {
                    $email_name = $comm['carrier']['email_name'];
                    $email = $comm['carrier']['email'];
                } else {
                    $email_name = $comm['owner']['email_name'];
                    $email = $comm['owner']['email'];
                }

                $data_sql = ['owner_email' => $email, 'owner_name' => $email_name];
                if ($edi_obj->where('id', $id)->update($data_sql)) {
                    $updated_count++;
                }
            }


            echo "Total Updated emails: " . $updated_count;
        }
    }

}
