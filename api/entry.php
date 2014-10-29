<?
    $method = substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), 1);
    if($method && substr($method, -4) == '.xml'){
        $xml = true;
        $method = substr($method, 0, -4);
    }

    function Error($error_code = 0, $error_data = array()){
        switch ($error_code){
            case 1: $error_msg = "Не переданы обязательные параметры"; break;
            case 2: $error_msg = "Метод не существует"; break;
            /*...*/
            default: $error_msg = "Неизвестная ошибка"; $error_code = 0; break;
        }
        $data = array(
            'error'=>array(
                'error_code'=>$error_code,
                'error_msg'=>$error_msg
            )
        );
        if(is_array($error_data) && count($error_data)>0){
            $data['error'] = array_merge($data['error'], $error_data);
        }
        return $data;
    }

    function Response($response = array()){
        $data = array('response'=>$response);
        return $data;
    }

    function arr2xml($data, $i = -1){
        $result = array(); $i++;
        if($i===0) $result[] = '<?xml version="1.0" encoding="utf-8"?>';
        foreach($data as $k => $v){
            $result[] = str_repeat("\t", $i)."<".(is_numeric($k)?"item":$k)."".(is_array($v) && $v == array_values($v)?' list="true"':"").">".(is_array($v)?"\r\n".arr2xml($v, $i)."\r\n".str_repeat("\t", $i):$v)."</".(is_numeric($k)?"item":$k).">";
        }
        return implode("\r\n", $result);
    }

    switch ($method){
        case 'createMachine':

            $response = Response(array('machine_id'=>1));

        break;
        default:

            $response = Error(1);

        break;
    }

    if($xml){
        header("Content-type: application/xml; charset=utf-8");
        echo arr2xml($response);
    } else {
        header("Content-type: text/plain; charset=utf-8");
        echo json_encode($response);
    }

