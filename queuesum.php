<?php

//echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");

// summary stats

$compstrings=array("any","running","not started","complete","dead");

if (""==$user) {
  if (""!=$lastjobuser) {
    $user=$lastjobuser;
  } elseif (""==$userid) {
    $user="%";
  } else {
    $user=$userid;
  }
}
if (""==$complete && ""!=$lastjobcomplete) {
  $complete=$lastjobcomplete;
} elseif (""==$complete) {
  $complete=-1;
}
if (""==$machinename) {
  $machinename="%";
}


$sql="SELECT complete,count(id) as activecount FROM tQueue WHERE user like \"$user\" GROUP BY complete ORDER BY complete";
$activedata=mysql_query($sql);
$userjobcount=array(0,0,0,0,0);
while ( $row = mysql_fetch_array($activedata) ) {
  $userjobcount[($row["complete"]+2)]=$row["activecount"];
}

$sql="SELECT complete,count(id) as activecount FROM tQueue GROUP BY complete ORDER BY complete";
$activedata=mysql_query($sql);
$jobcount=array(0,0,0,0,0);
while ( $row = mysql_fetch_array($activedata) ) {
  $jobcount[($row["complete"]+2)]=$row["activecount"];
}

echo("<a href=\"queuemonitor.php?userid=$userid&sessionid=$sessionid&user=$user&complete=$complete&notemask=" . rawurlencode($notemask) . "\">");
echo("Jobs:</a>  ");
for ($ii=1; $ii<count($compstrings); $ii++) {
  echo("<a href=\"queuemonitor.php?userid=$userid&sessionid=$sessionid&");
  echo("user=$user&complete=" . ($ii-2) . "&notemask=" . 
       rawurlencode($notemask) . "\">");
  echo($userjobcount[($ii)] . "/" . $jobcount[($ii)] . " " . 
       $compstrings[$ii] . "</a> / ");
}
echo(" (user/total) -- $userid($seclevel)<br>\n");

$sql="SELECT count(load1) as nodecount,sum(dead) as deadcount,sum(lastoverload * (1-dead)) as oloadcount,sum(maxproc * (1-lastoverload) * (1-dead)) as maxproc,avg(load1) as meanload FROM tComputer WHERE allowqueuemaster in (1,2);";
$compdata=mysql_query($sql);
$row = mysql_fetch_array($compdata);

echo("<a href=\"queuemachines.php?userid=$userid&sessionid=$sessionid&complete=$complete&notemask=" . rawurlencode($notemask) . "\">");
echo("Machines:</a>  ");
echo($row["nodecount"] . " nodes / ");
echo($row["deadcount"] . " dead / ");
echo($row["oloadcount"] . " o'load / ");
echo($row["maxproc"] . " procs max / ");
echo(sprintf("%.2f",$row["meanload"]) . " mean load\n");

echo(" --- ");

$sql="SELECT count(gUserPrefs.id) as usercount" .
     " FROM gUserPrefs";
$userdata=mysql_query($sql);
$row = mysql_fetch_array($userdata);
$usercount=$row["usercount"];

$sql="SELECT DISTINCT user" .
     " FROM tQueue" .
     " WHERE complete=-1 OR complete=0";
$userdata=mysql_query($sql);
$activeusercount=mysql_num_rows($userdata);

echo("<a href=\"queueusers.php?userid=$userid&sessionid=$sessionid&notemask=" . rawurlencode($notemask) . "&activeusers=1\">");
echo("Users:</a>  ");

echo($activeusercount . " active / ");
echo($usercount . " total");

echo(" --- ");

echo("<a href=\"queuestats.php?userid=$userid&sessionid=$sessionid&notemask=" . rawurlencode($notemask) . "&activeusers=1\">");
echo("Stats</a>  ");

echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");

?>
