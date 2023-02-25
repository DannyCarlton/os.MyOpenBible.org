<?php


/*******************************************************
 * 
 * 	This is the file used to hold server specific data. 
 * 	Fill in the data below, then rename it to "local.php" but keep it in the /includes folder.
 * 	This allows you to have duplicate installations by only leaving this one file different.
 * 
 *******************************************************/


$dbhost='localhost';
$dbuser='change to database user name';
$dbpassword='change to database password';
$dbname='change to database name';

$_path=str_replace('change to main web accessible folder','',$_SERVER['DOCUMENT_ROOT']);

$_smarty='change to smarty folder';

?>