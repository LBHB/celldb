<?php

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";

if (""==$allowqueuemaster && ""==$lastallowqueuemaster) {
  $allowqueuemaster=1;
} elseif (""==$allowqueuemaster) {
  $allowqueuemaster=$lastallowqueuemaster;
}
if (""==$orderby && ""==$lastmachinesort) {
  $orderby="tComputer.load1";
} elseif (""==$orderby) {
  $orderby=$lastmachinesort;
}
if (""==$action) {
  $action=0;
}

$sql="SELECT * FROM gUserPrefs WHERE userid=\"$userid\"";
$userdata=mysql_query($sql);

if (""!=$userid && $seclevel>0) {
  $sql="UPDATE gUserPrefs" .
    " SET lastmachinesort=\"$orderby\", " .
    " lastallowqueuemaster=$allowqueuemaster" .
    " WHERE userid=\"$userid\"";
  $userdata = mysql_query($sql);
}

if ((0!=$action && $target>0) || 21==$action) {
  $sql="SELECT * FROM tComputer WHERE id=$target";
  $compdata=mysql_query($sql);
  
  if (0==mysql_num_rows($compdata) && 21!=$action) {
    //echo("Invalid queue id $target<br>");
  } else {
    
    // queue entry does exist
    $row = mysql_fetch_array($compdata);
    
    /* actions:
       1. set allowqueuemaster=0 (not participating)
       2. set allowqueuemaster=1 (full participation)
       3. set allowqueuemaster=2 (conditional participation given load)
       4. set allowqueuemaster=3 (conditional participation, night only)
       5. edit name/ext/owner
       6. edit other computer info
       7. ressurect dead node
       8. set allowothers=0 (no sharing)
       9. set allowothers=1 (sharing)
       10. edit computer info
       11-18. set max processes to 1-8 (ie, action minus 10)
       21. add new computer
       22. switch nocheck status
     */

    // check requested action and make sure user has permission
    if (11<=$action && 18>=$action) {
      //echo("Setting max procs to " . ($action-10) . " for node $target<br>");
      $sql="UPDATE tComputer SET maxproc=" . ($action-10) ." WHERE id=$target";
      mysql_query($sql);
      dblog("Setting max procs to " . ($action-10) . " for " . 
            $row["name"] . "." . $row["ext"],$userid);
    } elseif (1==$action) {
      //echo("Removing node $target<br>");
      $sql="UPDATE tComputer SET allowqueuemaster=0,numproc=0 WHERE id=$target";
      mysql_query($sql);
      
      // kill active jobs on this machine
      $mname=$row["name"] . "." . $row["ext"];
      $sql="UPDATE tQueue SET killnow=1 WHERE machinename=\"$mname\"" .
        " AND complete=-1";
      mysql_query($sql);
      
      dblog("Removed node " . $row["name"] . "." . $row["ext"],$userid);
    } elseif (2==$action) {
      //echo("Added node $target<br>");
      $sql="UPDATE tComputer SET allowqueuemaster=1,lastoverload=0 WHERE id=$target";
      mysql_query($sql);
      
      dblog("Added node " . $row["name"] . "." . $row["ext"],$userid);
    } elseif (3==$action) {
      //echo("set node $target conditional<br>");
      $sql="UPDATE tComputer SET allowqueuemaster=2 WHERE id=$target";
      mysql_query($sql);
      
      dblog("Set node " . $row["name"] . "." . $row["ext"] .
            " to conditional",$userid);
    } elseif (4==$action) {
      //echo("set node $target night only<br>");
      $sql="UPDATE tComputer SET allowqueuemaster=3 WHERE id=$target";
      mysql_query($sql);
      
      dblog("Set node " . $row["name"] . "." . $row["ext"] .
            " to night-only",$userid);
    } elseif (5==$action) {
      //echo("edit name/ext/owner: $name/$ext/$owner<br>");
      
      $sql="UPDATE tComputer SET name=\"$name\",ext=\"$ext\",owner=\"$owner\" WHERE id=$target";
      mysql_query($sql);
      
      dblog("Set node $target to $name.$ext, owner=$owner",$userid);
    } elseif (6==$action) {
      //echo("edit other computer info<br>");

      $sql="UPDATE tComputer SET hardware=\"$hardware\",os=\"$os\",room=\"$room\",note=\"$note\" WHERE id=$target";
      mysql_query($sql);
      
      dblog("Set node $target to hardware=\"$hardware\",os=\"$os\",room=\"$room\",note=\"$note\"",$userid);
    } elseif (7==$action) {
      $sql="UPDATE tComputer SET dead=0 WHERE id=$target";
      mysql_query($sql);
      
      dblog("Resurrected node " . $row["name"] . "." . $row["ext"],$userid);
    } elseif (8==$action) {
      //echo("Disabling sharing for node $target<br>");
      $sql="UPDATE tComputer SET allowothers=0,numproc=0 WHERE id=$target";
      mysql_query($sql);
      
      // kill active jobs of other users on this machine
      $mname=$row["name"] . "." . $row["ext"];
      $sql="UPDATE tQueue SET killnow=1" .
        " WHERE machinename=\"$mname\"" .
        " AND complete=-1" .
        " AND not(user=\"" . $row["owner"] . "\")";
      //echo($sql . "<br>");
      mysql_query($sql);

      dblog("Disabled sharing for " . $row["name"] . "." . $row["ext"],$userid);
    } elseif (9==$action) {
      //echo("Enabling sharing for node $target<br>");
      $sql="UPDATE tComputer SET allowothers=1 WHERE id=$target";
      mysql_query($sql);
      dblog("Disabled sharing for " . $row["name"] . "." . $row["ext"],$userid);
    } elseif (21==$action) {
      //echo("new computer: name/ext/owner= $name/$ext/$owner<br>");
      
      $sql="INSERT INTO tComputer (name,ext,owner,maxproc,allowqueuemaster) VALUES (\"$name\",\"$ext\",\"$owner\",1,0)";
      mysql_query($sql);
      
      dblog("New node: $name.$ext, owner=$owner",$userid);
    } elseif (22==$action) {
      // toggle nocheck status
      $sql="UPDATE tComputer SET nocheck=1-nocheck WHERE id=$target";
      mysql_query($sql);
      
      dblog("Machine " . $row["name"] . "." . $row["ext"] .
            ": set nocheck=" . (1-$row["nocheck"]),$userid);
    } else {
      //echo("Insufficient security level for requested action<br>");
      dblog("Insufficient security level for requested action",$userid);
    }
  }
  $redirurl="queuemachines.php?sessionid=$sessionid&userid=$userid&machinename=$machinename&allowqueuemaster=$allowqueuemaster&orderby=$orderby&edmode=$edmode&action=0";
  header ("Location: $redirurl");
  exit;                 /* Make sure that code below does not execute */

}
?>

<HTML>
<HEAD>
<TITLE>dbqueue machines</TITLE>
</HEAD>

<?php
echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

  $redirurl="queuemachines.php?sessionid=$sessionid&userid=$userid&machinename=$machinename&allowqueuemaster=$allowqueuemaster&orderby=$orderby&edmode=$edmode&action=0";
echo("<meta http-equiv=\"Refresh\" content=\"60; URL=$redirurl\">");
?>
<meta NAME="description" CONTENT="Queue machine usage monitor">

<?php
  //echo( "<b>Queue machines</b>\n" );
  //echo(" (<a href=\"queue/queuemasterlog.txt\">today's log</a>");
  //echo(" <a href=\"queue/queuemasterlog.txt.1\">yesterday's log</a>");
  //echo(" <a href=\"../svd/queue.htm\">help</a>)<br>");
cellheader();

// summary stats
include "./queuesum.php";

//echo("<FORM ACTION=\"queuemachines.php\" METHOD=GET>\n");
//echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
//echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
//echo(" <input type=\"hidden\" name=\"orderby\" value=\"$orderby\">\n");

echo("<table><tr><td valign=top>\n");
$machurl="queuemachines.php?sessionid=$sessionid&userid=$userid&" .
         "allowqueuemaster=$allowqueuemaster&orderby=$orderby&machinename=";

echo("Machine: <select name=\"machinename\" size=1" .
     " OnChange=\"location.href=this.options[this.selectedIndex].value\">");
if ($machinename == "%") {
  echo(" <option value=\"$machurl%\" selected>All</option>");
} else {
  echo(" <option value=\"$machurl%\">All</option>");
}
$machinedata=mysql_query("SELECT DISTINCT machinename" .
                       " FROM tQueue WHERE complete=-1 ORDER BY machinename");
while ( $row = mysql_fetch_array($machinedata) ) {
   if ($machinename == $row["machinename"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option value=$machurl" . $row["machinename"] . "$sel>" . $row["machinename"] . "</option>");
}
echo(" </select>\n");

$acturl="queuemachines.php?sessionid=$sessionid&userid=$userid&" .
         "orderby=$orderby&machinename=$machinename&allowqueuemaster=";

echo("&nbsp;Show inactive: \n");
if (0==$allowqueuemaster) {
  $b1yes="<b>";
  $b2yes="</b>";
  $b1no="";
  $b2no="";
} else {
  $b1yes="";
  $b2yes="";
  $b1no="<b>";
  $b2no="</b>";
}
echo("$b1yes<a href=\"" . $acturl . "0&edmode=$edmode\">Yes</a>$b2yes\n");
echo(" / $b1no<a href=\"" . $acturl . "1&edmode=$edmode\">No</a>$b2no\n");

if ($seclevel>5) {
  $modstr=array("Load","Specs","Edit");
} else {
  $modstr=array("Load","Specs");
}
echo("&nbsp;Mode:\n");
for ($ii=0; $ii<count($modstr); $ii++) {
  
  if ($ii==$edmode) {
    echo("<b>");
  }
  echo("<a href=\"" . $acturl . $allowqueuemaster ."&edmode=$ii\">" .
       $modstr[$ii] . "</a>\n");
  
  if ($ii==$edmode) {
    echo("</b>");
  }
}

echo("&nbsp;&nbsp;<a href=\"" . $acturl . $allowqueuemaster ."&edmode=$edmode\">Refresh</a>");

echo("</td></tr></table>\n");

// query celldb for queue entries matching search criteria

$sql="SELECT * FROM tComputer" .
     " WHERE allowqueuemaster>=$allowqueuemaster" .
     " AND name like \"$machinename\"" .
     " AND location in (0,3)" .
     " ORDER BY $orderby, load1 desc";

//echo("sql: $sql<br>\n");
$compdata=mysql_query($sql);

if (!$compdata) {
  echo("<P>Error performing query: " . mysql_error() . "</P>");
  exit();
}

// list each entry
$sorturl="queuemachines.php?sessionid=$sessionid&userid=$userid&machinename=$machinename&allowqueuemaster=$allowqueuemaster&edmode=$edmode&orderby=";

$loadsc=50;
$keycount=6;

echo("<table>");

echo("<tr bgcolor=\"#bbbbff\">\n");
echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "tComputer.id\">id</a></b></td>\n");
echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "tComputer.name\">name</a></b></td>\n");
echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "tComputer.ext\">ext</a></b></td>\n");
echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "tComputer.owner\">owner</a></b></td>\n");
echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "tComputer.allowqueuemaster\">participation</a></b>&nbsp;</td>\n");
echo("  <td><b>&nbsp;shr&nbsp;</b></td>\n");
if (0==$edmode) {
  echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "tComputer.numproc DESC\">jobs</a>&nbsp;</td>\n");
  echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "tComputer.load1\">load1/15</a></b>&nbsp;</td>\n");
  
  for ($ii=0; $ii<$keycount; $ii++) {
    echo("  <td width=" . ($loadsc-3) . " align=right>" . (($ii+1)*2) . ".0</td>\n");
  }
} else {
  // specs column headers
  echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "tComputer.hardware\">hardware</a></b></td>\n");
  echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "tComputer.os\">os</a></b></td>\n");
  echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "tComputer.room\">loc</a></b></td>\n");
  echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "tComputer.note\">note</a></b></td>\n");
  echo("  <td></td>\n");
  
}

echo("</tr>\n");
$rowcount=0;
while ( $row = mysql_fetch_array($compdata) ) {
  $rowcount++;
  if (1==$edmode && round($rowcount/2)==$rowcount/2) {
    //ie, if it's an even row, make bg color differen
    echo("<tr bgcolor=\"#dddddd\">\n");
  } else {
    echo("<tr>\n");
  }
  echo("   <td>" . $row["id"] . "&nbsp;</td>\n");

  if ($edmode==2) {
    echo("   <td colspan=3>");
    
    echo("<FORM ACTION=\"queuemachines.php\" METHOD=GET>\n");
    echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
    echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
    echo(" <input type=\"hidden\" name=\"orderby\" value=\"$orderby\">\n");
    echo(" <input type=\"hidden\" name=\"action\" value=\"5\">\n");
    echo(" <input type=\"hidden\" name=\"target\" value=\"". $row["id"] ."\">\n");
    echo(" <input type=\"hidden\" name=\"edmode\" value=\"$edmode\">\n");
    echo("<input type=text size=12 name=\"name\" value=\"". $row["name"] ."\">\n");
    echo("<input type=text size=4 name=\"ext\" value=\"". $row["ext"] ."\">\n");
    echo("<input type=text size=8 name=\"owner\" value=\"". $row["owner"] ."\">\n");
    echo("<INPUT TYPE=SUBMIT VALUE=\"Set\">");
    
    echo("</FORM>\n");
    echo("</td>\n");

  } else {
    $mname=$row["name"] ."." . $row["ext"];
    $joburl="<a href=\"queuemonitor.php?sessionid=$sessionid&userid=$userid" .
      "&user=%&complete=-1&machinename=$mname\">";
    echo("   <td>$joburl" . $row["name"] . "</a>&nbsp;</td>\n");
    echo("   <td>" . $row["ext"] . "&nbsp;</td>\n");
    
    if (""==$row["owner"]) {
      echo("   <td>cluster&nbsp;</td>\n");
    } else {
      echo("   <td>" . $row["owner"] . "&nbsp;</td>\n");
    }
  }
  
  echo("   <td>");
  $sharestrings=array("Off","On","Cond","Night");
  
  if (checksec($userid,$seclevel,$row["owner"],5)) {

    // for users with high secrity level, allow editing machine status
    echo("   <select OnChange=\"location.href=this.options[this.selectedIndex].value\">\n");
    
    $setstrings=array("","","");
    $setstrings[$row["allowqueuemaster"]]="selected";
    
    for ($ii=0; $ii<count($sharestrings); $ii++) {
      echo("    <option value=\"$sorturl$orderby&action=" . ($ii+1) . 
           "&target=" . $row["id"] . "\" " . $setstrings[$ii] . ">" . 
           $sharestrings[$ii] . "</option>\n");
      }
    echo("   </select>");
    if (1==$row["dead"]) {
      echo("<b>DEAD</b>");
    } elseif (1==$row["nocheck"]) {
      echo("<a href=\"" . $sorturl . $orderby);
      echo("&action=22&target=" . $row["id"] . "\">NCHK</a>");
    } elseif (0==$row["nocheck"] && 0==$row["allowqueuemaster"]) {
      echo("<a href=\"" . $sorturl . $orderby);
      echo("&action=22&target=" . $row["id"] . "\">CHK</a>");
    } elseif ($row["allowqueuemaster"]>0) {
      $maxproc=$row["maxproc"];
      if ($maxproc>0) {
        echo("&nbsp;" . "<a href=\"" . $sorturl . $orderby);
        echo("&action=" . ($maxproc+9) . "&target=" . $row["id"] . "\">-1</a>");
      }
      if ($maxproc<8) {
        echo("&nbsp;" . "<a href=\"" . $sorturl . $orderby);
        echo("&action=" . ($maxproc+11) . "&target=" . $row["id"] . "\">+1</a>");
      }
    }
  } else {
    echo($sharestrings[$row["allowqueuemaster"]]);
    if (1==$row["dead"]) {
      echo("&nbsp;<b>DEAD</b>");
    }
  }
  echo("&nbsp;</td>\n");
  
  echo("   <td align=\"center\">");
  if (1==$row["dead"]) {
    //not participating
    if (checksec($userid,$seclevel,$row["owner"],2)) {
      echo("<a href=\"" . $sorturl . $orderby . "&action=7&target=" . $row["id"] . "\">");
      echo("RES</a>");
    }
  } elseif (0==$row["allowqueuemaster"]) {
    //not participating
    echo("X");
  } elseif (checksec($userid,$seclevel,$row["owner"],5)) {
    if (0==$row["allowothers"]) {
      echo("<a href=\"" . $sorturl . $orderby . "&action=9&target=" . $row["id"] . "\">");
      echo("N</a>");
    } else {
      echo("<a href=\"" . $sorturl . $orderby . "&action=8&target=" . $row["id"] . "\">");
      echo("Y</a>");
    }
  } else {
    if ($row["allowothers"]==0) {
      echo("N");
    } else {
      echo("Y");
    }
  }
  echo("&nbsp;</td>\n");
  
  if ($edmode==2) {
    echo("   <td colspan=5>\n");
    echo("<FORM ACTION=\"queuemachines.php\" METHOD=GET>\n");
    echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
    echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
    echo(" <input type=\"hidden\" name=\"orderby\" value=\"$orderby\">\n");
    echo(" <input type=\"hidden\" name=\"action\" value=\"6\">\n");
    echo(" <input type=\"hidden\" name=\"target\" value=\"". $row["id"] ."\">\n");
    echo(" <input type=\"hidden\" name=\"edmode\" value=\"$edmode\">\n");
    echo("<input type=text size=16 name=\"hardware\" value=\"". 
         $row["hardware"] ."\">\n");
    echo("<input type=text size=16 name=\"os\" value=\"". 
         $row["os"] ."\">\n");
    echo("<input type=text size=6 name=\"room\" value=\"". 
         $row["room"] ."\">\n");
    echo("<input type=text size=16 name=\"note\" value=\"". 
         $row["note"] ."\">\n");
    echo("<INPUT TYPE=SUBMIT VALUE=\"Set\">\n");
    
    echo("</FORM>\n");
    
    echo("   </td>\n");
    
  } elseif ($edmode==1) {
    // show machine specs
    echo("   <td>" . $row["hardware"] . "&nbsp;</td>\n");
    echo("   <td>" . $row["os"] . "&nbsp;</td>\n");
    echo("   <td>" . $row["room"] . "&nbsp;</td>\n");
    echo("   <td>" . $row["note"] . "&nbsp;</td>\n");
    
  } else {
    // show current load
    echo("   <td align=\"center\">");
    if ($row["numproc"] > 0) {
      echo($row["numproc"] . "/" . $row["maxproc"] . "&nbsp;</td>\n");
    } elseif ($row["allowqueuemaster"] > 0) {
      echo("<font color=\"#999999\">0/" . $row["maxproc"] . 
           "</font>&nbsp;</td>\n");
    } else {
      echo("<font color=\"#999999\">0/0</font>&nbsp;</td>\n");
    }
    
    $load=$row["load1"];
    if (1==$row["lastoverload"] && 2==$row["allowqueuemaster"]) {
      echo("   <td><font color=\"#CC0000\">" . sprintf("%.2f",$load) .
           "/" . sprintf("%.2f",$row["load15"]) .
           "*</font>&nbsp;</td>\n");
    } else {
      echo("   <td>" . sprintf("%.2f",$load) . 
           "/" . sprintf("%.2f",$row["load15"]) .
           "&nbsp;</td>\n");
    }
    
    if (0==$row["allowqueuemaster"]) {
      $fn="images/black.jpg";
    } elseif (2==$row["allowqueuemaster"] && 1==$row["lastoverload"]) {
      $fn="images/red.jpg";
    } elseif ($load>$keycount*2) {
      $fn="images/red.jpg";
    } elseif (0==$row["numproc"]) {
      $fn="images/green.jpg";
    } else {
      $fn="images/blue.jpg";
    }
    if ($load>$keycount*2) {
      $load=$keycount*2;
    }
    echo("   <td colspan=" . ($keycount+1) . ">");
    echo("<img border=0 src=\"$fn\" align=\"left\"");
    echo("width=" . round(($load*$loadsc+1)/2) . " height=12>");
    echo("</td>\n");
  }
  
  echo("</tr>\n");
}

if (2==$edmode) {
  echo("<tr>\n");
  echo("   <td>&nbsp;</td>\n");
  
  echo("   <td colspan=3>");
  
  echo("<FORM ACTION=\"queuemachines.php\" METHOD=GET>\n");
  echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
  echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
  echo(" <input type=\"hidden\" name=\"orderby\" value=\"$orderby\">\n");
  echo(" <input type=\"hidden\" name=\"action\" value=\"21\">\n");
  echo(" <input type=\"hidden\" name=\"target\" value=\"-1\">\n");
  echo(" <input type=\"hidden\" name=\"edmode\" value=\"2\">\n");
  echo("<input type=text size=12 name=\"name\" value=\"\">\n");
  echo("<input type=text size=4 name=\"ext\" value=\"\">\n");
  echo("<input type=text size=8 name=\"owner\" value=\"\">\n");
  echo("<INPUT TYPE=SUBMIT VALUE=\"Add\">");
  
  echo("</FORM>\n");
  echo("</td>\n");

} elseif (0==$edmode) {
  //only show legend if showing load bars
  echo("<tr ><td></td><td></td><td></td><td></td><td></td>\n");
  echo("   <td valign=center colspan=" . ($keycount+3) . ">\n");
  echo("   <img border=0 src=\"images/green.jpg\" width=12 height=12>");
  echo("&nbsp;inactive&nbsp;\n");
  echo("   <img border=0 src=\"images/blue.jpg\" width=12 height=12>");
  echo("&nbsp;active&nbsp;\n");
  echo("   <img border=0 src=\"images/red.jpg\" width=12 height=12>");
  echo("&nbsp;overloaded&nbsp;\n");
  echo("   <img border=0 src=\"images/black.jpg\" width=12 height=12>");
  echo("&nbsp;not in queue&nbsp;\n");
  
  echo("   </td>\n");
  echo("</tr>\n");
}

echo("</table>\n");
?>

<script language="javascript">
function gotoUrl(url) {
  if (url == "")
    return;
  location.href = url;
}
function newWin(url) {
  // url of this function should have the format: "target,URL".
  if (url == "")
    return;
  window.open(url.substring(url.indexOf(",") + 1, url.length), 
	url.substring(0, url.indexOf(",")));
}
</script>

<?php

queuefooter();

?>


<em>This page refreshes automatically every 60 seconds</em><br>

</BODY>
</HTML>
