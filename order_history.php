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

if (isset($itemid)) {
  echo("<p><b>Orders for itemid $itemid:</b></p>\n");
  $sql="SELECT oOrder.*,oCompany.name as co_name,date(dateordered) as odate FROM oOrder,oCompany,oOrderItem WHERE oOrder.companyid=oCompany.id AND oOrder.id=oOrderItem.orderid AND oOrderItem.itemid=$itemid AND not(oOrder.bad) ORDER BY dateordered;";

} elseif (isset($companyid)) {
  echo("<p><b>Orders from company $companyid:</b></p>\n");
  $sql="SELECT oOrder.*,oCompany.name as co_name,date(dateordered) as odate FROM oOrder,oCompany WHERE oOrder.companyid=oCompany.id AND oOrder.companyid=$companyid AND not(oOrder.bad) ORDER BY dateordered;";

} else {
  echo("<p><b>All orders:</b></p>\n");
  $sql="SELECT oOrder.*,oCompany.name as co_name,date(dateordered) as odate FROM oOrder,oCompany WHERE oOrder.companyid=oCompany.id AND not(oOrder.bad) ORDER BY dateordered;";
}

$cdata=mysql_query($sql);

echo("<table cellpadding=2>\n");
echo("<tr><td><b>Date</b></td>");
echo("<td><b>Company</b></td>");
echo("<td><b>By</b></td>");
echo("<td>&nbsp;</td>");
echo("<td>&nbsp;</td>");
echo("</tr>\n");
while ($row=mysql_fetch_array($cdata)) {
  $orderid=$row["id"];
  echo("<tr><td>".$row["odate"]."</td>");
  echo("<td>".$row["co_name"]."</td>");
  echo("<td>".$row["addedby"]."</td>");
  echo("<td><a href=\"order_editorder.php?id=$orderid&action=1\">delete</a></td>");
  echo("<td><a href=\"order_editorder.php?id=$orderid\">edit</a></td>");
  echo("<td><a href=\"orderform.php?id=$orderid\">view</a></td>");
  echo("</tr>\n");
}

echo("<tr><td>New order</td>");
echo("<td></td>");
echo("<td></td>");
echo("<td><a href=\"order_editorder.php\">edit</a></td>");
echo("</tr>\n");

echo("</table>");

cellfooter();
?>

</HTML>
