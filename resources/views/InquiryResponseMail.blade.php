<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Inquiry Request</title>
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
        .mainTable table {
            border: 1px solid #EFEFEF;
            border-collapse: collapse;
        }

        .mainTable td {
            border: 1px solid #EFEFEF;
            border-collapse: collapse;
        }

        .mainTable th {
            border: 1px solid #EFEFEF;
            border-collapse: collapse;
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
                <span style="color: #188676; font-size:18px;"><strong>{{ trans('WebSite.Dear',['user'=>trim($details['name'])]) }},
                </strong></span><br><br>
                Please find the Quote Details for the Inquiry (IN- {{ $details['inquiry_id']}}) from {{ $details['factory_name'] }}.
                <br><br>
                <table width="100%" style="border-collapse: collapse;" cellspacing="1px" cellpadding="5px" class="mainTable" >
                    <tr>
                        <td><strong>Price</strong></td>
                        <td>{{ $details['price'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Comments</strong></td>
                        <td>{!! $details['comments'] !!}</td>
                    </tr>
                </table>
                <br><br>
            </div>
            <a href="{{ "http://34.208.216.116:8080/Inquiry/".$details['inquiry_id'].".pdf" }}">Click here to view Inquiry</a>
        </div>
        <p style="font-size:0.9em; margin-bottom: 0px">{{ trans('WebSite.ThankYou') }}</p>
        <h4 style="margin-top: 0px;" >{{ trans('WebSite.MailSignature') }}</h4>
        </div>
      </div>
</body>
</html>
