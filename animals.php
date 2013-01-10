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

if (""==$id) {
  $id=-1;
}

if (""==$action) {
  $action=-2;
}
$errormsg="";

if (1==$action && (""==$animal || ""==$cellprefix)) {
  $errormsg="ERROR: You must enter an animal name and prefix.";
  $action=-1;
  $openforedit=1;
  $animal="NEW";
}
if (1==$action && -1==$id) {
  $sql="SELECT * FROM gAnimal WHERE animal=\"$animal\"";
  $adata=mysql_query($sql);
  if (mysql_num_rows($adata)>0) {
    $errormsg="ERROR: Animal already exists with the requested name.";
    $action=-1;
    $openforedit=1;
    $animal="NEW";
  }
}
if (1==$action && -1==$id) {
  $sql="SELECT * FROM gAnimal WHERE cellprefix=\"$cellprefix\"";
  $adata=mysql_query($sql);
  if (mysql_num_rows($adata)>0) {
    $errormsg="ERROR: Animal already exists with the requested prefix.";
    $action=-1;
    $openforedit=1;
    $animal="NEW";
  }
}

// ok, checks done. perform actions
if (1==$action) {
  
  // get data posted by user
  $formdata=$_REQUEST;
  $formdata["eartag"]=tidystr($formdata["eartag"]);
  $formdata["tattoo"]=tidystr($formdata["tattoo"]);
  $formdata["caretaker"]=tidystr($formdata["caretaker"]);
  $formdata["arrivalweight"]=1.0 * $formdata["arrivalweight"];
  $formdata["pullweight"]=1.0 * $formdata["pullweight"];
  $formdata["poleweight"]=1.0 * $formdata["poleweight"];
  $formdata["medical"]=tidystr($formdata["medical"]);
  $formdata["notes"]=tidystr($formdata["notes"]);
  $formdata["birthday"]=tidystr($formdata["abirthday"]);
  $formdata["lab"]=tidystr($formdata["lab"]);
  
  // actually save/update the data
  $errormsg=savedata("gAnimal",$id,$formdata);
  
  if (is_numeric($errormsg)) {
    $rawid=$errormsg;
    $errormsg="";
    $result=1;
  }

 } elseif (2==$action) {
   // delete
   $sql="DELETE FROM gAnimal WHERE id=$id";
   $result=mysql_query($sql);
 }

if ($action>=0 && !$result) {
  $errormsg=mysql_error() . ": " . $sql;
  $openforedit=1;
  $action=-1;
}


?>

<HTML>
<HEAD>
<TITLE><?php echo("$siteinfo"); ?> - Animals</TITLE>
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

if (!isset($queryspecies) || ""==$queryspecies){
  $queryspecies="%";
}

mysql_query("UPDATE gUserPrefs SET lastanimal=\"$animal\",".
            " lastspecies=\"$queryspecies\" WHERE userid=\"$userid\"");

$queryspecies=explode(",",$queryspecies);
if (count($queryspecies)>1) {
  $sactive=$queryspecies[1];
} else {
  $sactive="active";
}
$queryspecies=$queryspecies[0];


if (-1==$action) {
  // do nothing. preserve old field values
  
 } else {
  
  $sql="SELECT * FROM gAnimal WHERE animal='$animal'";
  $animaldata = mysql_query($sql);
  
  if ($row = mysql_fetch_array($animaldata)) {
    $id=$row["id"];
    $species=$row["species"];
    $animal=$row["animal"];
    $eartag=$row["eartag"];
    $tattoo=$row["tattoo"];
    $cellprefix=$row["cellprefix"];
    $abirthday=$row["birthday"];
    $caretaker=$row["caretaker"];
    $arrivalweight=$row["arrivalweight"];
    $pullweight=$row["pullweight"];
    $poleweight=$row["poleweight"];
    $medical=$row["medical"];
    $notes=$row["notes"];
    $onschedule=$row["onschedule"];
    $implanted=$row["implanted"];
    $photourl=$row["photourl"];
    $current_task=$row["current_task"];
    $lab=$row["lab"];
  } else {
    $lab=$LAB;
  }
 }

if (0==$openforedit || ""==$openforedit) {
  // just display, don't edit
  
  echo("<p>");
  echo("Species: ");
  echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\">\n");
  echo(" <option  value=\"animals.php?&queryspecies=%\"$sel>all</option>\n");
  for ($ii=0; $ii<count($SpeciesStatusList); $ii++) {
    if ($queryspecies.",".$sactive == $SpeciesStatusList[$ii]) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option  value=\"animals.php?&queryspecies=" . $SpeciesStatusList[$ii] .
         "\"$sel>" . $SpeciesStatusList[$ii] . "</option>\n");
  }
  echo("</select>&nbsp;&nbsp;");
  if ("active"==$sactive){
    $animallistdata = mysql_query("SELECT animal FROM gAnimal" .
                                  " WHERE species like \"$queryspecies\"".
                                  " AND onschedule<2 AND lab='$LAB'".
                                  " ORDER BY animal");
  } else {
    $animallistdata = mysql_query("SELECT animal FROM gAnimal" .
                                  " WHERE species like \"$queryspecies\"".
                                  " ORDER BY animal");
  }
  echo("Animal: ");
  echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\">\n");
  while ( $row = mysql_fetch_array($animallistdata) ) {
    if ($animal == $row["animal"]) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option  value=\"animals.php?queryspecies=$queryspecies" .
         "," . $sactive . "&animal=" .$row["animal"] . "\"$sel>" . 
         $row["animal"] . "</option>\n");
  }
  echo(" <option value=\"animals.php?queryspecies=$queryspecies" .
       "&animal=NEW&openforedit=1\">NEW</option>\n");
  echo("</select>");
  
  echo(" <a href=\"animal_list.php?queryspecies=$queryspecies\">Jump to list</a>\n");
       

  echo("</p>\n");
  
  echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
  
  echo("<p><a href=\"animals.php?animal=$animal&openforedit=1\">Edit this animal</a> - \n");
  echo("<a href=\"drugcalc.php?animal=$animal\">Dosages for this animal</a></p>\n");
  
  echo("<table>");
  
  echo("<tr>\n");
  echo("  <td><b>Name:</b></td>\n");
  echo("  <td>$animal</td>\n");
  if (""!=$photourl) {
    echo("  <td rowspan=10><img border=0 width=200 src=\"$photourl\"></td>\n");
  }
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Species:</b></td>\n");
  echo("  <td>$species</td>\n");
  echo("</tr>\n");
   echo("<tr>\n");
  echo("  <td><b>Lab:</b></td>\n");
  echo("  <td>$lab</td>\n");
  echo("</tr>\n");
 echo("<tr>\n");
  echo("  <td><b>Ear tag:</b></td>\n");
  echo("  <td>$eartag\n");
  echo("</tr>\n");
  echo("  <td><b>Tattoo:</b></td>\n");
  echo("  <td>$tattoo\n");
  echo("</tr>\n");
  echo("  <td><b>Birthday:</b></td>\n");
  echo("  <td>$abirthday</td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Cell prefix:</b></td>\n");
  echo("  <td>$cellprefix</td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Arrival weight:</b></td>\n");
  echo("  <td>$arrivalweight $weightunits</td>\n");
  echo("<tr>\n");
  echo("  <td><b>Pull weight:</b></td>\n");
  echo("  <td>$pullweight $weightunits (80%) - " . round($pullweight*0.75/0.8) . " $weightunits (75%)</td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Caretaker:</b></td>\n");
  echo("  <td>$caretaker</td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Current task:</b></td>\n");
  echo("  <td>$current_task</td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Schedule:</b></td>\n");
  echo("  <td>");
  if (0==$onschedule) {
    echo("No");
  } elseif (1==$onschedule) {
    echo("Yes");
  } elseif (2==$onschedule) {
    echo("Retired");
  }
  echo("</td></tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Implanted:</b></td>\n");
  echo("  <td>");
  if (0==$implanted) {
    echo("No");
  } elseif (1==$implanted) {
    echo("Yes");
  } elseif (2==$implanted) {
    echo("Fell off");
  }
  echo("</td></tr>\n");
  echo("<tr>\n");
  echo("  <td valign=top><b>Medical history:</b></td>\n");
  echo("  <td colspan=2>" . stringfilt($medical) . "</td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td valign=top><b>Training notes:</b></td>\n");
  echo("  <td colspan=2>" . stringfilt($notes) . "</td>\n");
  echo("</tr>\n");
  
  $sql="SELECT * FROM gHealth WHERE animal_id=$id ORDER BY date DESC LIMIT 1;";
  $wdata=mysql_query($sql);
  if ($row=mysql_fetch_array($wdata)) {
    
    echo("<tr>\n");
    echo("  <td valign=top><b>Latest weight:</b></td>\n");
    echo("  <td>" . round($row["weight"],0) . " $weightunits</td>\n");
    echo("</tr>\n");
  }
  echo("</table>\n"); 
  
} else {
  echo("<FORM ACTION=\"animals.php\" METHOD=POST>\n");
  echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
  echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
  echo(" <input type=\"hidden\" name=\"id\" value=\"$id\">\n");
  echo(" <input type=\"hidden\" name=\"action\" value=\"1\">\n");
  
  echo("<table>");
  
  echo("<tr>\n");
  echo("  <td><b>Name:</b></td>\n");
  echo("  <td><INPUT TYPE=TEXT SIZE=15 NAME=\"animal\" value=\"$animal\"></td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Lab:</b></td>\n");
  echo("  <td><INPUT TYPE=TEXT SIZE=15 NAME=\"lab\" value=\"$lab\"></td>\n");
  echo("</tr>\n");
    
  echo("<tr>\n");
  echo("  <td><b>Species:</b></td>\n");
  echo("  <td><select name=\"species\">\n");
  for ($ii=0; $ii<count($SpeciesList); $ii++) {
    if ($species == $SpeciesList[$ii]) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option value=\"" . $SpeciesList[$ii] . "\"$sel>" . 
         $SpeciesList[$ii] . "</option>\n");
  }
  echo("</select></td>\n");
  echo("</tr>\n");
  
  echo("<tr>\n");
  echo("  <td><b>Ear tag:</b></td>\n");
  echo("  <td><INPUT TYPE=TEXT SIZE=15 NAME=\"eartag\" value=\"$eartag\"></td>\n");
  echo("<tr>\n");
  echo("  <td><b>Tattoo:</b></td>\n");
  echo("  <td><INPUT TYPE=TEXT SIZE=15 NAME=\"tattoo\" value=\"$tattoo\"></td>\n");
  echo("</tr>\n");
  echo("  <td><b>Birthday:</b></td>\n");
  echo("  <td><INPUT TYPE=TEXT SIZE=15 NAME=\"abirthday\" value=\"$abirthday\">(YYYY-MM-DD)</td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Cell prefix:</b></td>\n");
  echo("  <td><INPUT TYPE=TEXT SIZE=5 NAME=\"cellprefix\" value=\"$cellprefix\"></td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Arrival weight:</b></td>\n");
  echo("  <td><INPUT TYPE=TEXT SIZE=5 NAME=\"arrivalweight\" value=\"$arrivalweight\"> $weightunits</td>\n");
  echo("<tr>\n");
  echo("  <td><b>Pull weight (80%):</b></td>\n");
  echo("  <td><INPUT TYPE=TEXT SIZE=5 NAME=\"pullweight\" value=\"$pullweight\">");
  echo(" + pole: <INPUT TYPE=TEXT SIZE=5 NAME=\"poleweight\" value=\"$poleweight\"> $weightunits");
  echo("</td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Caretaker:</b></td>\n");
  echo("  <td><INPUT TYPE=TEXT SIZE=15 NAME=\"caretaker\" value=\"$caretaker\"></td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Current task:</b></td>\n");
  echo("  <td><INPUT TYPE=TEXT SIZE=15 NAME=\"current_task\" value=\"$current_task\"></td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Schedule:</b></td>\n");
  echo("  <td><select name=\"onschedule\" size=\"1\">");
  if (0==$row["onschedule"]) {
    echo(" <option  value=\"0\" selected>No</option>");
    echo(" <option  value=\"1\">Yes</option>");
    echo(" <option  value=\"2\">Retired</option>");
  } elseif (1==$row["onschedule"]) {
    echo(" <option  value=\"0\">No</option>");
    echo(" <option  value=\"1\" selected>Yes</option>");
    echo(" <option  value=\"2\">Retired</option>");
  } elseif (2==$row["onschedule"]) {
    echo(" <option  value=\"0\">No</option>");
    echo(" <option  value=\"1\">Yes</option>");
    echo(" <option  value=\"2\" selected>Retired</option>");
  }
  echo("</td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Implanted:</b></td>\n");
  echo("  <td><select name=\"implanted\" size=\"1\">");
  if (0==$row["implanted"]) {
    echo(" <option  value=\"0\" selected>No</option>");
    echo(" <option  value=\"1\">Yes</option>");
    echo(" <option  value=\"2\">Fell off</option>");
  } elseif (1==$row["implanted"]) {
    echo(" <option  value=\"0\">No</option>");
    echo(" <option  value=\"1\" selected>Yes</option>");
    echo(" <option  value=\"2\">Fell off</option>");
  } elseif (2==$row["implanted"]) {
    echo(" <option  value=\"0\">No</option>");
    echo(" <option  value=\"1\">Yes</option>");
    echo(" <option  value=\"2\" selected>Fell off</option>");
  }
  echo("</td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Photo URL:</b></td>\n");
  echo("  <td><INPUT TYPE=TEXT SIZE=60 NAME=\"photourl\" value=\"$photourl\"></td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td valign=top><b>Medical history:</b></td>\n");
  echo("  <td><textarea NAME=\"medical\" rows=5 cols=60>$medical</textarea></td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td valign=top><b>Training notes:</b></td>\n");
  echo("  <td><textarea NAME=\"notes\" rows=10 cols=60>$notes</textarea></td>\n");
  echo("</tr>\n");
  
  echo("<tr>\n");
  echo("  <td></td>\n");
  echo("  <td><INPUT TYPE=SUBMIT VALUE=\"Change\"> <a href=\"animals.php?animal=$animal&openforedit=0\">Cancel</a></td>\n");
  echo("</tr>\n");
  
  echo("</table>\n"); 
  echo("</FORM>\n");
 }


cellfooter();

?>
