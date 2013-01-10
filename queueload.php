<?php

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php3";

if (""==$allowqueuemaster) {
  $allowqueuemaster=1;
}
if (""==$orderby) {
  $orderby="tComputer.load1";
}

?>

<HTML>
<HEAD>
<TITLE>dbqueue load</TITLE>
</HEAD>
<BODY bgcolor="#FFFFFF">
<meta http-equiv="Refresh" content="30">
<meta NAME="description" CONTENT="Queue machine load monitor">

<?php
echo( "<b>Queue load</b>\n" );
echo(" (<a href=\"queue/queuemasterlog.txt\">today's log</a>");
echo(" <a href=\"queue/queuemasterlog.txt.1\">yesterday's log</a>)<br>");

// summary stats
include "./queuesum.php3";

// query celldb for queue entries matching search criteria

$sql="SELECT * FROM tComputer" .
     " WHERE allowqueuemaster>=$allowqueuemaster" .
     " AND name like \"$machinename\"" .
     " AND location in (0,3)" .
     " ORDER BY $orderby";

//echo("sql: $sql<br>\n");
$compdata=mysql_query($sql);

if (!$compdata) {
  echo("<P>Error performing query: " . mysql_error() . "</P>");
  exit();
}

// list each entry
$loadsc=100;
$keycount=3;

$sorturl="queueload.php3?userid=$userid&sessionid=$sessionid&&allowqueuemaster=$allowqueuemaster&orderby=";

echo("<table>");
echo("<tr bgcolor=\"#bbbbff\">\n");
echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "tComputer.id\">id</a></b><br></td>\n");
echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "tComputer.name\">name (jobs)</a></b><br></td>\n");
echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "tComputer.load1\">load1/15</a></b><br></td>\n");

for ($ii=0; $ii<$keycount; $ii++) {
  echo("  <td width=" . ($loadsc) . " align=right>" . ($ii+1) . ".0</td>\n");
}
echo("</tr>\n");

while ( $row = mysql_fetch_array($compdata) ) {
  
  echo("<tr>\n");
  echo("   <td>" . $row["id"] . "&nbsp;</td>\n");
  
  $mname=$row["name"] ."." . $row["ext"];
  echo("   <td>$mname (" . $row["numproc"] . ")&nbsp;</td>\n");
  
  if (1==$row["lastoverload"] && 2==$row["allowqueuemaster"]) {
    echo("   <td><font color=\"#CC0000\">" . sprintf("%.2f",$row["load1"]) .
         "/" . sprintf("%.2f",$row["load15"]) .
         "*</font>&nbsp;</td>\n");
  } else {
    echo("   <td>" . sprintf("%.2f",$row["load1"]) . 
         "/" . sprintf("%.2f",$row["load15"]) .
         "&nbsp;</td>\n");
  }
  
  echo("   <td colspan=" . ($keycount+1) . ">");
  $load=$row["load1"];
  if (0==$row["allowqueuemaster"]) {
    $fn="black.jpg";
  } elseif (2==$row["allowqueuemaster"] && 1==$row["lastoverload"]) {
    $fn="red.jpg";
  } elseif (0==$row["numproc"]) {
    $fn="green.jpg";
  } else {
    $fn="blue.jpg";
  }
  echo("<img border=0 src=\"$fn\" align=\"left\"");
  echo("width=" . round($load*$loadsc+1) . " height=12>");
  echo("</td>\n");

  
  
  echo("</tr>\n");
}

echo("<tr ><td></td>\n");
echo("   <td valign=center colspan=" . ($keycount+3) . ">\n");
echo("   <img border=0 src=\"green.jpg\" width=12 height=12>");
echo("&nbsp;inactive&nbsp;\n");
echo("   <img border=0 src=\"blue.jpg\" width=12 height=12>");
echo("&nbsp;active&nbsp;\n");
echo("   <img border=0 src=\"red.jpg\" width=12 height=12>");
echo("&nbsp;overloaded&nbsp;\n");
echo("   <img border=0 src=\"black.jpg\" width=12 height=12>");
echo("&nbsp;not in queue&nbsp;\n");

echo("   </td>\n");
echo("</tr>\n");

echo("<tr><td colspan=" . ($keycount+4) . ">\n");

$acturl="queueload.php3?userid=$userid&sessionid=$sessionid&" .
         "orderby=$orderby&allowqueuemaster=";

echo("&nbsp;Show inactive: \n");
if (0==$allowqueuemaster) {
  $b1yes="<b>";
  $b2yes="</b>";
  $b1no="";
  $b2no="";
} else {
  $b1yes="";
  $b2yes="";
  $b1no="<b>";
  $b2no="</b>";
}

echo("$b1yes<a href=\"" . $acturl . "0\">Yes</a>$b2yes\n");
echo(" / $b1no<a href=\"" . $acturl . "1\">No</a>$b2no\n");
echo("&nbsp;&nbsp;<a href=\"" . $acturl . $allowqueuemaster ."\">Refresh</a>");

echo("</td></tr></table>\n");



?>

<?php

echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>");

$sql="SELECT * FROM tGlobalData";
$globaldata=mysql_query($sql);
$row=mysql_fetch_array($globaldata);
echo("Last queuemaster tick: " . $row["daemonclick"] . "<br>");

?>


<em>This page refreshes automatically every 30 seconds</em><br>

</BODY>
</HTML>
