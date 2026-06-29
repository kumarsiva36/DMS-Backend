<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class inquiryMaster extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('inquiry_master')->truncate();
        $inquiryMaster = [
            [
                'type' => 'PaymentTerms',
                'content' =>'PIA: Payment in advance.',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'PaymentTerms',
                'content' =>'EOM: End of month.',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'PaymentTerms',
                'content' =>'CND: Cash next delivery.',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'PaymentTerms',
                'content' =>'COD: Cash on delivery.',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'PaymentTerms',
                'content' =>'CBS: Cash before shipment.',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'PaymentTerms',
                'content' =>'CIA: Cash in advance.',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'PaymentTerms',
                'content' =>'21 MFI: 21st of the month following invoice date.',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'Patterns',
                'content' =>'To be prepared by factory',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'Patterns',
                'content' =>'Will be supplied by client',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'CustomsDeclarationDocument',
                'content' =>'Invoice',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'CustomsDeclarationDocument',
                'content' =>'Packing List',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'CustomsDeclarationDocument',
                'content' =>'Bill of Lading',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'CustomsDeclarationDocument',
                'content' =>'Airway Bill',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'CustomsDeclarationDocument',
                'content' =>'Truck Receipt',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'CustomsDeclarationDocument',
                'content' =>'Certificate of Origin',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'PrintType',
                'content' =>'Pigment',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'PrintType',
                'content' =>'High density',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'PrintType',
                'content' =>'Sublimation',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'MainLabel',
                'content' =>'Printed on Satin',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'MainLabel',
                'content' =>'Normal Print',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'NoofPly',
                'content' =>'3',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'NoofPly',
                'content' =>'5',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'NoofPly',
                'content' =>'7',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'CartonEdgeFinish',
                'content' =>'Calico',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'CartonEdgeFinish',
                'content' =>'Gummed - No Calico',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'CartonEdgeFinish',
                'content' =>'Stapled',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'TestingCost',
                'content' =>'To be borne by the buyer',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'TestingCost',
                'content' =>'To be borne by the seller',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'TestingCost',
                'content' =>'1st test cost to be borne by the buyer, retesting costs to be borne by the factory',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'Forwarder',
                'content' =>'To be decided by the Supplier',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'TestingRequirement',
                'content' =>'Color Fastness to Washing',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'TestingRequirement',
                'content' =>'Color Fastness to Rubbing',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'TestingRequirement',
                'content' =>'Composition Test',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'TestingRequirement',
                'content' =>'Print Fastness',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'InspectionType',
                'content' =>'100% Inspection',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'InspectionType',
                'content' =>'Inline',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'InspectionType',
                'content' =>'Random',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'InspectionType',
                'content' =>'Random Final Inspection',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'InspectionCost',
                'content' =>'Born by customer',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'InspectionCost',
                'content' =>'First Inspection by Customer or Second Inspection Factory',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'InspectionCost',
                'content' =>'Born by Factory',
                'inq_reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        DB::table('inquiry_master')->insert($inquiryMaster);
    }
}
