<?php
/*** CELLDB
order_editorder.php - order management subsystem

created 2008-06-03 - SVD
***/

// global include: connect to db and get important basic info about user prefs
$min_sec_level=6;
include_once "./celldb.php";

if (!isset($id)) {
  $id=-1;
}
$table="oOrder";
if (!isset($refpage)) {
  $refpage="order_history.php";
}

if (1==$action) {
  $sql="UPDATE $table set bad=1-bad WHERE id=$id";
  mysql_query($sql);

  $sql="UPDATE oOrderItem set bad=1-bad WHERE orderid=$id";
  mysql_query($sql);
  
  header ("Location: $refpage");
  exit;
}
if (2==$action) {
  // get data posted by user
  $formdata=$_REQUEST;
  
  if ("Save"==$formdata["done"]) {
    $formdata["bad"]=0;
  } else {
    $formdata["bad"]=1;
  }

  // actually save/update the data
  $errormsg=savedata($table,$id,$formdata);

  if (is_numeric($errormsg)) {
    $rawid=$errormsg;
    $errormsg="";
  }
  
  $ii=0;
  while ($ii<count($itemid)){
    if ($itemid[$ii]>0) {
      // item is valid, save it
      $itemdata=Array();
      $itemdata["id"]=$itemorderid[$ii];
      $itemdata["orderid"]=$id;
      $itemdata["itemid"]=$itemid[$ii];
      $itemdata["quantity"]=$quantity[$ii];
      $itemdata["note"]=$note[$ii];
      $itemdata["unitprice"]=$unitprice[$ii];
      
      $errormsg2=savedata("oOrderItem",$itemorderid[$ii],$itemdata);
    }
    $ii++;
  }

  
  if (""==$errormsg && $done) {
    header ("Location: $refpage");
    exit;
  } elseif (""==$errormsg) {

    //reload this page with updated values
    header ("Location: order_editorder.php?id=$rawid");
    exit;
  }
}

?>


<HTML>
<HEAD>
  <TITLE>celldb - Order</TITLE>
<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="./include/winter.css" />

<!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="./include/zapatec.js"></script>
    <script type="text/javascript" src="./include/calendar.js"></script>
<!-- Loading language definition file -->
    <script type="text/javascript" src="./include/calendar-en.js"></script>
</HEAD>

<?php
orderheader();

if (""!=$errormsg) {
  echo("<p><b><font color=\"#CC0000\">$errormsg</font></b></p>\n");
}

$sql="SELECT * FROM $table WHERE id=$id";
$ddata=mysql_query($sql);
if ($drow=mysql_fetch_array($ddata)) {
  $newrow=0;
  $addedby=$drow["addedby"];
  $companyid=$drow["companyid"];
} else {
  $newrow=1;
  $addedby=$userid;
  if (!isset($companyid)){
    $companyid=-1;
  }
}

$sql="DESCRIBE $table";
$tdata=mysql_query($sql);

echo("<FORM NAME=\"editform\" ACTION=\"order_editorder.php\" METHOD=\"POST\">\n");
echo(" <input type=\"hidden\" name=\"id\" value=\"$id\">\n");
echo(" <input type=\"hidden\" name=\"action\" value=\"2\">\n");
echo(" <input type=\"hidden\" name=\"refpage\" value=\"$refpage\">\n");

echo("<table>\n");
while ($row=mysql_fetch_array($tdata)) {
  if ("id"==$row["Field"] || "dateadded"==$row["Field"] ||
       "addedby"==$row["Field"] || "bad"==$row["Field"]) {
    // skip
  } elseif ("companyid"==$row["Field"]) {
    echo("<tr>");
    echo("<td>".$row["Field"]."</td>");
    echo("<td><SELECT NAME=\"".$row["Field"]."\" SIZE=\"1\" OnChange=\"document.editform.submit()\">");
    echo(" <option value=\"-1\"$sel>--</option>");
    $sql="SELECT * FROM oCompany ORDER BY name";
    $cdata=mysql_query($sql);
    while ($crow=mysql_fetch_array($cdata)) {
      if ($companyid == $crow["id"]) {
        $sel=" selected";
      } else {
        $sel="";
      }
      echo(" <option value=\"" . $crow["id"] . "\"$sel>" . $crow["name"] . "</option>");
    }
    echo(" </select>");
    echo("</td>");
    echo("</tr>\n");

  } elseif ("dateordered"==$row["Field"]||"daterequired"==$row["Field"]) {
    echo("<tr>");
    echo("<td>".$row["Field"]."</td>");
    echo("<td><INPUT TYPE=TEXT SIZE=15 ID=\"".$row["Field"]."\" NAME=\"".$row["Field"]."\"");
    if ($newrow && "dateordered"==$row["Field"]) {
      echo(" value=\"". date("Y-m-d"). "\"");
    } elseif ($newrow) {
      echo(" value=\"\"");
    } else {
      echo(" value=\"" . $drow[$row["Field"]] . "\"");
    }
    echo(">&nbsp;");

?>
    <button id="butt<?php echo($row["Field"]); ?>">...</button>
    <script type="text/javascript">//<![CDATA[
      Zapatec.Calendar.setup({
        electric          : false,
        inputField        : "<?php echo($row["Field"]); ?>",
        button            : "butt<?php echo($row["Field"]); ?>",
        ifFormat          : "%Y-%m-%d",
        daFormat          : "%Y/%m/%d"
      });
    //]]></script>
<?php
    echo("</td></tr>\n");

  } else {
    echo("<tr>");
    echo("<td>".$row["Field"]."</td>");
    echo("<td><INPUT TYPE=TEXT SIZE=15 NAME=\"".$row["Field"]."\"");
    if ($newrow) {
      echo(" value=\"\"");
    } else {
      echo(" value=\"" . $drow[$row["Field"]] . "\"");
    }
    echo("></td>");
    echo("</tr>\n");
  }
}

$sql="SELECT oOrderItem.*,name,productnumber,units".
" FROM oOrderItem,oItem WHERE oOrderItem.itemid=oItem.id".
" AND oOrderItem.orderid=$id";
$idata=mysql_query($sql);

$sql="SELECT * FROM oItem WHERE companyid=$companyid";
$listdata=mysql_query($sql);

echo("<tr><td></td><td>");
echo("<table>\n");
echo(" <tr><td>Item</td><td>Prod #</td><td>Quantity</td><td>Units</td>");
echo("<td>Unit price</td><td>Note</td></tr>\n");
$ii=0;
while ($irow=mysql_fetch_array($idata)) {
  echo(" <tr>");
  if (mysql_num_rows($listdata)>0){
    mysql_data_seek($listdata,0);
  }
  echo("<td><SELECT NAME=\"itemid[$ii]\" SIZE=\"1\" OnChange=\"document.editform.submit()\">");
  echo(" <option value=\"-1\">CHOOSE</option>");
  while ($crow=mysql_fetch_array($listdata)) {
    if ($irow["itemid"] == $crow["id"]) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo("<option value=\"" . $crow["id"] . "\"$sel>" . $crow["name"] . "</option>");
  }
  echo("</select>\n");
  echo(" <input type=\"hidden\" name=\"itemorderid[$ii]\" value=\"". $irow["id"]."\">\n");
  echo("</td>");
  echo("<td>".$irow["productnumber"]."</td>\n");
  
  echo("<td><INPUT TYPE=TEXT SIZE=4 NAME=\"quantity[$ii]\" value=\"" . $irow["quantity"] . "\"></td>\n");
  echo("<td>".$irow["units"]."</td>\n");
  echo("<td>$<INPUT TYPE=TEXT SIZE=10 NAME=\"unitprice[$ii]\" value=\"" . $irow["unitprice"] . "\"></td>\n");
  echo("<td><INPUT TYPE=TEXT SIZE=20 NAME=\"note[$ii]\" value=\"" . $irow["note"] . "\"></td>\n");
  
  echo("</tr>\n");
  $ii++;
}

// NEW ITEM
echo(" <tr>");
if (mysql_num_rows($listdata)>0){
  mysql_data_seek($listdata,0);
}
echo("<td><SELECT NAME=\"itemid[$ii]\" SIZE=\"1\" OnChange=\"document.editform.submit()\">");
echo(" <option value=\"-1\">NEW ITEM</option>");
while ($crow=mysql_fetch_array($listdata)) {
  echo(" <option value=\"" .$crow["id"]. "\">" .$crow["name"]. "</option>");
}
echo(" </select>\n");
echo(" <input type=\"hidden\" name=\"itemorderid[$ii]\" value=\"-1\">\n");
echo(" <input type=\"hidden\" name=\"quantity[$ii]\" value=\"1\">\n");
echo(" <input type=\"hidden\" name=\"unitprice[$ii]\" value=\"\">\n");
echo(" <input type=\"hidden\" name=\"note[$ii]\" value=\"\">\n");
echo("</td>");
echo("</tr>\n");
echo("</table>\n");
echo("</td></tr>");

echo("<tr><td></td><td>");
echo("<INPUT TYPE=SUBMIT NAME=\"done\" VALUE=\"Save\">");
echo("  (<a href=\"$refpage\">Cancel</a>)\n");
echo("</td></tr>");


echo("</table>\n");

echo("</FORM>\n");

cellfooter();
?>

</HTML>
