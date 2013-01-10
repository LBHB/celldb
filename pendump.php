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
   header ("Location: penedit.php?userid=$userid&penname=$penname");
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
$fff=1;
while ($penrow=mysql_fetch_array($pendata)) {
  // print extra blanks between penetrations
  if (0==$fff) {
    echo("<br><HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE><br>\n");
  }
  $fff=0;
  
  $penid=$penrow["id"];
  $penname=$penrow["penname"];
  $training=$penrow["training"];
  if ($training) {
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
  echo("<tr><td><tt>Animal:</td><td><tt>" . $penrow["animal"] . "</td></tr>\n");
  if (0==$training) {
    echo("<tr><td><tt>Well:</td><td><tt>" . $penrow["well"] . "</td></tr>\n");
  }
  echo("<tr><td><tt>Who:</td><td><tt>" . $penrow["who"] . "</td></tr>\n");
  echo("<tr><td><tt>Fix time:&nbsp;&nbsp;</td><td><tt>" . $penrow["fixtime"] . "</td></tr>\n");
  echo("<tr><td><tt>Water:</td><td><tt>" . $penrow["water"] * 1.0 . "&nbsp;ml</td></tr>\n");
  echo("<tr><td><tt>Weight:</td><td><tt>" . $penrow["weight"] * 1.0 . "&nbsp;$weightunits</td></tr>\n");
  //echo("<tr><td><tt>Mondist:</td><td><tt>" . $penrow["mondist"] * 1.0 . "&nbsp;cm</td></tr>\n");
  //echo("<tr><td><tt>Pix/deg:</td><td><tt>" . $penrow["etudeg"] * 1.0 . "</td></tr>\n");
  //echo("<tr><td><tt>Eye:</td><td><tt>" . $penrow["eye"] . "</td></tr>\n");
  echo("<tr><td><tt>Ear:</td><td><tt>" . $penrow["ear"] . "</td></tr>\n");
  
  echo("<tr><td valign=top><tt>Rack:</td><td colspan=3><tt>" . stringfilt($penrow["racknotes"]) . "</td></tr>\n");
  if (""!=$penrow["probenotes"]) {
    echo("<tr><td valign=top><tt>Probe:</td><td colspan=3><tt>" . $penrow["probenotes"] . "</td></tr>\n");
  }
  if (""!=$penrow["electrodenotes"]) {
    echo("<tr><td valign=top><tt>Electrode:&nbsp;</td><td colspan=3><tt>" . stringfilt($penrow["electrodenotes"]). "</td></tr>\n");
  }
  if (""!=$penrow["descentnotes"]) {
    echo("<tr><td valign=top><tt>Descent:</td></tr>");
    echo("<tr><td colspan=4><tt>" . stringfilt($penrow["descentnotes"]). "</td></tr>\n");
  }
  
  echo("</table>" );
  $ppd=$penrow["etudeg"] * 1.0;
  
  // load cells associated with this penetration
  $celldata = mysql_query("SELECT * FROM gCellMaster WHERE penid=$penid");
  
  while ( $cellrow = mysql_fetch_array($celldata) ) {
    $masterid=$cellrow["id"];
    echo("<table cellpadding=0 width=100%>\n");
    echo("<tr><td colspan=3>\n");
    echo("<a name=\"" . $cellrow["cellid"] . "\"></a>"); 
    echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
    echo("</td></tr>\n");
    echo("<table cellpadding=0 width=100%>\n");
    echo("<tr><td><tt><b>" . $cellcat .":</b></td><td><tt><b>" . 
         $cellrow["cellid"] . "</b></td></tr>\n");
    if (0==$training) {
      echo("<tr><td><tt>Depth:</td><td><tt>" . 
           $cellrow["depth"] . " clicks</td></tr>\n");
      echo("<tr><td><tt>Time:</td><td><tt>" . $cellrow["findtime"] . 
           "</td></tr>\n");
      echo("<tr><td valign=top><tt>Comments:</td>\n");
      echo("<td colspan=3><tt>" . stringfilt($cellrow["comments"]) . 
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
    }
    //echo("</table>\n");
    
    $rawfiledata = mysql_query("SELECT * FROM gDataRaw WHERE masterid=$masterid");
    
    // list each raw data file
    $count=0;
    while ( $rawrow = mysql_fetch_array($rawfiledata) ) {
      // display file names associated with this gDataRaw entry
      $count+=1;
      echo("<tr><td colspan=3>\n");
      echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
      echo("</td></tr>\n");
      //echo("<table cellpadding=0>\n");
      echo("<tr><td><tt>File $count:</td><td><tt>" . $rawrow["runclass"] . " (" . 
           $rawrow["stimclass"] . "/" . $rawrow["behavior"] . ")</td></tr>\n");
      echo("<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n");
      if (0==$training) {
        echo("<tr><td><tt>Isolation:</td><td><tt>");
        $singledata = mysql_query("SELECT * FROM gSingleRaw" .
                                  " WHERE masterid=$masterid" .
                                  " AND rawid=" . $rawrow["id"]);
        while ( $singrow = mysql_fetch_array($singledata) ) {
          echo($singrow["cellid"] . ": " . $singrow["isolation"] . 
               "&nbsp;&nbsp;");
        }
        echo("</td></tr>\n");
      }
      
      if (""!=$rawrow["parmfile"]) {
        echo("<tr><td><tt>Parm file:</td><td><tt>");
        echo($rawrow["resppath"] . $rawrow["parmfile"] . "</td></tr>\n");
      }
      if ((""!=$rawrow["resppath"] || ""!=$rawrow["respfile"]) &&
          $rawrow["respfile"][0]!="*") {
        echo("<tr><td><tt>Resp file:</td><td><tt>");
        echo($rawrow["resppath"] . $rawrow["respfile"] . "</td></tr>\n");
      }
      if (""!=$rawrow["stimpath"] || ""!=$rawrow["stimfile"]) {
        echo("<tr><td><tt>Stim:</td><td><tt>");
        echo($rawrow["stimpath"] . $rawrow["stimfile"] . "</td></tr>\n");
      }
      if (0!=$rawrow["corrtrials"] || 0!=$rawrow["trials"]) {
        echo("<tr><td><tt>Performance:</td><td><tt>");
        echo($rawrow["corrtrials"] . "/" . $rawrow["trials"] . "</td></tr>\n");
      }
      
      if (""!=$rawrow["parameters"]) {
        echo("<tr><td valign=top><tt>Parameters:</td>\n");
        echo("<td colspan=3><tt>" . stringfilt($rawrow["parameters"]) . "</td></tr>\n");
      }
      
      /// print baphy parameters, if they exist
      $sql="SELECT * FROM gData WHERE rawid=" . $rawrow["id"] . " AND parmtype=0 ORDER BY id";
      $parmdata=mysql_query($sql);
      if (mysql_num_rows($parmdata)>0) {
        echo(" <tr><td valign=top><tt>Parameters:</td>\n");
        echo("<td colspan=3>");
        echo("<table border=0 cellpadding=0 cellspacing=0><tr><td valign=top>\n\n");
        
        echo("<table>\n");
        while ($grow=mysql_fetch_array($parmdata)){
          echo("<tr><td><tt><font size=2>" . $grow["name"] . "</b>&nbsp;</td>\n");
          if (0==$grow["datatype"]) {
            echo("<td><tt><font size=2>". $grow["value"] . "</td>\n");
          } else {
            echo("<td><tt><font size=2>". $grow["svalue"] . "</td>\n");
          }
          echo("</tr>\n");
        }
        echo("</table>\n");
        
        echo("\n</td><td valign=top>\n\n");
        
        $sql="SELECT * FROM gData WHERE rawid=" . $rawrow["id"] . " AND parmtype=1 ORDER BY id";
        $parmdata=mysql_query($sql);
        echo("<table>\n");
        while ($grow=mysql_fetch_array($parmdata)){
          echo("<tr><td><tt><font size=2>" . $grow["name"] . "</b>&nbsp;</td>\n");
          if (0==$grow["datatype"]) {
            echo("<td><tt><font size=2>". $grow["value"] . "</td>\n");
          } else {
            echo("<td><tt><font size=2>". $grow["svalue"] . "</td>\n");
          }
          echo("</tr>\n");
        }
        echo("</table>\n");
        
        echo("\n</td></tr></table>\n");
      }
      echo("\n</td></tr>\n");

      echo("<tr><td valign=top><tt>Comments:</td>\n");
      echo("<td colspan=3><tt>" . stringfilt($rawrow["comments"]) . 
           "</td></tr>\n");
      

      echo("<tr>");
      
      if ($rawrow["bad"] > 0) {
        echo("<tr><td></td><td><tt><b>**BAD**</b></td></tr>\n");
      }
      
      //echo("</table>\n");
    }
    
    echo("<tr><td colspan=3>\n");
    echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
    echo("</td></tr>\n");
    //echo("<table cellpadding=1>\n");
    if ($training) {
      echo("<tr><td valign=top><tt><b>Remarks:</b></td>");
    } else {
      echo("<tr><td valign=top><tt>Descent:</td>");
    }
    echo("<td><tt>" . stringfilt($cellrow["descentnotes"]). "</td></tr>\n");
    echo("</table>" );
  }
  
  echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
  echo("<table cellpadding=0>\n");
  //echo("<tr><td><tt>Animal:</td><td><tt>" . $penrow["animal"]. "&nbsp;&nbsp;</td>\n");
  //echo("    <td><tt>Well:</td><td><tt>" . $penrow["well"] . "</td></tr>\n");
  echo("<tr><td><tt>Added by:&nbsp;</td><td><tt>" . $penrow["addedby"]. "&nbsp;&nbsp;</td>\n");
  echo("    <td><tt>Last mod:&nbsp;</td><td><tt>" . $penrow["lastmod"] . "</td></tr>\n");
  echo("</table>");
  
  echo( "<p><center><tt><b>*** END $pencat $penname ***</b></center></p>\n");
}

?>

</body>
</html>

