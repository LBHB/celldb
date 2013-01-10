<?php

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
  $chanvals=array(0,1,2,3,4); 
  echo("<select name=\"channum[$cellcount]\" size=\"1\">");
  for ($ii=0; $ii<count($chanstrings); $ii++) {
    if ($chanvals[$ii] == $row["channum"]) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option value=\"" . $chanvals[$ii] . "\"$sel>".
         $chanstrings[$ii] . "</option>");
  }
  echo("</td>\n");
  
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