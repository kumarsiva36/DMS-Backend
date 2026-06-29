<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Task Weekly Reminder Mail</title>
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
        }
    </style>
</head>
<body style="font-family: 'Roboto', sans-serif;">
    <table width="750">
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
                                {{ trans('WebSite.taskDueThisWeek') }}
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
                                            <td style="border:none; height:10px">
                                            </td>
                                        </tr>
                                    </table>
                                    <table style="width :100%; border-collapse: collapse;" >
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
                                            <td style="border:none; height:6px">
                                            </td>
                                        </tr>
                                    </table>
                                    <table style="width :100%;font-family: 'Roboto', sans-serif;" id="DataTable" >
                                        <tr>
                                            <th style="font-family: 'Roboto', sans-serif;">{{ trans('WebSite.slNo') }}</th>
                                            <th class="tableClass" style="font-family: 'Roboto', sans-serif;">{{ trans('WebSite.taskName') }}</th>
                                            <th class="tableClass" style="font-family: 'Roboto', sans-serif;">{{ trans('WebSite.pic') }}</th>
                                            <th class="tableClass" style="font-family: 'Roboto', sans-serif;">{{ trans('WebSite.scheduledStartDate') }}</th>
                                            <th class="tableClass" style="font-family: 'Roboto', sans-serif;">{{ trans('WebSite.scheduledEndDate') }}</th>
                                        </tr>
                                        <?php $i=1?>
                                        @foreach ($details['taskDetails'][$j] as $detail)
                                            <tr>
                                                <td style="text-align:center;font-family: 'Roboto', sans-serif;">{{ $i++ }}</td>
                                                <td class="tableClass" style="font-family: 'Roboto', sans-serif;">{{ $detail['taskName'] }}</td>
                                                <td class="tableClass" style="font-family: 'Roboto', sans-serif;">{{ $detail['pic'] }}</td>
                                                <td class="tableClass" style="font-family: 'Roboto', sans-serif;">{{ $detail['startDate'] }}</td>
                                                <td class="tableClass" style="font-family: 'Roboto', sans-serif;">{{ $detail['endDate'] }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @endfor
                                <div style="margin:0px 5px">
                                    <p>{!! trans('WebSite.accessWebVersion_text') !!} <a href="{{ config('app.app_url') }}taskupdate" style="color:#188676"> {{ trans('WebSite.here') }}</a></p>
                                </div>

                        </td>
                    </tr>
                </table>
                <table width="100%" style="border-collapse: collapse;">
                    {{-- <tr>
                        <td style="border:none; height:7px">
                        </td>
                    </tr> --}}
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


    {{-- <div style="min-width:1000px;overflow:auto;line-height:2">
        <div class="wrapper" style="margin:50px 0;padding:20px 0">
          <div >
            <a href="{{ config('app.logo_url') }}" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">
                <img src="{{ $message->embed(public_path().'/images/DMS-Logo.png') }}" width="125px" style="margin-bottom: 5px;">
            </a>
          </div>
          <div style="border:1px solid #eee; padding: 0px 10px 05px 10px;  ">
            <div style="line-height:25px;margin:10px 0px 0px 5px;">
                <span style="color: #188676; font-size:18px;"><strong>{{ trans('WebSite.Dear',['user'=>$details['userName']])  }},</strong></span><br>
                {{ trans('WebSite.taskDueThisWeek') }}
            </div>
            <div style="margin-top: 35px;">
                <div>
                    <img src="{{ $message->embed(public_path().'/images/TaskRemainderWithBackground.png') }}" class="center">
                </div>
                <div style="font-weight: 600; color: #FE9738;text-align:center; font-size:20px; margin-bottom:0px;">{{ trans('WebSite.taskReminder') }}</div>
                @for($j=0; $j < $details['count']; $j++)
                    <table style="width :100%; border-collapse: collapse;margin-top:35px;" cellpadding="5px">
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
                    <table style="width :100%;font-family: 'Roboto', sans-serif;" id="DataTable">
                        <th style="font-family: 'Roboto', sans-serif;">{{ trans('WebSite.slNo') }}</th>
                        <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.taskName') }}</span></th>
                        <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.pic') }}</span></th>
                        <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.scheduledStartDate') }}</span></th>
                        <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.scheduledEndDate') }}</span></th>
                        <?php $i=1?>
                        @foreach ($details['taskDetails'][$j] as $detail)
                            <tr>
                                <td style="text-align:center;font-family: 'Roboto', sans-serif;">{{ $i++ }}</td>
                                <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['taskName'] }}</span></td>
                                <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['pic'] }}</span></td>
                                <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['startDate'] }}</span></td>
                                <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['endDate'] }}</span></td>
                            </tr>
                        @endforeach
                    </table>
                    <br>
                @endfor
            </div>
        </div>
        <div style="background-color: #F7F7F7; font-size: 12px; text-align: center;padding: 3px;">
            {{ trans('WebSite.kindAttention') }}
        </div>
        <div style="line-height:25px;margin:10px 0px;">
            <span style="font-size:0.9em;">{{ trans('WebSite.ThankYou') }}</span><br>
            <h4 style="margin-top: 0px;" >{{ trans('WebSite.MailSignature') }}</h4>
        </div>
        </div>
    </div> --}}
</body>
</html>
