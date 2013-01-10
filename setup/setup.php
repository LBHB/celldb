<?php

/*** CELLDB
setup.php - initial database creation

created SVD 2012-07-27

***/
// read in user settings
include_once "../config.php";

if (!isset($dbserver)) {
  fatal_error("Database server not specified. <tt>config.php</tt> missing?");
}

// initial db connection needed basically for anything
if (!$dbcnx=@mysql_connect($dbserver.":3306",$dbuser,$dbpassword)) {
  
  $errormsg=rawurlencode("Error connecting to mysql (server: $dbserver, user: $dbuser)\n");
  header("Location: error.php?errormsg=".
         $errormsg . "&refurl=setup/setup.php");
  exit();
}

// use database called $dbname (specified in config.php)
if (!mysql_select_db($dbname, $dbcnx)) {
  header("Location: error.php?errormsg=".
         rawurlencode("Could connect to db server but could not open database $dbname. Make dbname specification is correct in <tt>config.php</tt>.") . 
         "&refurl=setup/setup.php");
 }

echo("Sucessfully connected to server: $dbserver, database: $dbname<br>\n");

echo("Creating tables...<br>\n");

$res=exec("mysql -u".$dbuser." -p".$dbpassword." ".$dbname." < celldb_struct.sql");

echo("Table creation complete ($res).<br>\n");

echo("Creating test database entries...<br>\n");

$sql="INSERT INTO gAnimal (animal,cellprefix,notes,addedby,species,lab)".
  " VALUES ('Test','tst','General behavior/experiment notes here',".
  "'admin','ferret','$LAB')";
echo("$sql<br>");
mysql_query($sql);

$sql="INSERT INTO gAnimal (animal,cellprefix,notes,addedby,species,lab)".
  " VALUES ('TestMouse','tsm','General behavior/experiment notes here',".
  "'admin','mouse','$LAB')";
mysql_query($sql);

$res=exec("mysql -u".$dbuser." -p".$dbpassword." ".$dbname." < gRunClass.sql");

$sessionid=md5('Ferret1');
$sql="INSERT INTO gUserPrefs" .
  " (userid,password,seclevel,email,realname,lab)".
  " VALUES (\"david\",\"" . 
  "$sessionid\",2,\"stephen.v.david@gmail.com\",\"Stephen David\",".
  "\"$LAB\")";
$result=mysql_query($sql);

echo("Test database entry creation complete.<br>\n");
$path_parts = pathinfo($_SERVER['SCRIPT_NAME']);
$celldb_path=$path_parts['dirname'];
echo("Pathinfo: $celldb_path<br>\n");

?>