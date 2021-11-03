<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Rule;

class RulesSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        //
        $data_rules = [
            [
                "rules" => "TSR",
                "name" => "Tariff service code (27 DD/ 28 DP/ 29 PD/ 30 PP) + 2 (FCL) / 3 (LCL)",
                "rule_type_id" => 1,
                "priority" => 1,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "FTX+BLC",
                "name" => "BL Clause text",
                "rule_type_id" => 1,
                "priority" => 2,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "CNT+7",
                "name" => "Consignment Total",
                "rule_type_id" => 1,
                "priority" => 3,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "RFF+BN",
                "name" => "Booking Number",
                "rule_type_id" => 1,
                "priority" => 4,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "RFF+SI",
                "name" => "Shipper's reference number",
                "rule_type_id" => 1,
                "priority" => 5,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "RFF+ERN",
                "name" => "Exporter's reference number",
                "rule_type_id" => 1,
                "priority" => 6,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "RFF+FF",
                "name" => "BDP reference number",
                "rule_type_id" => 1,
                "priority" => 7,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "RFF+CT",
                "name" => "Contract number",
                "rule_type_id" => 1,
                "priority" => 8,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "CPI+4",
                "name" => "Freight prepaid (CPI+4+P) / Collect (CPI+4+C) 4= Basic freight",
                "rule_type_id" => 1,
                "priority" => 9,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "TDT+20",
                "name" => "Main carriage leg Vessel name and Voyage number",
                "rule_type_id" => 1,
                "priority" => 10,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "LOC+88",
                "name" => "Place of Receipt",
                "rule_type_id" => 1,
                "priority" => 11,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "LOC+9",
                "name" => "Port of load",
                "rule_type_id" => 1,
                "priority" => 12,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "LOC+11",
                "name" => "Port of Discharge",
                "rule_type_id" => 1,
                "priority" => 13,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "LOC+7",
                "name" => "Place of Delivery",
                "rule_type_id" => 1,
                "priority" => 14,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "NAD+CA",
                "name" => "Scac code",
                "rule_type_id" => 1,
                "priority" => 15,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "DOC+710",
                "name" => "Document Code",
                "rule_type_id" => 1,
                "priority" => 16,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "NAD+CZ",
                "name" => "Shipper",
                "rule_type_id" => 1,
                "priority" => 17,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "NAD+CN",
                "name" => "Consignee",
                "rule_type_id" => 1,
                "priority" => 18,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "NAD+NI",
                "name" => "Notify",
                "rule_type_id" => 1,
                "priority" => 19,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "NAD+N1",
                "name" => "Also Notify",
                "rule_type_id" => 1,
                "priority" => 20,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "NAD+FW",
                "name" => "Freight forwarder",
                "rule_type_id" => 1,
                "priority" => 21,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "GID",
                "name" => "Package type and number of packages",
                "rule_type_id" => 1,
                "priority" => 22,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "PIA",
                "name" => "Harmonized code (HS code)",
                "rule_type_id" => 1,
                "priority" => 23,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "FTX+AAA",
                "name" => "Goods description",
                "rule_type_id" => 1,
                "priority" => 24,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "PCI",
                "name" => "Marks and Numbers",
                "rule_type_id" => 1,
                "priority" => 25,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "SGP",
                "name" => "Containernumber",
                "rule_type_id" => 1,
                "priority" => 26,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "MEA+AAE+AAW",
                "name" => "Volume",
                "rule_type_id" => 1,
                "priority" => 27,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "DGS+IMD",
                "name" => "Hazardous info DGS+IMD+3.2 (class)=>456+4056 (Unnr)+098 (flashpoint)=>FAH (cel/fah)+2 (packing group)+EMSNBR (ems)",
                "rule_type_id" => 1,
                "priority" => 28,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "FTX+AAD+PSN",
                "name" => "Proper Shipping Name",
                "rule_type_id" => 1,
                "priority" => 29,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "FTX+AAD+TN",
                "name" => "Technical name",
                "rule_type_id" => 1,
                "priority" => 30,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "CTA+HE",
                "name" => "Emergency contact name",
                "rule_type_id" => 1,
                "priority" => 31,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "COM",
                "name" => "Contact Number",
                "rule_type_id" => 1,
                "priority" => 32,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "EQD+CN",
                "name" => "Containernumber + containertype",
                "rule_type_id" => 1,
                "priority" => 33,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "MEA+AAE+WT",
                "name" => "Total Gross weight",
                "rule_type_id" => 1,
                "priority" => 34,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "MEA+AAU",
                "name" => "Total number of packages",
                "rule_type_id" => 1,
                "priority" => 35,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
            [
                "rules" => "SEL",
                "name" => "Seal number",
                "rule_type_id" => 1,
                "priority" => 36,
                "rule_fields" => "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15"
            ],
        ];

        foreach ($data_rules as $rules) {
            DB::table('rules')->insert($rules);
        }
    }

}
