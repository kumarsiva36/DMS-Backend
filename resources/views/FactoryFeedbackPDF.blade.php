
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ trans('WebSite.FactoryFeedback') }}</title>
    <style type="text/css">
        @font-face {
            font-family: 'poppins';
            src: url({{ storage_path('fonts/Poppins-Regular.ttf') }}) format("truetype");
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'poppins-semibold';
            src: url({{ storage_path('fonts/Poppins-SemiBold.ttf') }}) format("truetype");
            font-weight: 600;
            font-style: semibold;
        }

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

        .tableType td p, table td p, table td li,table td a {
            word-break: break-word !important;
            padding-right: 15px;
        }

        .tableType {
            border-collapse: collapse;
        }
        table td {word-wrap:break-word !important; }
        /* table tr:nth-child(even) {background: #f6f6f6}
        table tr:nth-child(odd) {background: #ffffff} */
    </style>

</head>

<body style="font-family: poppins,arialuni; font-size: 14px;">

    <div style="margin:25px 0;">
        <table width="100%"  cellpadding="0" cellspacing="0" >
            <tr>
                <td width="35%">
                    <div style="float:left;">
                        <img src="{{ public_path() . '/images/dms-log-with-tag.png' }}"
                            style="background-color: #FFFFFF; height: 70px; width:150px" />
                    </div>
                </td>
                <td width="25%">
                    <div style="float:right; font-size:20px; font-weight:600; color: #8C878D; ">
                        <strong>{{ trans('WebSite.FactoryFeedback') }} </strong>
                        <div
                            style=" background-color: #D1E7E4; font-size:16px; color: #178677; text-align:center;font-weight: 600;
                        padding: 1px 3px 5px;">
                            <img src="{{ public_path() . '/images/CalendarIcon.svg' }}" /> {{ date($data['dateFormat']) }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <br>
    <div style="clear : both;"></div>
    {{-- <div style="margin: 5px 0;">
        @if (count($data['advFilter'])>0)
            <strong>{{ trans('WebSite.filter') }} : </strong>
            @if (array_key_exists("factory",$data['advFilter']))
                <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">
                    {{ trans('WebSite.Factory').": ".$data['advFilter']['factory'] }}</strong>
            @endif
            @if (array_key_exists("article",$data['advFilter']))
                <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">
                    {{ trans('WebSite.article').": ".$data['advFilter']['article'] }}</strong>
            @endif
            @if (array_key_exists("startDate",$data['advFilter']) && array_key_exists("endDate",$data['advFilter']))
                <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">
                {{ trans('WebSite.StartDate').": ".$data['advFilter']['startDate'] }}
                </strong>
                <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">
                {{ trans('WebSite.EndDate').": ".$data['advFilter']['endDate'] }}
                </strong>
            @endif
        @endif
    </div> --}}
    <div style="clear : both;"></div>
    <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;margin-top:5px;"
    cellspacing="1px" class="mainTable">
    <tr style="background-color: #C4E1DD; color: #178677; font-weight:600;">
        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.sno') }}</strong></td>
        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.Factory') }}</strong></td>
        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.Inquiry') }}</strong></td>
        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.lowest_price') }}</strong></td>
        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.ontime_delivery') }}</strong></td>
        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.vendor_buyer_relation') }}</strong></td>
        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.sample_submission') }}</strong></td>
        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.communication') }}</strong></td>
        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.less_quality_issue') }}</strong></td>
        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.good_sell_through') }}</strong></td>
        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.collaborative_approach') }}</strong></td>
        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.createdDate') }}</strong></td>
    </tr>
    @foreach ($data['responses'] as $index=>$response)
        <tr>
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp;">{{ $index+1 }}</td>
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp;">{{ $response['factory'] }}</td>
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp;">IN-{{ $response['inquiry_id'] }}</td>
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp;">{{ $response['lowest_price'] }}</td>
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp;">{{ $response['ontime_delivery'] }}</td>
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp;">{{ $response['vendor_buyer_relation'] }}</td>
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp;">{{ $response['sample_submission'] }}</td>
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp;">{{ $response['communication'] }}</td>
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp;">{{ $response['less_quality_issue'] }}</td>
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp;">{{ $response['good_sell_through'] }}</td>
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp;">{{ $response['collaborative_approach'] }}</td>
            <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp;">{{ $response['created_date'] }}</td>
        </tr>
    @endforeach
    </table>
    <footer>
        <script type="text/php">
            if (isset($pdf)) {
               $font = $fontMetrics->getFont("Arial", "bold");
               $pdf->page_text(760, 565, "Page {PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
            }
        </script>
    </footer>
</body>
</html>
