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
        {{-- <img src="{{ public_path().'/images/dms-log-with-tag.png' }}" style="background-color: #FFFFFF; height: 98px; width:197px" />
        <div style="float:right; font-size:54px; font-weight:600; color: #8C878D;margin-top:-35px">
            <strong>{{ trans('WebSite.TaskStatus') }}</strong>
            <div style="margin-top:5px; background-color: #D1E7E4; font-size:18px; color: #178677; text-align:center;font-weight: 600;
            padding: 5px;">
                <img src="{{ public_path().'/images/CalendarIcon.svg' }}"/> {{ date($OrderStatusPDF['dateFormat']) }}
            </div> --}}
            <div style="font-size:18px;font-weight: 600;text-align:center; padding: 1px 3px 5px;">
                <span><strong>{{ trans('WebSite.TaskStatus') }}</strong></span>
            </div>
        </div>
        <?php $i=0  ?>
        @foreach ($OrderStatusPDF["styleDetails"] as $tasks)
        <div>
            <table style="width: 100%; border-collapse: collapse;font-size:10px;" cellspacing="1px" class="headTable">
                @if($OrderStatusPDF['useLogo']==1 && $OrderStatusPDF['userLogo']!='')
                    <tr style="">
                        <td rowspan="2" width="15%">
                            <img src="{{ $OrderStatusPDF['serverURL'].$OrderStatusPDF['userLogo'] }}"
                            style="background-color: #FFFFFF; height: 40px;margin-left:5px;" />
                        </td>
                        <td style="width:10%;background-color: #f0efef;">
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Order') }}</strong>
                        </td>
                        <td>
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $OrderStatusPDF['orderNo'] }}</strong>
                        </td>
                        <td style="width:10%;background-color: #f0efef;">
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Style') }}</strong>
                        </td>
                        <td>
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $tasks['styleNo'] }}</strong>
                        </td>
                        <td rowspan="2" width="8%">
                            <img src="{{ public_path() . "/images/dms_small.png" }}"
                            style="background-color: #FFFFFF; height: 30px;margin-left:5px;" />
                        </td>
                    </tr>
                @else
                    <tr style="">
                        <td rowspan="2" style="width:15%;">
                            <img src="{{ public_path() . "/images/dms-log-with-tag.png" }}"
                            style="background-color: #FFFFFF; height: 40px;margin-left:5px;" />
                        </td>
                        <td style="width:10%;background-color: #f0efef;">
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Order') }}</strong>
                        </td>
                        <td>
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $OrderStatusPDF['orderNo'] }}</strong>
                        </td>
                        <td style="width:10%;background-color: #f0efef;">
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Style') }}</strong>
                        </td>
                        <td>
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $tasks['styleNo'] }}</strong>
                        </td>
                    </tr>
                @endif
                <tr>
                    @if (in_array("factory",(array_keys($tasks))))
                            <td style="width:10%;background-color: #f0efef;">
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Factory') }}</strong>
                            </td>
                            <td>
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $tasks["factory"] }}</strong>
                            </td>
                    @endif
                    @if (in_array("pcu",(array_keys($tasks))))
                            <td style="width:10%;background-color: #f0efef;">
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.PCU') }}</strong>
                            </td>
                            <td>
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $tasks["pcu"] }}</strong>
                            </td>
                    @endif
                    @if (in_array("buyer",(array_keys($tasks))))
                            <td style="width:10%;background-color: #f0efef;">
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Buyer') }}</strong>
                            </td>
                            <td>
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $tasks["buyer"] }}</strong>
                            </td>
                    @endif
                </tr>
            </table>
        </div>
        {{-- <div style="margin: 25px 0;">
            <div style="width:40%;margin : 5px; float:left;border-radius : 5px; height:auto;">
                <table style="margin: 0;padding: 0; width:100%;" class="tableType" >
                    <tr style="background-color: #C4E1DD;">
                        <td style="width: 20%"><img src="{{ public_path().'/images/OrderIconColored.png' }}" style="padding:15px"/></td>
                        <td style="line-height: 1;">
                            <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                <strong>{{ trans('WebSite.Order') }}</strong></p>
                            <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ $OrderStatusPDF['orderNo'] }}</strong></p>
                        </td>
                    </tr>
                    <tr><td colspan="2" style="padding:4px;"></td></tr>
                    <tr style="background-color: #C4E1DD;">
                        <td style="width: 20%"><img src="{{ public_path().'/images/StyleIconColored.png' }}" style="padding:15px"/></td>
                        <td style="line-height: 1;">
                            <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                <strong>{{ trans('WebSite.Style') }}</strong></p>
                            <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ $tasks["styleNo"] }}</strong></p>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:40%; float:right;margin : 5px;border-radius : 5px; height:auto;">
                <table style="margin: 0;padding: 0; width:100%;" class="tableType" >
                    @if (in_array("factory",(array_keys($tasks))))
                        <tr style="background-color: #C4E1DD;">
                            <td style="width: 20%"><img src="{{ public_path().'/images/FactoryColored.png' }}" style="padding:15px"/></td>
                            <td style="line-height: 1;">
                                <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.Factory') }}</strong></p>
                                <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ $tasks["factory"] }}</strong></p>
                            </td>
                        </tr>
                        <tr><td colspan="2" style="padding:4px;"></td></tr>
                    @endif
                    @if (in_array("pcu",(array_keys($tasks))))
                        <tr style="background-color: #C4E1DD;">
                            <td style="width: 20%"><img src="{{ public_path().'/images/PCUColored.png' }}" style="padding:15px"/></td>
                            <td style="line-height: 1;">
                                <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.PCU') }}</strong></p>
                                <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ $tasks["pcu"] }}</strong></p>
                            </td>
                        </tr>
                        <tr><td colspan="2" style="padding:4px;"></td></tr>
                    @endif
                    @if (in_array("buyer",(array_keys($tasks))))
                        <tr style="background-color: #C4E1DD;">
                            <td style="width: 20%"><img src="{{ public_path().'/images/BuyerColored.png' }}" style="padding:15px"/></td>
                            <td style="line-height: 1;">
                                <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.Buyer') }}</strong></p>
                                <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ $tasks["buyer"] }}</strong></p>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
        </div> --}}
            </div>
            <div style="clear : both;"></div>
            <table style="margin-top : 25px;width: 100%; border-collapse: collapse" cellspacing="1px" class="mainTable">
                <tr style="background-color: #f0efef;font-weight:500;">
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.Task') }}</strong></td>
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.StartDate') }}</strong></td>
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.EndDate') }}</strong></td>
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.AccomplishedDate') }}</strong></td>
                   <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.ResponsiblePerson') }}</strong></td>
                </tr>
                @foreach ( $tasks['taskData'] as $taskData )
                    <tr>
                        <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;font-size:13px;">{{ $taskData['title'] }}</td>
                        @if ($taskData['scheduledStartDate'] == "")
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">-</td>
                        @else
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $taskData['scheduledStartDate'] }}</td>
                        @endif
                        @if ($taskData['scheduledEndDate'] == "")
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">-</td>
                        @else
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $taskData['scheduledEndDate'] }}</td>
                        @endif
                        @if ($taskData['scheduledAccDate'] == "")
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">-</td>
                        @else
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $taskData['scheduledAccDate'] }}</td>
                        @endif
                        @if ($taskData['pic'] == "")
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.NotAssigned') }}</strong></td>
                        @else
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $taskData['pic'] }}</td>
                        @endif
                    </tr>
                    @if (array_key_exists('subtasks',$taskData)>0)
                        @foreach ($taskData['subtasks'] as $subtask)
                        <tr>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;font-size:12px">
                                {{ $subtask['title'] }}</td>
                            @if ($subtask['scheduledStartDate'] == "")
                                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">-</td>
                            @else
                                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $subtask['scheduledStartDate'] }}</td>
                            @endif
                            @if ($subtask['scheduledEndDate'] == "")
                                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">-</td>
                            @else
                                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $subtask['scheduledEndDate'] }}</td>
                            @endif
                            @if ($subtask['scheduledAccDate'] == "")
                                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">-</td>
                            @else
                                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $subtask['scheduledAccDate'] }}</td>
                            @endif
                            @if ($subtask['pic'] == "")
                                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.NotAssigned') }}</strong></td>
                            @else
                                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $subtask['pic'] }}</td>
                            @endif
                        </tr>
                        @endforeach
                    @endif
                @endforeach
            </table>

            <footer style="margin-top: 15px;text-align: center;background-color: #f0efef;width: 100%;padding: 5px;">
                <strong>
                    {!! trans('WebSite.PDFFooter',['date'=>$tasks['orderLastDate']]) !!}
                </strong>
            </footer>
            @if($i < (count($OrderStatusPDF["styleDetails"])-1))
                <div class="page-break"></div>
            @endif
            <?php $i++ ?>
    @endforeach
    <footer>
        <script type="text/php">
            if (isset($pdf)) {
               $font = $fontMetrics->getFont("Poppins", "bold");
               $pdf->page_text(28,815, "{{ trans('WebSite.TaskStatus') }}", $font, 9, array(0, 0, 0));
               $pdf->page_text(490,815, "{{ date($OrderStatusPDF['dateFormat']) }}  {PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
            }
        </script>
    </footer>
</body>
</html>
