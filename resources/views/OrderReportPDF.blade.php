<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Report</title>
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
            <strong>{{ trans('WebSite.orderReport') }}</strong>
            <div style="background-color: #D1E7E4; font-size:16px; color: #178677; text-align:center;font-weight: 600;
            padding: 1px 3px 5px;">
                <img src="{{ public_path().'/images/CalendarIcon.svg' }}"/> {{ date($orderData['dateFormat']) }}
            </div>
        </div> --}}
        <div style="font-size:18px;font-weight: 600;text-align:center; padding: 1px 3px 5px;">
            <span><strong>{{ trans('WebSite.orderReport') }}</strong></span>
        </div>
        <div style="clear : both;"></div>
        <?php $i=0  ?>
        <div>
            {{-- <div style="width:100%;border-radius : 5px; height:auto;">
                @if (in_array("factory",(array_keys($orderData))))
                        <table style="margin: 0;padding: 0; width:31%; float:left;" class="tableType" >
                            <tr style="background-color: #C4E1DD;">
                                <td style="width: 20%;"><img src="{{ public_path().'/images/FactoryColored.png' }}" style="padding:15px"/></td>
                                <td style="line-height: 1;">
                                    <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                        <strong>{{ trans('WebSite.Factory') }}</strong></p>
                                    @if ($orderData["factory"] === "All")
                                    <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ trans('WebSite.All') }}</strong></p>
                                    @else
                                    <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ $orderData["factory"] }}</strong></p>
                                    @endif
                                </td>
                            </tr>
                        </table>
                @endif
                @if (in_array("buyer",(array_keys($orderData))))
                    @if (!(in_array("factory",(array_keys($orderData)))))
                        <table style="margin: 0;padding: 0; width:31%; float:left;" class="tableType" >
                            <tr style="background-color: #C4E1DD;">
                                <td style="width: 20%;"><img src="{{ public_path().'/images/BuyerColored.png' }}" style="padding:12px"/></td>
                                <td style="line-height: 1;width: 80%;">
                                    <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                        <strong>{{ trans('WebSite.Buyer') }}</strong></p>
                                        @if ($orderData["buyer"] === "All")
                                        <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ trans('WebSite.All') }}</strong></p>
                                        @else
                                        <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ $orderData["buyer"] }}</strong></p>
                                        @endif
                                </td>
                            </tr>
                        </table>
                    @else
                        <table style="margin: 0 15px 0;padding: 0; width:31%; float:left;" class="tableType" >
                            <tr style="background-color: #C4E1DD;">
                                <td style="width: 20%;"><img src="{{ public_path().'/images/BuyerColored.png' }}" style="padding:12px"/></td>
                                <td style="line-height: 1;width: 80%;">
                                    <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                        <strong>{{ trans('WebSite.Buyer') }}</strong></p>
                                        @if ($orderData["buyer"] === "All")
                                        <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ trans('WebSite.All') }}</strong></p>
                                        @else
                                        <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ $orderData["buyer"] }}</strong></p>
                                        @endif
                                </td>
                            </tr>
                        </table>
                    @endif
                @endif
                @if (in_array("pcu",(array_keys($orderData))))
                    <table style="margin: 0 15px 0;padding: 0; width:31%; float:left;" class="tableType" >
                        <tr style="background-color: #C4E1DD;">
                            <td style="width: 20%;"><img src="{{ public_path().'/images/PCUColored.png' }}" style="padding:13px 15px"/></td>
                            <td style="line-height: 1;">
                                <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.PCU') }}</strong></p>
                                    @if ($orderData["pcu"] === "All")
                                    <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ trans('WebSite.All') }}</strong></p>
                                    @else
                                    <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ $orderData["pcu"] }}</strong></p>
                                    @endif
                            </td>
                        </tr>
                    </table>
                @endif
                    <table style="margin: 0;padding: 0; width:33%; float:right;" class="tableType" >
                        <tr style="background-color: #C4E1DD;">
                            <td style="line-height: 1;padding:3px 25px;">
                                <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.orderStatus') }}</strong></p>
                                <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;"><strong>{{ trans('WebSite.'.$orderData["statusFilter"]) }}</strong></p>
                            </td>
                        </tr>
                    </table>
            </div> --}}
            <div>
                <table style="width: 100%; border-collapse: collapse;font-size:10px;" cellspacing="1px" class="headTable">
                    @if($orderData['useLogo']==1 && $orderData['userLogo']!='')
                        <tr style="">
                            <td width="15%">
                                <img src="{{ $orderData['userLogo'] }}"
                                style="background-color: #FFFFFF; height: 40px;margin-left:5px;" />
                            </td>
                            @if (in_array("factory",(array_keys($orderData))))
                                <td style="width:10%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Factory') }}</strong>
                                </td>
                                <td>
                                    @if ($orderData["factory"] === "All")
                                    <p style="padding-left: 0px;word-wrap: break-word;margin-left:5px;"><strong>{{ trans('WebSite.All') }}</strong></p>
                                    @else
                                    <p style="padding-left: 0px;word-wrap: break-word;margin-left:5px;"><strong>{{ $orderData["factory"] }}</strong></p>
                                    @endif
                                </td>
                            @endif
                            @if (in_array("pcu",(array_keys($orderData))))
                                <td style="width:10%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.PCU') }}</strong>
                                </td>
                                <td>
                                    @if ($orderData["pcu"] === "All")
                                    <p style="padding-left: 0px;word-wrap: break-word;margin-left:5px;"><strong>{{ trans('WebSite.All') }}</strong></p>
                                    @else
                                    <p style="padding-left: 0px;word-wrap: break-word;margin-left:5px;"><strong>{{ $orderData["pcu"] }}</strong></p>
                                    @endif
                                </td>
                            @endif
                            @if (in_array("buyer",(array_keys($orderData))))
                                <td style="width:10%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Buyer') }}</strong>
                                </td>
                                <td>
                                    @if ($orderData["buyer"] === "All")
                                    <p style="padding-left: 0px;word-wrap: break-word;margin-left:5px;"><strong>{{ trans('WebSite.All') }}</strong></p>
                                    @else
                                    <p style="padding-left: 0px;word-wrap: break-word;margin-left:5px;"><strong>{{ $orderData["buyer"] }}</strong></p>
                                    @endif
                                </td>
                            @endif
                            <td style="width:15%;background-color: #f0efef;">
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.orderStatus') }}</strong>
                            </td>
                            <td>
                                <p style="padding-left: 0px;word-wrap: break-word;margin-left:5px;">
                                    <strong>{{ trans('WebSite.'.$orderData["statusFilter"]) }}</strong></p>
                            </td>
                            <td width="8%">
                                <img src="{{ public_path() . "/images/dms_small.png" }}"
                                style="background-color: #FFFFFF; height: 30px;margin-left:5px;" />
                            </td>
                        </tr>
                    @else
                        <tr style="">
                            <td style="width:15%;">
                                <img src="{{ public_path() . "/images/dms-log-with-tag.png" }}"
                                style="background-color: #FFFFFF; height: 40px;margin-left:5px;" />
                            </td>
                            @if (in_array("factory",(array_keys($orderData))))
                                <td style="width:10%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Factory') }}</strong>
                                </td>
                                <td>
                                    @if ($orderData["factory"] === "All")
                                    <p style="padding-left: 0px;word-wrap: break-word;margin-left:5px;"><strong>{{ trans('WebSite.All') }}</strong></p>
                                    @else
                                    <p style="padding-left: 0px;word-wrap: break-word;margin-left:5px;"><strong>{{ $orderData["factory"] }}</strong></p>
                                    @endif
                                </td>
                            @endif
                            @if (in_array("pcu",(array_keys($orderData))))
                                <td style="width:10%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.PCU') }}</strong>
                                </td>
                                <td>
                                    @if ($orderData["pcu"] === "All")
                                    <p style="padding-left: 0px;word-wrap: break-word;margin-left:5px;"><strong>{{ trans('WebSite.All') }}</strong></p>
                                    @else
                                    <p style="padding-left: 0px;word-wrap: break-word;margin-left:5px;"><strong>{{ $orderData["pcu"] }}</strong></p>
                                    @endif
                                </td>
                            @endif
                            @if (in_array("buyer",(array_keys($orderData))))
                                <td style="width:10%;background-color: #f0efef;">
                                    <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Buyer') }}</strong>
                                </td>
                                <td>
                                    @if ($orderData["buyer"] === "All")
                                    <p style="padding-left: 0px;word-wrap: break-word;margin-left:5px;"><strong>{{ trans('WebSite.All') }}</strong></p>
                                    @else
                                    <p style="padding-left: 0px;word-wrap: break-word;margin-left:5px;"><strong>{{ $orderData["buyer"] }}</strong></p>
                                    @endif
                                </td>
                            @endif
                            <td style="width:10%;background-color: #f0efef;">
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold,poppins-semibold;">{{ trans('WebSite.orderStatus') }}</strong>
                            </td>
                            <td>
                                <p style="padding-left: 0px;word-wrap: break-word;margin-left:5px;">
                                    <strong>{{ trans('WebSite.'.$orderData["statusFilter"]) }}</strong></p>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        @php
            $buyer = $pcu = $factory=0;
        @endphp
        @if (!empty($orderData["orders"]))
                <div style="clear:both;"></div>
                <table style="border-collapse: collapse;width:100%;margin-top:5px;" cellspacing="1px" class="mainTable">
                    <tr style="background-color: #f0efef;font-weight:600;font-family: poppins,arialuni,notosansjp;font-size:12px;">
                        <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.Order_only') }} / {{ trans('WebSite.Style') }}</strong></td>
                        @if (in_array("factory",(array_keys($orderData))))
                            @php
                                $factory = 1;
                            @endphp
                            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.Factory') }}</strong></td>
                        @endif
                        @if (in_array("buyer",(array_keys($orderData))))
                            @php
                                $buyer = 1;
                            @endphp
                            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.Buyer') }}</strong></td>
                        @endif
                        @if (in_array("pcu",(array_keys($orderData))))
                            @php
                                $pcu = 1;
                            @endphp
                            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.PCU') }}</strong></td>
                        @endif
                        <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.StartDate') }}</strong></td>
                        <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.EndDate') }}</strong></td>
                        <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.created_by') }}</strong></td>
                        @if ($orderData["statusFilter"]==="All" ||$orderData["statusFilter"]==="Deleted" || $orderData["statusFilter"]==="Cancelled")
                            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.actionBy') }}</strong></td>
                            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.actionDate') }}</strong></td>
                        @endif
                        <td style="padding: 0 10px;text-align:center;"><strong>{{ trans('WebSite.Status') }}</strong></td>
                    </tr>
                    @foreach ( $orderData["orders"] as $order)
                        <tr style="font-size:11px;">
                            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp; ">{{ $order['order_no']." / ".$order['style_no'] }}</td>
                            @if ($factory==1)
                                @if ($order['factoryName'])
                                    <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ $order['factoryName'] }}</strong></td>
                                @else
                                    <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>--</strong></td>
                                @endif
                            @endif
                            @if ($buyer==1)
                                @if ($order['buyerName'])
                                    <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ $order['buyerName'] }}</strong></td>
                                @else
                                    <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>--</strong></td>
                                @endif
                            @endif
                            @if ($pcu==1)
                                @if ($order['pcuName'])
                                    <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ $order['pcuName'] }}</strong></td>
                                @else
                                    <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>--</strong></td>
                                @endif
                            @endif
                            {{-- Start Date --}}
                            @if ($order['startDate'] == "")
                                <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">--</td>
                            @else
                                <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ date($orderData['dateFormat'],strtotime($order['startDate'])) }}</td>
                            @endif
                            {{-- End Date --}}
                            @if ($order['endDate'] == "")
                                <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">--</td>
                            @else
                                <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ date($orderData['dateFormat'],strtotime($order['endDate'])) }}</td>
                            @endif
                            <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ $order['createdBy'] }}</strong></td>
                            {{-- Action Data --}}
                            @if ($orderData["statusFilter"]==="All" ||$orderData["statusFilter"]==="Deleted" || $orderData["statusFilter"]==="Cancelled" )
                                @if ($order['actionDoneBy'] == null && $order['actionDate']==null)
                                    <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">---</td>
                                    <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">---</td>
                                @else
                                    <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ $order['actionDoneBy'] }}</td>
                                    <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;">{{ date($orderData['dateFormat'],strtotime($order['actionDate'])) }}</td>
                                @endif
                            @endif
                            {{-- Status --}}
                            @if ($order['status']==="12")
                                <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.Completed') }}</strong></td>
                            @elseif ($order['status']==="1")
                                <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.Active') }}</strong></td>
                            @elseif ($order['status']==="3")
                                <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.Deleted') }}</strong></td>
                            @elseif ($order['status']==="10")
                                <td style="padding : 5px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.Cancelled') }}</strong></td>
                            @endif
                        </tr>
                    @endforeach
                </table>
                <?php $i++ ?>
        @endif
        <footer>
            <script type="text/php">
                if (isset($pdf)) {
                   $font = $fontMetrics->getFont("Poppins", "bold");
                   $pdf->page_text(28,815, "{{ trans('WebSite.orderReport') }}", $font, 9, array(0, 0, 0));
                   $pdf->page_text(490,815, "{{ date($orderData['dateFormat']) }}  {PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
                }
            </script>
        </footer>
    </div>
</body>
</html>
