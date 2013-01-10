<?php
/*** CELLDB
peninfo.php - display information about a single pentration
created 2002 - SVD
***/

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";

// load data pre-existing data about the penetration -- if it exists
if (-1==$penid or ""==$penid) {
   // only penname provided, see if it exists
   $sql="SELECT * FROM gPenetration WHERE penname=\"$penname\"";
} else {
   // see if penid exists
   $sql="SELECT * FROM gPenetration WHERE id=$penid";
}

$pendata = mysql_query($sql);
$pendatarows=mysql_num_rows($pendata);
if ($pendatarows==0) {
   header ("Location: $fnpenedit?userid=$userid&sessionid=$sessionid&penname=$penname&bkmk=$bkmk");
   exit;                 /* Make sure that code below does not execute */
}

// if there are rows, then there is an entry
$penrow=mysql_fetch_array($pendata);
$penid=$penrow["id"];
$penname=$penrow["penname"];
$animal=$penrow["animal"];

?>
<HTML>
<HEAD>
<TITLE>celldb - Penetration info - <?php echo($penname)?></TITLE>
</HEAD>

<?php
echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

$sql="SELECT max(penname) as penname FROM gPenetration WHERE penname<\"$penname\" AND animal=\"$animal\"";
$prevpendata=mysql_query($sql);
$tpenrow=mysql_fetch_array($prevpendata);
$prevpenname=$tpenrow["penname"];
//echo($sql);
$sql="SELECT min(penname) as penname FROM gPenetration WHERE penname>\"$penname\" AND animal=\"$animal\"";
$nextpendata=mysql_query($sql);
$tpenrow=mysql_fetch_array($nextpendata);
$nextpenname=$tpenrow["penname"];
//echo($sql);

if (1==$penrow["training"]) {
  $pencat="Training session";
  $cellcat="Pseudo-site";
} else {
  $pencat="Penetration";
  $cellcat="Site";
}

echo( "<p>$pencat <b>$penname</b>&nbsp;" );
echo("&nbsp;(<a href=\"$fnpenedit?userid=$userid&sessionid=$sessionid&penname=$penname&action=1&bkmk=$bkmk\">");
echo("Edit pen</a>)\n");
echo("&nbsp;(<a href=\"$fnpendump?userid=$userid&sessionid=$sessionid&penname=$penname&bkmk=$bkmk\">");
echo("Dump</a>)\n");
echo("&nbsp;(<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&penname=$prevpenname&bkmk=" . ($bkmk-1) . "\">");
echo("<-- $prevpenname</a>)\n");
echo("&nbsp;(<a href=\"celllist.php?userid=$userid&sessionid=$sessionid#$bkmk\">UP</a>)\n");
echo("&nbsp;(<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&penname=$nextpenname&bkmk=" . ($bkmk+1) . "\">");
echo("$nextpenname --></a>)</p>\n");

echo("<table cellpadding=1>\n");
echo("<tr><td><b>Animal:</b></td><td>" . $penrow["animal"]. "&nbsp;</td>\n");
echo("    <td><b>Well:</b></td><td>" . $penrow["well"]. "</td></tr>\n");
echo("<tr><td><b>Date:</b></td><td>" . $penrow["pendate"]. "&nbsp;</td>\n");
echo("    <td><b>Who:</b></td><td>" . $penrow["who"]. "</td></tr>\n");
echo("<tr><td><b>Fix time:</b></td><td>" . $penrow["fixtime"]. "&nbsp;</td>\n");
echo("    <td><b>Eye:</b></td><td>" . $penrow["eye"] . "</td></tr>\n");
echo("<tr><td><b>Mondist:</b></td><td>" . $penrow["mondist"] * 1.0 . "&nbsp;cm</td>\n");
echo("    <td><b>Pix/deg:</b></td><td>" . $penrow["etudeg"] * 1.0 . "</td></tr>\n");
echo("<tr><td><b>Water:</b></td><td>" . $penrow["water"] . "&nbsp;ml</td>\n");
echo("    <td><b>Weight:</b></td><td>" . $penrow["weight"] * 1.0 . "&nbsp;kg</td></tr>\n");
echo("<tr><td valign=top><b>Rack:</b></td><td colspan=3>" . $penrow["racknotes"] . "</td></tr>\n");
echo("<tr><td valign=top><b>Probe:</b></td><td colspan=3>" . $penrow["probenotes"] . "</td></tr>\n");
echo("<tr><td valign=top><b>Electrode:</b></td><td colspan=3>" . $penrow["electrodenotes"] . "</td></tr>\n");
//echo("<tr><td>Impedance:</td><td>" . $penrow["impedance"] . "</td></tr>\n");
//echo("<tr><td valign=top>Notes:</td><td colspan=3>" . $penrow["impedancenotes"] . "</td></tr>\n");
//echo("<tr><td>Stability:</td><td>" . $penrow["stability"] . "</td></tr>\n");
//echo("<tr><td valign=top>Notes:</td><td colspan=3>" . $penrow["stabilitynotes"] . "</td></tr>\n");
echo("<tr><td><b>Added by:</b></td><td>" . $penrow["addedby"]. "&nbsp;</td>\n");
echo("    <td><b>Last mod:</b></td><td>" . parsetimestamp($penrow["lastmod"]) . "</td></tr>\n");

echo("</table>" );
$ppd=$penrow["etudeg"] * 1.0;
?>

<?php


// load cells associated with this penetration
$celldata = mysql_query("SELECT * FROM gCellMaster WHERE penid=$penid" .
                        " ORDER BY cellid, id");


while ( $cellrow = mysql_fetch_array($celldata) ) {
   $masterid=$cellrow["id"];
   echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
   echo("<a name=\"" . $row["siteid"] . "\"></a>"); 

   $singledata = mysql_query("SELECT * FROM gSingleCell" .
                             " WHERE masterid=$masterid" .
                             " ORDER BY id");

   echo("<table cellpadding=1>\n");
   $rowcount=0;
   while ( $row = mysql_fetch_array($singledata) ) {
     $rowcount=$rowcount+1;
     if ($rowcount==1) {
       echo("<tr><td><b>" . $cellcat . " ");
       echo("<a href=\"$fncelledit?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&masterid=$masterid&action=1\">" . $row["siteid"] . "</a>:</b></td>\n");
     } else {
       echo("<tr><td></td>\n");
     }
     
     echo("<td>" . $row["cellid"] . "\n");
     echo("&nbsp;&nbsp;Area: ". $row["area"] . "\n");
     echo("&nbsp;&nbsp;RF: (". $row["xoffset"] . ",");
     echo($row["yoffset"] . ") " . $row["rfsize"] . " pix\n");
     if ($ppd>0) {
       $xdeg=round($row["xoffset"]/$ppd,1);
       $ydeg=round($row["yoffset"]/$ppd,1);
       $ddeg=round($row["rfsize"]/$ppd,1);
       echo(" / ($xdeg,$ydeg) $ddeg deg\n");
     }
     //echo("  Mod: " . parsetimestamp($row["lastmod"]));
     if ($row["crap"]>0) {
       echo("<b> *** CRAP ***</b>");
     }
     echo("</td>\n</tr>\n");
   }
   echo("</table>\n");
   
   $rawfiledata = mysql_query("SELECT * FROM gDataRaw" .
                              " WHERE masterid=$masterid" .
                              " ORDER BY RIGHT(respfile,3),id");

   // list each raw data file
   echo("<table cellpadding=1>\n");
   echo("<tr><td><b>Task</b></td><td><b>Class</b></td>\n");
   echo("    <td><b>Response file</b></td><td><b>Stim file</b></td>");
   //echo("    <td><b>(RawID)</b></td><td><b>(CellFileID)</b></td></tr>\n");
   echo("    <td><b>(RawID)</b></td><td></td></tr>\n");
   while ( $row = mysql_fetch_array($rawfiledata) ) {
      // display file names associated with this gDataRaw entry

      $rawid=$row["id"];
      $runclassid=$row["runclassid"];
      if (strcmp($row["respfile"],'')==0) {
         $respfile=$row["matlabfile"];
      } else {
         $respfile=$row["respfile"];
      }
      
      $stimfile=$row["stimfile"];
      $stimpath=$row["stimpath"];
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
      echo(" <td>" . $row["task"] . "</td>\n");
      
      echo(" <td><a href=\"$fncellfileedit?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&masterid=$masterid&rawid=$rawid&runclassid=$runclassid&action=1\">");
      echo($row["runclass"] . "</a></td>\n");
      
      echo(" <td><a href=\"$fncellfileedit?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&masterid=$masterid&rawid=$rawid&runclassid=$runclassid&action=1\">");
      echo("$respfile</td>\n");
      echo(" <td>$stimfile</td>\n");
      
      echo("</a></td>\n");
      echo(" <td>(" . $row["id"] . ") ");
      //while ( $cellfilerow = mysql_fetch_array($cellfiledata) ) {
      //   echo($cellfilerow["id"] . ": " . $cellfilerow["stimfilefmt"]);
      //}
      echo("<td>");
      $cellfiledata=mysql_query("SELECT * FROM sCellFile WHERE rawid=$rawid");
      $cellfilerow=mysql_fetch_array($cellfiledata);
      
      if ($cellfilerow) {
        echo("<a href=\"$fncellfilelist?userid=$userid&sessionid=$sessionid&sessionid=$sessionid&bkmk=$bkmk".
             "&cellid=" . $cellrow["cellid"] . "\">");
        echo("-->mat done");
        echo("</a>");
      }
      if ($row["bad"] > 0) {
         echo("( **BAD** )");
      }
      echo("</td></tr>\n");
   }
   echo("<tr><td></td><td></td>");
   echo("    <td><a href=\"$fncellfileedit?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&masterid=$masterid&rawid=-1&action=0\">");
   echo("New file</a></td></tr>\n");
   echo("<tr><td></td><td></td>");
   echo("    <td><a href=\"$fncelledit?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&masterid=$masterid&action=1#Descent\">");
   echo("Post-descent</a></td></tr>\n");
   echo("</table>\n");
}

?>
<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>

<?php
echo("<table cellpadding=1>\n");
echo("<tr><td><b>" . $cellcat . ":</b> ");
echo("<a href=\"$fncelledit?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&masterid=-1&penid=$penid&action=0\">\n");
echo("New site</a></td>\n");
echo("</tr>\n");
echo("</table>\n");
?>

<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>

<?php
echo( "<p>$pencat <b>$penname</b>&nbsp;" );
echo("&nbsp;(<a href=\"$fnpenedit?userid=$userid&sessionid=$sessionid&penname=$penname&action=1&bkmk=$bkmk\">");
echo("Edit pen</a>)\n");
echo("&nbsp;(<a href=\"$fnpendump?userid=$userid&sessionid=$sessionid&penname=$penname&bkmk=$bkmk\">");
echo("Dump</a>)\n");
echo("&nbsp;(<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&penname=$prevpenname&bkmk=" . ($bkmk-1) . "\">");
echo("<-- $prevpenname</a>)\n");
echo("&nbsp;(<a href=\"celllist.php?userid=$userid&sessionid=$sessionid#$bkmk\">UP</a>)\n");
echo("&nbsp;(<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&penname=$nextpenname&bkmk=" . ($bkmk+1) . "\">");
echo("$nextpenname --></a>)</p>\n");
?>


