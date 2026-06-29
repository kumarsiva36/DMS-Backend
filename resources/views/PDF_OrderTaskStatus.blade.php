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
            padding : 5px 5px 10px;
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
@php
    $title = trans('WebSite.order_task_status');
    if(isset($details_arr[0]['type']) && $details_arr[0]['type']=="Production")
        $title = trans('WebSite.order_production_status');
@endphp
<body style="font-family: 'Roboto', sans-serif;">
    <div >
        <div class="wrapper" style="">
                <div style="line-height:25px;margin:5px 0px 0px 5px; text-align:center">
                    <span style="font-size:18px;"><strong>{{ $title }} ({{ isset($details_arr[0]['dateFormat']) ? date($details_arr[0]['dateFormat']):"-" }})</strong></span>
                </div>
                <div style="margin-top: 10px;">
                    @php $tot_count= 2; @endphp
                    @foreach ($details_arr as $details)
                    @if(isset($details['orderNo']))
                        <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni; font-size:10px;" cellspacing="1px" class="mainTable">
                            <tr style="">
                                @if($details_arr[0]['companyLogo_url']!='')
                                    <td rowspan="2" style="width:15%;">
                                        <img src="{{ $details_arr[0]['companyLogo_url'] }}" alt="company logo"
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
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $details['orderNo'] ?? '-' }}</strong> / <strong>{{ $details['styleNo'] ?? '-' }}</strong>
                                </td>
                                <?php $i=$buyer_i=$factory_i=$pcu_i=0; ?>
                                @if ($details['buyer'] !== null && $i==0)
                                <?php $i++; $buyer_i++;?>
                                    <td style="width:10%;background-color: #f0efef;">
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Buyer') }}</strong>
                                    </td>
                                    <td >
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $details['buyer'] }}</strong>
                                    </td>
                                @endif
                                @if ($details['factory'] !== null && $i==0)
                                <?php $i++;$factory_i++; ?>
                                    <td style="width:10%;background-color: #f0efef;">
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Factory') }}</strong>
                                    </td>
                                    <td>
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $details['factory'] }}</strong>
                                    </td>
                                @endif
                                @if ($details['pcu'] !== null && $i==0)
                                <?php $i++;$pcu_i++; ?>
                                    <td style="width:10%;background-color: #f0efef;">
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.PCU') }}</strong>
                                    </td>
                                    <td>
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $details['pcu'] }}</strong>
                                    </td>
                                @endif
                                @if($details_arr[0]['companyLogo_url']!='')
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
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ date($details['dateFormat'],strtotime($details['delivery_date'])) }}</strong>
                                </td>
                                @if ($details['pcu'] !== null && $i==1 && $pcu_i==0 )
                                <?php $i++; ?>
                                    <td style="width:10%;background-color: #f0efef;">
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.PCU') }}</strong>
                                    </td>
                                    <td>
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $details['pcu'] }}</strong>
                                    </td>
                                @endif
                                @if ($details['factory'] !== null && $i==1 && $factory_i==0)
                                <?php $i++; ?>
                                <td style="width:10%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Factory') }}</strong>
                                </td>
                                <td>
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $details['factory'] }}</strong>
                                </td>
                                @endif
                                @if ($details['buyer'] !== null && $i==1 && $buyer_i==0)
                                <?php $i++; ?>
                                    <td style="width:10%;background-color: #f0efef;">
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Buyer') }}</strong>
                                    </td>
                                    <td>
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ $details['buyer'] }}</strong>
                                    </td>
                                @endif
                                @if ($i==1)
                                <?php $i++; ?>
                                    <td colspan="2"></td>
                                @endif
                            </tr>

                        </table>
                        @if (in_array("taskData",(array_keys($details))) || $details['type']=='Task')
                            <table style="width :100%;font-family: 'Roboto', sans-serif;font-size:12px" class="DataTable">
                                <tr style="background-color: #E9E9E9">
                                    {{-- <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.slNo') }}</span></th> --}}
                                    <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.taskName') }}</span></th>
                                    <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.StartDate') }}</span></th>
                                    <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.EndDate') }}</span></th>
                                    <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.AccomplishedDate') }}</span></th>
                                    <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.pic') }}</span></th>
                                    <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.Status') }}</span></th>
                                    <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.Result') }}</span></th>
                                </tr>
                                <?php
                                    $i=1;
                                ?>
                                @foreach ($details['taskData'] as $detail )
                                    <tr>
                                        {{-- <td style="text-align:center;font-family: 'Roboto', sans-serif;">{{ $i }}</td> --}}
                                        <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['taskTitle'] }}</span></td>
                                        <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['startDate']!=''?$detail['startDate']:'---' }}</span></td>
                                        <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['endDate']!=''?$detail['endDate']:'---' }}</span></td>
                                        <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['accomplishedDate']!=''?$detail['accomplishedDate']:'---' }}</span></td>
                                        <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['pic']!=' '?$detail['pic']:trans('WebSite.NotAssigned') }}</span></td>
                                        @if ($detail['accomplishedDate'] != "" && $detail['accomplishedDate'] <= $detail['endDate'])
                                            <td style="text-align:center; background-color: #FFFFFF;">
                                                <img src="{{ public_path().'/images/SmileyGreen.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.Completed') }}</strong></td>
                                        @elseif ($detail['accomplishedDate'] != "" && $detail['accomplishedDate'] > $detail['endDate'])
                                            <td style="text-align:center; background-color: #FFFFFF;">
                                                <img src="{{ public_path().'/images/SmileyGreen.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.DelayedComplete') }}</strong></td>
                                        @elseif ($detail['type'] === 'YetToBeStarted' && $detail['days'] > 1)
                                            <td style="text-align:center; background-color: #FFFFFF;">
                                                <img src="{{ public_path().'/images/YettoStart.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.DaysToStart',['day'=>$detail['days']]) }}</strong></td>
                                        @elseif ($detail['type'] === 'YetToBeStarted' && $detail['days'] === 1)
                                            <td style="text-align:center; background-color: #FFFFFF;">
                                                <img src="{{ public_path().'/images/YettoStart.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.startsTomorrow') }}</strong></td>
                                        @elseif ($detail['type'] === 'StartsToday')
                                            <td style="text-align:center; background-color: #FFFFFF;">
                                                <img src="{{ public_path().'/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.StartsToday') }}</strong></td>
                                        @elseif ($detail['days'] >0 && $detail['type'] === 'Progress')
                                            @if ($detail['days'] == 1)
                                                <td style="text-align:center; background-color: #FFFFFF;">
                                                    <img src="{{ public_path().'/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                                </td>
                                                <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">
                                                    <strong>{{ trans('WebSite.DayRemaining') }}
                                                        @if(config('constant.task_inprogress_percentage')==1)
                                                            ({{ $detail['inprogress_percentage'] }}%)
                                                        @endif
                                                    <strong>
                                                </td>
                                            @else
                                                <td style="text-align:center; background-color: #FFFFFF;">
                                                    <img src="{{ public_path().'/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                                </td>
                                                <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">
                                                    <strong>{{ trans('WebSite.DaysRemaining',["days"=>$detail['days']]) }}
                                                         @if(config('constant.task_inprogress_percentage')==1)
                                                            ({{ $detail['inprogress_percentage'] }}%)
                                                        @endif
                                                    <strong>
                                                </td>
                                            @endif
                                        @elseif ($detail['days']===0 && $detail['type'] === 'Progress')
                                            <td style="text-align:center; background-color: #FFFFFF;">
                                                <img src="{{ public_path().'/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.LastDay') }}
                                                @if(config('constant.task_inprogress_percentage')==1)
                                                    ({{ $detail['inprogress_percentage'] }}%)
                                                @endif
                                                </strong></td>
                                        @elseif ($detail['days'] <0 && $detail['type'] === 'Progress')
                                            @if ($detail['days']== -1)
                                                <td style="text-align:center; background-color: #FFFFFF;">
                                                    <img src="{{ public_path().'/images/2_Days_Delay.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                                </td>
                                                <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($detail['days'])." ".trans('WebSite.dayDelay') }}
                                                    @if(config('constant.task_inprogress_percentage')==1)
                                                        ({{ $detail['inprogress_percentage'] }}%)
                                                    @endif
                                                    </strong></td>
                                            @elseif (abs($detail['days'])<= 3)
                                                <td style="text-align:center; background-color: #FFFFFF;">
                                                    <img src="{{ public_path().'/images/2_Days_Delay.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                                </td>
                                                <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($detail['days'])." ".trans('WebSite.dayDelay') }}
                                                    @if(config('constant.task_inprogress_percentage')==1)
                                                        ({{ $detail['inprogress_percentage'] }}%)
                                                    @endif
                                                    </strong></td>
                                            @else
                                                <td style="text-align:center; background-color: #FFFFFF;">
                                                    <img src="{{ public_path().'/images/delay_bomb.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                                </td>
                                                <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($detail['days'])." ".trans('WebSite.daysDelay') }}
                                                    @if(config('constant.task_inprogress_percentage')==1)
                                                        ({{ $detail['inprogress_percentage'] }}%)
                                                    @endif
                                                    </strong></td>
                                            @endif
                                        @elseif ($detail['days'] === NULL)
                                            <td style="text-align:center; background-color: #FFFFFF;">
                                                <img src="{{ public_path().'/images/SmileySad.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.NotAssigned') }}</strong></td>
                                        @endif
                                    </tr>
                                    <?php
                                        $i++;
                                    ?>
                                @endforeach
                            </table>
                        @endif
                        @if (in_array("prodData",(array_keys($details))) || $details['type']=='Production')
                            <table style="width :100%;font-family: 'Roboto', sans-serif; font-size:12px" class="DataTable">
                                <?php
                                    $j=1;
                                ?>
                                {{-- <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.slNo') }}</span></th> --}}
                                <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.productionTerm') }}</span></th>
                                <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.StartDate') }}</span></th>
                                <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.EndDate') }}</span></th>
                                <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.Status') }}</span></th>
                                <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.Result') }}</span></th>
                                @foreach ($details['prodData'] as $detail )
                                    <tr>
                                        {{-- <td style="text-align:center;font-family: 'Roboto', sans-serif;">{{ $j }}</td>
                                        <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['title'] }}</span></td> --}}
                                        @if ($detail['title'] === 'Cutting')
                                            <td style="padding : 5px 5px 10px 20px;">
                                                <img src="{{ public_path() . '/images/BlackCutting.png' }}"
                                                    style="margin-left: 15px;margin-top:5px" />
                                                <p
                                                    style="margin-top:1px;font-weight:400; font-family: poppins,arialuni,notosansjp;">
                                                    <strong>{{ trans('WebSite.Cutting') }}</strong></p>
                                            </td>
                                        @endif
                                        @if ($detail['title'] === 'Sewing')
                                            <td
                                                style="padding : 5px 5px 10px 20px;">
                                                <img src="{{ public_path() . '/images/BlackSewing.png' }}"
                                                    style="margin-left: 15px;margin-top:5px" />
                                                <p
                                                    style="margin-top:1px;font-weight:400; font-family: poppins,arialuni,notosansjp;">
                                                    <strong>{{ trans('WebSite.Sewing') }}</strong></p>
                                            </td>
                                        @endif
                                        @if ($detail['title'] === 'Packing')
                                            <td style="padding : 5px 5px 10px 20px;">
                                                <img src="{{ public_path() . '/images/BlackPacking.png' }}"
                                                    style="margin-left: 15px;margin-top:5px" />
                                                <p
                                                    style="margin-top:1px;font-weight:400; font-family: poppins,arialuni,notosansjp;">
                                                    <strong>{{ trans('WebSite.Packing') }}</strong></p>
                                            </td>
                                        @endif
                                        <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['startDate'] }}</span></td>
                                        <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['endDate'] }}</span></td>
                                        @if (($detail['pendingQuantity'] === 0 || ($detail['updatedQuantity'] === $detail['totalQuantity'])))
                                            @if ($detail['actualEndDate'] >= $detail['accomplishedDate'])
                                                <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                                                    <img src="{{ public_path() . '/images/SmileyGreen.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                                </td>
                                                <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">
                                                <strong>{{ trans('WebSite.Completed') }}</strong></td>
                                            @elseif ($detail['actualEndDate'] < $detail['accomplishedDate'])
                                                <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                                                    <img src="{{ public_path() . '/images/SmileyGreen.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                                </td>
                                                <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">
                                                <strong>{{ trans('WebSite.DelayedComplete') }}</strong></td>
                                            @endif
                                        @elseif ($detail['type'] ==="YetToBeStarted" && $detail['delay'] > 1)
                                            <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                                                <img src="{{ public_path() . '/images/YettoStart.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">
                                            <strong>{!! trans('WebSite.DaysToStart',['day'=>$detail['delay']]) !!}</strong></td>
                                        @elseif ($detail['type'] ==="YetToBeStarted" && $detail['delay'] === 1)
                                            <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                                                <img src="{{ public_path() . '/images/YettoStart.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">
                                            <strong>{{ trans('WebSite.startsTomorrow') }}</strong></td>
                                        @elseif ($detail['type'] ==="StartsToday")
                                            <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                                                <img src="{{ public_path() . '/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">
                                            <strong>{{ trans('WebSite.StartsToday') }}</strong></td>
                                        @elseif ($detail['delay'] > 0 && $detail['type'] ==="Progress")
                                            @if ($detail['delay'] == 0)
                                                <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                                                    <img src="{{ public_path() . '/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                                </td>
                                                <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">
                                                <strong>{{ trans('WebSite.DayRemaining') }} {{ $detail['comp_per'] != '' ? "(".$detail['comp_per'].")" : '' }}</strong></td>
                                            @else
                                                <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                                                    <img src="{{ public_path() . '/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                                </td>
                                                <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">
                                                <strong>{{ trans('WebSite.DaysRemaining',['days'=>$detail['delay']]) }} {{ $detail['comp_per'] != '' ? "(".$detail['comp_per'].")" : '' }}</strong></td>
                                            @endif
                                        @elseif ($detail['delay'] === 0 && $detail['type'] ==="Progress")
                                            <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                                                <img src="{{ public_path() . '/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px 5px 10px;font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.LastDay') }} {{ $detail['comp_per'] != '' ? "(".$detail['comp_per'].")" : '' }}</strong></td>
                                        @elseif ($detail['delay'] < 0 && $detail['type'] ==="Progress")
                                            @if ($detail['delay'] == -1)
                                                <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                                                    <img src="{{ public_path() . '/images/2_Days_Delay.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                                </td>
                                                <td style="padding : 5px 5px 10px;font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($detail['delay'])." ".trans('WebSite.dayDelay') }} {{ $detail['comp_per'] != '' ? "(".$detail['comp_per'].")" : '' }}</strong></td>
                                            @elseif (abs($detail['delay'])<=3)
                                                <td style="text-align:center; background-color: #ffffff;">
                                                    <img src="{{ public_path().'/images/2_Days_Delay.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                                </td>
                                                <td style="padding : 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($detail['delay'])." ".trans('WebSite.dayDelay') }}</strong></td>
                                            @else
                                                <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                                                    <img src="{{ public_path() . '/images/delay_bomb.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                                </td>
                                                <td style="padding : 5px 5px 10px;font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($detail['delay'])." ".trans('WebSite.daysDelay') }} {{ $detail['comp_per'] != '' ? "(".$detail['comp_per'].")" : '' }}</strong></td>
                                            @endif
                                        @elseif ($detail['delay'] === null)
                                            <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                                                <img src="{{ public_path() . '/images/SmileySad.svg' }}" width="{{config('constant.pdf_icon_width')}}px" />
                                            </td>
                                            <td style="padding : 5px 5px 10px;font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.NotAssigned') }}</strong></td>
                                        @endif
                                    </tr>
                                    <?php
                                        $j++;
                                    ?>
                                @endforeach
                            </table>
                        @endif
                        @if(($tot_count+1) < count($details_arr))
                        <div class="page-break"></div>
                        @endif
                        @php $tot_count++; @endphp
                    @endif
                    @endforeach

                </div>
        </div>
    </div>
    <footer>
        <script type="text/php">
            if (isset($pdf)) {
            $font = $fontMetrics->getFont("Arial", "bold");
            $pdf->page_text(35, 805, "{{ $title }} ({{ date($details_arr[0]['dateFormat']) }})", $font, 10, array(0, 0, 0));
            $pdf->page_text(525, 805, "Page {PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
            }
        </script>
    </footer>
</body>
</html>
