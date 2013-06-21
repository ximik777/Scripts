PHP:<br />
MySql init class:<br />
<br />
$db = new db_mysql('db_host', 'db_user', 'db_pass', 'db_name');<br />
<br />
PgSql init class:<br />
$db = new db_pgsql('db_host', 'db_user', 'db_pass', 'db_name');<br />
<br />
Sqlite init class:<br />
$db = new db_lite('db_name');<br />
<br />
JavaScript:<br />
MySql, PgSql class for Yate No init.<br />
<br />
Methods:<br />
<br />
<ol>
<li><b>query</b> - return false or resource(true)</li>
<li><b>query_insert</b> - return false or last inser id</li>
<li><b>get_affected_rows</b> - return false or affected rows</li>
<li><b>get_value_query</b> - return false or first value</li>
<li><b>get_array_list</b> - return false or array</li>
<li><b>getKeyValArray</b> - return false or array</li>
<li><b>get_one_line_assoc</b> - return false or array</li>
<li><b>get_assoc_column</b> - return false or array</li>
<li><b>get_assoc_column1</b> - return false or array</li>
</ol>
<br />
Example:<br />
<br />
Example table users:<br />


<table>
  <tr>
    <td>id</td>
    <td>fname</td>
    <td>lname</td>
    <td>group</td>
    <td>login</td>
    <td>pass</td>
  </tr>
  <tr>
    <td>1</td>
    <td>John</td>
    <td>Smit</td>
    <td>0</td>
    <td>test1</td>
    <td>qwe</td>
  </tr>
  <tr>
    <td>2</td>
    <td>Tom</td>
    <td>Bink</td>
    <td>1</td>
    <td>test2</td>
    <td>ytr</td>
  </tr>
  <tr>
    <td>3</td>
    <td>Mike</td>
    <td>Depp</td>
    <td>1</td>
    <td>test3</td>
    <td>odi</td>
  </tr>
</table>
<br />
Example query:<br />
<br />

<b>query [php]:</b><br /><br />

<pre><code>
$q = $db->query('UPDATE `users` SET `pass`=$ WHERE `id`=$', array($pass, $id));
$q == false or resource(true);
</code></pre>
<br />

<b>query [js]:</b><br /><br />

<pre><code>
var q = db.query('UPDATE `users` SET `pass`=$ WHERE `id`=$', [pass, id]);
q == false or true;
</code></pre>
<br />

<b>query_insert[php]:</b><br /><br />
<pre><code>
$q = $db->query_insert('INSERT INTO `users` (`fname`,`lname`,`group`,`pass`) VALUES($,$,$,$)', array('Grag','Sher','1','123'));
$q == false or 4;
</code></pre>
<br />

<b>query_insert[js]:</b><br /><br />
<pre><code>
var q = db.query_insert('INSERT INTO `users` (`fname`,`lname`,`group`,`pass`) VALUES($,$,$,$)', ['Grag','Sher','1','123']);
q == false or 4;
</code></pre>
<br />

<b>get_affected_rows[php]:</b><br /><br />
<pre><code>
$q = $db->get_affected_rows('UPDATE `users` SET `group`=$ WHERE `group`=$', array('1','0'));
$q == false or 2;
</code></pre>
<br />

<b>get_affected_rows[js]:</b><br /><br />
<pre><code>
var q = db.get_affected_rows('UPDATE `users` SET `group`=$ WHERE `group`=$', ['1','0']);
q == false or 2;
</code></pre>
<br />

<b>get_value_query[php]:</b><br /><br />
<pre><code>
$q = $db->get_value_query('SELECT `pass` FROM `users` WHERE `id`=$', '1');
$q == false or qwe;
</code></pre>

<br />
<b>get_value_query[js]:</b><br /><br />
<pre><code>
var q = db.get_value_query('SELECT `pass` FROM `users` WHERE `id`=$', '1');
q == false or qwe;
</code></pre>
<br />

<b>get_array_list[php]:</b><br /><br />
<pre><code>
$q = $db->get_array_list('SELECT `fname`, `lname`, `group` FROM `users`');
$q == false or array(
  array('fname'=>'John', 'lname'=>'Smit', 'group'=>'0'),
  array('fname'=>'Tom', 'lname'=>'Bink', 'group'=>'1'),
  array('fname'=>'Mike', 'lname'=>'Depp', 'group'=>'1')
);
</code></pre>

<br />
<b>get_array_list[js]:</b><br /><br />
<pre><code>
var q = db.get_array_list('SELECT `fname`, `lname`, `grou` FROM `users`');
q == false or [
  ['fname'=>'John', 'lname'=>'Smit', 'group'=>'0'],
  ['fname'=>'Tom', 'lname'=>'Bink', 'group'=>'1'],
  ['fname'=>'Mike', 'lname'=>'Depp', 'group'=>'1']
];
</code></pre>

<br />
<b>getKeyValArray[php]:</b><br /><br />
<pre><code>
$q = $db->getKeyValArray('SELECT `id`, `login` FROM `users`');
$q == false or array(
  '1' => 'test1',
  '2' => 'test2',
  '3' => 'test3'
);
</code></pre>
<br />

<b>getKeyValArray[js]:</b><br />
<br />
<pre><code>
var q = db.getKeyValArray('SELECT `id`, `login` FROM `users`');
q == false or {
  '1' : 'test1',
  '2' : 'test2',
  '3' : 'test3'
};
</code></pre>
<br />
<b>get_one_line_assoc[php]:</b><br /><br />
<pre><code>
$q = $db->get_one_line_assoc('SELECT * FROM `users` WHERE `id`=$', '2');
$q == false or array(
  'id' => '2',
  'fname' => 'Tom',
  'lname' => 'Bink',
  'group' => '1',
  'login' => 'test2',
  'pass' => 'ytr'
);
</code></pre>
<br />
<b>get_one_line_assoc[js]:</b><br />
<br />
<pre><code>
var q = db.get_one_line_assoc('SELECT * FROM `users` WHERE `id`=$', '2');
q == false or {
  'id' : '2',
  'fname' : 'Tom',
  'lname' : 'Bink',
  'group' : '1',
  'login' : 'test2',
  'pass' : 'ytr'
};
</code></pre>

<br />
<b>get_assoc_column[php]:</b><br /><br />
<pre><code>
$q = $db->get_assoc_column('SELECT `login` FROM `users`');
$q == false or array(
  'test1',
	'test2',
	'test3'
);
</code></pre>
<br />
<b>get_assoc_column[js]:</b><br />
<br />
<pre><code>
var q = db.get_assoc_column('SELECT `login` FROM `users`');
q == false or [
  'test1',
	'test2',
	'test3'
];
</code></pre>

<br />
<b>get_assoc_column1[php]:</b><br /><br />
key array first value
<pre><code>
$q = $db->get_assoc_column1('SELECT * FROM `users`');
$q == false or array(
  '1' => array('fname'=>'John', 'lname'=>'Smit', 'group'=>'0', 'login'=>'test1', 'pass'=>'qwe'),
  '2' => array('fname'=>'Tom', 'lname'=>'Bink', 'group'=>'1', 'login'=>'test2', 'pass'=>'ytr'),
  '3' => array('fname'=>'Mike', 'lname'=>'Depp', 'group'=>'1', 'login'=>'test3', 'pass'=>'odi')
);
</code></pre>
<br />
<b>get_assoc_column1[js]:</b><br />
<br />
key object first value
<pre><code>
var q = db.get_assoc_column1('SELECT * FROM `users`');
q == false or {
  '1' : {'fname':'John', 'lname':'Smit', 'group':'0', 'login':'test1', 'pass':'qwe'},
  '2' : {'fname':'Tom', 'lname':'Bink', 'group':'1', 'login':'test2', 'pass':'ytr'},
  '3' : {'fname':'Mike', 'lname':'Depp', 'group':'1', 'login':'test3', 'pass':'odi'}
};
</code></pre>
