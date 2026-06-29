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
            /* width:700px;
            margin: 0 auto; */
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

        .tableType td p, table td p, table td li {
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

<?php
$trans_arr=[];
$trans_arr['lang'] = $datas['lang'];
$trans_arr['translate'] = $datas['translate'] ?? 0;
?>
<?php
if($datas['lang']=='en'){
 function dataGetVal($needle, $haystack, $array){
        foreach($array as $key => $value){
            if(is_array($value) && $value['size_id'] == $needle && $value['color_id'] == $haystack)
                return $key;
        }
        return 0;
    }
}else{
    function dataGetValJp($needle, $haystack, $array){
        foreach($array as $key => $value){
            if(is_array($value) && $value['size_id'] == $needle && $value['color_id'] == $haystack)
                return $key;
        }
        return 0;
    }
}
?>

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
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data'][0]['style_no'] }}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.category') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['inqdet'][0]['category'] }}</td>
        </tr>
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.fabric') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">
                @php
                    $txt = $datas['inqdet'][0]['fabric_composition'];
                    if($datas['data'][0]['fabric_type']!="")
                        $txt.= ", ".$datas['data'][0]['fabric_type'];
                    if($datas['data'][0]['fabric_GSM']!="")
                        $txt.= ", ".$datas['data'][0]['fabric_GSM']." GSM";
                    if($datas['data'][0]['yarn_count']!="")
                        $txt.= ", Ne ".$datas['data'][0]['yarn_count']." count";
                @endphp
                {{ $txt }}
            </td>
        </tr>

        <?php
            $i=0;
            if(isset($datas['media']['files'])){
                foreach($datas['media']['files'] as $file){
                    if($file['media_type']=='SampleFormat'){
                        if($i==0){
                            ?>
                            <tr style="font-weight:500; font-family: poppins,arialuni;">
                                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.sample_images') }}</strong></td>
                                <td style="padding : 5px ; font-family: poppins,arialuni;">
                            <?php
                        }
                        if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                        ?>
                            <img src="{{ $file['filepath'] }}" alt='Images' title="SampleFormat" height="100px" style="padding:5px; margin-top:20px;">
                        <?php
                        }else{
                            ?>
                            {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>
                            <?php
                        }
                        $i++;
                    }
                }
                if($i>0){
                    ?>
                        </td>
                    </tr>
                    <?php
                }
            }
        ?>

        @if($datas['data'][0]['target_price']!='' && $datas['data'][0]['target_price']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.target_price') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data'][0]['currency'] }} {{ $datas['data'][0]['target_price'] }}</td>
        </tr>
        @endif
        @if($datas['inqdet'][0]['income_terms']!='' && $datas['inqdet'][0]['income_terms']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.Incoterms') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['inqdet'][0]['income_terms'] }}</td>
        </tr>
        @endif
        @if($datas['data'][0]['payment_terms']!='' && $datas['data'][0]['payment_terms']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.payment_terms') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['payment_terms'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['payment_instructions']!='' && $datas['data'][0]['payment_instructions']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.payment_instructions') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['payment_instructions'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['due_date']!='' && $datas['data'][0]['due_date']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.due_date') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{{ $datas['data'][0]['due_date'] }}</td>
        </tr>
        @endif
        @if($datas['data'][0]['style_article_description']!='' && $datas['data'][0]['style_article_description']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.sty_art_desc') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['style_article_description'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['special_finish']!='' && $datas['data'][0]['special_finish']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.spl_finish') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['special_finish'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['total_qty']!='' && $datas['data'][0]['total_qty']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.total_qty') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['total_qty'] !!}</td>
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
                                if($datas['lang']=='en'){
                                $vv = dataGetVal($size['id'], $cols['id'], (array)$datas['sku']);
                                echo '<td>'.$datas['sku'][$vv]['quantity'].'</td>';
                                }else{
                                    $vv = dataGetValJp($size['id'], $cols['id'], (array)$datas['sku']);
                                echo '<td>'.$datas['sku'][$vv]['quantity'].'</td>';
                                }
                            }
                            echo '</tr>';
                        }
                    }
                    ?>
                </table>
            </td>
        </tr>
        @endif
        @if($datas['data'][0]['patterns']!='' && $datas['data'][0]['patterns']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.patterns') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['patterns'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['jurisdiction']!='' && $datas['data'][0]['jurisdiction']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.jurisdiction') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['jurisdiction'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['customs_declaraion_document']!='' && $datas['data'][0]['customs_declaraion_document']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.cus_dec_doc') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['customs_declaraion_document'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['penality']!='' && $datas['data'][0]['penality']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.penalty') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['penality'] !!}</td>
        </tr>
        @endif
        <tr style=" color:#009688; font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;" colspan="2"><strong>{{ trans('WebSite.prinrt_info') }}</strong></td>
        </tr>
        @if($datas['data'][0]['print_type']!='' && $datas['data'][0]['print_type']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.print_type') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['print_type'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['print_size']!='' && $datas['data'][0]['print_size']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.print_size') }} </strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['print_size'] !!} cm</td>
        </tr>
        @endif
        @if($datas['data'][0]['print_no_of_colors']!='' && $datas['data'][0]['print_no_of_colors']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.no_of_colors') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['print_no_of_colors'] !!}</td>
        </tr>
        @endif
        <?php
            $i=0;
            if(isset($datas['media']['files'])){
                foreach($datas['media']['files'] as $file){
                    if($file['media_type']=='PrintImage'){
                        if($i==0){
                            ?>
                            <tr style="font-weight:500; font-family: poppins,arialuni;">
                                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.print_image') }}</strong></td>
                                <td style="padding : 5px ; font-family: poppins,arialuni;">
                            <?php
                        }
                        if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                        ?>
                            <img src="{{ $file['filepath'] }}" alt='Images' title="PrintImage" height="100px" style="padding:5px; margin-top:20px;">
                        <?php
                        }else{
                            ?>
                            {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>                                    <?php
                        }
                        $i++;
                    }
                }
                if($i>0){
                    ?>
                        </td>
                    </tr>
                    <?php
                }
            }
        ?>
        <tr style=" color:#009688; font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;" colspan="2"><strong>{{ trans('WebSite.trims_info') }}</strong></td>
        </tr>
        @if($datas['data'][0]['main_lable']!='' && $datas['data'][0]['main_lable']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.main_lable_info') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['main_lable'] !!}</td>
        </tr>
        @endif
        <?php
            $i=0;
            if(isset($datas['media']['files'])){
                foreach($datas['media']['files'] as $file){
                    if($file['media_type']=='MainLabel'){
                        if($i==0){
                            ?>
                            <tr style="font-weight:500; font-family: poppins,arialuni;">
                                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.main_label_sample') }}</strong></td>
                                <td style="padding : 5px ; font-family: poppins,arialuni;">
                            <?php
                        }
                        if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                        ?>
                            <img src="{{ $file['filepath'] }}" alt='Images' title="Mainlabel" height="100px" style="padding:5px; margin-top:20px;">
                        <?php
                        }else{
                            ?>
                            {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>
                            <?php
                        }
                        $i++;
                    }
                }
                if($i>0){
                    ?>
                        </td>
                    </tr>
                    <?php
                }
            }
        ?>
        @if($datas['data'][0]['washcare_lable']!='' && $datas['data'][0]['washcare_lable']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.washcare_lable_info') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['washcare_lable'] !!}</td>
        </tr>
        @endif
        <?php
            $i=0;
            if(isset($datas['media']['files'])){
                foreach($datas['media']['files'] as $file){
                    if($file['media_type']=='WashCareLabel'){
                        if($i==0){
                            ?>
                            <tr style="font-weight:500; font-family: poppins,arialuni;">
                                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.washcare_label_sample') }}</strong></td>
                                <td style="padding : 5px ; font-family: poppins,arialuni;">
                            <?php
                        }
                        if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                        ?>
                            <img src="{{ $file['filepath'] }}" alt='Images' title="Washcare" height="100px" style="padding:5px; margin-top:20px;">
                        <?php
                        }else{
                            ?>
                            {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>
                            <?php
                        }
                        $i++;
                    }
                }
                if($i>0){
                    ?>
                        </td>
                    </tr>
                    <?php
                }
            }
        ?>
        @if($datas['data'][0]['hangtag_lable']!='' && $datas['data'][0]['hangtag_lable']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.hangtag_info') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['hangtag_lable'] !!}</td>
        </tr>
        @endif
        <?php
            $i=0;
            if(isset($datas['media']['files'])){
                foreach($datas['media']['files'] as $file){
                    if($file['media_type']=='Hangtag'){
                        if($i==0){
                            ?>
                            <tr style="font-weight:500; font-family: poppins,arialuni;">
                                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.hangtag_sample') }}</strong></td>
                                <td style="padding : 5px ; font-family: poppins,arialuni;">
                            <?php
                        }
                        if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                        ?>
                            <img src="{{ $file['filepath'] }}" alt='Images' title="Hangtag" height="100px" style="padding:5px; margin-top:20px;">
                        <?php
                        }else{
                            ?>
                            {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>
                            <?php
                        }
                        $i++;
                    }
                }
                if($i>0){
                    ?>
                        </td>
                    </tr>
                    <?php
                }
            }
        ?>
        @if($datas['data'][0]['barcode_lable']!='' && $datas['data'][0]['barcode_lable']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.barcode_stickers_info') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['barcode_lable'] !!}</td>
        </tr>
        @endif
        <?php
            $i=0;
            if(isset($datas['media']['files'])){
                foreach($datas['media']['files'] as $file){
                    if($file['media_type']=='BarcodeStickers'){
                        if($i==0){
                            ?>
                            <tr style="font-weight:500; font-family: poppins,arialuni;">
                                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.barcode_stickers_sample') }}</strong></td>
                                <td style="padding : 5px ; font-family: poppins,arialuni;">
                            <?php
                        }
                        if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                        ?>
                            <img src="{{ $file['filepath'] }}" alt='Images' title="BarCode" height="100px" style="padding:5px; margin-top:20px;">
                        <?php
                        }else{
                            ?>
                            {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>
                            <?php
                        }
                        $i++;
                    }
                }
                if($i>0){
                    ?>
                        </td>
                    </tr>
                    <?php
                }
            }
        ?>
        @foreach ( $datas['additional'] as $add )
            @if($add['label']!='')
                <tr style="  font-weight:500; font-family: poppins,arialuni;">
                    <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ $add['label'] }}</strong></td>
                    <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $add['label_description'] !!}</td>
                </tr>

                <?php
                $i=0;
                if(isset($datas['media']['files'])){
                    foreach($datas['media']['files'] as $file){
                        if($file['media_type']==$add['media_type']){
                            if($i==0){
                                ?>
                                <tr>
                                    <td style="padding : 5px ; font-family: poppins,arialuni;" colspan="2">
                                <?php
                            }
                            if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                            ?>
                                <img src="{{ $file['filepath'] }}" alt='Images' title="{{ $add['label'] }}" height="100px" style="padding:5px; margin-top:20px;">
                            <?php
                            }else{
                                ?>
                                {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>
                                <?php
                            }
                            $i++;
                        }
                    }
                    if($i>0){
                        ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            @endif
        @endforeach
        @if($datas['data'][0]['trims_nominations']!='' && $datas['data'][0]['trims_nominations']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.trims_notification') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['trims_nominations'] !!}</td>
        </tr>
        @endif
        <tr style="color:#009688; font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;" colspan="2"><strong>{{ trans('WebSite.packing_info') }}</strong></td>
        </tr>
        @if($datas['data'][0]['poly_bag_size']!='' && $datas['data'][0]['poly_bag_size']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.polybag_size') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['poly_bag_size'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['poly_bag_material']!='' && $datas['data'][0]['poly_bag_material']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.polybag_meterial') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['poly_bag_material'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['poly_bag_print']!='' && $datas['data'][0]['poly_bag_print']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.polybag_print') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['poly_bag_print'] !!}</td>
        </tr>
        @endif
        <?php
            $i=0;
            if(isset($datas['media']['files'])){
                foreach($datas['media']['files'] as $file){
                    if($file['media_type']=='Polybag'){
                        if($i==0){
                            ?>
                            <tr style="font-weight:500; font-family: poppins,arialuni;">
                                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.polybag_sample') }}</strong></td>
                                <td style="padding : 5px ; font-family: poppins,arialuni;">
                            <?php
                        }
                        if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                        ?>
                            <img src="{{ $file['filepath'] }}" alt='Images' title="Polybag" height="100px" style="padding:5px; margin-top:20px;">
                        <?php
                        }else{
                            ?>
                            {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>
                            <?php
                        }
                        $i++;
                    }
                }
                if($i>0){
                    ?>
                        </td>
                    </tr>
                    <?php
                }
            }
        ?>
        @if($datas['data'][0]['carton_bag_dimensions']!='' && $datas['data'][0]['carton_bag_dimensions']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.cartonbox_diamenition') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['carton_bag_dimensions'] !!} cm</td>
        </tr>
        @endif
        @if($datas['data'][0]['carton_color']!='' && $datas['data'][0]['carton_color']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.carton_color') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['carton_color'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['carton_material']!='' && $datas['data'][0]['carton_material']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.carton_material') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['carton_material'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['carton_edge_finish']!='' && $datas['data'][0]['carton_edge_finish']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.catton_edge') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['carton_edge_finish'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['carton_mark']!='' && $datas['data'][0]['carton_mark']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.carton_details') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['carton_mark'] !!}</td>
        </tr>
        @endif
        <?php
            $i=0;
            if(isset($datas['media']['files'])){
                foreach($datas['media']['files'] as $file){
                    if($file['media_type']=='Carton'){
                        if($i==0){
                            ?>
                            <tr style="font-weight:500; font-family: poppins,arialuni;">
                                <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.carton_sample') }}</strong></td>
                                <td style="padding : 5px ; font-family: poppins,arialuni;">
                            <?php
                        }
                        if(!stristr($file['orginalfilename'],'.pdf') && !stristr($file['orginalfilename'],'.ai')){
                        ?>
                            <img src="{{ $file['filepath'] }}" alt='Images' title="CartonBox" height="100px" style="padding:5px; margin-top:20px;">
                        <?php
                        }else{
                            ?>
                            {{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}<br>
                            <?php
                        }
                        $i++;
                    }
                }
                if($i>0){
                    ?>
                        </td>
                    </tr>
                    <?php
                }
            }
        ?>
        @if($datas['data'][0]['make_up']!='' && $datas['data'][0]['make_up']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.makeup') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['make_up'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['films_cd']!='' && $datas['data'][0]['films_cd']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.flimscd') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['films_cd'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['picture_card']!='' && $datas['data'][0]['picture_card']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.picture_card') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['picture_card'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['inner_cardboard']!='' && $datas['data'][0]['inner_cardboard']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.inner_cardboard') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['inner_cardboard'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['estimate_delivery_date']!='' && $datas['data'][0]['estimate_delivery_date']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.est_delivery_date') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['estimate_delivery_date'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['shipping_size']!='' && $datas['data'][0]['shipping_size']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.shipping_size') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['shipping_size'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['air_frieght']!='' && $datas['data'][0]['air_frieght']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.air_freight') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['air_frieght'] !!}</td>
        </tr>
        @endif
        <tr style="color:#009688; font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;" colspan="2"><strong>{{ trans('WebSite.others') }}</strong></td>
        </tr>
        @if($datas['data'][0]['forbidden_substance_info']!='' && $datas['data'][0]['forbidden_substance_info']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.forbidden_subs_info') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['forbidden_substance_info'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['testing_requirements']!='' && $datas['data'][0]['testing_requirements']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.test_requiremennt') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['testing_requirements'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['sample_requirements']!='' && $datas['data'][0]['sample_requirements']!=NULL)
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.sample_requirements') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['sample_requirements'] !!}</td>
        </tr>
        @endif
        @if($datas['data'][0]['special_requests']!='' && $datas['data'][0]['special_requests']!=NULL)
        <tr style="  font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;width:30%"><strong>{{ trans('WebSite.special_requests') }}</strong></td>
            <td style="padding : 5px ; font-family: poppins,arialuni;">{!! $datas['data'][0]['special_requests'] !!}</td>
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
                            <a href="{{ url('') }}/downloadfile?path={{ urlencode($file['org_file_path']) }}&filename={{ $file['orginalfilename'] }}" style="display: inline-block">{{ $file['orginalfilename'] }}</a>
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
        @if($datas['data'][0]['measurement_sheet']!='' && $datas['data'][0]['measurement_sheet']!=NULL && $datas['data'][0]['measurement_sheet']!='[]')
        <tr style="font-weight:500; font-family: poppins,arialuni;">
            <td style="padding : 5px ; font-family: poppins,arialuni;" colspan="2">

                {{-- {{ json_decode($datas['data'][0]['measurement_sheet']) }} --}}

                <?php
                    // $msarr = json_decode($datas['data'][0]['measurement_sheet']);
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
    <div style="clear:both"></div>



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
//exit;
?>





