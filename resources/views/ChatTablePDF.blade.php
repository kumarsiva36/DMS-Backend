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
        <?php $i=0  ?>
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
    <div style="background-color: #EBEBEB; color: #000000; font-weight:600;padding-bottom:5px;padding-top:0px;">
        <strong><span style="margin-left:10px;">{{ $chats['title']}}</span> <span>{{ " - ".$chats['subtitle'] }}</span>
            <span>{{ " - ".$chats['pic'] }}</span></strong>
    </div>
    @foreach ( $chats['taskChats'] as $chat )
        @if (($chat['comment_type'] === 1))
            <div style="float: right; width:45%;background-color:#B2D9D6;margin-top:5px;margin-bottom:3px;
            word-wrap: break-word;border: 1px solid #B2D9D6;border-radius:7px;">
                {{-- <div style="color:#000000;text-align: right;padding:1px;margin-right:5px;"><strong>{{ $chat['sender'] }}</strong></div> --}}
                @if ($chat['text_type'] === "image")
                    <div style="margin-left:10px;text-align: left;margin-top:5px"><img style="margin-left:10px;" src={{ $chats['serverURL'].$chat['text'] }} height="150" /></div>
                @elseif ($chat['text_type'] === "pdf")
                    <div style="margin-left:10px; font-size:12px;text-align: left"><strong style="margin-left:10px;"><a href={{ $chats['serverURL'].$chat['text'] }}>
                        {{ $chats['serverURL'].$chat['text'] }}</a></strong></div>
                @elseif ($chat['text_type'] === "excel")
                    <div style="margin-left:10px; font-size:12px;text-align: left"><strong style="margin-left:10px;"><a href={{ $chats['serverURL'].$chat['text'] }}>
                        {{ $chats['serverURL'].$chat['text'] }}</a></strong></div>
                @elseif ($chat['text_type'] === "docx")
                    <div style="margin-left:10px; font-size:12px;text-align: left"><strong style="margin-left:10px;"><a href={{ $chats['serverURL'].$chat['text'] }}>
                        {{ $chats['serverURL'].$chat['text'] }}</a></strong></div>
                @else
                    <div style="margin-left:10px;text-align: left">{{ $chat['text'] }}</div>
                @endif
                <div style="color:#000000;text-align: right;padding:1px;margin-right:5px;">{{ $chat['send_at'] }}</div>
            </div>
            <div style="clear : both;"></div>
        @else
            <div style="float: left;width:45%;background-color:#E5E5E5;margin-top:5px;margin-bottom:3px;
            word-wrap: break-word;border: 1px solid #E5E5E5;border-radius:7px;">
                {{-- <div style="color:#000000;text-align: left;padding:1px;margin-left:10px;"><strong>{{ $chat['sender'] }}</strong></div> --}}
                @if ($chat['text_type'] === "image")
                    <div style="margin-left:10px;margin-right:10px;margin-left:15px;margin-top:5px;">
                        <img src={{ $chats['serverURL'].$chat['text'] }} height="150"/></div>
                @elseif ($chat['text_type'] === "pdf")
                    <div style="margin-left:10px; font-size:12px;margin-right:10px;margin-left:15px;">
                        <strong><a href={{ $chats['serverURL'].$chat['text'] }}>{{ $chats['serverURL'].$chat['text'] }}</a></strong></div>
                @elseif ($chat['text_type'] === "excel")
                    <div style="margin-left:10px; font-size:12px;margin-right:10px;margin-left:15px;">
                        <strong><a href={{ $chats['serverURL'].$chat['text'] }}>{{ $chats['serverURL'].$chat['text'] }}</a></strong></div>
                @elseif ($chat['text_type'] === "docx")
                    <div style="margin-left:10px; font-size:12px;margin-right:10px;margin-left:15px;">
                        <strong><a href={{ $chats['serverURL'].$chat['text'] }}>{{ $chats['serverURL'].$chat['text'] }}</a></strong></div>
                @else
                    <div style="margin-left:10px;margin-right:10px;margin-left:15px;">{{ $chat['text'] }}</div>
                @endif
                <div style="color:#000000;text-align: right;padding:1px;margin-right:10px;">{{ $chat['send_at'] }}</div>
            </div>
            <div style="clear : both;"></div>
        @endif
    @endforeach
    @if (array_key_exists('subtasks',$chats) && count($chats['subtasks'])>0)
        @foreach ( $chats['subtasks'] as $subtaskChats )
            <div style="background-color: #EBEBEB; color: #000000; font-weight:600;padding-bottom:5px;padding-top:0px;margin-top:10px;">
                <strong><span style="margin-left:10px;">{{ $chats['title']}}</span> <span>{{ " - ".$chats['subtitle'] }}</span>
                <span>{{ " - ".$subtaskChats['subtasktitle'] }}</span> <span>{{ " - ".$subtaskChats['pic'] }}</span></strong>
            </div>
            @foreach ($subtaskChats['chats'] as $subtaskChat)
            @if (($subtaskChat['comment_type'] === 1) )
            <div style="float: right; width:45%;background-color:#B2D9D6;margin-top:5px;margin-bottom:3px;
                    word-wrap: break-word;border: 1px solid #B2D9D6;border-radius:7px;">
                        {{-- <div style="color:#000000;text-align: right;padding:1px;margin-right:5px;"><strong>{{ $subtaskChat['sender'] }}</strong></div> --}}
                        @if ($subtaskChat['text_type'] === "image")
                            <div style="margin-left:10px;text-align: left;margin-top:5px"><img style="margin-left:10px;" src={{ $chats['serverURL'].$subtaskChat['text'] }} height="150"/></div>
                        @elseif ($subtaskChat['text_type'] === "pdf")
                            <div style="margin-left:10px; font-size:12px;text-align: left"><strong style="margin-left:10px;"><a href={{ $chats['serverURL'].$subtaskChat['text'] }}>
                                {{ $chats['serverURL'].$subtaskChat['text'] }}</a></strong></div>
                        @elseif ($subtaskChat['text_type'] === "excel")
                            <div style="margin-left:10px; font-size:12px;text-align: left"><strong style="margin-left:10px;"><a href={{ $chats['serverURL'].$subtaskChat['text'] }}>
                                {{ $chats['serverURL'].$subtaskChat['text'] }}</a></strong></div>
                        @elseif ($subtaskChat['text_type'] === "docx")
                            <div style="margin-left:10px; font-size:12px;text-align: left"><strong style="margin-left:10px;"><a href={{ $chats['serverURL'].$subtaskChat['text'] }}>
                                {{ $chats['serverURL'].$subtaskChat['text'] }}</a></strong></div>
                        @else
                            <div style="margin-left:10px;text-align: left">{{ $subtaskChat['text'] }}</div>
                        @endif
                        <div style="color:#000000;text-align: right;padding:1px;margin-right:5px;">{{ $subtaskChat['send_at'] }}</div>
                    </div>
                    <div style="clear : both;"></div>
                @else
                    <div style="float: left;width:45%;background-color:#E5E5E5;margin-top:5px;margin-bottom:3px;
                    word-wrap: break-word;border: 1px solid #E5E5E5;border-radius:7px;">
                        {{-- <div style="color:#000000;text-align: left;padding:1px;margin-left:10px;"><strong>{{ $subtaskChat['sender'] }}</strong></div> --}}
                        @if ($subtaskChat['text_type'] === "image")
                            <div style="margin-left:10px;margin-right:10px;margin-left:15px;margin-top:5px">
                                <img src={{ $chats['serverURL'].$subtaskChat['text'] }} height="150"/></div>
                        @elseif ($subtaskChat['text_type'] === "pdf")
                            <div style="margin-left:10px; font-size:12px;margin-right:10px;margin-left:15px;">
                                <strong><a href={{ $chats['serverURL'].$subtaskChat['text'] }}>{{ $chats['serverURL'].$subtaskChat['text'] }}</a></strong></div>
                        @elseif ($subtaskChat['text_type'] === "excel")
                            <div style="margin-left:10px; font-size:12px;margin-right:10px;margin-left:15px;">
                                <strong><a href={{ $chats['serverURL'].$subtaskChat['text'] }}>{{ $chats['serverURL'].$subtaskChat['text'] }}</a></strong></div>
                        @elseif ($subtaskChat['text_type'] === "docx")
                            <div style="margin-left:10px; font-size:12px;margin-right:10px;margin-left:15px;">
                                <strong><a href={{ $chats['serverURL'].$subtaskChat['text'] }}>{{ $chats['serverURL'].$subtaskChat['text'] }}</a></strong></div>
                        @else
                            <div style="margin-left:10px;margin-right:10px;margin-left:15px;">{{ $subtaskChat['text'] }}</div>
                        @endif
                        <div style="color:#000000;text-align: right;padding:1px;margin-right:10px;">{{ $subtaskChat['send_at'] }}</div>
                    </div>
                    <div style="clear : both;"></div>
                @endif
            @endforeach
        @endforeach
    @endif
    {{-- <footer style="margin-top: 15px;width: 100%;background-color: #D1E7E4; font-size:14px; color: #178677; text-align:center;
    padding: 1px 3px 5px;">
    <strong>
        {!! trans('WebSite.PDFFooter',['date'=>$tasks['orderLastDate']]) !!}
    </strong>
    </footer> --}}
</body>
</html>
