<?php
/*** CELLDB
penedit.php - edit information about a recording penetration
created 2002 - SVD
***/

// userid - string id of user to go in added by
// masterid - id in gCellMaster
// action 0 - add file
//        1 - edit file
//        2 - do the edit/add and redirect back to $fnpeninfo

// global include: connect to db and get important basic info about user prefs
$min_sec_level=6;
include_once "./celldb.php";

if (""==$penid) {
   $penid=-1;
}

$errormsg="";

// if save selected, save posted info to gCellMaster
if (2==$action) {
   if (-1==$penid) {
      $sql="SELECT * FROM gPenetration WHERE penname=\"$penname\"";
      $pendata = mysql_query($sql);
      $pendatarows=mysql_num_rows($pendata);
      if ($pendatarows>0) {
         echo ("Pen $penname already exists id=$penid\n");
         exit;
      }
      
      // if weights already recorded for this date, fix new pen to that penid
      $sql="SELECT * FROM gPenetration".
        " WHERE animal='$animal' AND pendate='$pendate' AND training=2";
      $pendata = mysql_query($sql);
      $pendatarows=mysql_num_rows($pendata);
      if ($pendatarows>0) {
        $penrow=mysql_fetch_array($pendata);
        $penid=$penrow["id"];
      }
   } else {
      $sql="SELECT * FROM gPenetration WHERE id=$penid";
      $pendata = mysql_query($sql);
      $pendatarows=mysql_num_rows($pendata);
   }
   
   // make sure all numeric variables are numeric
   $well=$well*1;
   $water=$water*1;
   $weight=$weight*1;
   $mondist=$mondist*1;
   $etudeg=$etudeg*1;
   $stability=$stability*1;
   $training=$training*1;
   $numchans=$numchans*1;
   for ($ii=0;$ii<count($firstdepth);$ii++) {
    $firstdepth[$ii]=sprintf("%d",$firstdepth[$ii]);
   }
   if (count($firstdepth) > 0) {
    $firstdepth=implode(",",$firstdepth);
   }
   for ($ii=0;$ii<count($impedance);$ii++) {
    $impedance[$ii]=sprintf("%.1f",$impedance[$ii]);
   }
   if (count($impedance) > 0) {
    $impedance=implode(",",$impedance);
   }
   for ($ii=0;$ii<count($ecoordinates);$ii++) {
     $ecoordinates[$ii]=round($ecoordinates[$ii],2);
   }
   if (count($ecoordinates) > 0) {
    $ecoordinates=implode(",",$ecoordinates);
   }
  
   if (0==$pendatarows) {
      // ie, gPenetration entry doesn't exist yet. create a new
      // entry
      $sql="INSERT INTO gPenetration" .
        " (penname,animal,well,pendate,who,fixtime,water,weight,ear,".
        "mondist,etudeg,".
        "numchans,racknotes,speakernotes,probenotes,electrodenotes,".
        "ecoordinates,impedancenotes,stability,stabilitynotes,".
        "descentnotes,firstdepth,impedance,addedby,info,training)" .
        " VALUES (\"$penname\",\"$animal\",$well,\"$pendate\",".
        "\"$who\",\"$fixtime\",$water,$weight,\"$ear\",".
        "$mondist,$etudeg,".
        "$numchans,\"$racknotes\",\"$speakernotes\",".
        "\"$probenotes\",\"$electrodenotes\",".
        "\"$ecoordinates\","."\"$impedancenotes\",".
        "$stability,\"$stabilitynotes\",\"$descentnotes\",\"$firstdepth\",".
        "\"$impedance\",".
        "\"$addedby\",\"$siteinfo\",$training)";
      
      $result=mysql_query($sql);
      $penid=mysql_insert_id();
   } else {
     // gPenetration entry does exist.  update with posted values
     $sql="UPDATE gPenetration SET ".
       "penname=\"$penname\",".
       "animal=\"$animal\",".
       "well=$well,".
       "pendate=\"$pendate\",".
       "who=\"$who\",".
       "fixtime=\"$fixtime\",".
       "water=$water,".
       "weight=$weight,".
       "ear=\"$ear\",".
       "numchans=$numchans,".
       "eye=\"$eye\",".
       "mondist=$mondist,".
       "etudeg=$etudeg,".
       "racknotes=\"$racknotes\",".
       "speakernotes=\"$speakernotes\",".
       "probenotes=\"$probenotes\",".
       "electrodenotes=\"$electrodenotes\",".
       "ecoordinates=\"$ecoordinates\",".
       "impedancenotes=\"$impedancenotes\",".
       "stability=$stability,".
       "stabilitynotes=\"$stabilitynotes\",".
       "descentnotes=\"$descentnotes\",".
       "firstdepth=\"$firstdepth\",".
       "impedance=\"$impedance\",".
       "addedby=\"$addedby\",".
       "info=\"$siteinfo\",".
       "training=\"$training\"".
       " WHERE id=$penid";
     $result=mysql_query($sql);
     if (0==$result) {
       echo "SQL ERROR #" . mysql_errno().": ".mysql_error()."<BR>";
       echo $sql . "<BR>";
     }
     
     $sql="UPDATE gCellMaster SET".
       " well=$well,".
       " penname=\"$penname\",".
       " training=$training".
       " WHERE penid=$penid";
     $result=mysql_query($sql);

     $sql="SELECT * FROM gCellMaster where penid=$penid";
     $masterdata=mysql_query($sql);
     
     while ($row=mysql_fetch_array($masterdata)) {
       $sql="UPDATE gDataRaw set training=$training WHERE masterid=" . $row["id"];
       $result=mysql_query($sql);
     }

     
   }
   //echo mysql_errno().": ".mysql_error()."<BR>";
   //echo("$sql $result");
   
   // save last* to userprefs so that celllist shows the new cell
   //$userdata = mysql_query("UPDATE gUserPrefs SET lastanimal=\"$animal\", lastwell=$well, lastpen=\"$penname\" WHERE userid=\"$userid\"");

   if (""==$goback) {
     // do nothing
     
   } else {
     header ("Location: $fnpeninfo?userid=$userid&sessionid=$sessionid&penid=$penid");
     exit;  /* Make sure that code below does not execute */
   }
}

// load data pre-existing data about the penetration -- if it exists
if (-1==$penid) {
   // only penname provided, see if it exists
   $sql="SELECT * FROM gPenetration WHERE penname=\"$penname\"";
} else {
   // see if penid exists
   $sql="SELECT * FROM gPenetration WHERE id=$penid";
}

$pendata = mysql_query($sql);
$pendatarows=mysql_num_rows($pendata);
if ($pendatarows>0) {
   // if there are rows, then there is an entry
   $row=mysql_fetch_array($pendata);
   
   $penid=$row["id"];
   $penname=$row["penname"];
   $animal=$row["animal"];
   $well=$row["well"];
   $pendate=$row["pendate"];
   $who=$row["who"];
   $fixtime=$row["fixtime"];
   $water=$row["water"] * 1.0;
   $weight=$row["weight"] * 1.0;
   $ear=$row["ear"];
   $numchans=$row["numchans"] * 1.0;
   $eye=$row["eye"];
   $mondist=$row["mondist"] * 1.0;
   $etudeg=$row["etudeg"] * 1.0;
   $racknotes=$row["racknotes"];
   $speakernotes=$row["speakernotes"];
   $probenotes=$row["probenotes"];
   $electrodenotes=$row["electrodenotes"];
   $descentnotes=$row["descentnotes"];
   $addedby=$row["addedby"];
   $info=$row["info"];
   $lastmod=$row["lastmod"];
   $training=$row["training"];
   $firstdepth=$row["firstdepth"];
   $firstdepth=explode(",",$firstdepth);
   $impedance=$row["impedance"];
   $impedance=explode(",",$impedance);
   $ecoordinates=$row["ecoordinates"];
   $ecoordinates=explode(",",$ecoordinates);
   
} else {
   // guess what some parameters will be $animal and $well should be specified as input parameters
   if ($animal=="") {
      $row=mysql_fetch_array($userdata);
      $animal=$row["lastanimal"];
      $well=$row["lastwell"];
   }
   
   if ("my"==$animal) {
      $sql="SELECT animal FROM gPenetration".
         " WHERE addedby='$userid' ORDER BY lastmod DESC LIMIT 1";
      $latestpendata=mysql_query($sql);
      if ($row=mysql_fetch_array($latestpendata)) {
         $animal=$row["animal"];
      } else {
         $animal="test";
      }
   }
   if (""==$training) {
      $stset="0,1";
   } else {
      $stset="$training";
   }
 
   // figure out defaults using last entry for that critter
   $sql="SELECT max(id) as maxid,max(well) as maxwell".
     " FROM gPenetration" .
     " WHERE training in ($stset) AND animal like \"$animal\"";
   $latestpendata=mysql_query($sql);
   $row=mysql_fetch_array($latestpendata);
   $latestid=$row["maxid"];
   if ($well<1) {
      $well=$row["maxwell"];
   }
   if ("%"==$animal) {
      $animal=$row["manimal"];
   }
   if (""==$latestid) {
     $sql="SELECT gAnimal.cellprefix FROM gAnimal".
       " WHERE gAnimal.animal=\"$animal\"";
     $latestpendata=mysql_query($sql);
     $latestrow=mysql_fetch_array($latestpendata);
     $cellprefix=$latestrow["cellprefix"];
     $lastpennum=0;
     
     $errormsg="Guessing penetration info from scratch.";
     
   } else {
     $sql="SELECT gPenetration.*, gAnimal.cellprefix FROM gPenetration".
       " INNER JOIN gAnimal ON gAnimal.animal=gPenetration.animal".
       " WHERE gPenetration.id=$latestid";
     $latestpendata=mysql_query($sql);
     $latestrow=mysql_fetch_array($latestpendata);
     
     if (""==$well) {
       $well=$latestrow["well"];
     }
     $cellprefix=$latestrow["cellprefix"];
     $lastpennum=substr($latestrow["penname"],strlen($cellprefix));
     //echo($sql .": " . $lastpennum);
     
     $errormsg="Guessing info from pen " . $latestrow["penname"];
     
     $who=$latestrow["who"];
     $ear=$latestrow["ear"];
     $numchans=$latestrow["numchans"] * 1.0;
     $eye=$latestrow["eye"];
     $mondist=$latestrow["mondist"] * 1.0;
     $etudeg=$latestrow["etudeg"] * 1.0;
     $racknotes=$latestrow["racknotes"];
     $speakernotes=$latestrow["speakernotes"];
     $probenotes=$latestrow["probenotes"];
     $electrodenotes=$latestrow["electrodenotes"];
     $descentnotes="";
     $training=$latestrow["training"];
   }

   if ("eye"==$view) {
     $penname=$animal[0] . date("Y-m-d");
     $lastpenname=substr($latestrow["penname"],0,strlen($penname));
     if ($lastpenname==$penname) {
       if (strlen($latestrow["penname"])==strlen($penname)) {
         $lastletter="a";
       } else {
         $lastletter=substr($latestrow["penname"],-1);
       }
       $penname=$lastpenname . chr(ord($lastletter)+1);
     }
   } else {
     
     $pennum=$lastpennum+1;
     if (1==$training) {
       $penname=sprintf("%s%03dT",$cellprefix,$pennum);
     } else {
       $penname=sprintf("%s%03d",$cellprefix,$pennum);
     }
   }
   
   $pendate=date("Y-m-d");
   $fixtime=date("g:i a");
   $firstdepth="";
   $impedance="";
   $ecoordinates="";
   
   $addedby=$userid;
   $sql="SELECT * FROM gPenetration WHERE animal='$animal' AND pendate='$pendate' AND training=2";
   $pendata = mysql_query($sql);
   if (mysql_num_rows($pendata)>0) {
     $penrow=mysql_fetch_array($pendata);
     $water=$penrow["water"];
     $weight=$penrow["weight"];
     $penid=$penrow["id"];
   } else {
     $water="";
     $weight="";
   }
}

?>
<HTML>
<HEAD>
<TITLE>celldb - Edit penetration - <?php echo($penname)?></TITLE>
</HEAD>

<?php
echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

if (""==$userid) {
   echo("<p><b><font color=\"#CC0000\">ERROR userid must be specified</font></b></p>");
}
echo( "<p><b>Editing penetration</b>" );
echo("  (<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&penname=$penname&bkmk=$bkmk\">Go Back</a>)\n");
echo("  (<a href=\"celllist.php?userid=$userid&sessionid=$sessionid&bkmk=$bkmk\">Cell list</a>)</p>\n");

if (""!=$errormsg) {
  echo("<p><b><font color=\"#CC0000\">$errormsg</font></b></p>\n");
}

echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");

echo("<FORM ACTION=\"penedit.php\" METHOD=POST>");
echo(" <input type=\"hidden\" name=\"addedby\" value=\"$userid\">\n");
echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
echo(" <input type=\"hidden\" name=\"action\" value=2>\n");  // do the edit
echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
echo(" <input type=\"hidden\" name=\"penid\" value=\"$penid\">\n");
echo(" <input type=\"hidden\" name=\"bkmk\" value=\"$bkmk\">\n");

echo("<table border=0 cellpadding=3 cellspacing=0>\n");
$animaldata = mysql_query("SELECT DISTINCT animal FROM gAnimal ORDER BY animal");
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
echo("<td>Training:</td><td><INPUT TYPE=CHECKBOX NAME=\"training\" value=1");
if (1==$training) {
   echo(" checked");
}
echo("></td></tr>\n");
echo("<tr><td>Pen ID:</td><td><INPUT TYPE=TEXT SIZE=15 NAME=\"penname\" value=\"$penname\"></td>\n");
echo("<td>Well:</td><td><INPUT TYPE=TEXT SIZE=5 NAME=\"well\" value=\"$well\"></td></tr>\n");
echo("<tr><td>Date:</td><td><INPUT TYPE=TEXT SIZE=15 NAME=\"pendate\" value=\"$pendate\"></td>\n");
echo("<td>Who:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"who\" value=\"$who\"></td></tr>\n");
if ("eye"==$view) {
  echo("<td>Fix time:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"fixtime\" value=\"$fixtime\"></td>\n");
  echo("<td>Stim eye:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"eye\" value=\"$eye\"></td></tr>\n");
  echo("<tr><td>Mondist:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"mondist\" value=\"$mondist\">&nbsp;cm</td>\n");
  echo("<td>Pix/deg:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"etudeg\" value=\"$etudeg\"></td></tr>\n");
  echo("<tr><td>Rack:</td><td colspan=3><textarea NAME=\"racknotes\" rows=2 cols=60>$racknotes</textarea></td></tr>\n");
 } else {
  echo("<tr><td>Fix time:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"fixtime\" value=\"$fixtime\"></td>\n");
  echo("<td>Stim ear:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"ear\" value=\"$ear\"></td></tr>\n");
  echo("<tr><td># Channels:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"numchans\" value=\"$numchans\"></td></tr>\n");
  
  echo("<tr><td>Rack:</td><td colspan=3><textarea NAME=\"racknotes\" rows=2 cols=60>$racknotes</textarea></td></tr>\n");
  echo("<tr><td>Speaker:</td><td colspan=3><textarea NAME=\"speakernotes\" rows=2 cols=60>$speakernotes</textarea></td></tr>\n");
 }

echo("<tr><td>Probe:</td><td colspan=3><textarea NAME=\"probenotes\" rows=2 cols=60>$probenotes</textarea></td></tr>\n");
echo("<tr><td>Electrode:</td><td colspan=3><textarea NAME=\"electrodenotes\" rows=2 cols=60>$electrodenotes</textarea></td></tr>\n");
$estring=array("AP","ML","DV","AP","ML","DV","Tilt","Rot");
for ($ii=0; $ii<8; $ii++) {
  if ($ii>count($ecoordinates)) {
    $ecoordinates[$ii]="";
  }
}

echo("<tr><td valign=\"top\" style=\"line-height:1.5\">MT zero:<br>MT position:</td><td colspan=1>");
for ($ii=0; $ii<3; $ii++) {
  echo($estring[$ii] . 
       ":<INPUT TYPE=TEXT SIZE=3 NAME=\"ecoordinates[$ii]\" value=\"".
       $ecoordinates[$ii] . "\">&nbsp;\n");
}
echo("<br>\n");
for ($ii=3; $ii<6; $ii++) {
  echo($estring[$ii] . 
       ":<INPUT TYPE=TEXT SIZE=3 NAME=\"ecoordinates[$ii]\" value=\"".
       $ecoordinates[$ii] . "\">&nbsp;\n");
}
echo("</td>\n");
echo("<td valign=\"top\" style=\"line-height:1.5\">Tilt:<br>Rotation:</td><td colspan=1>");
$ii=6;
echo("<INPUT TYPE=TEXT SIZE=3 NAME=\"ecoordinates[$ii]\" value=\"".
     $ecoordinates[$ii] . "\">&nbsp;\n");
echo("<br>\n");
$ii=7;
echo("<INPUT TYPE=TEXT SIZE=3 NAME=\"ecoordinates[$ii]\" value=\"".
     $ecoordinates[$ii] . "\">&nbsp;\n");
echo("</td>\n");
echo("</tr>\n");

echo("<tr><td>Impedance:</td><td colspan=3>");
for ($ii=0; $ii<$numchans; $ii++) {
  if ($ii<count($impedance)) {
    $timp=$impedance[$ii];
  } else {
    $timp="";
  }
  echo(($ii+1) . ":<INPUT TYPE=TEXT SIZE=3 NAME=\"impedance[$ii]\" value=\"".
       $timp . "\">&nbsp;\n");
}
echo("</td>\n");
echo("</tr>\n");

echo("<tr><td>1st spike depth:</td><td colspan=3>");
for ($ii=0; $ii<$numchans; $ii++) {
  if ($ii<count($firstdepth)) {
    $tdepth=$firstdepth[$ii];
  } else {
    $tdepth="";
  }
  echo(($ii+1) . ":<INPUT TYPE=TEXT SIZE=3 NAME=\"firstdepth[$ii]\" value=\"".
       $tdepth . "\">&nbsp;\n");
}
echo("</td></tr>\n");
echo("<tr valign=\"top\"><td>Descent:</td><td colspan=3><textarea NAME=\"descentnotes\" rows=12 cols=60>$descentnotes</textarea></td></tr>\n");
//echo("<tr><td>Water:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"water\" value=\"$water\">&nbsp;ml</td>\n");
//echo("<td>Weight:</td><td><INPUT TYPE=TEXT SIZE=10 NAME=\"weight\" value=\"$weight\">&nbsp;g</td></tr>\n");

echo("</table>\n");

echo("<INPUT TYPE=SUBMIT VALUE=\"Save\">");
echo("<INPUT TYPE=SUBMIT name=\"goback\" VALUE=\"Save & Quit\">");
echo("  (<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&penname=$penname&bkmk=$bkmk\">Cancel</a>)\n");
echo("  (<a href=\"celllist.php?userid=$userid&sessionid=$sessionid&bkmk=$bkmk\">Cell list</a>)</p>\n");
echo("</FORM>\n"); // . phpinfo(32));

?>

</HTML>
