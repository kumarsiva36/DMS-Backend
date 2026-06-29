
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Fabric Inquiry</title>
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
                    {{-- @if ($datas['logo'] != "" || $datas['logo'] != null )
                        <div style="float:left;">
                            <img src={{ config('filesystems.disks.s3.url').$datas['logo'] }}
                                style="background-color: #FFFFFF; height: 65px;width:140px;margin-left:5px;" />

                        </div>
                    @endif --}}
                </td>
                <td style="vertical-align:middle;text-align:left;font-size:20px; font-weight:600; color: #8C878D;" width="40%">
                        <strong>{{ trans('WebSite.fabric_inquiry') }} from - {{ $datas['user']['name'] }} ({{ $datas['user']['user_type'] }})</strong>
                </td>
                <td width="25%">
                    <div style="float:right; font-size:20px; font-weight:600; color: #8C878D; ">
                        <strong>{{ trans('WebSite.fabric_inquiry') }} - {{ $datas['inquiryID'] }}</strong>
                        <div
                            style=" background-color: #D1E7E4; font-size:16px; color: #178677; text-align:center;font-weight: 600;
                        padding: 1px 3px 5px;">
                            <img src="{{ public_path() . '/images/CalendarIcon.svg' }}" /> {{ date('d M Y') }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <div style="clear : both;"></div>

    <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;"
    cellspacing="1px" class="mainTable">
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.yarn_count') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['request']->yarn_count }}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.yarn_quantity') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['request']->yarn_quantity }}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.yarn_quality') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['request']->yarn_quality }}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.meterial') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['request']->meterial }}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.composition') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['request']->composition }}</td>
        </tr>
        @if(isset($datas['request']->reference_inquiry) && $datas['request']->reference_inquiry!='')
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.reference_inquiry') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">IN-{{ $datas['request']->reference_inquiry }}</td>
        </tr>
        @endif
        @if(isset($datas['request']->delivery_date) && $datas['request']->delivery_date!='')
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.delivery_date') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['request']->delivery_date }}</td>
        </tr>
        @endif
        @if(isset($datas['request']->inhouse_date) && $datas['request']->inhouse_date!='')
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.inhouse_date') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['request']->inhouse_date }}</td>
        </tr>
        @endif



    </table>
        <footer>
            <script type="text/php">
                if (isset($pdf)) {
                $font = $fontMetrics->getFont("Arial", "bold");
                $pdf->page_text(35, 805, "FBIN-{{ $datas['inquiryID'] }}    {{ date('d M Y') }}", $font, 10, array(0, 0, 0));
                $pdf->page_text(525, 805, "Page {PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
                }
            </script>
        </footer>
</body>
</html>






