
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
            font-family: 'Poppins';
        }

        .mainTable table {
            border: 1px solid #EFEFEF;
            border-collapse: collapse;
        }

        .mainTable td {
            border: 1px solid #EFEFEF;
            border-collapse: collapse;
        }

        .mainTable th {
            border: 1px solid #EFEFEF;
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
    </style>

</head>

<body style="font-family: poppins,arialuni; font-size: 14px;">
    <div>
        <table width="100%"  cellpadding="0" cellspacing="0" class="mainTable" >
            <tr>
                <td width="10%">
                        <img src="{{ public_path() . '/images/dms-log-with-tag.png' }}"
                            style="background-color: #FFFFFF; height: 40px;" />
                </td>
                <td style="vertical-align:middle;text-align:center;font-size:16px; font-weight:600; ">
                        <strong>Purchase Order Details ({{ trans('WebSite.Po') }} - {{ $datas['poID'] }})</strong>
                </td>
                <td width="20%">
                    <div style="vertical-align:middle;text-align:right; font-size:14px; font-weight:600; padding-right: 5px; ">
                        <strong> Date : {{ date('d M Y',strtotime($datas['data'][0]['created_date'])) }}</strong>
                    </div>
                </td>

            </tr>
        </table>
    </div>
    <br>
    <div style="clear : both;"></div>

    <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;"cellspacing="1px" class="mainTable">
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Buyer</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! nl2br($datas['data'][0]['buyer']) !!} </td>
        </tr>
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Seller</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! nl2br($datas['data'][0]['seller']) !!}</td>
        </tr>
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Maker</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! nl2br($datas['data'][0]['maker']) !!}</td>
        </tr>
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td colspan="2" style="padding : 5px ; font-family: poppins,arialuni;" style="background: #f2f2f2; font-size:16px"><strong>&nbsp;Order Information</strong></td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.style_number') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data'][0]['style_no'] }}</td>
        </tr>
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.article_name') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data'][0]['article_name'] }}</td>
        </tr>
        @if($datas['data'][0]['article_description']!='' && $datas['data'][0]['article_description']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Article Description</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['article_description'] !!}</td>
        </tr>
        @endif
        @if(count($datas['media']['files'])>0)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Product Image</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni; height='110px'; vertical-align:middle;">
                <?php
                    if(isset($datas['media']['files'])){
                        foreach($datas['media']['files'] as $file){
                            if($file['media_type']=='ProductImage')
                                echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' height='100px' style='padding:5px; margin-top:20px' > ";
                        }
                    }
                ?>
            </td>
        </tr>
        @endif
        @if($datas['data'][0]['fabric_type']!='' && $datas['data'][0]['fabric_type']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Fabric Type</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['fabric_type'] !!}</td>
        </tr>
        @endif
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Fabric Detail</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">
                {!! $datas['data'][0]['fabric_GSM'] !!}
                @php
                $tol = explode('/',$datas['data'][0]['gsm_tolerance']);
                $tol_pls = $tol[0]??0;
                $tol_mins = $tol[1]??0;
                @endphp
                @if($tol_pls!=0 && $tol_mins!=0)
                    (+{{round($tol_pls,0)}} / -{{round($tol_mins,0)}}),
                @endif
                {{-- (+/-){!! $datas['data'][0]['gsm_tolerance'] !!}, --}}
                {!! $datas['data'][0]['fabric_composition'] !!}</td>
        </tr>
        @if($datas['data'][0]['yarn_count_type']!='' && $datas['data'][0]['yarn_count_type']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Yarn Type</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['yarn_count_type'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['total_qty']!='0' && $datas['data'][0]['total_qty']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Total Quantity</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['total_qty'] !!}
            @if($datas['data'][0]['unit_name']!='' && $datas['data'][0]['unit_name']!=NULL)
                &nbsp; {!! $datas['data'][0]['unit_name'] !!}
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
                    if(!empty($datas['sku'])){
                        ?>
                        <tr>
                            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.colors') }}</strong></td>
                            <?php
                            foreach($datas['sizes'] as $size){
                                echo '<td><strong>'.$size['name'].'</strong></td>';
                            }
                        echo '</tr>';
                            foreach($datas['colors'] as $cols){
                                echo '<tr>';
                                echo '<td><strong>'.$cols['name'].'</strong></td>';
                                foreach($datas['sizes'] as $size){
                                    $vv = getIndex($size['id'], $cols['id'], (array)$datas['sku']);
                                    echo '<td>'.$datas['sku'][$vv]['quantity'].'</td>';
                                }

                                echo '</tr>';
                            }
                    }
                    ?>
                </table>
            </td>
        </tr>
        @endif
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td colspan="2" style="padding : 5px ; font-family: poppins,arialuni;" style="background: #f2f2f2; font-size:16px;"><strong>&nbsp;Commercial Information</strong></td>
        </tr>
        @if($datas['data'][0]['price']!='0' && $datas['data'][0]['price']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Price</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data'][0]['currency'] }}  {{ $datas['data'][0]['price'] }}</td>
        </tr>
        @endif
        @if($datas['data'][0]['income_terms']!='' && $datas['data'][0]['income_terms']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.Incoterms') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data'][0]['income_terms'] }}</td>
        </tr>
        @endif
        @if($datas['data'][0]['delivery_date']!='' && $datas['data'][0]['delivery_date']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Delivery Date</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ date('d M Y',strtotime($datas['data'][0]['delivery_date'])) }} {!! $datas['data'][0]['delivery_date_type']  !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['origin_port']!='' && $datas['data'][0]['origin_port']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Origin Port</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data'][0]['origin_port'] }}</td>
        </tr>
        @endif
        @if($datas['data'][0]['destination_port']!='' && $datas['data'][0]['destination_port']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Destination Port</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data'][0]['destination_port'] }}</td>
        </tr>
        @endif
        @if($datas['data'][0]['mode_of_shipment']!='' && $datas['data'][0]['mode_of_shipment']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Mode of Shipment</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data'][0]['mode_of_shipment'] }}</td>
        </tr>
        @endif
        @if($datas['data'][0]['document_requirement']!='' && $datas['data'][0]['document_requirement']!=NULL)

        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Document Requirement</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">
                {{-- {!! $datas['data'][0]['document_requirement'] !!} --}}
                <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;"cellspacing="1px" class="mainTable">
                    <tr>
                        <td rowspan="2"></td>
                        <td colspan="2" style="text-align: center!important">No of Copies</td>
                    </tr>
                    <tr>
                        <td style="text-align: center!important">Original</td>
                        <td style="text-align: center!important">Duplicate</td>
                    </tr>
                    @php
                        $nos = explode('||',$datas['data'][0]['document_requirement']);
                    @endphp
                    @for ($i=0;$i<count($nos);$i++)
                        @php $docs = explode('|',$nos[$i]); @endphp
                        <tr>
                            <td>{{ $docs[0] }}</td>
                            <td style="text-align: center!important">{{ $docs[1] ?? 0 }}</td>
                            <td style="text-align: center!important">{{ $docs[2] ?? 0 }}</td>
                        </tr>
                    @endfor

                </table>
            </td>
        </tr>
        @endif
        @if($datas['data'][0]['hs_code']!='' && $datas['data'][0]['hs_code']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>H.S Code</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data'][0]['hs_code'] }}</td>
        </tr>
        @endif
        @if($datas['data'][0]['place_of_jurisdiction']!='' && $datas['data'][0]['place_of_jurisdiction']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Place of Jurisdiction</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data'][0]['place_of_jurisdiction'] }}</td>
        </tr>
        @endif
        @if($datas['data'][0]['penality']!='' && $datas['data'][0]['penality']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.penalty') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data'][0]['penality'] }}</td>
        </tr>
        @endif
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td colspan="2" style="padding : 5px ; font-family: poppins,arialuni;" style="background: #f2f2f2; font-size:16px"><strong>&nbsp;Testing Requirements</strong></td>
        </tr>
        @php $j=0; @endphp
        @if(!empty($datas['testings']))
            @foreach($datas['testings'] as $sam)
                @if($sam['type']=='fabric_testing')
                    @if($j==0)
                        <tr style="  font-weight:500; font-family: poppins,arialuni;">
                            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Fabric Testing</strong></td>
                            <td style="padding : 5px ; font-family: poppins,arialuni;">
                                <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;"cellspacing="1px" class="mainTable">
                                    <tr>
                                        <td>Color</td>
                                        <td>Length (in Meters)</td>
                                    </tr>
                    @endif
                                    <tr>
                                        <td>{{ $sam['color'] }}</td>
                                        <td>{{ $sam['length_qty'] }}</td>
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

        @if($datas['data'][0]['fabric_testing_agency']!='' && $datas['data'][0]['fabric_testing_agency']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Fabric Testing Agency</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! nl2br($datas['data'][0]['fabric_testing_agency']) !!}</td>
        </tr>
        @endif
        @php $j=0; @endphp
        @if(!empty($datas['testings']))
            @foreach($datas['testings'] as $sam)
                @if($sam['type']=='garment_testing')
                    @if($j==0)
                        <tr style="  font-weight:500; font-family: poppins,arialuni;">
                            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Garment Testing</strong></td>
                            <td style="padding : 5px ; font-family: poppins,arialuni;">
                                <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;"cellspacing="1px" class="mainTable">
                                    <tr>
                                        <td>Color</td>
                                        <td>Size</td>
                                        <td>Pieces</td>
                                    </tr>
                    @endif
                                    <tr>
                                        <td>{{ $sam['color'] }}</td>
                                        <td>{{ $sam['size'] }}</td>
                                        <td>{{ round($sam['length_qty'],0) }}</td>
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
        @if($datas['data'][0]['garment_testing_agency']!='' && $datas['data'][0]['garment_testing_agency']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Garment Testing Agency</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! nl2br($datas['data'][0]['garment_testing_agency']) !!}</td>
        </tr>
        @endif
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td colspan="2" style="padding : 5px ; font-family: poppins,arialuni;" style="background: #f2f2f2; font-size:16px"><strong>&nbsp;Sample Requirement</strong></td>
        </tr>
        @php $j=0; @endphp
        @if(!empty($datas['testings']))
            @foreach($datas['testings'] as $sam)
                @if($sam['type']=='fit_sample')
                    @if($j==0)
                        <tr style="  font-weight:500; font-family: poppins,arialuni;">
                            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Fit Sample</strong></td>
                            <td style="padding : 5px ; font-family: poppins,arialuni;">
                                <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;"cellspacing="1px" class="mainTable">
                                    <tr>
                                        <td>Color</td>
                                        <td>Size</td>
                                        <td>Quantity</td>
                                    </tr>
                    @endif
                                    <tr>
                                        <td>{{ $sam['color'] }}</td>
                                        <td>{{ $sam['size'] }}</td>
                                        <td>{{ round($sam['length_qty'],0) }}</td>
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
        @php $j=0; @endphp
        @if(!empty($datas['testings']))
            @foreach($datas['testings'] as $sam)
                @if($sam['type']=='testing_sample')
                    @if($j==0)
                        <tr style="  font-weight:500; font-family: poppins,arialuni;">
                            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Testing Sample</strong></td>
                            <td style="padding : 5px ; font-family: poppins,arialuni;">
                                <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;"cellspacing="1px" class="mainTable">
                                    <tr>
                                        <td>Color</td>
                                        <td>Size</td>
                                        <td>Quantity</td>
                                    </tr>
                    @endif
                                    <tr>
                                        <td>{{ $sam['color'] }}</td>
                                        <td>{{ $sam['size'] }}</td>
                                        <td>{{ round($sam['length_qty'],0) }}</td>
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
        @php $j=0; @endphp
        @if(!empty($datas['testings']))
            @foreach($datas['testings'] as $sam)
                @if($sam['type']=='pp_sample')
                    @if($j==0)
                        <tr style="  font-weight:500; font-family: poppins,arialuni;">
                            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>PP Sample</strong></td>
                            <td style="padding : 5px ; font-family: poppins,arialuni;">
                                <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;"cellspacing="1px" class="mainTable">
                                    <tr>
                                        <td>Color</td>
                                        <td>Size</td>
                                        <td>Quantity</td>
                                    </tr>
                    @endif
                                    <tr>
                                        <td>{{ $sam['color'] }}</td>
                                        <td>{{ $sam['size'] }}</td>
                                        <td>{{ round($sam['length_qty'],0) }}</td>
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
        @php $j=0; @endphp
        @if(!empty($datas['testings']))
            @foreach($datas['testings'] as $sam)
                @if($sam['type']=='shipment_sample')
                    @if($j==0)
                        <tr style="  font-weight:500; font-family: poppins,arialuni;">
                            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>Shipment Sample</strong></td>
                            <td style="padding : 5px ; font-family: poppins,arialuni;">
                                <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;"cellspacing="1px" class="mainTable">
                                    <tr>
                                        <td>Color</td>
                                        <td>Size</td>
                                        <td>Quantity</td>
                                    </tr>
                    @endif
                                    <tr>
                                        <td>{{ $sam['color'] }}</td>
                                        <td>{{ $sam['size'] }}</td>
                                        <td>{{ round($sam['length_qty'],0) }}</td>
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
        @if($datas['data'][0]['additional_information']!='' && $datas['data'][0]['additional_information']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td colspan="2" style="padding : 5px ; font-family: poppins,arialuni;" style="background: #f2f2f2; font-size:16px"><strong>&nbsp;Additional Information</strong></td>
        </tr>
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;" colspan="2">{!! $datas['data'][0]['additional_information'] !!}</td>
        </tr>
        @endif
    </table>
    <table width="100%" style="border: none;font-family: poppins,arialuni;"cellspacing="1px" >
        <tr>
            <td colspan="3" style="min-height: 150px">&nbsp;<br>&nbsp;<br>&nbsp;</td>
        </tr>
        <tr>
            <td style="padding : 5px ; font-family: poppins,arialuni;">
                <strong>&nbsp;&nbsp;&nbsp;Buyer <br>
                Seal & Sign</strong>
            </td>
            <td style="padding : 5px ; font-family: poppins,arialuni;text-align:center">
                <strong>Seller <br>
                Seal & Sign</strong>
            </td>
            <td style="padding : 5px ; font-family: poppins,arialuni;text-align:right">
                <strong>Maker &nbsp;&nbsp;&nbsp;<br>
                Seal & Sign</strong>
            </td>
        </tr>
    </table>
    <footer>
        <script type="text/php">
            if (isset($pdf)) {
            $font = $fontMetrics->getFont("Arial", "bold");
            $pdf->page_text(35, 805, "PO-{{ $datas['poID'] }}    {{ date('d M Y',strtotime($datas['data'][0]['created_date'])) }}", $font, 10, array(0, 0, 0));
            $pdf->page_text(525, 805, "Page {PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
            }
        </script>
    </footer>
</body>
</html>
<?php
function getIndex($needle, $haystack, $array){
    foreach($array as $key => $value){
        if(is_array($value) && $value['size_id'] == $needle && $value['color_id'] == $haystack)
              return $key;
    }
    return 0;
}
//exit;
?>





