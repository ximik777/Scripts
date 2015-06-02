<?
// v1.2.1
class mysqli_client {
    var $handle;
    var $sql;
    var $error;
    var $errno;

    var $config = array(
        'host' => 'localhost',
        'user' => '',
        'pass' => '',
        'name' => '',
        'port' => '3306',
        'charset' => 'utf8',
        'persistent' => false,
        'autocommit' => true
    );

    function __construct($config = array()){
        $this->config = array_merge($this->config,$config);

        if($this->config['persistent'] === true){
            $this->config['host'] = 'p:'.$this->config['host'];
        }

        $this->handle = @new mysqli($this->config['host'], $this->config['user'], $this->config['pass'], $this->config['name'], $this->config['port']);

        if ($this->handle->connect_errno) {
            $this->errno     = $this->handle->connect_errno;
            $this->error     = $this->errno . ' ' . $this->handle->connect_error;
            $this->handle = false;
            return false;
        }

        if($this->config['autocommit'] === true){
            $this->handle->autocommit(true);
        }

        if($this->config['charset'] !== false){
            $this->handle->query("SET NAMES {$this->config['charset']}");
        }
        return true;
    }

    function query_replace($sql, $data_arr = null) {
        if ($data_arr === null || $data_arr == array()) {
            return $sql;
        } else {
            $sql_out = '';
            $start   = 0;
            preg_match_all('/([^\\\\]{1}\\$)/', $sql, $math, PREG_OFFSET_CAPTURE);

            foreach ($math[1] as $key => $val) {
                $sql_out .= substr($sql, $start, $val[1] - $start + 1);

                if (is_array($data_arr)) {
                    $sql_out .= is_null($data_arr[$key]) ? 'NULL' : "'" . addslashes($data_arr[$key]) . "'";
                } elseif ($key == 0) {
                    $sql_out .= is_null($data_arr) ? 'NULL' : "'" . addslashes($data_arr) . "'";
                }
                $start = $val[1] + 2;
            }

            $sql_out .= substr($sql, $start);
            return str_replace('\\$', '$', $sql_out);
        }
    }

    function query($sql, $data_arr = null) {
        $this->sql = $this->query_replace($sql, $data_arr);
        if (!$res = $this->handle->query($this->sql)) {
            $this->errno = $this->handle->errno;
            $this->error = $this->sql . ' ' . $this->errno . ' ' . $this->handle->error;
            return false;
        }
        return $res;
    }

    function query_insert($sql, $data_arr = null){
        $this->query($sql, $data_arr);
        return $this->handle->insert_id;
    }

    function query_affected_rows($sql, $data_arr = null){
        $this->query($sql, $data_arr);
        return $this->handle->affected_rows;
    }

    function get_value_query($sql, $data_arr = null){
        if (!$res = $this->query($sql, $data_arr)) return false;
        if ($res->num_rows & $res->field_count) {
            $res = $res->fetch_array();
            return $res[0];
        } else {
            return false;
        }
    }

    function get_array_list($sql, $data_arr = null){
        if (!$res = $this->query($sql, $data_arr)) return false;
        $array = array();
        while ($row = $res->fetch_assoc()) {
            $array[] = $row;
        }
        return $array;
    }

    function get_key_val_array($sql, $data_arr = null) {
        if (!$res = $this->query($sql, $data_arr)) return false;
        $array = array();
        while ($row = $res->fetch_array()) {
            $array[$row[0]] = $row[1];
        }
        return $array;
    }

    function get_affected_rows($sql, $data_arr = null){
        $this->query($sql, $data_arr);
        return $this->handle->affected_rows;
    }

    function get_one_line_assoc($sql, $data_arr = null){
        if (!$res = $this->query($sql, $data_arr))
            return false;
        return $res->fetch_assoc();
    }

    function exec_query($query, $transaction = false) {
        $i    = 0;
        $arr  = preg_split('/;[ 	]*(\n|\r)/', trim($query));
        if($transaction) $this->transaction_start();
        foreach ($arr as $a) {
            if (!$this->query($a)) {
                if($transaction) {
                    $this->rollback();
                }
                return 0;
            }
            $i++;
        }
        if($transaction) $this->commit();
        return $i;
    }

    function get_assoc_column($sql, $data_arr = null){
        if (!$res = $this->query($sql, $data_arr)) return false;
        $arr = array();
        while ($row = $res->fetch_array()) {
            $arr[] = $row[0];
        }
        return $arr;
    }

    function get_assoc_column_id($sql, $data_arr = null){
        if (!$res = $this->query($sql, $data_arr)) return false;
        $arr = array();
        while ($row = $res->fetch_assoc()) {
            $id = array_shift($row);
            $arr[$id] = $row;
        }
        return $arr;
    }

    function begin() {
        return $this->transaction_start();
    }

    function transaction_start() {
        return $this->query('START TRANSACTION');
    }

    function commit(){
        return $this->query('COMMIT');
    }

    function rollback() {
        return $this->query('ROLLBACK');
    }

    public function __destruct(){
        if($this->handle)
            $this->handle->close();
    }
}
