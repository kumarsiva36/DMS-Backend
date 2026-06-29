<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment Confirmation Mail</title>
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
                {{-- <img src="{{ public_path().'/images/DMS-Logo.png' }}" width="125px" style="margin-bottom: 5px;"> --}}
            </a>
          </div>
          <div style="border:1px solid #eee; padding: 0px 10px 05px 10px;  ">
            <div style="line-height:25px;margin:10px 0px 0px 5px;">
                <span style="color: #188676; font-size:18px;"><strong>{{ trans('WebSite.Dear',['user'=>trim($details['name'])]) }},</strong></span><br>
                {{-- <span style="color: #188676; font-size:18px;"><strong>{{ "Dear Nomu" }},</strong></span><br> --}}
                {{-- Your order has been created successfully. Please find the order details below. --}}
            </div>
            <div style="margin-top: 35px;">
                <div >
                    <img src="{{ $message->embed(public_path().'/images/SlightGreenTickWithBackground.png') }}" class="center">
                    {{-- <img src="{{ asset('images/SlightGreenTickWithBackground.png') }}" class="center"> --}}
                </div>
                <div style="font-weight: 600; color: #17CBAA;text-align:center; font-size:18px;">
                    {{ trans('WebSite.paymentSuccessful') }} {{-- Payment Successful! --}}
                </div>
                <div style="font-weight: 600; color: #646464;text-align:center; font-size:18px;margin-bottom:15px;">
                    {{ trans('WebSite.paymentSuccessMessage') }}{{-- You have completed your payment --}}
                </div>
                <div style="font-weight: 600; color: #646464;text-align:center; font-size:12px;">
                    {{ trans('WebSite.amountPaid') }} {{-- Amount Paid --}}
                </div>
                <div style="font-weight: 600; color: #17CBAA;text-align:center; font-size:18px;margin-bottom:10px;">
                    {{ "Rs.".$details['planPrice'] }} {{-- Rs.5000 --}}
                </div>
                <table style="width :100%; border-collapse: collapse;" cellpadding="5px">
                    <tr>
                        <td style="text-align: left; width: 50%;font-size:14px;font-weight:500;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                            {{ trans('WebSite.planName') }} {{-- Plan Name --}}
                        </td>
                        <td style="text-align: right; width: 50%;font-size:14px;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                            <strong>{{ $details['planName'] }}</strong> {{-- Ultimate --}}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left; width: 50%;font-size:14px;font-weight:500;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                            {{ trans('WebSite.planType') }} {{-- Plan Type --}}
                        </td>
                        <td style="text-align: right; width: 50%;font-size:14px;border-bottom : 1px solid #E8E8E8;border-collapse : collapse;font-family: 'Roboto', sans-serif;">
                            <strong>{{ $details['planType'] }}</strong> {{-- Yearly --}}
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
