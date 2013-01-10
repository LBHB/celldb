<?php
/*** CELLDB
cellfileedit.php - edit information about a single experimental data file
created 2002 - SVD
***/

// userid - string id of user to go in added by
// masterid - id in gCellMaster
//|       id | name     |
//|        0 | freeview |
//|        1 | review   |
//|        2 | gratrev  |
//|        3 | natrev   |
//|        4 | imview   |
// rawid - id in gDataRaw (-1 for add new entry)
// action 0 - add file
//        1 - edit file
//        2 - delete file?

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";

if (""!=$masterid) {
   $celldata = mysql_query("SELECT * FROM gCellMaster WHERE id=$masterid");
   $masterdata=mysql_fetch_array($celldata);
   $cellid=$masterdata["cellid"];
   $penid=$masterdata["penid"];
   $penname=$masterdata["penname"];
   $animal=$masterdata["animal"];
   $well=$masterdata["well"];
   $ppd=$masterdata["ppd"];
   $rppd=round($ppd*100)/100;
   $rfsize=$masterdata["rfsize"];
   $xoffset=$masterdata["xoffset"];
   $yoffset=$masterdata["yoffset"];
   $addedby=$masterdata["addedby"];
   $lastmod=$masterdata["lastmod"];

}
if (2==$action) {
   $sql="SELECT * FROM gDataRaw WHERE masterid=$masterid AND id=$rawid";
   $rawfiledata = mysql_query($sql);
   $rawfilerows=mysql_num_rows($rawfiledata);
   
   // figure out runclass to insert/update in gDataRaw
   $sql="SELECT DISTINCT id,name FROM gRunClass where id=$runclassid";
   $runclassdata=mysql_query($sql);
   $row=mysql_fetch_array($runclassdata);
   $runclass=$row["name"];

   // make sure all numeric variables are numeric
   $reps=$reps*1;
   $seclength=$seclength*1;
   $isolation0=$isolation0*1;
   $snr=$snr*1;
   $bad=$bad*1;
   $syncpulse=$syncpulse*1;
   $monitorfreq=$monitorfreq*1;
   $stimconf=$stimconf*1;
   $healthy=$healthy*1;
   $eyewin=$eyewin*1;
   $timejuice=$timejuice*1;
   $fixtime=$fixtime*1;
   $maxrate=$maxrate*1;
   $corrtrials=$corrtrials*1;
   $trials=$trials*1;
   $stimspeedid=$stimspeedid*1;
   $resppath=tidystr($resppath);
   
   if ($task=="NEW") {
     $task=$otask;
   }

   if (0==$rawfilerows) {
      // ie, gDataRaw entry doesn't exist yet. create a new
      // entry

      $sql="INSERT INTO gDataRaw" .
           " (cellid,masterid,runclassid,stimspeedid,runclass," .
           "resppath,stimfile,respfile,matlabfile,eyecalfile,".
           "plexonfile,time,task,".
           "reps,seclength,stimpath,isolation,snr,bad,".
           "syncpulse,monitorfreq,stimconf,healthy,eyewin,".
           "timejuice,fixtime,maxrate,comments,corrtrials,trials,".
           "addedby,info)" .
           " VALUES (\"$cellid\",$masterid,$runclassid,$stimspeedid,".
           "\"$runclass\"," .
           "\"$resppath\",\"$stimfile\",\"$respfile\",".
           "\"$matlabfile\","."\"$eyecalfile\",".
           "\"$plexonfile\",\"$time\",\"$task\",".
           "$reps,$seclength,\"$stimpath\",$isolation0,$snr,".
           "$bad,$syncpulse,$monitorfreq,$stimconf,$healthy,$eyewin,".
           "$timejuice,$fixtime,$maxrate,\"$comments\",$corrtrials,$trials,".
           "\"$addedby\",\"$siteinfo\")";
      $result=mysql_query($sql);
      $rawid=mysql_insert_id();
   } else {
      // gDataRaw entry does exist.  update with posted values
     
      $sql="UPDATE gDataRaw SET ".
           "runclassid=$runclassid,".
           "stimspeedid=$stimspeedid,".
           "runclass=\"$runclass\",".
           "resppath=\"$resppath\",".
           "stimfile=\"$stimfile\",".
           "respfile=\"$respfile\",".
           "matlabfile=\"$matlabfile\",".
           "eyecalfile=\"$eyecalfile\",".
           "plexonfile=\"$plexonfile\",".
           "time=\"$time\",".
           "task=\"$task\",".
           "reps=$reps,".
           "seclength=$seclength,".
           "stimpath=\"$stimpath\",".
           "isolation=$isolation0,".
           "snr=$snr,".
           "bad=$bad,".
           "syncpulse=$syncpulse,".
           "monitorfreq=$monitorfreq,".
           "stimconf=$stimconf,".
           "healthy=$healthy,".
           "eyewin=$eyewin,".
           "timejuice=$timejuice,".
           "fixtime=$fixtime,".
           "maxrate=$maxrate,".
           "comments=\"$comments\",".
           "corrtrials=$corrtrials,".
           "trials=$trials,".
           "addedby=\"$addedby\",".
           "info=\"$siteinfo\"".
           " WHERE id=$rawid";
      $result=mysql_query($sql);
   }
   
   for ($ii = 1; $ii <= $cellcount; $ii++) {

     $isolation[$ii]=$isolation[$ii]*1;
     $crap[$ii]=$crap[$ii]*1;

     $sql="SELECT * FROM gSingleCell WHERE id=" . $singleid[$ii];
     $singledata=mysql_query($sql);
     $singrow=mysql_fetch_array($singledata);
     $cellid0=$singrow["cellid"];
     $channel[$ii]=$singrow["channel"];
     $unit[$ii]=$singrow["unit"];
     if (-1==$singlerawid[$ii]) {
       // need to create a new gSingleRaw entry
       
       $sql="INSERT INTO gSingleRaw" .
         " (cellid,masterid,singleid,penid,".
         "rawid,channel,unit,".
         "isolation,crap,".
         "addedby,info)" .
         " VALUES (\"$cellid0\",$masterid," . $singleid[$ii] . ",$penid,".
         "$rawid,\"" . $channel[$ii] . "\",\"" . $unit[$ii] . "\",".
         $isolation[$ii] . ",". $crap[$ii] . ",".
         "\"$addedby\",\"$siteinfo\")";
       //echo($sql . "<br>");
       $result=mysql_query($sql);
       $singleid[$ii]=mysql_insert_id();
       
     } else {
       $sql="UPDATE gSingleRaw SET ".
         "cellid=\"$cellid0\",".
         "rawid=" . $rawid . ",".
         "isolation=" . $isolation[$ii] . ",".
         "channel=\"" . $channel[$ii] . "\",".
         "unit=" . $unit[$ii] . ",".
         "crap=" . $crap[$ii] . ",".
         "addedby=\"$addedby\",".
         "info=\"$siteinfo\"".
         " WHERE id=" . $singlerawid[$ii];
       $result=mysql_query($sql);

     }
   }
 
   header ("Location: $fnpeninfo?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&masterid=$masterid&penid=$penid#$cellid");
   exit;                 /* Make sure that code below does not execute */
}


?>
<HTML>
<HEAD>
<TITLE>celldb - Edit cell file - <?php echo($cellid)?></TITLE>
</HEAD>

<?php
echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

if (""==$userid or ""==$masterid) {
   echo("<p><b>ERROR masterid and userid must be specified</b></p>");
}
echo( "<p><b>Editing cell file ($cellid)</b>" );
echo("&nbsp;&nbsp;(<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&masterid=$masterid&penid=$penid#$cellid\">Pen info</a>)\n");
echo("  (<a href=\"celllist.php?userid=$userid&sessionid=$sessionid#$bkmk\">Cell list</a>)</p>\n");

?>

<?php
if ($rawid>0) {
   // raw data entry already exists.  load info from gDataRaw
   $sql="SELECT * FROM gDataRaw WHERE id=$rawid";
   $rawdata=mysql_query($sql);
   $rowcount=mysql_num_rows($rawdata);
   
   if ($rowcount==0) {
      $rawid=-1; // no entry currently exists in gDataRaw
   } else {
      $row = mysql_fetch_array($rawdata);
      // don't overwrite variables that have already been defined. 
      // hopefully this won't be a problem
      extract($row,EXTR_SKIP); 
      $snr=$snr * 1.0;
      $monitorfreq=$monitorfreq * 1.0;
      $eyewin=$eyewin * 1.0;
      $timejuice=$timejuice * 1.0;
      $fixtime=$fixtime * 1.0;
   }
} else {
   $rowcount=0;
}

if (-1==$rawid and $masterid>0) {
   // new rawdata. guess info from previous gDataRaw entry for this cell
   $sql="SELECT max(gDataRaw.id) as maxid" .
     " FROM gDataRaw, gPenetration, gCellMaster" .
     " WHERE gPenetration.id=gCellMaster.penid" .
     " AND gDataRaw.masterid=gCellMaster.id" .
     " AND gPenetration.animal=\"$animal\"";
   
   $rawdata=mysql_query($sql);
   $rowcount=mysql_num_rows($rawdata);
   $row=mysql_fetch_array($rawdata);
   
   if (""==$row["maxid"]) {
     $sql="SELECT max(id) as maxid FROM gDataRaw WHERE masterid=$masterid";
     $rawdata=mysql_query($sql);
     $rowcount=mysql_num_rows($rawdata);
     $row=mysql_fetch_array($rawdata);
   }

   if (""<>$row["maxid"]) {
     $rowcount=1;
     $sql="SELECT * FROM gDataRaw WHERE id=" . $row["maxid"];
     $lastrawdata=mysql_query($sql);
     $lastrow=mysql_fetch_array($lastrawdata);
     
     $stimspeedid=$lastrow["stimspeedid"];
     //$stimpath=$lastrow["stimpath"];
     $resppath=$lastrow["resppath"];
     $respfile=$lastrow["respfile"];
     $task=$lastrow["task"];
     $runclassid=$lastrow["runclassid"];
     
     //increment resp file name by one to guess the new entry
     $ii=strlen($respfile);
     if ($ii>5 and strcmp($respfile[$ii-4],'.')==0) {
       $ext=(substr($respfile,$ii-3,3) * 1.0) + 1;
       $respfile=substr($respfile,0,$ii-3);
       $respfile=sprintf("%s%03d",$respfile,$ext);
     }
     
     $time=date("g:i a");
     $monitorfreq=$lastrow["monitorfreq"] * 1.0;
     $eyewin=$lastrow["eyewin"] * 1.0;
     $timejuice=$lastrow["timejuice"] * 1.0;
     $fixtime=$lastrow["fixtime"] * 1.0;
   } else {
     $rowcount=0;
   }
}
if (0==$rowcount) {
  $sql="SELECT gPenetration.* FROM gPenetration,gCellMaster" .
    " WHERE gPenetration.id=gCellMaster.penid" .
    " AND gCellMaster.id=$masterid";
  $pendata=mysql_query($sql);
  $penrow=mysql_fetch_array($pendata);
  if ($penrow) {
    $resppath=$userrow["dataroot"] . $penrow["penname"] . "/";
  } else {
    $resppath=$userrow["dataroot"];
  }
  
  $time=date("g:i a");
  $stimspeedid=60;
  $stimfile="";
  $respfile="";
  $matlabfile="";
  $eyecalfile="";
  $plexonfile="";
  $stimpath="";
  $reps="";
  $seclength="";
  $isolation0="";
  $snr="";
  $bad="";
  $syncpulse="";
  $monitorfreq="";
  $stimconf="";
  $healthy="";
  $eyewin="";
  $timejuice="";
  $maxrate="";
  $comments="";
  $corrtrials="";
  $trials="";
}

echo("<FORM ACTION=\"$fncellfileedit\" METHOD=POST>");
echo(" <input type=\"hidden\" name=\"addedby\" value=\"$userid\">\n");
echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
echo(" <input type=\"hidden\" name=\"action\" value=2>\n");  // do the edit
echo(" <input type=\"hidden\" name=\"masterid\" value=$masterid>\n");
echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
echo(" <input type=\"hidden\" name=\"rawid\" value=$rawid>\n");
echo(" <input type=\"hidden\" name=\"cellid\" value=\"$cellid\">\n");
echo(" <input type=\"hidden\" name=\"bkmk\" value=\"$bkmk\">\n");
echo("<table>\n");
echo("<tr><td colspan=4><HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE></td></tr>");

echo("<tr><td>Task:</td><td><select name=\"task\" size=1>");
$taskdata=mysql_query("SELECT DISTINCT task FROM gDataRaw" .
                      " WHERE not(isnull(task))" .
                      " AND (addedby=\"$userid\" OR id=$rawid)" .
                      " ORDER BY task");
echo(" <option value=\"\"$sel>---</option>");
while ( $row = mysql_fetch_array($taskdata) ) {
   if ($task == $row["task"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option value=\"" . $row["task"] . "\"$sel>" . $row["task"] . "</option>");
}
echo(" <option value=\"NEW\"$sel>NEW</option>");
echo(" </select></td>\n");
echo("<td>NEW TASK:</td><td><INPUT TYPE=TEXT SIZE=20 NAME=\"otask\" value=\"$task\"></td></tr>\n");

echo("<tr><td>Run class:</td><td><select name=\"runclassid\" size=1>");
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
echo(" </select></td>\n");
echo("<td>NEW RUN CLASS:</td><td><INPUT TYPE=TEXT SIZE=20 NAME=\"orunclass\" value=\"\"></td></tr>\n");

$speeddata=mysql_query("SELECT DISTINCT stimspeedid" .
                       " FROM gDataRaw ORDER BY stimspeedid");

echo("<tr><td>Speed:</td><td><select name=\"stimspeedid\" size=1>");
while ( $row = mysql_fetch_array($speeddata) ) {
   if ($stimspeedid == $row["stimspeedid"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option value=" . $row["stimspeedid"] . "$sel>" . 
        $row["stimspeedid"] . " Hz </option>");
}
echo(" </select></td>\n");

echo("<td>Time:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"time\" value=\"$time\"></td></tr>\n");
echo("<tr><td>Resp path:</td><td colspan=3><INPUT TYPE=TEXT SIZE=60 NAME=\"resppath\" value=\"$resppath\"></td></tr>\n");
echo("<tr><td>Resp file:</td><td colspan=3><INPUT TYPE=TEXT SIZE=60 NAME=\"respfile\" value=\"$respfile\"></td></tr>\n");

//echo("<tr><td>Isolation:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"isolation0\" value=\"$isolation0\">&nbsp;%</td>\n");
//echo("<td>S/N:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"snr\" value=\"$snr\"></td></tr>\n");

echo("<tr><td>BAD FILE:</td><td><INPUT TYPE=CHECKBOX NAME=\"bad\" value=1");
if (1==$bad) {
   echo(" checked");
}
echo("></td>\n");

echo("<td>Sync pulse ok:</td><td><INPUT TYPE=CHECKBOX NAME=\"syncpulse\" value=1");
if (1==$syncpulse) {
   echo(" checked");
}
echo("></td></tr>\n");
echo("<tr><td>Monitor freq:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"monitorfreq\" value=\"$monitorfreq\">&nbsp;Hz</td>\n");
echo("<td>Vis conf stim:</td><td><INPUT TYPE=CHECKBOX NAME=\"stimconf\" value=1");
if (1==$stimconf) {
   echo(" checked");
}
echo("></td></tr>\n");
echo("<tr><td>Eye win:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"eyewin\" value=\"$eyewin\">&nbsp;pix</td>\n");
echo("<td>Sounds healthy:</td><td><INPUT TYPE=CHECKBOX NAME=\"healthy\" value=1");
if (1==$healthy) {
   echo(" checked");
}
echo("></td></tr>\n");
//echo("<tr><td>Juice:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"timejuice\" value=\"$timejuice\"></td></tr>\n");

echo("<tr><td colspan=4><HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE></td></tr>");

//info specific to each cell
$singledata=mysql_query("SELECT * FROM gSingleCell".
                        " WHERE masterid=$masterid".
                        " ORDER BY id");
$cellcount=0;
while ( $row = mysql_fetch_array($singledata) ) {
  $cellcount=$cellcount+1;
  
  $singleid=$row["id"];
  $singlerawdata=mysql_query("SELECT * FROM gSingleRaw".
                             " WHERE singleid=$singleid AND rawid=$rawid");
  if (0==mysql_num_rows($singlerawdata)) {
    $singlerawid=-1;
    $sisolation="";
    $scrap=$row["crap"];
  } else {
    $singrow=mysql_fetch_array($singlerawdata);
    $singlerawid=$singrow["id"];
    $sisolation=$singrow["isolation"];
    $scrap=$singrow["crap"];
  }
  
  echo("<tr><td>\n");
  // singleid is gSingleCell.id for this singleraw combo
  echo("<input type=\"hidden\" name=\"singleid[$cellcount]\" value=" . 
       $singleid .">\n");
  echo("<input type=\"hidden\" name=\"singlerawid[$cellcount]\" value=" . 
       $singlerawid .">\n");
  echo("<b>" . $row["cellid"] . ":</b></td>\n");
  
  // post recording details
  echo("<td>Iso: <INPUT TYPE=TEXT SIZE=4 NAME=\"isolation[$cellcount]\" value=\"$sisolation\">&nbsp;%</td><td>Crap:</td>\n");
  echo("<td><INPUT TYPE=CHECKBOX NAME=\"crap[$cellcount]\" value=1");
  if (1==$scrap) {
    echo(" checked");
  }
  echo("></td>\n");
  
  echo("</tr>\n");
}
echo("<input type=\"hidden\" name=\"cellcount\" value=$cellcount>\n");

echo("<tr><td>Comments:</td><td colspan=3><textarea NAME=\"comments\" rows=4 cols=60>$comments</textarea></td></tr>\n");

echo("<tr><td colspan=4><HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE></td></tr>");
echo("<tr><td>Performance:</td><td><INPUT TYPE=TEXT SIZE=5 NAME=\"corrtrials\" value=\"$corrtrials\">\n");
echo(" / <INPUT TYPE=TEXT SIZE=5 NAME=\"trials\" value=\"$trials\"> trials</td></tr>\n");
echo("<tr><td>Stim path:</td><td colspan=3><INPUT TYPE=TEXT SIZE=60 NAME=\"stimpath\" value=\"$stimpath\"></td></tr>\n");
echo("<tr><td>Stim file:</td><td colspan=3><INPUT TYPE=TEXT SIZE=60 NAME=\"stimfile\" value=\"$stimfile\"></td></tr>\n");
echo("<tr><td>Eyecal file:</td><td colspan=3><INPUT TYPE=TEXT SIZE=60 NAME=\"eyecalfile\" value=\"$eyecalfile\"></td></tr>\n");
echo("<tr><td>Matlab file:</td><td colspan=3><INPUT TYPE=TEXT SIZE=60 NAME=\"matlabfile\" value=\"$matlabfile\"></td></tr>\n");
echo("<tr><td>Plexon file:</td><td colspan=3><INPUT TYPE=TEXT SIZE=60 NAME=\"plexonfile\" value=\"$plexonfile\"></td></tr>\n");


echo("</table>\n");
//echo("Stimulus make: <INPUT TYPE=TEXT NAME=\"vn\" value=\"$vn\"><br>")

echo("<INPUT TYPE=SUBMIT VALUE=\"Save\">");
//echo("<INPUT TYPE=RESET VALUE=\"Reset\">");
echo("&nbsp;&nbsp;<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&masterid=$masterid&penid=$penid#$cellid\">Cancel</a>\n");
echo("</FORM>\n");

?>

</HTML>

