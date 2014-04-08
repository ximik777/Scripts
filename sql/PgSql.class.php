<?
/*
  PgSql db class
  Author: Grigoriy Stratov
  Docs: https://github.com/ximik777/Scripts/blob/master/sql/README.md

*/

class db_pgsql
{ 
	var $db_hadnle; 
	var $sql; 
	var $error;
	
	function __construct($db_host = '', $db_name = '', $db_user = '', $db_pass = '') 
	{
		if(!$this->db_hadnle = pg_connect('host='.$db_host.' dbname='.$db_name.' user='.$db_user.' password='.$db_pass)) 
		{ 
			$this->error = pg_last_error($this->db_hadnle); 
			$this->db_hadnle = false; 
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
		
		if(!$res = pg_query($this->db_hadnle, $sql)) 
		{ 
			$this->error = $this->sql.' '.pg_last_error($this->db_hadnle).' '.pg_last_error($this->db_hadnle);
			return false; 
		} 
		return $res; 
	}
		
	function query_insert($sql,$repl_arr=null) 
	{ 
		$this->query($sql,$repl_arr); 
		return pg_last_oid($this->db_hadnle); 
	}	
	
	function query_affected_rows($sql,$repl_arr=null) 
	{ 
		$this->query($sql,$repl_arr); 
		return pg_affected_rows($this->db_hadnle); 
	} 
		
	function get_value_query($sql, $repl_arr = null) 
	{ 
		if(!$res=$this->query($sql,$repl_arr)) 
		{ 
			return false; 
		} 
		if(pg_num_rows($res) & pg_num_fields($res)) 
		{ 
			return pg_fetch_result($res,0,0); 
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
		while ($row = pg_fetch_assoc($res)) 
		{ 
			$array[]=$row; 
		} 
		return $array; 
	} 	
		
	function getKeyValArray($sql, $replArr = null) 
	{ 
		if(!$res = $this->query($sql, $replArr)) return false; 
		$array=array(); 
		while ($row = pg_fetch_array($res)) 
		{ 
			$array[$row[0]]=$row[1]; 
		} 
		return $array; 
	} 
	
	function get_affected_rows($sql,$repl_arr=null) 
	{ 
		$this->query($sql,$repl_arr); 
		return pg_affected_rows($this->db_hadnle); 
	} 
		
	function get_one_line_assoc($sql,$repl_arr=null) 
	{ 
		if(!$res=$this->query($sql,$repl_arr)) 
		return false; return pg_fetch_assoc($res); 
	} 
		
	function exec_query($query) 
	{ 
		$prev = 0; 
		$i=0; 
		$arr=preg_split('/;[ 	]*(\n|\r)/',trim($query)); 
		foreach ($arr as $a) 
		{ 
			if(!$this->query($a)) 
			{ 
				return 0; 
			} 
			$i++; 
		} 
		return $i; 
	} 
		
	function get_assoc_column ($sql,$repl_arr=null) 
	{ 
		if(!$res = $this->query($sql,$repl_arr)) return false; 
		$arr = array(); 
		while ($row = pg_fetch_array($res)) 
		{ 
			$arr[] = $row[0]; 
		} 
		return $arr; 
	}
	
	function get_assoc_column1($sql,$repl_arr=null)
  {
    if(!$res = $this->query($sql,$repl_arr)) return false;
    $arr = array();
    while ($row = pg_fetch_array($res))
    {
      $id = array_shift($row);
      $arr[$id] = $row;
    }
    return $arr;
  } 
}
?>
