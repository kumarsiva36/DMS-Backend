<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OTP Mail</title>
    {{-- <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins');
        .wrapper{width:40%}
        @media(max-width:768px)
        {
            .wrapper{width:100%}
        }
        body div p span a{
            /* font-family: 'Poppins' !important; */
            font-family: 'Roboto', sans-serif !important;
        }
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
            {{-- <img src="{{ asset('images/DMS-Logo.png') }}" width="125px" style="margin-bottom: 5px;"> --}}
          </div>
          <div style="border:1px solid #eee; padding: 0px 10px 05px 10px;  ">
            <p style="font-size:18px;color: #188676; font-weight: 600">{{ trans('WebSite.Dear',['user'=>trim($name)]) }},</p>
            <div>
                <div style="margin-top: 15px;">
                    <img src="{{ $message->embed(public_path().'/images/OtpImage.png') }}" class="center">
                    {{-- <img src="{{ asset('images/OtpImage.png') }}" class="center"> --}}
                </div>
                <div style="font-weight: 600; color: #188676;text-align:center; font-size:20px; margin-bottom:10px;">{{ trans('WebSite.OneTimePassword') }}</div>
                <div style="background-color: #E7F3F1;  width: 100%"  >
                <p style="margin: 0 auto;width: max-content;padding: 15px 05px 0  0 ;color: #000000;border-radius: 4px; font-size: 18; font-weight: 600;"> {{ trans('WebSite.VerificationOTP') }} <strong style="font-size: 24px;">  {{ $otp }}  </strong></p>
                  {{-- <h2 style="margin: 0 auto;width: max-content;padding: 0 05px 15px 0 ;color: #000000;border-radius: 4px;"></h2> --}}
                <p style="padding: 5px ;color: #000000;border-radius: 4px; font-size: 18; text-align: center;margin : 1px;">{{ trans('WebSite.securityReasonsOTP') }}</p>
                </div>
            </div>
        </div>
        <p style="font-size:0.9em; margin-bottom: 0px">{{ trans('WebSite.ThankYou') }}</p>
        <p style="margin-top: 0px; font-weight: bold;" >{{ trans('WebSite.MailSignature') }}</p>
        </div>
      </div>
</body>
</html>
