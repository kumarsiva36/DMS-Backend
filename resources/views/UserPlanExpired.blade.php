<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User Plan Expired</title>
    {{-- <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .wrapper {
            width: 40%
        }

        @media(max-width:768px) {
            .wrapper {
                width: 100%
            }
        }

        body {
            /* font-family: 'Poppins'; */
            font-family: 'Roboto', sans-serif;
        }

        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 125px;
            height: 59px;
        }

        .btnPlan {
            width: 110px;
            border: 1px solid #009688;
            color: #009688;
            border-radius: 20px;
            padding: 10px;
            background-color: #FFFFFF;
            /* cursor: pointer; */
        }

        .btnContact {
            width: 110px;
            color: #7A7A7A;
            border: 0px;
            border-radius: 20px;
            padding: 10px;
            background-color: #EBEBEB;
            margin-left: 5px;
            cursor: pointer !important;
        }

        a {
            text-decoration: none;
        }
    </style>
</head>
<body style="font-family: 'Roboto', sans-serif;">
    <div style="min-width:1000px;overflow:auto;line-height:2">
        <div class="wrapper" style="margin:50px 0;padding:20px 0">
            <div>
                <a href="{{ config('app.logo_url') }}"
                    style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">
                    <img src="{{ $message->embed(public_path().'/images/DMS-Logo.png') }}" width="125px"
                        style="margin-bottom: 5px;">
                    {{-- <img src="{{ asset('/images/DMS-Logo.png') }}" style="margin-bottom: 5px;"> --}}
                </a>
            </div>
            <div style="border:1px solid #eee; padding: 0px 10px 05px 10px;">
                <div style="line-height:25px;margin:10px 0px 0px 5px;">
                    <span style="color: #188676; font-size:18px;"><strong>{{ trans('WebSite.Dear',['user'=>trim($details['userName'])])  }},</strong></span>
                </div>
                <div style="margin-top: 35px;">
                    <div>
                        <img src="{{ $message->embed(public_path().'/images/ExpiredWording.png') }}" class="center"
                            width="125px">
                        {{-- <img src="{{ asset('images/ExpiredWording.png') }}" class="center" width="125px"> --}}
                    </div>
                    <div style="font-weight: 600; color: #CD3120;text-align:center; font-size:20px;margin-top: 10px;">
                        {{ trans('WebSite.planExpired') }}</div>
                    <div style="margin:0px;text-align:center;">
                        <p style="margin:0px; font-size:12px;">{{ trans('WebSite.planReactivation') }}</p>
                    </div>
                    <div style="text-align: center; margin: 10px 0px;">
                        <a href="{{ config('app.app_url').'pricing' }}">
                            <input type="button" value="{{ trans('WebSite.viewPlan') }}" class="btnPlan">
                        </a>
                        <br/>
                        <span>{{ trans('WebSite.or') }}</span>
                        <br/>
                        <p style="margin:0px; font-size:14px;">{{ trans('WebSite.contactUs') }} : <a href="mailto:support@catech.co.in"
                                style="color:#009688">support@catech.co.in</a></p>
                    </div>
                </div>
            </div>
            <div style="background-color: #F7F7F7; font-size: 12px; font-weight: 400;text-align: center;padding: 3px;">
                {{ trans('WebSite.kindAttention') }}
            </div>
            <div style="line-height:25px;margin:10px 0px;">
                <span style="font-size:0.9em;">{{ trans('WebSite.ThankYou') }}</span><br>
                <h4 style="margin-top: 0px;">{{ trans('WebSite.MailSignature') }}</h4>
            </div>
        </div>
    </div>
</body>

</html>
