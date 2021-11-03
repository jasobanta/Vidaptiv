<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarrierSetup;
use Illuminate\Support\Facades\Crypt;

class CarrierSetupSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
//waws-prod-blu-243.ftp.azurewebsites.windows.net
        //PRvb_NrV2!

        $data = [
            [
                'carrier_name' => 'ACL',
                'carrier_scac' => 'ACLU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'ALIANCA',
                'carrier_scac' => 'ANRM',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'ANL',
                'carrier_scac' => 'ANNU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'APL',
                'carrier_scac' => 'APLU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'BERTSCHI',
                'carrier_scac' => 'BIDU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'BLUE ANCHOR LINE',
                'carrier_scac' => 'BANQ',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'BORCHARD',
                'carrier_scac' => 'BORU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'CAROTRANS',
                'carrier_scac' => 'CROI',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'CMA-CGM',
                'carrier_scac' => 'CMDU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'COSCO',
                'carrier_scac' => 'COSU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => 'waws-prod-blu-243.ftp.azurewebsites.windows.net',
                'ftp_userid' => 'bdp-ftp__cosco\cozentusexternal',
                'ftp_password' => Crypt::encryptString('PRvb_NrV2!'),
                'folder_type' => 'IN',
                'folder_location' => 'COSCO',
                'status' => 1,
                'is_ftp' => 1
            ], [
                'carrier_name' => 'DAL',
                'carrier_scac' => 'DAYU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ],
            [
                'carrier_name' => 'DIAMOND LINES',
                'carrier_scac' => 'COEU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ],
            [
                'carrier_name' => 'DICL',
                'carrier_scac' => 'VLSF',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ],
            [
                'carrier_name' => 'ECU WORLDWIDE',
                'carrier_scac' => 'ECUW',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'EVERGREEN',
                'carrier_scac' => 'EGLV',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'FAMOUS PACIFIC',
                'carrier_scac' => 'FFPV',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => 'waws-prod-blu-243.ftp.azurewebsites.windows.net',
                'ftp_userid' => 'bdp-ftp__famous\cozentusexternal',
                'ftp_password' => Crypt::encryptString('PRvb_NrV2!'),
                'folder_type' => 'IN',
                'folder_location' => 'FAMOUS',
                'status' => 1,
                'is_ftp' => 1
            ], [
                'carrier_name' => 'GRIMALDI',
                'carrier_scac' => 'GCNU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'HAMBURG SUD',
                'carrier_scac' => 'SUDU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'HAPAG LLOYD',
                'carrier_scac' => 'HLCU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => 'waws-prod-blu-243.ftp.azurewebsites.windows.net',
                'ftp_userid' => 'bdp-ftp__hclu\cozentusexternal',
                'ftp_password' => Crypt::encryptString('PRvb_NrV2!'),
                'folder_type' => 'IN',
                'folder_location' => '/site/wwwroot/HLCU',
                'status' => 1,
                'is_ftp' => 1
            ], [
                'carrier_name' => 'HMM',
                'carrier_scac' => 'HDMU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'ICL',
                'carrier_scac' => 'IILU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => 'waws-prod-blu-243.ftp.azurewebsites.windows.net',
                'ftp_userid' => 'bdp-ftp__icl\cozentusexternal',
                'ftp_password' => Crypt::encryptString('PRvb_NrV2!'),
                'folder_type' => 'IN',
                'folder_location' => 'ICL',
                'status' => 1,
                'is_ftp' => 1
            ], [
                'carrier_name' => 'LESCHACO',
                'carrier_scac' => 'LEHO',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'MAERSK',
                'carrier_scac' => 'MAEU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'MSC',
                'carrier_scac' => 'MSCU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'NILE DUTCH',
                'carrier_scac' => 'NIDU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'ONE',
                'carrier_scac' => 'ONEY',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => 'waws-prod-blu-243.ftp.azurewebsites.windows.net',
                'ftp_userid' => 'bdp-ftp__one\cozentusexternal',
                'ftp_password' => Crypt::encryptString('PRvb_NrV2!'),
                'folder_type' => 'IN',
                'folder_location' => '/site/wwwroot/ONE',
                'status' => 1,
                'is_ftp' => 1
            ], [
                'carrier_name' => 'OOCL',
                'carrier_scac' => 'OOLU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => 'waws-prod-blu-243.ftp.azurewebsites.windows.net',
                'ftp_userid' => 'bdp-ftp__oocl\cozentusexternal',
                'ftp_password' => Crypt::encryptString('PRvb_NrV2!'),
                'folder_type' => 'IN',
                'folder_location' => '/site/wwwroot/OOCL',
                'status' => 1,
                'is_ftp' => 1
            ], [
                'carrier_name' => 'PANTAINER',
                'carrier_scac' => 'PNEP',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'PIL',
                'carrier_scac' => 'PILU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'SAFMARINE',
                'carrier_scac' => 'SAFM',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'SEABOARD MARINE',
                'carrier_scac' => 'SMLU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'SEAGOLAND EUROPE',
                'carrier_scac' => 'SEJJ',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'SEALAND AMERICAS',
                'carrier_scac' => 'SEAU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'SEALAND ASIA',
                'carrier_scac' => 'MCCQ',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'STOLT',
                'carrier_scac' => 'STZW',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => 'waws-prod-blu-243.ftp.azurewebsites.windows.net',
                'ftp_userid' => 'bdp-ftp__stolt\cozentusexternal',
                'ftp_password' => Crypt::encryptString('PRvb_NrV2!'),
                'folder_type' => 'IN',
                'folder_location' => '/site/wwwroot/STOLT',
                'status' => 1,
                'is_ftp' => 1
            ], [
                'carrier_name' => 'SUTTONS',
                'carrier_scac' => 'SUTU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'TRANSADRIATICA',
                'carrier_scac' => 'TRDA',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'VANGUARD',
                'carrier_scac' => 'NAQA',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'WAN HAI',
                'carrier_scac' => 'WHLC',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'WEC LINE',
                'carrier_scac' => 'WECC',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'WORLDWIDE ALLIANCE',
                'carrier_scac' => 'WAIC',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'YANG MING',
                'carrier_scac' => 'YMLU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'ZIM',
                'carrier_scac' => 'ZIMU',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => '',
                'ftp_userid' => '',
                'ftp_password' => Crypt::encryptString(''),
                'folder_type' => 'IN',
                'folder_location' => '',
                'status' => 1,
                'is_ftp' => 0
            ], [
                'carrier_name' => 'ECULINE',
                'carrier_scac' => 'ECULINE',
                'carrier_email' => 'shailu@cozentus.com',
                'ftp_location' => 'waws-prod-blu-243.ftp.azurewebsites.windows.net',
                'ftp_userid' => 'bdp-ftp__eculine\cozentusexternal',
                'ftp_password' => Crypt::encryptString('PRvb_NrV2!'),
                'folder_type' => 'IN',
                'folder_location' => 'ECULINE',
                'status' => 1,
                'is_ftp' => 1
            ]
        ];
        CarrierSetup::insert($data);
    }

}
