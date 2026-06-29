<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ trans('WebSite.inquiry_labels') }}</title>
    <style type="text/css">
        @font-face {
            font-family: 'poppins';
            src: url({{ storage_path('fonts/Poppins-Regular.ttf') }}) format("truetype");
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'poppins-semibold';
            src: url({{ storage_path('fonts/Poppins-SemiBold.ttf') }}) format("truetype");
            font-weight: 600;
            font-style: semibold;
        }
        @font-face {
            font-family: 'arialuni';
            src: url({{ storage_path('fonts/arial_unicode.ttf') }}) format("truetype");
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'arialuni';
            src: url({{ storage_path('fonts/arial_unicode.ttf') }}) format("truetype");
            font-weight: 600;
            font-style: semibold;
        }
        body {font-family: 'Poppins';}
        .mainTable table {border: 1px solid #a5a2a2;border-collapse: collapse;}
        .mainTable td {border: 1px solid #a5a2a2;border-collapse: collapse;}
        .mainTable th {border: 1px solid #a5a2a2;border-collapse: collapse;}
        .page-break {page-break-after: always;}
        .tableType td p, table td p, table td li,table td a {word-break: break-word !important;padding-right: 15px;}
        .tableType {border-collapse: collapse;}
        table td {word-wrap:break-word !important; }
        table td img{display: block; margin:10px 5px 5px 5px }
        @page { margin-top: 100px;margin-bottom: 60px; }
        #header { position: fixed; left: 0px; top: -70px; right: 0px;text-align: center; }
        #footer { position: fixed; left: 0px; bottom: -50px; right: 0px;text-align: center; }
        .header_footer table {border: 1px solid #e0e0e0;border-collapse: collapse;}
        .mainTable td {border: 1px solid #e0e0e0;border-collapse: collapse;}
        .mainTable th {border: 1px solid #e0e0e0;border-collapse: collapse;}
    </style>

</head>
<?php
$vendors =  $data['vendors'];
function fnVendor($id,$vendors){
    $new = array_filter($vendors, function ($var) use($id) {
        if ($var['id'] == $id){
            echo $var['vendor_name'];
        }
    });
}
$ms_sheet_avil=0;
?>
@foreach ($data['responses'] as $response)
    @if ($response['media_type'] == 'MeasurementSheet')
        @if((stristr($response['orginalfilename'],'.pdf')) && $ms_sheet_avil==0)
            @php $ms_sheet_avil =1; @endphp
        @endif
    @endif
    @if ($response['media_type'] == 'TechPack')
        @if((stristr($response['orginalfilename'],'.pdf')) && $ms_sheet_avil==0)
            @php $ms_sheet_avil =1; @endphp
        @endif
    @endif
@endforeach
<body style="font-family: poppins,arialuni; font-size: 14px;">
    <div id="header">
        <table width="100%"  style="border-collapse: collapse;font-family: poppins,arialuni; font-size:12px;" cellspacing="1px" class="mainTable header_footer" >
            <tr>
                <td width="12%" >
                    @if($data['useLogo']==1 && $data['userLogo']!='')
                        <img src="{{ $data['serverURL'].$data['userLogo'] }}"
                        style="background-color: #FFFFFF; height: 40px;" />
                    @else
                        <img src="{{ public_path() . "/images/dms-log-with-tag.png" }}"
                        style="background-color: #FFFFFF; height: 40px;" />
                    @endif
                </td>
                <td width="8%" style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.Order') }}</strong>
                </td>
                <td width="8%" style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ $data['responses'][0]['order_id'] }}
                </td>
                <td><p style="font-family: poppins,arialuni,notosansjp,poppins-semibold;font-weight:800; font-size:16px; text-align:center">
                    <strong>{{ trans('WebSite.bill_of_materials') }}</strong></p></td>
                <td width="8%" style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.date') }}</strong>
                </td>
                <td width="9%" style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ date($data['dateFormat'],strtotime($data['responses'][0]['updated_at'])) }}
                </td>
                @if($data['useLogo']==1 && $data['userLogo']!='')
                <td width="7%" >
                    <img src="{{ public_path() . "/images/dms_small.png" }}"
                        style="background-color: #FFFFFF; height: 40px;" />
                </td>
                @endif

            </tr>
        </table>
    </div>
    {{-- <div id="footer">
        <table width="96%"  style="border-collapse: collapse;font-family: poppins,arialuni; font-size:12px;" cellspacing="1px" class="mainTable header_footer" >
            <tr>
                <td width="7%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.created_by') }}</strong>
                </td>
                <td style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    <?php
                        //echo ($data['user_info']['user_name']!="") ?  $data['user_info']['user_name'] : $data['user_info']['staff_name'];
                    ?>
                </td>
                <td width="8%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.created_on') }}</strong>
                </td>
                <td width="8.5%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                     {{ date($data['dateFormat'],strtotime($data['user_info']['date_created'])) }}
                </td>
                <td width="7.5%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.modified_by') }}</strong>
                </td>
                <td style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ $modified_user }}
                </td>
                <td width="8%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.modified_on') }}</strong>
                </td>
                <td width="8.5%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ date($data['dateFormat'],strtotime($modified_date)) }}
                </td>
                <td width="7%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.last_issue') }}</strong>
                </td>
                <td width="8.5%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ date($data['dateFormat'],strtotime($modified_date)) }}
                </td>
            </tr>

        </table>
    </div> --}}

    <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;" cellspacing="1px" class="mainTable">
            <tr style="background-color: #f0efef; color: #000000; font-weight:800; font-size:15px">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" colspan="3"><strong>{{ trans('WebSite.prinrt_info') }}</strong></td>
            </tr>
            <tbody style="page-break-inside: avoid;">
                <tr>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="15%"  >
                        <strong>{{ trans('WebSite.print_image') }}</strong></td>
                    <td width="15%">
                        @foreach ($data['responses'] as $response)
                            @if ($response['media_type'] == 'PrintArtwork')
                                <img src="{{ $data['serverURL'].$response['filepath'] }}" title="PrintImage" width="100px"><br/>
                            @endif
                        @endforeach
                    </td>
                    <td style="padding : 5px 5px 1px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="70%">
                        <strong>Print Type</strong> : {!! $data['responses'][0]['print_type'] !!} <br/>
                        <strong>Print Size</strong> : {!! $data['responses'][0]['print_size'] !!} <br/>
                        <strong>No of Colors</strong> : {!! $data['responses'][0]['print_no_colors'] !!} <br/>
                        <strong>Vendor</strong> : {{ fnVendor( $data['responses'][0]['print_vendor_id'],$vendors) }} <br/>
                    </td>
                </tr>
            </tbody>
            <tr style="background-color: #f0efef; color: #000000; font-weight:800; font-size:15px">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" colspan="3"><strong>{{ trans('WebSite.trims_info') }}</strong></td>
            </tr>
            <tbody style="page-break-inside: avoid;">
                <tr>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="15%"  >
                        <strong>{{ trans('WebSite.main_lable_info') }}</strong></td>
                    <td width="15%">
                        @foreach ($data['responses'] as $response)
                            @if ($response['media_type'] == 'MainLabel')
                                <img src="{{ $data['serverURL'].$response['filepath'] }}" title="MainLabel" width="100px"><br/>
                            @endif
                        @endforeach
                    </td>
                    <td style="padding : 5px 5px 1px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="70%">
                        <strong>Main label</strong> : {!! $data['responses'][0]['main_label'] !!} <br/>
                        <strong>Vendor</strong> : {{ fnVendor( $data['responses'][0]['main_label_vendor_id'],$vendors) }} <br/>
                    </td>
                </tr>
            </tbody>
            <tbody style="page-break-inside: avoid;">
                <tr>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="15%"  >
                        <strong>{{ trans('WebSite.washcare_lable_info') }}</strong></td>
                    <td width="15%">
                        @foreach ($data['responses'] as $response)
                            @if ($response['media_type'] == 'WashCareLabel')
                                <img src="{{ $data['serverURL'].$response['filepath'] }}" title="WashCareLabel" width="100px"><br/>
                            @endif
                        @endforeach
                    </td>
                    <td style="padding : 5px 5px 1px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="70%">
                        <strong>WashCare Label</strong> : {!! $data['responses'][0]['washcare_label'] !!} <br/>
                        <strong>Vendor</strong> : {{ fnVendor( $data['responses'][0]['washcare_label_vendor_id'],$vendors) }} <br/>
                    </td>
                </tr>
            </tbody>
            <tbody style="page-break-inside: avoid;">
                <tr>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="15%"  >
                        <strong>{{ trans('WebSite.hangtag_info') }}</strong></td>
                    <td width="15%">
                        @foreach ($data['responses'] as $response)
                            @if ($response['media_type'] == 'Hangtag')
                                <img src="{{ $data['serverURL'].$response['filepath'] }}" title="Hangtag" width="100px"><br/>
                            @endif
                        @endforeach
                    </td>
                    <td style="padding : 5px 5px 1px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="70%">
                        <strong>Hangtag</strong> : {!! $data['responses'][0]['hangtag'] !!} <br/>
                        <strong>Vendor</strong> : {{ fnVendor( $data['responses'][0]['hangtag_vendor_id'],$vendors) }} <br/>
                    </td>
                </tr>
            </tbody>
            <tbody style="page-break-inside: avoid;">
                <tr>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="15%"  >
                        <strong>{{ trans('WebSite.barcode_stickers_info') }}</strong></td>
                    <td width="15%">
                        @foreach ($data['responses'] as $response)
                            @if ($response['media_type'] == 'BarcodeStickers')
                                <img src="{{ $data['serverURL'].$response['filepath'] }}" title="BarcodeStickers" width="100px"><br/>
                            @endif
                        @endforeach
                    </td>
                    <td style="padding : 5px 5px 1px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="70%">
                        <strong>Barcode Stickers</strong> : {!! $data['responses'][0]['barcode_label'] !!} <br/>
                        <strong>Vendor</strong> : {{ fnVendor( $data['responses'][0]['barcode_label_vendor_id'],$vendors) }} <br/>
                        <strong>Trims Notifications</strong> : {!! $data['responses'][0]['trims_notifications'] !!} <br/>
                    </td>
                </tr>
            </tbody>
            <tr style="background-color: #f0efef; color: #000000; font-weight:800; font-size:15px;">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" colspan="3"><strong>{{ trans('WebSite.packing_info') }}</strong></td>
            </tr>
            <tbody style="page-break-inside: avoid;">
                <tr>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="15%"  >
                        <strong>{{ trans('WebSite.polybag_sample') }}</strong></td>
                    <td width="15%">
                        @foreach ($data['responses'] as $response)
                            @if ($response['media_type'] == 'Polybag')
                                <img src="{{ $data['serverURL'].$response['filepath'] }}" title="Polybag" width="100px"><br/>
                            @endif
                        @endforeach
                    </td>
                    <td style="padding : 5px 5px 1px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="70%">
                        <strong>Polybag Size & thickness</strong> : {!! $data['responses'][0]['polybag_size_thickness'] !!} <br/>
                        <strong>Polybag Material</strong> : {!! $data['responses'][0]['polybag_material'] !!} <br/>
                        <strong>Print Details on polybag</strong> : {!! $data['responses'][0]['polybag_print_details'] !!} <br/>
                        <strong>Vendor</strong> : {{ fnVendor( $data['responses'][0]['polybag_vendor_id'],$vendors) }} <br/>
                    </td>
                </tr>
            </tbody>
            <tbody style="page-break-inside: avoid;">
                <tr>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="15%"  >
                        <strong>{{ trans('WebSite.carton_details') }}</strong></td>
                    <td width="15%">
                        @foreach ($data['responses'] as $response)
                            @if ($response['media_type'] == 'Carton')
                                <img src="{{ $data['serverURL'].$response['filepath'] }}" title="Carton" width="100px"><br/>
                            @endif
                        @endforeach
                    </td>
                    <td style="padding : 5px 5px 1px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="70%">
                        <strong>Carton Box Dimensions</strong> : {!! $data['responses'][0]['carton_dimensions'] !!} <br/>
                        <strong>Carton Color</strong> : {!! $data['responses'][0]['carton_color'] !!} <br/>
                        <strong>No Of Ply</strong> : {!! $data['responses'][0]['carton_no_of_ply'] !!} <br/>
                        <strong>Carton Edge Finish</strong> : {!! $data['responses'][0]['carton_edge_finish'] !!} <br/>
                        <strong>Carton Mark Details</strong> : {!! $data['responses'][0]['carton_mark_details'] !!} <br/>
                        <strong>Carton Make-Up</strong> : {!! $data['responses'][0]['carton_make_up'] !!} <br/>
                        <strong>Air Freight</strong> : {!! $data['responses'][0]['air_freight'] !!} <br/>
                        <strong>Flims/CD</strong> : {!! $data['responses'][0]['flims_cd'] !!} <br/>
                        <strong>Picture-Card</strong> : {!! $data['responses'][0]['picture_card'] !!} <br/>
                        <strong>Inner Cardboard</strong> : {!! $data['responses'][0]['inner_cardboard'] !!} <br/>
                        <strong>Shiping Size</strong> : {!! $data['responses'][0]['shiping_size'] !!} <br/>
                        <strong>Vendor</strong> : {{ fnVendor( $data['responses'][0]['carton_vendor_id'],$vendors) }} <br/>
                    </td>
                </tr>
            </tbody>
    </table>

    <footer>
        @if($ms_sheet_avil==0)
        <script type="text/php">
            if (isset($pdf)) {
               $font = $fontMetrics->getFont("Arial", "bold");
               $pdf->page_text(785, 568, "{PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
            }
        </script>
        @endif
    </footer>

</body>
</html>
