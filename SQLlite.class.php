/*
  SQLite db class
  Author: Grigoriy Stratov
*/

class db_lite
{
  var $handle;
  var $error;
  var $sql;
  var $ect = 'sqlite';

  function __construct($db_name = false)
  {
    if(!$this->handle = new SQLite3($db_name.'.'.$this->ect))
    {
      $this->error = $this->handle->lastErrorCode().' '.$this->handle->lastErrorMsg();
      return false;
    }
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
    $this->sql = $this->query_replase($sql, $repl_arr);

    if(!$res = $this->handle->query($this->sql))
    {
      $this->error = $this->sql.' '.$this->handle->lastErrorCode().' '.$this->handle->lastErrorMsg();
      return false;
    }
    return $res;
  }

  function query_insert($sql,$repl_arr=null)
  {
    $this->query($sql,$repl_arr);
    return $this->handle->lastInsertRowID();
  }

  function get_value_query($sql, $repl_arr = null)
  {
    if(!$res = $this->handle->querySingle($this->query_replase($sql, $repl_arr)))
    {
      return false;
    }
    return $res;
  }

  function get_array_list($sql,$repl_arr=null)
  {
    if(!$res=$this->query($sql,$repl_arr)) return false;
    $array=array();
    while($row = $res->fetchArray(SQLITE3_ASSOC))
    {
      $array[]=$row;
    }
    return $array;
  }

  function getKeyValArray($sql, $replArr = null)
  {
    if(!$res = $this->query($sql, $replArr)) return false;
    $array=array();
    while ($row = $res->fetchArray())
    {
      $array[$row[0]]=$row[1];
    }
    return $array;
  }


  function get_one_line_assoc($sql,$repl_arr=null)
  {
    if(!$res = $this->handle->querySingle($this->query_replase($sql, $repl_arr), true)) return false;
    return $res;
  }

  function get_assoc_column($sql,$repl_arr=null)
  {
    if(!$res = $this->query($sql,$repl_arr)) return false;
    $arr = array();
    while ($row = $res->fetchArray())
    {
      $arr[] = $row[0];
    }
    return $arr;
  }

  function get_assoc_column1($sql,$repl_arr=null)
  {
    if(!$res = $this->query($sql,$repl_arr)) return false;
    $arr = array();
    while ($row = $res->fetchArray(SQLITE3_ASSOC))
    {
      $id = array_shift($row);
      $arr[$id] = $row;
    }
    return $arr;
  }

  function get_affected_rows($sql,$repl_arr=null)
  {
    if(!$this->query($sql,$repl_arr)) return false;
    return $this->handle->changes();
  }

}
