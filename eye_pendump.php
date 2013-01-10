<?php
/*** CELLDB
pendump.php - display all information about a single pentration in 
               printable log format
created 2002 - SVD
***/

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";

// load data pre-existing data about the penetration -- if it exists
if ((-1==$penid or ""==$penid) and ""==$penname) {
   // only penname provided, see if it exists
   $sql="SELECT * FROM gPenetration WHERE well=$well AND animal=\"$animal\" ORDER BY penname";
} elseif (-1==$penid or ""==$penid) {
   // only penname provided, see if it exists
   $sql="SELECT * FROM gPenetration WHERE penname=\"$penname\"";
} else {
   // see if penid exists
   $sql="SELECT * FROM gPenetration WHERE id=$penid";
}

$pendata = mysql_query($sql);
$pendatarows=mysql_num_rows($pendata);
if ($pendatarows==0) {
   header ("Location: $fnpenedit?userid=$userid&penname=$penname");
   exit;                 /* Make sure that code below does not execute */
}

?>
<HTML>
<HEAD>
<TITLE>celldb - Penetration <?php echo($penname)?></TITLE>
</HEAD>
<BODY bgcolor="#FFFFFF">

<?php
// if there are rows, then there is an entry
while ($penrow=mysql_fetch_array($pendata)) {
  $penid=$penrow["id"];
  $penname=$penrow["penname"];
  
  if (1==$penrow["training"]) {
    $pencat="TRAINING SESSION";
    $cellcat="Pseudo-site";
  } else {
    $pencat="PENETRATION";
    $cellcat="Site";
  }
  
  echo( "<p><center><tt><b>*** $pencat $penname ***</b></center></p>\n");
  
  echo("<table cellpadding=0>\n");
  echo("<tr><td><tt>Pen:</td><td><tt>" . $penrow["penname"]);
  echo("&nbsp;&nbsp;&nbsp;" . $penrow["pendate"]. "</td></tr>\n");
  echo("<tr><td><tt>Who:</td><td><tt>" . $penrow["who"] . "</td></tr>\n");
  echo("<tr><td><tt>Fix time:</td><td><tt>" . $penrow["fixtime"] . "</td></tr>\n");
  echo("<tr><td><tt>Water:</td><td><tt>" . $penrow["water"] * 1.0 . "&nbsp;ml</td></tr>\n");
  echo("<tr><td><tt>Weight:</td><td><tt>" . $penrow["weight"] * 1.0 . "&nbsp;kg</td></tr>\n");
  echo("<tr><td><tt>Mondist:</td><td><tt>" . $penrow["mondist"] * 1.0 . "&nbsp;cm</td></tr>\n");
  echo("<tr><td><tt>Pix/deg:</td><td><tt>" . $penrow["etudeg"] * 1.0 . "</td></tr>\n");
  echo("<tr><td><tt>Eye:</td><td><tt>" . $penrow["eye"] . "</td></tr>\n");
  
  echo("<tr><td valign=top><tt>Rack:</td><td colspan=3><tt>" . stringfilt($penrow["racknotes"]) . "</td></tr>\n");
  echo("<tr><td valign=top><tt>Probe:</td><td colspan=3><tt>" . $penrow["probenotes"] . "</td></tr>\n");
  echo("<tr><td valign=top><tt>Electrode:&nbsp;</td><td colspan=3><tt>" . stringfilt($penrow["electrodenotes"]). "</td></tr>\n");
  //echo("<tr><td><tt>Impedance:</td><td><tt>" . $penrow["impedance"] . "</td></tr>\n");
  //echo("<tr><td valign=top><tt>Notes:</td><td colspan=3><tt>" . stringfilt($penrow["impedancenotes"]). "</td></tr>\n");
  //echo("<tr><td><tt>Stability:</td><td><tt>" . $penrow["stability"] . "</td></tr>\n");
  //echo("<tr><td valign=top><tt>Notes:</td><td colspan=3><tt>" . stringfilt($penrow["stabilitynotes"]). "</td></tr>\n");
  echo("<tr><td valign=top><tt>Descent:</td></tr>");
  echo("<tr><td colspan=4><tt>" . stringfilt($penrow["descentnotes"]). "</td></tr>\n");
  
  echo("</table>" );
  $ppd=$penrow["etudeg"] * 1.0;
  
  // load cells associated with this penetration
  $celldata = mysql_query("SELECT * FROM gCellMaster WHERE penid=$penid");
  
  while ( $cellrow = mysql_fetch_array($celldata) ) {
    $masterid=$cellrow["id"];
    echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
    echo("<a name=\"" . $cellrow["cellid"] . "\"></a>"); 
    echo("<table cellpadding=0 width=100%>\n");
    echo("<tr><td><tt><b>" . $cellcat .":</b></td><td><tt><b>" . 
         $cellrow["cellid"] . "</b></td></tr>\n");
    echo("<tr><td><tt>Depth:</td><td><tt>" . 
         $cellrow["depth"] . " clicks</td></tr>\n");
    echo("<tr><td><tt>Time:</td><td><tt>" . $cellrow["findtime"] . 
         "</td></tr>\n");
    //echo("<tr><td><tt>Polarity:</td><td><tt>" . $cellrow["polarity"] . 
    //     "</td></tr>\n");
    echo("<tr><td valign=top><tt>Comments:</td>\n");
    echo("<td colspan=2><tt>" . stringfilt($cellrow["comments"]) . 
         "</td></tr>\n");

    $singledata = mysql_query("SELECT * FROM gSingleCell" .
                              " WHERE masterid=$masterid");
    while ( $singrow = mysql_fetch_array($singledata) ) {
      echo("<tr><td><tt><b>Cell:</b></td>\n");
      echo("<td><tt><b>". $singrow["cellid"] . "</b></td></tr>\n");
      
      echo("<tr><td><tt>Area:</td><td><tt>" . $singrow["area"] . 
           "</td></tr>\n");
      echo("<tr><td valign=top><tt>Handplot:&nbsp;</td>\n");
      echo("<td colspan=3><tt>" . stringfilt($singrow["handplot"]) . 
           "</td></tr>\n");
      echo("<tr><td><tt>Final RF:</td>\n");
      echo("<td><tt>(x,y)=(" . $singrow["xoffset"] . ",");
      echo($singrow["yoffset"] . ")</td><td><tt>d=". 
           $singrow["rfsize"] . " pix ");
      echo("</td></tr>\n");
      
      if ($ppd > 0) {
        $xdeg=round($singrow["xoffset"]/$ppd,1);
        $ydeg=round($singrow["yoffset"]/$ppd,1);
        $ddeg=round($singrow["rfsize"]/$ppd,1);
        echo("<tr><td></td>\n");
        echo("<td><tt>(x,y)=($xdeg,$ydeg)</td>");
        echo("<td><tt>d=$ddeg deg ($ppd ppd)</td>\n");
      }
      echo("</tr>\n");
    }
    echo("</table>\n");
    
    $rawfiledata = mysql_query("SELECT * FROM gDataRaw WHERE masterid=$masterid");
    
    // list each raw data file
    $count=0;
    while ( $rawrow = mysql_fetch_array($rawfiledata) ) {
      // display file names associated with this gDataRaw entry
      $count+=1;
      echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
      echo("<table cellpadding=0>\n");
      echo("<tr><td><tt>Stim $count:</td><td><tt>" . 
           $rawrow["runclass"] . "</td></tr>\n");
      echo("<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n");
      echo("<tr><td><tt>Isolation:</td><td><tt>");
      $singledata = mysql_query("SELECT * FROM gSingleRaw" .
                                " WHERE masterid=$masterid" .
                                " AND rawid=" . $rawrow["id"]);
      while ( $singrow = mysql_fetch_array($singledata) ) {
        echo($singrow["cellid"] . ": " . $singrow["isolation"] . 
             "&nbsp;&nbsp;");
      }
      echo("</td></tr>\n");
      
      echo("<tr><td><tt>Sync pulse:</td><td><tt>" . $rawrow["syncpulse"] . 
           "</td></tr>\n");
      echo("<tr><td><tt>Vis conf:</td><td><tt>" . $rawrow["stimconf"] . 
           "</td></tr>\n");
      echo("<tr><td><tt>Sounds healthy:&nbsp;</td><td><tt>" . 
           $rawrow["healthy"] . "</td></tr>\n");
      echo("<tr><td><tt>Eyewin:</td><td><tt>" . $rawrow["eyewin"] . 
           "</td></tr>\n");
      echo("<tr><td><tt>Reward:</td><td><tt>" . $rawrow["juice"] . 
           "</td></tr>\n");
      echo("<tr><td><tt>Pype file:</td><td><tt>");
      echo($rawrow["resppath"] . $rawrow["respfile"] . "</td></tr>\n");
      echo("<tr><td><tt>Stim:</td><td><tt>");
      echo($rawrow["stimpath"] . $rawrow["stimfile"] . "</td></tr>\n");
      echo("<tr><td valign=top><tt>Comments:</td>\n");
      echo("<td colspan=3><tt>" . stringfilt($rawrow["comments"]) . "</td></tr>\n");
      
      echo("<tr>");
      //echo("<td><tt><a href=\"$fncellfileedit?userid=$userid&masterid=$masterid&rawid=$rawid&runclassid=$runclassid&action=1\">");
      //echo($row["runclass"] . "</a></td>\n");
      
      //$rawid=$rawrow["id"];
      //echo("<tr><td><tt>Raw ID:</td><td><tt>$rawid</td></tr>");
      //echo("<tr><td><tt>File IDs:</td><td><tt>");
      //$cellfiledata=mysql_query("SELECT * FROM sCellFile WHERE rawid=$rawid");
      //while ( $cellrawrow = mysql_fetch_array($cellfiledata) ) {
      //   echo($cellrawrow["id"] . " (" . $cellrawrow["stimfilefmt"] . ") ");
      //}
      //echo("</td></tr>\n");

      if ($rawrow["bad"] > 0) {
        echo("<tr><td></td><td><tt><b>**BAD**</b></td></tr>\n");
      }
      
      echo("</table>\n");
    }
    
    echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
    echo("<table cellpadding=1>\n");
    echo("<tr><td valign=top><tt>Descent:</td></tr>");
    echo("<tr><td><tt>" . stringfilt($cellrow["descentnotes"]). "</td></tr>\n");
    echo("</table>" );
  }
  
  echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
  echo("<table cellpadding=0>\n");
  echo("<tr><td><tt>Animal:</td><td><tt>" . $penrow["animal"]. "&nbsp;</td>\n");
  echo("    <td><tt>Well:</td><td><tt>" . $penrow["well"] . "</td></tr>\n");
  echo("<tr><td><tt>Added by:&nbsp;</td><td><tt>" . $penrow["addedby"]. "&nbsp;</td>\n");
  echo("    <td><tt>Last mod:&nbsp;</td><td><tt>" . parsetimestamp($penrow["lastmod"]) . "</td></tr>\n");
  echo("</table>");
  
  echo( "<p><center><tt><b>*** END PENETRATION $penname ***</b></center></p>\n");
  echo( "<br><br>\n");
}

?>

</body>
</html>

