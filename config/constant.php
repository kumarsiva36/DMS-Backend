<?php

    return [
        'dashboard_modules' => ['1'=>'Production Status','3'=>'Task Status','4'=>'Top 5 Delayed Task','2'=>'Top 5 Delayed Production',
                                /* '7'=>'Notifications','6'=>'Ongoing List', */'5'=>'Order Status'],
        'dashboard_modules_mobile' => ['4'=>'Top 5 Delayed Task','2'=>'Top 5 Delayed Production','7'=>'Notifications','6'=>'Ongoing List','5'=>'Order Status'],
        'rolesAndPermissions' =>['Manager'=>[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45],
                                'Merchandiser'=>[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,40,41,42,43,44],
                                'Supervisor'=>[1,2,3,4,5,6,7,8,9,10,11,12,13,14,16,17,18,19,21,22,23,24,25,26,27,28,29,30,32,33,35,36,40,44],
                                'Staff'=>[17,26,27,28,40],
                                'Guest'=>[19,26,28,40]],
        'bom_units' => ['1'=>'Nos','2'=>'Cones','3'=>'Meters','4'=>'Rolls'],
        'pdf_icon_width' => '20',
        'mail_icon_width' => '20',
        'plan_storage_size_validation' => '1',
        'plan_storage_free_mb' => '10',
        'plan_storage_free_mb_type' => '1', /* 1-Percentage , 2-Flat */
        'task_inprogress_percentage' => '1', /* 1-Enable , 0-Disable */
        'techpack_comments_audio_enable' => '1', /* 1-Enable , 0-Disable */
        'techpack_comments_video_enable' => '1', /* 1-Enable , 0-Disable */
        'order_comments_enable' => '1', /* 1-Enable , 0-Disable */
    ];
