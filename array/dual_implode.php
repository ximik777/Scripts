<?

    function dual_implode($array = array(), $glue = "", $glue_item = ","){
        return $glue_item == "," ?
            str_replace('],[', $glue, trim(json_encode($array), '[]')) : // 2 times faster
            implode($glue, array_map('implode', array_fill(0, count($array), $glue_item), $array));
    }

    $arr = array(
        array(1,2,3),
        array(4,5,6,7,8),
        array(9,10)
    );

    /*
    echo dual_implode($arr, ",\r\n", ", ");
    1, 2, 3,
    4, 5, 6, 7, 8,
    9, 10

    echo dual_implode($arr, ",\r\n");
    1,2,3,
    4,5,6,7,8,
    9,10
    */
