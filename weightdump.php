<?php
/*** CELLDB
animals.php - list/edit animals with data in celldb
created 8/3/2005 - SVD
***/

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";

?>

<HTML>
<HEAD>
<TITLE><?php echo("$siteinfo - Weights - $animal"); ?></TITLE>
<link rel="shortcut icon" href="favicon.ico" />
</HEAD>

<?php

echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

$sql="SELECT * FROM gHealth WHERE weight>0 AND animal='$animal' ORDER BY date";
$wdata=mysql_query($sql);

echo("<table>\n");
echo("<tr><td><b>$animal</b> - Date</td>");
echo("<td>Weight</td>");
echo("<td>Water(ml)</td>");
echo("</tr>\n");
while ($row=mysql_fetch_array($wdata)) {
  $pendate=$row["date"];
  echo("<tr><td>" . $pendate . "</td>");
  echo("<td>" . round($row["weight"],0) . "</td>");
  echo("<td>" . round($row["water"],0) . "</td>");
  echo("</tr>\n");
}


?>
