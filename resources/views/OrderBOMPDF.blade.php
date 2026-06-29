<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ trans('WebSite.bom_bill_of_materials') }}</title>
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
$currency = $data['responses'][0]['currency_type']??'';
function fnVendor($id,$vendors){
   // echo $id;
    $new = array_filter($vendors, function ($var) use($id) {
        if ($var['id'] == (int)$id){
            echo $var['vendor_name'];
        }
    });
}
$ms_sheet_avil=0;
?>

<body style="font-family: poppins,arialuni; font-size: 14px;">
    <div id="header">
        <table width="100%"  style="border-collapse: collapse;font-family: poppins,arialuni; font-size:12px;" cellspacing="1px" class="mainTable header_footer" >
            <tr>
                <td width="12%" >
                    @if($data['useLogo']==1 && $data['userLogo']!='')
                        <img src="{{ $data['userLogo'] }}"
                        style="background-color: #FFFFFF; height: 40px;" />
                    @else
                        <img src="{{ public_path() . "/images/dms-log-with-tag.png" }}"
                        style="background-color: #FFFFFF; height: 40px;" />
                    @endif
                </td>
                <td width="10%" style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.Order_only') }} / {{ trans('WebSite.Style') }}</strong>
                </td>
                <td width="10%" style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ $data['responses'][0]['order_no'] }} / {{ $data['responses'][0]['style_no'] }}
                </td>
                <td ><p style="font-family: poppins,arialuni,notosansjp,poppins-semibold;font-weight:800; font-size:18px; text-align:center">
                    <strong>{{ trans('WebSite.bom_bill_of_materials') }}</strong></p>
                </td>
                <td width="8%" style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.date') }}</strong>
                </td>
                <td width="12%" style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
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


    <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;" cellspacing="1px" class="mainTable">

        @php
            $sewing = json_decode($data['responses'][0]['sewing_accessories']);
            $sewing_img =0;
        @endphp
        @if(!empty($sewing))
            <tr style="background-color: #ffffff; color: #000000; font-weight:800; font-size:15px">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;font-size:17px;" colspan="10"><strong>{{ trans('WebSite.sewing_accessories') }}</strong></td>
            </tr>
            <tr style="background-color: #f0efef; color: #000000; font-weight:800; font-size:15px">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.bom_type') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.item') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.bom_color') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.bom_vendor') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.per_GMT') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.bom_total_qty') }}</strong></td>
                {{-- <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.lead_time') }}</strong></td> --}}
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.units') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.price_unit') }} </strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.total_price') }} </strong></td>
            </tr>
            @foreach($sewing as $sew)
                @php
                $sewing_img=0;
                @endphp
                @if(isset($sew->Type))
                    <tr style="background-color: #ffffff; color: #000000; font-weight:800; font-size:15px">
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->Type ?? '-' }}</strong></td>
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->component ?? '-' }}</strong></td>
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->color ?? '-' }}</strong></td>
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ fnVendor($sew->vendor ?? 0,$vendors) }}</strong></td>
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->perGMT ?? '-' }}</strong></td>
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->totalQty ?? '-' }}</strong></td>
                        {{-- <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->leadTime ?? '-' }}</strong></td> --}}
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->units ?? '-' }}</strong></td>
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->pricePerUnit ?? '-' }}</strong></td>
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->totalPrice ?? '-' }}</strong></td>
                    </tr>
                    @if($data['img_req']==1)
                        @foreach ($data['media'] as $response)
                            @if ($response['media_type'] == $sew->media_type)
                                @php
                                $sewing_img++;
                                @endphp
                                @if($sewing_img==1)
                                    <tr style="background-color: #ffffff; color: #000000; font-weight:800; font-size:15px">
                                        <td colspan="10">
                                            <table style="border: none">
                                                <tr>
                                @endif
                                    <td style="border: none">
                                        <img style="padding: 10px; " src="{{ Storage::disk('s3')->temporaryUrl($response['filepath'], '+15 minutes') }}" title="{{ $sew->Type ?? '-' }}" width="100px">
                                    </td>
                            @endif
                        @endforeach
                        @if($sewing_img>0)
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                        @endif
                    @endif
                @endif
            @endforeach
        @endif
        @php
            $packing = json_decode($data['responses'][0]['packing_accessories']);
            $packing_img =0;
        @endphp
        @if(!empty($packing))
            <tr style="background-color: #ffffff; color: #000000; font-weight:800; font-size:15px;border: none">
                <td colspan="10" style="border: none">&nbsp;</td>
            </tr>
            <tr style="background-color: #ffffff; color: #000000; font-weight:800; font-size:15px">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;font-size:17px;" colspan="10"><strong>{{ trans('WebSite.packing_accessories') }}</strong></td>
            </tr>
            <tr style="background-color: #f0efef; color: #000000; font-weight:800; font-size:15px">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.bom_type') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.item') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.bom_color') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.bom_vendor') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.per_GMT') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.bom_total_qty') }}</strong></td>
                {{-- <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.lead_time') }}</strong></td> --}}
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.units') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.price_unit') }} </strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.total_price') }} </strong></td>
            </tr>
            @foreach($packing as $sew)
                @php
                $packing_img=0;
                @endphp
                <tr style="background-color: #ffffff; color: #000000; font-weight:800; font-size:15px">
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->Type ?? '-' }}</strong></td>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->component ?? '-' }}</strong></td>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->color ?? '-' }}</strong></td>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ fnVendor($sew->vendor ?? 0,$vendors) }}</strong></td>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->perGMT ?? '-' }}</strong></td>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->totalQty ?? '-' }}</strong></td>
                    {{-- <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->leadTime ?? '-' }}</strong></td> --}}
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->units ?? '-' }}</strong></td>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->pricePerUnit ?? '-' }}</strong></td>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->totalPrice ?? '-' }}</strong></td>
                </tr>
                @if($data['img_req']==1)
                    @foreach ($data['media'] as $response)
                        @if ($response['media_type'] == $sew->media_type)
                            @php
                            $packing_img++;
                            @endphp
                            @if($packing_img==1)
                                <tr style="background-color: #ffffff; color: #000000; font-weight:800; font-size:15px">
                                    <td colspan="10">
                                        <table style="border: none">
                                            <tr>
                            @endif
                                <td style="border: none"><img style="padding: 10px; " src="{{ Storage::disk('s3')->temporaryUrl($response['filepath'], '+15 minutes') }}" title="{{ $sew->Type ?? '-' }}" width="100px"></td>
                        @endif
                    @endforeach
                    @if($packing_img>0)
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                    @endif
                @endif
            @endforeach
        @endif
        @php
        $miscellaneous = json_decode($data['responses'][0]['miscellaneous']);
        $miscellaneous_img =0;
        @endphp
        @if(!empty($miscellaneous))
            <tr style="background-color: #ffffff; color: #000000; font-weight:800; font-size:15px;border: none">
                <td colspan="10" style="border: none">&nbsp;</td>
            </tr>
            <tr style="background-color: #ffffff; color: #000000; font-weight:800; font-size:15px">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;font-size:17px;" colspan="10"><strong>{{ trans('WebSite.miscellaneous') }}</strong></td>
            </tr>
            <tr style="background-color: #f0efef; color: #000000; font-weight:800; font-size:15px">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.bom_type') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.item') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.bom_color') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.bom_vendor') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.per_GMT') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.bom_total_qty') }}</strong></td>
                {{-- <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.lead_time') }}</strong></td> --}}
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.units') }}</strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.price_unit') }} </strong></td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.total_price') }} </strong></td>
            </tr>
            @foreach($miscellaneous as $sew)
                @php
                $miscellaneous_img=0;
                @endphp
                <tr style="background-color: #ffffff; color: #000000; font-weight:800; font-size:15px">
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->Type ?? '-' }}</strong></td>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->component ?? '-' }}</strong></td>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->color ?? '-' }}</strong></td>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ fnVendor($sew->vendor ?? 0,$vendors) }}</strong></td>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->perGMT ?? '-' }}</strong></td>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->totalQty ?? '-' }}</strong></td>
                    {{-- <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->leadTime ?? '-' }}</strong></td> --}}
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->units ?? '-' }}</strong></td>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->pricePerUnit ?? '-' }}</strong></td>
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $sew->totalPrice ?? '-' }}</strong></td>
                </tr>
                @if($data['img_req']==1)
                    @foreach ($data['media'] as $response)
                        @if ($response['media_type'] == $sew->media_type)
                            @php
                            $miscellaneous_img++;
                            @endphp
                            @if($miscellaneous_img==1)
                                <tr style="background-color: #ffffff; color: #000000; font-weight:800; font-size:15px">
                                    <td colspan="10">
                                        <table style="border: none">
                                            <tr>
                            @endif
                                <td style="border: none"><img style="padding: 10px; " src="{{ Storage::disk('s3')->temporaryUrl($response['filepath'], '+15 minutes') }}" title="{{ $sew->Type ?? '-' }}" width="100px"></td>
                        @endif
                    @endforeach
                    @if($miscellaneous_img>0)
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                    @endif
                @endif
            @endforeach
        @endif
    </table>

    <table width="100%"  style="border-collapse: collapse;font-family: poppins,arialuni; margin-top:20px" cellspacing="1px" class="mainTable header_footer" >
        <tr>
            <td width="30%" style="padding :0px 3px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;text-align:center">
                <strong>{{ trans('WebSite.prepared_by') }}</strong>
            </td>
            <td>

            </td>
            <td width="30%" style="padding :0px 3px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;text-align:center;">
                <strong>{{ trans('WebSite.approved_by') }}</strong>
            </td>
        </tr>
        <tr>
            <td width="30%" style="padding :0px 3px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;text-align:center">
                <strong>{!! $data['responses'][0]['created_user_id'] > 0 ? $data['responses'][0]['user_name'] : $data['responses'][0]['staffName'] !!} &nbsp;&nbsp;&nbsp;&nbsp; {{ date($data['dateFormat'],strtotime($data['responses'][0]['created_at'])) }} &nbsp;&nbsp;&nbsp;{{ date('H:i:s',strtotime($data['responses'][0]['created_at'])) }}</strong>
            </td>
            <td>

            </td>
            @if($data['responses'][0]['approved_by']!='' && $data['responses'][0]['approved_by']!=NULL)
                <td width="30%" style="padding :0px 3px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;text-align:center;">
                    <strong>{{ $data['responses'][0]['approved_by'] }} &nbsp;&nbsp;&nbsp;&nbsp; {{ date($data['dateFormat'],strtotime($data['responses'][0]['approval_date'])) }} &nbsp;&nbsp;&nbsp;{{ date('H:i:s',strtotime($data['responses'][0]['approval_date'])) }}</strong>
                </td>
            @else
                <td width="30%" style="padding :0px 3px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;text-align:center;">
                    <strong>-</strong>
                </td>
            @endif
        </tr>
        {{-- <tr>
            <td width="7%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                <strong>{{ trans('WebSite.created_by') }}</strong>
            </td>
            <td style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">

            </td>
            <td width="8%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                <strong>{{ trans('WebSite.created_on') }}</strong>
            </td>
            <td width="8.5%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">

            </td>

        </tr> --}}

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

