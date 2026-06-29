
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Inquiry</title>
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

        .tableType td p, table td p, table td li,table td a {
            word-break: break-word !important;
            padding-right: 15px;
        }

        .tableType {
            border-collapse: collapse;
        }
        table td {word-wrap:break-word !important; }
        /* table tr:nth-child(even) {background: #f6f6f6}
        table tr:nth-child(odd) {background: #ffffff} */
    </style>

</head>

<body style="font-family: poppins,arialuni; font-size: 14px;">

    <div >
        <table width="100%"  cellpadding="2" cellspacing="0" class="mainTable" >
            <tr>
                <td width="15%">
                        <img src="{{ public_path() . '/images/dms-log-with-tag.png' }}"
                            style="background-color: #FFFFFF; height: 40px;"  />
                    {{-- @if ($datas['logo'] != "" || $datas['logo'] != null )
                        <div style="float:left;">
                            <img src={{ config('filesystems.disks.s3.url').$datas['logo'] }}
                                style="background-color: #FFFFFF; height: 65px;width:140px;margin-left:5px;" />

                        </div>
                    @endif --}}
                </td>
                <td style="vertical-align:middle;text-align:center;font-size:12px; font-weight:600; color: #8C878D;"  >
                        <strong>{{ trans('WebSite.Inquiry') }} from - {{ $datas['user']['name'] }} ({{ $datas['user']['user_type'] }})</strong>
                </td>
                <td width="15%">
                    <div style="vertical-align:middle;text-align:right; font-size:12px; font-weight:600; color: #8C878D; ">
                        <strong>{{ trans('WebSite.Inquiry') }} - {{ $datas['inquiryID'] }}</strong>
                        {{-- <div
                            style=" background-color: #D1E7E4; font-size:16px; color: #178677; text-align:center;font-weight: 600;
                        padding: 1px 3px 5px;">
                            <img src="{{ public_path() . '/images/CalendarIcon.svg' }}" /> {{ date('d M Y',strtotime($datas['inqdet'][0]['created_date'])) }}
                        </div> --}}
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <div style="clear : both;"></div>

    <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;"
    cellspacing="1px" class="mainTable">
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.article_name') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['inqdet'][0]['article_name'] }}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.style_number') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data']['style_no'] }}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.category') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['inqdet'][0]['category'] }}</td>
        </tr>
        {{-- <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.fabric_composition') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['inqdet'][0]['fabric_type'] }}</td>
        </tr>
        @if($datas['data']['fabric_type']!='' && $datas['data']['fabric_type']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.fabric_type') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data']['fabric_type'] }}</td>
        </tr>
        @endif
        @if($datas['data']['fabric_GSM']!='' && $datas['data']['fabric_GSM']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.fabric_gsm') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data']['fabric_GSM'] }}</td>
        </tr>
        @endif
        @if($datas['data']['yarn_count']!='' && $datas['data']['yarn_count']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.yarn_count') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data']['yarn_count'] }}</td>
        </tr>
        @endif --}}
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.fabric') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">
                @php
                    $txt = $datas['inqdet'][0]['fabric_type'];
                    if($datas['data']['fabric_type']!="")
                        $txt.= ", ".$datas['data']['fabric_type'];
                    if($datas['data']['fabric_GSM']!="")
                        $txt.= ", ".$datas['data']['fabric_GSM']." GSM";
                    if($datas['data']['yarn_count']!="")
                        $txt.= ", Ne ".$datas['data']['yarn_count']." count";
                @endphp
                {{ $txt }}
            </td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.sample_images') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni; height='110px'; vertical-align:middle;">
                <?php
                    if(isset($datas['media']['files'])){
                        foreach($datas['media']['files'] as $file){
                            if($file['media_type']=='SampleFormat'){
                                if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                                ?>
                                    <img src="{{ $file['filepath'] }}" alt='Images' title="SampleFormat" height="100px" style="padding:5px; margin-top:20px;">
                                <?php
                                }else{
                                    ?>
                                    {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>
                                    <?php
                                }
                                //echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' height='100px' style='padding:5px; margin-top:20px' > ";
                            }

                        }
                    }
                ?>
            </td>
        </tr>

        @if($datas['data']['target_price']!='' && $datas['data']['target_price']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.target_price') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data']['currency'] }} {{ $datas['data']['target_price'] }}</td>
        </tr>
        @endif
        @if($datas['inqdet'][0]['income_terms']!='' && $datas['inqdet'][0]['income_terms']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.Incoterms') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['inqdet'][0]['income_terms'] }}</td>
        </tr>
        @endif
        @if($datas['data']['payment_terms']!='' && $datas['data']['payment_terms']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.payment_terms') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['payment_terms'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['payment_instructions']!='' && $datas['data']['payment_instructions']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.payment_instructions') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['payment_instructions'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['due_date']!='' && $datas['data']['due_date']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.due_date') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data']['due_date'] }}</td>
        </tr>
        @endif
        @if($datas['data']['style_article_description']!='' && $datas['data']['style_article_description']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.sty_art_desc') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['style_article_description'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['special_finish']!='' && $datas['data']['special_finish']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.spl_finish') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['special_finish'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['total_qty']!='' && $datas['data']['total_qty']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.total_qty') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['total_qty'] !!}</td>
        </tr>
        @endif
        @if(!empty($datas['sku']))
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;" colspan="2">
                <strong>{{ trans('WebSite.sku_details') }}</strong>
                <table style="width: 100%;">
                    <?php
                    if(!empty($datas['sku'])){
                        ?>
                        <tr>
                            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.colors') }}</strong></td>
                            <?php
                            foreach($datas['sizes'] as $size){
                                echo '<td><strong>'.$size['name'].'</strong></td>';
                            }
                        echo '</tr>';
                            foreach($datas['colors'] as $cols){
                                echo '<tr>';
                                echo '<td><strong>'.$cols['name'].'</strong></td>';
                                foreach($datas['sizes'] as $size){
                                    $vv = getIndex($size['id'], $cols['id'], (array)$datas['sku']);
                                    echo '<td>'.$datas['sku'][$vv]['quantity'].'</td>';
                                }

                                echo '</tr>';
                            }
                    }
                    ?>
                </table>
            </td>
        </tr>
        @endif
        @if($datas['data']['patterns']!='' && $datas['data']['patterns']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.patterns') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['patterns'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['jurisdiction']!='' && $datas['data']['jurisdiction']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.jurisdiction') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['jurisdiction'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['customs_declaraion_document']!='' && $datas['data']['customs_declaraion_document']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.cus_dec_doc') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['customs_declaraion_document'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['penality']!='' && $datas['data']['penality']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.penalty') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['penality'] !!}</td>
        </tr>
        @endif
        <tr style=" color:#009688; font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;" colspan="2"><strong>{{ trans('WebSite.prinrt_info') }}</strong></td>
        </tr>
        @if($datas['data']['print_type']!='' && $datas['data']['print_type']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.print_type') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['print_type'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['print_size']!='' && $datas['data']['print_size']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.print_size') }} </strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['print_size'] !!} cm</td>
        </tr>
        @endif
        @if($datas['data']['print_no_of_colors']!='' && $datas['data']['print_no_of_colors']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.no_of_colors') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['print_no_of_colors'] !!}</td>
        </tr>
        @endif

        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.print_image') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">
                <?php
                    if(isset($datas['media']['files'])){
                        foreach($datas['media']['files'] as $file){
                            if($file['media_type']=='PrintImage'){
                                if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                                ?>
                                    <img src="{{ $file['filepath'] }}" alt='Images' title="PrintImage" height="100px" style="padding:5px; margin-top:20px;">
                                <?php
                                }else{
                                    ?>
                                    {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>                                    <?php
                                }
                                //echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' height='100px' style='padding:5px; margin-top:20px' >";
                            }
                        }
                    }
                ?>
            </td>
        </tr>

        <tr style=" color:#009688; font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;" colspan="2"><strong>{{ trans('WebSite.trims_info') }}</strong></td>
        </tr>
        @if($datas['data']['main_lable']!='' && $datas['data']['main_lable']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.main_lable_info') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['main_lable'] !!}</td>
        </tr>
        @endif
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.main_label_sample') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">
                <?php
                if(isset($datas['media']['files'])){
                    foreach($datas['media']['files'] as $file){
                        if($file['media_type']=='MainLabel'){
                            if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                            ?>
                                <img src="{{ $file['filepath'] }}" alt='Images' title="Mainlabel" height="100px" style="padding:5px; margin-top:20px;">
                            <?php
                            }else{
                                ?>
                                {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>
                                <?php
                            }
                            //echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' height='100px' style='padding:5px; margin-top:20px' >";
                        }
                    }
                }
            ?>
            </td>
        </tr>
        @if($datas['data']['washcare_lable']!='' && $datas['data']['washcare_lable']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.washcare_lable_info') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['washcare_lable'] !!}</td>
        </tr>
        @endif
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.washcare_label_sample') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">
                <?php
                if(isset($datas['media']['files'])){
                    foreach($datas['media']['files'] as $file){
                        if($file['media_type']=='WashCareLabel'){
                            if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                            ?>
                                <img src="{{ $file['filepath'] }}" alt='Images' title="Washcare" height="100px" style="padding:5px; margin-top:20px;">
                            <?php
                            }else{
                                ?>
                                {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>
                                <?php
                            }
                            //echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' height='100px' style='padding:5px; margin-top:20px' >";
                        }
                    }
                }
            ?>
            </td>
        </tr>
        @if($datas['data']['hangtag_lable']!='' && $datas['data']['hangtag_lable']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.hangtag_info') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['hangtag_lable'] !!}</td>
        </tr>
        @endif

        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.hangtag_sample') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">
                <?php
                if(isset($datas['media']['files'])){
                    foreach($datas['media']['files'] as $file){
                        if($file['media_type']=='Hangtag'){
                            if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                            ?>
                                <img src="{{ $file['filepath'] }}" alt='Images' title="Hangtag" height="100px" style="padding:5px; margin-top:20px;">
                            <?php
                            }else{
                                ?>
                                {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>
                                <?php
                            }
                            //echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' height='100px' style='padding:5px; margin-top:20px' >";
                        }
                    }
                }
            ?>
            </td>
        </tr>
        @if($datas['data']['barcode_lable']!='' && $datas['data']['barcode_lable']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.barcode_stickers_info') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['barcode_lable'] !!}</td>
        </tr>
        @endif
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.barcode_stickers_sample') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">
                <?php
                if(isset($datas['media']['files'])){
                    foreach($datas['media']['files'] as $file){
                        if($file['media_type']=='BarcodeStickers'){
                            if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                            ?>
                                <img src="{{ $file['filepath'] }}" alt='Images' title="BarCode" height="100px" style="padding:5px; margin-top:20px;">
                            <?php
                            }else{
                                ?>
                                {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>
                                <?php
                            }
                            //echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' height='100px' style='padding:5px; margin-top:20px' >";
                        }
                    }
                }
            ?>
            </td>
        </tr>
        @if($datas['data']['trims_nominations']!='' && $datas['data']['trims_nominations']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.trims_notification') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['trims_nominations'] !!}</td>
        </tr>
        @endif
        <tr style="color:#009688; font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;" colspan="2"><strong>{{ trans('WebSite.packing_info') }}</strong></td>
        </tr>
        @if($datas['data']['poly_bag_size']!='' && $datas['data']['poly_bag_size']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.polybag_size') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['poly_bag_size'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['poly_bag_material']!='' && $datas['data']['poly_bag_material']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.polybag_meterial') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['poly_bag_material'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['poly_bag_print']!='' && $datas['data']['poly_bag_print']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.polybag_print') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['poly_bag_print'] !!}</td>
        </tr>
        @endif
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.polybag_sample') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">
                <?php
                if(isset($datas['media']['files'])){
                    foreach($datas['media']['files'] as $file){
                        if($file['media_type']=='Polybag'){
                            if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                            ?>
                                <img src="{{ $file['filepath'] }}" alt='Images' title="Polybag" height="100px" style="padding:5px; margin-top:20px;">
                            <?php
                            }else{
                                ?>
                                {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>
                                <?php
                            }
                            //echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' height='100px' style='padding:5px; margin-top:20px' >";
                        }
                    }
                }
            ?>
            </td>
        </tr>
        @if($datas['data']['carton_bag_dimensions']!='' && $datas['data']['carton_bag_dimensions']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.cartonbox_diamenition') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['carton_bag_dimensions'] !!} cm</td>
        </tr>
        @endif
        @if($datas['data']['carton_color']!='' && $datas['data']['carton_color']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.carton_color') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['carton_color'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['carton_material']!='' && $datas['data']['carton_material']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.carton_material') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['carton_material'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['carton_edge_finish']!='' && $datas['data']['carton_edge_finish']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.catton_edge') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['carton_edge_finish'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['carton_mark']!='' && $datas['data']['carton_mark']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.carton_details') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['carton_mark'] !!}</td>
        </tr>
        @endif
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.carton_sample') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">
                <?php
                if(isset($datas['media']['files'])){
                    foreach($datas['media']['files'] as $file){
                        if($file['media_type']=='Carton'){
                            if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                            ?>
                                <img src="{{ $file['filepath'] }}" alt='Images' title="CartonBox" height="100px" style="padding:5px; margin-top:20px;">
                            <?php
                            }else{
                                ?>
                                {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>
                                <?php
                            }
                            //echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' height='100px' style='padding:5px; margin-top:20px' >";
                        }
                    }
                }
            ?>
            </td>
        </tr>
        @if($datas['data']['make_up']!='' && $datas['data']['make_up']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.makeup') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['make_up'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['films_cd']!='' && $datas['data']['films_cd']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.flimscd') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['films_cd'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['picture_card']!='' && $datas['data']['picture_card']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.picture_card') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['picture_card'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['inner_cardboard']!='' && $datas['data']['inner_cardboard']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.inner_cardboard') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['inner_cardboard'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['estimate_delivery_date']!='' && $datas['data']['estimate_delivery_date']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.est_delivery_date') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['estimate_delivery_date'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['shipping_size']!='' && $datas['data']['shipping_size']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.shipping_size') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['shipping_size'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['air_frieght']!='' && $datas['data']['air_frieght']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.air_freight') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['air_frieght'] !!}</td>
        </tr>
        @endif
        <tr style="color:#009688; font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;" colspan="2"><strong>{{ trans('WebSite.others') }}</strong></td>
        </tr>
        @if($datas['data']['forbidden_substance_info']!='' && $datas['data']['forbidden_substance_info']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.forbidden_subs_info') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['forbidden_substance_info'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['testing_requirements']!='' && $datas['data']['testing_requirements']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.test_requiremennt') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['testing_requirements'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['sample_requirements']!='' && $datas['data']['sample_requirements']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.sample_requirements') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['sample_requirements'] !!}</td>
        </tr>
        @endif
        @if($datas['data']['special_requests']!='' && $datas['data']['special_requests']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.special_requests') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data']['special_requests'] !!}</td>
        </tr>
        @endif

        <?php
        $ms_sheet_avil=0;
        if(isset($datas['media']['files'])){
            foreach($datas['media']['files'] as $file){
                if($file['media_type']=='MeasurementSheet'){

                    if((stristr($file['orginalfilename'],'.pdf')) && $ms_sheet_avil==0)
                        $ms_sheet_avil =1
                    ?>
                    <tr style="  font-weight:500; font-family: poppins,arialuni;">
                        <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.measurment') }}</strong></td>
                        <td style="padding : 5px ; font-family: poppins,arialuni;">
                            {{ url('') }}/downloadfile?path={{ $file['org_file_path'] }}&filename={{ $file['orginalfilename'] }}
                            <?php
                            // echo '<a href='.$file['org_file_path'].'>'.$file['orginalfilename'].'</a>';
                            // $datasc = json_decode($file['datasource']);
                            // if($datasc!=null && !empty($datasc[0])){
                            //     echo '<br>';
                            //     echo '<table width="100%">';
                            //         $i=0;
                            //     foreach ($datasc[0] as $dt) {
                            //         echo '<tr>';
                            //         foreach ($dt as $d) {
                            //             if($d!="" && $d!=null) {
                            //                 if($i==0)
                            //                 {
                            //                     echo '<td><strong>'.$d.'</strong></td>';
                            //                 }else{
                            //                     echo '<td>'.$d.'</td>';
                            //                 }
                            //                 $i++;
                            //             }
                            //         }
                            //         echo '</tr>';
                            //     }
                            //     echo '</table>';
                            // }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            }
        }
        ?>


        @if($datas['data']['measurement_sheet']!='' && $datas['data']['measurement_sheet']!=NULL && $datas['data']['measurement_sheet']!='[]')
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;" colspan="2">

                {{-- {{ json_decode($datas['data']['measurement_sheet']) }} --}}

                <?php
                    // $msarr = json_decode($datas['data']['measurement_sheet']);
                    // echo '<table width="100%">';
                    // foreach ($msarr as $key => $value) {
                    //     if($key ==0){
                    //         echo '<tr>';
                    //                 foreach($value as $k => $v){
                    //                     echo '<td><strong>'.ucfirst($k).'</strong></td>';
                    //                 }
                    //         echo '</tr>';
                    //     }
                    //     echo '<tr>';
                    //     foreach($value as $k => $v){
                    //         echo '<td>'.$v.'</td>';
                    //         }
                    //     echo '<tr> ';
                    // }
                    // echo '</table>';
                ?>

            </td>
        </tr>
        @endif

    </table>
    @if($ms_sheet_avil==0)
        <footer>
            <script type="text/php">
                if (isset($pdf)) {
                $font = $fontMetrics->getFont("Arial", "bold");
                $pdf->page_text(35, 805, "IN-{{ $datas['inquiryID'] }}    {{ date('d M Y',strtotime($datas['inqdet'][0]['created_date'])) }}", $font, 10, array(0, 0, 0));
                $pdf->page_text(525, 805, "Page {PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
                }
            </script>
        </footer>
    @endif
</body>
</html>
<?php
function getIndex($needle, $haystack, $array){
    foreach($array as $key => $value){
        if(is_array($value) && $value['size_id'] == $needle && $value['color_id'] == $haystack)
              return $key;
    }
    return 0;
}
//exit;
?>





