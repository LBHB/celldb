<?php
/*** CELLDB
celllist.php - list cells or behavioral/weight data for one or more animals

created 2002 - SVD
mod 2006-05-24 - merged in behavior/weight list as option view
***/

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";
?>

<HTML>
<HEAD>
  <TITLE>celldb - Animal history</TITLE>
  <link rel="shortcut icon" href="favicon.ico" />
</HEAD>
<?php

echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

// save latest well query parameters
if (""!=$userid) {
  if ("All"==$well) {
    $lastwell=0;
  } else {
    $lastwell=$well;
  }
  $sql="UPDATE gUserPrefs SET lastanimal=\"$animal\", lastspecies=\"$queryspecies\", lastwell=$lastwell, lasttraining=$recstat WHERE userid=\"$userid\"";
  $userdata = mysql_query($sql);
}

// generate sql statement to retrieve appropriate penetratioins
if ("cells"==$showdata) {
  $sql1start="SELECT gPenetration.penname as gpenname,".
    "gPenetration.id as gpenid," .
    "gPenetration.animal as ganimal," .
    "gPenetration.well as gwell," .
    "gPenetration.etudeg as getudeg," .
    "gCellMaster.*" .
    " FROM gPenetration LEFT JOIN gCellMaster" .
    " ON gPenetration.id=gCellMaster.penid".
    " INNER JOIN gAnimal ON gAnimal.animal=gPenetration.animal".
    " WHERE ";
} else {
  $sql1start="SELECT gPenetration.*,round(gHealth.water,1) as hwater,".
    " round(gHealth.weight) as hweight".
    " FROM gPenetration LEFT JOIN gHealth".
    " ON (gHealth.animal=gPenetration.animal".
    " AND gHealth.date=gPenetration.pendate)".
    " INNER JOIN gAnimal ON gAnimal.animal=gPenetration.animal".
    " WHERE";
}

// load cells that fit well/animal filter criteria
if (""==$orderby) {
  $tob="animal,well DESC,penname DESC";
 } else {
  $tob= "$orderby,animal,well DESC,penname DESC";
 }
if ("All"==$animal) {
  $sanimal="lab='$LAB'";
} elseif ("my"==$animal) {
  //$sanimal="gPenetration.addedby=\"" . $userid . "\"";
  $sanimal="gAnimal.caretaker like \"%" . $userid . "%\"";
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

if ("All"==$animal || "my"==$animal || ""==$animal) {
  $welldata = mysql_query("SELECT DISTINCT well FROM gPenetration WHERE well>0 ORDER BY well");
} else {
  $welldata = mysql_query("SELECT DISTINCT well FROM gPenetration WHERE animal=\"$animal\" AND well>0 ORDER BY well");
}

if (0) {
  if ( $well > 1 ) {
    $bwell=$well-1;
    echo("&nbsp;&nbsp;<a href=\"celllist.php?");
    echo("animal=$animal&recstat=$recstat&well=$bwell&showdata=$showdata\">");
    echo("$bwell <--</a>\n");
  }
  $fwell=$well+1;
  echo("&nbsp;Well&nbsp;<a href=\"celllist.php?");
  echo("animal=$animal&recstat=$recstat&well=$fwell&showdata=$showdata\">");
  echo("--> $fwell</a>\n");
 }

// header
cellheader();

echo("<p>");

$queryspecies2=explode(",",$queryspecies);
if (count($queryspecies2)>1) {
  $sactive=$queryspecies2[1];
} else { 
  $sactive="active";
} 
$queryspecies2=$queryspecies2[0];
if ("all"==$queryspecies2) {
   $queryspecies2="%";
}
 
  echo("Species: ");
  echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value
\">\n");
  echo(" <option value=\"celllist.php?animal=$animal&recstat=$recstat&well=$well&showdata=$showdata&queryspecies=%\"$sel>all</option>\n");
  for ($ii=0; $ii<count($SpeciesStatusList); $ii++) {
    if ($queryspecies == $SpeciesStatusList[$ii]) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option value=\"celllist.php?animal=$animal&recstat=$recstat&well=$well&showdata=$showdata&queryspecies=" . $SpeciesStatusList[$ii] . "\"$sel>" . $SpeciesStatusList[$ii] . "</option>\n");
  }
  echo("</select>&nbsp;\n");
 
if ($sactive=="active") {
   $animaldata = mysql_query("SELECT animal,id FROM gAnimal WHERE species like '$queryspecies2' AND onschedule<2 AND lab='$LAB' ORDER BY animal");
} elseif ($sactive=="mine") {
   $animaldata = mysql_query("SELECT animal,id FROM gAnimal WHERE species like '$queryspecies2' AND caretaker like '%$userid%' ORDER BY animal");
} else {
   $animaldata = mysql_query("SELECT animal,id FROM gAnimal WHERE species like '$queryspecies2' ORDER BY animal");
}
echo("Who: ");
echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\">\n");
if ($animal == "All") {
    $sel=" selected";
} else {
    $sel="";
}
echo(" <option  value=\"celllist.php?queryspecies=$queryspecies&animal=All&recstat=$recstat&well=$well&showdata=$showdata\"$sel>" . 
     "All</option>\n");
if ($animal == "my") {
    $sel=" selected";
} else {
    $sel="";
}
echo(" <option  value=\"celllist.php?queryspecies=$queryspecies&animal=my&recstat=$recstat&well=$well&showdata=$showdata\"$sel>" . 
     "My animals</option>\n");
while ( $row = mysql_fetch_array($animaldata) ) {
   if ($animal == $row["animal"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option  value=\"celllist.php?queryspecies=$queryspecies&animal=" .
        $row["animal"] . "&recstat=$recstat&well=$well&showdata=$showdata\"$sel>" . 
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
   echo(" <option value=\"celllist.php?".
        "queryspecies=$queryspecies&animal=$animal&recstat=$recstat&well=" . (-$ii) .  
        "&showdata=$showdata\"" . $timesel[$ii] . ">" .
        $timestrings[$ii] . "</option>\n");
}

while ( $row = mysql_fetch_array($welldata) ) {
   if ($well == $row["well"]) {
       $sel=" selected";
       $wellmatch=1;
   } else {
       $sel="";
   }
   echo(" <option value=\"celllist.php?".
        "queryspecies=$queryspecies&animal=$animal&recstat=$recstat&well=" . $row["well"] . 
        "&showdata=$showdata\"$sel>" . 
        "Well " . $row["well"] . "</option>\n");
}
echo("</select>");

echo(" What: ");
echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\">\n");
if ($showdata=="cells") {
   echo(" <option value=\"celllist.php?" .
        "queryspecies=$queryspecies&animal=$animal&recstat=$recstat&well=$well&showdata=cells\" selected>" . 
        "cells</option>\n");
   echo(" <option value=\"celllist.php?" .
        "queryspecies=$queryspecies&animal=$animal&recstat=$recstat&well=$well&showdata=behavior\">" . 
        "behavior</option>\n");
} else {
   echo(" <option value=\"celllist.php?" .
        "queryspecies=$queryspecies&animal=$animal&recstat=$recstat&well=$well&showdata=cells\">" . 
        "cells</option>\n");
   echo(" <option value=\"celllist.php?" .
        "queryspecies=$queryspecies&animal=$animal&recstat=$recstat&well=$well&showdata=behavior\" selected>" . 
        "behavior</option>\n");
}
echo("</select>\n");

echo(" Rec status: ");
$statstrings=array("Recording","Training","Both");
$setstrings=array("","","");
$setstrings[$recstat]="selected";

echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\">\n");
for ($ii=0; $ii<count($statstrings); $ii++) {
   echo(" <option value=\"celllist.php?" .
        "queryspecies=$queryspecies&animal=$animal&recstat=" . $ii . "&well=$well&showdata=$showdata\" " . 
        $setstrings[$ii] . ">" . $statstrings[$ii] . "</option>\n");
}
echo("</select>\n");

//echo(" <INPUT TYPE=SUBMIT VALUE=\"Filter\">");
//echo(" userid: <INPUT TYPE=TEXT NAME=\"search\" value=\"" . $search . "\">");
echo("</p>\n");
//echo("</FORM>\n");

if (!$celldata) {
  echo("<P>Error performing query $sql: " . mysql_error() . "</P>");
  exit();
}

// list each penetration/training session
echo("<table>");
$reorderlink="celllist.php?animal=$animal&recstat=$recstat&well=$well&showdata=$showdata&orderby=";
if ("cells"==$showdata) {
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
} else {
   echo("<tr><td><b>Well</b></td>\n");
   echo("    <td><a href=\"" . $reorderlink . "pendate\"><b>Date</b></a></td>\n");
   echo("    <td><a href=\"" . $reorderlink . "penname\"><b>Penetration&nbsp;</b></a></td>\n");
   echo("    <td><b>Water (ml)&nbsp;</b></td>\n");
   echo("    <td><b>Weight ($weightunits)&nbsp;</b></td>\n");
   echo("    <td><b>Files</b></td>\n");
   echo("    <td><b>Performance&nbsp;</b></td></tr>\n");
}

echo("<tr><td></td><td></td>");
echo("    <td><a href=\"$fnpenedit\">New</a></td>\n");
echo("    <td></td>\n");
echo("    <td></td>\n");
echo("    <td></td>\n");
echo("    <td></td>\n");
echo("    <td><a href=\"$fnpendump?well=$well&animal=$animal\">");
echo("Dump $animal/$well all</a></td>\n");
echo("</tr>\n");

$counter=0;
while ( $row = mysql_fetch_array($celldata) ) {
  $counter=$counter+1;
  if ($counter>15) {
    $bkmk="&bkmk=" . ($counter-15);
  } else {
    $bkmk="";
  }
  
  if ("cells"==$showdata) {
    $penid=$row["gpenid"];
    $penname=$row["gpenname"];
    $tanimal=$row["ganimal"];
    $twell=$row["gwell"];
  } else {
    $penid=$row["id"];
    $penname=$row["penname"];
    $tanimal=$row["animal"];
    $twell=$row["well"];
  }
  
  echo("<tr>\n");
  $animallink="celllist.php?animal=$tanimal&recstat=$recstat&well=0&showdata=$showdata";
 
  echo("   <td><a name=\"$counter\"><a href=\"$animallink\">$tanimal</a>/$twell</td>\n");
  
  if ("cells"==$showdata) {
    echo("   <td><a href=\"$fnpeninfo?".
         "penid=$penid" . $bkmk . "\">");
    echo($penname . "</a>&nbsp;</td>\n");

    if ($row["cellid"]) {
    
      echo("<td><a href=\"$fnpeninfo?" .
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
  
  } else {
    echo("   <td>" . $row["pendate"] . "</td>\n");
    echo("   <td><a href=\"$fnpeninfo?".
         "penid=$penid" . $bkmk . "\">");
    echo("$penname</a>&nbsp;</td>\n");
    echo("   <td>" . $row["hwater"] . "</td>\n");
    echo("   <td>" . $row["hweight"] . "</td>\n");
    $sql="SELECT count(gDataRaw.id) as filecount," .
      " sum(gDataRaw.corrtrials) as corrcount, sum(if(gDataRaw.behavior='active',gDataRaw.trials,0)) as trialcount".
      " FROM gDataRaw INNER JOIN gCellMaster ON gDataRaw.masterid=gCellMaster.id" .
      " WHERE gCellMaster.penid=$penid";
    $filedata=mysql_query($sql);
    $frow=mysql_fetch_array($filedata);
    $trialcount=$frow["trialcount"];
    
    echo("<td align=\"center\">" . $frow["filecount"] . "&nbsp;</td>\n");
    echo("   <td>" . $frow["corrcount"] . "/" . $frow["trialcount"]);
    if ($trialcount>0) {
      echo(" (" . round($frow["corrcount"]/ $frow["trialcount"]*100,0) . "%)");
    }
    echo("</td>\n");
  }
  
  echo("<td><a href=\"$fnpendump?penname=$penname\">");
  echo("Dump $penname log</a></td>\n");
 
  echo("</tr>\n");  
}

echo("<tr><td></td><td></td>");
echo("    <td><a href=\"$fnpenedit\">New</a></td>\n");
echo("    <td></td>\n");
echo("    <td></td>\n");
echo("    <td></td>\n");
echo("    <td></td>\n");
echo("    <td><a href=\"$fnpendump?well=$well&animal=$animal\">");
echo("Dump $animal/$well all</a></td>\n");
echo("</tr>\n");

echo("</table>\n");

echo("<p>");
if ( $well > 1 ) {
  $bwell=$well-1;
  echo("&nbsp;&nbsp;<a href=\"celllist.php?");
  echo("animal=$animal&recstat=$recstat&well=$bwell&showdata=$showdata\">");
  echo("$bwell <--</a>\n");
}
$fwell=$well+1;
echo("&nbsp;Well&nbsp;<a href=\"celllist.php?");
echo("animal=$animal&recstat=$recstat&well=$fwell&showdata=$showdata\">");
echo("--> $fwell</a></p>\n");

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
