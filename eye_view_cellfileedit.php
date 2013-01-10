<?php

/*** CELLDB
eye_view_cellfileedit.php - cellfileedit interface for $view="eye"
created 2005-10-02 - SVD
***/


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
  echo("<input type=\"hidden\" name=\"channum[$cellcount]\" value=" . 
       $schannum .">\n");
  echo("<input type=\"hidden\" name=\"unit[$cellcount]\" value=" . 
       $sunit .">\n");
  echo("<input type=\"hidden\" name=\"channel[$cellcount]\" value=" . 
       $schannel .">\n");
  echo("<b>" . $row["cellid"] . ":</b></td>\n");
  
  // rawfile-specific details for this cell
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
