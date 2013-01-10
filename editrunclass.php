<?php
/*** CELLDB
editrunclass.php - list/edit runclasses
created 8/4/2005 - SVD
***/

// global include: connect to db and get important basic info about user prefs
$min_sec_level=6;
include_once "./celldb.php";


// action -1 - action failed
//         0 - add runclass
//         1 - edit runclass
//         2 - delete runclass

if (""==$action) {
  $action=-2;
 }
$errormsg="";

if (0==$action && (""==$name || ""==$details)) {
  $errormsg="ERROR: You must enter an runclass name and details.";
  $action=-1;
}
if (0==$action) {
  $sql="SELECT * FROM gRunClass WHERE name=\"$name\"";
  $adata=mysql_query($sql);
  if (mysql_num_rows($adata)>0) {
    $errormsg="ERROR: Run class already exists with the requested abbreviation.";
    $action=-1;
  }
}

// ok, checks done. perform actions
if (0==$action) {
  $sql="INSERT INTO gRunClass" .
    " (name,details,task,stimclass,".
    "addedby,info)" .
    " VALUES (\"$name\",\"$details\",\"$task\",\"$stimclass\",".
    "\"$userid\",\"$siteinfo\")";
  $result=mysql_query($sql);
 } elseif (1==$action) {
   // edit
   $sql="UPDATE gRunClass SET ".
     "name=\"$name\",".
     "details=\"$details\",".
     "task=\"$task\",".
     "stimclass=\"$stimclass\"".
     " WHERE id=$id";
   $result=mysql_query($sql);
   
 } elseif (2==$action) {
   // delete
   $sql="DELETE FROM gRunClass SET ".
     " WHERE id=$id";
   $result=mysql_query($sql);

 }

if ($action>=0 && !$result) {
  $errormsg=mysql_error();
 }


?>

<HTML>
<HEAD>
<TITLE><?php echo("$siteinfo"); ?> - Run classes</TITLE>
  <link rel="shortcut icon" href="favicon.ico" />
</HEAD>

<?php


echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

// header
cellheader();

if (""!=$errormsg) {
  echo("<p><b><font color=\"#CC0000\">$errormsg</font></b></p>\n");
}

$sql="SELECT * FROM gRunClass ORDER BY id";
$namedata = mysql_query($sql);

echo("<table>");
echo("<tr><td><b>id</b></td><td><b>Name / Details / Stim class / Task</b></td>\n");
echo("</tr>\n");

while ( $row = mysql_fetch_array($namedata) ) {
  $id=$row["id"];
  $name=$row["name"];
  $details=$row["details"];
  $stimclass=$row["stimclass"];
  $task=$row["task"];
  
  echo("<tr>\n");
  echo("   <td>$id</td>\n");
  echo("   <td>\n");

  echo("<FORM ACTION=\"editrunclass.php\" METHOD=POST>\n");
  echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
  echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
  echo(" <input type=\"hidden\" name=\"id\" value=\"$id\">\n");
  echo(" <input type=\"hidden\" name=\"action\" value=\"1\">\n");
  
  echo("<INPUT TYPE=TEXT SIZE=4 NAME=\"name\" value=\"$name\">\n");
  echo("<INPUT TYPE=TEXT SIZE=30 NAME=\"details\" value=\"$details\">\n");
  echo("<INPUT TYPE=TEXT SIZE=20 NAME=\"stimclass\" value=\"$stimclass\">\n");
  echo("<INPUT TYPE=TEXT SIZE=20 NAME=\"task\" value=\"$task\">\n");
  echo("<INPUT TYPE=SUBMIT VALUE=\"Change\">");
  echo("</FORM>\n");
  
  echo("</td></tr>\n");
 }

echo("<tr>\n");
echo("   <td>&nbsp;</td>\n");

echo("   <td>\n");
echo("<FORM ACTION=\"editrunclass.php\" METHOD=POST>\n");
echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
echo(" <input type=\"hidden\" name=\"id\" value=\"-1\">\n");
echo(" <input type=\"hidden\" name=\"action\" value=\"0\">\n");

echo("<INPUT TYPE=TEXT SIZE=4 NAME=\"name\" value=\"\"> /\n");
echo("<INPUT TYPE=TEXT SIZE=30 NAME=\"details\" value=\"\">\n");
echo("<INPUT TYPE=TEXT SIZE=20 NAME=\"stimclass\" value=\"\">\n");
echo("<INPUT TYPE=TEXT SIZE=20 NAME=\"task\" value=\"\">\n");
echo("<INPUT TYPE=SUBMIT VALUE=\"Add\">");
echo("</FORM>\n");

echo("</td></tr>\n");

echo("</table>\n");


cellfooter();

?>
