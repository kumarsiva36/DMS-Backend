<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Techpack</title>
    <style type="text/css">
     @page {
            margin: 100px 25px;
        }
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
        body {font-family: 'Poppins';color: #000000;}
        .mainTable table {border: 1px solid #2d292a;border-collapse: collapse;}
        .mainTable td {border: 1px solid #2d292a;border-collapse: collapse;}
        .mainTable th {border: 1px solid #2d292a;border-collapse: collapse;}
        .page-break {page-break-after: always;}
        .tableType td p, table td p, table td li,table td a {word-break: break-word !important;padding-right: 15px;}
        .tableType {border-collapse: collapse;}
        table td {word-wrap:break-word !important; }
        table td img{display: block; margin:10px 5px 5px 5px }
        @page { margin-top: 100px;margin-bottom: 60px; }
        #header { position: fixed; left: 0px; top: -70px; right: 0px;text-align: center; }
        #footer { position: fixed; left: 0px; bottom: -50px; right: 0px;text-align: center; }
        .header_footer table {border: 1px solid #e0e0e0;border-collapse: collapse;}
        header {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            height: 20px;
            text-align: center;
            line-height: 15px;

        }
        main {

            padding: 0px; /* Adjust the padding as needed */
            width: calc(100% - 50px); /* Adjust for the left and right margin */
            height: calc(100vh - 200px); /* 100vh minus header and footer height and margins */
            box-sizing: border-box;
            position: relative;
            top: 10px; /* Move the main content down to accommodate the header */
            bottom: 50px;

        }
        .page-break {
            /* page-break-before: always; */
            page-break-inside: avoid;
            display: block;
            height: 10px !important; /* To prevent blank page issue */
            margin: 0;
            padding: 0;
            line-height: 0em !important;
        }
        footer {
            position: fixed;
            bottom: -10px;
            left: 0px;
            right:0px;
            height: 20px;
            text-align: center;
            line-height: 15px;
        }
        .noborder{ border: none!important; }
        p{margin: 0; padding: 0}
        a{margin: 0; padding: 0; text-decoration: none;}
    </style>

</head>
@php
    $ms_sheet_avil=0;
@endphp
<body style="font-family: poppins,arialuni; font-size: 14px;">
    <header>
        <div style="padding: 0px; margin: 0px;width: 100% !important;text-align: right;">
        <table width="100%"  style="border-collapse: collapse;font-family: poppins,arialuni; font-size:12px;float: right;" cellspacing="1px" class="mainTable header_footer" >
            <tr>
                <td width="8%"  style="padding :0px 3px 4px; font-family: poppins,arialuni;background-color: #d3d3d3;">
                    PO Number
                 </td>
                <td width="12%" style="padding :0px 3px 4px; font-family: poppins,arialuni;">
                    {{-- {{ $data['responses'][0]['order_id'] }} --}}
                    {{ $data['teckpackINFO']['po_no']?$data['teckpackINFO']['po_no']:"-" }}
                </td>
                <td width="8%"  style="padding :0px 3px 4px; font-family: poppins,arialuni;background-color: #d3d3d3;">
                   Style Number

                </td>
                <td width="12%" style="padding :0px 3px 4px; font-family: poppins,arialuni;">

                    {{ $data['teckpackINFO']['style_no']?$data['teckpackINFO']['style_no']:"-" }}
                </td>
                <td width="8%"  style="padding :0px 3px 4px; font-family: poppins,arialuni;background-color: #d3d3d3;">
                    Article

                 </td>
                 <td width="12%" style="padding :0px 3px 4px; font-family: poppins,arialuni;">

                    {{ $data['teckpackINFO']['article_name']?$data['teckpackINFO']['article_name']:"-" }}
                 </td>
            </tr>
            <tr>
                <td width="8%"  style="padding :0px 3px 4px; font-family: poppins,arialuni;background-color: #d3d3d3;">
                    Category
                 </td>
                <td width="12%" style="padding :0px 3px 4px; font-family: poppins,arialuni;">

                    {{ $data['teckpackINFO']['category_name']?$data['teckpackINFO']['category_name']:"-" }}
                </td>
                <td width="8%"  style="padding :0px 3px 4px; font-family: poppins,arialuni;background-color: #d3d3d3;">
                   Fabric

                </td>
                <td width="12%" style="padding :0px 3px 4px; font-family: poppins,arialuni;">

                    {{ $data['teckpackINFO']['fabric_name']?$data['teckpackINFO']['fabric_name']:"-" }}
                </td>
                <td width="8%"  style="padding :0px 3px 4px; font-family: poppins,arialuni;background-color: #d3d3d3;">
                   Printed Date

                 </td>
                 <td width="12%" style="padding :0px 3px 4px; font-family: poppins,arialuni;">
                    {{  date("d-M-Y")}}
                    {{-- {{ $data['teckpackINFO']['size_name']?$data['teckpackINFO']['size_name']:"-" }} --}}
                 </td>
            </tr>
            </tr>
        </table>
        </div>
        <div style="padding: 0px; margin: 0px;width: 100%;text-align: left;font-size: 14px;"><b><!--Logo Or Text here--></b></div>
    </header>
    <footer>
        <table width="100%"  style="border-collapse: collapse;font-family: poppins,arialuni; font-size:12px;v-align:middel;" cellspacing="1px" class="mainTable header_footer" >
            <tr>
                <td width="7%" style="padding :0px 3px 4px; font-family: poppins,arialuni;background-color: #d3d3d3;">
                    <strong>{{ trans('WebSite.created_by') }}</strong>
                </td>
                <td style="padding :0px 3px 2px; font-family: poppins,arialuni;">
                    {{ $data['created_by']}}

                </td>
                <td width="7%" style="padding :0px 3px 4px; font-family: poppins,arialuni;background-color: #d3d3d3;">
                    <strong>{{ trans('WebSite.created_on') }}</strong>
                </td>
                <td width="8.5%" style="padding :0px 3px 4px; font-family: poppins,arialuni;">
                     {{-- {{ date($data['dateFormat'],strtotime($data['user_info']['date_created'])) }} --}}
                     {{  date("d-M-Y",strtotime($data['teckpackINFO']['created_at']))}}
                </td>
                <td width="7%" style="padding :0px 3px 4px; font-family: poppins,arialuni;background-color: #d3d3d3;">
                    <strong>{{ trans('WebSite.modified_by') }}</strong>
                </td>
                <td style="padding :0px 3px 4px; font-family: poppins,arialuni;">
                    {{ $data['updated_by']!='' ? $data['updated_by'] : '-' }}
                </td>
                <td width="7%" style="padding :0px 3px 4px; font-family: poppins,arialuni;background-color: #d3d3d3;">
                    <strong>{{ trans('WebSite.modified_on') }}</strong>
                </td>
                <td width="8.5%" style="padding :0px 3px 4px; font-family: poppins,arialuni;">
                    {{ $data['updated_by']!=''? date("d-M-Y",strtotime($data['updated_date'])):"-"}}
                </td>
                <td width="6%" style="padding :0px 3px 4px; font-family: poppins,arialuni;background-color: #d3d3d3;">
                    <strong>{{ trans('WebSite.last_issue') }}</strong>
                </td>
                <td width="8.5%" style="padding :0px 3px 4px; font-family: poppins,arialuni;">
                    {{ $data['teckpackINFO']['is_publish']=='1'?date("d-M-Y",strtotime($data['teckpackINFO']['published_date'])):"-" }}
                </td>
                <td width="4%" style="padding :0px 3px 4px; font-family: poppins,arialuni;background-color: #d3d3d3;">
                    <strong>{{ trans('WebSite.page') }}</strong>
                </td>
                <td width="4%" style="padding :5px 0px 4px; font-family: poppins,arialuni;">
                </td>
            </tr>
        </table>
    </footer>
    <!--Boby Content Start-->
    <main>
        @if(count($data['GarmentSheet'])>0)
            <div style="padding: 0px; margin: 0px;text-align: left;font-size: 15px;font-weight:bold;">Garment Sheet</div>
            <div style="padding :5px; font-family: poppins,arialuni;text-align:center;over-flow:hidden;max-height:600px;">
            @foreach ($data['GarmentSheet'] as $garimg )
            @if($garimg['file_type']=="jpg" || $garimg['file_type']=="jpeg" || $garimg['file_type']=="png")
                <img src="{{ $garimg['filepath'] }}" style="padding: 0px;margin-top:0px;max-width:843px;max-height:570px;"/>
                <span class="page-break"></span>
                @elseif(isset($garimg['convert_images']))
                    @foreach ($garimg['convert_images'] as $conimgv )
                    <img src="{{ $conimgv }}" style="padding: 0px;margin-top:0px;max-height:550px;"/>
                    <span class="page-break"></span>
                    @endforeach
                @endif
            @endforeach
            </div>
        @endif

        @if(count($data['MeasurementChart'])>0)
            <div style="padding: 0px; margin: 0px;text-align: left;font-size: 15px;font-weight:bold;">Measurement Chart</div>
            <div style="padding :0px; font-family: poppins,arialuni;text-align:center;over-flow:hidden;max-height:600px;">
            @foreach ($data['MeasurementChart'] as $merimg )
                @if($merimg['convert_images'])
                    @foreach ($merimg['convert_images'] as $conimg )
                <img src="{{ $conimg }}" style="padding: 0px;margin-top:0px;max-height:550px;"/>
                    {{-- @if(stristr($merimg['orginalfilename'],'.pdf') || stristr($merimg['orginalfilename'],'.xls') || stristr($merimg['orginalfilename'],'.ai') || stristr($merimg['orginalfilename'],'.psd') || stristr($merimg['orginalfilename'],'.cdr'))
                        <br>Note : If the Measurement Chart is not clear, Please <a target="_blank" href="{{ config('app.public_url') }}api/download-ms-file?orginalfilename={{$merimg['orginalfilename']}}&filepath={{$merimg['org_filepath']}}">Click Here</a> to view.
                    @endif --}}
                    {{-- <span class="page-break"></span> --}}
                    <div style="clear: both;"></div>
                @endforeach
                @else
                @if($merimg['file_type']=="jpg" || $merimg['file_type']=="jpeg" || $merimg['file_type']=="png")
                <img src="{{ $merimg['filepath'] }}" style="padding: 0px;margin-top:0px;max-height:90%;"/>
                <span class="page-break"></span>
                @endif
                @endif


            @endforeach
            </div>
        @endif

        <div style="padding: 0px; margin: 0px;width: 100%;text-align: left;font-size: 14px;"><!--Material and Label--></div>
        {{-- <div style="padding :5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;border:1px solid #CCC;"> --}}
          @if(count($data['teckpack_details'])>0)
            @if($data['techpack_type']!='')
                <h3 style="padding: 0; margin:0">{{ $data['techpack_type'] }}</h3>
            @endif
            <table width="100%"  style="border-collapse: collapse;v-align:middel; font-family: poppins,arialuni;" cellspacing="1px" >

                @foreach ($data['teckpack_details'] as $tech)

                    @foreach ($tech['files'] as $filestech)
                    <tr>
                        <td style="padding :0px 5px; font-family: poppins,arialuni; background-color:#FFF;" colspan="2">

                            @if(isset($filestech['convert_images']) && count($filestech['convert_images'])>0)
                                @php $ic = 0; @endphp
                                @foreach ($filestech['convert_images'] as $cnfilestech)
                                    @php $ic++; @endphp
                                    <div><img src="{{ $cnfilestech}}"   alt="img" style="max-height: 80%;"/></div>
                                    @if($ic != count($filestech['convert_images']))
                                        <div class="page-break"></div>
                                    @endif
                                @endforeach
                            @elseif($filestech['file_type']=="jpg" || $filestech['file_type']=="jpeg" || $filestech['file_type']=="png")
                                <div><img src="{{  $filestech['filepath'] }}"   alt="img"  style="max-height: 80%;"/></div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td   style="padding :5px; font-family: poppins,arialuni; background-color:#FFF;" colspan="2">
                            {!! $tech['techpack_details'] !!}
                            <div style="clear: both;"></div>
                            @if($tech['updated_by']=='')
                                <p style="font-size:12px;text-align:left;font-style:italic;display:block"><i>{{ ucfirst($tech['created_by']) }} {{ date("d-M-Y H:i",strtotime($tech['create_date'])) }}</i></p>
                            @else
                                <p style="font-size:12px;text-align:left;font-style:italic;display:block"><i>{{ ucfirst($tech['updated_by']) }} {{ date("d-M-Y H:i",strtotime($tech['update_date'])) }}</i></p>
                            @endif
                        </td>
                    </tr>

                    @if(!empty($data['comments']))
                        @php $j = $created_id=0; $created_by_type =''; $float = $text_align='left'; $bgcolor='#f2f2f2'; @endphp
                        @foreach ($data['comments'] as $comts )
                            @if($j==0)
                                @php
                                    $created_id = $comts['created_id'];
                                    $created_by_type = $comts['created_by_type'];
                                @endphp
                            @endif
                            @if($created_id == $comts['created_id'] && $created_by_type == $comts['created_by_type'])
                                @php
                                    $float = $text_align='left';//right
                                    $bgcolor='#f5f8fd';
                                @endphp
                            @else
                                @php
                                    $float = $text_align='left';
                                    $bgcolor='#f8f8f8';
                                @endphp
                            @endif
                            <tr>
                                <td width="100%" class="noborder" colspan="2"  style="padding :10px 0 0 0; font-family: poppins,arialuni; text-align:{{ $text_align }}; font-size:12px; ">
                                    {{-- <div style="margin-bottom:20px; display:block;padding:5px; width:70%; float={{ $float }};"> --}}
                                        @if($comts['comment_type']=='Text' || $comts['comment_type']=='Audio' || $comts['comment_type']=='Video')
                                            <div style="clear : both;"></div>
                                            <div style="width: 100%;float:{{ $float }};">
                                                <div style="background-color:{{ $bgcolor }};padding:0px 0px 5px 5px;text-align:left; border:1px solid #bbcadd;" >
                                                    @if($comts['comment_type']=='Audio')
                                                        <img src="{{ public_path().'/images/audio.png' }}" width="{{config('constant.mail_icon_width')}}" style="display:inline-block; margin: 10px 5px 0 0; padding-top:10px;" />
                                                    @elseif($comts['comment_type']=='Video')
                                                        <img src="{{ public_path().'/images/video.png' }}" width="{{config('constant.mail_icon_width')}}" style="display:inline-block; margin: 10px 5px 0 0; padding-top:10px;" />
                                                    @endif
                                                    <strong>{!! $comts['comment_data'] !!}</strong>
                                                </div>
                                                @if($comts['is_translate']==1)
                                                <div style="background-color:{{ $bgcolor }};padding:0px 0px 5px 5px;text-align:left; border:1px solid #bbcadd; margin-top:10px;" >
                                                    <strong>{!! $comts['translated_data'] !!}</strong>
                                                </div>
                                                @endif
                                            </div>
                                            <div style="clear : both;"></div>
                                        @else
                                            <div style="clear : both;"></div>
                                            <div style="max-width: 100%;float:{{ $float }};">
                                                @if(isset($comts['convert_images']) && count($comts['convert_images'])>0)
                                                    <?php $k=0;?>
                                                    <div style="margin-top:20px;">
                                                        @foreach ($comts['convert_images'] as $cnfilestech)
                                                            <?php $k++;?>
                                                            {{-- <a href="#media{{ $comts['id'] }}{{ $k }}"><img src="{{ $cnfilestech}}" alt="Comment Image" width="100" style="margin-left: 5px; border:1px solid #bbcadd;"/></a> --}}
                                                            <div><img src="{{ $cnfilestech}}" alt="Comment Image" style="margin-left: 5px; border:1px solid #bbcadd;max-height: 85%;"/></div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    {{-- <a href="#media{{ $comts['id'] }}"><img src="{{ $comts['comment_data'] }}" alt="Comment Image" width="100px" style="margin-left: 5px; border:1px solid #bbcadd;"></a> --}}
                                                    <div><img src="{{ $comts['comment_data'] }}" alt="Comment Image" style="margin-left: 5px; border:1px solid #bbcadd;max-height: 85%;"></div>
                                                @endif
                                            </div>
                                        <div style="clear : both;"></div>
                                        @endif
                                        <span style="font-size: 10px"> <strong>{{ $comts['created_by'] }}</strong> {{ date("d-M-Y H:i",strtotime($comts['create_date'])) }}</span>
                                        <div style="clear : both;"></div>
                                    {{-- </div> --}}
                                </td>
                            </tr>
                            @php $j++; @endphp
                            <div style="clear : both;"></div>
                        @endforeach
                    @endif
                @endforeach
            {{-- </table> --}}

            @endif
        {{-- </div> --}}
        {{-- Display Images --}}
        <?php /*
        @if(count($data['teckpack_details'])>0)
            @foreach ($data['teckpack_details'] as $tech)
                @if(count($tech['files'])>0)
                    {{-- <div style="padding: 0px; margin: 0px;width: 100%;text-align: left;font-size: 14px;"> {!!  $tech['techpack_type'] !!}</div> --}}
                    @php $i=0; @endphp
                    @foreach ($tech['files'] as $key => $filestech)
                        <span class="page-break"></span>
                        @if(isset($filestech['convert_images']) && count($filestech['convert_images'])>0)
                            @php $ci=0; @endphp
                            @foreach ($filestech['convert_images'] as $pcntfilestech)
                                @php $ci++; @endphp
                                <div style="width:100%;justify-content: center; align-items: center;float:left;text-align:center;over-flow:hidden;max-height:600px;" id="media{{ $filestech['media_id'] }}{{ $ci }}">
                                    <img src="{{  $pcntfilestech }}"  style="padding: 5px;max-height: 94%;"  >
                                </div>
                                @if(count($filestech['convert_images'])!==$ci)
                                    <span class="page-break"></span>
                                @endif
                            @endforeach
                        @endif
                        @if($filestech['file_type']=="jpg" || $filestech['file_type']=="jpeg" || $filestech['file_type']=="png")
                            @php $i++; @endphp
                            <div style="width:100%;justify-content: center; align-items: center;float:left;text-align:center;over-flow:hidden;max-height:600px;" id="media{{ $filestech['media_id'] }}">
                                <img src="{{  $filestech['filepath'] }}" style="padding: 5px;max-height: 94%;"/>
                            </div>
                            {{-- @if(count($tech['files'])!==$i)
                                <span class="page-break"></span>
                            @endif --}}
                        @endif

                    @endforeach
                @endif
            @endforeach
        @endif
          */ ?>
        <?php /*
        @if(count($data['teckpack_details'])>0)
            @foreach ($data['teckpack_details'] as $tech)
            <!-- Convert Image Start-->

            <!-- Convert Image End-->
                @if(count($tech['files'])>0)
                    <span class="page-break"></span>
                    <div style="padding: 0px; margin: 0px;width: 100%;text-align: left;font-size: 14px;"> {!!  $tech['techpack_type'] !!}</div>
                    @if(count($tech['files'])>1)
                        <div style="padding :5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;over-flow:hidden;padding-top:10px;text-align:center;over-flow:hidden;max-height:600px;">
                    @else
                        <div style="padding :5px; font-family: poppins,arialuni,notosansjp,poppins-semibold;over-flow:hidden;text-align:center;over-flow:hidden;max-height:600px;">
                    @endif
                    {{-- @if(count($tech['files'])==1)
                    <img src="{{  $tech['files'][0]['filepath'] }}" style="padding: 10px;max-height:94%;"/>

                    @else --}}
                    @php $key1=0; @endphp
                    @foreach ($tech['files'] as $key => $filestech)
                        @php $key1++; @endphp
                        @if($key1%2==0 && $key1!=0)
                            <div style="width:50%;justify-content: center; align-items: center;float:right;text-align:center;over-flow:hidden;max-height:600px;">
                        @else
                            {{-- @if($key1%3==0)
                            <span class="page-break"></span>
                            @endif --}}
                            @if(count($tech['files'])==1)
                                <div style="width:100%;justify-content: center; align-items: center;float:left;text-align:center;over-flow:hidden;max-height:600px;">
                            @else
                                <div style="justify-content: center; align-items: center;float:left;text-align:center;over-flow:hidden;max-height:600px;">
                            @endif

                        @endif

                        @if(isset($filestech['convert_images']) && count($filestech['convert_images'])>0)
                            </div>
                            {{-- <span class="page-break"></span> --}}
                            </div> <div style="justify-content: center; align-items: center;float:left;text-align:center;over-flow:hidden;max-height:600px;">
                            @foreach ($filestech['convert_images'] as $pcntfilestech)
                                @php $key1++; @endphp
                                {{-- <img src="{{  $pcntfilestech }}"  @if($key1%2==0 && $key1!=0) style="padding: 5px;max-height: 88%;" @else style="padding: 5px;max-height: 50%;" @endif> --}}
                                <img src="{{  $pcntfilestech }}"  style="padding: 5px;max-height: 94%;">
                                <div style="clear: both;"></div>
                            @endforeach
                        @endif

                        @if($filestech['file_type']=="jpg" || $filestech['file_type']=="jpeg" || $filestech['file_type']=="png")
                            <img src="{{  $filestech['filepath'] }}" style="padding: 5px;max-height: 94%;"/>
                        @endif
                        </div>
                        {{-- @if($key1%2==0 && $key1!=0) --}}
                        <div style="clear: both;"></div>
                            {{-- <span class="page-break"></span> --}}
                        {{-- @endif --}}
                    @endforeach
                    {{-- @endif --}}
                    </div>
                    {{-- <span class="page-break"></span> --}}
                @endif
            @endforeach
        @endif
        */ ?>

        {{-- Display Comment Images --}}
        {{-- @if(!empty($data['comments']))
            @foreach ($data['comments'] as $comts )
                @if ($comts['comment_type']=='Image')
                    <span class="page-break"></span>
                    <div style="clear : both;"></div>
                        <div style="text-align:center;over-flow:hidden;max-height:600px;">
                            @if(isset($comts['convert_images']) && count($comts['convert_images'])>0)
                                <?php $ci=0;?>
                                @foreach ($comts['convert_images'] as $cnfilestech)
                                    @php $ci++; @endphp
                                    <div style="width:100%;justify-content: center; align-items: center;float:left;text-align:center;over-flow:hidden;max-height:600px;" id="media{{ $comts['id'] }}{{ $ci }}">
                                    <img src="{{ $cnfilestech}}"  alt="Comment Image"  style="padding: 5px;max-height: 94%;"/>
                                    </div>
                                    @if(count($comts['convert_images'])!==$ci)
                                        <span class="page-break"></span>
                                    @endif
                                @endforeach
                            @else
                                <div style="width:100%;justify-content: center; align-items: center;float:left;text-align:center;over-flow:hidden;max-height:600px;" id="media{{ $comts['id'] }}">
                                    <img src="{{ $comts['comment_data'] }}" alt="Comment Image" style="padding: 5px;max-height: 94%;" >
                                </div>
                            @endif
                        </div>
                    <div style="clear : both;"></div>
                @endif
            @endforeach
        @endif --}}
    </main>
    @if($ms_sheet_avil==0)
    <script type="text/php">
        if (isset($pdf)) {
           $font = $fontMetrics->getFont("poppins");
           $pdf->page_text(800, 544, "{PAGE_NUM}/{PAGE_COUNT}", $font, 9, array(0, 0, 0));
        }
    </script>
    @endif
    <?php //dd(); ?>
</body>
</html>
