<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Staff Invite to Order</title>
    {{-- <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .wrapper{width:35%}
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
    <table width="1000">
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
                                <span style="color: #188676; font-size:18px;"><strong>{{ trans('WebSite.Dear',['user'=>trim($details['userName'])])  }},</strong></span>
                            </div>
                            <br>
                            <div style="text-align: center;">
                                <img src="{{ $message->embed(public_path().'/images/StaffInviteToOrder.png') }}" width="59" height="59" style="padding-top: 25px;">
                            </div>
                            <div style="font-weight: 600; color: #188676;text-align:center; font-size:20px; margin-bottom:10px;">{{ trans('WebSite.inviteToParticipate') }}</div>

                            <div style="padding:0px 5px">
                                <p>{!! trans('WebSite.participateOrderText',['orderNo'=>$details['orderNo'],'workspaceName'=>$details['workspaceName']]) !!}</p>
                                <p>{!! trans('WebSite.accessWebVersion_text') !!} <a href="{{ config('app.app_url') }}orderstatus" style="color:#188676"> {{ config('app.app_url') }}orderstatus</a></p>
                            </div>

                        </td>
                    </tr>
                </table>
                <table width="100%" style="border-collapse: collapse;">
                    <tr>
                        <td style="border:1px solid #F7F7F7; padding:5px;background-color: #F7F7F7; text-align:center;font-size: 12px;">
                            {{ trans('WebSite.enjoyableExperiences') }}
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
{{-- <body style="font-family: 'Roboto', sans-serif;">
    <div style="min-width:1000px;overflow:auto;line-height:2">
        <div class="wrapper" style="margin:50px 0;padding:20px 0">
          <div >
            <a href="{{ config('app.logo_url') }}" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">
                <img src="{{ $message->embed(public_path().'/images/DMS-Logo.png') }}" width="125px" style="margin-bottom: 5px;">
            </a>
          </div>
          <div style="border:1px solid #eee; padding: 0px 10px 05px 10px;  ">
            <div style="line-height:25px;margin:10px 0px 0px 5px;">
                <span style="color: #188676; font-size:18px;"><strong>{{ trans('WebSite.Dear',['user'=>trim($details['userName'])])  }},</strong></span>
            </div>
            <div style="margin-top: 35px; ">
                <div>
                    <img src="{{ $message->embed(public_path().'/images/StaffInviteToOrder.png') }}" class="center">
                </div>
                <div style="font-weight: 600; color: #188676;text-align:center; font-size:20px; margin-bottom:10px;">{{ trans('WebSite.inviteToParticipate') }}</div>
                <div style="margin:0px 5px">
                    <p>{!! trans('WebSite.participateOrderText',['orderNo'=>$details['orderNo'],'workspaceName'=>$details['workspaceName']]) !!}</p>
                    <p>{!! trans('WebSite.accessWebVersion_text') !!} <a href="{{ config('app.app_url') }}orderstatus" style="color:#188676"> {{ config('app.app_url') }}orderstatus</a></p>
                </div>
            </div>
        </div>
        <div style="background-color: #F7F7F7; font-size: 12px; font-weight: 400;text-align: center;padding: 3px;">
            {{ trans('WebSite.enjoyableExperiences') }}
        </div>
        <div style="line-height:25px;margin:10px 0px;">
            <span style="font-size:0.9em;">{{ trans('WebSite.ThankYou') }}</span><br>
            <h4 style="margin-top: 0px;" >{{ trans('WebSite.MailSignature') }}</h4>
        </div>
        </div>
      </div>
</body> --}}
</html>
