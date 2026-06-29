<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Task Status Mail</title>
    {{-- <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body{
            font-family: 'Roboto', sans-serif;
        }
        td{
            border-bottom : 1px solid #E8E8E8;border-collapse : collapse;vertical-align: baseline;
            padding: 5px 0;
        }
        th{
            background-color: #F7F7F7;
            font-weight: 600;
            vertical-align: middle;
            padding: 5px 0;
        }
        .tableClass{
            text-align: left;
        }
        .DataTable td{
            border : 1px solid #E9E9E9;
            padding-left: 10px;
            vertical-align: middle !important;
        }
        .DataTable th{
            border : 1px solid #E9E9E9;
            padding-left: 10px;
        }
        .DataTable{
            font-size: 12px;
            border : 1px solid #F7F7F7;
            border-collapse: collapse;
            /* margin-top : 25px !important; */
        }
        .DataTable td img{
            padding-top: 5px;
        }

    </style>
</head>
<body >

    <table width="900">
        <tr>
            <td style="border: none">
                <table width="100%">
                    <tr>
                        <td style="border: none; padding-bottom:25px;">
                            <a href="{{ config('app.logo_url') }}">
                                <img src="{{ $message->embed(public_path().'/images/DMS-Logo.png') }}" width="125" >
                            </a>
                        </td>
                    </tr>
                </table>

                <table width="100%" style="border-collapse: collapse;">
                    <tr>
                        <td style="border:1px solid #E8E8E8; padding:5px 15px 15px;">
                            <div style="line-height:25px;padding:10px 0px 0px 5px;">
                                <span style="color: #188676; font-size:18px;"><strong>{{ trans('WebSite.Dear',['user'=>trim($details_arr[0]['userName'])])  }},</strong></span><br>
                                {{ trans('WebSite.orderStatusText') }}
                            </div>
                            <br>
                            <div style="text-align: center;">
                                <img src="{{ $message->embed(public_path().'/images/OrderStatus.png') }}" width="59" height="59" style="padding-top: 25px;">
                            </div>
                            <div style="font-weight: 600; color: #188676;text-align:center; font-size:20px;">
                                @if($details_arr[0]['type']=='Production')
                                    {{ trans('WebSite.ProductionStatus') }}
                                @else
                                    {{ trans('WebSite.TaskStatus') }}
                                @endif
                            </div>
                            @php $jj=0; @endphp
                            @foreach ($details_arr as $details)
                                @if(isset($details['orderNo']))
                                    @if($jj==0)
                                        @php $height=10; @endphp
                                    @else
                                        @php $height=35; @endphp
                                    @endif
                                    <div style="margin: 0; padding:0">
                                        <table style="width: 100%;border:none;">
                                            <tr style="border:none;">
                                                <td style="border:none;" height="{{ $height }}"> </td>
                                            </tr>
                                        </table>
                                        @php $jj++; @endphp
                                        <table style="width :100%;font-family: 'Roboto', sans-serif;" class="DataTable">
                                            <tr style="background-color: #c9c7c7">
                                                <td style="text-align: left; width: 33%;font-size:14px;font-weight:500;font-family: 'Roboto', sans-serif; padding:7px">
                                                    {{ trans('WebSite.Order') }} :  <strong>{{ $details['orderNo'] }}</strong>
                                                </td>
                                                <td style="text-align: center; width: 33%;font-size:14px;font-weight:500;font-family: 'Roboto', sans-serif; padding:7px">
                                                    {{ trans('WebSite.Style') }} : <strong>{{ $details['styleNo'] }}</strong>
                                                </td>
                                                <td style="text-align: right; width: 33%;font-size:14px;font-weight:500;font-family: 'Roboto', sans-serif; padding:7px">
                                                    {{ trans('WebSite.delivery_date') }} : <strong>{{ $details['delivery_date'] }}</strong>
                                                </td>
                                            </tr>
                                        </table>
                                        @if (in_array("taskData",(array_keys($details))) || $details['type']=='Task')
                                            <table style="width :100%;font-family: 'Roboto', sans-serif;" class="DataTable">
                                                <tr style="background-color: #E9E9E9">
                                                    <th style="padding:5px 5px 5px 10px;">{{ trans('WebSite.slNo') }}</th>
                                                    <th class="tableClass" ><span>{{ trans('WebSite.taskName') }}</span></th>
                                                    <th class="tableClass" ><span>{{ trans('WebSite.StartDate') }}</span></th>
                                                    <th class="tableClass" ><span>{{ trans('WebSite.EndDate') }}</span></th>
                                                    <th class="tableClass" ><span>{{ trans('WebSite.AccomplishedDate') }}</span></th>
                                                    <th class="tableClass" ><span>{{ trans('WebSite.pic') }}</span></th>
                                                    <th class="tableClass" ><span>{{ trans('WebSite.Status') }}</span></th>
                                                    <th class="tableClass" ><span>{{ trans('WebSite.Result') }}</span></th>
                                                </tr>
                                                <?php
                                                    $i=1;
                                                ?>
                                                @foreach ($details['taskData'] as $detail )
                                                    <tr>
                                                        <td style="text-align:center;font-family: 'Roboto', sans-serif; padding:0;">{{ $i }}</td>
                                                        <td class="tableClass" ><span>{{ $detail['taskTitle'] }}</span></td>
                                                        <td class="tableClass" ><span>{{ $detail['startDate']!=''?$detail['startDate']:'---' }}</span></td>
                                                        <td class="tableClass" ><span>{{ $detail['endDate']!=''?$detail['endDate']:'---' }}</span></td>
                                                        <td class="tableClass" ><span>{{ $detail['accomplishedDate']!=''?$detail['accomplishedDate']:'---' }}</span></td>
                                                        <td class="tableClass" ><span>{{ ($detail['pic']!=' ' && $detail['pic']!=NULL)?$detail['pic']:trans('WebSite.NotAssigned') }}</span></td>
                                                        @if ($detail['accomplishedDate'] != "" && $detail['accomplishedDate'] <= $detail['endDate'])
                                                            <td style="text-align:center; background-color: #FFFFFF;padding-left:0px;">
                                                                <img src="{{ $message->embed(public_path().'/images/SmileyGreen.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                            </td>
                                                            <td class="tableClass" ><span>{{ trans('WebSite.Completed') }}</span></td>
                                                        @elseif ($detail['accomplishedDate'] != "" && $detail['accomplishedDate'] > $detail['endDate'])
                                                            <td style="text-align:center; background-color: #FFFFFF;padding-left:0px;">
                                                                <img src="{{ $message->embed(public_path().'/images/SmileyGreen.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                            </td>
                                                            <td class="tableClass" ><span>{{ trans('WebSite.DelayedComplete') }}</span></td>
                                                        @elseif ($detail['type'] === 'YetToBeStarted' && $detail['days'] > 1)
                                                            <td style="text-align:center; background-color: #FFFFFF;padding-left:0px;">
                                                                <img src="{{ $message->embed(public_path().'/images/YettoStart.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                            </td>
                                                            <td class="tableClass" ><span>{!! trans('WebSite.DaysToStart',['day'=>$detail['days']]) !!}</span></td>
                                                        @elseif ($detail['type'] === 'YetToBeStarted' && $detail['days'] === 1)
                                                            <td style="text-align:center; background-color: #FFFFFF;padding-left:0px;">
                                                                <img src="{{ $message->embed(public_path().'/images/YettoStart.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                            </td>
                                                            <td class="tableClass" ><span>{{ trans('WebSite.startsTomorrow') }}</span></td>
                                                        @elseif ($detail['type'] === 'StartsToday')
                                                            <td style="text-align:center; background-color: #FFFFFF;padding-left:0px;">
                                                                <img src="{{ $message->embed(public_path().'/images/SmileyYellow.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                            </td>
                                                            <td class="tableClass" ><span>{{ trans('WebSite.StartsToday') }}</span></td>
                                                        @elseif ($detail['days'] >0 && $detail['type'] === 'Progress')
                                                            @if ($detail['days'] == 1)
                                                                <td style="text-align:center; background-color: #FFFFFF;padding-left:0px;">
                                                                    <img src="{{ $message->embed(public_path().'/images/SmileyYellow.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                                </td>
                                                                <td class="tableClass" ><span>{{ trans('WebSite.DayRemaining') }}
                                                                    @if(config('constant.task_inprogress_percentage')==1)
                                                                        ({{ $detail['inprogress_percentage'] }}%)
                                                                    @endif
                                                                    </span></td>
                                                            @else
                                                                <td style="text-align:center; background-color: #FFFFFF;padding-left:0px;">
                                                                    <img src="{{ $message->embed(public_path().'/images/SmileyYellow.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                                </td>
                                                                <td class="tableClass" ><span>{{ trans('WebSite.DaysRemaining',["days"=>$detail['days']]) }}
                                                                    @if(config('constant.task_inprogress_percentage')==1)
                                                                        ({{ $detail['inprogress_percentage'] }}%)
                                                                    @endif
                                                                    </span></td>
                                                            @endif
                                                        @elseif ($detail['days']===0 && $detail['type'] === 'Progress')
                                                            <td style="text-align:center; background-color: #FFFFFF;padding-left:0px;">
                                                                <img src="{{ $message->embed(public_path().'/images/SmileyYellow.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                            </td>
                                                            <td class="tableClass" ><span>{{ trans('WebSite.LastDay') }}
                                                                    @if(config('constant.task_inprogress_percentage')==1)
                                                                        ({{ $detail['inprogress_percentage'] }}%)
                                                                    @endif
                                                                </span></td>
                                                        @elseif ($detail['days'] <0 && $detail['type'] === 'Progress')
                                                            @if ($detail['days']== -1)
                                                                <td style="text-align:center; background-color: #FFFFFF;padding-left:0px;">
                                                                    <img src="{{ $message->embed(public_path().'/images/2_Days_Delay.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                                </td>
                                                                <td class="tableClass" ><span>{{ abs($detail['days'])." ".trans('WebSite.dayDelay') }}
                                                                    @if(config('constant.task_inprogress_percentage')==1)
                                                                        ({{ $detail['inprogress_percentage'] }}%)
                                                                    @endif
                                                                    </span></td>
                                                            @elseif (abs($detail['days'])<= 3)
                                                                <td style="text-align:center; background-color: #FFFFFF;padding-left:0px;">
                                                                    <img src="{{ $message->embed(public_path().'/images/2_Days_Delay.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                                </td>
                                                                <td class="tableClass" ><span>{{ abs($detail['days'])." ".trans('WebSite.daysDelay') }}
                                                                    @if(config('constant.task_inprogress_percentage')==1)
                                                                        ({{ $detail['inprogress_percentage'] }}%)
                                                                    @endif
                                                                    </span></td>
                                                            @else
                                                                <td style="text-align:center; background-color: #FFFFFF;padding-left:0px;">
                                                                    <img src="{{ $message->embed(public_path().'/images/delay_bomb.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                                </td>
                                                                <td class="tableClass" ><span>{{ abs($detail['days'])." ".trans('WebSite.daysDelay') }}
                                                                    @if(config('constant.task_inprogress_percentage')==1)
                                                                        ({{ $detail['inprogress_percentage'] }}%)
                                                                    @endif
                                                                    </span></td>
                                                            @endif
                                                        @elseif ($detail['days'] === NULL)
                                                            <td style="text-align:center; background-color: #FFFFFF;padding-left:0px;">
                                                                <img src="{{ $message->embed(public_path().'/images/SmileySad.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                            </td>
                                                            <td class="tableClass" ><span>{{ trans('WebSite.NotAssigned') }}</span></td>
                                                        @endif
                                                    </tr>
                                                    <?php
                                                        $i++;
                                                    ?>
                                                @endforeach
                                            </table>
                                            <div style="margin:0px 5px">
                                                <p>{!! trans('WebSite.accessWebVersion_text') !!} <a href="{{ config('app.app_url') }}taskupdate" style="color:#188676"> {{ trans('WebSite.here') }}</a></p>
                                            </div>
                                        @endif
                                        @if (in_array("prodData",(array_keys($details))) || $details['type']=='Production')
                                            <table style="width :100%;font-family: 'Roboto', sans-serif;" class="DataTable">
                                                <?php
                                                    $j=1;
                                                ?>
                                                <th style="padding:5px 5px 5px 10px;">{{ trans('WebSite.slNo') }}</th>
                                                <th class="tableClass" ><span>{{ trans('WebSite.productionTerm') }}</span></th>
                                                <th class="tableClass" ><span>{{ trans('WebSite.StartDate') }}</span></th>
                                                <th class="tableClass" ><span>{{ trans('WebSite.EndDate') }}</span></th>
                                                <th class="tableClass" ><span>{{ trans('WebSite.Status') }}</span></th>
                                                <th class="tableClass" ><span>{{ trans('WebSite.Result') }}</span></th>
                                                @foreach ($details['prodData'] as $detail )
                                                    <tr>
                                                        <td style="text-align:center;font-family: 'Roboto', sans-serif;">{{ $j }}</td>
                                                        {{-- <td class="tableClass" ><span>{{ $detail['title'] }}</span></td> --}}
                                                        @if ($detail['title'] === 'Cutting')
                                                            <td style="padding : 5px 5px 10px 20px;">
                                                                <img src="{{ $message->embed(public_path() . '/images/BlackCutting.png') }}"
                                                                    style="margin-left: 15px;margin-top:5px" />
                                                                <p
                                                                    style="margin-top:1px;font-weight:400; font-family: 'Roboto', sans-serif;">
                                                                    <strong>{{ trans('WebSite.Cutting') }}</strong></p>
                                                            </td>
                                                        @endif
                                                        @if ($detail['title'] === 'Sewing')
                                                            <td
                                                                style="padding : 5px 5px 10px 20px;">
                                                                <img src="{{ $message->embed(public_path() . '/images/BlackSewing.png') }}"
                                                                    style="margin-left: 15px;margin-top:5px" />
                                                                <p
                                                                    style="margin-top:1px;font-weight:400; font-family: 'Roboto', sans-serif;">
                                                                    <strong>{{ trans('WebSite.Sewing') }}</strong></p>
                                                            </td>
                                                        @endif
                                                        @if ($detail['title'] === 'Packing')
                                                            <td style="padding : 5px 5px 10px 20px;">
                                                                <img src="{{ $message->embed(public_path() . '/images/BlackPacking.png') }}"
                                                                    style="margin-left: 15px;margin-top:5px" />
                                                                <p
                                                                    style="margin-top:1px;font-weight:400; font-family: 'Roboto', sans-serif;">
                                                                    <strong>{{ trans('WebSite.Packing') }}</strong></p>
                                                            </td>
                                                        @endif
                                                        <td class="tableClass" ><span>{{ $detail['startDate'] }}</span></td>
                                                        <td class="tableClass" ><span>{{ $detail['endDate'] }}</span></td>
                                                        @if (($detail['pendingQuantity'] === 0 || ($detail['updatedQuantity'] === $detail['totalQuantity'])))
                                                            @if ($detail['actualEndDate'] >= $detail['accomplishedDate'])
                                                                <td style="text-align:center; background-color: #FFFFFF; padding-left:0px;padding-top:10px;">
                                                                    <img src="{{ $message->embed(public_path() . '/images/SmileyGreen.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                                </td>
                                                                <td class="tableClass" ><span>{{ trans('WebSite.Completed') }}</span></td>
                                                            @elseif ($detail['actualEndDate'] < $detail['accomplishedDate'])
                                                                <td style="text-align:center; background-color: #FFFFFF; padding-left:0px;padding-top:10px;">
                                                                    <img src="{{ $message->embed(public_path() . '/images/SmileyGreen.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                                </td>
                                                                <td class="tableClass" ><span>{{ trans('WebSite.DelayedComplete') }}</span></td>
                                                            @endif
                                                        @elseif ($detail['type'] ==="YetToBeStarted" && $detail['delay'] > 1)
                                                            <td style="text-align:center; background-color: #FFFFFF; padding-left:0px;padding-top:10px;">
                                                                <img src="{{ $message->embed(public_path() . '/images/YettoStart.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                            </td>
                                                            <td class="tableClass" ><span>{!! trans('WebSite.DaysToStart',['day'=>$detail['delay']]) !!}</span></td>
                                                        @elseif ($detail['type'] ==="YetToBeStarted" && $detail['delay'] === 1)
                                                            <td style="text-align:center; background-color: #FFFFFF; padding-left:0px;padding-top:10px;">
                                                                <img src="{{ $message->embed(public_path() . '/images/YettoStart.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                            </td>
                                                            <td class="tableClass" ><span>{{ trans('WebSite.startsTomorrow') }}</span></td>
                                                        @elseif ($detail['type'] ==="StartsToday")
                                                            <td style="text-align:center; background-color: #FFFFFF; padding-left:0px;padding-top:10px;">
                                                                <img src="{{ $message->embed(public_path() . '/images/SmileyYellow.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                            </td>
                                                            <td class="tableClass" ><span>{{ trans('WebSite.StartsToday') }}</span></td>
                                                        @elseif ($detail['delay'] > 0 && $detail['type'] ==="Progress")
                                                            @if ($detail['delay'] == 0)
                                                                <td style="text-align:center; background-color: #FFFFFF; padding-left:0px;padding-top:10px;">
                                                                    <img src="{{ $message->embed(public_path() . '/images/SmileyYellow.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                                </td>
                                                                <td class="tableClass" ><span>{{ trans('WebSite.DayRemaining') }} {{ $detail['comp_per'] != '' ? "(".$detail['comp_per'].")" : '' }}</span></td>
                                                            @else
                                                                <td style="text-align:center; background-color: #FFFFFF; padding-left:0px;padding-top:10px;">
                                                                    <img src="{{ $message->embed(public_path() . '/images/SmileyYellow.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                                </td>
                                                                <td class="tableClass" ><span>{{ trans('WebSite.DaysRemaining',['days'=>$detail['delay']]) }} {{ $detail['comp_per'] != '' ? "(".$detail['comp_per'].")" : '' }}</span></td>
                                                            @endif
                                                        @elseif ($detail['delay'] === 0 && $detail['type'] ==="Progress")
                                                            <td style="text-align:center; background-color: #FFFFFF; padding-left:0px;padding-top:10px;">
                                                                <img src="{{ $message->embed(public_path() . '/images/SmileyYellow.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                            </td>
                                                            <td class="tableClass" ><span>{{ trans('WebSite.LastDay') }} {{ $detail['comp_per'] != '' ? "(".$detail['comp_per'].")" : '' }}</span></td>
                                                        @elseif ($detail['delay'] < 0 && $detail['type'] ==="Progress")
                                                            @if ($detail['delay'] == -1)
                                                                <td style="text-align:center; background-color: #FFFFFF; padding-left:0px;padding-top:10px;">
                                                                    <img src="{{ $message->embed(public_path() . '/images/2_Days_Delay.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                                </td>
                                                                <td class="tableClass" ><span>{{ abs($detail['delay'])." ".trans('WebSite.dayDelay') }} {{ $detail['comp_per'] != '' ? "(".$detail['comp_per'].")" : '' }}</span></td>
                                                            @elseif (abs($detail['delay']) <= 3)
                                                                <td style="text-align:center; background-color: #FFFFFF; padding-left:0px;padding-top:10px;">
                                                                    <img src="{{ $message->embed(public_path() . '/images/2_Days_Delay.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                                </td>
                                                                <td class="tableClass" ><span>{{ abs($detail['delay'])." ".trans('WebSite.daysDelay') }} {{ $detail['comp_per'] != '' ? "(".$detail['comp_per'].")" : '' }}</span></td>
                                                            @else
                                                                <td style="text-align:center; background-color: #FFFFFF; padding-left:0px;padding-top:10px;">
                                                                    <img src="{{ $message->embed(public_path() . '/images/delay_bomb.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                                </td>
                                                                <td class="tableClass" ><span>{{ abs($detail['delay'])." ".trans('WebSite.daysDelay') }} {{ $detail['comp_per'] != '' ? "(".$detail['comp_per'].")" : '' }}</span></td>
                                                            @endif
                                                        @elseif ($detail['delay'] === null)
                                                            <td style="text-align:center; background-color: #FFFFFF; padding-left:0px;padding-top:10px;">
                                                                <img src="{{ $message->embed(public_path() . '/images/SmileySad.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                            </td>
                                                            <td class="tableClass" ><span>{{ trans('WebSite.NotAssigned') }}</span></td>
                                                        @endif
                                                    </tr>
                                                    <?php
                                                        $j++;
                                                    ?>
                                                @endforeach
                                            </table>
                                            <div style="margin:0px 5px">
                                                <p>{!! trans('WebSite.accessWebVersion_text') !!} <a href="{{ config('app.app_url') }}datainput" style="color:#188676"> {{ trans('WebSite.here') }}</a></p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach


                        </td>
                    </tr>
                </table>
                <table width="100%" style="border-collapse: collapse;">
                    <tr>
                        <td style="border:1px solid #F7F7F7; padding:5px;background-color: #F7F7F7; text-align:center;font-size: 12px;">
                            {{ trans('WebSite.kindAttention') }}
                        </td>
                    </tr>
                </table>

                <div style="line-height:25px;padding:10px 0px;">
                    <span style="font-size:0.9em;">{{ trans('WebSite.ThankYou') }}</span><br>
                    <h4 style="margin-top: 0px;" >{{ trans('WebSite.MailSignature') }}</h4>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
