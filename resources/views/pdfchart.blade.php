<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Status</title>
    <style type="text/css">
        @font-face {
            font-family: 'poppins';
            src: url({{ storage_path('fonts/Poppins-Regular.ttf') }}) format("truetype");
            font-weight: 400; // use the matching font-weight here ( 100, 200, 300, 400, etc).
            font-style: normal; // use the matching font-style here
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
        @font-face {
            font-family: 'notosansjp';
            src: url({{ storage_path('fonts/NotoSansJP-Regular.otf') }}) format("truetype");
            font-weight: 400;
            font-style: normal;
        }
        body {
            /* font-family: arialuni,poppins; */
            font-family:'poppins','arialuni','notosansjp';
        }
        .mainTable table{
            border: 1px solid #EFEFEF;
            border-collapse: collapse;
            }
        .mainTable td{
            border: 1px solid #EFEFEF;
            border-collapse: collapse;
            }
        .mainTable th{
            border: 1px solid #EFEFEF;
            border-collapse: collapse;
            }
            .page-break {
                page-break-after: always;
            }
        .tableType td p{
            word-break: break-word !important;
        }
        .tableType{
            border-collapse: collapse;
        }
        .headTable td{
            border: 1px solid #EFEFEF;
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
<body style="font-family: poppins,arialuni,notosansjp,poppins-semibold; font-size: 12px;">
    <div>
        <div style="font-size:18px;font-weight: 600;text-align:center; padding: 1px 3px 5px;">
            <span><strong>PDF Chart</strong></span>
        </div>
    </div>

    <div style="clear : both;"></div>
    <?php

// Format the image SRC:  data:{mime};base64,{data};
$src = 'data: image/png;base64,'.$data['imageData'];
// Echo out a sample image
//echo '<img src="' . $src . '" >';



//dd($src);
?>

    {{-- <img src="{{ public_path().('/images/chart.webp') }}" style="margin-left: 15px;margin-top:5px"/> --}}

    {{-- <img src="https://quickchart.io/chart?width=200&height=100&c={{ $data['chart'] }} " alt="chart"><br>
    <img src="{{ $data['chart_url'] }} " alt="chart111"><br>--}}
    <br><br>
    <img src="{{ $src }} " alt="Chart Image" ><br>


@php
exit;
@endphp
    <footer>
        <script type="text/php">
            if (isset($pdf)) {
               $font = $fontMetrics->getFont("Poppins", "bold");
               $pdf->page_text(28,815, "PDF Chart", $font, 9, array(0, 0, 0));
               $pdf->page_text(490,815, "{{ date('Y-m-d') }}  {PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
            }
        </script>
    </footer>
</body>
</html>
<?php //exit('out'); ?>
