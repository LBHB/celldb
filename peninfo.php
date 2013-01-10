<?php
/*** CELLDB
peninfo.php - display information about a single pentration
created 2002 - SVD
***/

// global include: connect to db and get important basic info about user prefs
$min_sec_level=6;
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
   header ("Location: $fnpenedit?userid=$userid&sessionid=$sessionid&penname=$penname&animal=$animal&training=$training&bkmk=$bkmk&expand=$expand");
   exit;                 /* Make sure that code below does not execute */
}

// if there are rows, then there is an entry
$penrow=mysql_fetch_array($pendata);
$penid=$penrow["id"];
$penname=$penrow["penname"];
$pendate=$penrow["pendate"];
$training=$penrow["training"];
$animal=$penrow["animal"];

?>
<HTML>
<HEAD>
<TITLE>celldb - Penetration info - <?php echo($penname)?></TITLE>
</HEAD>

<?php
echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

$redirurl="$fnpeninfo?penname=$penname&bkmk=$bkmk&expand=$expand";
echo("<meta http-equiv=\"Refresh\" content=\"30; URL=$redirurl\">\n");

$sql="SELECT max(penname) as penname FROM gPenetration WHERE pendate<=\"$pendate\" AND penname<\"$penname\" AND animal=\"$animal\" AND training=$training";
$prevpendata=mysql_query($sql);
$tpenrow=mysql_fetch_array($prevpendata);
$prevpenname=$tpenrow["penname"];
//echo($sql);
$sql="SELECT min(penname) as penname FROM gPenetration WHERE pendate>=\"$pendate\" AND penname>\"$penname\" AND animal=\"$animal\" AND training=$training";
$nextpendata=mysql_query($sql);
$tpenrow=mysql_fetch_array($nextpendata);
$nextpenname=$tpenrow["penname"];
//echo($sql);
$sql="SELECT max(penname) as penname FROM gPenetration WHERE animal=\"$animal\" AND training=$training";
$lastpendata=mysql_query($sql);
$tpenrow=mysql_fetch_array($lastpendata);
$lastpenname=$tpenrow["penname"];

if (1==$training) {
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
echo("&nbsp;(<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&penname=$prevpenname&bkmk=" . ($bkmk-1) . "&expand=$expand\">");
echo("<-- $prevpenname</a>)\n");
if (1==$penrow["training"]) {
  echo("&nbsp;(<a href=\"celllist.php?userid=$userid&sessionid=$sessionid&showdata=behavior#$bkmk\">UP</a>)\n");
} else {
  echo("&nbsp;(<a href=\"celllist.php?userid=$userid&sessionid=$sessionid&showdata=cells#$bkmk\">UP</a>)\n");
}
echo("&nbsp;(<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&penname=$nextpenname&animal=" . $penrow["animal"] . "&training=" . $penrow["training"] . "&bkmk=" . ($bkmk+1) . "&expand=$expand\">");
echo("$nextpenname --></a>)\n");
if (""!=$nextpenname && $lastpenname!=$nextpenname) {
   echo("&nbsp;(<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&penname=$lastpenname&animal=" . $penrow["animal"] . "&training=" . $penrow["training"] . "&bkmk=" . ($bkmk+1) . "&expand=$expand\">");
   echo("$lastpenname ->|</a>)\n");
}
echo("</p>\n");

$sql="SELECT round(weight,0) as weight,round(water,1) as water" .    " FROM gHealth INNER JOIN gAnimal ON gHealth.animal_id=gAnimal.id" .
   " WHERE gAnimal.animal='".$penrow["animal"] . "'" .  
   " AND gHealth.date='". $penrow["pendate"] . "'"; 
$hdata=mysql_query($sql); 

echo("<table cellpadding=1>\n");
echo("<tr><td colspan=2><b>Animal:</b></td><td><a href=\"animals.php?userid=$userid&sessionid=$sessionid&animal=$animal\">" . $penrow["animal"]. "</a>&nbsp;</td>\n");
echo("    <td><b>Well:</b></td><td>" . $penrow["well"]. "</td></tr>\n");
echo("<tr><td colspan=2><b>Date:</b></td><td>" . $penrow["pendate"]. "&nbsp;</td>\n");
echo("    <td><b>Who:</b></td><td>" . $penrow["who"]. "</td></tr>\n");
echo("<tr><td colspan=2><b>Fix time:</b></td><td>" . $penrow["fixtime"]. "&nbsp;</td>\n");
echo("    <td><b># channels:</b></td><td>" . $penrow["numchans"] * 1.0 . "&nbsp;</td></tr>\n"); 
if ($hrow=mysql_fetch_array($hdata)) {
  echo("<tr><td colspan=2><b>Water:</b></td><td><a href=\"celldbstats.php?userid=$userid&sessionid=$sessionid&animal=$animal&timeframe=&statcode=water\">" . $hrow["water"] . "&nbsp;ml</a></td>\n");
  echo("    <td><b>Weight:</b></td><td><a href=\"celldbstats.php?userid=$userid&sessionid=$sessionid&animal=$animal&timeframe=&statcode=weight\">" . $hrow["weight"] * 1.0 . "&nbsp;$weightunits</a></td></tr>\n");
}
if ($expand=="pendetails") {
   echo("<tr><td valign=top><a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&penname=$penname&bkmk=$bkmk\">-(Hide)</a></td><td><b>Ear:</b></td><td>" . $penrow["ear"] . "</td></tr>\n");
   echo("<tr><td></td><td valign=top><b>Rack:</b></td><td colspan=3>" . $penrow["racknotes"] . "</td></tr>\n");
   echo("<tr><td></td><td valign=top><b>Probe:</b></td><td colspan=3><table><tr>\n");
   if (""!=$penrow["wellimfile"]) {
     $wellimfile=str_replace("/auto/data/nsl/common/photos/","photo/",
                             $penrow["wellimfile"]);
     //echo("<td><img src=\"" . ($wellimfile) . "\"></td>");
     echo("<td><img width=\"300\" src=\"wellimage.php?penid=".$penrow["id"]."\"></td>");
   }
   echo("<td valign=\"top\">" . $penrow["probenotes"] . "</td></td></tr></table>\n");
   echo("</td></tr>\n");
   echo("<tr><td></td><td valign=top><b>Electrode:</b></td><td colspan=3>" . $penrow["electrodenotes"] . "<br>\n");
   echo("</td></tr>\n");
   if (""!=$penrow["impedance"]) {
      echo("<tr><td></td><td valign=top><b>Impedance:</b></td>");
      echo("<td colspan=3><tt>" . stringfilt($penrow["impedance"]). "</td></tr>\n");
   }
   if (""!=$penrow["firstdepth"]) {
      echo("<tr><td></td><td valign=top><b>First spike:</b></td>");
      echo("<td colspan=3><tt>" . stringfilt($penrow["firstdepth"]). "</td></tr>\n");
   }
   if (""!=$penrow["descentnotes"]) {
      echo("<tr><td></td><td valign=top><b>Descent:</b></td>");
      echo("<td colspan=3><tt>" . stringfilt($penrow["descentnotes"]). "</td></tr>\n");
   }
 
   echo("<tr><td></td><td><b>Added by:</b></td><td>" . $penrow["addedby"]. "&nbsp;</td>\n");
   echo("    <td><b>Last mod:</b></td><td>" . $penrow["lastmod"] . "</td></tr>\n");
} else {
   echo("<tr><td valign=top><a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&penname=$penname&bkmk=$bkmk&expand=pendetails\"><b>+ Show details</b></a>");
}
echo("</table>" );
$ppd=$penrow["etudeg"] * 1.0;


// load cells associated with this penetration
$celldata = mysql_query("SELECT * FROM gCellMaster WHERE penid=$penid" .
                        " ORDER BY cellid, id");


while ( $cellrow = mysql_fetch_array($celldata) ) {
   $masterid=$cellrow["id"];
   echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
   echo("<a name=\"" . $cellrow["cellid"] . "\"></a>"); 
   
   $singledata = mysql_query("SELECT * FROM gSingleCell" .
                             " WHERE masterid=$masterid" .
                             " ORDER BY cellid,id");
   
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
     echo("&nbsp;&nbsp;CF: ". $row["cf"]);
     echo("&nbsp;&nbsp;BW: ". $row["bw"] . "\n");
     if ($row["crap"]>0) {
       echo("<b> *** CRAP ***</b>");
     }
     echo("</td>\n</tr>\n");
   }
   echo("</table>\n");
   
   //sort on last three characters of file name
   //$rawfiledata = mysql_query("SELECT * FROM gDataRaw" .
   //                           " WHERE masterid=$masterid" .
   //                           " ORDER BY RIGHT(respfile,3),id");
 
   if (isset($showbad) && $showbad>0) {
     $badstr="(<a href=\"$fnpeninfo?penname=$penname&bkmk=$bkmk&expand=$expand&showbad=0#" . $cellrow["cellid"] ."\">-Hide bad files</a>)"; 
     $badwhere="";
   } else {
     $badstr="(<a href=\"$fnpeninfo?penname=$penname&bkmk=$bkmk&expand=$expand&showbad=1#" . $cellrow["cellid"] ."\">+Show bad files</a>)"; 
     $badwhere=" AND not(bad)";
   }
   $sql="SELECT * FROM gDataRaw" .
     " WHERE masterid=$masterid $badwhere" .
     " ORDER BY id,respfile,parmfile";
   $rawfiledata = mysql_query($sql);
   //echo($sql . "<br>");

   // list each raw data file
   echo("<table cellpadding=1>\n<tr>");
   //echo("<td><b>Task</b></td>");
   //echo("<td><b>Class</b></td>\n");
   echo("<td><b>(RawID)</b></td>");
   echo("<td><b>Resp file</b></td>");
   //echo("<td><b>Stim</b></td>");
   if (1==$penrow["training"]) {
      echo("<td><b>Perf</b></td>");
   } else {
     echo("<td><b>Trials (Reps)</b></td>");
   }
   
   echo("<td>$badstr</td></tr>\n");
   while ( $row = mysql_fetch_array($rawfiledata) ) {
     // display file names associated with this gDataRaw entry
     
     $rawid=$row["id"];
     $runclassid=$row["runclassid"];
     $parmfile=basename($row["parmfile"]);
     $parmfull=$row["parmfile"];
     $resppath=$row["resppath"];
     
     if (strcmp($row["respfile"],'')==0) {
       $respfile=$row["matlabfile"];
     } else {
       $respfile=$row["respfile"];
     }
     if (""==$parmfile) {
       $parmfile=$respfile;
     }
     
     $stimfile=$row["stimfile"];
     $stimpath=$row["stimpath"];
     $stimclass=$row["stimclass"];
     if (""==$stimfile) {
       $stimfile=$stimclass;
     } else {
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
     }
     echo("<tr>\n");
     //echo(" <td>" . $row["task"] . "</td>\n");
     
     //echo(" <td><a href=\"$fncellfileedit?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&masterid=$masterid&rawid=$rawid&runclassid=$runclassid&action=1\">");
     //echo($row["runclass"] . "</a></td>\n");
     echo(" <td>($rawid)</td>");
     
     echo(" <td><a href=\"$fncellfileedit?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&masterid=$masterid&rawid=$rawid&runclassid=$runclassid&action=1\">");
     echo("$parmfile</td>\n");
     //echo(" <td>$stimfile</td>\n");

     if (1==$penrow["training"]) {
        if ($row["trials"]>0) {
           echo(" <td>" . $row["corrtrials"] . "/" . $row["trials"] . " (" .
                round($row["corrtrials"]/ $row["trials"] *100) . "%)</td>\n");
        } else {
           echo(" <td align=\"center\">--</td>\n");
        }
     } else {
        if ($row["trials"]>0 && $row["corrtrials"]>0) {
           echo(" <td>" . $row["corrtrials"] . "/" . $row["trials"] . " (" .
                round($row["corrtrials"]/ $row["trials"] *100) . "%)</td>\n");
        } elseif ($row["trials"]>0) {
           echo(" <td>" . $row["trials"] . " (" . $row["reps"] . ")</td>\n");
        } else {
           echo(" <td align=\"center\">--</td>\n");
        }
        echo("</td>\n");
       
     }
     //echo("</a></td>\n");
     $cellfiledata=mysql_query("SELECT DISTINCT channum,unit FROM sCellFile WHERE rawid=$rawid ORDER BY channum");
     
     echo("<td>\n");
     if (mysql_num_rows($cellfiledata)>0) {
       echo("<a href=\"$fncellfilelist?userid=$userid&sessionid=$sessionid".
            "&bkmk=$bkmk&cellid=" . $cellrow["cellid"] . "&animal=$animal&showunproc=0\">");
       echo("sorted:");
       while ($cellfilerow=mysql_fetch_array($cellfiledata)) {
         echo(" " . $cellfilerow["channum"] . "-" . $cellfilerow["unit"]);
       }
       echo("</a>");
     } else {
       echo("<a href=\"$fncellfilelist?userid=$userid&sessionid=$sessionid".
            "&bkmk=$bkmk&cellid=" . $cellrow["cellid"] . "&animal=$animal&showunproc=1\">");
       if ($parmfull[0]=="/" || $parmfull[1]==":") {
         // already contains path
         echo("not sorted");
       } elseif (substr($resppath,0,10)!="/auto/data") {
         $parmfile=$parmfile . "(MISSING?)";
         //$parmfile=$resppath . $parmfile;
         echo("not on server");
       } else {
         echo("not sorted");
       }
       echo("</a>");
       
     }
     
     // path to output image?
     $jpegfilebase=(str_replace(".m","",$row["parmfile"]));
     $jpegfilename=(str_replace(".m",".jpg",$row["parmfile"]));
     if ($expand=="parm" . $rawid) {
       echo(" - <a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&penname=$penname&bkmk=$bkmk#" . $row["cellid"] . "\">PARMS</a>");
     } else {
       echo(" - <a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&penname=$penname&bkmk=$bkmk&expand=parm$rawid#" . $row["cellid"] . "\">parms</a>");
     }
     $behavior_file="/var/www/celldb/behaviorcharts/" .
       strtolower($animal) . "/" . substr($pendate,0,4) . "/" .
       $jpegfilename;
     if (file_exists($behavior_file)) {
       echo(" - <a href=\"behaviorcharts/" . strtolower($animal) . "/" . substr($pendate,0,4) . "/$jpegfilename\">behavior</a>");
     }
     $dir="/var/www/celldb/analysis/" . strtolower($animal) . "/" . substr($pendate,0,4);
     if (file_exists($dir)) {
       $dh  = opendir($dir);
       while (false !== ($filename = readdir($dh))) {
         if (strstr($filename,$jpegfilebase)) {
           $fparts=explode(".",$filename);
           if ($fparts[1]!="jpg") {
             $analysis_str=$fparts[1];
           } else
             $analysis_str="analysis";
           
           echo(" - <a href=\"analysis/" . strtolower($animal) . "/" . substr($pendate,0,4) . "/$filename\">$analysis_str</a>");
         }
       }
     }
     if ($row["bad"] > 0) {
       echo("( **BAD** )");
     }
     echo("</td></tr>\n");
     $ff="<tt><font size=2>";
     if ($expand=="parm" . $rawid) {
        echo("<tr><td colspan=3>\n");
        $sql="SELECT * FROM gData WHERE rawid=$rawid AND parmtype=0 ORDER BY id";
        $parmdata=mysql_query($sql);
        if (mysql_num_rows($parmdata)==0) {
          echo("<b>No parameter data</b>");
        } else {
          echo("<table cellpadding=0 cellspacing=0>\n");
          while ($row=mysql_fetch_array($parmdata)){
            echo("<tr><td><b>$ff" . $row["name"] . "</b>&nbsp;</td>\n");
            if (0==$row["datatype"]) {
              echo("<td>$ff". $row["value"] . "</td>\n");
            } else {
              echo("<td>$ff". $row["svalue"] . "</td>\n");
            }
            echo("</tr>\n");
          }
          echo("</table>\n");
          
          echo("\n</td><td valign=top colspan=3>\n\n");
          
          $sql="SELECT * FROM gData WHERE rawid=$rawid AND parmtype=1 ORDER BY id";
          $parmdata=mysql_query($sql);
          echo("<table cellpadding=0 cellspacing=0>\n");
          while ($row=mysql_fetch_array($parmdata)){
            echo("<tr><td><b>$ff" . $row["name"] . "</b>&nbsp;</td>\n");
            if (0==$row["datatype"]) {
              echo("<td>$ff". $row["value"] . "</td>\n");
            } else {
              echo("<td>$ff". $row["svalue"] . "</td>\n");
            }
            echo("</tr>\n");
          }
          echo("</table>\n");
        }
        echo("</td></tr>\n");
     }
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
echo( "$pencat <b>$penname</b>&nbsp;" );
echo("&nbsp;(<a href=\"$fnpenedit?userid=$userid&sessionid=$sessionid&penname=$penname&action=1&bkmk=$bkmk\">");
echo("Edit pen</a>)\n");
echo("&nbsp;(<a href=\"$fnpendump?userid=$userid&sessionid=$sessionid&penname=$penname&bkmk=$bkmk\">");
echo("Dump</a>)\n");
echo("&nbsp;(<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&penname=$prevpenname&bkmk=" . ($bkmk-1) . "\">");
echo("<-- $prevpenname</a>)\n");
if (1==$penrow["training"]) {
  echo("&nbsp;(<a href=\"celllist.php?userid=$userid&sessionid=$sessionid&showdata=behavior#$bkmk\">UP</a>)\n");
} else {
  echo("&nbsp;(<a href=\"celllist.php?userid=$userid&sessionid=$sessionid&showdata=cells#$bkmk\">UP</a>)\n");
}
echo("&nbsp;(<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&penname=$nextpenname&bkmk=" . ($bkmk+1) . "\">");
echo("$nextpenname --></a>)\n");

cellfooter("<em>This page refreshes every 30 seconds.</em>");
?>


