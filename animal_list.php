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
<TITLE><?php echo("$siteinfo"); ?> - Animal List</TITLE>
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

echo("<p>");
echo("Species: ");
echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\">\n");
echo(" <option  value=\"animal_list.php?&queryspecies=%\"$sel>all</option>\n");
for ($ii=0; $ii<count($SpeciesStatusList); $ii++) {
  if ($queryspecies.",".$sactive == $SpeciesStatusList[$ii]) {
    $sel=" selected";
  } else {
    $sel="";
  }
  echo(" <option  value=\"animal_list.php?&queryspecies=" . $SpeciesStatusList[$ii] .
       "\"$sel>" . $SpeciesStatusList[$ii] . "</option>\n");
 }
echo("</select>&nbsp;&nbsp;");

//echo("Caretaker: ");
//echo
//echo("  <td><INPUT TYPE=TEXT SIZE=20 NAME=\"caretaker\" value=\"$caretaker\" OnChange=\"location.href=this.options[this.selectedIndex].value></td>\n");


if ("active"==$sactive){
  $animaldata = mysql_query("SELECT * FROM gAnimal" .
                            " WHERE species like \"$queryspecies\"".
                            " AND onschedule<2".
                            " AND caretaker like \"%$caretaker%\"".
                            " ORDER BY animal");
 } else {
  $animaldata = mysql_query("SELECT * FROM gAnimal" .
                            " WHERE species like \"$queryspecies\"".
                            " AND caretaker like \"%$caretaker%\"".
                            " ORDER BY animal");
 }
echo("</p>\n");

echo("<table>");
echo("<tr>\n");
echo("<td><b>Animal</b></td>");
echo("<td><b>Eartag</b></td>");
echo("<td><b>Tattoo</b></td>");
echo("<td><b>Caretaker(s)</b></td>");
echo("<td><b>Current task</b></td>");
echo("<td><b>Last weight</b></td>");
echo("<td><b>75% pull weight</b></td>");
echo("</tr>\n");

while ($row=mysql_fetch_array($animaldata)) {
  $id=$row["id"];
  $sql="SELECT * FROM gHealth WHERE animal_id=$id and weight>0 ORDER BY date DESC LIMIT 1;";
  $wdata=mysql_query($sql);
  if ($wrow=mysql_fetch_array($wdata)){
    $lastweight=$wrow["weight"];
  } else {
    $lastweight="";
  }

  echo("<tr>\n");
  echo("<td><a href=\"animals.php?animal=" . $row["animal"] ."&openforedit=1\">" . $row["animal"]."</a></td>");
  echo("<td>" . $row["eartag"]."</td>");
  echo("<td>" . $row["tattoo"]."</td>");
  echo("<td>" . $row["caretaker"]."</td>");
  echo("<td>" . $row["current_task"]."</td>");
  echo("<td align=\"center\">" . round($lastweight) ."</td>");
  echo("<td align=\"center\">" . round($row["pullweight"]*0.75/0.8) ."</td>");
  
  echo("</tr>\n");
 }

return;

  
  echo("<p><a href=\"animals.php?animal=$animal&openforedit=1\">Edit this animal</a></p>\n");
  
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
  echo("  <td>$pullweight (+ pole: $poleweight) $weightunits</td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("  <td><b>Caretaker:</b></td>\n");
  echo("  <td>$caretaker</td>\n");
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
  


cellfooter();

?>
