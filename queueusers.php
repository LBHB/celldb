<?php

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";

if (""==$allowqueuemaster) {
  $allowqueuemaster=1;
}
if (""==$orderby) {
  $orderby="gUserPrefs.userid";
}
if (""==$action) {
  $action=0;
}

if (0!=$action && $target>0) {

  $sql="SELECT * FROM gUserPrefs WHERE id=$target";
  $userdata=mysql_query($sql);
  
  if (0==mysql_num_rows($userdata)) {
    //echo("Invalid user id $target<br>");
  } else {
    
    // queue entry does exist
    $row = mysql_fetch_array($userdata);
    
    /* actions:
       1. change colors from input text boxes
    */
    
    if (1==$action) {
      //echo("Changing colors for id=$target<br>\n");
      $sql="UPDATE gUserPrefs SET ".
        "bgcolor=\"$bgcolor\",".
        "fgcolor=\"$fgcolor\",".
        "linkfg=\"$newlinkfg\",".
        "vlinkfg=\"$newvlinkfg\",".
        "alinkfg=\"$newalinkfg\"".
        " WHERE id=$target";
      mysql_query($sql);

      //echo("$sql\n");
    }
  }
  $redirurl="queueusers.php?userid=$userid&sessionid=$sessionid&orderby=$orderby&action=0";
  header ("Location: $redirurl");
  exit;                 /* Make sure that code below does not execute */

}
?>

<HTML>
<HEAD>
<TITLE>dbqueue users</TITLE>
</HEAD>
<?php
echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

$redirurl="queueusers.php?userid=$userid&sessionid=$sessionid&orderby=$orderby&activeusers=$activeusers&action=0";
echo("<meta http-equiv=\"Refresh\" content=\"60; URL=$redirurl\">");
?>
<meta NAME="description" CONTENT="Queue users">

<?php
  //echo( "<b>Queue users</b>\n" );
  //echo(" (<a href=\"queue/queuemasterlog.txt\">today's log</a>");
  //echo(" <a href=\"queue/queuemasterlog.txt.1\">yesterday's log</a>");
  //echo(" <a href=\"../svd/queue.htm\">help</a>)<br>");
cellheader();

// summary stats
include "./queuesum.php";

echo("<table><tr><td>\n");

$acturl="queueusers.php?userid=$userid&sessionid=$sessionid&" .
        "orderby=$orderby&activeusers=";

echo("&nbsp;Show inactive: \n");
if (""==$activeusers) {
  $activeusers=1;
}
if (0==$activeusers) {
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

echo("$b1yes<a href=\"" . $acturl . "0\">Yes</a>$b2yes\n");
echo(" / $b1no<a href=\"" . $acturl . "1\">No</a>$b2no\n");
echo("&nbsp;&nbsp;<a href=\"" . $acturl . $activeusers ."\">Refresh</a>");

echo("</td></tr></table>\n");

// query celldb for queue entries matching search criteria

if ($activeusers) {
  $sql="SELECT gUserPrefs.*,count(tQueue.id) as jobcount," .
    " sum(tQueue.complete in (0,-1)) as activejobs".
    " FROM tQueue LEFT JOIN gUserPrefs" .
    " ON gUserPrefs.userid=tQueue.user" .
    " GROUP BY tQueue.user" .
    " HAVING activejobs>0" .
    " ORDER BY $orderby";
} else {
  $sql="SELECT gUserPrefs.*,count(tQueue.id) as jobcount," .
    " sum(tQueue.complete in (0,-1)) as activejobs".
    " FROM gUserPrefs LEFT JOIN tQueue" .
    " ON gUserPrefs.userid=tQueue.user" .
    " GROUP BY gUserPrefs.id" .
    " ORDER BY $orderby";
}

//echo("sql: $sql<br>\n");
$userdata=mysql_query($sql);

if (!$userdata) {
  echo("<P>Error performing query: " . mysql_error() . "</P>");
  exit();
}

$maxjobs=1;
while ( $row = mysql_fetch_array($userdata) ) {
  if ($row["jobcount"]>$max_jobs) {
    $max_jobs=$row["jobcount"];
  }
}
//reset for display
mysql_data_seek($userdata,0);
$jscale=400/$max_jobs;

// list each entry
$sorturl="queueusers.php?userid=$userid&sessionid=$sessionid&activeusers=$activeusers&orderby=";

echo("<table>");
echo("<tr bgcolor=\"#bbbbff\">\n");
echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "gUserPrefs.id\">id</a></b><br></td>\n");
echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "gUserPrefs.userid\">name</a></b><br></td>\n");
echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "gUserPrefs.lab\">lab</a></b><br></td>\n");
echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "jobcount DESC\">jobs</a>&nbsp;</td>\n");

$keycount=4;
for ($ii=0; $ii<$keycount; $ii++) {
  echo("  <td width=" . (400/$keycount) . " align=right>" . 
       round(($ii+1)*$max_jobs/$keycount) . "&nbsp;</td>\n");
}
echo("</tr>\n");

while ( $row = mysql_fetch_array($userdata) ) {
  
  echo("<tr>\n");
  echo("   <td align=\"right\">" . $row["id"] . "&nbsp;</td>\n");
  
  $joburl="<a href=\"queuemonitor.php?userid=$userid&sessionid=$sessionid" .
    "&user=" . $row["userid"] . "&complete=-1\">";
  echo("   <td>$joburl" . $row["userid"] . "</a>&nbsp;</td>\n");
  echo("   <td>" . $row["lab"] . "&nbsp;</td>\n");
  
  $sql="SELECT sum(complete=-1) as activecount," .
    "sum(complete=0) as newcount," .
    "sum(complete=1) as donecount," .
    "sum(complete=2) as deadcount" .
    " FROM tQueue WHERE user=\"" . $row["userid"] . "\"";
  $jobdata=mysql_query($sql);
  $jobrow= mysql_fetch_array($jobdata);
  
  echo("   <td>");
  if ($row["jobcount"]>0) {
    echo($row["jobcount"] . ": ");
    
    echo($jobrow["activecount"] . "/");
    echo($jobrow["newcount"] . "/");
    echo($jobrow["donecount"] . "/");
    echo($jobrow["deadcount"]);
  } else {
    echo("0");
  }
  echo("&nbsp;</td>\n");
  
  echo("   <td colspan=$keycount align=\"left\">");
  if ($jobrow["donecount"] > 0) {
    echo("<a href=\"queuemonitor.php?userid=$userid&sessionid=$sessionid" .
      "&user=" . $row["userid"] . "&complete=1\">");
    echo("<img border=0 src=\"images/black.jpg\"");
    echo("width=" . round($jobrow["donecount"]*$jscale+1) . " height=12></a>");
  }
  if ($jobrow["activecount"] > 0) {
    echo("<a href=\"queuemonitor.php?userid=$userid&sessionid=$sessionid" .
      "&user=" . $row["userid"] . "&complete=-1\">");
    echo("<img border=0 src=\"images/blue.jpg\"");
    echo("width=" . round($jobrow["activecount"]*$jscale+1) . " height=12></a>");
  }
  if ($jobrow["newcount"] > 0) {
    echo("<a href=\"queuemonitor.php?userid=$userid&sessionid=$sessionid" .
      "&user=" . $row["userid"] . "&complete=0\">");
    echo("<img border=0 src=\"images/green.jpg\"");
    echo("width=" . round($jobrow["newcount"]*$jscale+1) . " height=12></a>");
  }
  if ($jobrow["deadcount"] > 0) {
    echo("<a href=\"queuemonitor.php?userid=$userid&sessionid=$sessionid" .
      "&user=" . $row["userid"] . "&complete=2\">");
    echo("<img border=0 src=\"images/red.jpg\"");
    echo("width=" . round($jobrow["deadcount"]*$jscale+1) . " height=12></a>");
  }

  echo("</td>\n");
  
  echo("   </td>\n");

  echo("</tr>\n");

  if ($jobrow["activecount"]>0){
    $sql="SELECT substring(note,instr(note,\"/\")+1) as grp,".
      " count(id) as total, sum(complete=0) as new,".
      " sum(complete=1) as done,sum(complete=-1) as running,".
      " sum(complete=2) as dead, round(avg(priority),0) as priority".
      " FROM tQueue WHERE user=\"" . $row["userid"] . "\"".
      " GROUP BY grp ORDER BY priority DESC, min(id)";
    $jobdata=mysql_query($sql); 
    while ($jr=mysql_fetch_array($jobdata)){
      if ($jr["total"] > $jr["done"]) {
        echo("<tr>");
        $joburl="<a href=\"queuemonitor.php?userid=$userid" .
                "&sessionid=$sessionid&user=" . $row["userid"] . 
                "&complete=-1&notemask=". $jr["grp"]. "\">";
        echo("<td colspan=\"5\">$joburl". $jr["grp"] . "</a>(".
             $jr["priority"].")</td>\n");
        echo("<td colspan=\"4\">\n");
        echo("New: " . $jr["new"] .
             " - Running: " . $jr["running"] .
             " - Done: " . $jr["done"] .
             " - Dead: " . $jr["dead"] .
             "<br>\n");
        echo("</td>");
        echo("</tr>\n");
      } 
    }
  }
}

echo("<tr ><td></td><td></td><td></td><td></td>\n");
echo("   <td valign=center colspan=" . ($keycount+3) . ">\n");
echo("   <img border=0 src=\"images/black.jpg\" width=12 height=12>");
echo("&nbsp;complete&nbsp;\n");
echo("   <img border=0 src=\"images/blue.jpg\" width=12 height=12>");
echo("&nbsp;running&nbsp;\n");
echo("   <img border=0 src=\"images/green.jpg\" width=12 height=12>");
echo("&nbsp;not started&nbsp;\n");
echo("   <img border=0 src=\"images/red.jpg\" width=12 height=12>");
echo("&nbsp;dead\n");

echo("   </td>\n");
echo("</tr>\n");

echo("<tr>\n");
echo("<td colspan=8 bgcolor=\"#bbbbff\"><b>My settings:</b></td>");
echo("</tr>\n");

$sql="SELECT gUserPrefs.*,count(tQueue.id) as jobcount" .
     " FROM gUserPrefs LEFT JOIN tQueue" .
     " ON gUserPrefs.userid=tQueue.user" .
     " WHERE gUserPrefs.userid=\"$userid\"" .
     " GROUP BY gUserPrefs.id";
$userdata=mysql_query($sql);
while ( $row = mysql_fetch_array($userdata) ) {

  echo("<tr>\n");
  echo("<td colspan=3></td>\n");
  echo("<td colspan=5>\n");
  echo(" <FORM ACTION=\"queueusers.php\" METHOD=GET>\n");
  echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
  echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
  echo(" <input type=\"hidden\" name=\"orderby\" value=\"$orderby\">\n");
  echo(" <input type=\"hidden\" name=\"action\" value=\"1\">\n");
  echo(" <input type=\"hidden\" name=\"target\" value=\"" . $row["id"] . "\">\n");
  echo(" <input type=\"hidden\" name=\"activeusers\" value=\"$activeusers\">\n");
  
  echo("bg");
  echo(" <input type=text size=9 name=\"bgcolor\" value=\"$userbg\">\n");
  echo("fg");
  echo(" <input type=text size=9 name=\"fgcolor\" value=\"$userfg\">\n");
  echo("<font color=\"$linkfg\">ln</font>");
  echo(" <input type=text size=9 name=\"newlinkfg\" value=\"$linkfg\"><br>\n");
  echo("<font color=\"$vlinkfg\">vln</font>");
  echo(" <input type=text size=9 name=\"newvlinkfg\" value=\"$vlinkfg\">\n");
  echo("<font color=\"$alinkfg\">aln</font>");
  echo(" <input type=text size=9 name=\"newalinkfg\" value=\"$alinkfg\">\n");
  
  echo("<INPUT TYPE=SUBMIT VALUE=\"Change\">");
  
  echo(" </form>\n");
  echo("</td>\n</tr>\n");
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
