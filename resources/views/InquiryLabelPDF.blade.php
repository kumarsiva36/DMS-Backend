
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
        table td img{display: block; margin:10px 5px 5px 5px }
        /* table tr:nth-child(even) {background: #f6f6f6}
        table tr:nth-child(odd) {background: #ffffff} */
        @page { margin-top: 100px;margin-bottom: 60px; }
        #header { position: fixed; left: 0px; top: -80px; right: 0px;text-align: center; }
        #footer { position: fixed; left: 0px; bottom: -50px; right: 0px;text-align: center; }
    </style>

</head>
<body style="font-family: poppins,arialuni; font-size: 14px;">
    <div id="header">
        <table width="100%"  style="border-collapse: collapse;font-family: poppins,arialuni;" cellspacing="1px" class="mainTable" >
            <tr>
                <td rowspan="2">
                        <img src="{{ public_path() . "/images/dms-log-with-tag.png" }}"
                            style="background-color: #FFFFFF; height: 50px; width:130px" />
                </td>
                <td style="padding : 2px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #C4E1DD;">
                    <strong>{{ trans('WebSite.Inquiry') }}</strong>
                </td>
                <td style="padding : 2px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    IN-{{ $data['responses'][0]['id'] }}
                </td>
                <td style="padding : 2px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #C4E1DD;">
                    <strong>{{ trans('WebSite.article') }}</strong>
                </td>
                <td style="padding : 2px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ $data['responses'][0]['article'] }}
                </td>
                <td style="padding : 2px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #C4E1DD;">
                    <strong>{{ trans('WebSite.composition') }}</strong>
                </td>
                <td style="padding : 2px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ $data['responses'][0]['fabric_composition'] }}
                </td>
            </tr>
            <tr>

                <td style="padding : 2px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #C4E1DD;">
                    <strong>{{ trans('WebSite.Style') }}</strong>
                </td>
                <td style="padding : 2px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ $data['responses'][0]['style_no'] }}
                </td>
                <td style="padding : 2px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #C4E1DD;">
                    <strong>{{ trans('WebSite.category') }}</strong>
                </td>
                <td style="padding : 2px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ $data['responses'][0]['category'] }}
                </td>
                <td style="padding : 2px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #C4E1DD;">
                    <strong>{{ trans('WebSite.inq_date') }}</strong>
                </td>
                <td style="padding : 2px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ date($data['dateFormat'],strtotime($data['responses'][0]['inq_date'])) }}
                </td>

            </tr>
        </table>
    </div>
    <div id="footer">
        <table width="96%"  style="border-collapse: collapse;font-family: poppins,arialuni; font-size:12px;" cellspacing="1px" class="mainTable" >
            <tr>

                <td style="padding : 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #C4E1DD;">
                    <strong>{{ trans('WebSite.created_by') }}</strong>
                </td>
                <td style="padding : 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    <?php
                        echo ($data['user_info']['user_name']!="") ?  $data['user_info']['user_name'] : $data['user_info']['staff_name'];
                    ?>
                </td>
                <td style="padding : 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #C4E1DD;">
                    <strong>{{ trans('WebSite.created_on') }}</strong>
                </td>
                <td style="padding : 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ $data['user_info']['date_created'] }}
                </td>
                <td style="padding : 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #C4E1DD;">
                    <strong>{{ trans('WebSite.modified_by') }}</strong>
                </td>
                <td style="padding : 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    <?php
                        echo ($data['user_info']['user_name']!="") ?  $data['user_info']['user_name'] : $data['user_info']['staff_name'];
                    ?>
                </td>
                <td style="padding : 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #C4E1DD;">
                    <strong>{{ trans('WebSite.modified_on') }}</strong>
                </td>
                <td style="padding : 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ $data['user_info']['date_created'] }}
                </td>
                <td style="padding : 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #C4E1DD;">
                    <strong>{{ trans('WebSite.last_issue') }}</strong>
                </td>
                <td style="padding : 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ $data['user_info']['date_created'] }}
                </td>
            </tr>

        </table>
    </div>
    <p style="font-family: poppins,arialuni,notosansjp,poppins-semibold;font-weight:800;"><strong>{{ trans('WebSite.bill_of_materials') }}</strong></p>
        {{-- <div style="margin:0;">
            <table width="100%"  cellpadding="0" cellspacing="0" >
                <tr>
                    <td width="35%">
                        <div style="float:left;">
                            <img src="{{ public_path() . '/images/dms-log-with-tag.png' }}"
                                style="background-color: #FFFFFF; height: 70px; width:150px" />
                        </div>
                    </td>
                    <td width="25%">
                        <div style="float:right; font-size:20px; font-weight:600; color: #8C878D; ">
                            <strong>IN-{{ $data['responses'][0]['id'] }} {{ trans('WebSite.inquiry_labels') }}</strong>
                            <div
                                style=" background-color: #D1E7E4; font-size:16px; color: #178677; text-align:center;font-weight: 600;
                            padding: 1px 3px 5px;">
                                <img src="{{ public_path() . '/images/CalendarIcon.svg' }}" /> {{ date($data['dateFormat']) }}
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div> --}}

    <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;" cellspacing="1px" class="mainTable">
        @if ($data['responses'][0]['print_type']!='' || $data['responses'][0]['print_no_of_colors']!='' || $data['responses'][0]['print_size']!='')
        <tr style="background-color: #C4E1DD; color: #178677; font-weight:600;">
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" colspan="3"><strong>{{ trans('WebSite.prinrt_info') }}</strong></td>
        </tr>
        <tr style="background-color: #ffffff; color: #000000; font-weight:600;">
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="15%"><strong>{{ trans('WebSite.print_image') }}</strong></td>
            <td width="15%">
                @foreach ($data['responses'] as $index =>$response)
                    @if($response['media_type']=="PrintImage")
                        <a href="#" target="_blank">
                            <img src="{{ $data['serverURL'].$response['filepath'] }}" title="PrintImage" width="100px"><br/>
                        </a>
                    @endif
                @endforeach
            </td>
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="70%"><strong>
                {{ trans('WebSite.print_type') }} : {{ $data['responses'][0]['print_type'] }}
                @if ($data['responses'][0]['print_size']!='')
                    <br/>{{ trans('WebSite.print_size') }} : {{ $data['responses'][0]['print_size'] }} (in cm)
                @endif
                @if ($data['responses'][0]['print_no_of_colors']!='')
                    <br/>{{ trans('WebSite.no_of_colors') }} : {{ $data['responses'][0]['print_no_of_colors'] }}
                @endif
            </strong></td>
        </tr>
        @endif
        @if ($data['responses'][0]['main_lable']!='' || $data['responses'][0]['washcare_lable']!='' || $data['responses'][0]['hangtag_lable']!='' || $data['responses'][0]['barcode_lable']!='')
        <tr style="background-color: #C4E1DD; color: #178677; font-weight:600;">
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" colspan="3"><strong>{{ trans('WebSite.trims_info') }}</strong></td>
        </tr>
            @if ($data['responses'][0]['main_lable']!='')
            <tr style="background-color: #ffffff; color: #000000; font-weight:600;">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.main_lable_info') }}</strong></td>
                <td>
                    @foreach ($data['responses'] as $index =>$response)
                        @if($response['media_type']=="MainLabel")
                            <a href="#" target="_blank">
                                <img src="{{ $data['serverURL'].$response['filepath'] }}" title="MainLabel" width="100px"><br/>
                            </a>
                        @endif
                    @endforeach
                </td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>
                    {!! $data['responses'][0]['main_lable'] !!}
                </strong></td>
            </tr>
            @endif
            @if ($data['responses'][0]['washcare_lable']!='')
            <tr style="background-color: #ffffff; color: #000000; font-weight:600;">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.washcare_lable_info') }}</strong></td>
                <td>
                    @foreach ($data['responses'] as $index =>$response)
                        @if($response['media_type']=="WashCareLabel")
                            <a href="#" target="_blank">
                                <img src="{{ $data['serverURL'].$response['filepath'] }}" title="WashCareLabel" width="100px"><br/>
                            </a>
                        @endif
                    @endforeach
                </td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>
                    {!! $data['responses'][0]['washcare_lable'] !!}
                </strong></td>
            </tr>
            @endif
            @if ($data['responses'][0]['hangtag_lable']!='')
            <tr style="background-color: #ffffff; color: #000000; font-weight:600;">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.hangtag_info') }}</strong></td>
                <td>
                    @foreach ($data['responses'] as $index =>$response)
                        @if($response['media_type']=="Hangtag")
                            <a href="#" target="_blank">
                                <img src="{{ $data['serverURL'].$response['filepath'] }}" title="Hangtag" width="100px"><br/>
                            </a>
                        @endif
                    @endforeach
                </td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>
                    {!! $data['responses'][0]['hangtag_lable'] !!}
                </strong></td>
            </tr>
            @endif
            @if ($data['responses'][0]['barcode_lable']!='')
            <tr style="background-color: #ffffff; color: #000000; font-weight:600;">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.barcode_stickers_info') }}</strong></td>
                <td>
                    @foreach ($data['responses'] as $index =>$response)
                        @if($response['media_type']=="BarcodeStickers")
                            <a href="#" target="_blank">
                                <img src="{{ $data['serverURL'].$response['filepath'] }}" title="BarcodeStickers" width="100px"><br/>
                            </a>
                        @endif
                    @endforeach
                </td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>
                    {!! $data['responses'][0]['barcode_lable'] !!}
                </strong></td>
            </tr>
            @endif
        @endif
        @if ($data['responses'][0]['poly_bag_size']!='' || $data['responses'][0]['poly_bag_material']!='' || $data['responses'][0]['poly_bag_price']!=''
        || $data['responses'][0]['poly_bag_print']!='' || $data['responses'][0]['carton_bag_dimensions']!='' || $data['responses'][0]['carton_color']!=''
        || $data['responses'][0]['carton_material']!='' || $data['responses'][0]['carton_edge_finish']!='' || $data['responses'][0]['carton_mark']!='')
        <tr style="background-color: #C4E1DD; color: #178677; font-weight:600;">
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" colspan="3"><strong>{{ trans('WebSite.packing_info') }}</strong></td>
        </tr>
            @if ($data['responses'][0]['poly_bag_size']!='' || $data['responses'][0]['poly_bag_material']!='' || $data['responses'][0]['poly_bag_price']!=''
            || $data['responses'][0]['poly_bag_print']!='')
            <tr style="background-color: #ffffff; color: #000000; font-weight:600;">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.polybag_sample') }}</strong></td>
                <td>
                    @foreach ($data['responses'] as $index =>$response)
                        @if($response['media_type']=="Polybag")
                            <a href="#" target="_blank">
                                <img src="{{ $data['serverURL'].$response['filepath'] }}" title="Polybag" width="100px"><br/>
                            </a>
                        @endif
                    @endforeach
                </td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>
                    @if ($data['responses'][0]['poly_bag_size']!='')
                        {{ trans('WebSite.polybag_size') }} : {!! $data['responses'][0]['poly_bag_size'] !!} (in cm)<br/>
                    @endif
                    @if ($data['responses'][0]['poly_bag_material']!='')
                        {{ trans('WebSite.polybag_meterial') }} : {!! $data['responses'][0]['poly_bag_material'] !!} <br/>
                    @endif
                    @if ($data['responses'][0]['poly_bag_print']!='')
                        {{ trans('WebSite.polybag_print') }} : {!! $data['responses'][0]['poly_bag_print'] !!}
                    @endif

                </strong></td>
            </tr>
            @endif
            @if ($data['responses'][0]['carton_bag_dimensions']!='' || $data['responses'][0]['carton_color']!=''
            || $data['responses'][0]['carton_material']!='' || $data['responses'][0]['carton_edge_finish']!='' || $data['responses'][0]['carton_mark']!='')
            <tr style="background-color: #ffffff; color: #000000; font-weight:600;">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.carton_details') }}</strong></td>
                <td>
                    @foreach ($data['responses'] as $index =>$response)
                        @if($response['media_type']=="Carton")
                            <a href="#" target="_blank">
                                <img src="{{ $data['serverURL'].$response['filepath'] }}" title="Carton" width="100px"><br/>
                            </a>
                        @endif
                    @endforeach
                </td>
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>
                    @if ($data['responses'][0]['carton_bag_dimensions']!='')
                        {{ trans('WebSite.cartonbox_diamenition') }} : {!! $data['responses'][0]['carton_bag_dimensions'] !!} (in cm)<br/>
                    @endif
                    @if ($data['responses'][0]['carton_color']!='')
                        {{ trans('WebSite.carton_color') }} : {!! $data['responses'][0]['carton_color'] !!}<br/>
                    @endif
                    @if ($data['responses'][0]['carton_material']!='')
                        {{ trans('WebSite.carton_material') }} : {!! $data['responses'][0]['carton_material'] !!}<br/>
                    @endif
                    @if ($data['responses'][0]['carton_edge_finish']!='')
                        {{ trans('WebSite.catton_edge') }} : {!! $data['responses'][0]['carton_edge_finish'] !!}<br/>
                    @endif
                    @if ($data['responses'][0]['carton_mark']!='')
                        {{ trans('WebSite.carton_details') }} : {!! $data['responses'][0]['carton_mark'] !!}
                    @endif
                </strong></td>
            </tr>
            @endif
        @endif

    </table>

    <footer>
        <script type="text/php">
            if (isset($pdf)) {
               $font = $fontMetrics->getFont("Arial", "bold");
               $pdf->page_text(785, 568, "{PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
            }
        </script>
    </footer>

</body>
</html>
