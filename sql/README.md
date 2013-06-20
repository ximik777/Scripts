  
PHP:

  MySql init class:
  
  $db = new db_mysql('db_host', 'db_user', 'db_pass', 'db_name');
  
  PgSql init class:
  
  $db = new db_pgsql('db_host', 'db_user', 'db_pass', 'db_name');

  Sqlite init class:
  
  $db = new db_lite('db_name');
  
JavaScript:

  MySql, PgSql class for Yate
  No init.

Methods:

  query - return false or true
  
  query_insert - return false or last inser id
  
  get_affected_rows - return false or affected rows
  
  get_value_query - return false or first value
  
  get_array_list - return false or array
  
  getKeyValArray - return false or array 
  
  get_one_line_assoc - return false or array 
  
  get_assoc_column - return false or array
  
  get_assoc_column1 - return false or array
  
  

Example:


  Example table:
  
  id | fnama | lname | group | pass | 
  
  1  | John  | Smit  |   0   | qwe  |
  
  2  | Tom   | Bink  |   1   | ytr  |
  
  3  | Mike  | Depp  |   1   | odi  |
  
