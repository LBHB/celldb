<?php
/*** CELLDB
calendar.php - events/chores in lab
created 3/7/2007 - SVD
***/

// global include: connect to db and get important basic info about user prefs
$min_sec_level=2;
include_once "./celldb.php";

// action -1 - action failed
//         0 - add entry
//         1 - edit entry
//         2 - delete entry

if (""==$action) {
  $action=-2;
 }
$errormsg="";
//$action=-2;

// ok, checks done. perform actions
if (1==$action) {
  
  if ($calid>0) {
    
    // update existing gHealth entry
    $sql="UPDATE gCalendar SET".
      " caldate=\"" . $caldate . "\"," .
      " userid=\"" . $caluser . "\"," .
      " calname=\"" . $calname . "\"" .
      " WHERE id=$calid";
      //echo("$sql<BR>");
    $result=mysql_query($sql);
  } else {
    
    // insert new penetration only if values entered
    $sql="INSERT INTO gCalendar".
      " (calname,userid,caldate,dateadded,addedby)".
      " VALUES (\"$calname\",\"$caluser\",\"$caldate\"," . 
      "now(),'$userid')";
    //echo("$sql<BR>");
    $result=mysql_query($sql);
      
  }
  //redirect to avoid double-posting
  header ("Location: calendar.php");
  exit;                 /* Make sure that code below does not execute */

}

?>

<HTML>
<HEAD>
<TITLE><?php echo("$siteinfo"); ?> - Calendar - <?php echo("Event/Date"); ?></TITLE>
  <link rel="shortcut icon" href="favicon.ico" />
<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="./include/winter.css" />

<!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="./include/zapatec.js"></script>
    <script type="text/javascript" src="./include/calendar.js"></script>
<!-- Loading language definition file -->
    <script type="text/javascript" src="./include/calendar-en.js"></script>
</HEAD>

<?php


echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

// header
cellheader();

if (""!=$errormsg) {
  echo("<p><b><font color=\"#CC0000\">$errormsg</font></b></p>\n");
}

if (""==$caluser) {
  $caluser="%";
}
if (""==$calname) {
  $calname="%";
}
if (""==$startdate) {
  $startdate=date("Y-m-d");
}
if (""==$stopdate) {
  $stopdate=date("Y-m-d",strtotime("+1 year"));
}

if (""==$calid) {
  echo("<p><b>NSL doo-ty calendar for $calname / user $caluser</b></p>\n");
  
  $sql="SELECT DISTINCT calname FROM gCalendar ORDER BY calname";
  $caldata=mysql_query($sql);
  echo("<FORM ACTION=\"calendar.php\" METHOD=GET>\n");
  echo(" <input type=\"hidden\" name=\"calname\" value=\"$calname\">\n");
  echo(" <input type=\"hidden\" name=\"caluser\" value=\"$caluser\">\n");

  echo("Event: ");
  echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\">\n");
  echo(" <option value=\"calendar.php?calname=%&caluser=$caluser&startdate=$startdate&stopdate=$stopdate\">all</option>\n");
  while ($row=mysql_fetch_array($caldata)) {
    if ($calname == $row["calname"]) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option value=\"calendar.php?calname=" . $row["calname"] . "&caluser=$caluser&startdate=$startdate&stopdate=$stopdate\". $sel>" . $row["calname"] . "</option>\n");
  }
  echo("</select>&nbsp;\n");

  $sql="SELECT DISTINCT userid as caluser FROM gUserPrefs WHERE lab=\"nsl\" ORDER BY caluser";
  $caldata=mysql_query($sql);
  echo("User: ");
  echo("<select OnChange=\"location.href=this.options[this.selectedIndex].value\">\n");
  echo(" <option value=\"calendar.php?calname=$calname&caluser=%&startdate=$startdate&stopdate=$stopdate\">all</option>\n");
  while ($row=mysql_fetch_array($caldata)) {
    if ($caluser == $row["caluser"]) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option value=\"calendar.php?calname=$calname&caluser=" . $row["caluser"] . "&startdate=$startdate&stopdate=$stopdate\". $sel>" . $row["caluser"] . "</option>\n");
  }
  echo("</select>\n");
  
  // jscript for calendar hacked from freebie at zapotec.com
  echo("Start: <input type=\"text\" id=\"startdate\" name=\"startdate\" size=9 value=\"$startdate\" />\n");
?>
    <button id="buttstartdate">...</button>
    <script type="text/javascript">//<![CDATA[
      Zapatec.Calendar.setup({
        electric          : false,
        inputField        : "startdate",
        button            : "buttstartdate",
        ifFormat          : "%Y-%m-%d",
        daFormat          : "%Y/%m/%d"
      });
    //]]></script>
<?php
  // jscript for calendar hacked from freebie at zapotec.com
  echo("&nbsp;Stop: <input type=\"text\" id=\"stopdate\" name=\"stopdate\" size=9 value=\"$stopdate\" />\n");
?>
    <button id="buttstopdate">...</button>
    <script type="text/javascript">//<![CDATA[
      Zapatec.Calendar.setup({
        electric          : false,
        inputField        : "stopdate",
        button            : "buttstopdate",
        ifFormat          : "%Y-%m-%d",
        daFormat          : "%Y/%m/%d"
      });
    //]]></script>
<?php
    
  echo("<INPUT TYPE=SUBMIT VALUE=\"Filter\">\n");
  
  echo("&nbsp;<a href=\"calendar.php?calid=-1&calname=$calname&caluser=$caluser\">New ...</a>");
  echo("</form>\n");

  //echo("<br>\n");
  
  $sql="SELECT gCalendar.*,gUserPrefs.email".
    " FROM gCalendar INNER JOIN gUserPrefs ON gCalendar.userid=gUserPrefs.userid".
    " WHERE gCalendar.userid like \"$caluser\" AND calname like \"$calname\"".
    " AND caldate >= \"$startdate\"  AND caldate <= \"$stopdate\"".
    " ORDER BY caldate,userid";
  $caldata=mysql_query($sql);
  
  //echo("$sql<br>\n");

  echo("<table>\n");
  echo("<tr>\n");
  echo("  <td><b>Date&nbsp;</b></td>\n");
  echo("  <td><b>What&nbsp;</b></td>\n");
  echo("  <td><b>Who&nbsp;</b></td>\n");
  echo("  <td><b>Email&nbsp;</b></td>\n");
  echo("</tr>\n");
  
  $counter=0;
  while ( $row = mysql_fetch_array($caldata) ) {
    $counter++;
    if (round($counter/2)!=$counter/2){
      $bgtxt="bgcolor=#F0F0F0";
    } else {
      $bgtxt="bgcolor=#DDDDDD";
    }
    $calid=$row["id"];
    echo("<tr>\n");
    echo("  <td $bgtxt>". substr($row["caldate"],0,10) . "&nbsp;</td>\n");
    echo("  <td $bgtxt>". $row["calname"] . "&nbsp;</td>\n");
    echo("  <td $bgtxt>". $row["userid"] . "&nbsp;</td>\n");
    echo("  <td $bgtxt><em>". $row["email"] . "</em>&nbsp;</td>\n");
    echo("  <td $bgtxt><a href=\"calendar.php?calid=$calid\">Edit ...</a>&nbsp;</td>\n");
    echo("  <td $bgtxt><a href=\"calendar.php?calid=-1&calname=" .
         $row["calname"] . "&caluser=" . $row["userid"] . "&caldate=" . $row["caldate"] .
         "\">Copy ...</a>&nbsp;</td>\n");
    //echo("  <td $bgtxt><a href=\"calendar.php?calid=$calid&action=2\">Del</a>&nbsp;</td>\n");
    echo("</tr>\n");
  }
  echo("</table>\n");

} else {

  if (-1==$calid) {
    
  } else {
    $sql="SELECT * FROM gCalendar WHERE id=$calid";
    $caldata=mysql_query($sql);
    $row=mysql_fetch_array($caldata);
    $caluser=$row["userid"];
    $calname=$row["calname"];
    $caldate=substr($row["caldate"],0,10);
    
  }
  echo("<FORM ACTION=\"calendar.php\" METHOD=POST>\n");
  echo(" <input type=\"hidden\" name=\"userid\" value=\"$userid\">\n");
  echo(" <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n");
  echo(" <input type=\"hidden\" name=\"action\" value=\"1\">\n");
  echo(" <input type=\"hidden\" name=\"calid\" value=\"$calid\">\n");
  
  echo("<table>\n");

  // jscript for calendar hacked from freebie at zapotec.com
  echo("<tr><td>Date:</td><td><input type=\"text\" id=\"caldate\" name=\"caldate\" size=12 value=\"$caldate\" />\n");
?>
    
    <button id="trigger">...</button>
    <script type="text/javascript">//<![CDATA[
      Zapatec.Calendar.setup({
        electric          : false,
        inputField        : "caldate",
        button            : "trigger",
        ifFormat          : "%Y-%m-%d",
        daFormat          : "%Y/%m/%d"
      });
    //]]></script>
<?php
  echo("</td></tr>\n");

  echo("<tr><td>What:</td><td><select name=\"calname\" $ustr1>\n");
  $sql="SELECT DISTINCT calname FROM gCalendar ORDER BY calname";
  $caldata=mysql_query($sql);
  while ( $row = mysql_fetch_array($caldata) ) {
    if ($calname == $row["calname"]) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option value=\"" . $row["calname"] . "\"$sel>" . $row["calname"] . 
         "</option>\n");
  }
  echo(" </select></td><tr>\n");

  echo("<tr><td>Who:</td><td><select name=\"caluser\" $ustr1>\n");
  $sql="SELECT DISTINCT userid as caluser FROM gUserPrefs WHERE lab=\"nsl\" ORDER BY caluser";
  $caldata=mysql_query($sql);
  while ( $row = mysql_fetch_array($caldata) ) {
    if ($caluser == $row["caluser"]) {
      $sel=" selected";
    } else {
      $sel="";
    }
    echo(" <option value=\"" . $row["caluser"] . "\"$sel>" . $row["caluser"] . 
         "</option>\n");
  }
  echo(" </select></td></tr>\n");
  
  echo("<tr><td></td><td colspan=2><INPUT TYPE=SUBMIT VALUE=\"Change\">\n");
  echo("&nbsp;<a href=\"calendar.php\">Cancel</a>\n");
  
  echo("</form>\n");
  
}


cellfooter();

?>
