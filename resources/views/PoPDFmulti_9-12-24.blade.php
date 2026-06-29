
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Order Details</title>
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

        body {
            font-family: 'Poppins'; color:#000000;
        }
        .mainTable table {
            border: 1px solid #2d292a;
            border-collapse: collapse;
        }

        .mainTable td {
            border: 1px solid #2d292a;
            border-collapse: collapse;
        }

        .mainTable th {
            border: 1px solid #2d292a;
            border-collapse: collapse;
        }

        .page-break {
            page-break-after: always;
        }

        .tableType td p, table td p, table td li,table td a {
            word-break: break-word !important;
            padding-right: 15px;
        }

        .tableType {
            border-collapse: collapse;
        }
        table td {word-wrap:break-word !important; }
        /* table tr:nth-child(even) {background: #f6f6f6}
        table tr:nth-child(odd) {background: #ffffff} */
        .noborder td, .noborder tr,.noborder table, .noborder {
            border: none !important;
        }
        td{
            padding-top: 2px !important;
        }
        table {
    page-break-inside: auto;
}

/* tr.avoid_page_break {
    page-break-after: avoid;
} */



    .avoid_page_break{
        page-break-inside: avoid;
        page-break-after: auto;
    }


    </style>

</head>
@php
    $forwarder = [
        '1'=> 'Buyer Nominated',
        '2'=> 'Seller Nominated',
        '3'=> 'Others',
        '4'=> 'To be adviced'
    ];
    $publish_status = '';
@endphp

<body style="font-family: poppins,arialuni; font-size: 14px;">
    <div>
        <table width="100%"  cellpadding="0" cellspacing="0" class="mainTable noborder"  >
            <tr>
                <td colspan="2" style="vertical-align:middle;text-align:center;font-size:22px; font-weight:800;padding:0; ">
                        <strong>{{ trans('WebSite.purchare_order') }} </strong>
                </td>
            </tr>
        </table>
        <table width="100%"  cellpadding="0" cellspacing="0" class="mainTable noborder" >
            <tr>
                <td style="text-align: left">
                       P.O # {{ $datas['po_number'] }}
                       @if($datas['data'][0]['status']==0)
                        @php $publish_status = ''; //' ('. trans('WebSite.unpublished') .')';
                        @endphp
                        {{-- &nbsp;<strong>({{ trans('WebSite.unpublished') }})</strong> --}}
                       @endif
                </td>
                <td style="text-align: right">
                        <strong> {{ trans('WebSite.date') }} : {{ date('d M Y',strtotime($datas['data'][0]['created_date'])) }}</strong>
                </td>

            </tr>
        </table>
    </div>
    <div style="clear : both;"></div>


        @php
        $client = $buyer = $seller = $maker =0;
        if($datas['data'][0]['buyer']!='')
        {
            $client++;
            $buyer=1;
        }
        if($datas['data'][0]['seller']!='')
        {
            $client++;
            $seller=1;
        }
        if($datas['data'][0]['maker']!='')
        {
            $client++;
            $maker=1;
        }
        @endphp
        <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;margin:5px 0 0"cellspacing="1px" class="mainTable">
            <tr style="background: #d3d3d3; font-size:14px; font-weight:800;">
                @if($buyer==1)
                <td style="text-align: center" width="50%"><strong>{{ trans('WebSite.Buyer') }}</strong></td>
                @endif
                @if($seller==1)
                <td style="text-align: center" width="50%"><strong>{{ trans('WebSite.seller') }}</strong></td>
                @endif
                @if($maker==1)
                <td style="text-align: center" width="50%"><strong>{{ trans('WebSite.maker') }}</strong></td>
                @endif
            </tr>
            <tr style="  font-weight:500; font-family: poppins,arialuni;">
                @if($buyer==1)
                <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{!! nl2br($datas['data'][0]['buyer']) !!}</strong></td>
                @endif
                @if($seller==1)
                <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{!! nl2br($datas['data'][0]['seller']) !!}</strong></td>
                @endif
                @if($maker==1)
                <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{!! nl2br($datas['data'][0]['maker']) !!}</strong></td>
                @endif
            </tr>

            @php $same_address_multi = 0;
            $style_count = count($datas['data']);
            if($datas['data'][0]['same_testing_agency']==1 && $style_count > 1){
                $same_address_multi=1;
            }
            @endphp
            @if($datas['data'][0]['fabric_testing_agency']!='' && $datas['data'][0]['fabric_testing_agency']!=NULL && $same_address_multi==1)
            <tr style="  font-weight:500; font-family: poppins,arialuni;">
                <td style="padding : 5px ; font-family: poppins,arialuni"><strong>{{ trans('WebSite.testing_agency') }} </strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni;" colspan={{ $client-1 }}><strong>{!! nl2br($datas['data'][0]['fabric_testing_agency']) !!}</strong></td>
            </tr>
            @endif

        </table>

            {{-- <tr style="  font-weight:500; font-family: poppins,arialuni;">
                <td colspan="2" style="padding : 5px ; font-family: poppins,arialuni;" style="background: #f2f2f2; font-size:16px; font-weight:800"><strong>&nbsp;Order Information</strong></td>
            </tr> --}}
            @foreach ($datas['data'] as $data)
            <?php
                $prod_img_count = 0;
                if(isset($datas['media']['files'])){
                    foreach($datas['media']['files'] as $file){
                        if($file['media_type']=='ProductImage' && $file['style_no']==$data['style_no']){
                            $prod_img_count=1;
                        }

                    }
                }
            ?>
            <div style="width:50%;float:left;display:inline-block;">
            <p style="padding : 0px; margin:0; font-family: poppins,arialuni;" style="color: #4e90de; font-size:17px; font-weight:800; line-height:10px"><strong>{{ trans('WebSite.order_information') }}</strong></p>
            </div>
            <div style="width:40%;float:right;display:inline-block;">
            <p style="padding : 0px; margin:0; font-family: poppins,arialuni;" style="color: #4e90de; font-size:17px; font-weight:800; line-height:10px; text-align:right"><strong>{{ $data['style_no'] }}</strong></p>
            </div>
            <div style="clear : both;"></div>
            <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni; margin:-5px 0 0;"cellspacing="1px" class="mainTable">
                <tr style="font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.style_number') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{{ $data['style_no'] }}</strong></td>
                </tr>
                <tr style=" font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.article_name') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{{ $data['article_name'] }}</strong></td>
                </tr>
                @if($data['article_description']!='' && $data['article_description']!=NULL)
                    <tr style="font-weight:500; font-family: poppins,arialuni;">
                        <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.article_description') }}</strong></td>
                        <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{!! ucfirst($data['article_description']) !!}</strong></td>
                    </tr>
                @endif
                @if(count($datas['media']['files'])>0 && $prod_img_count==1)
                    <tr style="font-weight:500; font-family: poppins,arialuni;">
                        <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.product_image') }}</strong></td>
                        <td style="padding : 5px ; font-family: poppins,arialuni; vertical-align:middle;">
                            <div style="clear : both;"></div>
                            <?php
                                if(isset($datas['media']['files'])){
                                    foreach($datas['media']['files'] as $file){
                                        if($file['media_type']=='ProductImage' && $file['style_no']==$data['style_no']){
                                            echo "
                                            <span style='display: inline-block; vertical-align: top; padding:5px;'>
                                            <img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' style='height: 100px;' >
                                            </span>
                                            ";
                                        }

                                    }
                                }
                            ?>
                        </td>
                    </tr>
                @endif
                <tr style="font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.fabric_details') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;">
                        @if($data['fabric_type']!='' && $data['fabric_type']!=NULL)
                            <strong>{!! $data['fabric_type'] !!}</strong>,&nbsp;
                        @endif
                        @if($data['fabric_composition']!='' && $data['fabric_composition']!=NULL)
                            <strong>{!! str_replace(',',' & ',$data['fabric_composition']) !!}</strong>,&nbsp;
                        @endif
                        @if($data['fabric_GSM']!='' && $data['fabric_GSM']!=NULL)
                            <strong>{!! $data['fabric_GSM'] !!}</strong> GSM,&nbsp;
                        @endif
                        @php
                        $tol = explode('/',$data['gsm_tolerance']);
                        $tol_pls = $tol[0]??0;
                        $tol_mins = $tol[1]??0;
                        @endphp
                        @if($tol_pls!=0 || $tol_mins!=0)
                            (+{{round((int)$tol_pls,0)}} / -{{(int)round($tol_mins,0)}}){{ $data['gsm_percent_type']==1 ? '%' : ' GSM' }}
                        @endif
                        {{-- (+/-){!! $datas['data'][0]['gsm_tolerance'] !!}, --}}
                    </td>
                </tr>
                @if($data['yarn_count_type']!='' && $data['yarn_count_type']!=NULL)
                    <tr style="font-weight:500; font-family: poppins,arialuni;">
                        <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.yarn_type') }}</strong></td>
                        <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{!! $data['yarn_count_type'] !!}</strong></td>
                    </tr>
                @endif
                @if($data['total_qty']!='0' && $data['total_qty']!=NULL)
                    <tr style="font-weight:500; font-family: poppins,arialuni;">
                        <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.total_qty') }}</strong></td>
                        <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $data['total_qty'] !!}
                        @if($data['unit_name']!='' && $data['unit_name']!=NULL)
                            &nbsp; <strong>{!! $data['unit_name'] !!}</strong>
                        @endif
                        @php
                        $tol_mins = $data['total_qty_min_tol']??0;
                        $tol_pls =  $data['total_qty_max_tol']??0;
                        @endphp
                        @if($tol_pls!=0 || $tol_mins!=0)
                            (+{{round($tol_pls,0)}} / -{{round($tol_mins,0)}}){{ $data['total_qty_percent_type']==1 ? '%' : ' Piece' }}
                        @endif
                        </td>
                    </tr>
                @endif
                @if(!empty($datas['sku']))
                <tr style="font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;" colspan="2">
                        <strong>{{ trans('WebSite.sku_details') }}</strong>
                        <table style="width: 100%;">
                            <?php
                            $total_arr = [];
                            $k=0;
                            if(!empty($datas['sku'])){
                                ?>
                                <tr style="background: #d3d3d3;">
                                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%;text-align:center;"><strong>{{ trans('WebSite.colors') }}</strong></td>
                                    <?php
                                    foreach($datas['sizes'] as $size){
                                        if($data['id']==$size['po_id'])
                                            echo '<td style="padding : 5px ; font-family: poppins,arialuni;text-align:center;"><strong>'.$size['name'].'</strong></td>';
                                    }
                                    echo '<td style="padding : 5px ; font-family: poppins,arialuni;text-align:center;">'.trans('WebSite.total').'</td>';
                                echo '</tr>';
                                    foreach($datas['colors'] as $cols){
                                        if($data['id']==$cols['po_id'])
                                        {
                                            echo '<tr>';
                                                echo '<td style="padding : 5px ; font-family: poppins,arialuni;text-align:center;"><strong>'.ucwords($cols['name']).'</strong></td>';
                                                $total_qty = 0;
                                                foreach($datas['sizes'] as $size){

                                                    if($data['id']==$size['po_id']){
                                                        $vv = getIndex($size['id'], $cols['id'],$data['id'], (array)$datas['sku']);
                                                        $total_qty+=(int)$datas['sku'][$vv]['quantity'];
                                                        echo '<td style="padding : 5px ; font-family: poppins,arialuni;text-align:center;">'.$datas['sku'][$vv]['quantity'].'</td>';
                                                        $total_arr[$k]['qty'] = (int)$datas['sku'][$vv]['quantity'];
                                                        $total_arr[$k]['size'] = (int)$size['id'];
                                                        $k++;
                                                    }

                                                }
                                                echo '<td style="padding : 5px ; font-family: poppins,arialuni;text-align:center;">'.$total_qty.'</td>';

                                            echo '</tr>';
                                        }
                                    }

                                    $qresult = [];
                                    foreach ($total_arr as $a) {   // $arr is your initial array
                                        (isset($qresult[$a['size']]))?
                                            $qresult[$a['size']] += $a['qty']
                                            : $qresult[$a['size']] = $a['qty'];
                                    }

                                    //print_r($qresult);
                                    $total_qty = 0;
                                    echo '<tr style="background-color: #d3d3d3;">';
                                                echo '<td style="padding : 5px ; font-family: poppins,arialuni;text-align:center;"><strong>'.trans('WebSite.total').'</strong></td>';
                                                foreach($datas['sizes'] as $size){
                                                    if($data['id']==$size['po_id']){
                                                        $total_qty+=(int)$qresult[$size['id']];
                                                        echo '<td style="padding : 5px ; font-family: poppins,arialuni;text-align:center;">'.$qresult[$size['id']].'</td>';
                                                    }

                                                }
                                               // dd($total_arr);
                                               echo '<td style="padding : 5px ; font-family: poppins,arialuni;text-align:center;">'.$total_qty.'</td>';
                                    echo '</tr>';
                            }
                            ?>
                        </table>
                    </td>
                </tr>
                @endif
            </table>
            <div style="width:50%;float:left;display:inline-block;">
                <p style="padding : 0px; margin:0; font-family: poppins,arialuni;" style="color: #4e90de; font-size:17px; font-weight:800; line-height:10px"><strong>{{ trans('WebSite.commercial_information') }}</strong></p>
            </div>
            <div style="width:40%;float:right;display:inline-block;">
                <p style="padding : 0px; margin:0; font-family: poppins,arialuni;" style="color: #4e90de; font-size:17px; font-weight:800; line-height:10px; text-align:right"><strong>{{ $data['style_no'] }}</strong></p>
            </div>
            <div style="clear : both;"></div>
            <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni; margin:-5px 0 0;"cellspacing="1px" class="mainTable">
                {{-- <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td colspan="2" style="padding : 5px ; font-family: poppins,arialuni;" style="background: #f2f2f2; font-size:16px;"><strong>&nbsp;Commercial Information</strong></td>
                </tr> --}}
                @if($data['price']!='0' && $data['price']!=NULL)
                <tr style="font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.price') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $data['currency_short_code'] }}  {{ $data['price'] }}
                        @if ($data['price_unit_name']!='')
                            / <strong>{{ strtolower($data['price_unit_name']) == 'pieces' ? 'Piece' : $data['price_unit_name'] }}</strong>
                        @endif
                    </td>
                </tr>
                @endif
                @if($data['income_terms']!='' && $data['income_terms']!=NULL)
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.Incoterms') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{{ $data['income_terms'] }}</strong></td>
                </tr>
                @endif
                @if($data['payment_terms']!='' && $data['payment_terms']!=NULL)
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.payment_terms') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{{ $data['payment_terms'] }}</strong></td>
                </tr>
                @endif
                @if($data['delivery_date']!='' && $data['delivery_date']!=NULL && $data['delivery_date']!='0000-00-00')
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.delivery_date') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;">{{ date('d M Y',strtotime($data['delivery_date'])) }} {!! $data['delivery_date_type']  !!}</td>
                </tr>
                @endif
                @if($data['origin_port']!='' && $data['origin_port']!=NULL)
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.origin_port') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{{ ucfirst($data['origin_port']) }}</strong></td>
                </tr>
                @endif
                @if($data['destination_port']!='' && $data['destination_port']!=NULL)
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.destination_port') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{{ ucfirst($data['destination_port']) }}</strong></td>
                </tr>
                @endif
                @if($data['mode_of_shipment']!='' && $data['mode_of_shipment']!=NULL)
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.mode_of_shipment') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{{ $data['mode_of_shipment'] }}</strong></td>
                </tr>
                @endif
                @if($data['document_requirement']!='' && $data['document_requirement']!=NULL)
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.document_requirement') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;">
                        <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;"cellspacing="1px" class="mainTable">
                            <tr>
                                <td rowspan="2"></td>
                                <td colspan="2" style="text-align: center;">{{ trans('WebSite.no_of_copies') }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">{{ trans('WebSite.original') }}</td>
                                <td style="text-align: center;">{{ trans('WebSite.duplicate') }}</td>
                            </tr>
                            @php
                                $nos = explode('||',$data['document_requirement']);
                            @endphp
                            @for ($i=0;$i<count($nos);$i++)
                                @php $docs = explode('|',$nos[$i]); @endphp
                                <tr>
                                    <td style="text-align:center;"><strong>{{ $docs[0] }}</strong></td>
                                    <td style="text-align: center;">{{ $docs[1] ?? 0 }}</td>
                                    <td style="text-align: center;">{{ $docs[2] ?? 0 }}</td>
                                </tr>
                            @endfor

                        </table>
                    </td>
                </tr>
                @endif
                @if($data['hs_code']!='' && $data['hs_code']!=NULL)
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.hsn_code') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{{ $data['hs_code'] }}</strong></td>
                </tr>
                @endif
                @if($data['forwarder']!='0' && $data['forwarder']!=NULL)
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.forwarder') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;" >
                        @if($data['forwarder_id']==0)
                            {{ $forwarder[$data['forwarder']]  }}
                        @elseif($data['forwarder_id']>0)
                            <table cellspacing="0" cellpadding="0" class="noborder">
                                @if($data['company_name']!='')
                                    <tr>
                                        <td><strong>{{ trans('WebSite.company_name') }}</strong></td>
                                        <td>&nbsp;:&nbsp;</td>
                                        <td><strong>{!! $data['company_name'] !!}</strong></td>
                                    </tr>
                                @endif
                                @if($data['address']!='')
                                    <tr>
                                        <td><strong>{{ trans('WebSite.address') }}</strong></td>
                                        <td>&nbsp;:&nbsp;</td>
                                        <td><strong>{!! $data['address'] !!}</strong></td>
                                    </tr>
                                @endif
                                @if($data['contact_person']!='')
                                    <tr>
                                        <td><strong>{{ trans('WebSite.contact_person') }}</strong></td>
                                        <td>&nbsp;:&nbsp;</td>
                                        <td><strong>{{ ucfirst($data['contact_person']) }}</strong></td>
                                    </tr>
                                @endif
                                @if($data['contact_phone']!='')
                                    <tr>
                                        <td><strong>{{ trans('WebSite.phone_number') }}</strong></td>
                                        <td>&nbsp;:&nbsp;</td>
                                        <td><strong>{{ $data['contact_phone'] }}</strong></td>
                                    </tr>
                                @endif
                                @if($data['contact_email']!='')
                                    <tr>
                                        <td><strong>{{ trans('WebSite.email') }}</strong></td>
                                        <td>&nbsp;:&nbsp;</td>
                                        <td><strong>{{ $data['contact_email'] }}</strong></td>
                                    </tr>
                                @endif
                            </table>

                        @elseif($data['forwarder_contact_person']!='' && $data['forwarder_phone']!='' && $data['forwarder_email']!='' && $data['forwarder_address']!='')
                            <table cellspacing="0" cellpadding="0" class="noborder">
                                @if($data['forwarder_contact_person']!='')
                                    <tr>
                                        <td>{{ trans('WebSite.contact_person') }}</td>
                                        <td>&nbsp;:&nbsp;</td>
                                        <td>{{ ucfirst($data['forwarder_contact_person']) }}</td>
                                    </tr>
                                @endif
                                @if($data['forwarder_phone']!='')
                                    <tr>
                                        <td>{{ trans('WebSite.phone_number') }}</td>
                                        <td>&nbsp;:&nbsp;</td>
                                        <td>{{ $data['forwarder_phone'] }}</td>
                                    </tr>
                                @endif
                                @if($data['forwarder_email']!='')
                                    <tr>
                                        <td>{{ trans('WebSite.email') }}</td>
                                        <td>&nbsp;:&nbsp;</td>
                                        <td>{{ $data['forwarder_email'] }}</td>
                                    </tr>
                                @endif
                                @if($data['forwarder_address']!='')
                                    <tr>
                                        <td>{{ trans('WebSite.address') }}</td>
                                        <td>&nbsp;:&nbsp;</td>
                                        <td>{!! $data['forwarder_address'] !!}</td>
                                    </tr>
                                @endif
                            </table>
                        {{-- @else
                            {{ $forwarder[$data['forwarder']]  }} --}}
                        @endif
                    </td>
                </tr>

                @endif
                @if($data['place_of_jurisdiction']!='' && $data['place_of_jurisdiction']!=NULL)
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.jurisdiction') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{{ ucfirst($data['place_of_jurisdiction']) }}</strong></td>
                </tr>
                @endif
                @if($data['penality']!='' && $data['penality']!=NULL)
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.penalty') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{{ $data['penality'] }}</strong></td>
                </tr>
                @endif

            </table>
            @if(!empty($datas['testings']) || ($data['testing_requirements']!='' && $data['testing_requirements']!=NULL) || ( $same_address_multi==0 && $data['fabric_testing_agency']!='' && $data['fabric_testing_agency']!=NULL && $data['garment_testing_agency']!='' && $data['garment_testing_agency']!=NULL) || (!empty($datas['testings'])) ||
                ($data['testing_costs']!='' && $data['testing_costs']!=NULL ) || (($data['inspection_company']!='' && $data['inspection_company']!=NULL) || ($data['inspection_type']!='' && $data['inspection_type']!=NULL) || ($data['inspection_cost']!='' && $data['inspection_cost']!=NULL)))
                <div style="width:50%;float:left;display:inline-block;">
                    <p style="padding : 0px; margin:0; font-family: poppins,arialuni;" style="color: #4e90de; font-size:17px; font-weight:800; line-height:10px"><strong>{{ trans('WebSite.testing') }} </strong></p>
                </div>

                <div style="width:40%;float:right;display:inline-block;">
                    <p style="padding : 0px; margin:0; font-family: poppins,arialuni;" style="color: #4e90de; font-size:17px; font-weight:800; line-height:10px; text-align:right"><strong>{{ $data['style_no'] }}</strong></p>
                </div>
            @endif
            <div style="clear : both;"></div>
            <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni; margin:-5px 0 0;"cellspacing="1px" class="mainTable">
                @if($data['testing_requirements']!='' && $data['testing_requirements']!=NULL)
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.test_requiremennt') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;">
                        <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;"cellspacing="1px" class="mainTable">
                            <tr style="background-color: #d3d3d3">
                                <td style="padding : 5px ; font-family: poppins,arialuni;text-align: center;"><strong>{{ trans('WebSite.type') }}</strong></td>
                                <td style="text-align: center;"><strong>{{ trans('WebSite.grade') }}</strong></td>
                                <td style="text-align: center;"><strong>{{ trans('WebSite.test_method') }}</strong></td>
                                <td style="text-align: center;"><strong>{{ trans('WebSite.remarks') }}</strong></td>
                            </tr>
                            @php
                                $nos = explode('||',$data['testing_requirements']);
                            @endphp
                            @for ($i=0;$i<count($nos);$i++)
                                @php $docs = explode('|',$nos[$i]); @endphp
                                <tr>
                                    <td style="padding : 5px ; font-family: poppins,arialuni;text-align: center;"><strong>{{ $docs[0] }}</strong></td>
                                    <td style="text-align: center;"><strong>{{ $docs[1] ?? 0 }}</strong></td>
                                    <td style="text-align: center;">{{ (isset($docs[2]) && $docs[2]!='' && $docs[2]!='0') ? ($docs[2]==1 ? 'AATCC': 'JIS') : "-" }}</td>
                                    <td style="text-align: center;"><strong>{{ $docs[3] ?? '-' }}</strong></td>
                                </tr>
                            @endfor

                        </table>
                    </td>
                </tr>
                @endif
                @php $j=0; $same_address_single = 0;
                if($data['fabric_testing_agency'] == $data['garment_testing_agency']  ){
                    $same_address_single=1;
                }
                @endphp
                @if($data['fabric_testing_agency']!='' && $data['fabric_testing_agency']!=NULL && $same_address_single==1 && $same_address_multi==0)
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.testing_agency') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{!! nl2br($data['fabric_testing_agency']) !!}</strong></td>
                </tr>
                @endif
                @if(!empty($datas['testings']))
                    @foreach($datas['testings'] as $sam)
                        @if($sam['type']=='fabric_testing' && $sam['po_id']==$data['id'])
                            @if($j==0)
                                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.fabric_testing') }}</strong></td>
                                    <td style="padding : 5px ; font-family: poppins,arialuni;">
                                        <table width="50%" style="border-collapse: collapse;font-family: poppins,arialuni;"cellspacing="1px" class="mainTable">
                                            <tr style="background: #d3d3d3;font-family: poppins,arialuni;">
                                                <td style="padding : 5px ; font-family: poppins,arialuni;text-align: center;"><strong>{{ trans('WebSite.color') }}</strong></td>
                                                <td style="padding : 5px ; font-family: poppins,arialuni;text-align: center"><strong>{{ trans('WebSite.length_in_meters') }}</strong></td>
                                            </tr>
                            @endif
                                            <tr>
                                                <td style="padding : 5px ; font-family: poppins,arialuni;text-align: center;"><strong>{{ $sam['color'] }}</strong></td>
                                                <td style="padding : 5px ; font-family: poppins,arialuni;text-align: center">{{ $sam['length_qty'] }}</td>
                                            </tr>

                            @php $j++; @endphp
                        @endif

                    @endforeach
                    @if($j>0)
                                </table>
                            </td>
                        </tr>
                    @endif
                @endif
                @if($data['fabric_testing_agency']!='' && $data['fabric_testing_agency']!=NULL && $same_address_single==0)
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.fabric_testing_agency') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{!! nl2br($data['fabric_testing_agency']) !!}</strong></td>
                </tr>
                @endif
                @php $j=0; @endphp
                @if(!empty($datas['testings']))
                    @foreach($datas['testings'] as $sam)
                        @if($sam['type']=='garment_testing' && $sam['po_id']==$data['id'])
                            @if($j==0)
                                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.garment_testing') }}</strong></td>
                                    <td style="padding : 5px ; font-family: poppins,arialuni;">
                                        <table width="75%" style="border-collapse: collapse;font-family: poppins,arialuni;"cellspacing="1px" class="mainTable">
                                            <tr style="background: #d3d3d3;font-family: poppins,arialuni;">
                                                <td style="padding : 5px ; font-family: poppins,arialuni;text-align: center;"><strong>{{ trans('WebSite.color') }}</strong></td>
                                                <td style="padding : 5px ; font-family: poppins,arialuni;text-align: center"><strong>{{ trans('WebSite.size') }}</strong></td>
                                                <td style="padding : 5px ; font-family: poppins,arialuni;text-align: center"><strong>{{ trans('WebSite.pieces') }}</strong></td>
                                            </tr>
                            @endif
                                            <tr>
                                                <td style="padding : 5px ; font-family: poppins,arialuni;text-align: center;"><strong>{{ $sam['color'] }}</strong></td>
                                                <td style="padding : 5px ; font-family: poppins,arialuni;text-align: center"><strong>{{ $sam['size'] }}</strong></td>
                                                <td style="padding : 5px ; font-family: poppins,arialuni;text-align: center">{{ round($sam['length_qty'],0) }}</td>
                                            </tr>

                            @php $j++; @endphp
                        @endif
                    @endforeach
                    @if($j>0)
                                </table>
                            </td>
                        </tr>
                    @endif
                @endif
                @if($data['garment_testing_agency']!='' && $data['garment_testing_agency']!=NULL && $same_address_single==0)
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.garment_testing_agency') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{!! nl2br($data['garment_testing_agency']) !!}</strong></td>
                </tr>
                @endif
                @if($data['testing_costs']!='' && $data['testing_costs']!=NULL )
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.testing_cost') }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{!! nl2br($data['testing_costs']) !!}</strong></td>
                </tr>
                @endif
            </table>

            @if(($data['inspection_company']!='' && $data['inspection_company']!=NULL)|| ($data['inspection_type']!='' && $data['inspection_type']!=NULL) || ($data['inspection_cost']!='' && $data['inspection_cost']!=NULL))
                <div style="width:50%;float:left;display:inline-block;">
                    <p style="padding : 0px; margin:0; font-family: poppins,arialuni;" style="color: #4e90de; font-size:17px; font-weight:800; line-height:10px"><strong>{{ trans('WebSite.inspection') }} </strong></p>
                </div>
                <div style="width:40%;float:right;display:inline-block;">
                    <p style="padding : 0px; margin:0; font-family: poppins,arialuni;" style="color: #4e90de; font-size:17px; font-weight:800; line-height:10px; text-align:right"><strong>{{ $data['style_no'] }}</strong></p>
                </div>
                <div style="clear : both;"></div>
                <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni; margin:-5px 0 0;"cellspacing="1px" class="mainTable">
                    @if($data['inspection_company']!='' && $data['inspection_company']!=NULL)
                    <tr style="  font-weight:500; font-family: poppins,arialuni;">
                        <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.inspection_company') }}</strong></td>
                        <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{{($data['inspection_company']) }}</strong></td>
                    </tr>
                    @endif
                    @if($data['inspection_type']!='' && $data['inspection_type']!=NULL)
                    <tr style="  font-weight:500; font-family: poppins,arialuni;">
                        <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.inspection_type') }}</strong></td>
                        <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{{($data['inspection_type']) }}</strong></td>
                    </tr>
                    @endif
                    @if($data['inspection_cost']!='' && $data['inspection_cost']!=NULL)
                    <tr style="  font-weight:500; font-family: poppins,arialuni;">
                        <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.inspection_cost') }}</strong></td>
                        <td style="padding : 5px ; font-family: poppins,arialuni;"><strong>{{($data['inspection_cost']) }}</strong></td>
                    </tr>
                    @endif
                </table>
            @endif
            {{-- <tr style="  font-weight:800; font-family: poppins,arialuni;">
                <td colspan="2" style="padding : 5px ; font-family: poppins,arialuni;" style="background: #f2f2f2; font-size:16px"><strong>&nbsp;Sample Requirements</strong></td>
            </tr> --}}
                @php $j = $k=0; @endphp
                @if(!empty($datas['testings']))
                    @php
                        $sample_type = $datas['testings'][0]['type'];
                    @endphp
                {{-- <tr>
                    <td colspan="2"> --}}

                        @foreach($datas['testings'] as $sam)
                        @if($sam['po_id']==$data['id'] && $sam['type']!='fabric_testing' && $sam['type']!='garment_testing')

                           <?php
                            $counter = 0;
                            foreach($datas['testings'] as $item) {
                                if($item['po_id'] == $sam['po_id'] and $item['type'] == $sam['type']) {
                                    $counter++;
                                }
                            }
                            if($sample_type != $sam['type']){
                                $k=0;
                                $sample_type = $sam['type'];
                            }
                            ?>

                            @if($j==0)
                                <div style="width:50%;float:left;display:inline-block;">
                                    <p style="padding : 0px; margin:0; font-family: poppins,arialuni;" style="color: #4e90de; font-size:17px; font-weight:800; line-height:10px"><strong>{{ trans('WebSite.sample_requirements') }}</strong></p>
                                </div>
                                <div style="width:40%;float:right;display:inline-block;">
                                    <p style="padding : 0px; margin:0; font-family: poppins,arialuni;" style="color: #4e90de; font-size:17px; font-weight:800; line-height:10px; text-align:right"><strong>{{ $data['style_no'] }}</strong></p>
                                </div>
                                <div style="clear : both;"></div>
                                <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni; margin:-5px 0 0;"cellspacing="1px" class="mainTable">
                                <tr style="  font-weight:500; font-family: poppins,arialuni;background: #d3d3d3;">
                                    <td width="35%" style="padding : 5px ; font-family: poppins,arialuni;width:30%;text-align: center;"><strong>{{ trans('WebSite.sample_type') }}</strong></td>
                                    <td width="30%" style="padding : 5px ; font-family: poppins,arialuni;text-align: center"><strong>{{ trans('WebSite.color') }}</strong></td>
                                    <td width="20%" style="padding : 5px ; font-family: poppins,arialuni;text-align: center"><strong>{{ trans('WebSite.size') }}</strong></td>
                                    <td width="15%" style="padding : 5px ; font-family: poppins,arialuni;text-align: center"><strong>{{ trans('WebSite.quantity') }}</strong></td>
                                </tr>
                            @endif
                            @if($k == 0)
                                </table>
                                <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni; margin:0 0;"cellspacing="1px" class="mainTable avoid_page_break">
                            @endif
                                <tr style="  font-weight:500; font-family: poppins,arialuni;" class="">
                                    @if($k == 0)
                                        <td width="35%" rowspan="{{ $counter }}" style="padding : 5px ; font-family: poppins,arialuni;width:30%;text-align: center;"><strong>
                                            {{
                                                // ucfirst(str_replace('_',' ',$sam['type']))
                                                $sam['type']=='fit_sample' ? trans('WebSite.fit_sample') :
                                                    ($sam['type']=='pp_sample' ? trans('WebSite.pp_sample') : trans('WebSite.shipment_sample'))
                                            }}
                                        </strong></td>
                                    @endif
                                    <td width="30%" style="padding : 5px ; font-family: poppins,arialuni;text-align: center"><strong>{{ $sam['color'] }}</strong></td>
                                    <td width="20%" style="padding : 5px ; font-family: poppins,arialuni;text-align: center"><strong>{{ $sam['size'] }}</strong></td>
                                    <td width="15%" style="padding : 5px ; font-family: poppins,arialuni;text-align: center">{{ round($sam['length_qty'],0) }}</td>
                                </tr>

                            @php $j++; $k++; @endphp
                        @endif
                        @endforeach
                        </table>
                    {{-- </td>
                </tr> --}}
                @endif
            {{-- </table> --}}
        @endforeach

        @if($datas['data'][0]['additional_information']!='' && $datas['data'][0]['additional_information']!=NULL)
            <p style="padding : 0px; margin:0; font-family: poppins,arialuni;" style="color: #4e90de; font-size:17px; font-weight:800; line-height:10px"><strong>&nbsp;{{ trans('WebSite.additional_information') }}</strong></p>
            <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni; margin:-5px 0 0;"cellspacing="1px" class="mainTable">
                {{-- <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td colspan="2" style="padding : 5px ; font-family: poppins,arialuni;" style="background: #f2f2f2; font-size:16px"><strong>&nbsp;Additional Information</strong></td>
                </tr> --}}
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;" colspan="2"><strong>{!! nl2br($datas['data'][0]['additional_information']) !!}</strong></td>
                </tr>
            </table>
        @endif

    <table width="100%" style="border: none;font-family: poppins,arialuni;"cellspacing="1px" >
        {{-- <tr>
            <td colspan="3" style="min-height: 150px">&nbsp;<br>&nbsp;<br>&nbsp;</td>
        </tr> --}}
        @php
            $sing_arr = explode(',',$datas['data'][0]['sign_option']);
        @endphp
        <tr>
            @if(in_array("1",$sing_arr))
                <td style="padding : 5px ; font-family: poppins,arialuni;text-align:center" width="50%">
                    <strong>{{ trans('WebSite.Buyer') }}</strong><br>
                    <strong style="font-size: 16px;">{{ str_replace(',','',$datas['buyer']) }}</strong>
                </td>
            @endif
            @if(in_array("2",$sing_arr))
                <td style="padding : 5px ; font-family: poppins,arialuni;text-align:center" width="50%">
                    <strong>{{ trans('WebSite.seller') }}</strong><br>
                    <strong style="font-size: 16px;">{{ str_replace(',','',$datas['seller']) }}</strong>
                </td>
            @endif
            @if(in_array("3",$sing_arr))
                <td style="padding : 5px ; font-family: poppins,arialuni;text-align:center" width="50%">
                    <strong>{{ trans('WebSite.maker') }} </strong><br>
                    <strong style="font-size: 16px;">{{ str_replace(',','',$datas['maker']) }}</strong>
                </td>
            @endif
        </tr>
        <tr>
            @if(in_array("1",$sing_arr))
                <td style="padding : 5px ; font-family: poppins,arialuni;text-align:center">
                    <?php
                        if(isset($datas['media']['files'])){
                            foreach($datas['media']['files'] as $file){
                                if($file['media_type']=='buyer' ){
                                    echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' height='75px' style='padding:5px; margin-top:20px; max-width:100%' > ";
                                }

                            }
                        }
                    ?>
                </td>
            @endif
            @if(in_array("2",$sing_arr))
                <td style="padding : 5px ; font-family: poppins,arialuni;text-align:center">
                    <?php
                        if(isset($datas['media']['files'])){
                            foreach($datas['media']['files'] as $file){
                                if($file['media_type']=='seller' ){
                                    echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' height='75px' style='padding:5px; margin-top:20px; max-width:100%' > ";
                                }

                            }
                        }
                    ?>
                </td>
            @endif
            @if(in_array("3",$sing_arr))
                <td style="padding : 5px ; font-family: poppins,arialuni;text-align:center">
                    <?php
                        if(isset($datas['media']['files'])){
                            foreach($datas['media']['files'] as $file){
                                if($file['media_type']=='maker' ){
                                    echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' height='75px' style='padding:5px; margin-top:20px; max-width:100%' > ";
                                }

                            }
                        }
                    ?>
                </td>
            @endif
        </tr>


    </table>
    <footer>
        <script type="text/php">
            if (isset($pdf)) {
            $font = $fontMetrics->getFont("Arial", "bold");
            $pdf->page_text(35, 805, "P.O#{{ $datas['po_number'] }}{{ $publish_status }} {{ date('d M Y',strtotime($datas['data'][0]['created_date'])) }}", $font, 10, array(0, 0, 0));
            $pdf->page_text(525, 805, "Page {PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
            }
        </script>
    </footer>
</body>
</html>
<?php
function getIndex($needle, $haystack,$po_id, $array){
    foreach($array as $key => $value){
        if(is_array($value) && $value['size_id'] == $needle && $value['color_id'] == $haystack && $value['po_id'] == $po_id)
              return $key;
    }
    return 0;
}
//exit;
?>





