<?php
/*** CELLDB test page
created 2005-09-08 - SVD
***/
?>

<HTML>
<HEAD>
<TITLE>celldb test page</TITLE>

<script language="javascript">
function gotoUrl(url) {
  if (url == "")
    return;
  location.href = url;
}
</script>


</HEAD>
<?php

if ( !function_exists( 'mysql_list_dbs' ) ) {
	die( "MySQL functions missing, have you compiled PHP with the --with-mysql option?\n" );
}

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";

$respfile0="topaz-2005-09-08-dms0-4.mat";
$lastsamemaster=1;

$ii=strlen($respfile0);
if ($ii>5 && strcmp($respfile0[$ii-6],'-')==0) {
  if ($lastsamemaster) {
    $ext=(substr($respfile0,$ii-5,1) * 1.0) + 1;
    $respfile=substr($respfile0,0,$ii-6);
    $respfile=sprintf("%s-%d.mat",$respfile,$ext);
  } else {
    $rpc=explode("-",$respfile0);
    $respfile=$rpc[0] . "-" . date("Y-m-d") . "-" . $rpc[4] . "-1.mat";
  }
 }
echo $respfile0 . " ---> " . $respfile . "<br>\n";
  
?>

</BODY>
</HTML>

