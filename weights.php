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

// ok, checks done. perform actions
if (1==$action) {
  
  for ($ii = 1; $ii <= $acount; $ii++) {
    $weight_text="";
    $water_text="";
    $trained_text="";
    $onschedule_text="";
    if ($weight[$ii]!=$weight_old[$ii]) {
      $weight_text=" weight=" . $weight[$ii]*1 . ",";
    }
    if ($water[$ii]!=$water_old[$ii]) {
      $water_text=" water=" . $water[$ii]*1 . ",";
      }
    if ($trained[$ii]!=$trained_old[$ii]) {
      $trained_text=" trained=" . $trained[$ii]*1 . ",";
    }
    if ($onschedule[$ii]!=$onschedule_old[$ii]) {
      $onschedule_text=" schedule=" . $onschedule[$ii]*1 . ",";
      $sql="UPDATE gAnimal SET".
        " onschedule=" . $onschedule[$ii]*1 .
        " WHERE animal='". $animal[$ii] ."'";
      mysql_query($sql);
    }
    
    $weight[$ii]=$weight[$ii]*1.0;
    $water[$ii]=$water[$ii]*1;
    $trained[$ii]=$trained[$ii]*1;
    $onschedule[$ii]=$onschedule[$ii]*1;
    $wetfood[$ii]=$wetfood[$ii]*1;
    
    $sql="SELECT * FROM gHealth".
      " WHERE date='$pendate' AND animal_id=" . $animal_id[$ii];
    $hdata=mysql_query($sql);
    
    if ($row=mysql_fetch_array($hdata)) {
      
      // update existing gHealth entry
      $sql="UPDATE gHealth SET".
        " animal=\"" . $animal[$ii] . "\"," .
        " $weight_text" .
        " $water_text" .
        " $trained_text" .
        " $onschedule_text" .
        " timeonoroff='" . $timeonoroff[$ii] . "'," .
        " wetfood=" . $wetfood[$ii] . "," .
        " notes=\"" . stripslashes($notes[$ii]) . "\"" .
        " WHERE id=". $row["id"];
      //echo("$sql<BR>");
      $result=mysql_query($sql);
    } else {
      
      // insert new penetration only if values entered
      $sql="INSERT INTO gHealth".
        " (animal_id,animal,date,water,weight,trained,schedule,".
        " timeonoroff,wetfood,notes,addedby,info)".
        " VALUES (".$animal_id[$ii].",\"" . $animal[$ii] . 
        "\",'$pendate'," . 
        $water[$ii] . "," . $weight[$ii] . "," . 
        $trained[$ii] . "," . $onschedule[$ii] . "," . 
        "\"" . $timeonoroff[$ii] . "\"," . $wetfood[$ii] . "," . 
        "\"" . stripslashes($notes[$ii]) . "\"," . 
        "'$userid','weights.php')";
      //echo("$sql<BR>");
      $result=mysql_query($sql);
      
    }
    
    $sql="UPDATE gPenetration SET ".
      "weight=" . $weight[$ii] . ",".
      "water=" . $water[$ii].
      " WHERE pendate='$pendate' AND animal='". $animal[$ii] ."'";
    mysql_query($sql);
    $result=mysql_query($sql);
  }
  $openforedit=0;
  header("Location: weights.php?pendate=$pendate");
}

//if ($action>=0 && !$result) {
//  $errormsg=mysql_error();
//  $openforedit=1;
//  $action=-1;
// }

if (""==$pendate) {
  $pendate=date("Y-m-d");
 }

?>

<HTML>
<HEAD>
<TITLE><?php echo("$siteinfo"); ?> - Weights - <?php echo("$pendate"); ?></TITLE>
  <link rel="shortcut icon" href="favicon.ico" />
</HEAD>

<?php


echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

// header
cellheader();

if (""!=$errormsg) {
  echo("<p><b><font color=\"#CC0000\">$errormsg</font></b></p>\n");
}

// limit species in list
if (!isset($queryspecies) || ""==$queryspecies){
  $queryspecies="ferret,active";
}
mysql_query("UPDATE gUserPrefs SET ".
            " lastspecies=\"$queryspecies\" WHERE userid=\"$userid\"");

$queryspecies=explode(",",$queryspecies);
if (count($queryspecies)>1) {
  $sactive=$queryspecies[1];
} else {
  $sactive="active";
}
$queryspecies=$queryspecies[0];

if (0==$openforedit || ""==$openforedit) {
  $stamp=strtotime($pendate);
  $dow=date("w",$stamp);
  $mm=date("m",$stamp);
  $dd=date("d",$stamp);
  $yy=date("y",$stamp);
  $firststamp=mktime(0,0,0,$mm,$dd-$dow,$yy);
  $firstdate=date("Y-m-d",$firststamp);
  $lastdate=date("Y-m-d",mktime(0,0,0,$mm,$dd+6-$dow,$yy));
  $prevweek=date("Y-m-d",mktime(0,0,0,$mm,$dd-7,$yy));
  $nextweek=date("Y-m-d",mktime(0,0,0,$mm,$dd+7,$yy));
  echo("<b>Weight/water for week of $firstdate to $lastdate</b>\n");
  
  $specieslist=array("ferret,active","ferret,mine","ferret,all",
                     "rat,active","rat,mine","rat,all",
                     "mouse,active","mouse,mine","mouse,all",
                     "monkey,all","alien,all");
  echo("(Species: ");
  echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\">\n");
  echo(" <option value=\"weights.php?pendate=$firstdate&openforedit=0&queryspecies=%\"$sel>all</option>\n");
  for ($ii=0; $ii<count($specieslist); $ii++) {
    if ($queryspecies.",".$sactive == $specieslist[$ii]) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option value=\"weights.php?pendate=$firstdate&openforedit=0&queryspecies=" . $specieslist[$ii] . "\"$sel>" . $specieslist[$ii] . "</option>\n");
  }
  echo("</select>)&nbsp;\n");
  
  echo("<a href=\"weights.php?pendate=$prevweek&openforedit=0\">&lt;- Prev</a>\n");
  echo("<a href=\"weights.php?pendate=$nextweek&openforedit=0\">Next -&gt;</a>\n");
  echo(" - <a href=\"weightchart.php\">Generate chart</a>\n");
  echo("<BR>\n");

  echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
  
  // only retrieve non-"retired" animals (ie, onschedule=0 or 1)
  if ("active"==$sactive){
    $sql="SELECT * FROM gAnimal".
      " WHERE species like \"$queryspecies\"".
      " AND onschedule<2 AND lab='$LAB' and not(animal like 'test')" .
      " ORDER BY animal;";
  } elseif ("mine"==$sactive){
    $sql="SELECT * FROM gAnimal".
      " WHERE species like \"$queryspecies\"".
      " AND caretaker like '%$userid%' and not(animal like 'test')" .
      " ORDER BY animal;";
  } else {
    $sql="SELECT * FROM gAnimal".
      " WHERE species like \"$queryspecies\"".
      " AND not(animal like 'test')" .
      " ORDER BY animal;";
  }
  $adata = mysql_query($sql);
  
  echo("<table>\n");
  $bgtxt="bgcolor=#F0F0F0";
  echo("<tr>\n");
  echo("  <td $bgtxt><b>Animal&nbsp;</b></td>\n");
  for ($cdow=0; $cdow<7; $cdow++) {
    $tstamp=mktime(0,0,0,$mm,$dd-$dow+$cdow,$yy);
    $tdate=date("D (m-d)",$tstamp);
    $tpendate=date("Y-m-d",$tstamp);
    if (round($cdow/2)!=$cdow/2){
      $bgtxt="bgcolor=#F0F0F0";
    } else {
      $bgtxt="bgcolor=#DDDDDD";
    }
    
    echo("  <td colspan=2 align=center $bgtxt><a href=\"weights.php?pendate=$tpendate&openforedit=1\">$tdate&nbsp;</a></td>\n");
  }
  echo("</tr>\n");

  while ( $row = mysql_fetch_array($adata) ) {
    echo("<tr>");
    $canimal=$row["animal"];
    $canimal_id=$row["id"];
    $cspecies=$row["species"];
    if ("rat"==$cspecies || "mouse"==$cspecies) {
        $roundto="1";
    } else {
        $roundto="0";
    }
    $sql="SELECT round(weight,$roundto) as weight, round(water,1) as water, date, ".
      " schedule " .
      " FROM gHealth".
      " WHERE animal_id=$canimal_id AND date>='$firstdate'".
      " AND date<='$lastdate' ORDER BY date";
    $wdata = mysql_query($sql);
    $bgtxt="bgcolor=#F0F0F0";
    echo("  <td $bgtxt><a href=\"animals.php?animal=$canimal\">$canimal</a>&nbsp;</td>\n");
    $usedlast=1;
    for ($cdow=0; $cdow<7; $cdow++) {
      if (round($cdow/2)!=$cdow/2){
        $bgtxt="bgcolor=#F0F0F0";
      } else {
        $bgtxt="bgcolor=#DDDDDD";
      }
      if (1==$usedlast) {
        $crow = mysql_fetch_array($wdata);
        $usedlast=0;
      }
      if ($crow && date("w",strtotime($crow["date"]))==$cdow) {
        if ($crow["weight"] < $row["pullweight"]) {
          $wcol="#FF0000";
        } else {
          $wcol="#000088";
        }
        if (0==$crow["schedule"]){
          $bgtxt="bgcolor=#F8F800";
        }
        if (0==$crow["weight"]) {
          $sweight="";
        } else {
          $sweight=$crow["weight"];
        }
        if (0==$crow["water"]) {
          $swater="";
        } else {
          $swater=$crow["water"];
        }
        echo("<td $bgtxt><font color=\"$wcol\">$sweight</font>&nbsp;</td>");
        echo("<td $bgtxt>$swater&nbsp;</td>");
        $usedlast=1;
      } else {
        echo("<td $bgtxt></td><td $bgtxt></td>");
      }
    }
    echo("<td><a href=\"celldbstats.php?userid=$userid" .
         "&sessionid=$sessionid&animal=" . $canimal .
         "&timeframe=$timeframe&statcode=weight\">->graph</a></td>");
    echo("<td><a href=\"weightdump.php?animal=" . $canimal .
         "\">->dump</a></td>");
    echo("</tr>\n");
  }
  echo("<tr>\n");
  echo("  <td></td>\n");
  for ($cdow=0; $cdow<7; $cdow++) {
    $tstamp=mktime(0,0,0,date("m",$firststamp),date("d",$firststamp)+$cdow,
                   date("y",$firststamp));
    $tdate=date("Y-m-d",$tstamp);
    
    if (round($cdow/2)!=$cdow/2){
      $bgtxt="bgcolor=#F0F0F0";
    } else {
      $bgtxt="bgcolor=#DDDDDD";
    }
    echo("  <td colspan=2 align=center $bgtxt><a href=\"weights.php?pendate=$tdate&openforedit=1\">Edit</a></td>\n");
  }
  echo("</tr>\n");
  echo("</table>\n");

 } else {
  echo("<b>Weight data for $pendate</b><br>\n");
  echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
  
  // only retrieve non-"retired" animals (ie, onschedule=0 or 1)
  if ("active"==$sactive){
    $afilt=" AND lab='$LAB'";
  } elseif ("mine"==$sactive){
    $afilt=" AND caretaker like '%$userid%'";
  } else {
    $afilt="";
  }
  
  $sql="SELECT gAnimal.animal as name,gAnimal.onschedule,".
    " gAnimal.id as animal_id, date, round(water,1) as water, round(weight,0) as weight, trained, schedule, timeonoroff, wetfood, gHealth.notes".
    " FROM gAnimal LEFT JOIN gHealth".
    " ON gAnimal.id=gHealth.animal_id AND date='$pendate'" .
    " WHERE gAnimal.onschedule in (0,1) $afilt".
    " AND species like \"$queryspecies\"".
    " AND not(gAnimal.animal like 'test')".
    " GROUP BY gAnimal.animal,gAnimal.onschedule,date".
    " ORDER BY gAnimal.animal;";
  $wdata = mysql_query($sql);
  
  if (!$wdata) {
    echo("<p><b><font color=\"#CC0000\">".mysql_error()."</font></b></p>\n");
    $errormsg=mysql_error();
  }
  
  echo("<FORM ACTION=\"weights.php\" METHOD=POST>\n");
  echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
  echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
  echo(" <input type=\"hidden\" name=\"action\" value=\"1\">\n");
  echo(" <input type=\"hidden\" name=\"pendate\" value=\"$pendate\">\n");
  echo("<table>\n");
  
  echo("<tr>\n");
  echo("  <td><b>Name</b></td>\n");
  echo("  <td><b>Weight(g)&nbsp;</b></td>\n");
  echo("  <td><b>Water(ml)&nbsp;</b></td>\n");
  echo("  <td><b>Trained&nbsp;</b></td>\n");
  echo("  <td><b>Sched?&nbsp;</b></td>\n");
  echo("  <td><b>Time</b></td>\n");
  echo("  <td><b>Wet food&nbsp;</b></td>\n");
  echo("  <td><b>Notes</b></td>\n");
  echo("</tr>\n");
  
  $acount=0;
  while ( $row = mysql_fetch_array($wdata) ) {
    $acount++;
    
    echo("<tr>\n");
    echo("  <td>" . $row["name"] . "</td>\n");
    echo("<input type=\"hidden\" name=\"animal[$acount]\" value=\"" . 
         $row["name"] ."\">\n");
    echo("<input type=\"hidden\" name=\"animal_id[$acount]\" value=" . 
         $row["animal_id"] .">\n");
    echo("<input type=\"hidden\" name=\"weight_old[$acount]\" value=" . 
         $row["weight"] .">\n");
    echo("<input type=\"hidden\" name=\"water_old[$acount]\" value=" . 
         $row["water"] .">\n");
    echo("<input type=\"hidden\" name=\"trained_old[$acount]\" value=" . 
         $row["trained"] .">\n");
    echo("<input type=\"hidden\" name=\"onschedule_old[$acount]\" value=" . 
         $row["onschedule"] .">\n");
    
    if (0==$row["weight"]) {
      $s="";
    } else {
      $s=$row["weight"];
    }
    echo("  <td><INPUT TYPE=TEXT SIZE=6 NAME=\"weight[$acount]\" value=\"" . 
         "$s\"></td>\n");
    if (0==$row["water"]) {
      $s="";
    } else {
      $s=$row["water"];
    }
    echo("   <td><INPUT TYPE=TEXT SIZE=6 NAME=\"water[$acount]\" value=\"" . 
         "$s\"></td>\n");
    
    
    if (0==$row["trained"]) {
      echo("  <td align=center><INPUT TYPE=checkbox name=\"trained[$acount]\" value=\"1\"></td>\n");
    } else {
      echo("  <td align=center><INPUT TYPE=checkbox name=\"trained[$acount]\" value=\"1\" checked></td>\n");
    }

    if (0==$row["schedule"] && 0==$row["onschedule"]) {
      echo("  <td align=center><INPUT TYPE=checkbox name=\"onschedule[$acount]\" value=\"1\"></td>\n");
    } else {
      echo("  <td align=center><INPUT TYPE=checkbox name=\"onschedule[$acount]\" value=\"1\" checked></td>\n");
    }
    
    if ("00:00:00"==$row["timeonoroff"]) {
      $s="";
    } else {
      $s=$row["timeonoroff"];
      $s=substr($s,0,5);
    }
    echo("   <td><INPUT TYPE=TEXT SIZE=6 NAME=\"timeonoroff[$acount]\" value=\"" . 
         "" . $s . "\"></td>\n");
    
    if (0==$row["wetfood"]) {
      echo("  <td align=center><INPUT TYPE=checkbox name=\"wetfood[$acount]\" value=\"1\">");
    } else {
      echo("  <td align=center><INPUT TYPE=checkbox name=\"wetfood[$acount]\" value=\"1\" checked>");
    }
    echo("</td>\n");
    
    echo("   <td><INPUT TYPE=TEXT SIZE=40 NAME=\"notes[$acount]\" value=\"" . 
         "" . $row["notes"] . "\"></td>\n");
    
    echo("</tr>\n");
  }
  echo("<tr><td></td><td colspan=4><INPUT TYPE=SUBMIT VALUE=\"Change\">\n");
  echo("<a href=\"weights.php?pendate=$pendate&openforedit=0\">Cancel</a></td></tr>\n");
  echo("</table>\n");
  
  echo(" <input type=\"hidden\" name=\"acount\" value=\"$acount\">\n");
  echo("</form>\n");
 }


cellfooter();

?>
