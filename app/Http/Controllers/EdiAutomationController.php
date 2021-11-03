<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarrierSetup;
use App\Http\Controllers\EdiController;

class EdiAutomationController extends Controller {

    public function ediFileDownload(Request $request) {
        try {
            $rows = CarrierSetup::select([
                        'id',
                        'carrier_scac'
                    ])
                    ->where('is_ftp', 1)
                    ->where('status', 1)
                    ->where('carrier_scac', '!=', '')
                    ->whereNull('deleted_at')
                    ->get();

            if (!empty($rows)) {
                $edi_obj = new EdiController();
                echo "<br/>-------------Start Download files from FTP--------------------<br/>";
                foreach ($rows as $row) {
                    $result = $edi_obj->processIncomingEdi($request, '', $row->id);
                    echo!empty($result['message']) ? $result['message'] : '';
                }
                echo "<br/>-------------End Download files from FTP----------------------<br/>";
            }
        } catch (\Exception $ex) {
            echo 'ERROR: ediFileDownload:' . $ex->getMessage();
        }
    }

    public function ediProcessANDCompareAutomation(Request $request) {
        try {
            $edi_obj = new EdiController();
            echo "<br/>-------------Start process of outgoing file-------------------<br/>";
            $result = $edi_obj->processEdi($request, 'outgoing');
            echo!empty($result['message']) ? $result['message'] : '';
            echo "<br/>-------------End process of outgoing file---------------------<br/>";

            echo "<br/>-------------Start process of incoming file-------------------<br/>";
            $result = $edi_obj->processEdi($request, 'incoming');
            echo!empty($result['message']) ? $result['message'] : '';
            echo "<br/>-------------End process of incoming file---------------------<br/>";

            echo "<br/>-------------Start Compare files------------------------------<br/>";
            $result = $edi_obj->CompareEdi($request);
            echo!empty($result['message']) ? $result['message'] : '';
            echo "<br/>-------------End Compare files--------------------------------<br/>";
        } catch (\Exception $ex) {
            echo 'ERROR: ediProcessANDCompareAutomation:' . $ex->getMessage();
        }
    }

    public function ediAutomation(Request $request) {
        $this->ediFileDownload($request);
        $this->ediProcessANDCompareAutomation($request);
    }

    public function ediOutgoingProcess(Request $request) {
        try {
            $edi_obj = new EdiController();
            echo "<br/>-------------Start process of outgoing file-------------------<br/>";
            $result = $edi_obj->processEdi($request, 'outgoing');
            echo!empty($result['message']) ? $result['message'] : '';
            echo "<br/>-------------End process of outgoing file---------------------<br/>";
        } catch (\Exception $ex) {
            echo 'ERROR: ediOutgoingProcess:' . $ex->getMessage();
        }
    }

    public function ediIncomingProcess(Request $request) {
        try {
            $edi_obj = new EdiController();
            echo "<br/>-------------Start process of incoming file-------------------<br/>";
            $result = $edi_obj->processEdi($request, 'incoming');
            echo!empty($result['message']) ? $result['message'] : '';
            echo "<br/>-------------End process of incoming file---------------------<br/>";
        } catch (\Exception $ex) {
            echo 'ERROR: ediIncomingProcess:' . $ex->getMessage();
        }
    }

    public function ediCompare(Request $request) {
        try {
            $edi_obj = new EdiController();
            echo "<br/>-------------Start Compare files------------------------------<br/>";
            $result = $edi_obj->CompareEdi($request);
            echo!empty($result['message']) ? $result['message'] : '';
            echo "<br/>-------------End Compare files--------------------------------<br/>";
        } catch (\Exception $ex) {
            echo 'ERROR: ediCompare:' . $ex->getMessage();
        }
    }

}
