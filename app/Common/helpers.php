<?php

use App\Common\Encryption;

function webDecrypt($str){
    try {
        //return $str;
        $nonceValue = 'ITOHENDMS';
        $decrypted = \App\Common\Encryption::decrypt($str, $nonceValue);
        return is_string($decrypted) ? $decrypted : (string) $decrypted;
    } catch (\Exception $e) {
        return $str; // or log the error
    }
}
