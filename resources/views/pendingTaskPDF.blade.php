<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Pending Task</title>
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
        @page { /* margin-top: 100px; */margin-bottom: 60px; }
        /* #header { position: fixed; left: 0px; top: -80px; right: 0px;text-align: center; } */
        #footer { position: fixed; left: 0px; bottom: -50px; right: 0px;text-align: center; }
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
<body style="font-family: poppins,arialuni,notosansjp,poppins-semibold;font-size: 12px;width:100%">
    <div >
        {{-- <img src="{{ public_path().'/images/dms-log-with-tag.png' }}" style="background-color: #FFFFFF; height: 98px; width:197px" /> --}}
        {{-- <div style="float:right; font-size:45px; font-weight:600; color: #8C878D; margin-top:-35px"> --}}
        <div style="font-size:18px;font-weight: 600;text-align:center; padding: 1px 3px 5px;">
                <span><strong>{{ trans('WebSite.TaskPending') }}</strong></span>
                {{-- <img src="{{ public_path().'/images/CalendarIcon.svg' }}"/> {{ date($pendingTask['dateFormat']) }} --}}
        </div>
        {{-- </div> --}}
        <div style="clear : both;"></div>
        <?php $i=0  ?>
        @foreach ( $pendingTask["styleDetails"] as $tasks)
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
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $pendingTask['orderNo'] }} / {{ $tasks['styleNo'] }}</strong>
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
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $pendingTask['orderNo'] }} / {{ $tasks['styleNo'] }}</strong>
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
        <div style="margin: 2px 0;">
            @if (count($pendingTask['advFilter'])>0)
                <strong>{{ trans('WebSite.filter') }} : </strong>
                @if (array_key_exists("dayCount",$pendingTask['advFilter']) && array_key_exists("operator",$pendingTask['advFilter']))
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">
                    {{ trans('WebSite.noOfDaysDelay',['delay'=>$pendingTask['advFilter']['dayCount'],
                    'operator'=>$pendingTask['advFilter']['operator']])}}</strong>
                @endif
                @if (array_key_exists("pic",$pendingTask['advFilter']))
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.pic').": ".$pendingTask['advFilter']['pic'] }}</strong>
                @endif
            @endif
        </div>
        {{-- <div style="margin: 25px 0;">
            <div style="width:40%;margin : 5px; float:left;border-radius : 5px; height:auto;">
                <table style="margin: 0;padding: 0; width:100%;" class="tableType" >
                    <tr style="background-color: #C4E1DD;">
                        <td style="width: 20%"><img src="{{ public_path().'/images/OrderIconColored.png' }}" style="padding:15px 10px"/></td>
                        <td style="line-height: 1;">
                            <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                <strong>{{ trans('WebSite.Order') }}</strong></p>
                            <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ $pendingTask['orderNo'] }}</strong></p>
                        </td>
                    </tr>
                    <tr><td colspan="2" style="padding:4px;"></td></tr>
                    <tr style="background-color: #C4E1DD;">
                        <td style="width: 20%"><img src="{{ public_path().'/images/StyleIconColored.png' }}" style="padding:15px 10px"/></td>
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
                            <td style="width: 20%"><img src="{{ public_path().'/images/FactoryColored.png' }}" style="padding:15px 10px"/></td>
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
                            <td style="width: 20%"><img src="{{ public_path().'/images/PCUColored.png' }}" style="padding:15px 10px"/></td>
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
                            <td style="width: 20%"><img src="{{ public_path().'/images/BuyerColored.png' }}" style="padding:15px 10px"/></td>
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
            <table style="margin-top : 5px;width: 100%; border-collapse: collapse" cellspacing="1px" class="mainTable">
                <tr style="background-color: #f0efef;font-weight:600;">
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.Task') }}</strong></td>                   
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.StartDate') }}</strong></td>
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.EndDate') }}</strong></td>
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.pic') }}</strong></td>
                    <td style="padding : 5px 5px 10px 20px;text-align:center;"><strong>{{ trans('WebSite.Status') }}</strong></td>
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.Result') }}</strong></td>
                </tr>
                @foreach ( $tasks['taskData'] as $taskData )
                    <tr>
                        <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ $taskData['title'] }}</strong></td>
                        @if ($taskData['startDate'] == "")
                        <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.NotAssigned') }}</strong></td>
                    @else
                        <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $taskData['startDate'] }}</td>
                    @endif
                    @if ($taskData['scheduledDate'] == "")
                        <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.NotAssigned') }}</strong></td>
                    @else
                        <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $taskData['scheduledDate'] }}</td>
                    @endif
                        @if ($taskData['pic'] == "")
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.NotAssigned') }}</strong></td>
                        @else
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $taskData['pic'] }}</td>
                        @endif
                       
                        @if ($taskData['type'] === 'YetToBeStarted' && $taskData['days'] >1 )
                            <td style="text-align:center; background-color: #FFFFFF;">
                                <img src="{{ public_path().'/images/YettoStart.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                            </td>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.DaysToStart',['day'=>$taskData['days']]) }}</strong></td>
                        @endif
                        @if ($taskData['type'] === 'YetToBeStarted' && $taskData['days'] === 1)
                            <td style="text-align:center; background-color: #FFFFFF;">
                                <img src="{{ public_path().'/images/YettoStart.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                            </td>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.startsTomorrow') }}</strong></td>
                        @endif
                        @if ($taskData['type'] === 'StartsToday')
                            <td style="text-align:center; background-color: #FFFFFF;">
                                <img src="{{ public_path().'/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                            </td>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.StartsToday') }}</strong></td>
                        @endif
                        @if ($taskData['days'] >0 && $taskData['type'] === 'Progress')
                            @if ($taskData['days'] == 1)
                                <td style="text-align:center; background-color: #FFFFFF;">
                                    <img src="{{ public_path().'/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                                </td>
                                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.DayRemaining') }}</strong></td>
                            @else
                                <td style="text-align:center; background-color: #FFFFFF;">
                                    <img src="{{ public_path().'/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                                </td>
                                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.DaysRemaining',["days"=>$taskData['days']]) }}</strong></td>
                            @endif
                        @endif
                        @if ($taskData['days']===0 && $taskData['type'] === 'Progress')
                            <td style="text-align:center; background-color: #FFFFFF;">
                                <img src="{{ public_path().'/images/SmileyYellow.svg' }}" />
                            </td>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.LastDay') }}</strong></td>
                        @endif
                        @if ($taskData['days'] <0 && $taskData['type'] === 'Progress')
                            @if ($taskData['days']== -1)
                                <td style="text-align:center; background-color: #FFFFFF;">
                                    <img src="{{ public_path().'/images/2_Days_Delay.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                                </td>
                                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($taskData['days'])." ".trans('WebSite.dayDelay') }}</strong></td>
                            @elseif (abs($taskData['days'])<= 3)
                                <td style="text-align:center; background-color: #FFFFFF;">
                                    <img src="{{ public_path().'/images/2_Days_Delay.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                                </td>
                                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($taskData['days'])." ".trans('WebSite.daysDelay') }}</strong></td>
                            @else
                                <td style="text-align:center; background-color: #FFFFFF;">
                                    <img src="{{ public_path().'/images/delay_bomb.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                                </td>
                                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($taskData['days'])." ".trans('WebSite.daysDelay') }}</strong></td>
                            @endif
                        @endif
                        @if ($taskData['days'] === NULL)
                            <td style="text-align:center; background-color: #FFFFFF;">
                                <img src="{{ public_path().'/images/SmileySad.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                            </td>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.NotAssigned') }}</strong></td>
                        @endif

                    </tr>
                    @if (array_key_exists('subtasks', $taskData))
                        @foreach ($taskData['subtasks'] as $subtasks)
                            <tr>
                                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp; font-size:13px;">{{ $subtasks['subtasktitle'] }}</td>
                                @if ($subtasks['pic'] == "")
                                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.NotAssigned') }}</strong></td>
                                @else
                                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $subtasks['pic'] }}</td>
                                @endif
                                @if ($subtasks['startDate'] == "")
                                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.NotAssigned') }}</strong></td>
                                @else
                                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $subtasks['startDate'] }}</td>
                                @endif
                                @if ($subtasks['scheduledDate'] == "")
                                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.NotAssigned') }}</strong></td>
                                @else
                                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $subtasks['scheduledDate'] }}</td>
                                @endif
                                @if ($subtasks['type'] === 'YetToBeStarted' && $subtasks['days'] >1 )
                                    <td style="text-align:center; background-color: #FFFFFF;">
                                        <img src="{{ public_path().'/images/YettoStart.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                                    </td>
                                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.DaysToStart',['day'=>$subtasks['days']]) }}</strong></td>
                                @endif
                                @if ($subtasks['type'] === 'YetToBeStarted' && $subtasks['days'] === 1)
                                    <td style="text-align:center; background-color: #FFFFFF;">
                                        <img src="{{ public_path().'/images/YettoStart.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                                    </td>
                                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.startsTomorrow') }}</strong></td>
                                @endif
                                @if ($subtasks['type'] === 'StartsToday')
                                    <td style="text-align:center; background-color: #FFFFFF;">
                                        <img src="{{ public_path().'/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                                    </td>
                                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.StartsToday') }}</strong></td>
                                @endif
                                @if ($subtasks['days'] >0 && $subtasks['type'] === 'Progress')
                                    @if ($subtasks['days'] == 1)
                                        <td style="text-align:center; background-color: #FFFFFF;">
                                            <img src="{{ public_path().'/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                                        </td>
                                        <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">
                                            <strong>{{ trans('WebSite.DayRemaining') }}</strong></td>
                                    @else
                                        <td style="text-align:center; background-color: #FFFFFF;">
                                            <img src="{{ public_path().'/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                                        </td>
                                        <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">
                                            <strong>{{ trans('WebSite.DaysRemaining',["days"=>$subtasks['days']]) }}</strong></td>
                                    @endif
                                @endif
                                @if ($subtasks['days']===0 && $subtasks['type'] === 'Progress')
                                    <td style="text-align:center; background-color: #FFFFFF;">
                                        <img src="{{ public_path().'/images/SmileyYellow.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                                    </td>
                                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.LastDay') }}</strong></td>
                                @endif
                                @if ($subtasks['days'] <0 && $subtasks['type'] === 'Progress')
                                    @if ($subtasks['days']== -1)
                                        <td style="text-align:center; background-color: #FFFFFF;">
                                            <img src="{{ public_path().'/images/2_Days_Delay.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                                        </td>
                                        <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($subtasks['days'])." ".trans('WebSite.dayDelay') }}</strong></td>
                                    @elseif (abs($subtasks['days'])<= 3)
                                        <td style="text-align:center; background-color: #FFFFFF;">
                                            <img src="{{ public_path().'/images/2_Days_Delay.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                                        </td>
                                        <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($subtasks['days'])." ".trans('WebSite.daysDelay') }}</strong></td>
                                    @else
                                        <td style="text-align:center; background-color: #FFFFFF;">
                                            <img src="{{ public_path().'/images/delay_bomb.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                                        </td>
                                        <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($subtasks['days'])." ".trans('WebSite.daysDelay') }}</strong></td>
                                    @endif
                                @endif
                                @if ($subtasks['days'] === NULL)
                                    <td style="text-align:center; background-color: #FFFFFF;">
                                        <img src="{{ public_path().'/images/SmileySad.svg' }}" width="{{config('constant.pdf_icon_width')}}"  />
                                    </td>
                                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.NotAssigned') }}</strong></td>
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
            {{-- <div id="footer">
                <strong>
                    {!! trans('WebSite.PDFFooter',['date'=>$tasks['orderLastDate']]) !!}
                </strong>
            </div> --}}
            @if($i < (count($pendingTask["styleDetails"])-1))
                <div class="page-break"></div>
            @endif
            <?php $i++ ?>
    @endforeach
    <footer>
        <script type="text/php">
            if (isset($pdf)) {
               $font = $fontMetrics->getFont("Poppins", "bold");
               $pdf->page_text(28,815, "{{ trans('WebSite.TaskPending') }}", $font, 9, array(0, 0, 0));
               $pdf->page_text(490,815, "{{ date($pendingTask['dateFormat']) }}  {PAGE_NUM}/{PAGE_COUNT}", $font, 9, array(0, 0, 0));

               {{-- $canvas = $pdf->getDomPDF()->getCanvas();
                $imageURL = 'images/dms-log-with-tag.png';
                $imgWidth = 200;
                $imgHeight = 150;
                $x = (200);
                $y = (300);
                $mode = "Multiply";
                $canvas->set_opacity(.2, $mode);
                $canvas->image($imageURL, $x, $y, $imgWidth, $imgHeight,$resolution = "normal"); --}}
            }
        </script>
    </footer>
</body>
</html>
