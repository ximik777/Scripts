  
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

  query - return false or resource(true)
  
  query_insert - return false or last inser id
  
  get_affected_rows - return false or affected rows
  
  get_value_query - return false or first value
  
  get_array_list - return false or array
  
  getKeyValArray - return false or array 
  
  get_one_line_assoc - return false or array 
  
  get_assoc_column - return false or array
  
  get_assoc_column1 - return false or array
  
  

Example:


  Example table `users`:
  
  id | fnama | lname | group | pass | 
  
  1  | John  | Smit  |   0   | qwe  |
  
  2  | Tom   | Bink  |   1   | ytr  |
  
  3  | Mike  | Depp  |   1   | odi  |
  

  Example query:
  
  query [php]:
  
  
  $q = $db->query('UPDATE `users` SET pass=$ WHERE id=$', array($pass, $id));
  
  $q == false or true;
  
  query [js]:
  
  var q = db.query('UPDATE `users` SET pass=$ WHERE id=$', [pass, id]);
  
  q == false or resource(true);
  
  
  query_insert[php]:
  
  $q = $db->query_insert('INSERT INTO `users` (`fname,`lname`,`group`,`pass`) VALUES($,$,$,$)', array('Grag','Sher','1','123'));
  
  $q == false or 4;
  
  query_insert[js]:
  
  var q = db.query_insert('INSERT INTO `users` (`fname,`lname`,`group`,`pass`) VALUES($,$,$,$)', ['Grag','Sher','1','123']);
  
  q == false or 4;
  
  
  get_affected_rows[php]:
  
  $q = $db->get_affected_rows('UPDATE `users` SET `group`=$ WHERE group=$', array('1','0'));
  
  $q == false or 2;
  
  get_affected_rows[js]:
  
  var q = db.get_affected_rows('UPDATE `users` SET `group`=$ WHERE group=$', ['1','0']);
  
  q == false or 2;
  
  
  get_value_query[php]:
  
  $q = $db->get_value_query('SELECT pass FROM `users` WHERE `id`=$', '1');
  
  $q == false or qwe;
  
  get_value_query[js]:
  
  var q = db.get_value_query('SELECT pass FROM `users` WHERE `id`=$', '1');
  
  q == false or qwe;
  
  
  get_array_list[php]:
  
  $q = $db->get_array_list('SELECT `fname`, `lname`, `group` FROM `users`');
  
  $q == false or array(
  
    array('fname'=>'John', 'lname'=>'Smit', 'group'=>'0'),
    
    array('fname'=>'Tom', 'lname'=>'Bink', 'group'=>'1'),
    
    array('fname'=>'Mike', 'lname'=>'Depp', 'group'=>'1'),
  
  );
  
  get_array_list[js]:
  
  var q = db.get_array_list('SELECT `fname`, `lname`, `group` FROM `users`');
  
  q == false or [
  
    ['fname'=>'John', 'lname'=>'Smit', 'group'=>'0'],
    
    ['fname'=>'Tom', 'lname'=>'Bink', 'group'=>'1'],
    
    ['fname'=>'Mike', 'lname'=>'Depp', 'group'=>'1'],
  
  ];
  
  
  
  
  
  
  
  
  
  
  
  
  

