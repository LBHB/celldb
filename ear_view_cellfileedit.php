<?php

/// figure out if there is anything stored in gData because this will affect whether other things get displayed
$sql="SELECT * FROM gData WHERE rawid=$rawid AND parmtype=0 ORDER BY id";
$parmdata=mysql_query($sql);
if (mysql_num_rows($parmdata)==0) {
  $gdataexists=0;
} else {
  $gdataexists=1;
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

// svd 6/1/06 removed javascript stuff since this is mostly matlab-driven now
if (1 || $training) {
  $ustr1="";
  $ustr2="";
 } else {
  $ustr1="onchange=\"updateguesses()\"";
  $ustr2="onchange=\"updateguesses2()\"";
 }

echo("<tr><td>Run class:</td><td><select name=\"runclassid\" $ustr1>");
$runclassdata=mysql_query("SELECT DISTINCT id,name" .
                          " FROM gRunClass ORDER BY name,id");
while ( $row = mysql_fetch_array($runclassdata) ) {
   if ($runclassid == $row["id"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option value=" . $row["id"] . "$sel>" . $row["name"] . "</option>");
}
echo(" </select></td>\n");
echo("<td></td>\n");

$sql="SELECT DISTINCT stimclass FROM gDataRaw" .
  " WHERE not(isnull(stimclass))" .
  " AND (addedby=\"$userid\" OR cellid=\"$siteid\")" .
  " ORDER BY stimclass";
echo("<tr><td>Stim class:</td><td><select name=\"stimclass\" $ustr1>");
$stimclassdata=mysql_query($sql);
echo(" <option value=\"NEW\">NEW</option>");
while ( $row = mysql_fetch_array($stimclassdata) ) {
   if ($stimclass == $row["stimclass"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option value=\"" . $row["stimclass"] . "\"$sel>" . $row["stimclass"] . "</option>");
}
echo(" </select></td>\n");
echo("<td>NEW STIMCLASS:</td><td><INPUT TYPE=TEXT SIZE=20 NAME=\"ostimclass\" value=\"$stimclass\"></td></tr>\n");

$sql="SELECT DISTINCT task FROM gDataRaw" .
  " WHERE not(isnull(task))" .
  " AND (addedby=\"$userid\" OR cellid=\"$siteid\")" .
  " ORDER BY task";
echo("<tr><td>Task:</td><td><select name=\"task\" $ustr1>");
$taskdata=mysql_query($sql);
echo(" <option value=\"NEW\"$sel>NEW</option>");
while ( $row = mysql_fetch_array($taskdata) ) {
   if ($task == $row["task"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option value=\"" . $row["task"] . "\"$sel>" . $row["task"] . "</option>");
}
echo(" </select></td>\n");
echo("<td>NEW TASK:</td><td><INPUT TYPE=TEXT SIZE=20 NAME=\"otask\" value=\"$task\"></td></tr>\n");

$behstrings=array("Active","Passive (trained)","Naive");
$behvals=array("active","passive","naive");

echo("<tr><td>Behavior:</td><td><select name=\"behavior\" $ustr1>");
for ($ii=0; $ii<count($behstrings); $ii++) {
  if ($behvals[$ii]==$behavior) {
    $ss="selected";
  } else {
    $ss="";
  }
  echo(" <option value=\"" . $behvals[$ii] . "\" $ss>" .
       $behstrings[$ii] . "</option>\n");
}
echo("</select>\n");

echo("<td>Time:</td><td><INPUT TYPE=TEXT SIZE=20 NAME=\"time\" value=\"$time\"></td>\n");

echo("<tr><td>Parameter file:</td><td colspan=3><INPUT TYPE=TEXT SIZE=80 NAME=\"parmfile\" value=\"$parmfile\" $ustr2></td></tr>\n");
echo("<tr><td>Data path:</td><td colspan=3><INPUT TYPE=TEXT SIZE=80 NAME=\"resppath\" value=\"$resppath\"></td></tr>\n");
echo("<tr><td>Raw data file/path:</td><td colspan=3><INPUT TYPE=TEXT SIZE=80 NAME=\"respfile\" value=\"$respfile\"></td></tr>\n");
echo("<tr><td>Stim path:</td><td colspan=3><INPUT TYPE=TEXT SIZE=80 NAME=\"stimpath\" value=\"$stimpath\"></td></tr>\n");
echo("<tr><td>Pupil video:</td>");
echo("<td colspan=3><INPUT TYPE=CHECKBOX NAME=\"eyewin\" value=1");
if (1==$eyewin) {
   echo(" checked");
}
echo("<INPUT TYPE=TEXT SIZE=80 NAME=\"eyecalfile\" value=\"$eyecalfile\"></td></tr>\n");
//echo("<tr><td>Stim file:</td><td colspan=3><INPUT TYPE=TEXT SIZE=80 NAME=\"stimfile\" value=\"$stimfile\"></td></tr>\n");

echo("<tr><td>BAD FILE:</td><td><INPUT TYPE=CHECKBOX NAME=\"bad\" value=1");
if (1==$bad) {
   echo(" checked");
}
echo("></td>\n");

echo("<td>Eq callibrated:</td><td><INPUT TYPE=CHECKBOX NAME=\"syncpulse\" value=1");
if (1==$syncpulse) {
   echo(" checked");
}
echo("></td></tr>\n");
echo("<tr><td>Aud conf stim:</td><td><INPUT TYPE=CHECKBOX NAME=\"stimconf\" value=1");
if (1==$stimconf) {
   echo(" checked");
}
echo("></td>\n");
echo("<td>Sounds healthy:</td><td><INPUT TYPE=CHECKBOX NAME=\"healthy\" value=1");
if (1==$healthy) {
   echo(" checked");
}
echo("></td></tr>\n");

if (!$gdataexists) {
  echo("<tr><td>Parameters:</td><td colspan=3><textarea NAME=\"parameters\" rows=4 cols=60>$parameters</textarea></td></tr>\n");
}

echo("<tr><td>Comments:</td><td colspan=3><textarea NAME=\"comments\" rows=4 cols=60>$comments</textarea></td></tr>\n");

if (!$gdataexists) {
  echo("<tr><td>Performance:</td><td><INPUT TYPE=TEXT SIZE=5 NAME=\"corrtrials\" value=\"$corrtrials\">\n");
  echo(" / <INPUT TYPE=TEXT SIZE=5 NAME=\"trials\" value=\"$trials\"> trials</td>\n");
  echo("<td>Reps:</td><td><INPUT TYPE=TEXT SIZE=5 NAME=\"reps\" value=\"$reps\"></td></tr>\n");
}

echo("<tr><td colspan=4><HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE></td></tr>");

//info specific to each cell
$singledata=mysql_query("SELECT * FROM gSingleCell".
                        " WHERE masterid=$masterid".
                        " ORDER BY cellid,id");
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
    $schannum=$row["channum"];
    $sunit=$row["unit"];
    $schannel=$row["channel"];
  } else {
    $singrow=mysql_fetch_array($singlerawdata);
    $singlerawid=$singrow["id"];
    $sisolation=$singrow["isolation"];
    $scrap=$singrow["crap"];
    $schannum=$singrow["channum"];
    $sunit=$singrow["unit"];
    $schannel=$singrow["channel"];
  }
  
  echo("<tr><td>\n");
  // singleid is gSingleCell.id for this singleraw combo
  echo("<input type=\"hidden\" name=\"singleid[$cellcount]\" value=" . 
       $singleid .">\n");
  echo("<input type=\"hidden\" name=\"singlerawid[$cellcount]\" value=" . 
       $singlerawid .">\n");
  echo("<b>" . $row["cellid"] . ":</b></td>\n");
  
  // rawfile-specific details for this cell
      
  echo("<td>");
  echo("<select name=\"channum[$cellcount]\" size=\"1\">");
  for ($ii=0; $ii<count($chanstrings); $ii++) {
    if ($chanvals[$ii] == $schannum) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option value=\"" . $chanvals[$ii] . "\"$sel>".
         $chanstrings[$ii] . "</option>");
  }
  echo("</select>\n");
  
  echo("<select name=\"unit[$cellcount]\" size=\"1\">");
  for ($ii = 1; $ii <= 8; $ii++) {
    if ($ii == $sunit) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option value=\"$ii\"$sel>$ii</option>");
  }
  echo("</select>\n");
  
  echo("Iso: <INPUT TYPE=TEXT SIZE=4 NAME=\"isolation[$cellcount]\" value=\"$sisolation\">&nbsp;%</td><td>Crap:</td>\n");
  echo("<td><INPUT TYPE=CHECKBOX NAME=\"crap[$cellcount]\" value=1");
  if (1==$scrap) {
    echo(" checked");
  }
  echo("></td>\n");
  
  echo("</tr>\n");
}
echo("<input type=\"hidden\" name=\"cellcount\" value=$cellcount>\n");

echo("<tr><td colspan=4><HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE></td></tr>");

echo("<tr><td>EVP file:</td><td colspan=3><INPUT TYPE=TEXT SIZE=80 NAME=\"respfileevp\" value=\"$respfileevp\"></td></tr>\n");

//echo("<tr><td>Eyecal file:</td><td colspan=3><INPUT TYPE=TEXT SIZE=80 NAME=\"eyecalfile\" value=\"$eyecalfile\"></td></tr>\n");
echo("<tr><td>Spike file:</td><td colspan=3><INPUT TYPE=TEXT SIZE=80 NAME=\"matlabfile\" value=\"$matlabfile\"></td></tr>\n");
//echo("<tr><td>Plexon file:</td><td colspan=3><INPUT TYPE=TEXT SIZE=60 NAME=\"plexonfile\" value=\"$plexonfile\"></td></tr>\n");

echo("<tr><td colspan=4><HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE></td></tr>");
echo("<tr><td colspan=4><INPUT TYPE=SUBMIT VALUE=\"Save\">");
echo("&nbsp;&nbsp;<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&masterid=$masterid&penid=$penid#$cellid\">Cancel</a></td></tr>\n");

echo("<tr><td colspan=4><HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE></td></tr>");
if ($gdataexists) {
  echo(" <tr><td valign=top>Baphy data:</td>\n");
  echo("<td colspan=3>");
  echo("<table border=0 cellpadding=0 cellspacing=0><tr><td valign=top>\n\n");
  
  echo("<table>\n");
  while ($row=mysql_fetch_array($parmdata)){
    echo("<tr><td><b>" . $row["name"] . "</b>&nbsp;</td>\n");
    if (0==$row["datatype"]) {
      echo("<td>". $row["value"] . "</td>\n");
    } else {
      echo("<td>". $row["svalue"] . "</td>\n");
    }
    echo("</tr>\n");
  }
  echo("</table>\n");

  echo("\n</td><td valign=top>\n\n");

  $sql="SELECT * FROM gData WHERE rawid=$rawid AND parmtype=1 ORDER BY id";
  $parmdata=mysql_query($sql);
  echo("<table>\n");
  while ($row=mysql_fetch_array($parmdata)){
    echo("<tr><td><b>" . $row["name"] . "</b>&nbsp;</td>\n");
    if (0==$row["datatype"]) {
      echo("<td>". $row["value"] . "</td>\n");
    } else {
      echo("<td>". $row["svalue"] . "</td>\n");
    }
    echo("</tr>\n");
  }
  echo("</table>\n");
  
  echo("\n</td></tr></table>\n");
}

echo("</table>\n");

echo("</FORM>\n");

?>
