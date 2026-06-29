<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Chat Table</title>
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
            border: 1px solid #c9c9c9;
            border-collapse: collapse;
        }
        .mainTable td{
            border: 1px solid #c9c9c9;
            border-collapse: collapse;
            }
        .mainTable th{
            border: 1px solid #c9c9c9;
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
        .dot{
            height: 25px;
            width: 25px;
            border-radius:50px;
            background-color:#3FC984;
        }
        /* table tr:nth-child(even) {background: #ffffff}
        table tr:nth-child(odd) {background: #f6f6f6} */
        .response {
            background: #ffffff
        }
        .request {
            background: #f6f6f6
        }
        .headTable td{
            border: 1px solid #EFEFEF;
        }
    </style>
</head>
<body style="font-family: poppins,arialuni,notosansjp,poppins-semibold;font-size: 12px;width:100%">
    <div>
        {{-- <img src="{{ public_path().'/images/dms-log-with-tag.png' }}" style="background-color: #FFFFFF; height: 98px; width:197px" /> --}}
        {{-- <div style="float:right; font-size:32px; font-weight:600; color: #8C878D; margin-top:-15px"> --}}
            {{-- <table class="mainTable" style="font-size: 14px;border-collapse: collapse;width:100%;">
                <tr>
                    <td style="width: 20%" rowspan="2">
                        <img src="{{ public_path().'/images/dms-log-with-tag.png' }}"
                        style="padding:15px 10px;background-color: #FFFFFF; height: 65px; width:140px"/>
                    </td>
                    <td style="padding: 5px;" width="15%">
                        <strong style="margin-left: 10px;">{{ trans('WebSite.Order') }}</strong>
                    </td>
                    <td style="padding: 5px;" width="25%">
                        <strong style="margin-left: 10px;">{{ $chats['orderNo'] }}</strong>
                    </td>
                    @if ($chats['workspacetype']==="factory")
                        @if (in_array("buyer",(array_keys($chats))))
                                <td style="padding: 5px;" width="15%">
                                    <strong style="margin-left: 10px;">{{ trans('WebSite.Buyer') }}</strong></p>
                                </td>
                                <td style="padding: 5px;" width="25%">
                                    <strong style="margin-left: 10px;">{{ $chats["buyer"] }}</strong>
                                </td>
                        @endif
                    @endif
                    @if ($chats['workspacetype']==="buyer")
                        @if (in_array("factory",(array_keys($chats))))
                                <td style="padding: 5px;" width="15%">
                                    <strong style="margin-left: 10px;">{{ trans('WebSite.Factory') }}</strong></p>
                                </td>
                                <td style="padding: 5px;" width="25%">
                                    <strong style="margin-left: 10px;">{{ $chats["factory"] }}</strong>
                                </td>
                        @endif
                    @endif
                    @if ($chats['workspacetype']==="pcu")
                        @if (in_array("factory",(array_keys($chats))))
                                <td style="padding: 5px;" width="15%">
                                    <strong style="margin-left: 10px;">{{ trans('WebSite.Factory') }}</strong></p>
                                </td>
                                <td style="padding: 5px;" width="25%">
                                    <strong style="margin-left: 10px;">{{ $chats["factory"] }}</strong>
                                </td>
                        @endif
                    @endif
                </tr>
                <tr>
                    <td style="padding: 5px;" width="15%">
                        <strong style="margin-left: 10px;">{{ trans('WebSite.Style') }}</strong>
                    </td>
                    <td style="padding: 5px;" width="25%">
                        <strong style="margin-left: 10px;">{{ $chats["styleNo"] }}</strong>
                    </td>
                    @if ($chats['workspacetype']==="factory")
                        @if (in_array("pcu",(array_keys($chats))))
                                <td style="padding: 5px;" width="15%">
                                    <strong style="margin-left: 10px;">{{ trans('WebSite.PCU') }}</strong></p>
                                </td>
                                <td style="padding: 5px;" width="25%">
                                    <strong style="margin-left: 10px;">{{ $chats["pcu"] }}</strong>
                                </td>
                        @endif
                    @endif
                    @if ($chats['workspacetype']==="buyer")
                        @if (in_array("pcu",(array_keys($chats))))
                                <td style="padding: 5px;" width="15%">
                                    <strong style="margin-left: 10px;">{{ trans('WebSite.PCU') }}</strong></p>
                                </td>
                                <td style="padding: 5px;" width="25%">
                                    <strong style="margin-left: 10px;">{{ $chats["pcu"] }}</strong>
                                </td>
                        @endif
                    @endif
                    @if ($chats['workspacetype']==="pcu")
                        @if (in_array("buyer",(array_keys($chats))))
                                <td style="padding: 5px;" width="15%">
                                    <strong style="margin-left: 10px;">{{ trans('WebSite.Buyer') }}</strong></p>
                                </td>
                                <td style="padding:5px;" width="25%">
                                    <strong style="margin-left: 10px;">{{ $chats["buyer"] }}</strong>
                                </td>
                        @endif
                    @endif
                </tr>
            </table> --}}
            <div>
                <table style="width: 100%; border-collapse: collapse;font-size:10px;" cellspacing="1px" class="headTable">
                    @if($chats['useLogo']==1 && $chats['userLogo']!='')
                        <tr style="">
                            <td rowspan="2" width="15%">
                                <img src="{{ $chats['userLogo'] }}"
                                style="background-color: #FFFFFF; height: 40px;margin-left:5px;" />
                            </td>
                            <td style="width:10%;background-color: #f0efef;">
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Order') }}</strong>
                            </td>
                            <td>
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $chats['orderNo'] }}</strong>
                            </td>
                            <td style="width:10%;background-color: #f0efef;">
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Style') }}</strong>
                            </td>
                            <td>
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $chats['styleNo'] }}</strong>
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
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $chats['orderNo'] }}</strong>
                            </td>
                            <td style="width:10%;background-color: #f0efef;">
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Style') }}</strong>
                            </td>
                            <td>
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $chats['styleNo'] }}</strong>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        @if (in_array("factory",(array_keys($chats))))
                                <td style="width:10%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Factory') }}</strong>
                                </td>
                                <td>
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $chats["factory"] }}</strong>
                                </td>
                        @endif
                        @if (in_array("pcu",(array_keys($chats))))
                                <td style="width:10%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.PCU') }}</strong>
                                </td>
                                <td>
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $chats["pcu"] }}</strong>
                                </td>
                        @endif
                        @if (in_array("buyer",(array_keys($chats))))
                                <td style="width:10%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Buyer') }}</strong>
                                </td>
                                <td>
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $chats["buyer"] }}</strong>
                                </td>
                        @endif
                    </tr>
                </table>
            </div>
        {{-- </div> --}}
        <div style="clear : both;"></div>
        {{-- <div style="margin: 25px 0;">
            <div style="width:40%;margin : 5px; float:left;border-radius : 5px; height:auto;">
                <table style="margin: 0;padding: 0; width:100%;" class="tableType" >
                    <tr style="background-color: #C4E1DD;">
                        <td style="width: 20%"><img src="{{ public_path().'/images/OrderIconColored.png' }}" style="padding:15px 10px"/></td>
                        <td style="line-height: 1;">
                            <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                <strong>{{ trans('WebSite.Order') }}</strong></p>
                            <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ $chats['orderNo'] }}</strong></p>
                        </td>
                    </tr>
                    <tr><td colspan="2" style="padding:4px;"></td></tr>
                    <tr style="background-color: #C4E1DD;">
                        <td style="width: 20%"><img src="{{ public_path().'/images/StyleIconColored.png' }}" style="padding:15px 10px"/></td>
                        <td style="line-height: 1;">
                            <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                <strong>{{ trans('WebSite.Style') }}</strong></p>
                            <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ $chats["styleNo"] }}</strong></p>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:40%; float:right;margin : 5px;border-radius : 5px; height:auto;">
                <table style="margin: 0;padding: 0; width:100%;" class="tableType" >
                    @if (in_array("factory",(array_keys($chats))))
                        <tr style="background-color: #C4E1DD;">
                            <td style="width: 20%"><img src="{{ public_path().'/images/FactoryColored.png' }}" style="padding:15px 10px"/></td>
                            <td style="line-height: 1;">
                                <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.Factory') }}</strong></p>
                                <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ $chats["factory"] }}</strong></p>
                            </td>
                        </tr>
                        <tr><td colspan="2" style="padding:4px;"></td></tr>
                    @endif
                    @if (in_array("pcu",(array_keys($chats))))
                        <tr style="background-color: #C4E1DD;">
                            <td style="width: 20%"><img src="{{ public_path().'/images/PCUColored.png' }}" style="padding:15px 10px"/></td>
                            <td style="line-height: 1;">
                                <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.PCU') }}</strong></p>
                                <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ $chats["pcu"] }}</strong></p>
                            </td>
                        </tr>
                        <tr><td colspan="2" style="padding:4px;"></td></tr>
                    @endif
                    @if (in_array("buyer",(array_keys($chats))))
                        <tr style="background-color: #C4E1DD;">
                            <td style="width: 20%"><img src="{{ public_path().'/images/BuyerColored.png' }}" style="padding:15px 10px"/></td>
                            <td style="line-height: 1;">
                                <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.Buyer') }}</strong></p>
                                <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ $chats["buyer"] }}</strong></p>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
        </div> --}}
    </div>
    <div style="clear : both;"></div>
    <div style="background-color: #f0efef;font-weight:600;padding-bottom:5px;padding-top:0px;margin-top:5px;">
        <strong><span style="margin-left:10px;">{{ $chats['title']}}</span> <span>{{ " - ".$chats['subtitle'] }}</span>
        <span>{{ " - ".$chats['pic'] }}</span></strong>
    </div>
    <?php
        function getNonTextType($type,$time=""){
            $nonTextType = "";
            if($type == 1){
                /* Approved */
                // $nonTextType='<span style="border-radius:50%;background-color:#3FC984;width:6px;height:6px;display: inline-block; "> </span>
                // <span style="font-size:10px;color:#3FC984;margin-top:3px;">'.trans('WebSite.approved').'</span>
                // <span style="font-size:8px;">'.$time.'</span>';
                $nonTextType='<span style="font-size:8px;">'.$time.'</span>';
            }
            elseif($type == 2){
                /* Rejected */
                // $nonTextType='<span style="border-radius:50%;background-color:#EF6565;width:6px;height:6px;display: inline-block; "> </span>
                // <span style="font-size:10px;color:#EF6565;margin-top:3px;">'.trans('WebSite.rejected').'</span>
                // <span style="font-size:8px;">'.$time.'</span>';
                $nonTextType='<span style="font-size:8px;">'.$time.'</span>';
            }
            elseif($type == 3){
                /* Submission */
                $nonTextType='<span style="font-size:8px;">'.$time.'</span>';
            }
            elseif($type == 4){
                /* Re-submission */
                $nonTextType='<span style="font-size:8px;">'.$time.'</span>';
            }
            return $nonTextType;
        }
    ?>
    <table class="mainTable" style="border-collapse: collapse;background-color:#f0efef;width:100%;">
        @foreach ($chats['taskChats'] as $chat)
            @if ($chat['comment_type']==1)
                <tr class="{{ $chat['comment_type']===1 ? 'request' : 'response' }}">
                    {{-- Check Chat Text Type Starts --}}
                    <td style="width:20%;padding-left:5px;padding-right:5px;">
                        @if ($chat['text_type'] != "text")
                            @if ($chat['text_type'] === "image")
                                <div style="margin-left:10px;text-align: left;margin-top:5px">
                                    <img style="margin-left:10px;display: inline-block;margin-top:35px;" src={{ $chats['serverURL'].$chat['text'] }}
                                    height="100" />
                                    {!!  getNonTextType($chat['comment_status'])  !!}
                                </div>
                            @else
                                <div style="margin-left:10px; font-size:12px;text-align: left;">
                                    <strong style="margin-left:10px;cursor: pointer;text-decoration: underline;word-wrap: break-word">
                                            <a href={{ $chats['serverURL'].$chat['text'] }}>
                                    {{ $chat['original_name'] }}</a></strong>
                                    {!!  getNonTextType($chat['comment_status'])  !!}
                                </div>
                            @endif
                            {{-- To Check if reply also has a non text content --}}
                            @if (count($chats['taskChatsReplies'])>0)
                                @foreach ($chats['taskChatsReplies'] as $replies)
                                    @if ($replies['comment_type']==1)
                                        @if ($replies['reply_to_id'] == $chat['id'] && $replies['text_type'] != 'text')
                                            @if ($replies['text_type'] === "image")
                                                <div style="margin-left:10px;text-align: left;margin-top:5px">
                                                    <img style="margin-left:10px;display: inline-block;margin-top:35px;"
                                                    src={{ $chats['serverURL'].$replies['text'] }} height="150" />
                                                    {!!  getNonTextType($replies['comment_status'])  !!}
                                                </div>
                                            @else
                                                <div style="margin-left:10px; font-size:12px;text-align: left;">
                                                    <strong style="margin-left:10px;cursor: pointer;text-decoration: underline;word-break: break-all">
                                                        <a href={{ $chats['serverURL'].$replies['text'] }}>
                                                    {{ $replies['original_name'] }}</a></strong>
                                                    {!!  getNonTextType($replies['comment_status'])  !!}
                                                </div>
                                            @endif
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        @else
                            {{-- To Check if the replies has no text content --}}
                            @if (count($chats['taskChatsReplies'])>0)
                                @foreach ($chats['taskChatsReplies'] as $replies)
                                    @if ($replies['comment_type']==1)
                                        @if ($replies['reply_to_id'] == $chat['id'] && $replies['text_type'] != 'text')
                                            @if ($replies['text_type'] === "image")
                                                <div style="text-align: left;margin-top:5px">
                                                    <img style="margin-left:10px;margin-top:35px;"
                                                    src={{ $chats['serverURL'].$replies['text'] }} height="100" />
                                                    {!!  getNonTextType($replies['comment_status'])  !!}
                                                </div>
                                            @else
                                                <div style="font-size:12px;text-align: left;">
                                                    <strong style="margin-left:10px;cursor: pointer;
                                                    text-decoration: underline;word-break: break-all">
                                                    <a href={{ $chats['serverURL'].$replies['text'] }}>
                                                    {{ $replies['original_name'] }}</a></strong>
                                                    {!!  getNonTextType($replies['comment_status'])  !!}
                                                </div>
                                            @endif
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        @endif
                    </td>
                    {{-- Check Chat Text Type Ends --}}
                    {{-- The Text Part Starts --}}
                    <td style="width:80%;padding-left:5px;padding-right:5px;">
                        @if ($chat['text_type'] === "text")
                            <div style="">{{ $chat['text'] }} {!!  getNonTextType($chat['comment_status'],$chat['send_at'])  !!}</div>
                            {{-- Check if the replied text has text content with the replied to text content --}}
                            @if (count($chats['taskChatsReplies'])>0)
                                @foreach ($chats['taskChatsReplies'] as $replies)
                                    @if ($replies['comment_type']==1)
                                        @if ($replies['reply_to_id'] == $chat['id'] && $replies['text_type'] === 'text')
                                                <div style="">{{ $replies['text'] }}
                                                    {!!  getNonTextType($replies['comment_status'],$replies['send_at'])  !!}</div>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        @else
                            {{-- Check if the replied text has text content --}}
                            @if (count($chats['taskChatsReplies'])>0)
                                @foreach ($chats['taskChatsReplies'] as $replies)
                                    @if ($replies['comment_type']==1)
                                        @if ($replies['reply_to_id'] == $chat['id'] && $replies['text_type'] === 'text')
                                                <div style="">{{ $replies['text'] }}
                                                    {!!  getNonTextType($replies['comment_status'],$replies['send_at'])  !!}</div>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        @endif
                    </td>
                    {{-- The Text Part Ends --}}
                </tr>
            @endif
            @if (count($chats['taskChatsReplies'])>0)
                <tr class="response">
                    {{-- Check Chat Text Type Starts --}}
                    <td style="width:20%;padding-left:5px;padding-right:5px;">
                        {{-- To Check if reply also has a non text content --}}
                        {{-- @if (count($chats['taskChatsReplies'])>0) --}}
                        @foreach ($chats['taskChatsReplies'] as $replies)
                            @if ($replies['comment_type']==2)
                                @if ($replies['reply_to_id'] == $chat['id'] && $replies['text_type'] != 'text')
                                    @if ($replies['text_type'] === "image")
                                        <br>
                                        <div style="margin-left:10px;text-align: left;">
                                            <img style="margin-left:10px;display: inline-block;"
                                            src={{ $chats['serverURL'].$replies['text'] }} height="100" />
                                            {!!  getNonTextType($replies['comment_status'])  !!}
                                        </div>
                                    @else
                                        <div style="margin-left:10px; font-size:12px;text-align: left;">
                                            <strong style="margin-left:10px;cursor: pointer;text-decoration: underline;
                                            word-break: break-all">
                                                <a href={{ $chats['serverURL'].$replies['text'] }}>
                                            {{ $replies['original_name'] }}</a></strong>
                                            {!!  getNonTextType($replies['comment_status'])  !!}
                                        </div>
                                    @endif
                                @endif
                            @endif
                        @endforeach
                    </td>
                    {{-- Check Chat Text Type Ends --}}
                    {{-- The Text Part Starts --}}
                    <td style="width:80%;padding-left:5px;padding-right:5px;">
                        @foreach ($chats['taskChatsReplies'] as $replies)
                            @if ($replies['comment_type']==2)
                                @if ($replies['reply_to_id'] == $chat['id'] && $replies['text_type'] === 'text')
                                        <div style="">{{ $replies['text'] }}
                                            {!!  getNonTextType($replies['comment_status'],$replies['send_at'])  !!}</div>
                                @endif
                            @endif
                        @endforeach
                    </td>
                    {{-- The Text Part Ends --}}
                </tr>
            @endif
        @endforeach
    </table>
    <footer>
        <script type="text/php">
            if (isset($pdf)) {
               $font = $fontMetrics->getFont("Poppins", "bold");
               {{-- $pdf->page_text(28,500, "{{ trans('WebSite.Production') }}", $font, 9, array(0, 0, 0)); --}}
               $pdf->page_text(731,568, "{{ date($chats['dateFormat']) }}  {PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
            }
        </script>
    </footer>
</body>
</html>
