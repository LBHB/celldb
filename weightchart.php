<?php
/*** CELLDB
animals.php - list/edit animals with data in celldb
created 8/3/2005 - SVD
***/

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";


// action -1 - action failed
//         0 - add animal
//         1 - edit animal
//         2 - delete animal

if (""==$action) {
  $action=-2;
 }
$errormsg="";


?>

<HTML>
<HEAD>
<TITLE><?php echo("$siteinfo"); ?> - Weight Chart - print landscape </TITLE>
  <link rel="shortcut icon" href="favicon.ico" />
</HEAD>

<?php


echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

// header
//cellheader();

if (!isset($species)) {
  $species="ferret";
}
if (!isset($maxonschedule)) {
  $maxonschedule=1;
}

if (isset($animals) && "all"==$animals) {
   //generate list of all animals for dumping all the charts in one page
 
   $animals="";
   // find all active animals (excluding test fake animal)
   $sql="SELECT *, (implanted=1) as imp FROM gAnimal".
     " WHERE onschedule<=$maxonschedule AND animal<>\"Test\" AND lab like '$LAB' AND species=\"$species\"" .
     " ORDER BY (implanted=1) DESC,animal";
   $adata=mysql_query($sql);
 
   $setcounter=-1;
   $acounter=0;
   $lastimplanted=-1;
   $animalset=array("");
   while ($row=mysql_fetch_array($adata)) {  
    if (4==$acounter || ($lastimplanted && 0==$row["implanted"])) {
      $setcounter++;
      $animalset[$setcounter]=$animals;
      
      // start over
      $acounter=0;
    }
    if (0==$acounter) { 
      $animals=$row["animal"];
    } else { 
      $animals=$animals . "," . $row["animal"];
    } 
    $acounter++;
    $lastimplanted=$row["imp"];
   }
   if ($acounter>0) {
      $setcounter++;
      $animalset[$setcounter]=$animals;
   }
   $setcounter++;
} elseif (isset($animals)) {
  $animalset[0]=$animals;
  $setcounter=1;
}

if (!isset($month) || !isset($year) || !isset($animals)) {

  cellheader();
  
  if (""!=$errormsg) {
    echo("<p><b><font color=\"#CC0000\">$errormsg</font></b></p>\n");
  }
  
  $year=date("Y");
  $month=date("m");

   $animals="";
   // find all active animals (excluding test fake animal)
   $sql="SELECT *, (implanted=1) as imp FROM gAnimal".
     " WHERE onschedule<=$maxonschedule AND animal<>\"Test\" AND lab like '$LAB' AND species=\"$species\"" .
     " ORDER BY (implanted=1) DESC,animal";
   $adata=mysql_query($sql);
 
  $acounter=0;
  $lastimplanted=-1;
  echo("<a href=\"weights.php?pendate=$pendate&openforedit=0\">Cancel</a>\n");
  echo("<table>\n");

  echo("<tr><td>");
  echo("<FORM ACTION=\"weightchart.php\" METHOD=POST>\n");

  echo("Year:");
  echo("&nbsp;<INPUT TYPE=TEXT SIZE=5 NAME=\"year\" value=\"$year\">\n");
  echo("&nbsp;Month (1-12):");
  echo("&nbsp;<INPUT TYPE=TEXT SIZE=3 NAME=\"month\" value=\"$month\">\n");
  echo("&nbsp;Animals (comma sep):");
  echo("&nbsp;<INPUT TYPE=TEXT SIZE=25 NAME=\"animals\" value=\"all\">\n");
  echo("<INPUT TYPE=SUBMIT VALUE=\"Make chart\">\n");
  echo("</form>\n");
  echo("</td></tr>\n");

  
  while ($row=mysql_fetch_array($adata)) {
    if (4==$acounter || ($lastimplanted && 0==$row["implanted"])) {
      echo("<tr><td>");
      echo("<FORM ACTION=\"weightchart.php\" METHOD=POST>\n");
      
      echo("Year:");
      echo("&nbsp;<INPUT TYPE=TEXT SIZE=5 NAME=\"year\" value=\"$year\">\n");
      echo("&nbsp;Month (1-12):");
      echo("&nbsp;<INPUT TYPE=TEXT SIZE=3 NAME=\"month\" value=\"$month\">\n");
      echo("&nbsp;Animals (comma sep):");
      echo("&nbsp;<INPUT TYPE=TEXT SIZE=25 NAME=\"animals\" value=\"$animals\">\n");
      echo("<INPUT TYPE=SUBMIT VALUE=\"Make chart\">\n");
      echo("</form>\n");
      echo("</td></tr>\n");
      
      // start over
      $acounter=0;
    }
    if (0==$acounter) {
      $animals=$row["animal"];
    } else {
      $animals=$animals . "," . $row["animal"];
    }
    $acounter++;
    $lastimplanted=$row["imp"];
  }
  // any remaining animals?
  echo("<tr><td>");
  echo("<FORM ACTION=\"weightchart.php\" METHOD=POST>\n");
  
  echo("Year:");
  echo("&nbsp;<INPUT TYPE=TEXT SIZE=5 NAME=\"year\" value=\"$year\">\n");
  echo("&nbsp;Month (1-12):");
  echo("&nbsp;<INPUT TYPE=TEXT SIZE=3 NAME=\"month\" value=\"$month\">\n");
  echo("&nbsp;Animals (comma sep):");
  echo("&nbsp;<INPUT TYPE=TEXT SIZE=25 NAME=\"animals\" value=\"$animals\">\n");
  echo("<INPUT TYPE=SUBMIT VALUE=\"Make chart\">\n");
  echo("</form>\n");
  echo("</td></tr>\n");
  echo("</table>\n");
  
  

  cellfooter();
  
  exit();
}

for ($setidx=0;$setidx<$setcounter;$setidx++) {

if ($setidx>0){
   $pbstring="style=\"page-break-before: always\"";
} else {
  $pbstring="";
}

 $aset=explode(",",$animalset[$setidx]);


 $firststamp=mktime(0,0,0,$month,1,$year);
 $laststamp=mktime(0,0,0,$month+1,1,$year);
 $mstring=date("F",$firststamp);

 
 $animal_id=array();
 $pull_weight=array();
 $eartag=array();
 $roundto="0";
for ($ii=0; $ii<count($aset); $ii++) {
  $aset[$ii]=trim($aset[$ii]);
  $sql="SELECT * FROM gAnimal WHERE animal like \"" . $aset[$ii] . "\"";
  $adata=mysql_query($sql);
  if ($row=mysql_fetch_array($adata)){
    $animal_id[$ii]=$row["id"];
    $pull_weight[$ii]=$row["pullweight"];
    $eartag[$ii]=$row["eartag"];
    $tattoo[$ii]=$row["tattoo"];
    if ("rat"==$row["species"] || "mouse"==$row["species"]) {
      $roundto="1";
    }
  } else {
    $animal_id[$ii]=0;
    $pull_weight[$ii]="";
    $eartag[$ii]="";
  }
}

$ff="<font size=1>";

echo("<table $pbstring width=\"100%\" border=1 cellspacing=0 cellpadding=1>\n");
echo("<tr><td align=\"center\" colspan=" . (count($aset)*5+1) . "><b>Weights - $mstring $year</b></td></tr>\n");

echo("<tr><td width=\"4%\">&nbsp;</td>\n");
for ($ii=0; $ii<count($aset); $ii++) {
  echo("<td align=\"center\" colspan=5><b>" . ucfirst($aset[$ii]) . "</b></td>");
}
echo("</tr>\n");

echo("<tr><td>&nbsp;</td>\n");
for ($ii=0; $ii<count($aset); $ii++) {
  echo("<td colspan=1>$ff Tat: " . $tattoo[$ii] . "</td>");
  echo("<td colspan=2>$ff Tag: " . $eartag[$ii] . "</td>");
  echo("<td colspan=2 align=\"right\">$ff Pull: " . $pull_weight[$ii] .
       " (80)<br>".round($pull_weight[$ii]/0.8*0.75) . " (75)</td>\n");
}
echo("</tr>\n");

echo("<tr><td align=\"center\">$ff<b>Date</b></td>\n");
for ($ii=0; $ii<count($aset); $ii++) {
  echo("<td align=\"center\" width=\"7%\">$ff<b>Weight</b></td>");
  echo("<td align=\"center\" width=\"5%\">$ff<b>on/off<br>Study</b></td>");
  echo("<td align=\"center\" width=\"4%\">$ff<b>Time</b></td>");
  echo("<td align=\"center\" width=\"4%\">$ff<b>H2O</b></td>\n");
  echo("<td align=\"center\" width=\"4%\">$ff<b>Health</b></td>\n");
}
echo("</tr>\n");
 
for ($curstamp=$firststamp; $curstamp<$laststamp;
     $curstamp=$curstamp+(60*60*24)) {
  
  $dd=date("d",$curstamp) + 0;
  $dow=date("w",$curstamp) + 0;
  if ($dow==0 || $dow==6) {
    $bgtext="align=\"center\" bgcolor=\"#AAAAAA\"";
  } else {
    $bgtext="align=\"center\"";
  }
  echo("<tr><td $bgtext>$ff $dd</td>\n");

  for ($ii=0; $ii<count($aset); $ii++) {
    $sql="SELECT schedule,round(weight,$roundto) as weight,".
      " round(water,$roundto) as water, left(timeonoroff,5) as time".
      " FROM gHealth".
      " WHERE animal_id=" . $animal_id[$ii] .
      " AND date=\"$year-$month-$dd\"";
    $adata=mysql_query($sql);
    if ($row=mysql_fetch_array($adata)){
      $ww=$row["weight"];
      $ss=$row["schedule"];
      $water=$row["water"];
      $timeonoff=$row["time"];
      if ($ww<=0) {
        $ww="&nbsp;";
      }
      if ($ss) {
        $ss="on";
      } else {
        $ss="&nbsp;";
      }
      if ($water<=0) {
        $water="&nbsp;";
      }
      if ($timeonoff=="00:00") {
        $timeonoff="&nbsp;";
      }
    } else {
      $ww="&nbsp;";
      $ss="&nbsp;";
      $water="&nbsp;";
    }
    echo("<td $bgtext>$ff $ww</td>\n");
    echo("<td $bgtext>$ff $ss</td>\n");
    echo("<td $bgtext>$ff $timeonoff</td>\n");
    echo("<td $bgtext>$ff $water</td>\n");
    echo("<td $bgtext>$ff &nbsp;</td>\n");
  }
  echo("</tr>\n");
}

echo("<tr><td v align=\"top\">$ff Notes:</td>\n");
for ($ii=0; $ii<count($aset); $ii++) {
  echo("<td colspan=5>&nbsp;<br><br><br></td>");
}
echo("</tr>\n");
echo("</table>\n");

} //end for $setidx

//echo("<table style=\"page-break-before: always\">");
//echo("<tr><td>I like ferrets</td></tr>\n");

//cellfooter();

?>
