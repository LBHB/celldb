<?php

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";
include_once "./dbgraph.php";
?>

<HTML>
<HEAD>
  <TITLE><?php echo($siteinfo);?> stats</TITLE>
  <meta NAME="description" CONTENT="<?php echo($siteinfo);?> stats">

</HEAD>


<?php
echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

// header
cellheader();

// summary stats

if (""==$statcode) {
  $statcode="weight";
}
if (""==$timeframe) {
  $timeframe="month";
}
if (""==$stat) {
  $stat="avg";    // avg or sum
}

// special action for weight, figure out pull weight
if ("weight"==$statcode) {
  $sql="SELECT * FROM gAnimal WHERE animal='$animal'";
  $animaldata=mysql_query($sql);
  if ($row=mysql_fetch_array($animaldata)) {
    $pullweight=$row["pullweight"];
  } else {
    $pullweight=1500;
  }
  if (0==$pullweight) {
    $pullweight=1500;
  }
}

// set criteria to match query specs

// this works for weight and water
if ("weight"==$statcode || "water"==$statcode) {
  // this works for weight and water
  $datefield="gHealth.date";
  $selvar="$stat($statcode)+1";
} elseif ("b_"==substr($statcode,0,2)) {
  $datefield="gPenetration.pendate";
  $selvar="$stat(gData.value)+0.01";
} else {
  $datefield="gPenetration.pendate";
  $selvar="$stat(corrtrials)/(avg(trials)+(sum(trials)=0))*100";
}

if ("month"==$timeframe) {
  $datevar="date_format($datefield,\"%d\") as tm";
  $datecrit=" WHERE $datefield + INTERVAL 1 MONTH > now()";
  $xlabel="day";
  $mmax=30;
} else {
  $datevar=" date_format($datefield,\"%m\") as tm";
  $datecrit=" WHERE $datefield + INTERVAL 1 YEAR > now()";
  $xlabel="month";
  $mmax=12;
}

if ("b_"==substr($statcode,0,2)) {
  $sql="SELECT $selvar as value,$datevar, min(pendate) as mindate ".
    " FROM gPenetration LEFT JOIN gCellMaster ON gPenetration.id=gCellMaster.penid".
    " LEFT JOIN gData ON gCellMaster.id=gData.masterid" .
    "$datecrit AND gPenetration.animal='$animal'".
    " AND gData.name='" . substr($statcode,2) . "'".
    " GROUP BY tm ORDER BY mindate";
  
} elseif ("weight"==$statcode || "water"==$statcode) {
  $sql="SELECT $selvar as value,$datevar, min(date) as mindate ".
    " FROM gAnimal LEFT JOIN gHealth ON gAnimal.id=gHealth.animal_id".
    "$datecrit AND gAnimal.animal='$animal' AND $statcode>0 GROUP BY tm ORDER BY mindate";
} else {
  $sql="SELECT $selvar as value,$datevar, min(pendate) as mindate ".
    " FROM gPenetration LEFT JOIN gCellMaster ON gPenetration.id=gCellMaster.penid".
    " LEFT JOIN gDataRaw ON gCellMaster.id=gDataRaw.masterid" .
    "$datecrit AND gPenetration.animal='$animal' GROUP BY tm ORDER BY mindate";
}

$eventdata=mysql_query($sql);
//echo("$sql<br>");

//set up the chart
$chart = new svdChart();                        // Object initialized
$chart->setTitle("$statcode over last $timeframe");  // Title of Chart
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
$chart->setXlabel($xlabel);

$expectedtm=-1;
while ( $row = mysql_fetch_array($eventdata) ) {
  $tm=intval($row["tm"]);
  if (-1==$expectedtm) {
    $expectedtm=$tm;
  }
  if ($tm>$mmax) {
    $mmax=$tm;
  }
  while ($expectedtm < $tm or ($expectedtm>$tm and $expectedtm < $mmax+1)) {
    //echo("$expectedtm $tm<br>");
    if ("weight"==$statcode) {
      $chart->addBar(sprintf("%02d",$expectedtm),0,0);
      $chart->addBar(sprintf("%02d",$expectedtm),0,1);
    } else {
      $chart->addBar(sprintf("%02d",$expectedtm),0);
    }
    $expectedtm++;
    if ($expectedtm==$mmax+1) {
      $expectedtm=1;
    }
  }
  $expectedtm=$tm+1;
  $ecount=$row["value"];
  if (0==$ecount){
    $ecount=0.1;
  }
  if ("weight"==$statcode && $seclevel>2 && $ecount<$pullweight) {
    $chart->addBar(sprintf("%02d",$tm),0,0);
    $chart->addBar(sprintf("%02d",$tm),$ecount,1);
  } elseif ("weight"==$statcode) {
    $chart->addBar(sprintf("%02d",$tm),$ecount,0);
    $chart->addBar(sprintf("%02d",$tm),0,1);
  } else {
    $chart->addBar(sprintf("%02d",$tm),$ecount);
  }
}

if ("weight"==$statcode && $ecount<$pullweight) {
  $chart->setBarStacked(1); 
  $chart->setLegend("<a href=\"animals.php?animal=$animal\">$animal</a>",0);
  $chart->setLegend("under pull",1);
} else {
  $chart->setLegend("<a href=\"animals.php?animal=$animal\">$animal</a>",0);
}

$chart->prepare();                               // Chart Preparation
$chart->generateChartHtml();                     // Chart Generation

echo("<p>");
$animallistdata = mysql_query("SELECT animal FROM gAnimal ORDER BY animal");
echo("Animal: ");
echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\">\n");
while ( $row = mysql_fetch_array($animallistdata) ) {
  if ($animal == $row["animal"]) {
    $sel=" selected";
  } else {
    $sel="";
  }
  echo(" <option value=\"celldbstats.php?userid=$userid" .
       "&sessionid=$sessionid&animal=" .$row["animal"] . 
       "&timeframe=$timeframe&statcode=$statcode\"$sel>" . 
       $row["animal"] . "</option>\n");
 }
echo("</select>");

echo(" Stat: ");
$statstrings=array("weight","water","perf");
$statcodes=$statstrings;
echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\" size=\"1\">\n");
for ($ii=0; $ii<count($statcodes); $ii++) {
  if ($statcodes[$ii] == $statcode) {
    $sel=" selected";
  } else {
    $sel="";
  }
  echo(" <option value=\"celldbstats.php?userid=$userid" .
       "&sessionid=$sessionid&animal=$animal" . 
       "&timeframe=$timeframe&statcode=" .$statcodes[$ii] . 
       "&stat=$stat\"$sel>" . 
       $statstrings[$ii] . "</option>\n");
}

$sql="SELECT gData.name, max(gData.datatype) as mdatatype, min(gData.id) as minid".
    " FROM gData,gCellMaster".
    " WHERE gData.masterid=gCellMaster.id AND animal=\"$animal\"".
    " GROUP BY gData.name HAVING mdatatype=0 ORDER BY parmtype DESC,minid";
$pdata=mysql_query($sql);
while ($prow=mysql_fetch_array($pdata)) {
  if ("b_" . $prow["name"] == $statcode) {
    $sel=" selected";
  } else {
    $sel="";
  }
  echo(" <option value=\"celldbstats.php?userid=$userid" .
       "&sessionid=$sessionid&animal=$animal" . 
       "&timeframe=$timeframe&statcode=b_" . $prow["name"] . 
       "&stat=$stat\"$sel>" . 
       $prow["name"] . "</option>\n");
}

echo("</select>\n");

echo(" Compute: ");
$computestrings=array("Average","Sum");
$computecodes=array("avg","sum");
echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\" size=\"1\">\n");
for ($ii=0; $ii<count($computecodes); $ii++) {
  if ($computecodes[$ii] == $stat) {
    $sel=" selected";
  } else {
    $sel="";
  }
  echo(" <option value=\"celldbstats.php?userid=$userid" .
       "&sessionid=$sessionid&animal=$animal" . 
       "&timeframe=$timeframe&statcode=$statcode" .
       "&stat=". $computecodes[$ii] . "\"$sel>" . 
       $computestrings[$ii] . "</option>\n");
}
echo("</select>\n");

echo(" Time frame: ");
$timestrings=array("month","year");
$timecodes=$timestrings;
echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\" size=\"1\">\n");
for ($ii=0; $ii<count($timecodes); $ii++) {
  if ($timecodes[$ii] == $timeframe) {
    $sel=" selected";
  } else {
    $sel="";
  }
  echo(" <option value=\"celldbstats.php?userid=$userid" .
       "&sessionid=$sessionid&animal=$animal" . 
       "&timeframe=" . $timecodes[$ii] . 
       "&statcode=$statcode&stat=$stat\"$sel>" . 
       $timestrings[$ii] . "</option>\n");
}
echo("</select>\n");

echo("</p>\n");



cellfooter();

?>
</BODY>
</HTML>
