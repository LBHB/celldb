<?php
/*** CELLDB
celledit.php - edit information about a recording site
created 2002 - SVD
***/

// userid - string id of user to go in added by
// masterid - id in gCellMaster
// action 0 - add file
//        1 - edit file
//        2 - do the edit/add and redirect back to cellinfo.php

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";

// if save selected, save posted info to gCellMaster
if (2==$action) {

   $sql="SELECT * FROM gCellMaster WHERE id=$masterid";
   $celldata = mysql_query($sql);
   $celldatarows=mysql_num_rows($celldata);
   
   // make sure all numeric variables are numeric
   $well=$well*1;
   $depth=$depth*1;
   $umperdepth=$umperdepth*1;
   $rfppd=$rfppd*1;
   $rfsize[1]=$rfsize[1]*1;
   $xoffset[1]=$xoffset[1]*1;
   $yoffset[1]=$yoffset[1]*1;
   $training=$training*1;
   $quality[1]=$quality[1]*1;
   $latency[1]=$latency[1]*1;
   $crap[1]=$crap[1]*1;
   
   if (0==$celldatarows) {
      // ie, gCellMaster entry doesn't exist yet. create a new
      // entry
      $sql="INSERT INTO gCellMaster" .
        " (siteid,cellid,animal,well,area,penid,".
        "depth,umperdepth,findtime,polarity,handplot,comments,".
        "descentnotes,rfppd,rfsize,xoffset,yoffset,eyecal,".
        "addedby,info,training)" .
        " VALUES (\"$cellid\",\"$cellid\",\"$animal\",$well,\"$area\",$penid,".
        "$depth,$umperdepth,\"$findtime\",\"$polarity\",".
        "\"$handplot\",\"$comments\",\"$descentnotes\",".
        "$rfppd," . $rfsize[1] . "," . $xoffset[1] . "," . 
        $yoffset[1] . ",\"$eyecal\",".
        "\"$addedby\",\"$siteinfo\",$training)";
      $result=mysql_query($sql);
      //echo mysql_errno().": ".mysql_error()."<BR>";
      //echo("$sql $result");
      //exit;
      $masterid=mysql_insert_id();
      $newcell="New Cell";
   } else {
     // gDataRaw entry does exist.  update with posted values
     
     $sql="UPDATE gCellMaster SET ".
       "siteid=\"$cellid\",".
       "cellid=\"$cellid\",".
       "animal=\"$animal\",".
       "well=$well,".
       "penid=$penid,".
       "depth=$depth,".
       "umperdepth=$umperdepth,".
       "findtime=\"$findtime\",".
       "comments=\"$comments\",".
       "descentnotes=\"$descentnotes\",".
       "rfppd=$rfppd,".
       "area=\"" . $area[1] . "\",".
       "rfsize=" . $rfsize[1] . ",".
       "xoffset=" . $xoffset[1] . ",".
       "yoffset=" . $yoffset[1] . ",".
       "crap=" . $crap[1] . ",".
       "latency=" . $latency[1] . ",".
       "quality=" . $quality[1] . ",".
       "handplot=\"" . $handplot[1] . "\",".
       "eyecal=\"$eyecal\",".
       "addedby=\"$addedby\",".
       "info=\"$siteinfo\",".
       "training=$training".
       " WHERE id=$masterid";
     //       "polarity=\"$polarity\",".
     $result=mysql_query($sql);
     
     $sql="UPDATE gDataRaw SET " .
       "cellid=\"$cellid\"" .
       " WHERE masterid=$masterid";
     $result=mysql_query($sql);
   }
   
   for ($ii = 1; $ii <= $cellcount; $ii++) {
     $unit[$ii]=$unit[$ii]*1;
     $rfsize[$ii]=$rfsize[$ii]*1;
     $xoffset[$ii]=$xoffset[$ii]*1;
     $yoffset[$ii]=$yoffset[$ii]*1;
     $quality[$ii]=$quality[$ii]*1;
     $latency[$ii]=$latency[$ii]*1;
     $crap[$ii]=$crap[$ii]*1;
     
     if (1==$cellcount && "p"==$channel[$ii]) {
       $cellid2= $cellid;
     } else {
       $cellid2= $cellid . $channel[$ii] . $unit[$ii];
     }
     
     $sql="UPDATE gSingleCell SET ".
       "siteid=\"$cellid\",".
       "cellid=\"$cellid2\",".
       "area=\"" . $area[$ii] . "\",".
       "penid=$penid,".
       "masterid=$masterid,".
       "latency=" . $latency[$ii] . ",".
       "handplot=\"" . $handplot[$ii] . "\",".
       "rfsize=" . $rfsize[$ii] . ",".
       "xoffset=" . $xoffset[$ii] . ",".
       "yoffset=" . $yoffset[$ii] . ",".
       "unit=" . $unit[$ii] . ",".
       "channel=\"" . $channel[$ii] . "\",".
       "quality=" . $quality[$ii] . ",".
       "crap=" . $crap[$ii] . ",".
       "addedby=\"$addedby\",".
       "info=\"$siteinfo\"".
       " WHERE id=" . $singleid[$ii];
     $result=mysql_query($sql);
     
     $sql="UPDATE gSingleRaw SET ".
       "cellid=\"$cellid2\",".
       "masterid=$masterid,".
       "penid=$penid,".
       "unit=" . $unit[$ii] . ",".
       "channel=\"" . $channel[$ii] . "\",".
       "addedby=\"$addedby\",".
       "info=\"$siteinfo\"".
       " WHERE singleid=" . $singleid[$ii];
     $result=mysql_query($sql);
   }
   
   if (""!=$newcell) {
     $sql="INSERT INTO gSingleCell" .
       " (siteid,cellid,area,penid,masterid,addedby,info)" .
       " VALUES (\"$cellid\",\"$cellid\",\"$area[$cellcount]\",$penid,".
       "$masterid,\"$addedby\",\"$siteinfo\")";
     $result=mysql_query($sql);
     $singleid=mysql_insert_id();
     //echo("$sql<br>");
     
     $sql="UPDATE gSingleCell set singleid=$singleid WHERE id=$singleid";
     $result=mysql_query($sql);
     //echo("$sql<br>");
     //exit;
     
     // create a gSingleRaw entry for new cell matched to every 
     // existing gDataRaw entry
     $sql="SELECT id FROM gDataRaw WHERE masterid=$masterid";
     $masterdata=mysql_query($sql);
     while ($masterrow=mysql_fetch_array($masterdata)) {
       $rawid=$masterrow["id"];
       $sql="INSERT INTO gSingleRaw" .
         " (cellid,masterid,singleid,penid,rawid,".
         "addedby,info)" .
         " VALUES (\"$cellid\",$masterid," . $singleid . ",$penid,".
         "$rawid," . "\"$addedby\",\"$siteinfo\")";
       //echo($sql . "<br>");
       $result=mysql_query($sql);
     }
     //exit;
   }
   
   // save last* to userprefs so that celllist shows the new cell
   $userdata = mysql_query("UPDATE gUserPrefs SET lastanimal=\"$animal\", lastwell=$well WHERE userid=\"$userid\"");

   if (""==$goback) {
     header("Location: $fncelledit?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&masterid=$masterid&action=1");
   } else {
     header("Location: $fnpeninfo?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&penid=$penid#$cellid");
   }
   exit;                 /* Make sure that code below does not execute */
}

// load data about the cell from gCellMaster
if (""!=$masterid and $masterid>-1) {
   $celldata = mysql_query("SELECT * FROM gCellMaster WHERE id=$masterid");
   $rowcount=mysql_num_rows($celldata);
   if ($rowcount==0) {
      $masterid=-1;
   }
} else {
   $masterid=-1;
}
if ($masterid>-1) {  
   $masterdata=mysql_fetch_array($celldata);
   $penid=$masterdata["penid"];
   $penname=$masterdata["penname"];
   $cellid=$masterdata["cellid"];
   $animal=$masterdata["animal"];
   $well=$masterdata["well"];
   $latency=$masterdata["latency"] * 1.0;
   $depth=$masterdata["depth"] * 1.0;
   $umperdepth=$masterdata["umperdepth"] * 1.0;
   $area=$masterdata["area"];
   $findtime=$masterdata["findtime"];
   $polarity=$masterdata["polarity"];
   $handplot=$masterdata["handplot"];
   $comments=$masterdata["comments"];
   $descentnotes=$masterdata["descentnotes"];
   $training=$masterdata["training"];
   
   $eyecal=$masterdata["eyecal"];
   $rfppd=$masterdata["rfppd"];
   $rfppd=round($rfppd*100)/100;
   $rfsize=$masterdata["rfsize"] * 1.0;
   $xoffset=$masterdata["xoffset"] * 1.0;
   $yoffset=$masterdata["yoffset"] * 1.0;
   $quality=$masterdata["quality"];
   $crap=$masterdata["crap"];
   $addedby=$masterdata["addedby"];
   $lastmod=$masterdata["lastmod"];

} else {
   // guess what some parameters will be 
   // $penid should be specified -- this gives animal and well
   if ($animal=="") {
      $row=mysql_fetch_array($userdata);
      $animal=$row["lastanimal"];
      $well=$row["lastwell"];
   }
   
   if ($penid<=0) {
      $sql="SELECT max(id) as maxid FROM gPenetration WHERE animal=\"$animal\"";
      $pendata=mysql_query($sql);
      $row=mysql_fetch_array($pendata);
      $penid=$row["maxid"];
   }
   
   $sql="SELECT * FROM gPenetration WHERE id=$penid";
   $pendata=mysql_query($sql);
   $row=mysql_fetch_array($pendata);
   $training=$row["training"];
   
   // figure out defaults using last entry for that critter
   $sql="SELECT max(id) as maxid FROM gCellMaster WHERE animal=\"$animal\"" .
     " AND training=$training";
   $latestcelldata=mysql_query($sql);
   $row=mysql_fetch_array($latestcelldata);
   $latestid=$row["maxid"];
   
   if (""==$latestid) {
     $lastcellid=sprintf("%st0000",substr($animal,0,1));
   
   } else {
     $sql="SELECT * FROM gCellMaster WHERE id=$latestid";
     $latestcelldata=mysql_query($sql);
     $latestrow=mysql_fetch_array($latestcelldata);
     $lastcellid=$latestrow["cellid"];
   }
   
   if (1==$training) {
     $lastcellnum=substr($lastcellid,2);
     $cellid=sprintf("%st%04d",substr($animal,0,1),$lastcellnum+1);
   } else {
     $lastcellnum=substr($lastcellid,1);
     $cellid=sprintf("%s%04d",substr($animal,0,1),$lastcellnum+1);
   }
   
   $latency="";
   $depth="";
   $umperdepth=0.5;
   $area="";
   $findtime=date("g:i a");
   $polarity="";
   $handplot="";
   $comments="";
   $descentnotes="";
   
   $eyecal="";
   $rfppd=$latestrow["rfppd"];
   $rfppd=round($rfppd*100)/100;
   $rfsize=0;
   $xoffset=0;
   $yoffset=0;
   $quality="";
   $crap=0;
   $addedby=$userid;
}

$runclassdata=mysql_query("SELECT DISTINCT id,name FROM gRunClass ORDER BY id");

?>
<HTML>
<HEAD>
<TITLE>celldb - Edit cell - <?php echo($cellid)?></TITLE>
</HEAD>
<BODY bgcolor="#FFFFFF">

<?php

if (""==$userid) {
   echo("<p><b>ERROR userid must be specified</b></p>");
}
echo( "<b>Editing site $cellid</b>" );
echo("&nbsp;(<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&penid=$penid#$cellid\">Pen info</a>)\n");
echo("&nbsp;(<a href=\"celllist.php?userid=$userid&sessionid=$sessionid#$bkmk\">Pen list</a>)\n");

echo("<FORM ACTION=\"$fncelledit\" METHOD=POST>");
echo(" <input type=\"hidden\" name=\"addedby\" value=\"$userid\">\n");
echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
echo(" <input type=\"hidden\" name=\"action\" value=2>\n");  // do the edit
echo(" <input type=\"hidden\" name=\"masterid\" value=$masterid>\n");
echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
echo(" <input type=\"hidden\" name=\"training\" value=$training>\n");
echo(" <input type=\"hidden\" name=\"bkmk\" value=$bkmk>\n");
echo("<table>\n");

// animal dropdown
$animaldata = mysql_query("SELECT DISTINCT animal FROM gCellMaster ORDER BY animal");
echo("<tr><td colspan=4><HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE></td></tr>\n");
echo("<tr><td>Animal:</td><td><select name=\"animal\" size=\"1\">");
if ($animal == "All") {
    $sel=" selected";
} else {
    $sel="";
}
echo(" <option value=\"All\" $sel>All</option>");
while ( $row = mysql_fetch_array($animaldata) ) {
   if ($animal == $row["animal"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option  value=\"" . $row["animal"] . "\"$sel>" . $row["animal"] . "</option>");
}
echo(" </select></td>\n");

// pentration dropdown
$pendata = mysql_query("SELECT DISTINCT id,penname FROM gPenetration" .
                       " WHERE animal=\"$animal\" AND well=$well ORDER BY penname");
echo("<td>Pen/Well:</td><td><select name=\"penid\" size=\"1\">");
if ($penid <= 0) {
    $sel=" selected";
} else {
    $sel="";
}
echo(" <option value=0 $sel>None</option>");
while ( $row = mysql_fetch_array($pendata) ) {
   if ( $penid == $row["id"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option value=" . $row["id"] . "$sel>" . $row["penname"] . "</option>");
}
echo(" </select> / \n");
echo("<INPUT TYPE=TEXT SIZE=5 NAME=\"well\" value=\"$well\"></td></tr>\n");

//info for all cells at this site
echo("<tr><td>Site ID:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"cellid\" value=\"$cellid\"></td>\n");
echo("<td>Time:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"findtime\" value=\"$findtime\"></td></tr>\n");
echo("<tr><td>Depth:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"depth\" value=\"$depth\">&nbsp;clicks");
echo(" (<INPUT TYPE=TEXT SIZE=5 NAME=\"umperdepth\" value=\"$umperdepth\">&nbsp;um/click)</td>\n");
echo("<td>PPD:</td>\n");
echo("<td><INPUT TYPE=TEXT SIZE=5 NAME=\"rfppd\" value=\"$rfppd\">");
echo("</td></tr>\n");

//echo("<tr><td>Stim offset (x,y):</td><td><INPUT TYPE=TEXT SIZE=5 NAME=\"xoffset\" value=\"$xoffset\">, \n");
//echo("<INPUT TYPE=TEXT SIZE=5 NAME=\"yoffset\" value=\"$yoffset\">&nbsp;pix</td>\n");
//echo("<td>Stim diam:</td>");
//echo("<td><INPUT TYPE=TEXT SIZE=5 NAME=\"rfsize\" value=\"$rfsize\">&nbsp;pix");

echo("<tr><td>Comments:</td><td colspan=3><textarea NAME=\"comments\" rows=4 cols=65>$comments</textarea></td></tr>\n");


echo("<tr><td colspan=4><HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE></td></tr>\n");

echo("<tr><td colspan=4>\n");
echo("<table>\n");
echo("<tr><td>Chan</td><td>Unit</td>");
echo("<td>RF (x,y)</td><td>Diam</td><td>Area</td><td>Lat</td>");
echo("<td>Crap</td><td>Handplot\n");
echo("<INPUT TYPE=SUBMIT VALUE=\"Save\"><INPUT TYPE=SUBMIT NAME=\"newcell\" VALUE=\"New Cell\"></td>\n");

//info specific to each cell
$singledata=mysql_query("SELECT * FROM gSingleCell".
                        " WHERE masterid=$masterid" .
                        " ORDER BY id");
$cellcount=0;
while ( $row = mysql_fetch_array($singledata) ) {
  $cellcount=$cellcount+1;
  
  echo("<tr><td>\n");
  echo("<input type=\"hidden\" name=\"singleid[$cellcount]\" value=" . 
       $row["id"] .">\n");
  
  //echo("<INPUT TYPE=TEXT SIZE=5 NAME=\"channel[$cellcount]\" value=\"" . $row["channel"] . "\"></td>\n");

  $chanstrings=array("p","a","b","c","d"); 
  $chanvals=array("p","a","b","c","d"); 
  echo("<select name=\"channel[$cellcount]\" size=\"1\">");
  for ($ii=0; $ii<count($chanstrings); $ii++) {
    if ($chanvals[$ii] == $row["channel"]) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option  value=\"" . $chanvals[$ii] . "\"$sel>".
         $chanstrings[$ii] . "</option>");
  }
  //for ($ii = "a"; $ii <= "d"; $ii++) {
  //  if ($ii == $row["channel"]) {
  //    $sel=" selected";
  //  } else {
  //    $sel="";
  //  }
  //  echo(" <option  value=\"$ii\"$sel>$ii</option>");
  //}
  echo("</td>\n");
  
  //echo("<td><INPUT TYPE=TEXT SIZE=5 NAME=\"unit[$cellcount]\" value=\"" . $row["unit"] . "\"></td>");
  echo("<td><select name=\"unit[$cellcount]\" size=\"1\">");
  for ($ii = 1; $ii <= 4; $ii++) {
    if ($ii == $row["unit"]) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option  value=\"$ii\"$sel>$ii</option>");
  }
  echo("</td>\n");
  
  echo("<td><INPUT TYPE=TEXT SIZE=4 NAME=\"xoffset[$cellcount]\" value=\"" . $row["xoffset"] . "\">, \n");
  echo("<INPUT TYPE=TEXT SIZE=4 NAME=\"yoffset[$cellcount]\" value=\"" . $row["yoffset"] . "\"></td>\n");
  echo("<td><INPUT TYPE=TEXT SIZE=4 NAME=\"rfsize[$cellcount]\" value=\"" . $row["rfsize"] . "\"></td>\n");
  
  echo("<td><INPUT TYPE=TEXT SIZE=5 NAME=\"area[$cellcount]\" value=\"" . $row["area"] . "\"></td>\n");
  echo("<td><INPUT TYPE=TEXT SIZE=4 NAME=\"latency[$cellcount]\" value=\"" . $row["latency"] . "\"></td>");
  
  // post recording details
  //echo("<tr><td>Quality:</td><td><INPUT TYPE=TEXT SIZE=4 NAME=\"quality[$cellcount]\" value=\"" . $row["quality"] . "\">&nbsp;(1 crap-10 perfect)</td>\n");
  echo("<td><INPUT TYPE=CHECKBOX NAME=\"crap[$cellcount]\" value=1");
  if (1==$row["crap"]) {
    echo(" checked");
  }
  echo("></td>\n");
  
  echo("<td><textarea NAME=\"handplot[$cellcount]\" rows=2 cols=35>" . $row["handplot"] . "</textarea></td>\n");
  
  echo("</tr>\n");
}

echo("</table>\n");
echo("<input type=\"hidden\" name=\"cellcount\" value=$cellcount>\n");
echo("</td></tr>\n");

// post cell descent
echo("<tr><td colspan=4><HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
echo("<a name=\"Descent\"></a>\n"); 
echo("<b>Descent</b> (after leaving cell):</td></tr>\n");
echo("<tr><td colspan=4 align=right><textarea NAME=\"descentnotes\" rows=12 cols=75>$descentnotes</textarea></td></tr>\n");
echo("<tr><td colspan=4><HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE></td></tr>\n");

echo("</table>\n");

echo("<INPUT TYPE=SUBMIT VALUE=\"Save\">");

echo("<INPUT TYPE=RESET VALUE=\"Reset\">");

echo("<INPUT TYPE=SUBMIT name=\"goback\" VALUE=\"Save & Quit\">");
echo(" <a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&penid=$penid#$cellid\">Go Back</a>\n");
echo("</FORM>\n");

?>
