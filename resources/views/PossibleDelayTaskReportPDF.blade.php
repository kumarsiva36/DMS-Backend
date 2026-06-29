<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Task Report</title>
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
        body{
            font-family: 'Poppins';
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
<body style="font-family: poppins,arialuni,notosansjp,poppins-semibold; font-size: 12px;width:100%">
    <div>
        {{-- <img src="{{ public_path().'/images/dms-log-with-tag.png' }}" style="background-color: #FFFFFF; height: 98px; width:197px" />
        <div style="float:right; font-size:30px; font-weight:600; color: #8C878D;">
            <strong>{{ trans('WebSite.TaskReport') }}</strong> - <strong>{{  trans('WebSite.'.$pendingTask['statusFilter']) }}</strong>
            <div style="background-color: #D1E7E4; font-size:16px; color: #178677; text-align:center;font-weight: 600;
            padding: 1px 3px 5px;">
                <img src="{{ public_path().'/images/CalendarIcon.svg' }}"/> {{ date($pendingTask['dateFormat']) }}
            </div>
        </div> --}}
        <div style="font-size:18px;font-weight: 600;text-align:center; padding: 1px 3px 5px;">
            <span><strong>{{ trans('WebSite.PossibleDelayTasks') }}</strong>
            </strong></span>
        </div>
        <div style="clear : both;"></div>
        <?php $i=0  ?>
        @foreach ( $pendingTask["styleDetails"] as $tasks)
            @if (!empty($tasks['taskData']))
                <div>
                    <table style="width: 100%; border-collapse: collapse;font-size:10px;" cellspacing="1px" class="headTable">
                        @if($pendingTask['useLogo']==1 && $pendingTask['userLogo']!='')
                            <tr style="">
                                <td rowspan="2" width="15%">
                                    <img src="{{ $pendingTask['userLogo'] }}"
                                    style="background-color: #FFFFFF; height: 40px;margin-left:5px;" />
                                </td>
                                <td style="width:20%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Order_only').' / '.trans('WebSite.Style') }}</strong>
                                </td>
                                <td>
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $tasks['orderNo'] }} / {{ $tasks['styleNo'] }}</strong>
                                </td>
                                <td style="width:12%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.delivery_date') }}</strong>
                                </td>
                                <td>
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">
                                        {{ ($tasks['delivery_date']!=null && $tasks['delivery_date']!='')?date($pendingTask['dateFormat'],strtotime($tasks['delivery_date'])):trans('WebSite.delivered') }}
                                    </strong>
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
                                <td style="width:20%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Order_only').' / '.trans('WebSite.Style') }}</strong>
                                </td>
                                <td>
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $tasks['orderNo'] }} / {{ $tasks['styleNo'] }}</strong>
                                </td>
                                <td style="width:12%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.delivery_date') }}</strong>
                                </td>
                                <td>
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">
                                        {{ ($tasks['delivery_date']!=null && $tasks['delivery_date']!='')?date($pendingTask['dateFormat'],strtotime($tasks['delivery_date'])):trans('WebSite.delivered') }}
                                    </strong>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <?php $td_i=0;?>
                            @if (in_array("factory",(array_keys($tasks))))
                            <?php $td_i++;?>
                                    <td style="width:10%;background-color: #f0efef;">
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Factory') }}</strong>
                                    </td>
                                    <td>
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $tasks["factory"] }}</strong>
                                    </td>
                            @endif
                            @if (in_array("pcu",(array_keys($tasks))))
                            <?php $td_i++;?>
                                    <td style="width:10%;background-color: #f0efef;">
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.PCU') }}</strong>
                                    </td>
                                    <td>
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $tasks["pcu"] }}</strong>
                                    </td>
                            @endif
                            @if (in_array("buyer",(array_keys($tasks))))
                            <?php $td_i++;?>
                                    <td style="width:10%;background-color: #f0efef;">
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Buyer') }}</strong>
                                    </td>
                                    <td>
                                        <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $tasks["buyer"] }}</strong>
                                    </td>
                            @endif
                            @if($td_i<2)
                                <td colspan="2"></td>
                            @endif
                        </tr>
                    </table>
                </div>
                <div style="margin: 2px 0;font-size:12px;">
                    @if (count($pendingTask['advFilter'])>0)
                        <strong>{{ trans('WebSite.filter') }} : </strong>
                        @if (array_key_exists("startDate",$pendingTask['advFilter']) && array_key_exists("endDate",$pendingTask['advFilter']))
                            <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.StartDate') .": ".$pendingTask['advFilter']['startDate'] }}</strong>
                            <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.EndDate') .": ".$pendingTask['advFilter']['endDate'] }}</strong>
                        @endif
                        @if (array_key_exists("styleNo",$pendingTask['advFilter']))
                            <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.Style').": ".$pendingTask['advFilter']['styleNo'] }}</strong>
                        @endif
                        @if (array_key_exists("pic",$pendingTask['advFilter']))
                            <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.pic').": ".$pendingTask['advFilter']['pic'] }}</strong>
                        @endif
                    @endif
                </div>
                </div>
                <div style="clear : both;"></div>
                <table style="margin-top : 5px;width: 100%; border-collapse: collapse" cellspacing="1px" class="mainTable">
                    <tr style="background-color: #f0efef;font-weight:600;font-family: poppins,arialuni,notosansjp;font-size:12px">
                        <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.Task') }}</strong></td>
                        <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.StartDate') }}</strong></td>
                        <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.EndDate') }}</strong></td>
                        <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.InProgress') }}(%)</strong></td>
                        <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.pic') }}</strong></td>

                    </tr>
                    @foreach ( $tasks['taskData'] as $taskData )
                        <tr>
                            <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">{{ $taskData['title'] }}</td>
                            {{-- Start Date --}}
                            @if ($taskData['startDate'] == "")
                                <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">---</td>
                            @else
                                <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">{{ $taskData['startDate'] }}</td>
                            @endif
                            {{-- End Date --}}
                            @if ($taskData['scheduledDate'] == "")
                                <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">---</td>
                            @else
                                <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">{{ $taskData['scheduledDate'] }}</td>
                            @endif
                            <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">{{ $taskData['inprogress_percentage'] }}</td>
                            {{-- Person In Charge --}}
                            @if ($taskData['pic'] == "")
                                <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.NotAssigned') }}</strong></td>
                            @else
                                <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">{{ $taskData['pic'] }}</td>
                            @endif
                            {{-- Status --}}


                        </tr>
                        @if (array_key_exists('subtasks',$taskData)>0)
                            @foreach ( $taskData['subtasks'] as $subtask )
                                <tr>
                                    <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">{{ $subtask['subtasktitle'] }}</td>
                                    {{-- Start Date --}}
                                    @if ($subtask['startDate'] == "")
                                        <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">---</td>
                                    @else
                                        <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">{{ $subtask['startDate'] }}</td>
                                    @endif
                                    {{-- End Date --}}
                                    @if ($subtask['scheduledDate'] == "")
                                        <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">---</td>
                                    @else
                                        <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">{{ $subtask['scheduledDate'] }}</td>
                                    @endif
                                    <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">{{ $subtask['inprogress_percentage'] }}</td>
                                    {{-- Person In Charge --}}
                                    @if ($subtask['pic'] == "")
                                        <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.NotAssigned') }}</strong></td>
                                    @else
                                        <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp;">{{ $subtask['pic'] }}</td>
                                    @endif

                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </table>
                <footer style="margin-top: 15px;width: 100%;background-color: #f0efef; font-size:12px;text-align:center;
                padding: 1px 3px 5px;">
                    <strong>
                        {!! trans('WebSite.PDFFooter',['date'=>$tasks['orderLastDate']]) !!}
                    </strong>
                </footer>
                @if($i < (count($pendingTask["styleDetails"])-1))
                    <div class="page-break"></div>
                @endif
                <?php $i++ ?>
            @endif
    @endforeach
    <footer>
        <script type="text/php">
            if (isset($pdf)) {
               $font = $fontMetrics->getFont("Poppins", "bold");
               $pdf->page_text(28,815, "{{ trans('WebSite.TaskReport') }} - {{  trans('WebSite.'.$pendingTask['statusFilter']) }}", $font, 9, array(0, 0, 0));
               $pdf->page_text(490,815, "{{ date($pendingTask['dateFormat']) }}  {PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
            }
        </script>
    </footer>
</body>
</html>
