
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

<body style="font-family: poppins,arialuni,notosansjp; font-size: 14px;">

    <div style="margin:25px 0;">
        <img src="{{ public_path() . '/images/dms-log-with-tag.png' }}"
            style="background-color: #FFFFFF; height: 98px; width:197px" />
        <div style="float:right; font-size:25px; font-weight:600; color: #8C878D; ">
            <strong>{{ trans('WebSite.Inquiry') }}</strong>
            <div
                style=" background-color: #D1E7E4; font-size:16px; color: #178677; text-align:center;font-weight: 600;
            padding: 1px 3px 5px;">
                <img src="{{ public_path() . '/images/CalendarIcon.svg' }}" /> {{ date('d M Y',strtotime($datas['inqdet'][0]['created_date'])) }}
            </div>
        </div>


    </div>
    <div style="clear : both;"></div>

    <table style="width: 100%;border-collapse: collapse;font-family: poppins,arialuni,notosansjp;"
    cellspacing="1px" class="mainTable">
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.article_name') }}</strong><br>
                {{ $datas['inqdet'][0]['article_name'] }}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.style_number') }}</strong><br>
                {{ $datas['data']['style_no'] }}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.fabric_type') }}</strong><br>
                {{ $datas['inqdet'][0]['fabric_type'] }}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.fabric_gsm') }}</strong><br>
                {{ $datas['data']['fabric_GSM'] }}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.yarn_count') }}</strong><br>{{ $datas['data']['yarn_count'] }}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.sample_images') }}</strong><br>
                <?php
                    if(isset($datas['media']['files'])){
                        foreach($datas['media']['files'] as $file){
                            if($file['media_type']=='SampleFormat')
                                echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' width='100px' style='padding:5px'>";
                        }
                    }
                ?>
            </td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.measurment') }}</strong><br>
                <?php
                    if(isset($datas['media']['files'])){
                        foreach($datas['media']['files'] as $file){
                            if($file['media_type']=='MeasurementSheet')
                                echo $datas['media']['serverURL'].$file['filepath'];
                        }
                    }else{
                        echo '--';
                    }
                ?>
            </td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.target_price') }}</strong><br>
                {{ $datas['data']['target_price'] }}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.Incoterms') }}</strong><br>
                {{ $datas['inqdet'][0]['income_terms'] }}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.payment_terms') }}</strong><br>
                {{ $datas['data']['payment_terms'] }}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.due_date') }}</strong><br>
                {{ $datas['data']['due_date'] }}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.sty_art_desc') }}</strong><br>
                {!! $datas['data']['style_article_description'] !!}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.spl_finish') }}</strong><br>
                {!! $datas['data']['special_finish'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.total_qty') }}</strong><br>
                {!! $datas['data']['total_qty'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;" >
                <strong>{{ trans('WebSite.sku_details') }}</strong>
                <table style="width: 100%;">
                    <?php
                    if(!empty($datas['sku'])){
                        ?>
                        <tr>
                            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.colors') }}</strong></td>
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
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.patterns') }}</strong><br>
                {!! $datas['data']['patterns'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.jurisdiction') }}</strong><br>
                {!! $datas['data']['jurisdiction'] !!}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.cus_dec_doc') }}</strong><br>
                {!! $datas['data']['customs_declaraion_document'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.penalty') }}</strong><br>
                {!! $datas['data']['penality'] !!}</td>
        </tr>
        <tr style="background-color: #f6f6f6; color:#009688; font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.prinrt_info') }}</strong></td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.print_type') }}</strong><br>
                {!! $datas['data']['print_type'] !!}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.print_size') }}</strong><br>
                {!! $datas['data']['print_size'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.no_of_colors') }}</strong><br>
                {!! $datas['data']['print_no_of_colors'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.print_image') }}</strong><br>
                <?php
                    if(isset($datas['media']['files'])){
                        foreach($datas['media']['files'] as $file){
                            if($file['media_type']=='PrintImage')
                                echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' width='100px' style='padding:5px' >";
                        }
                    }
                ?>
            </td>
        </tr>
        <tr style="background-color: #f6f6f6; color:#009688; font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;" ><strong>{{ trans('WebSite.trims_info') }}</strong></td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.main_lable_info') }}</strong><br>
                {!! $datas['data']['main_lable'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.main_label_sample') }}</strong><br>
                <?php
                if(isset($datas['media']['files'])){
                    foreach($datas['media']['files'] as $file){
                        if($file['media_type']=='MainLabel')
                            echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' width='100px' style='padding:5px' >";
                    }
                }
            ?>
            </td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.washcare_lable_info') }}</strong><br>
                {!! $datas['data']['washcare_lable'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.washcare_label_sample') }}</strong><br>
                <?php
                if(isset($datas['media']['files'])){
                    foreach($datas['media']['files'] as $file){
                        if($file['media_type']=='WashCareLabel')
                            echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' width='100px' style='padding:5px' >";
                    }
                }
            ?>
            </td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.hangtag_info') }}</strong><br>
                {!! $datas['data']['hangtag_lable'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.hangtag_sample') }}</strong><br>
                <?php
                if(isset($datas['media']['files'])){
                    foreach($datas['media']['files'] as $file){
                        if($file['media_type']=='Hangtag')
                            echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' width='100px' style='padding:5px'>";
                    }
                }
            ?>
            </td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.barcode_stickers_info') }}</strong><br>
                {!! $datas['data']['barcode_lable'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.barcode_stickers_sample') }}</strong><br>
                <?php
                if(isset($datas['media']['files'])){
                    foreach($datas['media']['files'] as $file){
                        if($file['media_type']=='BarcodeStickers')
                            echo "<img src='".$datas['media']['serverURL'].$file['filepath']."' alt='Images' width='100px' style='padding:5px' >";
                    }
                }
            ?>
            </td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.trims_notification') }}</strong><br>
                {!! $datas['data']['trims_nominations'] !!}</td>
        </tr>
        <tr style="color:#009688; font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.packing_info') }}</strong></td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.polybag_size') }}</strong><br>
                {!! $datas['data']['poly_bag_size'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.polybag_meterial') }}</strong><br>
                {!! $datas['data']['poly_bag_material'] !!}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.polybag_print') }}</strong><br>
                {!! $datas['data']['poly_bag_price'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.cartonbox_diamenition') }}</strong><br>
                {!! $datas['data']['carton_bag_dimensions'] !!}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.carton_color') }}</strong><br>
                {!! $datas['data']['carton_color'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.carton_material') }}</strong><br>
                {!! $datas['data']['carton_material'] !!}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.catton_edge') }}</strong><br>
                {!! $datas['data']['carton_edge_finish'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.carton_details') }}</strong><br>
                {!! $datas['data']['carton_mark'] !!}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.makeup') }}</strong><br>
                {!! $datas['data']['make_up'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.flimscd') }}</strong><br>
                {!! $datas['data']['films_cd'] !!}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.picture_card') }}</strong><br>
                {!! $datas['data']['picture_card'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.inner_cardboard') }}</strong><br>
                {!! $datas['data']['inner_cardboard'] !!}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.est_delivery_date') }}</strong><br>
                {!! $datas['data']['estimate_delivery_date'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.shipping_size') }}</strong><br>
                {!! $datas['data']['shipping_size'] !!}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.air_freight') }}</strong><br>
                {!! $datas['data']['air_frieght'] !!}</td>
        </tr>
        <tr style="color:#009688; font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.others') }}</strong></td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.forbidden_subs_info') }}</strong><br>
                {!! $datas['data']['forbidden_substance_info'] !!}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.test_requiremennt') }}</strong><br>
                {!! $datas['data']['testing_requirements'] !!}</td>
        </tr>
        <tr style="font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.sample_requirements') }}</strong><br>
                {!! $datas['data']['sample_requirements'] !!}</td>
        </tr>
        <tr style="background-color: #f6f6f6;  font-weight:500; font-family: poppins,arialuni,notosansjp;">
            <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.special_requests') }}</strong><br>
                {!! $datas['data']['special_requests'] !!}</td>
        </tr>


    </table>

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
?>





