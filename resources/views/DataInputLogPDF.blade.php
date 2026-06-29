<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Production Report</title>
    <style type="text/css">
        @font-face {
            font-family: 'poppins';
            src: url({{ storage_path('fonts/Poppins-Regular.ttf') }}) format("truetype");
            font-weight: 400;
            font-style: normal;
        }
        /* @font-face {
            font-family: 'poppins-semibold';
            src: url({{ storage_path('fonts/Poppins-SemiBold.ttf') }}) format("truetype");
            font-weight: 600;
            font-style: semibold;
        } */

        @font-face {
            font-family: 'arialuni';
            src: url({{ storage_path('fonts/arial_unicode.ttf') }}) format("truetype");
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'arialuni';
            src: url({{ storage_path('fonts/arial_unicode.ttf') }}) format("truetype");
            font-weight: 600;
            font-style: semibold;
        }
        @font-face {
            font-family: 'notosansjp';
            src: url({{ storage_path('fonts/NotoSansJP-Regular.otf') }}) format("truetype");
            font-weight: 400;
            font-style: normal;
        }
        body {
            font-family: 'Poppins';
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

        .page-break {
            page-break-after: always;
        }

        .tableType td p {
            word-break: break-word !important;
        }

        .tableType {
            border-collapse: collapse;
        }
    </style>
    <script type="text/php">
            $x = 250;
            $y = 10;
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $font = null;
            $size = 14;
            $color = array(255,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
    </script>
</head>

<body style="font-family: poppins,arialuni,notosansjp; font-size: 14px;">
    <div style="margin:25px 0;">
        <img src="{{ public_path() . '/images/dms-log-with-tag.png' }}"
            style="background-color: #FFFFFF; height: 98px; width:197px" />
        <div style="float:right; font-size:25px; font-weight:600; color: #8C878D; ">
            <strong>{{ trans('WebSite.DataInputLog') }}</strong>
            <div
                style=" background-color: #D1E7E4; font-size:16px; color: #178677; text-align:center;font-weight: 600;
            padding: 1px 3px 5px;">
                <img src="{{ public_path() . '/images/CalendarIcon.svg' }}" /> {{ date('d M Y') }}
            </div>
        </div>
        <div style="margin: 5px 0;">
            @if ($request)
                <strong>{{ trans('WebSite.filter') }} : </strong>
                @if ($request->company_id !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.companyName') .": ".$result[0]['company_name'] }}</strong>
                @endif
                @if ($request->workspace_id !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.workSpace') .": ".$result[0]['name'] }}</strong>
                @endif
                @if ($request->user_id !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.user') .": ".$result[0]['username'] }}</strong>
                @endif
                @if ($request->staff_id !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.staff') .": ".$result[0]['staffname'] }}</strong>
                @endif
                @if ($request->order_id !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.styleNo') .": ".$result[0]['style_no'] }}</strong>
                @endif
                @if ($request->start_date !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.StartDate') .": ".$request->start_date }}</strong>
                @endif
                @if ($request->end_date !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.EndDate') .": ".$request->end_date }}</strong>
                @endif
                @if ($request->production !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.production') .": ".$request->production }}</strong>
                @endif
            @endif
        </div>
        
    </div>
    <div style="clear : both;"></div>

    <table style="width: 100%;border-collapse: collapse;font-family: poppins,arialuni,notosansjp;"
        cellspacing="1px" class="mainTable">
            <tr style="background-color: #C4E1DD; color: #178677; font-weight:500; font-family: poppins,arialuni,notosansjp;">
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.date') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.companyName') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.workSpace') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.user/staff') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.styleNo') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.color') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.size') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.production') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.quantity') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.CreatedAt') }}</strong></td>
            </tr>
            @foreach ($result as $res)
            <tr>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>{{$res['sku_date']}}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>{{$res['company_name']}}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>{{$res['name']}}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>{{$res['username'] ?? $res['staffname']}}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>{{$res['style_no']}}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>{{$res['colorname']}}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>{{$res['size']}}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>{{$res['type_of_production']}}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>{{$res['updated_quantity']}}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>{{$res['created_date']}}</strong></td>
            </tr>
            @endforeach
    </table>
   
    
    
    {{-- <footer style="margin-top: 15px;width: 100%;background-color: #D1E7E4; font-size:14px; color: #178677; text-align:center;
            padding: 1px 3px 5px;">
            <strong>
                {!! trans('WebSite.PDFFooter',['date'=>date('d M Y')]) !!}
            </strong>
    </footer> --}}
</body>

</html>
