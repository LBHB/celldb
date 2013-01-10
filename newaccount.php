<?php
/*** celldb
newaccount.php - sign up for new account
created 07-30-2005 - SVD, hacked from mtdb newaccount form
***/

$newaccount=1;  // tells celldb not to check for password

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";
?>

<HTML>
<HEAD>
<style type="text/css">
<!--
A{text-decoration:none}
A:visited {text-decoration: none}
A:active {text-decoration: none}
A:hover {color: #AA0000; text-decoration: underline}
-->
</style>
<TITLE>CellDB - New Account</TITLE>
</HEAD>
<?php

if (1==$action) {
  // "delete" this account
  $sql="UPDATE gUserPrefs SET bad=1-bad WHERE id=$uid";
  mysql_query($sql);
}

$errormsg="";

// test to see if parameters have been entered correctly
if (2==$action && (""==$passwd || ""==$userid)) {
  $errormsg="ERROR: You must enter a user id and password.";
  $action=0;
}
if (2==$action && ($passwd!=$passwd2)) {
  $errormsg="ERROR: Passwords do not match.";
  $action=0;
}
if (2==$action && (""==$realname)) {
  $errormsg="ERROR: You must enter your real name in the field provided.";
  $action=0;
}
if (2==$action) {
  $sql="SELECT * FROM gUserPrefs WHERE userid=\"$userid\" AND not(isnull(password))";
  $userdata=mysql_query($sql);
  if (mysql_num_rows($userdata)>0) {
    $errormsg="ERROR: Account already exists with the requested userid.";
    $action=0;
  }
}

//$action=0;  //disable action for debugging

// ok, passed the tests. now save posted info to people
if (2==$action) {
  
  // create a new entry in the user table
  $sessionid=md5($passwd);
  $sql="SELECT * FROM gUserPrefs WHERE userid=\"$userid\"";
  $userdata=mysql_query($sql);
  
  if (mysql_num_rows($userdata)>0) {
    $sql="UPDATE gUserPrefs" .
      " SET password=\"$sessionid\",".
      " seclevel=2,".
      " email=\"" . tidystr($email) . "\",".
      " realname=\"$realname\", ".
      " lab=\"$LAB\"".
      " WHERE userid=\"$userid\"";
    $result=mysql_query($sql);
    $row=mysql_fetch_array($userdata);
    $uid=$row["id"];
  } else {
    $sql="INSERT INTO gUserPrefs" .
      " (userid,password,seclevel,email,realname,lab)".
      " VALUES (\"" . tidystr($userid) . "\",\"" . 
      "$sessionid\",2,\"" . tidystr($email) . "\",\"$realname\",".
      "\"$LAB\")";
    $result=mysql_query($sql);
    $uid=mysql_insert_id();
  }
  
  if (mysql_errno()>0) {
    echo mysql_errno().": ".mysql_error()."<BR>";
    echo("$sql $result");
  } else {
    // tell sysadmin that they signed up
    mail("svd@umd.edu", 
         $userid . " just signed up!", 
         $userid . " ($realname) just signed up for nsl-celldb",
         "From: $from\nX-Mailer: PHP/ . $phpversion()", "-f $from");
  }
}

echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");
echo("<P>$siteinfo - New account sign-up</p>\n");

if ($uid>0) {
  echo("<p><b>Account created successfully!</b><br></p>\n");
  echo("Remember this info. To log in, you must know:<br>\n");
  echo("&nbsp;&nbsp;&nbsp;&nbsp;userid: $userid<br>\n");
  echo("&nbsp;&nbsp;&nbsp;&nbsp;password: ********<br>\n");
  echo("</p>\n");
  
  echo("<p>\n");
  echo("<a href=\"celllist.php?userid=$userid&sessionid=$sessionid&reqfmt=1\">");
  echo("Browse celldb!</a>");
  echo("</p>\n");
  
  //footer($userid);
  exit;
}

if (""!=$errormsg) {
  echo("<p><b><font color=\"#CC0000\">$errormsg</font></b></p>\n");
}

if ("guest"==$userid) {
  $userid="";
}

// print out some basic instructions
echo("<table>\n");
echo("<tr><td><b>Instructions</b></td></tr>\n");
echo("<tr><td>\n");
echo("Signing up is easy. Just enter your info below.\n");
echo("</td></tr>\n");
echo("<tr><td>&nbsp;</td></tr>\n");
echo("</table>\n");

echo("<FORM ACTION=\"newaccount.php\" METHOD=POST>\n");
echo("<input type=\"hidden\" name=\"action\" value=2>\n");  // do the edit
echo("<table>\n");
echo("<tr><td colspan=3><b>Account info</b></td></tr>\n");
echo("<tr><td valign=top>User ID:</td><td valign=top><INPUT SIZE=30 TYPE=TEXT NAME=\"userid\" VALUE=\"$userid\">&nbsp;&nbsp;</td>\n");
echo("<td valign=top></td></tr>\n");
echo("<tr><td>Password:</td><td><INPUT SIZE=30 TYPE=PASSWORD NAME=\"passwd\" VALUE=\"\"></td><td>(Warning: Transmitted in clear text!)</td></tr>\n");
echo("<tr><td>Re-enter:</td><td><INPUT SIZE=30 TYPE=PASSWORD NAME=\"passwd2\" VALUE=\"\"></td><td></td></tr>\n");
echo("<tr><td>Real name:</td><td><INPUT TYPE=TEXT SIZE=30 NAME=\"realname\" VALUE=\"$realname\"></td><td>(So we know who you are)</td></tr>\n");
echo("<tr><td>E-mail:</td><td><INPUT TYPE=TEXT SIZE=30 NAME=\"email\" VALUE=\"$email\"></td><td></td></tr>\n");

echo("<tr><td></td><td><INPUT TYPE=SUBMIT NAME=\"Submit\" VALUE=\"Submit\">");
echo(" <a href=\"./\">Cancel</a></td></tr>\n");
echo("</table>\n");
echo("</FORM>\n");

//footer("");
?>
</BODY>
</HTML>
