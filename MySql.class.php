/*

  MySql db class


*/

class db_mysql
{
  var $db_hadnle;
  var $db_name;
  var $sql;
  var $error;

  function __construct($db_host = null, $db_user = null, $db_pass = null, $db_name = null, $charset = 'utf8')
  {
    $this->db_host = $db_host ? $db_host : DB_HOST;
    $this->db_user = $db_user ? $db_user : DB_USER;
    $this->db_pass = $db_pass ? $db_pass : DB_PASS;
    $this->db_name = $db_name ? $db_name : DB_NAME;

    if(!$this->db_hadnle = @mysql_connect($this->db_host,$this->db_user,$this->db_pass))
    {
      $this->error = mysql_errno($this->db_hadnle).' '.mysql_error($this->db_hadnle);
      $this->db_hadnle = false;
      return false;
    }

    if(!@mysql_select_db($this->db_name,$this->db_hadnle))
    {
      $this->error = mysql_errno($this->db_hadnle).' '.mysql_error($this->db_hadnle);
      trigger_error($this->error, ERROR);
      $this->db_hadnle = false;
      return false;
    }
    else
    {
      mysql_query("SET NAMES '".$charset."';");
    }
    return true;
  }

  function query_replase($sql,$repl_arr=null)
  {
    if($repl_arr === null || $repl_arr == array())
    {
      return $sql;
    }
    else
    {
      $sql_out = '';
      $start = 0;
      preg_match_all('/([^\\\\]{1}\\$)/',$sql,$math,PREG_OFFSET_CAPTURE);

      foreach ($math[1] as $key=>$val)
      {
        $sql_out .= substr($sql, $start, $val[1]-$start+1);

        if(is_array($repl_arr))
        {
          $sql_out.="'".addslashes($repl_arr[$key])."'";
        }
        elseif($key == 0)
        {
          $sql_out .= "'".addslashes($repl_arr)."'";
        }

        $start = $val[1]+2;
      }

      $sql_out .= substr($sql,$start);
      return str_replace('\\$', '$', $sql_out);
    }
  }

  function query($sql, $repl_arr = null)
  {
    $sql = $this->query_replase($sql, $repl_arr);
    $this->sql = $sql;

    if(!$res = mysql_query($sql, $this->db_hadnle))
    {
      $this->error = $sql.' '.mysql_error($this->db_hadnle).' '.mysql_errno($this->db_hadnle);
      return false;
    }
    return $res;
  }

  function query_insert($sql,$repl_arr=null)
  {
    $this->query($sql,$repl_arr);
    return mysql_insert_id($this->db_hadnle);
  }

  function get_affected_rows($sql,$repl_arr=null)
  {
    $this->query($sql,$repl_arr);
    return mysql_affected_rows($this->db_hadnle);
  }

  function get_value_query($sql, $repl_arr = null)
  {
    if(!$res=$this->query($sql,$repl_arr))
    {
      return false;
    }
    if(mysql_num_rows($res) & mysql_num_fields($res))
    {
      return mysql_result($res,0,0);
    }
    else
    {
      return false;
    }
  }

  function get_array_list($sql,$repl_arr=null)
  {
    if(!$res=$this->query($sql,$repl_arr)) return false;
    $array=array();
    while ($row = mysql_fetch_assoc($res))
    {
      $array[]=$row;
    }
    return $array;
  }

  function getKeyValArray($sql, $replArr = null)
  {
    if(!$res = $this->query($sql, $replArr)) return false;
    $array=array();
    while ($row = mysql_fetch_array($res))
    {
      $array[$row[0]]=$row[1];
    }
    return $array;
  }


  function get_one_line_assoc($sql,$repl_arr=null)
  {
    if(!$res=$this->query($sql,$repl_arr)) return false;
    return mysql_fetch_assoc($res);
  }

  function exec_query($query)
  {
    $prev = 0; $i=0; $arr=preg_split('/;[   ]*(\n|\r)/',trim($query));
    foreach ($arr as $a)
    {
      if(!$this->query($a))
      {
        return false;
      }

      $i++;
    }
    return $i;
  }

  function get_assoc_column($sql,$repl_arr=null)
  {
    if(!$res = $this->query($sql,$repl_arr)) return false;
    $arr = array();
    while ($row = mysql_fetch_array($res))
    {
      $arr[] = $row[0];
    }
    return $arr;
  }

  function get_assoc_column1($sql,$repl_arr=null)
  {
    if(!$res = $this->query($sql,$repl_arr)) return false;
    $arr = array();
    while ($row = mysql_fetch_assoc($res))
    {
      $id = array_shift($row);
      $arr[$id] = $row;
    }
    return $arr;
  }

  function begin()
  {
    return $this->transaction_start();
  }

  function transaction_start()
  {
    return $this->query('START TRANSACTION');
  }

  function commit()
  {
    return $this->query('COMMIT');
  }

  function rollback()
  {
    return $this->query('ROLLBACK');
  }

}
