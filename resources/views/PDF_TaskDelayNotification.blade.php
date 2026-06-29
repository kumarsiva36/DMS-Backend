<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Status Mail</title>
    {{-- <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
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
        .wrapper{width:100%}
        body{
            font-family: 'Poppins';
        }
        /* td{
            border-bottom : 1px solid #E8E8E8;border-collapse : collapse;
        } */
        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 59px;
            height: 59px;
        }
        th{
            background-color: #F7F7F7;
            font-weight: 400;
        }
        .tableClass{
            text-align: left;
            padding : 5px 5px 10px 5px;
        }
        /* .tableClass span{
            margin-left: 10px;
        } */
        .DataTable td{
            font-weight: 600;
            border : 1px solid #E9E9E9;
        }
        .DataTable{
            font-size: 12px;
            border : 1px solid #F7F7F7;
            border-collapse: collapse;
            margin-top : 5px;
        }
        .dot {
            height: 8px;
            width: 8px;
            border-radius: 50%;
            display: inline-block;
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
    </style>
</head>
<body style="font-family: 'Roboto', sans-serif;">
    <div >
        <div class="wrapper" style="">
                <div style="line-height:25px;margin:5px 0px 0px 5px; text-align:center">
                    <span style="font-size:18px;"><strong>{{ trans('WebSite.delay_order_status') }} ({{ isset($details['dateFormat']) ? date($details['dateFormat']):date('d M Y') }})</strong></span>
                </div>
                <div style="margin-top: 10px;">
                    @php $tot_count= 0; @endphp
                    {{-- @foreach ($details_arr as $details) --}}
                    @for($j=0; $j < $details['count']; $j++)
                    @if(isset($details['orderNo'][$j]))
                        <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni; font-size:10px;" cellspacing="1px" class="mainTable">
                            <tr style="">
                                @if($details['companyLogo_url']!='')
                                    <td rowspan="2" style="width:15%;">
                                        <img src="{{ $details['companyLogo_url'] }}" alt="company logo"
                                        style="background-color: #FFFFFF; height: 40px;margin-left:5px;" />
                                    </td>
                                @else
                                    <td rowspan="2" style="width:15%;">
                                        <img src="{{ public_path() . "/images/dms-log-with-tag.png" }}"
                                        style="background-color: #FFFFFF; height: 40px;margin-left:5px;" />
                                    </td>
                                @endif
                                <td style="width:20%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Order_only').' / '.trans('WebSite.Style') }}</strong>
                                </td>
                                <td>
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $details['orderNo'][$j] ?? '-' }}</strong> / <strong>{{ $details['styleNo'][$j] ?? '-' }}</strong>
                                </td>
                                <?php $i=$buyer_i=$factory_i=$pcu_i=0; ?>
                                @if ($details['buyer'][$j] !== null && $i==0)
                                <?php $i++; $buyer_i++;?>
                                    <td style="width:10%;background-color: #f0efef;">
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Buyer') }}</strong>
                                    </td>
                                    <td >
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $details['buyer'][$j] }}</strong>
                                    </td>
                                @endif
                                @if ($details['factory'][$j] !== null && $i==0)
                                <?php $i++;$factory_i++; ?>
                                    <td style="width:10%;background-color: #f0efef;">
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Factory') }}</strong>
                                    </td>
                                    <td>
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $details['factory'][$j] }}</strong>
                                    </td>
                                @endif
                                @if ($details['pcu'][$j] !== null && $i==0)
                                <?php $i++;$pcu_i++; ?>
                                    <td style="width:10%;background-color: #f0efef;">
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.PCU') }}</strong>
                                    </td>
                                    <td>
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $details['pcu'][$j] }}</strong>
                                    </td>
                                @endif
                                @if($details['companyLogo_url']!='')
                                    <td rowspan="2" width="8%">
                                        <img src="{{ public_path() . "/images/dms_small.png" }}"
                                        style="background-color: #FFFFFF; height: 30px;margin-left:5px;" />
                                    </td>
                                @endif
                            </tr>
                            <tr>
                                <td style="width:20%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.delivery_date') }}</strong>
                                </td>
                                <td >
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ date($details['dateFormat'],strtotime($details['delivery_date'][$j])) }}</strong>
                                </td>
                                @if ($details['pcu'][$j] !== null && $i==1 && $pcu_i==0 )
                                <?php $i++; ?>
                                    <td style="width:10%;background-color: #f0efef;">
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.PCU') }}</strong>
                                    </td>
                                    <td>
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $details['pcu'][$j] }}</strong>
                                    </td>
                                @endif
                                @if ($details['factory'][$j] !== null && $i==1 && $factory_i==0)
                                <?php $i++; ?>
                                <td style="width:10%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Factory') }}</strong>
                                </td>
                                <td>
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $details['factory'][$j] }}</strong>
                                </td>
                                @endif
                                @if ($details['buyer'][$j] !== null && $i==1 && $buyer_i==0)
                                <?php $i++; ?>
                                    <td style="width:10%;background-color: #f0efef;">
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Buyer') }}</strong>
                                    </td>
                                    <td>
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $details['buyer'][$j] }}</strong>
                                    </td>
                                @endif
                                @if ($i==1)
                                <?php $i++; ?>
                                    <td colspan="2"></td>
                                @endif
                            </tr>

                        </table>
                        <table style="width :100%;font-family: 'Roboto', sans-serif; font-size:12px; margin-top:15px" class="DataTable">
                            {{-- <th style="font-family: 'Roboto', sans-serif;">{{ trans('WebSite.slNo') }}</th> --}}
                            <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.taskName') }}</span></th>
                            <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.StartDate') }}</span></th>
                            <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.EndDate') }}</span></th>
                            <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.pic') }}</span></th>
                            <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.Status') }}</span></th>
                            <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.Result') }}</span></th>
                            <?php $i=1?>
                            @foreach ($details['taskDetails'][$j] as $detail)
                                <tr>
                                    {{-- <td style="text-align:center;font-family: 'Roboto', sans-serif;">{{ $i++ }}</td> --}}
                                    <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['taskName'] }}</span></td>
                                    <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['start_date'] }}</span></td>
                                    <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['end_date'] }}</span></td>
                                    <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ ($detail['pic']!=' ' && $detail['pic']!=NULL)?$detail['pic']:trans('WebSite.NotAssigned') }}</span></td>
                                    @if ($detail['status']=== "Delay")
                                        @if ($detail['noOfDays'] == -1)
                                            <td style="text-align:center; background-color: #ffffff; font-family: poppins,arialuni,notosansjp;">
                                                <img src="{{ public_path() . '/images/2_Days_Delay.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px;font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($detail['noOfDays'])." ".trans('WebSite.dayDelay') }}</strong></td>
                                        @elseif ($detail['noOfDays'] == 0)
                                            <td style="text-align:center; background-color: #ffffff;">
                                                <img src="{{ public_path().'/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.LastDay') }}</strong></td>
                                        @elseif (abs($detail['noOfDays'])<=3)
                                            <td style="text-align:center; background-color: #ffffff;">
                                                <img src="{{ public_path().'/images/2_Days_Delay.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($detail['noOfDays'])." ".trans('WebSite.daysDelay') }}</strong></td>
                                        @else
                                            <td style="text-align:center; background-color: #ffffff; font-family: poppins,arialuni,notosansjp;">
                                                <img src="{{ public_path() . '/images/delay_bomb.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px;font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($detail['noOfDays'])." ".trans('WebSite.daysDelay') }}</strong></td>
                                        @endif
                                    @endif
                                </tr>
                            @endforeach
                        </table>

                        @if(($tot_count+1) < $details['count'])
                        <div class="page-break"></div>
                        @endif
                    @endif
                    @php $tot_count++; @endphp
                    @endfor
                    {{-- @endforeach --}}

                </div>
        </div>
    </div>
    <footer>
        <script type="text/php">
            if (isset($pdf)) {
            $font = $fontMetrics->getFont("Arial", "bold");
            $pdf->page_text(35, 805, "{{ trans('WebSite.delay_order_status') }} ({{ date($details['dateFormat']) }})", $font, 10, array(0, 0, 0));
            $pdf->page_text(525, 805, "Page {PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
            }
        </script>
    </footer>
</body>
</html>
