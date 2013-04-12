<?php

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";

if (""==$orderby) {
  $orderby="tQueue.id";
}
if (""==$complete && ""==$lastjobcomplete) {
  $complete=-1;
} elseif (""==$complete) {
  $complete=$lastjobcomplete;
}
if (""==$user && ""==$lastjobuser) {
  $user=$userid;
} elseif (""==$orderby) {
  $user=$lastjobuser;
}

if (""!=$userid) {
  $sql="UPDATE gUserPrefs" .
    " SET lastjobuser=\"$user\", " .
    " lastjobcomplete=$complete" .
    " WHERE userid=\"$userid\"";
  $userdata = mysql_query($sql);
}

if (0!=$action && $target>0) {
  $sql="SELECT * FROM tQueue WHERE id=$target";
  $queuedata=mysql_query($sql);
  
  if (0==mysql_num_rows($queuedata)) {
    //echo("Invalid queue id $target<br>");
  } else {

    // queue entry does exist
    $row = mysql_fetch_array($queuedata);
    
    // check requested action and make sure user has permission
    if (-1==$action && ($seclevel>=5 || $row["user"]==$userid)) {
      //echo("Removing queue id $target<br>");
      $sql="DELETE FROM tQueue WHERE id=$target";
      $compdata=mysql_query($sql);
    } elseif (-2==$action && ($seclevel>=5 || $row["user"]==$userid)) {
      //echo("Removing queue id $target<br>");
      $sql="UPDATE tQueue SET killnow=1 WHERE id=$target";
      $compdata=mysql_query($sql);
    } elseif (1==$action) {
      //echo("Reseting queue id $target<br>");
      $sql="UPDATE tQueue SET complete=0 WHERE id=$target";
      $compdata=mysql_query($sql);
    } else {
      //echo("Insufficient security level for requested action<br>");
    }
  }
  
  $redirurl="queuemonitor.php?sessionid=$sessionid&userid=$userid&user=$user&complete=$complete&machinename=$machinename&notemask=$notemask&orderby=$orderby&action=0";
  header("Location: $redirurl");
  exit;                 /* Make sure that code below does not execute */
}
?>

<HTML>
<HEAD>
  <TITLE>dbqueue monitor</TITLE>
  <link rel="shortcut icon" href="favicon.ico" />
</HEAD>
<?php
echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

$redirurl="queuemonitor.php?sessionid=$sessionid&userid=$userid&user=$user&complete=$complete&machinename=$machinename&notemask=$notemask&orderby=$orderby&action=0";
echo("<meta http-equiv=\"Refresh\" content=\"60; URL=$redirurl\">");
?>

<?php

//echo( "<b>Queue status</b>\n" );
//echo(" (<a href=\"queue/queuemasterlog.txt\">today's log</a>");
//echo(" <a href=\"queue/queuemasterlog.txt.1\">yesterday's log</a>");
//echo(" <a href=\"queuehelp.php\">help</a>)<br>");
cellheader();

// summary stats
include "./queuesum.php";

echo("<FORM ACTION=\"queuemonitor.php\" METHOD=GET>\n");
echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
echo(" <input type=\"hidden\" name=\"orderby\" value=\"$orderby\">\n");

echo("User: <select name=\"user\" size=\"1\">\n");
if ($user == "%") {
  echo(" <option value=\"%\" selected>All</option>\n");
} else {
  echo(" <option value=\"%\">All</option>\n");
}
$userdata = mysql_query("SELECT DISTINCT user FROM tQueue ORDER BY user");
while ( $row = mysql_fetch_array($userdata) ) {
   if ($user == $row["user"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option  value=\"" . $row["user"] . "\"$sel>" . $row["user"] . "</option>\n");
}
echo("</select>\n");

$setstrings=array("","","","","");
$setstrings[$complete+2]="selected";

echo("Status: <select name=\"complete\" size=\"1\">\n");

for ($ii=0; $ii<count($compstrings); $ii++) {
  echo(" <option value=\"" . ($ii-2) . "\"" . $setstrings[$ii] . " >" .
       $compstrings[$ii] . "</option>\n");
}

echo("</select>\n");

echo("Machine: <select name=\"machinename\" size=1>");
if ($machinename == "%") {
  echo(" <option value=\"%\" selected>All</option>");
} else {
  echo(" <option value=\"%\">All</option>");
}
$machinedata=mysql_query("SELECT DISTINCT machinename" .
                       " FROM tQueue WHERE complete=-1 ORDER BY machinename");
while ( $row = mysql_fetch_array($machinedata) ) {
   if ($machinename == $row["machinename"]) {
       $sel=" selected";
   } else {
       $sel="";
   }
   echo(" <option value=" . $row["machinename"] . "$sel>" . $row["machinename"] . "</option>");
}
echo(" </select>\n");
echo("Note: <input type=text size=9 name=\"notemask\" value=\"$notemask\">\n");

echo("<INPUT TYPE=SUBMIT VALUE=\"Go\">");
echo("</FORM>");

// query celldb for queue entries matching search criteria

if (-2==$complete) {
  $sql="SELECT *,(time_to_sec(lastdate)-time_to_sec(startdate)) as duration" .
    " FROM tQueue" .
    " WHERE user like \"$user\"" .
    " AND note like \"%$notemask%\"" .
    " AND (machinename like \"$machinename\"" .
    " OR (\"%\"=\"$machinename\" AND isnull(machinename)))" .
    " ORDER BY $orderby";
} else {
  $sql="SELECT *,(time_to_sec(lastdate)-time_to_sec(startdate)) as duration" .
    " FROM tQueue" .
    " WHERE user like \"$user\"" .
    " AND note like \"%$notemask%\"" .
    " AND (machinename like \"$machinename\"" .
    " OR (\"%\"=\"$machinename\" AND isnull(machinename)))" .
    " AND complete=$complete" .
    " ORDER BY $orderby";
}
//echo("sql: $sql<br>\n");
$queuedata=mysql_query($sql);

if (!$queuedata) {
  echo("<P>Error performing query: " . mysql_error() . "</P>");
  exit();
}

// list each entry
$sorturl="<a href=\"queuemonitor.php?sessionid=$sessionid&userid=$userid&user=$user&complete=$complete&machinename=$machinename&notemask=$notemask&orderby=";

echo("<table>");
echo("<tr bgcolor=\"#bbbbff\">\n");
echo("  <td><b>" . $sorturl . "tQueue.id\">QID</a></b>&nbsp;</td>\n");
echo("  <td><b>&nbsp;" . $sorturl . "tQueue.user\">user</a></b>&nbsp;</td>\n");
echo("  <td><b>&nbsp;" . $sorturl . "tQueue.complete\">status</a>\n");
echo("(" . $sorturl . "duration\">dur</a>)</b>&nbsp;</td>\n");
echo("  <td><b>&nbsp;" . $sorturl . "tQueue.progress\">cnt</a></b>&nbsp;</td>\n");
if ($seclevel>=10) {
  echo("  <td><b>&nbsp;" . $sorturl . "tQueue.priority DESC\">pri</a></b>&nbsp;</td>\n");
}
//echo("  <td><b>&nbsp;" . $sorturl . "tQueue.progname\">proc name</a></b></td>\n");
echo("  <td><b>&nbsp;" . $sorturl . "tQueue.note\">note</a></b>&nbsp;</td>\n");
echo("  <td><b>&nbsp;" . $sorturl . "tQueue.machinename\">machine</a></b></td>\n");
echo("  <td><b>&nbsp;" . $sorturl . "tQueue.pid\">pid</a></b>&nbsp;</td>\n");
echo("   <td>&nbsp;</td>\n");
echo("   <td>&nbsp;</td>\n");
echo("  </tr>\n");

while ( $row = mysql_fetch_array($queuedata) ) {
  
  echo("<tr>\n");
  $deturl="<a href=\"queuedetails.php?sessionid=$sessionid&userid=$userid&user=$user&complete=$complete&machinename=$machinename&notemask=$notemask&orderby=$orderby&qid=" . $row["id"] . "\">";
  echo("   <td>$deturl" . $row["id"] . "</a></td>\n");
  echo("   <td>" . $row["user"] . "&nbsp;</td>\n");
  
  if ($row["complete"]==-1) {
    echo("   <td>&nbsp;<font color=\"#999900\">");
    echo($compstrings[($row["complete"]+2)] . "</font>");
    echo("(" . sec2hrs($row["duration"]) . ")");
  } elseif ($row["complete"]==0) {
    echo("   <td>&nbsp;<font color=\"#000099\">");
    echo($compstrings[($row["complete"]+2)] . "</font>");
  } elseif ($row["complete"]==1) {
    echo("   <td>&nbsp;<font color=\"#999999\">");
    echo($compstrings[($row["complete"]+2)] . "</font>");
    echo("(" . substr($row["lastdate"],0,10) . ")");
  } else {  //complete==2 : dead
    echo("   <td>&nbsp;<font color=\"#cc0000\">");
    echo($compstrings[($row["complete"]+2)] . "</font>");
    echo("(" . substr($row["lastdate"],0,10) . ")");
  }
  echo("&nbsp;</td>\n");

  echo("   <td align=right>");
  if ($row["complete"]==0) {
    echo("");
  } elseif ($row["complete"]==1) {
    echo("");
  } else {
    echo($row["progress"]);
  }
  echo("&nbsp;</td>\n");
  if ($seclevel>=10) {
    echo("<td align=right>" . $row["priority"] . "&nbsp;</td>\n");
  }
  if (""==$row["note"]) {
    echo("   <td>" . $row["progname"] . "&nbsp;</td>\n");
  } else {
    echo("   <td>" . $row["note"] . "&nbsp;</td>\n");
  }

  if ($row["complete"]==0) {
    echo("   <td>&nbsp;</td>\n");
    echo("   <td>&nbsp;</td>\n");
  } else {
    echo("   <td>" . $row["machinename"] . "&nbsp;</td>\n");
    echo("   <td>" . $row["pid"] . "&nbsp;</td>\n");
  }
  
  if ((1==$row["complete"] || 2==$row["complete"]) && ($seclevel>=5 || ($row["user"]==$userid && $seclevel>0))) {
    echo("   <td>&nbsp;" . $sorturl . $orderby . "&action=-1&target=" . $row["id"] . "\">DELETE</a>&nbsp;");
    echo($sorturl . $orderby . "&action=1&target=" . $row["id"] . "\">RESET</a>&nbsp;</td>");
  } elseif (0==$row["complete"] && ($seclevel>=5 || ($row["user"]==$userid && $seclevel>0))) {
    echo("   <td>&nbsp;" . $sorturl . $orderby . "&action=-1&target=" . $row["id"] . "\">DELETE</a>&nbsp;</td>");
  } elseif (-1==$row["complete"] && ($seclevel>=5 || ($row["user"]==$userid && $seclevel>0))) {
    echo("   <td>&nbsp;" . $sorturl . $orderby . "&action=-2&target=" . $row["id"] . "\">KILL</a>&nbsp;</td>");
  } else {
    echo("   <td></td>\n");
  }
  
  if (0==$row["complete"]) {
    echo("   <td>&nbsp;</td>\n");
  } elseif (1==$row["complete"]) {
    echo("   <td><a href=\"queue/" . $row["id"]. ".out\">DUMP</a>&nbsp;\n");
    echo("<a href=\"queue/" . $row["id"]. ".1.jpg\">1</a>&nbsp;\n");
    echo("<a href=\"queue/" . $row["id"]. ".2.jpg\">2</a>&nbsp;\n");
    echo("<a href=\"queue/" . $row["id"]. ".3.jpg\">3</a>&nbsp;</td>\n");
  } else {
    echo("   <td><a href=\"queue/" . $row["id"]. ".out\">DUMP</a>&nbsp;</td>\n");
  }
  
  echo("</tr>\n");
}
echo("</table>\n");

queuefooter();

?>

<em>This page refreshes automatically every 60 seconds</em><br>

</BODY>
</HTML>
