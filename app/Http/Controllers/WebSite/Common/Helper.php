<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
class Helper extends Controller
{

    public static function getIndexvalue($needle, $haystack, $array){
        foreach($array as $key => $value){
            if(is_array($value) && $value['size_id'] == $needle && $value['color_id'] == $haystack)
                return $key;
        }
        return 0;
    }

    public static function translate($text,$datas){
        //return $text; //die();
        $do_translate = $datas['translate'] ?? 0;
        $to_lan = $datas['lang']=='jp'?'ja_JP':'en_GB';
        $from_lan = $datas['lang']=='en'?'ja_JP':'en_GB';
       // $text = urlencode($text);
        if($do_translate==1){
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api-b2b.backenster.com/b1/api/v3/translate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
            "from": "'.$from_lan.'",
            "to": "'.$to_lan.'",
            "data": "'.$text.'",
            "platform": "api",
            "translateMode": "html"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: a_sRQqC1wwFz9srVK8NjxycyfvIqd2fX406IZqf1ZED1rYikQTJxXqppIjl0PZzr7xianGjhYKzWc4xXD5',
                'Cookie: INGRESSCOOKIE=1693386266.371.1101.134316|36ad97186a8a117df027cda122686747'
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            //echo $response; exit;
            $res = json_decode($response, true);
            return $res['result'];
        }else{
            return $text;
        }
    }

    public static function translate_old($text,$datas){

        $do_translate = $datas['translate'] ?? 0;
        $to_lan = $datas['lang']=='jp'?'ja':'en';
        $from_lan = $datas['lang']=='en'?'ja':'en';
        if($do_translate==1){
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://text-translator2.p.rapidapi.com/translate",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "source_language=".$from_lan."&target_language=".$to_lan."&text=".urlencode($text),
                CURLOPT_HTTPHEADER => [
                    "X-RapidAPI-Host: text-translator2.p.rapidapi.com",
                    "X-RapidAPI-Key: de9c167f1bmsh89040a071975c9cp15e2f9jsn064ff1ee9800",
                    "content-type: application/x-www-form-urlencoded"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                return $text;
            } else {
                //echo $response; exit;
                $res = json_decode($response, true);
                return $res['data']['translatedText'];
            }
        }else{
            return $text;
        }



    }

    public static function speechToText($file_path){
       // $file_name = pathinfo($file_path, PATHINFO_FILENAME); // Get your audio file name
        $file_extension = pathinfo($file_path, PATHINFO_EXTENSION); // Get your audio file extension

        $audio_file = new \CurlFile($file_path, 'audio/'.$file_extension); // Create a CurlFile object

        $data = [
            'audio' => $audio_file,
            // Then you can pass any parameters you want. Please see: https://docs.gladia.io/api-reference/pre-recorded-flow
            'toggle_diarization' => true,
        ];
        //dd($data);
        $headers = [
            'x-gladia-key: 7442eb2a-fbd6-4923-849a-fb4ef32f95fa', // Replace with your Gladia Token
            'accept: application/json', // Accept json as a response, but we are sending a Multipart FormData
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.gladia.io/audio/text/audio-transcription/');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //echo "- Sending request to Gladia API...\n";
        $response = curl_exec($curl);
        //dd($response);
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status_code == 200) {
            // echo "- Request successful\n";
            $result = json_decode($response, true);
            return $result;
        } else {
            //echo "- Request failed\n";
            $error = json_decode($response, true);
            return $error;
            //print_r($error);
        }

        curl_close($curl);
    }



}
