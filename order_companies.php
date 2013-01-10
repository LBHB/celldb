<?php
/*** CELLDB
order.php - order management subsystem

created 2008-06-03 - SVD
***/

// global include: connect to db and get important basic info about user prefs
$min_sec_level=6;
include_once "./celldb.php";
?>


<HTML>
<HEAD>
  <TITLE>celldb - Companies</TITLE>
</HEAD>

<?php
orderheader();

$sql="SELECT * FROM oCompany WHERE not(bad) ORDER BY name;";
$cdata=mysql_query($sql);

echo("<table cellpadding=2>\n");
echo("<tr><td><b>Company</b></td>");
echo("<td><b>City, State</b></td>");
echo("<td><b>URL</b></td>");
echo("<td>&nbsp;</td>");
echo("</tr>\n");
while ($row=mysql_fetch_array($cdata)) {
  $companyid=$row["id"];
  echo("<tr><td>".$row["name"]."</td>");
  if (""<>$row["city"] || ""<>$row["state"]) {
    echo("<td>".$row["city"].", ".$row["state"]."</td>");
  } else {
    echo("<td>&nbsp;</td>");
  }
  $turl=$row["url"];
  if (""<>$turl && "http" <> substr($turl,0,4)) {
    $turl="http://".$turl;
  }
  echo("<td><a href=\"$turl\">$turl</a></td>");
  echo("<td><a href=\"order_history.php?companyid=$companyid\">history</a></td>");
  echo("<td><a href=\"order_editcompany.php?id=$companyid&action=1\">del</a></td>");
  echo("<td><a href=\"order_editcompany.php?id=$companyid\">edit</a></td>");
  echo("<td><a href=\"order_editorder.php?companyid=$companyid\">new&nbsp;order</a></td>");
  echo("</tr>\n");
}

echo("<tr><td>New company</td>");
echo("<td></td><td></td><td></td><td></td>");
echo("<td></td>");
echo("<td><a href=\"order_editcompany.php\">edit</a></td>");
echo("</tr>\n");

echo("</table>");

cellfooter();
?>

</HTML>
