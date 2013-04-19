<?php
//automatically parse html posted variables (i think?)
//import_request_variables("GP", "");

if (1==$_GET['logout']) {
  // global include: connect to db and get basic info about user prefs
  include_once "./celldb.php";
  
  // user chose to log out
  $errormsg="$userid logged out";
  $userid="";
  $sessionid="";
  $_SESSION["sessuserid"]="";
  $_SESSION["sesssessionid"]="";
  $_SESSION["sessuidnum"]="";
 } else {
  
  $newaccount=1;  // tells celldb not to check for password
  include_once "./celldb.php";
}

if (""!=$sessionid) {
  // password checked out, jump to celllist.php
  header("Location: celllist.php");
  exit;
}


?>
<HTML>
<HEAD>
<TITLE>CellDB Login</TITLE>
</HEAD>
<body bgcolor="#FFFFFF">
<?php

//echo("<p align=\"center\"><img width=200 border=0 src=\"photo/ferret_xmas.jpg\"></p>"); 
//echo("<p align=\"center\"><img width=300 border=0 src=\"photo/ferretNewYear.jpg\"></p>");
echo("<p align=\"center\"><img width=300 border=0 src=\"photo/bf_ferret.jpg\"></p>");
echo("<p align=\"center\">Welcome to CellDB.<br>Today is " .date("l, F dS Y") . "<br>");
$sql="SELECT count(id) as ucount from gUserPrefs WHERE lab='$LAB'";
$adata=mysql_query($sql);
$urow=mysql_fetch_array($adata);
$ucount=$urow['ucount'];
echo("$ucount accounts in CellDB lab=$LAB.</p>");


if (""!=$errormsg) {
  echo("<p><b><font color=\"#CC0000\">$errormsg</font></b></p>\n");
}
?>

<p>
<FORM ACTION="celllist.php" METHOD=POST>
User ID: <INPUT TYPE=TEXT NAME="userid">
Password: <INPUT TYPE=PASSWORD NAME="passwd">
<INPUT TYPE=SUBMIT VALUE="GO">
</FORM>
</p>

<?php

if (0==$ypaccounts) {
  echo("<p><b>Don't have an account yet?</b> <a href=\"newaccount.php\">Create an account</a></p>\n");
  echo("<p>... or email Stephen -- davids at ohsu dot edu</p>\n");
}
?>

</BODY>
</HTML>
