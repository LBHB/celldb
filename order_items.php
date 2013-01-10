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
  <TITLE>celldb - Items</TITLE>
</HEAD>

<?php
orderheader();

$sql="SELECT oItem.*,oCompany.name as co_name FROM oItem,oCompany WHERE oItem.companyid=oCompany.id AND not(oItem.bad) ORDER BY oItem.name;";
$cdata=mysql_query($sql);

echo("<table cellpadding=2>\n");
echo("<tr><td><b>Item</b></td>");
echo("<td><b>Company</b></td>");
echo("<td>&nbsp;</td>");
echo("<td>&nbsp;</td>");
echo("</tr>\n");
while ($row=mysql_fetch_array($cdata)) {
  $itemid=$row["id"];
  echo("<tr><td>".$row["name"]."</td>");
  echo("<td><a href=\"order_history.php?companyid=".$row["companyid"].
       "\">" . $row["co_name"]."</a></td>");
  echo("<td><a href=\"order_history.php?itemid=$itemid\">history</a></td>");
  echo("<td><a href=\"order_edititem.php?id=$itemid&action=1\">delete</a></td>");
  echo("<td><a href=\"order_edititem.php?id=$itemid\">edit</a></td>");
  echo("<td><a href=\"order_editorder.php?companyid=".$row["companyid"]. "&itemid[0]=$itemid\">new order</a></td>");
  echo("</tr>\n");
}

echo("<tr><td>New item</td>");
echo("<td></td><td></td>");
echo("<td></td>");
echo("<td><a href=\"order_edititem.php\">edit</a></td>");
echo("</tr>\n");

echo("</table>");

cellfooter();
?>

</HTML>
