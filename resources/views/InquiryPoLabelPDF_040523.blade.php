<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ trans('WebSite.inquiry_labels') }}</title>
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
        body {font-family: 'Poppins';}
        .mainTable table {border: 1px solid #a5a2a2;border-collapse: collapse;}
        .mainTable td {border: 1px solid #a5a2a2;border-collapse: collapse;}
        .mainTable th {border: 1px solid #a5a2a2;border-collapse: collapse;}
        .page-break {page-break-after: always;}
        .tableType td p, table td p, table td li,table td a {word-break: break-word !important;padding-right: 15px;}
        .tableType {border-collapse: collapse;}
        table td {word-wrap:break-word !important; }
        table td img{display: block; margin:10px 5px 5px 5px }
        @page { margin-top: 100px;margin-bottom: 60px; }
        #header { position: fixed; left: 0px; top: -80px; right: 0px;text-align: center; }
        #footer { position: fixed; left: 0px; bottom: -50px; right: 0px;text-align: center; }
        .header_footer table {border: 1px solid #e0e0e0;border-collapse: collapse;}
        .mainTable td {border: 1px solid #e0e0e0;border-collapse: collapse;}
        .mainTable th {border: 1px solid #e0e0e0;border-collapse: collapse;}
    </style>

</head>
<?php
$status_arr=['Open','Closed','Approved','Rejected'];
$status_color=['#000000','#0000FF','#008000','#FF0000'];
$filter_type = $data['filter_type'];
$refId = $data['responses'][0]['reference_id'];
$print_arr=$print_img=$print_txt=[];
$print_i=$print_count=$j=$print_status=0;
$print_user=$print_date=$ref_id="";

$mainlb_arr=$mainlb_img=$mainlb_txt=[];
$mainlb_i=$mainlb_count=$mainlb_j=$mainlb_status=0;
$mainlb_user=$mainlb_date="";

$washlb_arr=$washlb_img=$washlb_txt=[];
$washlb_i=$washlb_count=$washlb_j=$washlb_status=0;
$washlb_user=$washlb_date="";

$hanglb_arr=$hanglb_img=$hanglb_txt=[];
$hanglb_i=$hanglb_count=$hanglb_j=$hanglb_status=0;
$hanglb_user=$hanglb_date="";

$barlb_arr=$barlb_img=$barlb_txt=[];
$barlb_i=$barlb_count=$barlb_j=$barlb_status=0;
$barlb_user=$barlb_date="";

$poly_arr=$poly_img=$poly_txt=[];
$poly_i=$poly_count=$poly_j=$poly_status=0;
$poly_user=$poly_date="";

$carton_arr=$carton_img=$carton_txt=[];
$carton_i=$carton_count=$carton_j=$carton_status=0;
$carton_user=$carton_date="";

$modified_user=$modified_date="";
$firstdate = '';

foreach ($data['responses'] as $arr) {
    if($firstdate==''){
        $modified_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
        $modified_date = date($data['dateFormat'],strtotime($arr['createdDate']));
        $firstdate = $arr['createdDate'];
    }elseif($arr['createdDate'] > $firstdate){
        $modified_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
        $modified_date = date($data['dateFormat'],strtotime($arr['createdDate']));
        $firstdate = $arr['createdDate'];
    }
    if($arr['type']=="PrintArtWork"){
        $ref_id=$arr['reference_id'];
        if($print_i==0)
            $refId = $arr['reference_id'];

        if($refId==$arr['reference_id']){
            if($arr['content_type']=='image'){
                $print_img[] = $arr['content'];
            }
            if($arr['content_type']=='text'){
                $print_txt[] = $arr['content'];
            }
            $print_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
            $print_date = date('Y-m-d H:i:s',strtotime($arr['createdDate']));
            $print_status=$arr['status']??0;
        }else{
            $print_arr[$j]['image']=$print_img;
            $print_arr[$j]['text']=$print_txt;
            $print_arr[$j]['refId']=$ref_id;
            $print_arr[$j]['user']=$print_user;
            $print_arr[$j]['date']=$print_date;
            $print_arr[$j]['status']=$print_status;

            $print_img=$print_txt=[];
            $refId = $arr['reference_id'];
            $print_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
            $print_date = date('Y-m-d H:i:s',strtotime($arr['createdDate']));
            $print_status=$arr['status']??0;
            if($arr['content_type']=='image'){
                $print_img[] = $arr['content'];
            }
            if($arr['content_type']=='text'){
                $print_txt[] = $arr['content'];
            }
            $j++;
        }
        $print_i++;
        $print_count++;
    }
    if($arr['type']=="MainLabel"){
        $ref_id=$arr['reference_id'];
        if($mainlb_i==0)
            $refId = $arr['reference_id'];

        if($refId==$arr['reference_id']){
            if($arr['content_type']=='image'){
                $mainlb_img[] = $arr['content'];
            }
            if($arr['content_type']=='text'){
                $mainlb_txt[] = $arr['content'];
            }
            $mainlb_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
            $mainlb_date = date('Y-m-d H:i:s',strtotime($arr['createdDate']));
            $mainlb_status=$arr['status']??0;
        }else{
            $mainlb_arr[$mainlb_j]['image']=$mainlb_img;
            $mainlb_arr[$mainlb_j]['text']=$mainlb_txt;
            $mainlb_arr[$mainlb_j]['refId']=$ref_id;
            $mainlb_arr[$mainlb_j]['user']=$mainlb_user;
            $mainlb_arr[$mainlb_j]['date']=$mainlb_date;
            $mainlb_arr[$mainlb_j]['status']=$mainlb_status;

            $mainlb_img=$mainlb_txt=[];
            $refId = $arr['reference_id'];
            $mainlb_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
            $mainlb_date = date('Y-m-d H:i:s',strtotime($arr['createdDate']));
            $mainlb_status=$arr['status']??0;
            if($arr['content_type']=='image'){
                $mainlb_img[] = $arr['content'];
            }
            if($arr['content_type']=='text'){
                $mainlb_txt[] = $arr['content'];
            }
            $mainlb_j++;
        }
        $mainlb_i++;
        $mainlb_count++;
    }
    if($arr['type']=="WashCare"){
        $ref_id=$arr['reference_id'];
        if($washlb_i==0)
            $refId = $arr['reference_id'];

        if($refId==$arr['reference_id']){
            if($arr['content_type']=='image'){
                $washlb_img[] = $arr['content'];
            }
            if($arr['content_type']=='text'){
                $washlb_txt[] = $arr['content'];
            }
            $washlb_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
            $washlb_date = date('Y-m-d H:i:s',strtotime($arr['createdDate']));
            $washlb_status=$arr['status']??0;
        }else{
            $washlb_arr[$washlb_j]['image']=$washlb_img;
            $washlb_arr[$washlb_j]['text']=$washlb_txt;
            $washlb_arr[$washlb_j]['refId']=$ref_id;
            $washlb_arr[$washlb_j]['user']=$washlb_user;
            $washlb_arr[$washlb_j]['date']=$washlb_date;
            $washlb_arr[$washlb_j]['status']=$washlb_status;

            $washlb_img=$washlb_txt=[];
            $refId = $arr['reference_id'];
            $washlb_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
            $washlb_date = date('Y-m-d H:i:s',strtotime($arr['createdDate']));
            $washlb_status=$arr['status']??0;
            if($arr['content_type']=='image'){
                $washlb_img[] = $arr['content'];
            }
            if($arr['content_type']=='text'){
                $washlb_txt[] = $arr['content'];
            }
            $washlb_j++;
        }
        $washlb_i++;
        $washlb_count++;
    }
    if($arr['type']=="HangTag"){
        $ref_id=$arr['reference_id'];
        if($hanglb_i==0)
            $refId = $arr['reference_id'];

        if($refId==$arr['reference_id']){
            if($arr['content_type']=='image'){
                $hanglb_img[] = $arr['content'];
            }
            if($arr['content_type']=='text'){
                $hanglb_txt[] = $arr['content'];
            }
            $hanglb_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
            $hanglb_date = date('Y-m-d H:i:s',strtotime($arr['createdDate']));
            $hanglb_status=$arr['status']??0;
        }else{
            $hanglb_arr[$hanglb_j]['image']=$hanglb_img;
            $hanglb_arr[$hanglb_j]['text']=$hanglb_txt;
            $hanglb_arr[$hanglb_j]['refId']=$ref_id;
            $hanglb_arr[$hanglb_j]['user']=$hanglb_user;
            $hanglb_arr[$hanglb_j]['date']=$hanglb_date;
            $hanglb_arr[$hanglb_j]['status']=$hanglb_status;

            $hanglb_img=$hanglb_txt=[];
            $refId = $arr['reference_id'];
            $hanglb_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
            $hanglb_date = date('Y-m-d H:i:s',strtotime($arr['createdDate']));
            $hanglb_status=$arr['status']??0;
            if($arr['content_type']=='image'){
                $hanglb_img[] = $arr['content'];
            }
            if($arr['content_type']=='text'){
                $hanglb_txt[] = $arr['content'];
            }
            $hanglb_j++;
        }
        $hanglb_i++;
        $hanglb_count++;
    }
    if($arr['type']=="BarCode"){
        $ref_id=$arr['reference_id'];
        if($barlb_i==0)
            $refId = $arr['reference_id'];

        if($refId==$arr['reference_id']){
            if($arr['content_type']=='image'){
                $barlb_img[] = $arr['content'];
            }
            if($arr['content_type']=='text'){
                $barlb_txt[] = $arr['content'];
            }
            $barlb_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
            $barlb_date = date('Y-m-d H:i:s',strtotime($arr['createdDate']));
            $barlb_status=$arr['status']??0;
        }else{
            $barlb_arr[$barlb_j]['image']=$barlb_img;
            $barlb_arr[$barlb_j]['text']=$barlb_txt;
            $barlb_arr[$barlb_j]['refId']=$ref_id;
            $barlb_arr[$barlb_j]['user']=$barlb_user;
            $barlb_arr[$barlb_j]['date']=$barlb_date;
            $barlb_arr[$barlb_j]['status']=$barlb_status;

            $barlb_img=$barlb_txt=[];
            $refId = $arr['reference_id'];
            $barlb_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
            $barlb_date = date('Y-m-d H:i:s',strtotime($arr['createdDate']));
            $barlb_status=$arr['status']??0;
            if($arr['content_type']=='image'){
                $barlb_img[] = $arr['content'];
            }
            if($arr['content_type']=='text'){
                $barlb_txt[] = $arr['content'];
            }
            $barlb_j++;
        }
        $barlb_i++;
        $barlb_count++;
    }
    if($arr['type']=="PolyBag"){
        $ref_id=$arr['reference_id'];
        if($poly_i==0)
            $refId = $arr['reference_id'];

        if($refId==$arr['reference_id']){
            if($arr['content_type']=='image'){
                $poly_img[] = $arr['content'];
            }
            if($arr['content_type']=='text'){
                $poly_txt[] = $arr['content'];
            }
            $poly_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
            $poly_date = date('Y-m-d H:i:s',strtotime($arr['createdDate']));
            $poly_status=$arr['status']??0;
        }else{
            $poly_arr[$poly_j]['image']=$poly_img;
            $poly_arr[$poly_j]['text']=$poly_txt;
            $poly_arr[$poly_j]['refId']=$ref_id;
            $poly_arr[$poly_j]['user']=$poly_user;
            $poly_arr[$poly_j]['date']=$poly_date;
            $poly_arr[$poly_j]['status']=$poly_status;

            $poly_img=$poly_txt=[];
            $refId = $arr['reference_id'];
            $poly_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
            $poly_date = date('Y-m-d H:i:s',strtotime($arr['createdDate']));
            $poly_status=$arr['status']??0;
            if($arr['content_type']=='image'){
                $poly_img[] = $arr['content'];
            }
            if($arr['content_type']=='text'){
                $poly_txt[] = $arr['content'];
            }
            $poly_j++;
        }
        $poly_i++;
        $poly_count++;
    }
    if($arr['type']=="Carton"){
        $ref_id=$arr['reference_id'];
        if($carton_i==0)
            $refId = $arr['reference_id'];

        if($refId==$arr['reference_id']){
            if($arr['content_type']=='image'){
                $carton_img[] = $arr['content'];
            }
            if($arr['content_type']=='text'){
                $carton_txt[] = $arr['content'];
            }
            $carton_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
            $carton_date = date('Y-m-d H:i:s',strtotime($arr['createdDate']));
            $carton_status=$arr['status']??0;
        }else{
            $carton_arr[$carton_j]['image']=$carton_img;
            $carton_arr[$carton_j]['text']=$carton_txt;
            $carton_arr[$carton_j]['refId']=$ref_id;
            $carton_arr[$carton_j]['user']=$carton_user;
            $carton_arr[$carton_j]['date']=$carton_date;
            $carton_arr[$carton_j]['status']=$carton_status;

            $carton_img=$carton_txt=[];
            $refId = $arr['reference_id'];
            $carton_user = $arr['user_type']=='user' ? $arr['username'] : $arr['staffname'];
            $carton_date = date('Y-m-d H:i:s',strtotime($arr['createdDate']));
            $carton_status=$arr['status']??0;
            if($arr['content_type']=='image'){
                $carton_img[] = $arr['content'];
            }
            if($arr['content_type']=='text'){
                $carton_txt[] = $arr['content'];
            }
            $carton_j++;
        }
        $carton_i++;
        $carton_count++;
    }
}

if($print_i==$print_count){
    $print_arr[$j]['image']=$print_img;
    $print_arr[$j]['text']=$print_txt;
    $print_arr[$j]['refId']=$ref_id;
    $print_arr[$j]['user']=$print_user;
    $print_arr[$j]['date']=$print_date;
    $print_arr[$j]['status']=$print_status;
    $print_i=$j=0;
}
if($mainlb_i==$mainlb_count){
    $mainlb_arr[$mainlb_j]['image']=$mainlb_img;
    $mainlb_arr[$mainlb_j]['text']=$mainlb_txt;
    $mainlb_arr[$mainlb_j]['refId']=$ref_id;
    $mainlb_arr[$mainlb_j]['user']=$mainlb_user;
    $mainlb_arr[$mainlb_j]['date']=$mainlb_date;
    $mainlb_arr[$mainlb_j]['status']=$mainlb_status;
    $mainlb_i=$mainlb_j=0;
}
if($washlb_i==$washlb_count){
    $washlb_arr[$washlb_j]['image']=$washlb_img;
    $washlb_arr[$washlb_j]['text']=$washlb_txt;
    $washlb_arr[$washlb_j]['refId']=$ref_id;
    $washlb_arr[$washlb_j]['user']=$washlb_user;
    $washlb_arr[$washlb_j]['date']=$washlb_date;
    $washlb_arr[$washlb_j]['status']=$washlb_status;
    $washlb_i=$washlb_j=0;
}
if($hanglb_i==$hanglb_count){
    $hanglb_arr[$hanglb_j]['image']=$hanglb_img;
    $hanglb_arr[$hanglb_j]['text']=$hanglb_txt;
    $hanglb_arr[$hanglb_j]['refId']=$ref_id;
    $hanglb_arr[$hanglb_j]['user']=$hanglb_user;
    $hanglb_arr[$hanglb_j]['date']=$hanglb_date;
    $hanglb_arr[$hanglb_j]['status']=$hanglb_status;
    $hanglb_i=$hanglb_j=0;
}
if($barlb_i==$barlb_count){
    $barlb_arr[$barlb_j]['image']=$barlb_img;
    $barlb_arr[$barlb_j]['text']=$barlb_txt;
    $barlb_arr[$barlb_j]['refId']=$ref_id;
    $barlb_arr[$barlb_j]['user']=$barlb_user;
    $barlb_arr[$barlb_j]['date']=$barlb_date;
    $barlb_arr[$barlb_j]['status']=$barlb_status;
    $barlb_i=$barlb_j=0;
}
if($poly_i==$poly_count){
    $poly_arr[$poly_j]['image']=$poly_img;
    $poly_arr[$poly_j]['text']=$poly_txt;
    $poly_arr[$poly_j]['refId']=$ref_id;
    $poly_arr[$poly_j]['user']=$poly_user;
    $poly_arr[$poly_j]['date']=$poly_date;
    $poly_arr[$poly_j]['status']=$poly_status;
    $poly_i=$poly_j=0;
}
if($carton_i==$carton_count){
    $carton_arr[$carton_j]['image']=$carton_img;
    $carton_arr[$carton_j]['text']=$carton_txt;
    $carton_arr[$carton_j]['refId']=$ref_id;
    $carton_arr[$carton_j]['user']=$carton_user;
    $carton_arr[$carton_j]['date']=$carton_date;
    $carton_arr[$carton_j]['status']=$carton_status;
    $carton_i=$carton_j=0;
}
?>
<body style="font-family: poppins,arialuni; font-size: 14px;">
    <div id="header">
        <table width="100%"  style="border-collapse: collapse;font-family: poppins,arialuni; font-size:12px;" cellspacing="1px" class="mainTable header_footer" >
            <tr>
                <td width="15%" rowspan="2">
                    @if($data['useLogo']==1 && $data['userLogo']!='')
                        <img src="{{ $data['serverURL'].$data['userLogo'] }}"
                        style="background-color: #FFFFFF; height: 40px; max-width:130px " />
                    @else
                        <img src="{{ public_path() . "/images/dms-log-with-tag.png" }}"
                        style="background-color: #FFFFFF; height: 40px; width:130px" />
                    @endif
                </td>
                <td width="8%" style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.Po') }}</strong>
                </td>
                <td style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    PO-{{ $data['responses'][0]['po_id'] }}
                </td>
                <td width="8%" style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.article') }}</strong>
                </td>
                <td style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ $data['responses'][0]['article'] }}
                </td>
                <td width="8%" style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.composition') }}</strong>
                </td>
                <td style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ $data['responses'][0]['fabric_composition'] }}
                </td>
                @if($data['useLogo']==1 && $data['userLogo']!='')
                <td width="7%" rowspan="2">
                    <img src="{{ public_path() . "/images/dms_small.png" }}"
                        style="background-color: #FFFFFF; height: 40px;" />
                </td>
                @endif

            </tr>
            <tr>
                <td style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.Style') }}</strong>
                </td>
                <td style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ $data['responses'][0]['style_no'] }}
                </td>
                <td style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.category') }}</strong>
                </td>
                <td style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ $data['responses'][0]['category'] }}
                </td>
                <td style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.po_date') }}</strong>
                </td>
                <td style="padding :0px 5px 2px 5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{ date($data['dateFormat'],strtotime($data['responses'][0]['inq_date'])) }}
                </td>
            </tr>
        </table>
    </div>
    <div id="footer">
        <table width="96%"  style="border-collapse: collapse;font-family: poppins,arialuni; font-size:12px;" cellspacing="1px" class="mainTable header_footer" >
            <tr>
                <td width="7%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.created_by') }}</strong>
                </td>
                <td style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    <?php
                        echo ($data['user_info']['user_name']!="") ?  $data['user_info']['user_name'] : $data['user_info']['staff_name'];
                    ?>
                </td>
                <td width="7%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.created_on') }}</strong>
                </td>
                <td width="8%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                     {{ date($data['dateFormat'],strtotime($data['user_info']['date_created'])) }}
                </td>
                <td width="7.5%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.modified_by') }}</strong>
                </td>
                <td style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    <?php
                       // echo ($data['user_info']['user_name']!="") ?  $data['user_info']['user_name'] : $data['user_info']['staff_name'];
                    ?>
                    {{ $modified_user }}
                </td>
                <td width="7.5%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.modified_on') }}</strong>
                </td>
                <td width="8%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{-- {{ $data['user_info']['date_created'] }} --}}
                    {{ date($data['dateFormat'],strtotime($modified_date)) }}
                </td>
                <td width="7%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;background-color: #f0efef;">
                    <strong>{{ trans('WebSite.last_issue') }}</strong>
                </td>
                <td width="8%" style="padding :0px 3px 2px; font-family: poppins,arialuni,notosansjp,poppins-semibold;">
                    {{-- {{ $data['user_info']['date_created'] }} --}}
                    {{ date($data['dateFormat'],strtotime($modified_date)) }}
                </td>
            </tr>

        </table>
    </div>
    <p style="font-family: poppins,arialuni,notosansjp,poppins-semibold;font-weight:800; font-size:16px; padding:0; margin:-10px 0 10px 0; "><strong>{{ trans('WebSite.bill_of_materials') }}</strong></p>

    <table width="100%" style="border-collapse: collapse;font-family: poppins,arialuni;" cellspacing="1px" class="mainTable">
        @if (count($print_arr)>0 && (in_array('All',$filter_type) || in_array('printArtwork',$filter_type)))
            <tr style="background-color: #f0efef; color: #000000; font-weight:800; font-size:15px">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" colspan="3"><strong>{{ trans('WebSite.prinrt_info') }}</strong></td>
            </tr>
            @php
                $title=trans('WebSite.print_image');
                $i=1;
                $border_bottom='';
            @endphp
            <tbody style="page-break-inside: avoid;">
            @foreach ( $print_arr as $arr)
                <tr style="background-color: #ffffff; color: #000000; font-weight:600;">
                    @if($i==count($print_arr))
                        @php
                        $border_bottom='border-bottom: 1px solid #e0e0e0';
                        @endphp
                    @endif
                    @if($title==trans('WebSite.print_image'))
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;border-bottom:none" width="15%"  >
                        <strong>{{ trans('WebSite.print_image') }}</strong></td>
                    @else
                    <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;border:none;border-left: 1px solid #e0e0e0; {{ $border_bottom }}" width="15%"  >
                        </td>
                    @endif
                    <td width="15%">
                        @foreach ($arr['image'] as $response)
                            <img src="{{ $data['serverURL'].$response }}" title="PrintImage" width="100px"><br/>
                        @endforeach
                    </td>
                    <td style="padding : 5px 5px 1px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="70%"><strong>
                        @foreach ($arr['text'] as $response)
                            {!! $response !!}
                        @endforeach
                        <p style="font-size:10px; margin:5px 0 0 0; padding:0">
                            {{-- @if($arr['status'] !=0)
                                <span style="color:{{ $status_color[$arr['status']] }}">{{ $status_arr[$arr['status']] }} &nbsp;</span>
                            @endif --}}
                            {{ $arr['user'] }} {{ $arr['date'] }}</p>
                        </strong>
                    </td>
                </tr>
                @php
                    $title='';
                    $i++;
                @endphp
            @endforeach
        </tbody>
        @endif
        @if ((count($mainlb_arr)>0 || count($washlb_arr)>0 || count($hanglb_arr)>0 || count($barlb_arr)>0 ) &&
        (in_array('All',$filter_type) || in_array('mainLabel',$filter_type) || in_array('washCare',$filter_type) || in_array('hangtag',$filter_type) || in_array('barcode',$filter_type)))
            <tr style="background-color: #f0efef; color: #000000; font-weight:800; font-size:15px">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" colspan="3"><strong>{{ trans('WebSite.trims_info') }}</strong></td>
            </tr>
            @php
                $title=trans('WebSite.main_lable_info');
                $i=1;
                $border_bottom='';
            @endphp
            @if(in_array('All',$filter_type) || in_array('mainLabel',$filter_type))
                @foreach ( $mainlb_arr as $arr)
                    <tr style="background-color: #ffffff; color: #000000; font-weight:600;">

                        @if($i==count($mainlb_arr))
                            @php
                            $border_bottom='border-bottom: 1px solid #e0e0e0';
                            @endphp
                        @endif
                        @if($title==trans('WebSite.main_lable_info'))
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;border-bottom:none" width="15%"  >
                            <strong>{{ trans('WebSite.main_lable_info') }}</strong></td>
                        @else
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;border:none;border-left: 1px solid #e0e0e0; {{ $border_bottom }}" width="15%"  >
                            </td>
                        @endif
                        <td width="15%">
                            @foreach ($arr['image'] as $response)
                                <img src="{{ $data['serverURL'].$response }}" title="MainLabel" width="100px"><br/>
                            @endforeach
                        </td>
                        <td style="padding : 5px 5px 1px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="70%"><strong>
                            @foreach ($arr['text'] as $response)
                                {!! $response !!}
                            @endforeach
                            <p style="font-size:10px; margin:5px 0 0 0; padding:0">
                                {{-- @if($arr['status'] !=0)
                                    <span style="color:{{ $status_color[$arr['status']] }}">{{ $status_arr[$arr['status']] }} &nbsp;</span>
                                @endif --}}
                                {{ $arr['user'] }} {{ $arr['date'] }}</p>
                            </strong>
                        </td>
                    </tr>
                    @php
                        $title='';
                        $i++;
                    @endphp
                @endforeach
            @endif
            @if(in_array('All',$filter_type) || in_array('washCare',$filter_type))
                @foreach ( $washlb_arr as $arr)
                    <tr style="background-color: #ffffff; color: #000000; font-weight:600;">
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="15%" ><strong>{{ trans('WebSite.washcare_lable_info') }}</strong></td>
                        <td width="15%">
                            @foreach ($arr['image'] as $response)
                                <img src="{{ $data['serverURL'].$response }}" title="WashcareLabel" width="100px"><br/>
                            @endforeach
                        </td>
                        <td style="padding : 5px 5px 1px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="70%"><strong>
                            @foreach ($arr['text'] as $response)
                                {!! $response !!}
                            @endforeach
                            <p style="font-size:10px; margin:5px 0 0 0; padding:0">
                                {{-- @if($arr['status'] !=0)
                                    <span style="color:{{ $status_color[$arr['status']] }}">{{ $status_arr[$arr['status']] }} &nbsp;</span>
                                @endif --}}
                                {{ $arr['user'] }} {{ $arr['date'] }} </p>
                            </strong>
                        </td>
                    </tr>
                    @if ($arr['status']=='2')
                    {{-- <tr style="background-color: #ffffff; color: #000000; font-weight:600;">
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="15%" ><strong>{{ trans('WebSite.washcare_lable_info') }}</strong></td>
                        <td colspan="2">
                            @foreach ($arr['image'] as $response)
                                <img src="{{ $data['serverURL'].$response }}" title="MainLabel" style="max-width: 100%"><br/>
                            @endforeach
                        </td>
                    </tr> --}}
                    @endif
                @endforeach
            @endif
            @if(in_array('All',$filter_type) || in_array('hangtag',$filter_type))
                @foreach ( $hanglb_arr as $arr)
                    <tr style="background-color: #ffffff; color: #000000; font-weight:600;">
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="15%" ><strong>{{ trans('WebSite.hangtag_info') }}</strong></td>
                        <td width="15%">
                            @foreach ($arr['image'] as $response)
                                <img src="{{ $data['serverURL'].$response }}" title="HangTag" width="100px"><br/>
                            @endforeach
                        </td>
                        <td style="padding : 5px 5px 1px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="70%"><strong>
                            @foreach ($arr['text'] as $response)
                                {!! $response !!}
                            @endforeach
                            <p style="font-size:10px; margin:5px 0 0 0; padding:0">
                                {{-- @if($arr['status'] !=0)
                                    <span style="color:{{ $status_color[$arr['status']] }}">{{ $status_arr[$arr['status']] }} &nbsp;</span>
                                @endif --}}
                                {{ $arr['user'] }} {{ $arr['date'] }}</p>

                            </strong>
                        </td>
                    </tr>
                @endforeach
            @endif
            @if(in_array('All',$filter_type) || in_array('barcode',$filter_type))
                @foreach ( $barlb_arr as $arr)
                    <tr style="background-color: #ffffff; color: #000000; font-weight:600;">
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="15%" ><strong>{{ trans('WebSite.barcode_stickers_info') }}</strong></td>
                        <td width="15%">
                            @foreach ($arr['image'] as $response)
                                <img src="{{ $data['serverURL'].$response }}" title="BarCode" width="100px"><br/>
                            @endforeach
                        </td>
                        <td style="padding : 5px 5px 1px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="70%"><strong>
                            @foreach ($arr['text'] as $response)
                                {!! $response !!}
                            @endforeach
                            <p style="font-size:10px; margin:5px 0 0 0; padding:0">
                                {{-- @if($arr['status'] !=0)
                                    <span style="color:{{ $status_color[$arr['status']] }}">{{ $status_arr[$arr['status']] }} &nbsp;</span>
                                @endif --}}
                                {{ $arr['user'] }} {{ $arr['date'] }}</p>

                            </strong>
                        </td>
                    </tr>
                @endforeach
            @endif
        @endif
        @if ((count($poly_arr)>0 || count($carton_arr)>0) && (in_array('All',$filter_type) || in_array('polybag',$filter_type) || in_array('carton',$filter_type)))
            <tr style="background-color: #f0efef; color: #000000; font-weight:800; font-size:15px;">
                <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" colspan="3"><strong>{{ trans('WebSite.packing_info') }}</strong></td>
            </tr>
            @if(in_array('All',$filter_type) || in_array('polybag',$filter_type))
                @foreach ( $poly_arr as $arr)
                    <tr style="background-color: #ffffff; color: #000000; font-weight:600;">
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="15%" ><strong>{{ trans('WebSite.polybag_sample') }}</strong></td>
                        <td width="15%">
                            @foreach ($arr['image'] as $response)
                                <img src="{{ $data['serverURL'].$response }}" title="Polybag" width="100px"><br/>
                            @endforeach
                        </td>
                        <td style="padding : 5px 5px 1px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="70%"><strong>
                            @foreach ($arr['text'] as $response)
                                {!! $response !!}
                            @endforeach
                            <p style="font-size:10px; margin:5px 0 0 0; padding:0">
                                {{-- @if($arr['status'] !=0)
                                    <span style="color:{{ $status_color[$arr['status']] }}">{{ $status_arr[$arr['status']] }} &nbsp;</span>
                                @endif --}}
                                {{ $arr['user'] }} {{ $arr['date'] }}</p>
                            </strong>
                        </td>
                    </tr>
                @endforeach
            @endif
            @if(in_array('All',$filter_type) || in_array('carton',$filter_type))
                @foreach ( $carton_arr as $arr)
                    <tr style="background-color: #ffffff; color: #000000; font-weight:600;">
                        <td style="padding : 5px 5px 10px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="15%" ><strong>{{ trans('WebSite.carton_details') }}</strong></td>
                        <td width="15%">
                            @foreach ($arr['image'] as $response)
                                <img src="{{ $data['serverURL'].$response }}" title="Polybag" width="100px"><br/>
                            @endforeach
                        </td>
                        <td style="padding : 5px 5px 1px 10px; font-family: poppins,arialuni,notosansjp,poppins-semibold;" width="70%"><strong>
                            @foreach ($arr['text'] as $response)
                                {!! $response !!}
                            @endforeach
                            <p style="font-size:10px; margin:5px 0 0 0; padding:0">
                                {{-- @if($arr['status'] !=0)
                                    <span style="color:{{ $status_color[$arr['status']] }}">{{ $status_arr[$arr['status']] }} &nbsp;</span>
                                @endif --}}
                                {{ $arr['user'] }} {{ $arr['date'] }}</p>
                            </strong>
                        </td>
                    </tr>
                @endforeach
            @endif
        @endif


    </table>

    <footer>
        <script type="text/php">
            if (isset($pdf)) {
               $font = $fontMetrics->getFont("Arial", "bold");
               $pdf->page_text(785, 568, "{PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
            }
        </script>
    </footer>

</body>
</html>
