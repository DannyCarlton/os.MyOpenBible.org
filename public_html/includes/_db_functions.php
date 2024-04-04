<?php



	
if($phpver>6.9)
	{
	function connect2db()
		{
		global $_server,$_debug,$dbuser,$dbpassword,$dbname;
		$_mysql = mysqli_connect('localhost',$dbuser,$dbpassword,$dbname);
		if(mysqli_errno($_mysql)){$_debug['mysql_error'][]= mysqli_error($_mysql);}
		mysqli_set_charset( $_mysql,'utf8mb4');
		return $_mysql;
		}

	function mysql_query($querytext,$db)
		{
		global $_mysql;
		return mysqli_query($_mysql,$querytext);
		}


	function mysql_real_escape_string($string)
		{
		global $_mysql;
		return mysqli_real_escape_string($_mysql,$string);
		}

	function mysql_errno($_mysql)
		{
		global $_mysql;
		return mysqli_errno($_mysql);
		}

	function mysql_error($_mysql)
		{
		global $_mysql;
		return mysqli_error($_mysql);
		}

	function mysql_num_rows($query)
		{
		global $_mysql;
		return mysqli_num_rows($query);
		}

	function mysql_fetch_assoc($query)
		{
		global $_mysql;
		return mysqli_fetch_assoc($query);
		}

	function mysql_result($res,$row=0,$col=0)
		{
		global $_mysql;
		$numrows = mysqli_num_rows($res); 
		if ($numrows && $row <= ($numrows-1) && $row >=0)
			{
			mysqli_data_seek($res,$row);
			$resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
			if (isset($resrow[$col]))
				{
				return $resrow[$col];
				}
			}
		return false;
		}

	function mysql_select_db($db_name)
		{
		global $_mysql;
		return mysqli_select_db($_mysql,$db_name);
		}
	}
else
	{
	function connect2db()
		{
		global $dbuser,$dbpassword,$dbname;
		$_mysql=mysql_connect('localhost', $dbuser, $dbpassword);
		mysql_select_db($dbname);
		if(mysql_errno($_mysql)){$_debug['mysql_error'][]= mysql_error($_mysql);}
		$_debug['db_connect']="local db accessed<br>";
		return $_mysql;
		}
	}
    
    

  function dbFetch($table,$Where='',$cell='*',$order='')
    {
    global $_mysql,$memcached_installed,$memCache;
	$result=[];
    if(!$cell){$cell='*';}
    if($Where)
      {
      foreach($Where as $column => $criteria)
        {
        $criteria=str_replace("'","\'",$criteria);
        $WHere[]="`$column`='$criteria'";
        }
      $where='WHERE '.implode(' AND ',$WHere);
      }
    $queryText = sprintf("SELECT $cell FROM `$table` $where $order");

    if($memcached_installed)
      {
      $result=$memCache->get($queryText);
      }
    if(!$result)
      {
      $query=mysql_query($queryText, $_mysql);
      if(mysql_errno($_mysql)){echo ": " . mysql_error($_mysql) . "\n<hr>$queryText";}
      if(mysql_num_rows($query))
        {
        while ($dbRow = mysql_fetch_assoc($query)) 
          {
          $result[]=$dbRow;
          }
        }
      if($memcached_installed)
        {
        $memCache->set($queryText, $result, 0, 30);
        }
      }
    if(isset($result[0]) and !is_array($result[0])){$T=$result;$result=[];$result[0]=$T;}
    $result['queryText']=$queryText;
    return $result;
    }    
    

  function dbFetch1($table,$Where='',$cell='*')
    {
	global $_mysql,$memcached_installed,$memCache;
	$result=[];
    if(!$cell){$cell='*';}
    if($Where)
      {
      foreach($Where as $column => $criteria)
        {
        $criteria=str_replace("'","\'",$criteria);
        $WHere[]="`$column`='$criteria'";
        }
      $where='WHERE '.implode(' AND ',$WHere);
      }
    $queryText = sprintf("SELECT $cell FROM `$table` $where LIMIT 1");

    if($memcached_installed)
      {
      $result=$memCache->get($queryText);
      }
    if(!$result)
      {
      $query=mysql_query($queryText, $_mysql);
      if(mysql_errno($_mysql)){echo ": " . mysql_error($_mysql) . "\n<hr>$queryText";}
      $result = mysql_fetch_assoc($query);
      if($memcached_installed)
        {
        $memCache->set($queryText, $result, 0, 30);
        }
      }
    $result['queryText']=$queryText;
    return $result;
    }
    
    
     
    
    
/*
 dbUpsert ( $table ,$Criteria ( search criteria: an associative array cell names as keys, cell values as values)
                     $Data (input data: an associate array cell name as key, cell value as value)

  updates existing row if exists, if it doesn't exist, inserts new row.

EXAMPLE:

  $Criteria['user']=$ADuser;
  $Criteria['element']=$element;
  $Data['user']=$ADuser;
  $Data['element']=$element;
  $Data['state']=$state;
  dbUpsert('toggle',$Criteria,$Data);

*/

function dbUpsert($table,$Criteria,$Data,$debug=false)
	{
	global $_mysql;
	if($Criteria)
		{
		foreach($Criteria as $cell => $value)
			{
			$value=stripslashes($value);
			$value=str_replace("\'","'",$value);
			$value=str_replace("\'","'",$value);
			$value=str_replace("'","\'",$value);
			$Where[]="`$cell`='$value'";
			}
		$where=implode(' AND ',$Where);
		$queryText = sprintf("SELECT * FROM `$table` WHERE $where");
		$query=mysql_query($queryText, $_mysql);
		if(mysql_errno($_mysql)){echo ": " . mysql_error($_mysql) . "\n<hr>$queryText";}
		$there=mysql_num_rows($query);
		}
	if($there)
		{
		foreach($Data as $cell => $value)
			{
			$value=stripslashes($value);
			$value=str_replace("'","\'",$value);
			$Set[]="`$cell`='$value'";
			}
		$set=implode(',',$Set);
		$queryText="UPDATE `$table` SET $set WHERE $where";
		}
	else
		{
		if($Data)
			{
			foreach($Data as $cell => $value)
				{
				$value=stripslashes($value);
				$value=str_replace("\'","'",$value);
				$value=str_replace("'","\'",$value);
				$Cell[]="`$cell`";
				$Values[]="'$value'";
				}
			$cells=implode(',',$Cell);
			$values=implode(',',$Values);
			$queryText="INSERT INTO `$table` ($cells) VALUES ($values);";
			}
		else
			{
			return 'no data supplies';
			}
		}
	if(!$debug)
		{
		$query=mysql_query($queryText, $_mysql);
		if(mysql_errno($_mysql))
			{
			return ": " . mysql_error($_mysql) . "\n<hr>$queryText<hr>";
			}
		else
			{
			return $queryText;
			}
		}
	else
		{
		return $queryText;
		}
	}

 
  
?>