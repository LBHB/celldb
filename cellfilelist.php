<?php
 /*** CELLDB
cellfilelist.php - search for particular data, according to animal, experiment, etc.
also a useful demo of how celldb works.

created 2002 - SVD, modified heavily since then
***/

// global include: connect to db and get important basic info about 
// the user who's currently logged in
include_once "./celldb.php";

?>
<hTML>
<HEAD>
<TITLE><?php echo($siteinfo)?> - File query</TITLE>
</HEAD>
<BODY bgcolor="#FFFFFF">
<?php

// display header bar at the top of the page (cellheader
// function defined in celldb.php)
cellheader();

// file paths/names are stored in linux format in the db. flag to convert
$windowsfmt=1;
$excludetest=1;  // exclude "test" animal from searches

// define the form for choosing search options
// calls this page using html GET so that search parameters
// are encoded in the URL
echo("<FORM ACTION=\"cellfilelist.php\" METHOD=GET>\n");

// hidden parameter saves how output list should be ordered
echo(" <input type=\"hidden\" name=\"orderby\" value=\"$orderby\">\n");

// inputs for various search options.
echo("Animal: <select name=\"animal\" size=\"1\">\n");
if ($animal == "All") {
  echo(" <option value=\"All\" selected>All</option>\n");
} else {
  echo(" <option value=\"All\">All</option>\n");
}
if ($exlcudetest) {
  $animaldata = mysql_query("SELECT DISTINCT animal FROM gCellMaster WHERE animal<>\"Test\" ORDER BY animal");
} else {
  $animaldata = mysql_query("SELECT DISTINCT animal FROM gCellMaster ORDER BY animal");
}
while ( $row = mysql_fetch_array($animaldata) ) {
   if ($animal == $row["animal"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option  value=\"" . $row["animal"] . "\"$sel>" . $row["animal"] . "</option>\n");
}
echo("</select>\n");

echo("&nbsp;&nbsp;Site ID: <INPUT TYPE=TEXT SIZE=6 NAME=\"cellid\" VALUE=\"$cellid\">\n");

echo("&nbsp;&nbsp;Well: <select name=\"well\" size=\"1\">\n");
if (0==$well) {
  echo(" <option value=\"0\" selected>All</option>\n");
} else {
  echo(" <option value=\"0\">All</option>\n");
}
$welldata = mysql_query("SELECT DISTINCT well FROM gPenetration WHERE well>0 ORDER BY well");
while ( $row = mysql_fetch_array($welldata) ) {
   if ($well == $row["well"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option  value=\"" . $row["well"] . "\"$sel>" . $row["well"] . "</option>\n");
}
echo("</select>\n");

echo("<br>Run class: <select name=\"runclassid\" size=1>");
if ($runclassid == -1) {
  echo(" <option value=\"-1\" selected>All</option>");
} else {
  echo(" <option value=\"-1\">All</option>");
}
$runclassdata=mysql_query("SELECT DISTINCT id,name" .
                          " FROM gRunClass ORDER BY name,id");
while ( $row = mysql_fetch_array($runclassdata) ) {
   if ($runclassid == $row["id"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option value=" . $row["id"] . "$sel>" . $row["name"] . "</option>");
}
echo(" </select>\n");

echo("&nbsp;&nbsp;Behavior: <select name=\"behavior\" size=1>");
$behaviorstrings=array("all","all physiology","training","active","passive","naive");
$behaviorsel=array("","","","","","");
for ($ii=0; $ii<count($behaviorstrings); $ii++) {
  if ($behaviorstrings[$ii]==$behavior) {
    $behaviorsel[$ii]=" selected";
  }
  echo(" <option value=\"" . $behaviorstrings[$ii] . "\"" .
       $behaviorsel[$ii] . ">" .
       $behaviorstrings[$ii] . "</option>\n");
}
echo("</select>");

echo("<br><INPUT TYPE=SUBMIT VALUE=\"Filter\">");
echo("</FORM><br>");

// generate SQL command for current query
$swhere="";

if (""!=$animal && $animal!="All") {
  $swhere=$swhere . " AND gCellMaster.animal=\"$animal\"";
}
if ($well>0) {
  $swhere=$swhere . " AND gCellMaster.well=$well";
}
if (""!=$runclassid && $runclassid>=0) {
  $swhere=$swhere . " AND gDataRaw.runclassid=$runclassid";
}
if ($stimspeedid>0) {
  $swhere=$swhere . " AND gDataRaw.stimspeedid=$stimspeedid";
}
if (""!=$cellid) {
  $swhere=$swhere . " AND gCellMaster.cellid like \"" . $cellid. "%\"";
}
if (""!=$orderby) {
  $sorder=" $orderby,";
}
if (""==$behavior || "all"==$behavior) {
  // do nothing
} elseif ("training"==$behavior) {
  $swhere=$swhere . " AND gDataRaw.training=1";
} elseif ("all physiology"==$behavior) {
  $swhere=$swhere . " AND gDataRaw.training=0";
} else {
  $swhere=$swhere." AND gDataRaw.training=0 AND gDataRaw.behavior='$behavior'";
}

if ($excludetest) {
  $swhere=$swhere . " AND not(gCellMaster.animal like 'test')";
}

// if no parameters passed, search returns nothing 
// ... this avoids massive accidental search results
if (""==$swhere) {
  $swhere=" AND 0";
}

$rawsql="SELECT gDataRaw.*,gPenetration.pendate,gCellMaster.penid,".
     " gCellMaster.area,gCellMaster.animal".
     " FROM gDataRaw, gCellMaster, gPenetration" . 
     " WHERE gDataRaw.masterid=gCellMaster.id" .
     " AND gPenetration.id=gCellMaster.penid" .
     " AND gDataRaw.bad=0" .
     $swhere .
     " ORDER BY $sorder gCellMaster.animal,gDataRaw.id";
//echo("sql: $rawsql<br>\n");

$rawfiledata=mysql_query($rawsql);

if (!$rawfiledata) {
  echo("<P>Error performing query: " . mysql_error() . "($sql)</P>");
  exit();
}

// list each cell
$sorturl="<a href=\"demo_page.php?userid=$userid&sessionid=$sessionid&animal=$animal&well=$well&runclassid=$runclassid&stimspeedid=$stimspeedid&stimfmtcode=$stimfmtcode&showunproc=$showunproc&behavior=$behavior&orderby=";

echo("<table>");
echo("<tr>\n");
echo("    <td><b>" . $sorturl . "gDataRaw.id\">RID</a></b></td>\n");
echo("    <td><b>" . $sorturl . "gCellMaster.animal\">Animal</a></b></td>\n");
echo("    <td><b>" . $sorturl . "gDataRaw.cellid\">SiteID</a></b></td>\n");
echo("    <td><b>" . $sorturl . "gPenetration.pendate\">Date</a></b></td>\n");
echo("    <td><b>" . $sorturl . "gDataRaw.runclassid\">Class</a></b></td>\n");
echo("    <td><b>Area</b></td>\n");
echo("    <td><b>Parameter file</b></td>\n");
echo("    </tr>\n");

// loop through each row returned by the search
while ( $row = mysql_fetch_array($rawfiledata) ) {
  
  $cellid=$row["cellid"];
  $penid=$row["penid"];
  $parmfile=$row["parmfile"];
  $resppath=$row["resppath"];
  $areas=explode(",",$row["area"]);
  $areas=array_unique($areas);
  $badareas=explode(",","XX,");
  $areas=array_diff($areas,$badareas);
  $areas=implode(",",$areas);
  
  if (""==$row["matlabfile"]) {
    $file_sorted=0;
  } else {
    $file_sorted=1;
  }
  if ($parmfile[0]=="/" || $parmfile[1]==":") {
    // already contains path
  } elseif (substr($resppath,0,10)!="/auto/data") {
    $parmfile=$parmfile . "(MISSING?)";
    //$parmfile=$resppath . $parmfile;
  }
  if ($windowsfmt) {
    $parmfile=str_replace("/afs/glue.umd.edu/department/isr/labs/nsl/projects/daqsc",
                          "M:\\daq",$parmfile);
    $parmfile=str_replace("/auto/data/daq","M:\\daq",$parmfile);
    $parmfile=str_replace("/","\\",$parmfile);
  }
  
  echo("<tr>\n");
  echo("   <td><a href=\"$fnpeninfo?userid=$userid&penid=$penid");
  echo("#$cellid\">" . $row["id"] . "</a>&nbsp;</td>\n");
  echo("   <td>" . $row["animal"] . "</td>\n");
  echo("   <td>" . $cellid . "</td>\n");
  echo("   <td>" . $row["pendate"] . "</td>\n");
  echo("   <td>" . $row["runclass"] . "</td>\n");
  echo("   <td>$areas</td>\n");
  echo("   <td>$parmfile</td>\n");
  if ($file_sorted) {
    echo("   <td>sorted</td>\n");
  } else {
    echo("   <td>&nbsp;</td>\n");
  }
  
  echo("</tr>\n");
}
echo("</table>\n");

echo("<p>sql: $rawsql</p>\n");

// display page footer
cellfooter();

?>

</BODY>
</HTML>
