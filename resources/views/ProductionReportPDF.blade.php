<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Production Report</title>
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

        .tableType td p {
            word-break: break-word !important;
        }

        .tableType {
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
        {{-- <img src="{{ public_path() . '/images/dms-log-with-tag.png' }}"
            style="background-color: #FFFFFF; height: 98px; width:197px" />
        <div style="float:right; font-size:25px; font-weight:600; color: #8C878D;">
            <strong>{{ trans('WebSite.ProductionReport') }}</strong> - <strong>{{  trans('WebSite.'.$productionData['statusFilter']) }}
            <div style="background-color: #D1E7E4; font-size:16px; color: #178677; text-align:center;font-weight: 600;
            padding: 1px 3px 5px;">
                <img src="{{ public_path() . '/images/CalendarIcon.svg' }}" /> {{ date($productionData['dateFormat']) }}
            </div>
        </div> --}}
        <div style="font-size:18px;font-weight: 600;text-align:center; padding: 1px 3px 5px;">
            <span><strong>{{ trans('WebSite.ProductionReport') }}</strong> - <strong>{{  trans('WebSite.'.$productionData['statusFilter']) }}
            </strong></span>
        </div>
        <?php $i = 0; ?>
    @foreach ($productionData['productionData'] as $data)
        {{-- <div style="margin: 25px 0;">
            <div style="width:40%;margin : 5px; float:left;border-radius : 5px; height:auto;">
                <table style="margin: 0;padding: 0; width:100%;" class="tableType">
                    <tr style="background-color: #C4E1DD;">
                        <td style="width: 20%;font-family: poppins,arialuni,notosansjp;"><img
                                src="{{ public_path() . '/images/OrderIconColored.png' }}" style="padding:15px" />
                        </td>
                        <td style="line-height: 1;">
                            <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                <strong>{{ trans('WebSite.Order') }}</strong></p>
                            <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;">
                                <strong>{{ $data['orderNo'] }}</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:4px;"></td>
                    </tr>
                    <tr style="background-color: #C4E1DD;">
                        <td style="width: 20%;font-family: poppins,arialuni,notosansjp;"><img
                                src="{{ public_path() . '/images/StyleIconColored.png' }}" style="padding:15px" />
                        </td>
                        <td style="line-height: 1;">
                            <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                <strong>{{ trans('WebSite.Style') }}</strong></p>
                            <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;">
                                <strong>{{ $data['styleNo'] }}</strong></p>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:40%; float:right;margin : 5px;border-radius : 5px; height:auto;">
                <table style="margin: 0;padding: 0; width:100%;" class="tableType">
                    @if (in_array('factory', array_keys($data)))
                        <tr style="background-color: #C4E1DD;">
                            <td style="width: 20%;font-family: poppins,arialuni,notosansjp;"><img
                                    src="{{ public_path() . '/images/FactoryColored.png' }}" style="padding:15px" />
                            </td>
                            <td style="line-height: 1;">
                                <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.Factory') }}</strong></p>
                                <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;">
                                    <strong>{{ $data['factory'] }}</strong></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding:4px;"></td>
                        </tr>
                    @endif
                    @if (in_array('pcu', array_keys($data)))
                        <tr style="background-color: #C4E1DD;">
                            <td style="width: 20%;font-family: poppins,arialuni,notosansjp;"><img
                                    src="{{ public_path() . '/images/PCUColored.png' }}" style="padding:15px" /></td>
                            <td style="line-height: 1;">
                                <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.PCU') }}</strong></p>
                                <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;">
                                    <strong>{{ $data['pcu'] }}</strong></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding:4px;"></td>
                        </tr>
                    @endif
                    @if (in_array('buyer', array_keys($data)))
                        <tr style="background-color: #C4E1DD;">
                            <td style="width: 20%;font-family: poppins,arialuni,notosansjp;"><img
                                    src="{{ public_path() . '/images/BuyerColored.png' }}" style="padding:15px" />
                            </td>
                            <td style="line-height: 1;">
                                <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.Buyer') }}</strong></p>
                                <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;">
                                    <strong>{{ $data['buyer'] }}</strong></p>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
        </div> --}}
        <div>
            <table style="width: 100%; border-collapse: collapse;font-size:10px;" cellspacing="1px" class="headTable">
                @if($productionData['useLogo']==1 && $productionData['userLogo']!='')
                    <tr style="">
                        <td rowspan="2" width="15%">
                            <img src="{{ $productionData['userLogo'] }}"
                            style="background-color: #FFFFFF; height: 40px;margin-left:5px;" />
                        </td>
                        <td style="width:20%;background-color: #f0efef;">
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Order_only').' / '.trans('WebSite.Style') }}</strong>
                        </td>
                        <td>
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $data['orderNo'] }} / {{ $data['styleNo'] }}</strong>
                        </td>
                        <td style="width:12%;background-color: #f0efef;">
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.delivery_date') }}</strong>
                        </td>
                        <td>
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">
                                {{ ($data['delivery_date']!=null && $data['delivery_date']!='')?date($productionData['dateFormat'],strtotime($data['delivery_date'])):trans('WebSite.delivered') }}
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
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $data['orderNo'] }} / {{ $data['styleNo'] }}</strong>
                        </td>
                        <td style="width:12%;background-color: #f0efef;">
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.delivery_date') }}</strong>
                        </td>
                        <td>
                            <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">
                                {{ ($data['delivery_date']!=null && $data['delivery_date']!='')?date($productionData['dateFormat'],strtotime($data['delivery_date'])):trans('WebSite.delivered') }}
                            </strong>
                        </td>
                    </tr>
                @endif
                <tr>
                    <?php $td_i=0;?>
                    @if (in_array("factory",(array_keys($data))))
                    <?php $td_i++;?>
                            <td style="width:10%;background-color: #f0efef;">
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Factory') }}</strong>
                            </td>
                            <td>
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $data["factory"] }}</strong>
                            </td>
                    @endif
                    @if (in_array("pcu",(array_keys($data))))
                    <?php $td_i++;?>
                            <td style="width:10%;background-color: #f0efef;">
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.PCU') }}</strong>
                            </td>
                            <td>
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $data["pcu"] }}</strong>
                            </td>
                    @endif
                    @if (in_array("buyer",(array_keys($data))))
                    <?php $td_i++;?>
                            <td style="width:10%;background-color: #f0efef;">
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ trans('WebSite.Buyer') }}</strong>
                            </td>
                            <td>
                                <strong style="font-family: poppins,arialuni,notosansjp,poppins-semibold;margin-left:5px;">{{ $data["buyer"] }}</strong>
                            </td>
                    @endif
                    @if($td_i<2)
                            <td colspan="2"></td>
                    @endif
                </tr>
            </table>
        </div>
        <div style="margin: 2px 0;font-size:12px;">
            @if (count($productionData['advFilter'])>0)
                <strong>{{ trans('WebSite.filter') }} : </strong>
                @if (array_key_exists("startDate",$productionData['advFilter']) && array_key_exists("endDate",$productionData['advFilter']))
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.StartDate') .": ".$productionData['advFilter']['startDate'] }}</strong>
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.EndDate') .": ".$productionData['advFilter']['endDate'] }}</strong>
                @endif
                @if (array_key_exists("type",$productionData['advFilter']))
                    @foreach ($productionData['advFilter']['type'] as $type)
                        @if ($type === "Cut")
                        <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.'.$type.'ting') }}</strong>
                        @else
                        <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.'.$type.'ing') }}</strong>
                        @endif
                    @endforeach
                @endif
                @if (array_key_exists("delay",$productionData['advFilter']) && array_key_exists("operator",$productionData['advFilter']))
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">
                        {{ trans('WebSite.noOfDaysDelay',['delay'=>$productionData['advFilter']['delay'],
                        'operator'=>$productionData['advFilter']['operator']]) }}</strong>
                @endif
            @endif
        </div>
    </div>
    <div style="clear : both;"></div>
    <table style="margin-top : 5px;width: 100%;border-collapse: collapse;font-family: poppins,arialuni,notosansjp;"
        cellspacing="1px" class="mainTable">
        <tr style="background-color: #f0efef;font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.productionTerm') }}</strong></td>
            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.StartDate') }}</strong></td>
            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.EndDate') }}</strong></td>
            <td style="text-align:center;padding:2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.Total') }}</strong></td>
            <td style="text-align:center;padding:2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.Completed') }}</strong></td>
            <td style="text-align:center;padding:2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;"><strong>{{ trans('WebSite.Pending') }}</strong></td>
            <td style="padding: 0 10px;text-align:center; font-family: poppins,arialuni,notosansjp,poppins-semibold;" ><strong>{{ trans('WebSite.Status') }}</strong></td>
            <td style="padding : 5px 5px 5px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" ><strong>{{ trans('WebSite.Result') }}</strong></td>
        </tr>
        @foreach ($data['prodData'] as $prodData)
            <tr>
                @if ($prodData['title'] === 'Cutting')
                    <td style="padding : 5px 5px 10px 20px;">
                        {{-- {{ $prodData['title'] }} --}}
                        <img src="{{ public_path() . '/images/BlackCutting.png' }}"
                            style="margin-left: 15px;margin-top:5px" />
                        {{-- <img src="{{ asset('images/BlackCutting.png') }}" style="margin-left: 30px;padding-bottom : 1px;"/> --}}
                        <p
                            style="margin-top:1px;font-weight:400; font-family: poppins,arialuni,notosansjp;">
                            <strong>{{ trans('WebSite.Cutting') }}</strong></p>
                    </td>
                @endif
                @if ($prodData['title'] === 'Sewing')
                    <td
                        style="padding : 5px 5px 10px 20px;">
                        {{-- {{ $prodData['title'] }} --}}
                        <img src="{{ public_path() . '/images/BlackSewing.png' }}"
                            style="margin-left: 15px;margin-top:5px" />
                        {{-- <img src="{{ asset('images/BlackSewing.png') }}" style="margin-left: 30px;padding-bottom : 1px;"/> --}}
                        <p
                            style="margin-top:1px;font-weight:400; font-family: poppins,arialuni,notosansjp;">
                            <strong>{{ trans('WebSite.Sewing') }}</strong></p>
                    </td>
                @endif
                @if ($prodData['title'] === 'Packing')
                    <td style="padding : 5px 5px 10px 20px;">
                        {{-- {{ $prodData['title'] }} --}}
                        <img src="{{ public_path() . '/images/BlackPacking.png' }}"
                            style="margin-left: 15px;margin-top:5px" />
                        {{-- <img src="{{ asset('images/BlackPacking.png') }}" style="margin-left: 30px;padding-bottom : 1px;"/> --}}
                        <p
                            style="margin-top:1px;font-weight:400; font-family: poppins,arialuni,notosansjp;">
                            <strong>{{ trans('WebSite.Packing') }}</strong></p>
                    </td>
                @endif
                @if ($prodData['startDate'] == '')
                    <td style="padding : 10px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ trans('WebSite.NotAssigned') }}</td>
                @else
                    <td style="padding : 10px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">
                        {{ $prodData['startDate'] }}</td>
                @endif
                @if ($prodData['endDate'] == '')
                    <td style="padding : 10px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ trans('WebSite.NotAssigned') }}</td>
                @else
                    <td style="padding : 10px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">
                        {{ $prodData['endDate'] }}</td>
                @endif
                @if ($prodData['totalQuantity'] === '')
                    <td style="text-align:center;padding:2px; font-family: poppins,arialuni,notosansjp;">{{ trans('WebSite.NoTotalQuantity') }}</td>
                @else
                    <td style="text-align:center;padding:2px; font-family: poppins,arialuni,notosansjp;">
                        {{ $prodData['totalQuantity'] }}</td>
                @endif
                @if ($prodData['updatedQuantity'] === '')
                    <td style="text-align:center;padding:2px; font-family: poppins,arialuni,notosansjp;">{{ trans('WebSite.NoUpdatedQuantity') }}
                    </td>
                @else
                    <td style="text-align:center;padding:2px; font-family: poppins,arialuni,notosansjp;">
                        {{ $prodData['updatedQuantity'] }}</td>
                @endif
                @if ($prodData['pendingQuantity'] === '')
                    <td style="text-align:center;padding:2px; font-family: poppins,arialuni,notosansjp;">{{ trans('WebSite.NoPendingQuantity') }}
                    </td>
                @else
                    <td style="text-align:center;padding:2px; font-family: poppins,arialuni,notosansjp;">
                        {{ $prodData['pendingQuantity'] }}</td>
                @endif
                @if (($prodData['pendingQuantity'] === 0 || ($prodData['updatedQuantity'] === $prodData['totalQuantity'])))
                    @if ($prodData['actualEndDate'] >= $prodData['accomplishedDate'])
                        <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                            <img src="{{ public_path() . '/images/SmileyGreen.svg' }}" style="padding : 5px 10px" width="{{config('constant.pdf_icon_width')}}px"/>
                            {{-- <img src="{{ asset('images/SmileyGreen.png') }}" style="padding : 10px 0px" /> --}}
                        </td>
                        <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">
                        <strong>{{ trans('WebSite.Completed') }}</strong></td>
                    @elseif ($prodData['actualEndDate'] < $prodData['accomplishedDate'])
                        <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                            <img src="{{ public_path() . '/images/SmileyGreen.svg' }}" style="padding : 5px 10px" width="{{config('constant.pdf_icon_width')}}px"/>
                            {{-- <img src="{{ asset('images/SmileyGreen.png') }}" style="padding : 10px 0px" /> --}}
                        </td>
                        <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">
                        <strong>{{ trans('WebSite.DelayedComplete') }}</strong></td>
                    @endif
                @elseif ($prodData['type'] ==="YetToBeStarted" && $prodData['delay'] > 1)
                    <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                        <img src="{{ public_path() . '/images/YettoStart.svg' }}" style="padding : 5px 10px" width="{{config('constant.pdf_icon_width')}}px"/>
                        {{-- <img src="{{ asset('images/SmileyGreen.png') }}" style="padding : 10px 0px" /> --}}
                    </td>
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">
                    <strong>{!! trans('WebSite.DaysToStart',['day'=>$prodData['delay']]) !!}</strong></td>
                @elseif ($prodData['type'] ==="YetToBeStarted" && $prodData['delay'] === 1)
                    <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                        <img src="{{ public_path() . '/images/YettoStart.svg' }}" style="padding : 5px 10px" width="{{config('constant.pdf_icon_width')}}px"/>
                        {{-- <img src="{{ asset('images/SmileyGreen.png') }}" style="padding : 10px 0px" /> --}}
                    </td>
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">
                    <strong>{{ trans('WebSite.startsTomorrow') }}</strong></td>
                @elseif ($prodData['type'] ==="StartsToday")
                    <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                        <img src="{{ public_path() . '/images/SmileyYellow.svg' }}" style="padding : 5px 10px" width="{{config('constant.pdf_icon_width')}}px"/>
                        {{-- <img src="{{ asset('images/SmileyGreen.png') }}" style="padding : 10px 0px" /> --}}
                    </td>
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">
                    <strong>{{ trans('WebSite.StartsToday') }}</strong></td>
                @elseif ($prodData['delay'] > 0 && $prodData['type'] ==="Progress")
                    @if ($prodData['delay'] == 0)
                        <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                            <img src="{{ public_path() . '/images/SmileyYellow.svg' }}" style="padding : 5px 10px" width="{{config('constant.pdf_icon_width')}}px"/>
                        </td>
                        <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">
                        <strong>{{ trans('WebSite.DayRemaining') }}</strong></td>
                    @else
                        <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                            <img src="{{ public_path() . '/images/SmileyYellow.svg' }}" style="padding : 5px 10px" width="{{config('constant.pdf_icon_width')}}px"/>
                        </td>
                        <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">
                        <strong>{{ trans('WebSite.DaysRemaining',['days'=>$prodData['delay']]) }}</strong></td>
                    @endif
                @elseif ($prodData['delay'] === 0 && $prodData['type'] ==="Progress")
                    <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                        <img src="{{ public_path() . '/images/SmileyYellow.svg' }}" style="padding : 5px 10px" width="{{config('constant.pdf_icon_width')}}px"/>
                    </td>
                    <td style="padding : 5px 5px 10px 20px;font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.LastDay') }}</strong></td>
                @elseif ($prodData['delay'] < 0 && $prodData['type'] ==="Progress")
                    @if ($prodData['delay'] == -1)
                        <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                            <img src="{{ public_path() . '/images/2_Days_Delay.svg' }}" style="padding : 5px 10px" width="{{config('constant.pdf_icon_width')}}px"/>
                        </td>
                        <td style="padding : 5px 5px 10px 20px;font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($prodData['delay'])." ".trans('WebSite.dayDelay') }}</strong></td>
                    @elseif (abs($prodData['delay']) <= 3)
                        <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                            <img src="{{ public_path() . '/images/2_Days_Delay.svg' }}" style="padding : 5px 10px" width="{{config('constant.pdf_icon_width')}}px"/>
                        </td>
                        <td style="padding : 5px 5px 10px 20px;font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($prodData['delay'])." ".trans('WebSite.daysDelay') }}</strong></td>
                    @else
                        <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                            <img src="{{ public_path() . '/images/delay_bomb.svg' }}" style="padding : 5px 10px" width="{{config('constant.pdf_icon_width')}}px"/>
                        </td>
                        <td style="padding : 5px 5px 10px 20px;font-family: poppins,arialuni,notosansjp;"><strong>{{ abs($prodData['delay'])." ".trans('WebSite.daysDelay') }}</strong></td>
                    @endif
                @elseif ($prodData['delay'] === null)
                    <td style="text-align:center; background-color: #FFFFFF; font-family: poppins,arialuni,notosansjp;">
                        <img src="{{ public_path() . '/images/SmileySad.svg' }}" style="padding : 5px 10px" width="{{config('constant.pdf_icon_width')}}px"/>
                    </td>
                    <td style="padding : 5px 5px 10px 20px;font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.NotAssigned') }}</strong></td>
                @endif
            </tr>
        @endforeach
    </table>
    <footer style="margin-top: 15px;width: 100%;background-color: #f0efef; font-size:12px;text-align:center;
            padding: 1px 3px 5px;">
            <strong>
                {!! trans('WebSite.PDFFooter',['date'=>$data['lastDate']]) !!}
            </strong>
            </footer>
    @if ($i < count($productionData['productionData']) - 1)
        <div class="page-break"></div>
    @endif
    <?php $i++; ?>
    @endforeach
    <footer>
        <script type="text/php">
            if (isset($pdf)) {
                $font = $fontMetrics->getFont("Poppins", "bold");
                $pdf->page_text(28,815, "{{ trans('WebSite.ProductionReport') }} - {{  trans('WebSite.'.$productionData['statusFilter']) }}", $font, 9, array(0, 0, 0));
                $pdf->page_text(490,815, "{{ date($productionData['dateFormat']) }}  {PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
            }
        </script>
    </footer>
</body>

</html>
