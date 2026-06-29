<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Finished Order Status Mail</title>
    {{-- <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style type="text/css">
        .wrapper{width:70%}
        @media(max-width:768px)
        {
            .wrapper{width:100%}
        }
        body {
            /* font-family: arialuni,poppins; */
            /* font-family:'poppins','arialuni','notosansjp'; */
            font-family: 'Roboto', sans-serif;
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
        }
        .tableClass span{
            margin-left: 10px;
        }
        .DataTable td{
            font-weight: 600;
            border : 1px solid #E9E9E9;
        }
        .DataTable{
            font-size: 12px;
            border : 1px solid #F7F7F7;
            border-collapse: collapse;
            margin-top : 25px;
        }
        .dot {
            height: 8px;
            width: 8px;
            border-radius: 50%;
            display: inline-block;
        }
    </style>
</head>
<body style="font-family: 'Roboto', sans-serif;">
    <div style="min-width:1000px;overflow:auto;line-height:2;">
        <div class="wrapper" style="margin:50px 0;padding:20px 0;">
          <div >
            <a href="{{ config('app.logo_url') }}" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600;">
                <img src="{{ $message->embed(public_path().'/images/DMS-Logo.png') }}" width="125px" style="margin-bottom: 5px;">
                {{-- <img src="{{ asset('images/DMS-Logo.png') }}" width="125px" style="margin-bottom: 5px;"> --}}
            </a>
          </div>
          <div style="border:1px solid #eee; padding: 0px 10px 05px 10px;  ">
            <div style="line-height:25px;margin:10px 0px 0px 5px;">
                <span style="color: #188676; font-size:18px;"><strong>{{ trans('WebSite.Dear',['user'=>trim($details['userName'])])  }},</strong></span><br>
                {{ trans('WebSite.orderStatusText') }}
            </div>
            <div style="margin-top: 35px;">
                <div>
                    <img src="{{ $message->embed(public_path().'/images/OrderStatus.png') }}" class="center">
                    {{-- <img src="{{ asset('images/OrderStatus.png') }}" class="center"> --}}
                    {{-- <img src="{{ asset('images/OrderStatus.png') }}" class="center"> --}}
                </div>
                <div style="font-weight: 600; color: #188676;text-align:center; font-size:20px; margin-bottom:35px;">{{ trans('WebSite.finishedOrderText') }}</div>
                <table style="width :100%; border-collapse: collapse;" cellpadding="5px">
                    <tr>
                        <td style="text-align: left; width: 15%;font-size:14px;font-weight:500;font-family: 'Roboto', sans-serif;">
                            {{-- Order Number  <strong>{{ $details['orderNo'] }}</strong> --}}
                            {{ trans('WebSite.Order') }}
                        </td>
                        <td style="text-align: left; width: 35%;font-size:14px;font-weight:500;font-family: 'Roboto', sans-serif;">
                            {{-- Order Number  <strong>{{ $details['orderNo'] }}</strong> --}}
                             <strong>{{ $details['orderNo'] }}</strong>
                        </td>
                        <td style="text-align: right; width: 25%;font-size:14px;font-family: 'Roboto', sans-serif;">
                            {{ trans('WebSite.Style') }}
                        </td>
                        <td style="text-align: right; width: 25%;font-size:14px;font-family: 'Roboto', sans-serif;">
                            <strong>{{ $details['styleNo'] }}</strong>
                        </td>
                    </tr>
                </table>
                @if (in_array("taskData",(array_keys($details))))
                    <table style="width :100%;font-family: 'Roboto', sans-serif;" class="DataTable">
                        <th style="font-family: 'Roboto', sans-serif;">{{ trans('WebSite.slNo') }}</th>
                        <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.taskName') }}</span></th>
                        <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.StartDate') }}</span></th>
                        <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.EndDate') }}</span></th>
                        <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.AccomplishedDate') }}</span></th>
                        <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.Status') }}</span></th>
                        <?php
                            $i=1;
                        ?>
                        @foreach ($details['taskData'] as $detail )
                            <tr>
                                <td style="text-align:center;font-family: 'Roboto', sans-serif;">{{ $i }}</td>
                                <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['taskTitle'] }}</span></td>
                                <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['startDate'] }}</span></td>
                                <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['endDate'] }}</span></td>
                                <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['accomplishedDate'] }}</span></td>
                                @if ($detail['status'] === "Completed")
                                    <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span class="dot" style="background-color:#368C0E"></span>
                                        <span>  {{ trans('WebSite.Completed') }}</span></td>
                                @endif
                                @if ($detail['status'] === "Delayed Completion")
                                    <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span class="dot" style="background-color:#E69020"></span>
                                        <span>  {{ trans('WebSite.DelCompleted') }}</spane=></td>
                                @endif
                            </tr>
                            <?php
                                $i++;
                            ?>
                        @endforeach
                    </table>
                @endif
                @if (in_array("prodData",(array_keys($details))))
                    <table style="width :100%;font-family: 'Roboto', sans-serif;" class="DataTable">
                        <?php
                            $j=1;
                        ?>
                        <th style="font-family: 'Roboto', sans-serif;">{{ trans('WebSite.slNo') }}</th>
                        <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.productionTerm') }}</span></th>
                        <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.StartDate') }}</span></th>
                        <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.EndDate') }}</span></th>
                        <th class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ trans('WebSite.Status') }}</span></th>
                        @foreach ($details['prodData'] as $detail )
                            <tr>
                                <td style="text-align:center;font-family: 'Roboto', sans-serif;">{{ $j }}</td>
                                <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['title'] }}</span></td>
                                <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['startDate'] }}</span></td>
                                <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span>{{ $detail['endDate'] }}</span></td>
                                @if ($detail['status'] === "Completed")
                                    <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span class="dot" style="background-color:#368C0E"></span>
                                        <span>  {{ trans('WebSite.Completed') }}</span></td>
                                @endif
                                @if ($detail['status'] === "Delayed Completion")
                                    <td class="tableClass" style="font-family: 'Roboto', sans-serif;"><span class="dot" style="background-color:#E69020"></span>
                                        <span>  {{ trans('WebSite.DelayedComplete') }}</span></td>
                                @endif
                            </tr>
                            <?php
                                $j++;
                            ?>
                        @endforeach
                    </table>
                @endif
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
