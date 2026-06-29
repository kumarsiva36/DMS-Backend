<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Export Chat</title>
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
            border: 1px solid #e0e0e0;
            border-collapse: collapse;
        }

        .mainTable td {
            border: 1px solid #e0e0e0;
            border-collapse: collapse;
            vertical-align: top;
        }

        .mainTable th {
            border: 1px solid #e0e0e0;
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
        .chat_div span{
            display: block;
            font-size: 10px;
        }
        .chat_det_div{
            width: 70%
        }
        .chat_div div p{
           background: #f2f2f2;
           padding: 3px 10px 10px 10px;
           display: inline-block;
           margin: 0;
           border-radius: 10px;
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
    <div >
        <table width="100%"  cellpadding="5" cellspacing="0" class="" >
            <tr>
                <td width="15%">
                        <img src="{{ public_path() . '/images/dms-log-with-tag.png' }}"
                            style="background-color: #FFFFFF; height: 40px;"  />
                </td>
                <td >
                    <div style="vertical-align:middle;text-align:center; font-size:18px; font-weight:600; color: #8C878D; ">
                    <strong>Chat Details</strong>
                    </div>
                </td>
                <td width="12%">
                    <div style="vertical-align:middle;text-align:right; font-size:12px; font-weight:600; color: #8C878D; ">
                        {{ date('d M Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div style="clear : both;"></div>
    <div class="chat_div" style="font-family: poppins,arialuni,notosansjp;">
        @foreach ($result as $res)
            @if($res['sent_by']==1)
                <div style="float: left" class="chat_det_div">
                    <span><strong>{{ $res['sender_name'] }}</strong></span>
                    <p><strong>{{ $res['message'] }}</strong></p>
                    <span><strong>{{ date('d M Y H:i', strtotime($res['created_at'])) }}</strong></span>
                </div>
                <div style="clear : both;"></div>
            @else
                <div style="float: right; text-align:right;" class="chat_det_div">
                    <span><strong>{{ $res['sender_name'] }}</strong></span>
                    <p><strong>{{ $res['message'] }}</strong></p>
                    <span><strong>{{ date('d M Y H:i', strtotime($res['created_at'])) }}</strong></span>
                </div>
                <div style="clear : both;"></div>
            @endif
        @endforeach
    </div>

    <footer>
        <script type="text/php">
            if (isset($pdf)) {
               $font = $fontMetrics->getFont("Poppins", "bold");
               $pdf->page_text(28,815, "Chat Details", $font, 9, array(0, 0, 0));
               $pdf->page_text(490,815, "{{ date('d M Y') }}  {PAGE_NUM}/{PAGE_COUNT}", $font, 9, array(0, 0, 0));
            }
        </script>
    </footer>
</body>
</html>
