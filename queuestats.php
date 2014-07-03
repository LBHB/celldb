<?php

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";

echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

  $redirurl="queuestats.php?sessionid=$sessionid&userid=$userid&statcode=$statcode&timeframe=$timeframe";
echo("<meta http-equiv=\"Refresh\" content=\"60; URL=$redirurl\">");
?>
<HTML>
<HEAD>
<TITLE>dbqueue stats</TITLE>
</HEAD>

<meta NAME="description" CONTENT="Queue machine usage monitor">

<?php
  //echo( "<b>Queue statistics</b>\n" );
  //echo(" (<a href=\"queue/queuemasterlog.txt\">today's log</a>");
  //echo(" <a href=\"queue/queuemasterlog.txt.1\">yesterday's log</a>");
  //echo(" <a href=\"../svd/queue.htm\">help</a>)<br>");
cellheader();

// summary stats
include "./queuesum.php";

if (""==$statcode) {
  $statcode="Jobs Completed";
}
if (""==$timeframe) {
  $timeframe="hour";
}
if (""==$cat1) {
  $cat1="";
  $catcount=1;
}
if (""==$cat2) {
  $catcount=1;
} elseif (""==$cat3) {
  $catcount=2;
}

//set up the chart
include_once "./dbgraph.php";
$chart = new svdChart();                        // Object initialized
$chart->setTitle("$statcode per $timeframe");  // Title of Chart
$chart->setBackground("0,0,0");            // Background of chart  [opt]
$chart->setLineColor("0,0,0");             // Line color separator [opt]
  $chart->setBarColor("0,0,255");              // Bar color  [opt]
$chart->setTitleColor("0,0,0");             // Color Title   [opt]
$chart->setLegendColor("255,255,255");     // Legend Color [opt]
$chart->setBarInfoColor("255,255,255");   // Bar Info Color       [opt]
$chart->setWidth(600);                           // Width of Chart
$chart->setHeight(500);                          // Height of Chart
$chart->setBarWidth(12);                         // Bar Width
$chart->setLeftSpace(75);                        // Left Space
$chart->setRightSpace(25);                       // Right Space
$chart->setLeftLegend(5);                       // Left Legend
$chart->setXlabel($timeframe);

// set criteria to match query specs
$fromstr="FROM tEvent";
$datefield="tEvent.eventdate";

if ("hour"==$timeframe) {
  $datevar="date_format($datefield,\"%k\") as tm";
  $datecrit="WHERE $datefield + INTERVAL 12 HOUR > now()";
} elseif ("day"==$timeframe) {
  $datevar="date_format($datefield,\"%b-%d\") as tm";
  $datecrit="WHERE $datefield + INTERVAL 7 DAY > now()";
} elseif ("week"==$timeframe) {
  $datevar=" date_format($datefield,\"%V\") as tm";
  $datecrit="WHERE $datefield + INTERVAL 56 DAY > now()";
} else {
  $datevar=" date_format($datefield,\"%b\") as tm";
  $datecrit="WHERE $datefield + INTERVAL 1 YEAR > now()";
}


// figure out relevant users
if ("Active Nodes"==$statcode) {
  $sql="SELECT DISTINCT user $fromstr $datecrit AND user<>\"queued\" AND user<>\"root\"";
} else {
  $sql="SELECT DISTINCT user $fromstr $datecrit AND user<>\"queued\" AND user<>\"root\"";
}
$userdata=mysql_query($sql);

// for each user, count matching events
$catid=0;
while ( $row = mysql_fetch_array($userdata) ) {
  $uid=$row["user"];
  
  if ("Jobs Completed"==$statcode) {
    $selstr="SELECT sum(code=3) as rc1," .
      " sum((user=\"$uid\") * (code=3)) as rc2,".
      " min(eventdate) as mindate,";
    $wheresup="";
  } elseif ("Dead Jobs"==$statcode) {
    $selstr="SELECT sum(code=4) as rc1," .
      " sum((user=\"$uid\") * (code=4)) as rc2,".
      " min(eventdate) as mindate,";
    $wheresup="AND tEvent.code=4";
  } elseif ("Killed Jobs"==$statcode) {
    $selstr="SELECT sum(code=5) as rc1," .
      " sum((user=\"$uid\") * (code=5)) as rc2,".
      " min(eventdate) as mindate,";
    $wheresup="AND tEvent.code=5";
  } elseif ("Mean Minutes per Job"==$statcode) {
    $selstr="SELECT count(tEvent.id) as rc1," .
      " sum(((TO_DAYS(tE2.eventdate)-TO_DAYS(tEvent.eventdate))*86400+".
      "       TIME_TO_SEC(tE2.eventdate)-TIME_TO_SEC(tEvent.eventdate)) *".
      "     (tEvent.user=\"$uid\")) /".
      " (sum(tEvent.user=\"$uid\"))/60 + 0 as rc2,".
      "sum(tEvent.user=\"$uid\") as rc3,".
      " min(tEvent.eventdate) as mindate,";
    $fromstr="FROM tEvent INNER JOIN tEvent tE2".
      " ON (tEvent.queueid=tE2.queueid AND tEvent.computerid=tE2.computerid)" ;
    $wheresup="AND tEvent.code=2 AND tE2.code=3 AND tE2.eventdate>tEvent.eventdate";
  } elseif ("Active Nodes"==$statcode) {
    $selstr="SELECT sum(code=11) as rc1," .
      " sum((queueid) * (code=11))/sum(code=11) as rc2,".
      " min(eventdate) as mindate,";
    $wheresup="";
  }
  
  $sql="$selstr $datevar $fromstr $datecrit $wheresup".
    " GROUP BY tm" .
    " ORDER BY mindate";
  
  $eventdata=mysql_query($sql);

  while ( $row = mysql_fetch_array($eventdata) ) {
    $ecount=$row["rc2"];
    if (0==$ecount){
      $ecount=0.1;
    }
    $chart->addBar($row["tm"],$ecount,$catid);
  }
  $chart->setLegend($uid,$catid);
  $catid=$catid+1;
}

$chart->prepare();                               // Chart Preparation
$chart->generateChartHtml();                         // Chart Generation

echo("Stat: ");
$statstrings=array("Jobs Completed","Dead Jobs","Killed Jobs",
                   "Mean Minutes per Job","Active Nodes");
$statcodes=$statstrings;
echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\" size=\"1\">\n");
for ($ii=0; $ii<count($statcodes); $ii++) {
  if ($statcodes[$ii] == $statcode) {
    $sel=" selected";
  } else {
    $sel="";
  }
  echo(" <option value=\"queuestats.php?userid=$userid&sessionid=$sessionid&userid=$userid&statcode=" . $statcodes[$ii] . "&timeframe=$timeframe&cat1=$cat1&cat2=$cat2\"$sel>" . $statstrings[$ii]. "</option>");
}
echo("</select>\n");

echo("Timeframe: ");
$popstrings=array("Hours","Days","Weeks","Months"); 
$popcodes=array("hour","day","week","month"); 
echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\" size=\"1\">\n");
for ($ii=0; $ii<count($popcodes); $ii++) {
  if ($popcodes[$ii] == $timeframe) {
    $sel=" selected";
  } else {
    $sel="";
  }
  echo(" <option value=\"queuestats.php?userid=$userid&sessionid=$sessionid&userid=$userid&statcode=$statcode&timeframe=" . $popcodes[$ii] . "&cat1=$cat1&cat2=$cat2\"$sel>" . $popstrings[$ii]. "</option>");
}
echo("</select>\n");

echo("<br>\n");


queuefooter();

echo("br" . $sql . "<br>\n");

exit;



