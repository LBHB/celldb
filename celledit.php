<?php
/*** CELLDB
celledit.php - edit information about a recording site
created 2002 - SVD
***/

// userid - string id of user to go in added by
// masterid - id in gCellMaster
// action 0 - nothing
//        2 - edit/add gCellMaster entry

// global include: connect to db and get important basic info about user prefs
$min_sec_level=6;
include_once "./celldb.php";

$errormsg="";



// if save selected, save posted info to gCellMaster
if (2==$action) {
  
  if (-1==$masterid) {
    $newcell="New Cell";
  }
  
  // get data posted by user
  $formdata=$_REQUEST;
  $formdata["siteid"]=$formdata["cellid"];
  if (count($area) > 0) {
    $formdata["area"]=implode(",",$area);
  }
  for ($ii=0;$ii<count($depth);$ii++) {
    $depth[$ii]=sprintf("%d",$depth[$ii]);
  }
  if (count($depth) > 0) {
    $formdata["depth"]=implode(",",$depth);
  }
  for ($ii=0;$ii<count($bf);$ii++) {
    $bf[$ii]=sprintf("%d",$bf[$ii]);
  }
  if (count($bf) > 0) {
    $formdata["bf"]=implode(",",$bf);
  }

  // actually save/update the data
  $errormsg=savedata("gCellMaster",$masterid,$formdata);
  
  
  if (is_numeric($errormsg)) {
    $masterid=$errormsg;
    $errormsg="";
  }
  $errormsg2=$formdata["area"] . " / " . $formdata["depth"];
  
 }

if (2==$action && $masterid>-1) {
  $sql="UPDATE gDataRaw SET cellid=\"$cellid\"" .
    " WHERE masterid=$masterid";
  $result=mysql_query($sql);

  $sql="SELECT gPenetration.* FROM gPenetration, gCellMaster".
    " WHERE gPenetration.id=gCellMaster.penid AND gCellMaster.id=$masterid";
  $pendata = mysql_query($sql);
  if ($penrow=mysql_fetch_array($pendata)) {
    $numchans=$penrow["numchans"];
    $penid=$penrow["id"];
  } else {
    $errormsg="Penetration not found?";
    $goback="";
    $numchans=1;
  }
  // save singlecell info
  $chanstrings=array("p","a","b","c","d","e","f","g","h"); 
  for ($ii = 1; $ii <= $cellcount; $ii++) {
    $singdata=Array();
    $singdata["siteid"]=$cellid;
    if ("ear"==$view && 1==$numchans) {
      $singdata["cellid"]=$cellid . "-" . $unit[$ii];
    } elseif ("ear"==$view) {
      $singdata["cellid"]=$cellid . "-" . 
        $chanstrings[$channum[$ii]] . $unit[$ii];
    } elseif (1==$cellcount) {
      $singdata["cellid"]= $cellid;
    } else {
      $singdata["cellid"]=$cellid . $chanstrings[$channum[$ii]] . $unit[$ii];
    }
    $singdata["penid"]=$penid;
    $singdata["masterid"]=$masterid;
    $singdata["addedby"]=$addedby;
    $singdata["channum"]=$channum[$ii]*1;
    $singdata["unit"]=$unit[$ii]*1;
    $singdata["channel"]=$chanstrings[$channum[$ii]];
    
    //if (isset($cf)) { $singdata["cf"]=$cf[$ii]*1; }
    //if (isset($bw)) { $singdata["bw"]=$bw[$ii]*1; }
    //if (isset($latency)) { $singdata["latency"]=$latency[$ii]*1; }
    if (isset($area)) { $singdata["area"]=$area[$channum[$ii]-1]; }
    if (isset($handplot)) { $singdata["handplot"]=$handplot[$ii]; }
    if (isset($crap)) { $singdata["crap"]=$crap[$ii]; }
    if (isset($rfsize)) { $singdata["rfsize"]=$rfsize[$ii]*1; }
    if (isset($xoffset)) { $singdata["xoffset"]=$xoffset[$ii]*1; }
    if (isset($yoffset)) { $singdata["yoffset"]=$yoffset[$ii]*1; }
    if (isset($quality)) { $singdata["quality"]=$quality[$ii]; }
    
    $errormsg=savedata("gSingleCell",$singleid[$ii],$singdata);
    
    $sql="UPDATE gSingleRaw SET ".
      "cellid=\"" . $singdata["cellid"] . "\",".
      "masterid=$masterid,".
      "penid=$penid,".
      "channum=" . $channum[$ii] . ",".
      "unit=" . $unit[$ii] . ",".
      "channel=\"" . $chanstrings[$channum[$ii]] . "\",".
      "addedby=\"$addedby\",".
      "info=\"$siteinfo\"".
      " WHERE singleid=" . $singleid[$ii];
    $result=mysql_query($sql);
  }

  if (""!=$newcell) {
    if (count($channum)==0) {
      $newchannum=1;
    } else {
      $newchannum=max($channum)+0;
    }
    if (count($unit)==0) {
      $newunit=1;
    } else {
      $newunit=max($unit)+1;
    }
    
    $sql="INSERT INTO gSingleCell" .
      " (siteid,cellid,area,penid,masterid,channum,unit,addedby,info)" .
      " VALUES (\"$cellid\",\"$cellid\",\"".$area[$newchannum]."\",$penid,".
      "$masterid,$newchannum,$newunit,\"$addedby\",\"$siteinfo\")";
    $result=mysql_query($sql);
    $singleid=mysql_insert_id();
    //echo("$sql<br>");
    
    // create a gSingleRaw entry for new cell matched to every 
    // existing gDataRaw entry
    $sql="SELECT id FROM gDataRaw WHERE masterid=$masterid";
    $masterdata=mysql_query($sql);
    while ($masterrow=mysql_fetch_array($masterdata)) {
      $rawid=$masterrow["id"];
      $sql="INSERT INTO gSingleRaw" .
        " (cellid,masterid,singleid,penid,rawid,channum,unit,".
        "addedby,info)" .
        " VALUES (\"$cellid\",$masterid,$singleid,$penid,$rawid," .
        "$newchannum,$newunit,\"$addedby\",\"$siteinfo\")";
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
   $depth=$masterdata["depth"];
   $depth=explode(",",$depth);
   $area=$masterdata["area"];
   $area=explode(",",$area);
   $umperdepth=$masterdata["umperdepth"] * 1.0;
   $bf=$masterdata["bf"];
   $bf=explode(",",$bf);
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
   
   $sql="SELECT * FROM gPenetration WHERE id=$penid";
   $pendata=mysql_query($sql);
   $row=mysql_fetch_array($pendata);
   $numchans=$row["numchans"];
} else {
  // guess what some parameters will be 
  
  // $penid should be specified -- this gives animal and well
  if ($penid<=0) {
    $errormsg="penid not specified";
  }
  
  $sql="SELECT * FROM gPenetration WHERE id=$penid";
  $pendata=mysql_query($sql);
  $row=mysql_fetch_array($pendata);
  $animal=$row["animal"];
  $well=$row["well"];
  $training=$row["training"];
  $penname=$row["penname"];
  $numchans=$row["numchans"];
  
  //echo($sql . "<br>\n");
  
  // figure out defaults using last entry for that critter
  // kludge for compatibility with all lab naming schemes
  if ("eye"==$view) {
    // vision labs: base siteid on last siteid
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
      $umperdepth=$latestrow["umperdepth"];
    }
    
    if (1==$training) {
      $lastcellnum=substr($lastcellid,2);
      $cellid=sprintf("%st%04d",substr($animal,0,1),$lastcellnum+1);
    } else {
      $lastcellnum=substr($lastcellid,1);
      $cellid=sprintf("%s%04d",substr($animal,0,1),$lastcellnum+1);
    }

  } else {

    // auditory labs: base siteid on penname
    $sql="SELECT max(id) as maxid FROM gCellMaster WHERE penid=$penid";
    $latestcelldata=mysql_query($sql);
    $row=mysql_fetch_array($latestcelldata);
    $latestid=$row["maxid"];
    
    if (""==$latestid) {
      
      // this is the first site for this penetration
      $lastletter=chr(ord("a")-1);
      
    } else {
      $sql="SELECT * FROM gCellMaster WHERE id=$latestid";
      $latestcelldata=mysql_query($sql);
      $latestrow=mysql_fetch_array($latestcelldata);
      $lastcellid=$latestrow["cellid"];
      $lastletter=substr($lastcellid,-1,1);
      $umperdepth=$latestrow["umperdepth"];
    }
   
    $curletter=chr(ord($lastletter)+1);
    if (1==$training) {
      //moved T prefix to penname for NSL
      //$cellid=sprintf("%s%s-T",$penname,$curletter);
      $cellid=sprintf("%s%s",$penname,$curletter);
    } else {
      $cellid=sprintf("%s%s",$penname,$curletter);
    }
  }

  $depth="";
  $bf="";
  $findtime=date("g:i a");
  $comments="";
  $descentnotes="";
  
  $crap=0;
  $addedby=$userid;
 }

$runclassdata=mysql_query("SELECT DISTINCT id,name".
                          " FROM gRunClass ORDER BY id");

?>
<HTML>
<HEAD>
<TITLE>celldb - Edit site <?php echo($cellid)?></TITLE>
</HEAD>

<?php
echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">\n");

echo( "<b>Editing site $cellid</b>" );
echo("&nbsp;(<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&penid=$penid#$cellid\">Pen info</a>)\n");
echo("&nbsp;(<a href=\"celllist.php?userid=$userid&sessionid=$sessionid#$bkmk\">Pen list</a>)\n");

if (""!=$errormsg) {
  echo("<br><b><font color=\"#CC0000\">$errormsg</font></b>\n");
}
if (""!=$errormsg2) {
  echo("<br><b><font color=\"#CC0000\">$errormsg2</font></b>\n");
}

include "./" . $view . "_view_celledit.php";

?>
</BODY>
</HTML>
