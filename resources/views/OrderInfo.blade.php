
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Info</title>
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
        <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;" cellspacing="1px" class="mainTable">
            @if($datas['useLogo']==1 && $datas['userLogo']!='')
                <tr style="">
                    <td rowspan="2" width="15%">
                        <img src="{{ $datas['userLogo'] }}"
                        style="background-color: #FFFFFF; height: 40px;margin-left:5px;" />
                    </td>
                    <td style="width:10%;background-color: #f0efef;">
                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Order') }}</strong>
                    </td>
                    <td>
                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $datas['responses'][0]['order_no'] }}</strong>
                    </td>
                    <td style="width:10%;background-color: #f0efef;">
                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Style') }}</strong>
                    </td>
                    <td>
                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $datas['responses'][0]['style_no'] }}</strong>
                    </td>

                    <td rowspan="2" width="8%">
                        <img src="{{ public_path() . "/images/dms_small.png" }}"
                        style="background-color: #FFFFFF; height: 30px;margin-left:5px;" />
                    </td>
                </tr>
            @else
                <tr style="">
                    <td rowspan="2" style="width:15%;">
                        <img src="{{ public_path() . "/images/dms-log-with-tag.png" }}"
                        style="background-color: #FFFFFF; height: 40px;margin-left:5px;" />
                    </td>
                    <td style="width:10%;background-color: #f0efef;">
                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Order') }}</strong>
                    </td>
                    <td>
                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $datas['responses'][0]['order_no'] }}</strong>
                    </td>
                    <td style="width:10%;background-color: #f0efef;">
                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Style') }}</strong>
                    </td>
                    <td>
                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $datas['responses'][0]['style_no'] }}</strong>
                    </td>

                </tr>
            @endif
            <tr>
                <?php $td_i=0;?>
                @if ($datas['responses'][0]["factory"]!="")
                <?php $td_i++;?>
                        <td style="width:10%;background-color: #f0efef;">
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Factory') }}</strong>
                        </td>
                        <td>
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $datas['responses'][0]["factory"] }}</strong>
                        </td>
                @endif
                @if ($datas['responses'][0]["pcu"]!='')
                <?php $td_i++;?>
                        <td style="width:10%;background-color: #f0efef;">
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.PCU') }}</strong>
                        </td>
                        <td>
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $datas['responses'][0]["pcu"] }}</strong>
                        </td>
                @endif
                @if ($datas['responses'][0]["buyer"]!='')
                <?php $td_i++;?>
                        <td style="width:10%;background-color: #f0efef;">
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Buyer') }}</strong>
                        </td>
                        <td>
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $datas['responses'][0]["buyer"] }}</strong>
                        </td>
                @endif
                @if($td_i<2)
                        <td colspan="2"></td>
                @endif

            </tr>
        </table>
    </div>
    <br>
    <div style="clear : both;"></div>
    <div style="font-weight: 600; color: #188676;text-align:center; font-size:20px; margin-bottom:10px;">{{ trans('WebSite.basic_info') }}</div>
    <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;" cellspacing="1px" class="mainTable">
        @if ($datas['responses'][0]["inquiry_date"]!='')
            <tr style="  font-weight:500; font-family: poppins,arialuni;">
                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.po_date') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni;">{{ date('d M Y',strtotime($datas['responses'][0]["inquiry_date"])) }}</td>
            </tr>
        @endif
        @if ($datas['responses'][0]["article"]!='')
            <tr style="  font-weight:500; font-family: poppins,arialuni;">
                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.article') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['responses'][0]["article"] }}</td>
            </tr>
        @endif
        @if ($datas['responses'][0]["category"]!='')
            <tr style="  font-weight:500; font-family: poppins,arialuni;">
                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.category') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['responses'][0]["category"] }}</td>
            </tr>
        @endif
        @if ($datas['responses'][0]["fabric"]!='')
            <tr style="  font-weight:500; font-family: poppins,arialuni;">
                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.fabric_type') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['responses'][0]["fabric"] }}</td>
            </tr>
        @endif
        {{-- @if ($datas['responses'][0]["currency_type"]!='' || ($datas['responses'][0]["order_price"]!='' && $datas['responses'][0]["order_price"]!='0'))
            <tr style="  font-weight:500; font-family: poppins,arialuni;">
                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.order_price') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['responses'][0]["currency_type"] }} {{ $datas['responses'][0]["order_price"] }}</td>
            </tr>
        @endif --}}
        {{-- @if ($datas['responses'][0]["income_terms"]!='')
            <tr style="  font-weight:500; font-family: poppins,arialuni;">
                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.income_terms') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['responses'][0]["income_terms"] }}</td>
            </tr>
        @endif --}}
        @if ($datas['responses'][0]["total_quantity"]!='')
            <tr style="  font-weight:500; font-family: poppins,arialuni;">
                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.total_qty') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['responses'][0]["total_quantity"] }}</td>
            </tr>
        @endif
        @if ($datas['responses'][0]["order_units"]!='')
            <tr style="  font-weight:500; font-family: poppins,arialuni;">
                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.units') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['responses'][0]["order_units"] }}</td>
            </tr>
        @endif
        @if ($datas['responses'][0]["no_of_deliverys"]!='')
            <tr style="  font-weight:500; font-family: poppins,arialuni;">
                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.no_of_deliverys') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['responses'][0]["no_of_deliverys"] }}</td>
            </tr>
        @endif
        {{-- @if ($datas['responses'][0]["tolerance_perc"]!='')
            <tr style="  font-weight:500; font-family: poppins,arialuni;">
                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.tolerance_perc') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['responses'][0]["tolerance_perc"] }}%</td>
            </tr>
        @endif --}}
        @if ($datas['responses'][0]["del_date"]!='')
            <tr style="  font-weight:500; font-family: poppins,arialuni;">
                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.delivery_date') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni;">
                    @php $del_i=1; @endphp
                    @foreach ($datas['responses'] as $res)
                    {{ trans('WebSite.delivery') }} {{ $del_i }} : {{ date('d M Y',strtotime($res["del_date"])) }}<br>
                    @php $del_i++; @endphp
                    @endforeach
                </td>
            </tr>
        @endif
    </table>


        <?php
        if(!empty($datas['sku'])){
            $result_arr = array_reduce($datas['sku'], function($carry, $item){
                if(!isset($carry[$item['size_id']])){
                    $carry[$item['size_id']] = ['size_id'=>$item['size_id'],'quantity'=>$item['quantity']];
                } else {
                    $carry[$item['size_id']]['quantity'] += $item['quantity'];
                }
                return $carry;
            });
            ?>
            <div style="clear : both;"></div>
            <div style="font-weight: 600; color: #188676;text-align:center; font-size:20px; margin:10px 0px;">{{ trans('WebSite.sku_info') }}</div>
            <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;" cellspacing="1px" class="mainTable">
            <tr>
                <td style="padding : 5px ; font-family: poppins,arialuni;background-color: #f0efef;"><strong>{{ trans('WebSite.colors') }} / {{ trans('WebSite.size') }}</strong></td>
                <?php
                foreach($datas['sizes'] as $size){
                    echo '<td style="padding : 5px ; font-family: poppins,arialuni;background-color: #f0efef;"><strong>'.$size['name'].'</strong></td>';
                }
                ?>
                <td style="padding : 5px ; font-family: poppins,arialuni;background-color: #f0efef;"><strong>{{ trans('WebSite.Total') }}</strong></td>
                <?php
            echo '</tr>';
                $total_qty = 0 ;
                foreach($datas['colors'] as $cols){
                    echo '<tr>';
                    echo '<td style="padding : 5px ; font-family: poppins,arialuni;"><strong>'.$cols['name'].'</strong></td>';
                    $total_col_qty =  0;
                    foreach($datas['sizes'] as $size){
                        $vv = getIndex($size['id'], $cols['id'], (array)$datas['sku']);
                        $total_col_qty+=$datas['sku'][$vv]['quantity'];
                        $total_qty+=$datas['sku'][$vv]['quantity'];
                        echo '<td style="padding : 5px ; font-family: poppins,arialuni;">'.$datas['sku'][$vv]['quantity'].'</td>';
                    }
                    echo '<td style="padding : 5px ; font-family: poppins,arialuni;"><strong>'.$total_col_qty.'</strong></td>';
                    echo '</tr>';
                }
            echo '<tr>';
                echo '<td style="padding : 5px ; font-family: poppins,arialuni;"><strong>'.trans('WebSite.Total') .'</strong></td>';
                    foreach($datas['sizes'] as $size){
                        echo '<td style="padding : 5px ; font-family: poppins,arialuni;">'.$result_arr[$size['id']]['quantity'].'</td>';
                    }
                    echo '<td style="padding : 5px ; font-family: poppins,arialuni;"><strong>'.$total_qty.'</strong></td>';
            echo '</tr>';

            echo '</table>';
        }
        ?>




        <footer>
            <script type="text/php">
                if (isset($pdf)) {
                $font = $fontMetrics->getFont("Arial", "bold");
                $pdf->page_text(35, 805, "Order Info ({{ $datas['responses'][0]["order_no"] }})   {{ date('d M Y',strtotime($datas['responses'][0]["created_at"])) }}", $font, 10, array(0, 0, 0));
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
?>







