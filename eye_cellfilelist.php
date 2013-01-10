<HTML>
<HEAD>
<TITLE>celldb - Cell file entries</TITLE>
</HEAD>
<BODY bgcolor="#FFFFFF">
<?php

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";

echo( "<p><b>$siteinfo Preprocessed data query</b></p>\n" );

// load results of currently requested query
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
  $swhere=$swhere . " AND gSingleCell.cellid like \"" . $cellid. "%\"";
}
if (""!=$area) {
  $swhere=$swhere . " AND gSingleCell.area = \"" . $area . "\"";
}
if (""!=$orderby) {
  $sorder=" $orderby,";
}

$rawsql="SELECT gDataRaw.*,gSingleCell.cellid as scellid,".
     "gSingleCell.penid,gSingleCell.area".
     " FROM gDataRaw, gCellMaster, gSingleCell" . 
     " WHERE gDataRaw.masterid=gSingleCell.masterid" .
     " AND gDataRaw.masterid=gCellMaster.id" .
     " AND gDataRaw.bad=0" .
     $swhere .
     " ORDER BY $sorder gCellMaster.animal,gDataRaw.cellid,gDataRaw.id";
//echo("sql: $rawsql<br>\n");

$rawfiledata=mysql_query($rawsql);

echo("<FORM ACTION=\"$fncellfilelist\" METHOD=POST>\n");
echo(" <input type=\"hidden\" name=\"userid\" value=\"" . $userid . "\">\n");
echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
echo(" <input type=\"hidden\" name=\"orderby\" value=\"$orderby\">\n");

echo("Animal: <select name=\"animal\" size=\"1\">\n");
if ($animal == "All") {
  echo(" <option value=\"All\" selected>All</option>\n");
} else {
  echo(" <option value=\"All\">All</option>\n");
}
$animaldata = mysql_query("SELECT DISTINCT animal FROM gCellMaster ORDER BY animal");
while ( $row = mysql_fetch_array($animaldata) ) {
   if ($animal == $row["animal"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option  value=\"" . $row["animal"] . "\"$sel>" . $row["animal"] . "</option>\n");
}
echo("</select>\n");

echo("Well: <select name=\"well\" size=\"1\">\n");
$welldata = mysql_query("SELECT DISTINCT well FROM gPenetration ORDER BY well");
while ( $row = mysql_fetch_array($welldata) ) {
   if ($well == $row["well"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   if (0 == $row["well"]) {
       $wellname="All";
   } else {
       $wellname=$row["well"];
   }
   echo(" <option  value=\"" . $row["well"] . "\"$sel>" . $wellname . "</option>\n");
}
echo("</select>\n");

echo("Show unproc: <select name=\"showunproc\" size=\"1\">\n");
if (1==$showunproc) {
  $selyes="selected";
  $selno="";
} else {
  $selyes="";
  $selno="selected";
}
echo(" <option value=\"1\" $selyes>Yes</option>\n");
echo(" <option value=\"0\" $selno>No</option>\n");
echo("</select>\n");

echo("<br>Run class: <select name=\"runclassid\" size=1>");
if ($runclassid == -1) {
  echo(" <option value=\"-1\" selected>All</option>");
} else {
  echo(" <option value=\"-1\">All</option>");
}
$runclassdata=mysql_query("SELECT DISTINCT id,name" .
                          " FROM gRunClass ORDER BY id");
while ( $row = mysql_fetch_array($runclassdata) ) {
   if ($runclassid == $row["id"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option value=" . $row["id"] . "$sel>" . $row["name"] . "</option>");
}
echo(" </select>\n");

echo(" Speed: <select name=\"stimspeedid\" size=1>");
if ($stimspeedid == 0) {
  echo(" <option value=\"0\" selected>All</option>");
} else {
  echo(" <option value=\"0\">All</option>");
}
$speeddata=mysql_query("SELECT DISTINCT stimspeedid FROM gDataRaw" .
                       " WHERE stimspeedid>0 ORDER BY stimspeedid DESC");
while ( $row = mysql_fetch_array($speeddata) ) {
   if ($stimspeedid == $row["stimspeedid"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option value=" . $row["stimspeedid"] . "$sel>" . $row["stimspeedid"] . " Hz</option>");
}
echo(" </select>\n");

echo(" Area: <select name=\"area\" size=1>");
if ("" == $area) {
  echo(" <option value=\"\" selected>All</option>");
} else {
  echo(" <option value=\"\">All</option>");
}
$areadata=mysql_query("SELECT DISTINCT area FROM gSingleCell" .
                       " ORDER BY area");
while ( $row = mysql_fetch_array($areadata) ) {
   if ($area==$row["area"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo("<option value=" .$row["area"]. "$sel>" .$row["area"]. "</option>");
}
echo(" </select>\n");

echo(" Stim fmt: <select name=\"stimfmtcode\" size=1>");
if ($stimfmtcode == 0) {
  echo(" <option value=\"-1\" selected>All</option>");
} else {
  echo(" <option value=\"-1\">All</option>");
}
$fmtdata=mysql_query("SELECT DISTINCT stimfmtcode,stimfilefmt".
                     " FROM sCellFile" .
                     " ORDER BY stimfmtcode");
while ( $row = mysql_fetch_array($fmtdata) ) {
   if ($stimfmtcode == $row["stimfmtcode"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option value=" . $row["stimfmtcode"] . "$sel>" . $row["stimfilefmt"] . "</option>");
}
echo(" </select><br>\n");

echo("Cellid: <INPUT TYPE=TEXT SIZE=6 NAME=\"cellid\" VALUE=\"$cellid\"><br>");

echo("<INPUT TYPE=SUBMIT VALUE=\"Filter\">");
echo("</FORM><br>");

if (!$rawfiledata) {
  echo("<P>Error performing query: " . mysql_error() . "</P>");
  exit();
}

// list each cell
$sorturl="<a href=\"$fncellfilelist?userid=$userid&sessionid=$sessionid&animal=$animal&well=$well&runclassid=$runclassid&stimspeedid=$stimspeedid&stimfmtcode=$stimfmtcode&showunproc=$showunproc&orderby=";

echo("<table>");
echo("<tr>\n");
echo("    <td><b>" . $sorturl . "gDataRaw.id\">RID</a></b><br></td>\n");
echo("    <td><b>CFID</b></td>\n");
echo("    <td><b>" . $sorturl . "gDataRaw.cellid\">Cell</a></b><br></td>\n");
echo("    <td><b>" . $sorturl . "gSingleCell.area\">Area</a></b><br></td>\n");
echo("    <td><b>" . $sorturl . "gDataRaw.runclassid\">Class</a></b></td>\n");
echo("    <td><b>" . $sorturl . "gDataRaw.stimspeedid\">Speed</a></b></td>\n");
echo("    <td><b>Fmt</b></td>\n");
echo("    <td><b>Pix</b></td>\n");
echo("    <td><b>Reps</b></td>\n");
echo("    <td><b>Frames</b></td>\n");
//echo("    <td><b>Stim file</b></td>\n");
//echo("    <td><b>Resp file</b></td>\n");
echo("    </tr>\n");

while ( $rawrow = mysql_fetch_array($rawfiledata) ) {
  
  if (""!=$stimfmtcode && $stimfmtcode>-1) {
    $swhere=" AND sCellFile.stimfmtcode=$stimfmtcode";
  } else{
    $swhere="";
  }
  
  $sql="SELECT sCellFile.*,gRunClass.name AS runclass" .
    " FROM sCellFile LEFT JOIN gRunClass" .
    " ON sCellFile.runclassid=gRunClass.id" .
    " WHERE sCellFile.cellid='" . $rawrow["scellid"] . "'" .
    " AND sCellFile.rawid=" . $rawrow["id"] .
    $swhere .
    " ORDER BY stimfmtcode";
  $cellfiledata=mysql_query($sql);
  //echo($sql);
  
  $cellid=$rawrow["scellid"];
  $area=$rawrow["area"];
  $penid=$rawrow["penid"];
  
  $filecount=0;
  while ( $row = mysql_fetch_array($cellfiledata) ) {
    $filecount+=1;
    echo("<tr>\n");
    echo("   <td><a href=\"$fnpeninfo?userid=$userid&penid=$penid");
    echo("#$cellid\">\n");
    echo($row["rawid"] . "</a>&nbsp;</td>\n");
    echo("   <td>" . $row["id"] . "&nbsp;</td>\n");
    echo("   <td>" . $cellid . "</td>\n");
    echo("   <td>" . $area . "</td>\n");
    echo("   <td>" . $row["runclass"] . "</td>\n");
    echo("   <td>" . $row["stimspeedid"] . " Hz</td>\n");
    echo("   <td>" . $row["stimfilefmt"] . "</td>\n");
    echo("   <td align=right>" . $row["stimwindowsize"] . "</td>\n");
    echo("   <td align=right>" . $row["repcount"] . "</td>\n");
    echo("   <td align=right>" . $row["resplen"] . "</td>\n");
    //echo("   <td>" . $row["stimfile"] . "</td>\n");
    //echo("   <td>" . $row["respfile"] . "</td>\n");
    
    echo("<td><a href=\"$fnpeninfo?userid=$userid&penid=$penid#" . $row["cellid"] . "\">\n");
    //echo("<td><a href=\"$fnpeninfo?userid=$userid&penid=$penid\">");
    //echo($penname . "</a>&nbsp;</td>\n");
  
    echo("</tr>\n");

    // zero out cellid and area to tabulate in a clean way
    $cellid="";
    $area="";
  }
  
  if ( 0 == $filecount && $showunproc) {
    $sql="SELECT gRunClass.name AS runclass FROM gRunClass" .
      " WHERE id=" . $rawrow["runclassid"];
    $data=mysql_query($sql);
    $classrow=mysql_fetch_array($data);
    $runclass=$classrow["runclass"];

    $stimfile=$rawrow["stimfile"];
    $stimpath=$rawrow["stimpath"];
    if (strlen($stimpath)>0) {
      if (substr($stimpath,-1)=="/") {
        $stimpath=substr($stimpath,0,strlen($stimpath)-1);
      }
      $sp=substr(strrchr($stimpath,"/"),1);
      if ($sp / 1.0 == $sp) {
        $sp=substr(strrchr($stimpath,"_"),1);
      }
      $stimfile=$sp . "/" . $stimfile;
    } elseif (strcmp($stimfile,'')==0) {
      // use stimfile
    } else {
      $stimfile="UNKNOWN";
    }

    echo("<tr>\n");
    echo("   <td><a href=\"$fnpeninfo?userid=$userid&penid=$penid");
    echo("#$cellid\">\n");
    echo($rawrow["id"] . "</a>&nbsp;</td>\n");
    echo("   <td>--</td>\n");
    echo("   <td>" . $rawrow["cellid"] . "</td>\n");
    echo("   <td>$area</td>\n");
    echo("   <td>$runclass</td>\n");
    echo("   <td>" . $rawrow["stimspeedid"] . " Hz</td>\n");
    echo("   <td>--</td>\n");
    echo("   <td>* $stimfile *</td>\n");
    echo("   <td>* " . $rawrow["respfile"] . " *</td>\n");
    
    echo("</tr>\n");
  }

}
echo("</table>\n");


echo("<p>sql: $rawsql</p>\n");

?>

</BODY>
</HTML>
