<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Creation Mail</title>
    {{-- <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .wrapper{width:50%}
        @media(max-width:768px)
        {
            .wrapper{width:100%}
        }
        body{
            /* font-family: 'Poppins'; */
            font-family: 'Roboto', sans-serif;
        }
        /* td,tr{
            border-bottom : 1px solid #E8E8E8;border-collapse : collapse;
        } */
        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 59px;
            height: 59px;
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
                <span style="color: #188676; font-size:18px;"><strong>{{ trans('WebSite.Dear',['user'=>trim($details['created_by'])]) }},</strong></span><br>
                {{-- Your order has been created successfully. Please find the order details below. --}}
            </div>
            <div style="margin-top: 35px;">
                <div >
                    <img src="{{ $message->embed(public_path().'/images/TickWithBackGround.png') }}" class="center">
                    {{-- <img src="{{ $message->embed(asset('images/TickWithBackGround.png')) }}" class="center"> --}}
                </div>
                <div style="font-weight: 600; color: #188676;text-align:center; font-size:20px;margin-bottom:35px;">{{ trans('WebSite.orderCreatedText') }}</div>
                <table style="width :100%; border-collapse: collapse;" cellpadding="5px">
                    <tr>
                        <td style="text-align: left; width: 50%;font-size:14px;font-weight:500;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                            {{ trans('WebSite.Order') }}
                        </td>
                        <td style="text-align: right; width: 50%;font-size:14px;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                            <strong>{{ $details['orderNo'] }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left; width: 50%;font-size:14px;font-weight:500;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                            {{ trans('WebSite.Style') }}
                        </td>
                        <td style="text-align: right; width: 50%;font-size:14px;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                            <strong>{{ $details['styleNo'] }}</strong>
                        </td>
                    </tr>
                    @if($details['factory']!='')
                        <tr>
                            <td style="text-align: left; width: 50%;font-size:14px;font-weight:500;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                                {{ trans('WebSite.Factory') }}
                            </td>
                            <td style="text-align: right; width: 50%;font-size:14px;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                                <strong>{{ $details['factory'] }}</strong>
                            </td>
                        </tr>
                    @endif
                    @if($details['buyer']!='')
                        <tr>
                            <td style="text-align: left; width: 50%;font-size:14px;font-weight:500;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                                {{ trans('WebSite.Buyer') }}
                            </td>
                            <td style="text-align: right; width: 50%;font-size:14px;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                                <strong>{{ $details['buyer'] }}</strong>
                            </td>
                        </tr>
                    @endif
                    @if($details['buyer']!='')
                        <tr>
                            <td style="text-align: left; width: 50%;font-size:14px;font-weight:500;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                                {{ trans('WebSite.PCU') }}
                            </td>
                            <td style="text-align: right; width: 50%;font-size:14px;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                                <strong>{{ $details['pcu'] }}</strong>
                            </td>
                        </tr>
                    @endif
                    @if($details['fabric']!='')
                        <tr>
                            <td style="text-align: left; width: 50%;font-size:14px;font-weight:500;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                                {{ trans('WebSite.fabric_type') }}
                            </td>
                            <td style="text-align: right; width: 50%;font-size:14px;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                                <strong>{{ $details['fabric'] }}</strong>
                            </td>
                        </tr>
                    @endif
                    @if($details['article']!='')
                        <tr>
                            <td style="text-align: left; width: 50%;font-size:14px;font-weight:500;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                                {{ trans('WebSite.article') }}
                            </td>
                            <td style="text-align: right; width: 50%;font-size:14px;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                                <strong>{{ $details['article'] }}</strong>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="text-align: left; width: 50%;font-size:14px;font-weight:500;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                            {{ trans('WebSite.createdDate') }}
                        </td>
                        <td style="text-align: right; width: 50%;font-size:14px;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                            <strong>{{ $details['created_at'] }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left; width: 50%;font-size:14px;font-weight:500;font-family: 'Roboto', sans-serif;">
                            {{ trans('WebSite.createdBy') }}
                        </td>
                        <td style="text-align: right; width: 50%;font-size:14px;font-family: 'Roboto', sans-serif;">
                            <strong>{{ $details['created_by'] }}</strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <p style="font-size:0.9em; margin-bottom: 0px">{{ trans('WebSite.ThankYou') }}</p>
        <h4 style="margin-top: 0px;" >{{ trans('WebSite.MailSignature') }}</h4>
        </div>
      </div>
</body>
</html>
