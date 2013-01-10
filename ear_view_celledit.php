<?php

echo("<FORM ACTION=\"$fncelledit\" METHOD=POST>");
echo(" <input type=\"hidden\" name=\"addedby\" value=\"$userid\">\n");
echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
echo(" <input type=\"hidden\" name=\"action\" value=2>\n");  // do the edit
echo(" <input type=\"hidden\" name=\"masterid\" value=$masterid>\n");
echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
echo(" <input type=\"hidden\" name=\"training\" value=$training>\n");
echo(" <input type=\"hidden\" name=\"animal\" value=\"$animal\">\n");
echo(" <input type=\"hidden\" name=\"bkmk\" value=$bkmk>\n");
echo("<table>\n");

// animal dropdown
$animaldata = mysql_query("SELECT DISTINCT animal FROM gAnimal ORDER BY animal");
echo("<tr><td colspan=4><HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE></td></tr>\n");
echo("<tr><td>Animal:</td><td>$animal</td>\n");

// pentration dropdown
$pendata = mysql_query("SELECT DISTINCT id,penname,well,numchans".
                       " FROM gPenetration" .
                       " WHERE animal=\"$animal\"".
                       " AND well=$well ORDER BY penname");
echo("<td>Pen/Well:</td><td><select name=\"penid\" size=\"1\">");
while ( $row = mysql_fetch_array($pendata) ) {
  if ( $penid == $row["id"]) {
    $sel=" selected";
  } else {
    $sel="";
  }
  echo(" <option value=" . $row["id"] . "$sel>" . $row["penname"] .
       "/" . $row["well"] . "</option>");
}
echo(" </select>\n");
echo("</td></tr>\n");

//info for all cells at this site
echo("<tr><td>Site ID:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"cellid\" value=\"$cellid\"></td>\n");
echo("<td>Time:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"findtime\" value=\"$findtime\"></td></tr>\n");
echo("<tr><td>Area:</td><td colspan=3>");
for ($ii=0; $ii<$numchans; $ii++) {
  if ($ii<count($area)) {
    $tarea=$area[$ii];
  } else {
    $tarea="";
  }
  echo(($ii+1) . ":<INPUT TYPE=TEXT SIZE=4 NAME=\"area[$ii]\" value=\"$tarea\">&nbsp;\n");
}
echo("<td></td>\n");
echo("<tr><td>Depth:</td><td colspan=3>");
for ($ii=0; $ii<$numchans; $ii++) {
  if ($ii<count($depth)) {
    $tdepth=$depth[$ii];
  } else {
    $tdepth="";
  }
  echo(($ii+1) . ":<INPUT TYPE=TEXT SIZE=4 NAME=\"depth[$ii]\" value=\"". 
       $tdepth . "\">&nbsp;\n");
}
echo(" (<INPUT TYPE=TEXT SIZE=3 NAME=\"umperdepth\" value=\"$umperdepth\">&nbsp;um/click)</td>\n");
echo("<td></td>\n");
echo("<td></td></tr>\n");

echo("<tr><td>BF:</td><td colspan=3>");
for ($ii=0; $ii<$numchans; $ii++) {
  if ($ii<count($bf)) {
    $tbf=$bf[$ii];
  } else {
    $tbf="";
  }
  echo(($ii+1) . ":<INPUT TYPE=TEXT SIZE=4 NAME=\"bf[$ii]\" value=\"". 
       $tbf . "\">&nbsp;\n");
}
echo("</td>\n");
echo("<td></td>\n");
echo("<td></td></tr>\n");

echo("<tr><td>Comments:</td><td colspan=3><textarea NAME=\"comments\" rows=4 cols=65>$comments</textarea></td></tr>\n");


echo("<tr><td colspan=4><HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE></td></tr>\n");

echo("<tr><td colspan=4>\n");
echo("<table>\n");
echo("<tr><td>Chan</td><td>Unit</td>");
echo("<td>CF</td><td>BW</td><td>Lat</td>");
echo("<td>Crap</td><td>Handplot\n");
echo("<INPUT TYPE=SUBMIT VALUE=\"Save\"><INPUT TYPE=SUBMIT NAME=\"newcell\" VALUE=\"New Cell\"></td>\n");

//info specific to each cell
$singledata=mysql_query("SELECT * FROM gSingleCell".
                        " WHERE masterid=$masterid" .
                        " ORDER BY cellid,id");
$cellcount=0;
while ( $row = mysql_fetch_array($singledata) ) {
  $cellcount=$cellcount+1;
  
  echo("<tr><td>\n");
  echo("<input type=\"hidden\" name=\"singleid[$cellcount]\" value=" . 
       $row["id"] .">\n");
  
  //echo("<INPUT TYPE=TEXT SIZE=5 NAME=\"channel[$cellcount]\" value=\"" . $row["channel"] . "\"></td>\n");

  $chanstrings=array("a","b","c","d","e","f","g","h"); 
  $chanvals=array(1,2,3,4,5,6,7,8); 
  echo("<select name=\"channum[$cellcount]\" size=\"1\">");
  for ($ii=0; $ii<$numchans; $ii++) {
    if ($chanvals[$ii] == $row["channum"]) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option  value=\"" . $chanvals[$ii] . "\"$sel>".
         $chanstrings[$ii] . "</option>");
  }
  echo("</td>\n");
  
  echo("<td><select name=\"unit[$cellcount]\" size=\"1\">");
  for ($ii = 1; $ii <= 8; $ii++) {
    if ($ii == $row["unit"]) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option  value=\"$ii\"$sel>$ii</option>");
  }
  echo("</td>\n");
  
  echo("<td><INPUT TYPE=TEXT SIZE=6 NAME=\"cf[$cellcount]\" value=\"" . $row["cf"] . "\"></td>\n");
  echo("<td><INPUT TYPE=TEXT SIZE=4 NAME=\"bw[$cellcount]\" value=\"" . $row["bw"] . "\"></td>\n");
  
  //echo("<td><INPUT TYPE=TEXT SIZE=4 NAME=\"area[$cellcount]\" value=\"" . $row["area"] . "\"></td>\n");
  echo("<td><INPUT TYPE=TEXT SIZE=4 NAME=\"latency[$cellcount]\" value=\"" . $row["latency"] . "\"></td>");
  
  // post recording details
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
