
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ trans('WebSite.SAMReport') }}</title>
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

    <div style="margin:25px 0;">
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
                        <strong>{{ trans('WebSite.SAMReport') }} </strong>
                        <div
                            style=" background-color: #D1E7E4; font-size:16px; color: #178677; text-align:center;font-weight: 600;
                        padding: 1px 3px 5px;">
                            <img src="{{ public_path() . '/images/CalendarIcon.svg' }}" /> {{ date($data['dateFormat'],strtotime($data['responses'][0]['report_date'])) }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <br>
    <div style="clear : both;"></div>
    <div style="margin: 5px 0;">
        @if (count($data['advFilter'])>0)
            <strong>{{ trans('WebSite.filter') }} : </strong>
            @if (array_key_exists("report_date",$data['advFilter']))
                <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">
                    {{ trans('WebSite.date').": ".$data['advFilter']['report_date'] }}</strong>
            @endif
            @if (array_key_exists("shift_id",$data['advFilter']))
                <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">
                    {{ trans('WebSite.shift').": ".$data['responses'][0]['shift'] }}</strong>
            @endif
            @if (array_key_exists("style_no",$data['advFilter']))
                <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">
                    {{ trans('WebSite.style').": ".$data['advFilter']['style_no'] }}</strong>
            @endif
            @if (array_key_exists("unit_id",$data['advFilter']))
                <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">
                    {{ trans('WebSite.unit').": ".$data['responses'][0]['unit'] }}</strong>
            @endif
            @if (array_key_exists("line_no_id",$data['advFilter']))
                <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">
                    {{ trans('WebSite.lineNo').": ".$data['responses'][0]['line_no'] }}</strong>
            @endif
            @if (array_key_exists("supervisor_id",$data['advFilter']))
                <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">
                    {{ trans('WebSite.supervisor').": ".$data['responses'][0]['supervisor'] }}</strong>
            @endif

        @endif
    </div>
    <div style="clear : both;"></div>
    <?php
    $totQty=$no_of_hrs=$total_eff=0;
    ?>
    <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;margin-top:5px;"
    cellspacing="1px" class="mainTable">
    <tr style="background-color: #C4E1DD; color: #178677; font-weight:600;">
        <td style="padding : 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.unit') }}</strong></td>
        <td style="padding : 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.lineNo') }}</strong></td>
        <td style="padding : 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.shift') }}</strong></td>
        <td style="padding : 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.Style') }}</strong></td>
        <td style="padding : 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.supervisor') }}</strong></td>
        <td style="padding : 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.sam') }}</strong></td>
        <td style="padding : 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.tailors') }} / <br>{{ trans('WebSite.helpers') }}</strong></td>
        <td style="padding : 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.tgt') }}</strong></td>
        @foreach ($data['timings'] as $timings)
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ $timings }}</strong></td>
        @endforeach
        <td style="padding : 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.total') }}</strong></td>
        <td style="padding : 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.eff') }}</strong></td>
    </tr>
    @foreach ($data['responses'] as $index=>$response)
        <tr>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ $response['unit'] }}</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ $response['line_no'] }}</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ $response['shift'] }}</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ $response['style_no'] }}</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ $response['supervisor'] }}</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ $response['sam'] }}</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ $response['tailors'] }}/{{ $response['helpers'] }}</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp; background-color:#4c8b43;color:#ffffff;">{{ $response['target'] }}</td>
            @foreach ($data['timings'] as $timings)
                @if(isset($response[$timings]))
                    @php
                        $totQty+=(int)$response[$timings]['qty'];
                        $no_of_hrs+=1;
                        $color=( $response[$timings]['qty'] < $response['target'] ) ? (($response[$timings]['qty'] >= ($response['target']*(80/100)))?'#f79d69':'#f5453e' ) : '#9CC450';
                    @endphp
                    @endphp
                    <td style="padding : 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color:{{ $color }};color:#ffffff;"><strong>
                        {{ $response[$timings]['qty'] }}</strong>
                    </td>
                @else
                    <td style="padding : 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>-</strong></td>
                @endif
            @endforeach
            @php
                $working_hrs = $no_of_hrs > 4 ? ($no_of_hrs-$response['break']) : $no_of_hrs;
                $act_tot = $working_hrs*($response['target']);
                $eff = round((($totQty*$response['sam'])/(60*($working_hrs)*$response['tailors']))*100);
                $total_eff+=$eff;
                //$tot_bgclor = ($totQty < $act_tot) ? '#f5453e' : '#4c8b43';
                $tot_bgclor = ($totQty < $act_tot) ? (($totQty < ($act_tot)*(80/100)) ? '#f5453e':'#f79d69') : '#9CC450';
                $eff_bgclor = ($eff < 100) ? (($eff < 80) ? '#f5453e':'#f79d69') : '#9CC450';
            @endphp
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;background-color:{{ $tot_bgclor }};color:#ffffff;">{{ $totQty }}</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;background-color:{{ $eff_bgclor }};color:#ffffff;">{{ $eff }}%</td>
            @php
                $totQty=$no_of_hrs=0;
            @endphp
        </tr>
    @endforeach
        <tr>
            <td colspan="20">&nbsp;</td>
        </tr>
        <tr style="background-color: #fbfdd8">
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp; font-size:15px; text-align:right;" colspan="5">TOTAL</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ $data['calculations']['avg_sam'] }}</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ $data['calculations']['total_tailors'] }}/{{ $data['calculations']['total_helpers'] }}</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;background-color:#4c8b43;color:#ffffff;">{{ $data['calculations']['total_target'] }} </td>
            @foreach ($data['timings'] as $timings)
                @php
                    $target = $data['calculations']['total_target'];
                    $production = array_sum($data['calculations']['time_arr'][$timings]);
                    $color=( $production < $target ) ? (($production >= ($target*(80/100)))?'#f79d69':'#f5453e' ) : '#9CC450';
                @endphp
                <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;background-color:{{$color}};color:#ffffff;">{{ $production }} </td>
            @endforeach
            @php
                $teff = round(($total_eff/count($data['responses'])),0);
                $teff_bgclor = ($teff < 100) ? (($teff < 80) ? '#f5453e':'#f79d69') : '#9CC450';
            @endphp
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;background-color:{{$teff_bgclor}};color:#ffffff;">{{ $data['calculations']['total_production'] }} </td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;background-color:{{$teff_bgclor}};color:#ffffff;">{{ $teff }}%</td>
        </tr>
        <tr style="background-color: #f2f2f2">
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp; font-size:15px; text-align:right;" colspan="8">Total Eff %</td>
            @foreach ($data['timings'] as $timings)
                @php
                    $target = $data['calculations']['total_target'];
                    $production = array_sum($data['calculations']['time_arr'][$timings]);
                    $teff = round(($production/$target)*100,0);
                    $teff_bgclor = ($teff < 100) ? (($teff < 80) ? '#f5453e':'#f79d69') : '#9CC450';
                @endphp
                <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;background-color:{{$teff_bgclor}};color:#ffffff;">{{$teff}}%</td>
            @endforeach
            {{-- <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ $data['calculations']['total_production'] }} </td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ round(($total_eff/count($data['responses'])),0) }}</td> --}}

        </tr>
    </table>
    <br/>
    <table width="55%" style="border-collapse: collapse;font-family: poppins,arialuni;margin-top:5px;"
    cellspacing="1px" class="mainTable">
        <tr style="background-color: #C4E1DD; color: #178677; font-weight:600;">
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">Manpower Cost</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">Earned Cost</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">Profit/Loss</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">Manpower Cost/pc</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">Earned Cost/pc</td>
        </tr>
        <tr>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ $data['manpower_cost'] }}</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ $data['earned_cost'] }}</td>
            @php
                $profit = (int)$data['earned_cost'] - (int)$data['manpower_cost'];
                $color=( $profit < 0 ) ? '#f5453e' : '#4c8b43';
                $pc_color=( round($data['earned_cost']/$data['calculations']['total_production'] < $data['manpower_cost']/$data['calculations']['total_production'] )) ? '#f5453e' : '#4c8b43';
            @endphp
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;background-color:{{ $color }};color:#ffffff;">{{ $profit }}</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ round($data['manpower_cost']/$data['calculations']['total_production'],2) }}</td>
            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;background-color:{{ $pc_color }};color:#ffffff;">{{ round($data['earned_cost']/$data['calculations']['total_production'],2) }}</td>
        </tr>
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
