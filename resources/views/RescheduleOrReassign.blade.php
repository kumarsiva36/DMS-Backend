<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Reschedule/Reassign Mail</title>
    {{-- <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .wrapper{width:70%}
        @media(max-width:768px)
        {
            .wrapper{width:100%}
        }
        body{
            /* font-family: 'Poppins'; */
            font-family: 'Roboto', sans-serif;
        }
        td{
            border-bottom : 1px solid #E8E8E8;border-collapse : collapse;
        }
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
        }
        .tableClass span{
            margin-left: 10px;
        }
        #DataTable td{
            font-weight: 600;
            border : 1px solid #E9E9E9;
        }
        #DataTable{
            font-size: 12px;
            border : 1px solid #F7F7F7;
            border-collapse: collapse;
            margin-top : 25px;
        }
    </style>
</head>
<body style="font-family: 'Roboto', sans-serif;">
    <div style="min-width:1000px;overflow:auto;line-height:2">
        <div class="wrapper" style="margin:50px 0;padding:20px 0">
          <div >
            <a href="{{ config('app.logo_url') }}" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">
                <img src="{{ $message->embed(public_path().'/images/DMS-Logo.png') }}" width="125px" style="margin-bottom: 5px;">
            </a>
          </div>
          <div style="border:1px solid #eee; padding: 0px 10px 05px 10px;  ">
            <div style="line-height:25px;margin:10px 0px 0px 5px;">
                <span style="color: #188676; font-size:18px;"><strong>{{ trans('WebSite.Dear',['user'=>trim($details['userName'])])  }},</strong></span><br>
                {{ trans('WebSite.rescheduleText') }}
            </div>
            <div style="margin-top: 35px;">
                <div>
                    <img src="{{ $message->embed(public_path().'/images/TaskRescheduleWithBackGround.png') }}" class="center">
                    {{-- <img src="{{ asset('images/TaskRescheduleWithBackGround.svg') }}" class="center"> --}}
                </div>
                <div style="font-weight: 600; color: #188676;text-align:center; font-size:20px; margin-bottom:35px;">{{ trans('WebSite.taskReschedule') }}</div>
                <table style="width :100%; border-collapse: collapse;font-family: 'Roboto', sans-serif;" cellpadding="5px">
                    <tr>
                        <td style="text-align: left; width: 20%;font-size:14px;font-weight:500;font-family: 'Roboto', sans-serif;">
                            {{-- Order Number  <strong>{{ $details['orderNo'] }}</strong> --}}
                            {{ trans('WebSite.Order_only')." / ".trans('WebSite.Style') }}
                        </td>
                        <td style="text-align: left; width: 50%;font-size:14px;font-weight:500;font-family: 'Roboto', sans-serif;">
                            {{-- Order Number  <strong>{{ $details['orderNo'] }}</strong> --}}
                             <strong>{{ $details['orderNo'] }}</strong> / <strong>{{ $details['styleNo'] }}</strong>
                        </td>
                        <?php $i=0; ?>
                        @if ($details['buyer'] !== null && $i==0)
                        <?php $i++; ?>
                            <td style="text-align: left; width: 10%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                {{ trans('WebSite.Buyer') }}
                            </td>
                            <td style="text-align: right; width: 20%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                <strong>{{ $details['buyer'] }}</strong>
                            </td>
                        @endif
                        @if ($details['factory'] !== null && $i==0)
                        <?php $i++; ?>
                            <td style="text-align: left; width: 10%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                {{ trans('WebSite.Factory') }}
                            </td>
                            <td style="text-align: right; width: 20%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                <strong>{{ $details['factory'] }}</strong>
                            </td>
                        @endif
                        @if ($details['pcu'] !== null && $i==0)
                        <?php $i++; ?>
                            <td style="text-align: left; width: 10%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                {{ trans('WebSite.PCU') }}
                            </td>
                            <td style="text-align: right; width: 20%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                <strong>{{ $details['pcu'] }}</strong>
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: left; width: 20%;font-size:14px;font-weight:500;font-family: 'Roboto', sans-serif;">
                            {{-- Order Number  <strong>{{ $details['orderNo'] }}</strong> --}}
                            {{ trans('WebSite.date') }}
                        </td>
                        <td style="text-align: left; width: 50%;font-size:14px;font-weight:500;font-family: 'Roboto', sans-serif;">
                            {{-- Order Number  <strong>{{ $details['orderNo'] }}</strong> --}}
                             <strong>{{ date($details['dateFormat']) }}</strong>
                        </td>


                        @if ($details['pcu'] !== null && $i==1 )
                        <?php $i++; ?>
                            <td style="text-align: left; width: 10%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                {{ trans('WebSite.PCU') }}
                            </td>
                            <td style="text-align: right; width: 20%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                <strong>{{ $details['pcu'] }}</strong>
                            </td>
                        @endif
                        @if ($details['factory'] !== null && $i==1)
                        <?php $i++; ?>
                        <td style="text-align: left; width: 10%;font-size:14px;font-family: 'Roboto', sans-serif;">
                            {{ trans('WebSite.Factory') }}
                        </td>
                        <td style="text-align: right; width: 20%;font-size:14px;font-family: 'Roboto', sans-serif;">
                            <strong>{{ $details['factory'] }}</strong>
                        </td>
                        @endif
                        @if ($details['buyer'] !== null && $i==1)
                        <?php $i++; ?>
                            <td style="text-align: left; width: 10%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                {{ trans('WebSite.Buyer') }}
                            </td>
                            <td style="text-align: right; width: 20%;font-size:14px;font-family: 'Roboto', sans-serif;">
                                <strong>{{ $details['buyer'] }}</strong>
                            </td>
                        @endif
                    </tr>
                </table>
                <table style="width :100%;font-family: 'Roboto', sans-serif;" id="DataTable">
                    <th>{{ trans('WebSite.slNo') }}</th>
                    <th class="tableClass"><span>{{ trans('WebSite.taskName') }}</span></th>
                    <th class="tableClass"><span>{{ trans('WebSite.type') }}</span></th>
                    @if ($details['type'] === 'EndDate' || $details['type'] === 'StartDate')
                        <th class="tableClass"><span>{{ trans('WebSite.plannedDate') }}</span></th>
                        <th class="tableClass"><span>{{ trans('WebSite.rescheduledDate') }}</span></th>
                    @endif
                    @if ($details['type'] === 'Reassign')
                        <th class="tableClass"><span>{{ trans('WebSite.plannedPIC') }}</span></th>
                        <th class="tableClass"><span>{{ trans('WebSite.reassignedPIC') }}</span></th>
                    @endif
                    <th class="tableClass"><span>{{ trans('WebSite.reason') }}</span></th>
                    <tr style="font-family: 'Roboto', sans-serif;">
                        <td style="text-align:center" style="font-family: 'Roboto', sans-serif;">1</td>
                        <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $details['taskName'] }}</span></td>
                        @if ($details['type'] === 'StartDate')
                            <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.StartDate') }}</span></td>
                            <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $details['plannedDate'] }}</span></td>
                            <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $details['changedDate'] }}</span></td>
                        @endif
                        @if ($details['type'] === 'EndDate')
                            <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.EndDate') }}</span></td>
                            <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $details['plannedDate'] }}</span></td>
                            <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $details['changedDate'] }}</span></td>
                        @endif
                        @if ($details['type'] === 'Reassign')
                            <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.reassign') }}</span></td>
                            <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $details['plannedPIC'] }}</span></td>
                            <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $details['changedPIC'] }}</span></td>
                        @endif
                        <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $details['reason'] }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="background-color: #F7F7F7; font-size: 12px; font-weight: 400;text-align: center;padding: 3px;">
            {{ trans('WebSite.kindAttention') }}
        </div>
        <div style="line-height:25px;margin:10px 0px;">
            <span style="font-size:0.9em;">{{ trans('WebSite.ThankYou') }}</span><br>
            <h4 style="margin-top: 0px;" >{{ trans('WebSite.MailSignature') }}</h4>
        </div>
        </div>
      </div>
</body>
</html>
