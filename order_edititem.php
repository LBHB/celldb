<?php
/*** CELLDB
order_edititem.php - order management subsystem

created 2008-06-03 - SVD
***/

// global include: connect to db and get important basic info about user prefs
$min_sec_level=6;
include_once "./celldb.php";

if (!isset($id)) {
  $id=-1;
}
$table="oItem";
if (!isset($refpage)) {
  $refpage="order_items.php";
}

if (1==$action) {
  $sql="UPDATE $table set bad=1-bad WHERE id=$id";
  mysql_query($sql);
  header ("Location: $refpage");
  exit;
}

if (2==$action) {
  // get data posted by user
  $formdata=$_REQUEST;
  
  // actually save/update the data
  $errormsg=savedata($table,$id,$formdata);
  
  if (is_numeric($errormsg)) {
    $rawid=$errormsg;
    $errormsg="";
  }
  
  if (""==$errormsg) {
    header ("Location: $refpage");
    exit;
  }
}

?>


<HTML>
<HEAD>
  <TITLE>celldb - Items</TITLE>
</HEAD>

<?php
orderheader();

if (""!=$errormsg) {
  echo("<p><b><font color=\"#CC0000\">$errormsg</font></b></p>\n");
}


echo("<FORM ACTION=\"order_edititem.php\" METHOD=\"POST\">\n");
echo(" <input type=\"hidden\" name=\"id\" value=\"$id\">\n");
echo(" <input type=\"hidden\" name=\"action\" value=\"2\">\n");
echo(" <input type=\"hidden\" name=\"refpage\" value=\"$refpage\">\n");

$sql="SELECT * FROM $table WHERE id=$id";
$ddata=mysql_query($sql);
if ($drow=mysql_fetch_array($ddata)) {
  $newrow=0;
} else {
  $newrow=1;
}

$sql="DESCRIBE $table";
$tdata=mysql_query($sql);

echo("<table>\n");
while ($row=mysql_fetch_array($tdata)) {
  if ("id"==$row["Field"] || "dateadded"==$row["Field"] ||
       "addedby"==$row["Field"] || "bad"==$row["Field"]) {
    // skip
  } elseif ("companyid"==$row["Field"]) {
    echo("<tr>");
    echo("<td>".$row["Field"]."</td>");
    echo("<td><SELECT NAME=\"".$row["Field"]."\" SIZE=\"1\">");
    $sql="SELECT * FROM oCompany ORDER BY name";
    $cdata=mysql_query($sql);
    while ($crow=mysql_fetch_array($cdata)) {
      
      if (!$newrow && $drow[$row["Field"]] == $row["id"]) {
        $sel=" selected";
      } else {
        $sel="";
      }
      echo(" <option value=\"" . $crow["id"] . "\"$sel>" . $crow["name"] . "</option>");
    }
    echo(" </select>");
    echo("</td>");
    echo("</tr>\n");
    

  } else {
    echo("<tr>");
    echo("<td>".$row["Field"]."</td>");
    echo("<td><INPUT TYPE=TEXT SIZE=40 NAME=\"".$row["Field"]."\"");
    if ($newrow) {
      echo(" value=\"\"");
    } else {
      echo(" value=\"" . $drow[$row["Field"]] . "\"");
    }
    echo("></td>");
    echo("</tr>\n");
  }
}
echo("</table>\n");

echo("<INPUT TYPE=SUBMIT VALUE=\"Save\">");
echo("  (<a href=\"$refpage\">Cancel</a>)\n");
echo("</FORM>\n");

cellfooter();
?>

</HTML>
