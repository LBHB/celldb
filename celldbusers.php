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
       1. change password, colors from input text boxes
    */
    
    if (1==$action) {

      $sql="UPDATE gUserPrefs SET ";
      if ($newseclevel>0) {
        $sql=$sql."seclevel=$newseclevel,";
      }
      if ($newpasswd!="") {
        $sql=$sql."password='". md5($newpasswd) ."',";
      }
      
      $sql=$sql.
        "lab=\"$newlab\",".
        "bgcolor=\"$bgcolor\",".
        "fgcolor=\"$fgcolor\",".
        "linkfg=\"$newlinkfg\",".
        "vlinkfg=\"$newvlinkfg\",".
        "alinkfg=\"$newalinkfg\"".
        " WHERE id=$target";
      //echo("$sql");exit;
      mysql_query($sql);
    }
  }
  $redirurl="celldbusers.php?edituserid=$edituserid";
  header ("Location: $redirurl");
  exit;                 /* Make sure that code below does not execute */

}

?>

<HTML>
<HEAD>
<TITLE>Celldb users</TITLE>
<meta NAME="description" CONTENT="Celldb users">
</HEAD>
<?php
echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

cellheader();

echo("<table><tr><td>\n");

$acturl="celldbusers.php?orderby=$orderby&activeusers=";

echo("&nbsp;Show inactive: \n");
if (""==$activeusers) {
  $activeusers=1;
}
if (0==$activeusers) {
  $b1yes="<b>";
  $b2yes="</b>";
  $b1no="";
  $b2no="";
  $actstring=" GROUP BY gUserPrefs.userid";
} else {
  $b1yes="";
  $b2yes="";
  $b1no="<b>";
  $b2no="</b>";
  $actstring=" WHERE gUserPrefs.lab='$LAB' GROUP BY gUserPrefs.userid";
}

echo("$b1yes<a href=\"" . $acturl . "0\">Yes</a>$b2yes\n");
echo(" / $b1no<a href=\"" . $acturl . "1\">No</a>$b2no\n");
echo("&nbsp;&nbsp;<a href=\"" . $acturl . $activeusers ."\">Refresh</a>");

echo("</td></tr></table>\n");

echo("<table>");

if ($edituserid!="") {
  $sql="SELECT * FROM gUserPrefs WHERE userid='$edituserid'";
  $thisuserdata=mysql_query($sql);

  echo("<tr>\n");
  echo("<td colspan=8 bgcolor=\"#bbbbff\"><b>$edituserid settings:</b></td>");
  echo("</tr>\n");
  
  while ( $urow = mysql_fetch_array($thisuserdata) ) {
    
    echo("<tr>\n");
    echo("<td colspan=5>\n");
    echo(" <FORM ACTION=\"celldbusers.php\" METHOD=\"POST\">\n");
    echo(" <input type=\"hidden\" name=\"edituserid\" value=\"$edituserid\">\n");
    echo(" <input type=\"hidden\" name=\"orderby\" value=\"$orderby\">\n");
    echo(" <input type=\"hidden\" name=\"action\" value=\"1\">\n");
    echo(" <input type=\"hidden\" name=\"target\" value=\"" . $urow["id"] . "\">\n");
    echo(" <input type=\"hidden\" name=\"activeusers\" value=\"$activeusers\">\n");
    echo("<table>\n");
    
    if ($userid==$edituserid || $seclevel>=10){
      echo("<tr>");
      echo("<td>password&nbsp;</td>");
      echo("<td><input type=password size=9 name=\"newpasswd\" value=\"\"></td>");
      echo("</tr>\n");
    }
    
    if ($seclevel>=10){
      echo("<tr>");
      echo("<td>seclevel</td>");
      echo("<td><input type=text size=9 name=\"newseclevel\" value=\"" . $urow["seclevel"] . "\"></td>");
      echo("</tr>\n");
    }

    echo("<tr>");
    echo("<td>bg</td>");
    echo("<td><input type=text size=9 name=\"newlab\" value=\"" . $urow["lab"] . "\"></td>");
    echo("</tr>\n");
    echo("<tr>");
    echo("<tr>");
    echo("<td>bg</td>");
    echo("<td><input type=text size=9 name=\"bgcolor\" value=\"" . $urow["bgcolor"] . "\"></td>");
    echo("</tr>\n");
    echo("<tr>");
    echo("<td>fg</td>");
    echo("<td><input type=text size=9 name=\"fgcolor\" value=\"" . $urow["fgcolor"] . "\"></td>");
    echo("</tr>\n");
    echo("<tr>");
    echo("<td>link</td>");
    echo("<td><input type=text size=9 name=\"newlinkfg\" value=\"" . $urow["linkfg"] . "\"></td>");
    echo("</tr>\n");
    echo("<tr>");
    echo("<td>vlink</td>");
    echo("<td><input type=text size=9 name=\"newvlinkfg\" value=\"" . $urow["vlinkfg"] . "\"></td>");
    echo("</tr>\n");
    echo("<tr>");
    echo("<td>alink</td>");
    echo("<td><input type=text size=9 name=\"newalinkfg\" value=\"" . $urow["alinkfg"] . "\"></td>");
    echo("</tr>\n");

    echo("</table>");
    
    echo("<INPUT TYPE=SUBMIT VALUE=\"Change\">");
    
    echo(" </form>\n");
    echo("</td>\n</tr>\n");
  }
}


// query celldb for queue entries matching search criteria
$sql="SELECT gUserPrefs.*,count(gPenetration.id) as pencount".
  " FROM gUserPrefs LEFT JOIN gPenetration ON gUserPrefs.userid=gPenetration.addedby".
  $actstring.
  " ORDER BY $orderby";
//echo("sql: $sql<br>\n");
$userdata=mysql_query($sql);

if (!$userdata) {
  echo("<P>Error performing query: " . mysql_error() . "</P>");
  exit();
}

// list each entry
$sorturl="celldbusers.php?activeusers=$activeusers&orderby=";


echo("<tr bgcolor=\"#bbbbff\">\n");
echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "gUserPrefs.id\">id</a></b><br></td>\n");
echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "gUserPrefs.userid\">name</a></b><br></td>\n");
echo("  <td><b>&nbsp;<a href=\"" . $sorturl . "gUserPrefs.lab\">lab</a></b><br></td>\n");
echo("</tr>\n");

while ( $row = mysql_fetch_array($userdata) ) {
  
  echo("<tr>\n");
  echo("   <td align=\"right\">" . $row["id"] . "&nbsp;</td>\n");
  
  $joburl="<a href=\"queuemonitor.php?userid=$userid&sessionid=$sessionid" .
    "&user=" . $row["userid"] . "&complete=-2\">";
  echo("   <td>$joburl" . $row["userid"] . "</a>&nbsp;</td>\n");
  echo("   <td>" . $row["lab"] . "&nbsp;</td>\n");
  
  $editurl="celldbusers.php?orderby=$orderby&activeusers=$activeusers&edituserid=";
  echo("   <td><a href=\"$editurl" . $row["userid"] . "\">edit</a>&nbsp;</td>\n");
  

  echo("</tr>\n");
}

echo("</table>\n");

cellfooter();
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

</BODY>
</HTML>
