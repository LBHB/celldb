<?php
/*** CELLDB
celllist.php - list all penetrations for a single animal/well
created 2002 - SVD
***/

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";
?>

<HTML>
<HEAD>
  <TITLE>celldb - Cell list</TITLE>
  <link rel="shortcut icon" href="favicon.ico" />
</HEAD>
<?php

echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

// header
echo("<p><b>$siteinfo</b>\n");
echo("&nbsp;<b>Cell list</b>\n");
echo("&nbsp;<a href=\"animalhistory.php?animal=$animal\">Behavior history</a>\n");
echo("&nbsp;<a href=\"animals.php?animal=$animal\">Animal details</a>\n");
echo("&nbsp;<a href=\"weights.php\">Weights</a>\n");
echo("&nbsp;<a href=\"editrunclass.php\">Run classes</a>\n");
echo("&nbsp;<a href=\"index.php?logout=1\">Logout</a>\n" );
echo("</p>\n");
echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");


$animaldata = mysql_query("SELECT animal FROM gAnimal ORDER BY animal");
if ("All"==$animal || ""==$animal) {
  $welldata = mysql_query("SELECT DISTINCT well FROM gPenetration WHERE training<>2 AND well>0 ORDER BY well");
} else {
  $welldata = mysql_query("SELECT DISTINCT well FROM gPenetration WHERE training<>2 AND animal=\"$animal\" AND well>0 ORDER BY well");
}
if (0) {
  if ( $well > 1 ) {
    $bwell=$well-1;
    echo("&nbsp;&nbsp;<a href=\"celllist.php?userid=$userid&sessionid=$sessionid&");
    echo("animal=$animal&recstat=$recstat&well=$bwell\">");
    echo("<-- $bwell</a>\n");
  }
  $fwell=$well+1;
  echo("&nbsp;Well&nbsp;<a href=\"celllist.php?userid=$userid&sessionid=$sessionid&");
  echo("animal=$animal&recstat=$recstat&well=$fwell\">");
  echo("$fwell --></a>\n");
 }

//echo("<FORM ACTION=\"celllist.php\" METHOD=POST>\n");
//echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
//echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
//echo(" <input type=\"hidden\" name=\"well\" value=\"$well\">\n");
//echo(" <input type=\"hidden\" name=\"animal\" value=\"$animal\">\n");
//echo(" <input type=\"hidden\" name=\"recstat\" value=\"$recstat\">\n");

echo("<p>");
echo("Animal: ");
echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\">\n");
if ($animal == "All") {
    $sel=" selected";
} else {
    $sel="";
}
echo(" <option  value=\"celllist.php?userid=$userid&sessionid=$sessionid" .
     "&animal=All&recstat=$recstat&well=$well\"$sel>" . 
     "All</option>\n");
while ( $row = mysql_fetch_array($animaldata) ) {
   if ($animal == $row["animal"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option  value=\"celllist.php?userid=$userid&sessionid=$sessionid" .
        "&animal=" .$row["animal"] . "&recstat=$recstat&well=$well\"$sel>" . 
        $row["animal"] . "</option>\n");
}
echo("</select>");

echo(" When: \n");
echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\">\n");
$wellmatch=0;

$timestrings=array("All","Today","Two days","This week","This month");
$timesel=array("","","","","");
if ($well<=0) {
  $timesel[-$well]=" selected";
  $wellmatch=1;
}
for ($ii=0; $ii<count($timestrings); $ii++) {
   echo(" <option value=\"celllist.php?userid=$userid&sessionid=$sessionid" .
        "&animal=$animal&recstat=$recstat&well=" . (-$ii) .  "\"" .
        $timesel[$ii] . ">" .
        $timestrings[$ii] . "</option>\n");
}

while ( $row = mysql_fetch_array($welldata) ) {
   if ($well == $row["well"]) {
       $sel=" selected";
       $wellmatch=1;
   } else {
       $sel="";
   }
   echo(" <option value=\"celllist.php?userid=$userid&sessionid=$sessionid" .
        "&animal=$animal&recstat=$recstat&well=" . $row["well"] . "\"$sel>" . 
        "Well " . $row["well"] . "</option>\n");
}
echo("</select>");

$statstrings=array("Recording","Training","Both");
$setstrings=array("","","");
$setstrings[$recstat]="selected";

echo(" Rec status: ");
echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\">\n");
for ($ii=0; $ii<count($statstrings); $ii++) {
   echo(" <option value=\"celllist.php?userid=$userid&sessionid=$sessionid" .
        "&animal=$animal&recstat=" . $ii . "&well=$well\" " . 
        $setstrings[$ii] . ">" . $statstrings[$ii] . "</option>\n");
}
echo("</select>\n");

//echo(" <INPUT TYPE=SUBMIT VALUE=\"Filter\">");
echo("</p>\n");
//echo("</FORM>\n");

// if selected well has no entries for this animal, select all penetrations.
if (!$wellmatch) {
  $well=0;
}

// save latest well query parameters
if (""!=$userid) {
  $userdata=mysql_query("UPDATE gUserPrefs SET lastanimal=\"$animal\", lastwell=$well, lasttraining=$recstat WHERE userid=\"$userid\"");
}

$sql1start="SELECT gPenetration.penname as gpenname,".
    "gPenetration.id as gpenid," .
    "gPenetration.animal as ganimal," .
    "gPenetration.well as gwell," .
    "gPenetration.etudeg as getudeg," .
    "gCellMaster.*" .
    " FROM gPenetration LEFT JOIN gCellMaster" .
    " ON gPenetration.id=gCellMaster.penid WHERE ";

// load cells that fit well/animal filter criteria
if (""==$orderby) {
  $tob="gPenetration.animal,gPenetration.well,gpenname,cellid";
 } else {
  $tob= "$orderby,gPenetration.animal,gPenetration.well,gpenname,cellid";
 }
if ("All"==$animal) {
  $sanimal="1";
} else {
  $sanimal="gPenetration.animal=\"" . $animal . "\"";
}
if (0==$well) {
  $swell="1";
} elseif ($well>0) {
  $swell="gPenetration.well=" . $well;
} else {
  $mm=date("m");
  $dd=date("d");
  $yy=date("y");
  if (-1==$well) {
    $firststamp=mktime(0,0,0,$mm,$dd,$yy);
  } elseif (-2==$well) {
    $firststamp=mktime(0,0,0,$mm,$dd-1,$yy);
  } elseif (-3==$well) {
    $firststamp=mktime(0,0,0,$mm,$dd-7,$yy);
  } elseif (-4==$well) {
    $firststamp=mktime(0,0,0,$mm,$dd-30,$yy);
  }
  $firstdate=date("Y-m-d",$firststamp);
  $swell="gPenetration.pendate>=\"" . $firstdate . "\"";
}

if (2==$recstat) {
  $strain="gPenetration.training in (0,1) ";
} elseif (1==$recstat) {
  $strain="gPenetration.training=1 ";
} else {
  $strain="gPenetration.training=0 ";
}

$sql ="$sql1start $sanimal AND $swell AND $strain ORDER BY $tob";
$celldata = mysql_query($sql);

if (!$celldata) {
  echo("<P>Error performing query: " . mysql_error() . ": $sql</P>");
  exit();
}

// list each cell
$reorderlink="celllist.php?userid=$userid&sessionid=$sessionid" .
  "&animal=$animal&recstat=$recstat&well=$well&orderby=";
echo("<table>");
echo("<tr><td><b>Well</b></td><td><b>Penetration</b></td>\n");
if ($orderby=="gCellMaster.siteid") {
  echo("    <td><b><a href=\"" . $reorderlink . "gCellMaster.siteid+DESC\">Site</a></b></td>\n");
 } else {
  echo("    <td><b><a href=\"" . $reorderlink . "gCellMaster.siteid\">Site</a></b></td>\n");
 }
if ("eye"==$view) {
  echo("    <td><b>Area/RF</b></td></tr>\n");
 } else {
  echo("    <td><b>Files</b></td></tr>\n");
 }
echo("<tr><td></td><td><a href=\"$fnpenedit?userid=$userid&sessionid=$sessionid\">");
echo("New</a></td>\n");

// removed 'new cell' option
if (0) {
  echo("    <td><a href=\"$fncelledit?userid=$userid&sessionid=$sessionid&masterid=-1\">");
  echo("New cell</a></td>\n");
 } else {
  echo("    <td></td>\n");
}

echo("    <td></td>\n");
echo("<td><a href=\"$fnpendump?userid=$userid&sessionid=$sessionid&well=$well&animal=$animal\">");
echo("Dump $animal/$well all</a></td>\n");
echo("</tr>\n");

$counter=0;
while ( $row = mysql_fetch_array($celldata) ) {
  $counter=$counter+1;
  
  $penid=$row["gpenid"];
  
  //$sql="SELECT * from gPenetration WHERE id=$penid";
  //$pen1data = mysql_query($sql);
  //$penrow = mysql_fetch_array($pen1data);
  //$penname=$penrow["penname"];
  $penname=$row["gpenname"];
  
  if ($counter>15) {
    $bkmk="&bkmk=" . ($counter-15);
  } else {
    $bkmk="";
  }
  
  echo("<tr>\n");
  echo("   <td><a name=\"$counter\">" . $row["ganimal"] . "/" . 
       $row["gwell"] . "</td>\n");
  echo("   <td><a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&".
       "penid=$penid" . $bkmk . "\">");
  echo($penname . "</a>&nbsp;</td>\n");

  if ($row["cellid"]) {
    
    echo("<td><a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&" .
         "penid=$penid" . $bkmk . "#" . $row["cellid"] . "\">\n");
    echo($row["cellid"] . "</a>&nbsp;</td>\n");
    
    if (0==$row["training"] && "eye"==$view) {
      echo("<td>" . $row["area"] . "&nbsp;");
      
      $ppd=$row["rfppd"];
      if (0==$ppd) {
        $ppd=$row["getudeg"];
      }
      if ($ppd>0) {
        $xdeg=round($row["xoffset"]/$ppd,1);
        $ydeg=round($row["yoffset"]/$ppd,1);
        $ddeg=round($row["rfsize"]/$ppd,2);
        echo(" (x,y)=($xdeg,$ydeg) d=$ddeg deg\n");
      }
      echo("&nbsp;</td>\n");
    } elseif (0==$row["training"]) {
      $sql="SELECT count(id) as filecount FROM gDataRaw WHERE masterid=" . 
        $row["id"];
      $filedata=mysql_query($sql);
      $frow=mysql_fetch_array($filedata);
      echo("<td align=\"center\">" . $frow["filecount"] . "&nbsp;</td>\n");
      
    } else {
      echo("<td>*TRAINING*</td>\n");
    }
  } else {
    echo("   <td></td>\n");
    echo("   <td></td>\n");
  }
  echo("<td><a href=\"$fnpendump?userid=$userid&sessionid=$sessionid&penname=$penname\">");
  echo("Dump $penname log</a></td>\n");
 
  echo("</tr>\n");  
}

echo("<tr>\n");
echo("<td></td><td><a href=\"$fnpenedit?userid=$userid&sessionid=$sessionid$bkmk\">");
echo("New</a></td>\n");
// removed 'new cell' option
if (0) {
  echo("    <td><a href=\"$fncelledit?userid=$userid&sessionid=$sessionid&masterid=-1\">");
  echo("New cell</a></td>\n");
 } else {
  echo("    <td></td>\n");
}
echo("    <td></td>\n");
echo("<td><a href=\"$fnpendump?userid=$userid&sessionid=$sessionid&well=$well&animal=$animal\">");
echo("Dump $animal/$well all</a></td>\n");
echo("</tr>\n");
echo("</table>\n");

echo("<p>");
if ( $well > 1 ) {
  $bwell=$well-1;
  echo("&nbsp;&nbsp;<a href=\"celllist.php?userid=$userid&sessionid=$sessionid&");
  echo("animal=$animal&recstat=$recstat&well=$bwell\">");
  echo("<-- $bwell</a>\n");
}
$fwell=$well+1;
echo("&nbsp;Well&nbsp;<a href=\"celllist.php?userid=$userid&sessionid=$sessionid&");
echo("animal=$animal&recstat=$recstat&well=$fwell\">");
echo("$fwell --></a></p>\n");

cellfooter();

?>

<script language="javascript">
function gotoUrl(url) {
  if (url == "")
    return;
  location.href = url;
}
function newWin(url) {
  // url of this function should have the format: "target,URL".
  if (url == "")
    return;
  window.open(url.substring(url.indexOf(",") + 1, url.length), 
	url.substring(0, url.indexOf(",")));
}
</script>

</BODY>
</HTML>
