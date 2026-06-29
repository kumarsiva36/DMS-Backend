<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Task Reminder Mail</title>
    {{-- <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* .wrapper{width:50%}
        @media(max-width:768px)
        {
            .wrapper{width:100%}
        } */
        body{
            /* font-family: 'Poppins'; */
            font-family: 'Roboto', sans-serif;
        }
        td{
            border-bottom : 1px solid #E8E8E8;border-collapse : collapse;vertical-align: baseline;
            padding: 5px 0;
        }
        /* .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 59px;
            height: 59px;
        } */
        th{
            background-color: #F7F7F7;
            font-weight: 600;
            vertical-align: middle;
            padding: 5px 0;
        }
        .tableClass{
            text-align: left;
        }
        /* .tableClass span{
            margin-left: 10px;
        } */
        #DataTable td{
            border : 1px solid #E9E9E9;
            padding-left: 10px;
        }
        #DataTable th{
            border : 1px solid #E9E9E9;
            padding-left: 10px;
        }
        #DataTable{
            font-size: 12px;
            border : 1px solid #F7F7F7;
            border-collapse: collapse;
            /* margin-top : 25px !important; */
        }

    </style>
</head>
<body style="font-family: 'Roboto', sans-serif;">

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
                                <span style="color: #188676; font-size:18px;"><strong>{{ trans('WebSite.Dear',['user'=>trim($details['userName'])])  }},</strong></span><br>
                                {{ trans('WebSite.taskDue_notify') }}
                            </div>
                            <br>
                            <div style="text-align: center;">
                                <img src="{{ $message->embed(public_path().'/images/TaskRemainderWithBackground.png') }}" width="59" height="59" style="padding-top: 25px;">
                                {{-- <img src="{{ asset('images/TaskRemainderWithBackground.png') }}" class="center"> --}}
                            </div>
                            <div style="font-weight: 600; color: #FE9738;text-align:center; font-size:20px; ">{{ trans('WebSite.taskReminder') }}</div>

                            @for($j=0; $j < $details['count']; $j++)
                                <table width="100%" >
                                    <tr>
                                        <td style="border:none; height:11px">
                                        </td>
                                    </tr>
                                </table>
                                <table style="width :100%; border-collapse: collapse;" cellpadding="5px">
                                    <tr>
                                        <td style="text-align: left; width: 8%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                            {{ trans('WebSite.date') }}
                                        </td>
                                        <td style="text-align: left; width: 25%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                        :&nbsp;&nbsp;&nbsp;<strong>{{ date($details['dateFormat'])  }}</strong>
                                        </td>
                                        <?php $i=$buyer_i=$factory_i=$pcu_i=0; ?>
                                        @if ($details['buyer'][$j] !== null && $i==0)
                                        <?php $i++; $buyer_i++; ?>
                                            <td style="text-align: right; width: 25%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                                {{ trans('WebSite.Buyer') }}&nbsp;&nbsp;&nbsp;:
                                            </td>
                                            <td style="text-align: right; width: 10%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                                <strong> {{ $details['buyer'][$j] }}</strong>
                                            </td>
                                        @endif
                                        @if ($details['factory'][$j] !== null && $i==0)
                                        <?php $i++;$factory_i++; ?>
                                            <td style="text-align: right; width: 25%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                                {{ trans('WebSite.Factory') }}&nbsp;&nbsp;&nbsp;:
                                            </td>
                                            <td style="text-align: right; width: 10%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                                <strong>{{ $details['factory'][$j] }}</strong>
                                            </td>
                                        @endif
                                        @if ($details['pcu'][$j] !== null && $i==0)
                                        <?php $i++;$pcu_i++; ?>
                                            <td style="text-align: right; width: 25%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                                {{ trans('WebSite.PCU') }}&nbsp;&nbsp;&nbsp;:
                                            </td>
                                            <td style="text-align: right; width: 10%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                                <strong>{{ $details['pcu'][$j] }}</strong>
                                            </td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td style="text-align: left; width: 8%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                            {{ trans('WebSite.Order') }}
                                        </td>
                                        <td style="text-align: left; width: 25%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                            :&nbsp;&nbsp;&nbsp;<strong>{{ $details['orderNo'][$j] }}</strong>
                                        </td>
                                        @if ($details['pcu'][$j] !== null && $i==1 && $pcu_i==0 )
                                        <?php $i++; ?>
                                            <td style="text-align: right; width: 25%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                                {{ trans('WebSite.PCU') }}&nbsp;&nbsp;&nbsp;:
                                            </td>
                                            <td style="text-align: right; width: 10%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                                <strong>{{ $details['pcu'][$j] }}</strong>
                                            </td>
                                        @endif
                                        @if ($details['factory'][$j] !== null && $i==1 && $factory_i==0)
                                        <?php $i++; ?>
                                        <td style="text-align: right; width: 25%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                            {{ trans('WebSite.Factory') }}&nbsp;&nbsp;&nbsp;:
                                        </td>
                                        <td style="text-align: right; width: 10%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                            <strong>{{ $details['factory'][$j] }}</strong>
                                        </td>
                                        @endif
                                        @if ($details['buyer'][$j] !== null && $i==1 && $buyer_i==0)
                                        <?php $i++; ?>
                                            <td style="text-align: right; width: 25%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                                {{ trans('WebSite.Buyer') }}&nbsp;&nbsp;&nbsp;:
                                            </td>
                                            <td style="text-align: right; width: 10%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                            <strong>{{ $details['buyer'][$j] }}</strong>
                                            </td>
                                        @endif
                                        @if ($i==1)
                                        <?php $i++; ?>
                                            <td colspan="2"></td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td style="text-align: left; width: 8%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                            {{ trans('WebSite.Style') }}
                                        </td>
                                        <td style="text-align: left; width: 25%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                        :&nbsp;&nbsp;&nbsp;<strong>{{ $details['styleNo'][$j] }}</strong>
                                        </td>
                                        <td style="text-align: right; width: 25%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                            {{ trans('WebSite.delivery_date') }}&nbsp;&nbsp;&nbsp;:
                                        </td>
                                        <td style="text-align: right; width: 10%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                            <strong>
                                                {{ date($details['dateFormat'],strtotime($details['delivery_date'][$j])) }}
                                            </strong>
                                        </td>
                                    </tr>
                                </table>
                                <table width="100%" >
                                    <tr>
                                        <td style="border:none; height:5px">
                                        </td>
                                    </tr>
                                </table>
                                <table style="width :100%;font-family: 'Roboto', sans-serif;" id="DataTable">
                                    <th style="padding:5px 5px 5px 10px;">{{ trans('WebSite.slNo') }}</th>
                                    <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.taskName') }}</span></th>
                                    <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.StartDate') }}</span></th>
                                    <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.EndDate') }}</span></th>
                                    <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.pic') }}</span></th>
                                    <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.Status') }}</span></th>
                                    <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.Result') }}</span></th>
                                    <?php $i=1?>
                                    @foreach ($details['taskDetails'][$j] as $detail)
                                        <tr>
                                            <td style="text-align:center;font-family: 'Roboto', sans-serif; padding:0">{{ $i++ }}</td>
                                            <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['taskName'] }}</span></td>
                                            <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['start_date'] }}</span></td>
                                            <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['end_date'] }}</span></td>
                                            <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['pic'] }}</span></td>
                                            @if ($detail['status']=== "Delay")
                                                @if ($detail['noOfDays'] == -1)
                                                    <td style="text-align:center;background-color: #ffffff;">
                                                        <img src="{{ $message->embed(public_path() . '/images/2_Days_Delay.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                    </td>
                                                    <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ abs($detail['noOfDays'])." ".trans('WebSite.dayDelay') }}</span></td>
                                                @elseif ($detail['noOfDays'] == 0)
                                                    <td style="text-align:center;background-color: #ffffff;">
                                                        <img src="{{ $message->embed(public_path().'/images/SmileyYellow.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                    </td>
                                                    <td class="tableClass" style="font-family: 'Roboto', sans-serif;;"><span>{{ trans('WebSite.LastDay') }}</span></td>
                                                @elseif (abs($detail['noOfDays']) <= 3)
                                                    <td style="text-align:center;background-color: #ffffff;">
                                                        <img src="{{ $message->embed(public_path().'/images/2_Days_Delay.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                    </td>
                                                    <td class="tableClass" style="font-family: 'Roboto', sans-serif;;"><span>{{ abs($detail['noOfDays'])." ".trans('WebSite.daysDelay') }}</span></td>
                                                @else
                                                    <td style="text-align:center;background-color: #ffffff;">
                                                        <img src="{{ $message->embed(public_path() . '/images/delay_bomb.png') }}" width="{{config('constant.mail_icon_width')}}" />
                                                    </td>
                                                    <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ abs($detail['noOfDays'])." ".trans('WebSite.daysDelay') }}</span></td>
                                                @endif
                                            @endif

                                        </tr>
                                    @endforeach
                                </table>
                            @endfor

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

