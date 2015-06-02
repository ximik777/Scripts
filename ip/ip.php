<?php

function ip($ip_param_name = null){
    if(is_string($ip_param_name) && isset($_SERVER[$ip_param_name])){
        $ip = $_SERVER[$ip_param_name];
    } else {
        if (isset($_SERVER['HTTP_X_REAL_IP']) && !empty($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (strpos($ip, ',') !== false) {
                $ip = array_pop(explode(',', $ip));
            }
        } elseif (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = "";
        }
    }

    if(!filter_var($ip, FILTER_VALIDATE_IP)){
        throw new Exception("Can't detect IP");
    }

    return $ip;
}

function is_local_ip($ip = false){
    $long_ip = ip2long($ip);

    if(($long_ip & 4278190080) == 2130706432) return true;          // 127.0.0.0/8
    elseif (($long_ip & 4278190080) == 167772160) return true;      // 10.0.0.0/8
    elseif (($long_ip & 4293918720) == 2886729728) return true;     // 172.16.0.0/12
    elseif (($long_ip & 4294901760) == 3232235520) return true;     // 192.168.0.0/16
    return false;
}
