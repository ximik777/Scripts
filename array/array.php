<?

    function dual_implode($root = "", $item = "", $array){
        return implode($root, array_map('implode', array_fill(0, count($array), $item), $array));
    }

    /* Result
    $arr = array(
        array(1,2,3),
        array(4,5,6,7,8),
        array(9,10)
    );

    echo dual_implode(",\r\n", ", ", $arr);

    1, 2, 3,
    4, 5, 6, 7, 8,
    9, 10
    */
