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
    </style>
</head>
<body style="font-family: poppins,arialuni,notosansjp,poppins-semibold;font-size: 14px;width:100%">
    <div style="margin:25px 0;">
        <img src="{{ public_path().'/images/dms-log-with-tag.png' }}" style="background-color: #FFFFFF; height: 98px; width:197px" />
        <div style="float:right; font-size:32px; font-weight:600; color: #8C878D; margin-top:-15px">
        <strong>{{ trans('WebSite.taskComms') }}</strong>
            <div style="background-color: #D1E7E4; font-size:16px; color: #178677; text-align:center;font-weight: 600;
            padding: 1px 3px 5px;">
                <img src="{{ public_path().'/images/CalendarIcon.svg' }}"/> {{ date('d M Y') }}
            </div>
        </div>
        <div style="clear : both;"></div>
        <div style="margin: 25px 0;">
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
        </div>
    </div>
    <div style="clear : both;"></div>
    <div style="background-color: #C4E1DD; color: #000000; font-weight:600;padding-bottom:5px;padding-top:0px;">
        <strong><span style="margin-left:10px;">{{ $chats['title']}}</span> <span>{{ " - ".$chats['subtitle'] }}</span>
        <span>{{ " - ".$chats['pic'] }}</span></strong>
    </div>
    <?php
        function getNonTextType($type){
            $nonTextType = "";
            // if($type == 1){
            //     $nonTextType='<span style="border-radius:50%;background-color:#3FC984;width:6px;height:6px;display: inline-block;"> </span>';
            // }
            // elseif($type == 2){
            //     $nonTextType='<span style="border-radius:50%;background-color:#EF6565;width:6px;height:6px;display: inline-block;"> </span>';
            // }
            // elseif($type == 3){
            //     $nonTextType='<span style="border-radius:50%;background-color:#009688;width:6px;height:6px;display: inline-block;"> </span>';
            // }
            // elseif($type == 4){
            //     $nonTextType='<span style="border-radius:50%;background-color:#529ED1;width:6px;height:6px;display: inline-block;"> </span>';
            // }
            if($type == 1){
                $nonTextType='<span style="border-radius:50%;background-color:#3FC984;width:6px;height:6px;display: inline-block; "> </span>
                <span style="font-size:10px;color:#3FC984;margin-top:3px;">'.trans('WebSite.approved').'</span>';
            }
            elseif($type == 2){
                $nonTextType='<span style="border-radius:50%;background-color:#EF6565;width:6px;height:6px;display: inline-block; "> </span>
                <span style="font-size:10px;color:#EF6565;margin-top:3px;">'.trans('WebSite.rejected').'</span>';
            }
            elseif($type == 3){
                $nonTextType='<span style="border-radius:50%;background-color:#009688;width:6px;height:6px;display: inline-block; "> </span>
                <span style="font-size:10px;color:#009688;margin-top:3px;">'.trans('WebSite.submission').'</span>';
            }
            elseif($type == 4){
                $nonTextType='<span style="border-radius:50%;background-color:#529ED1;width:6px;height:6px;display: inline-block; "> </span>
                <span style="font-size:10px;color:#529ED1;margin-top:3px;">'.trans('WebSite.reSubmission').'</span>';
            }
            return $nonTextType;
        }
    ?>
    <table class="mainTable" style="border-collapse: collapse;background-color:#f3f2f2;width:100%;">
        @foreach ($chats['taskChats'] as $chat)
            <tr class="{{ $chat['comment_type']===1 ? 'request' : 'response' }}">
                {{-- Chat Type Starts --}}
                @if ($chat['comment_type']===1)
                    <td style="width:10%;padding-left:5px;padding-right:5px;"><span><strong>{{ trans('WebSite.request') }}</strong></span></td>
                @else
                    <td style="width:10%;padding-left:5px;padding-right:5px;"><span><strong>{{ trans('WebSite.response') }}</strong></span>
                        <span class="dot"></span></td>
                @endif
                {{-- Chat Type Ends --}}
                {{-- Check Chat Text Type Starts --}}
                <td style="width:35%;padding-left:5px;padding-right:5px;">
                    @if ($chat['text_type'] != "text")
                        @if ($chat['text_type'] === "image")
                            <div style="margin-left:10px;text-align: left;margin-top:5px">
                                <img style="margin-left:10px;display: inline-block;margin-top:35px;" src={{ $chats['serverURL'].$chat['text'] }} height="150" />
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
                                @if ($replies['reply_to_id'] == $chat['id'] && $replies['text_type'] != 'text')
                                    @if ($replies['text_type'] === "image")
                                        <div style="margin-left:10px;text-align: left;margin-top:5px">
                                            <img style="margin-left:10px;display: inline-block;margin-top:35px;" src={{ $chats['serverURL'].$replies['text'] }} height="150" />
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
                            @endforeach
                        @endif
                    @else
                        {{-- To Check if the replies has no text content --}}
                        @if (count($chats['taskChatsReplies'])>0)
                            @foreach ($chats['taskChatsReplies'] as $replies)
                                @if ($replies['reply_to_id'] == $chat['id'] && $replies['text_type'] != 'text')
                                    @if ($replies['text_type'] === "image")
                                        <div style="text-align: left;margin-top:5px">
                                            <img style="margin-left:10px;margin-top:35px;" src={{ $chats['serverURL'].$replies['text'] }} height="150" />
                                            {!!  getNonTextType($replies['comment_status'])  !!}
                                        </div>
                                    @else
                                        <div style="font-size:12px;text-align: left;">
                                            <strong style="margin-left:10px;cursor: pointer;text-decoration: underline;word-break: break-all"><a href={{ $chats['serverURL'].$replies['text'] }}>
                                            {{ $replies['original_name'] }}</a></strong>
                                            {!!  getNonTextType($replies['comment_status'])  !!}
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    @endif
                </td>
                {{-- Check Chat Text Type Ends --}}
                {{-- The Text Part Starts --}}
                <td style="width:50%;padding-left:5px;padding-right:5px;">
                    @if ($chat['text_type'] === "text")
                        <div style="">{{ $chat['text'] }} {!!  getNonTextType($chat['comment_status'])  !!}</div>
                        {{-- Check if the replied text has text content with the replied to text content --}}
                        @if (count($chats['taskChatsReplies'])>0)
                            @foreach ($chats['taskChatsReplies'] as $replies)
                                @if ($replies['reply_to_id'] == $chat['id'] && $replies['text_type'] === 'text')
                                        <div style="">{{ $replies['text'] }} {!!  getNonTextType($replies['comment_status'])  !!}</div>
                                @endif
                            @endforeach
                        @endif
                    @else
                        {{-- Check if the replied text has text content --}}
                        @if (count($chats['taskChatsReplies'])>0)
                            @foreach ($chats['taskChatsReplies'] as $replies)
                                @if ($replies['reply_to_id'] == $chat['id'] && $replies['text_type'] === 'text')
                                        <div style="">{{ $replies['text'] }} {!!  getNonTextType($replies['comment_status'])  !!}</div>
                                @endif
                            @endforeach
                        @endif
                    @endif
                </td>
                {{-- The Text Part Ends --}}
            </tr>
        @endforeach
    </table>
    @if (array_key_exists('subtasks',$chats) && count($chats['subtasks'])>0)
        @foreach ( $chats['subtasks'] as $subtaskChats )
            <div style="background-color: #C4E1DD; color: #000000; font-weight:600;padding-bottom:5px;padding-top:0px;margin-top:10px;">
                <strong><span style="margin-left:10px;">{{ $chats['title']}}</span> <span>{{ " - ".$chats['subtitle'] }}</span>
                <span>{{ " - ".$subtaskChats['subtasktitle'] }}</span> <span>{{ " - ".$subtaskChats['pic'] }}</span></strong>
            </div>
            <table class="mainTable" style="border-collapse: collapse;background-color:#f3f2f2;width:100%;">
                @foreach ($subtaskChats['chats'] as $chat)
                    <tr class="{{ $chat['comment_type']===1 ? 'request' : 'response' }}">
                        {{-- Chat Type Starts --}}
                        @if ($chat['comment_type']===1)
                            <td style="width:10%;padding-left:5px;padding-right:5px;"><span><strong>{{ trans('WebSite.request') }}</strong></span></td>
                        @else
                            <td style="width:10%;padding-left:5px;padding-right:5px;"><span><strong>{{ trans('WebSite.response') }}</strong></span></td>
                        @endif
                        {{-- Chat Type Ends --}}
                        {{-- Check Chat Text Type Starts --}}
                        <td style="width:35%;padding-left:5px;padding-right:5px;">
                            @if ($chat['text_type'] != "text")
                                @if ($chat['text_type'] === "image")
                                    <div style="margin-left:10px;text-align: left;margin-top:5px">
                                        <img style="margin-left:10px;display: inline-block;margin-top:35px;" src={{ $chats['serverURL'].$chat['text'] }} height="150" />
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
                                @if (count($subtaskChats['taskChatsReplies'])>0)
                                    @foreach ($subtaskChats['taskChatsReplies'] as $replies)
                                        @if ($replies['reply_to_id'] == $chat['id'] && $replies['text_type'] != 'text')
                                            @if ($replies['text_type'] === "image")
                                                <div style="margin-left:10px;text-align: left;margin-top:5px">
                                                    <img style="margin-left:10px;display: inline-block;margin-top:35px;" src={{ $chats['serverURL'].$replies['text'] }} height="150" />
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
                                    @endforeach
                                @endif
                            @else
                                {{-- To Check if the replies has no text content --}}
                                @if (count($subtaskChats['taskChatsReplies'])>0)
                                    @foreach ($subtaskChats['taskChatsReplies'] as $replies)
                                        @if ($replies['reply_to_id'] == $chat['id'] && $replies['text_type'] != 'text')
                                            @if ($replies['text_type'] === "image")
                                                <div style="text-align: left;margin-top:5px">
                                                    <img style="margin-left:10px;margin-top:35px;" src={{ $chats['serverURL'].$replies['text'] }} height="150" />
                                                    {!!  getNonTextType($replies['comment_status'])  !!}
                                                </div>
                                            @else
                                                <div style="font-size:12px;text-align: left;">
                                                    <strong style="margin-left:10px;cursor: pointer;text-decoration: underline;word-break: break-all"><a href={{ $chats['serverURL'].$replies['text'] }}>
                                                    {{ $replies['original_name'] }}</a></strong>
                                                    {!!  getNonTextType($replies['comment_status'])  !!}
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                @endif
                            @endif
                        </td>
                        {{-- Check Chat Text Type Ends --}}
                        {{-- The Text Part Starts --}}
                        <td style="width:50%;padding-left:5px;padding-right:5px;">
                            @if ($chat['text_type'] === "text")
                                <div style="">{{ $chat['text'] }} {!!  getNonTextType($chat['comment_status'])  !!}</div>
                                {{-- Check if the replied text has text content with the replied to text content --}}
                                @if (count($subtaskChats['taskChatsReplies'])>0)
                                    @foreach ($subtaskChats['taskChatsReplies'] as $replies)
                                        @if ($replies['reply_to_id'] == $chat['id'] && $replies['text_type'] === 'text')
                                                <div style="">{{ $replies['text'] }} {!!  getNonTextType($replies['comment_status'])  !!}</div>
                                        @endif
                                    @endforeach
                                @endif
                            @else
                                {{-- Check if the replied text has text content --}}
                                @if (count($subtaskChats['taskChatsReplies'])>0)
                                    @foreach ($subtaskChats['taskChatsReplies'] as $replies)
                                        @if ($replies['reply_to_id'] == $chat['id'] && $replies['text_type'] === 'text')
                                                <div style="">{{ $replies['text'] }} {!!  getNonTextType($replies['comment_status'])  !!}</div>
                                        @endif
                                    @endforeach
                                @endif
                            @endif
                        </td>
                        {{-- The Text Part Ends --}}
                    </tr>
                @endforeach
            </table>
        @endforeach
    @endif
    {{-- <footer style="margin-top: 15px;width: 100%;background-color: #D1E7E4; font-size:14px; color: #178677; text-align:center;
    padding: 1px 3px 5px;">
    <strong>
        {!! trans('WebSite.PDFFooter',['date'=>$tasks['orderLastDate']]) !!}
    </strong>
    </footer> --}}
    <footer>
        <script type="text/php">
            if (isset($pdf)) {
               $font = $fontMetrics->getFont("poppins", "bold");
               $pdf->page_text(525, 805, "Page {PAGE_NUM}/{PAGE_COUNT}", $font, 12, array(0, 0, 0));
            }
        </script>
    </footer>
</body>
</html>
