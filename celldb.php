<?php

/*** CELLDB
celldb.php - global include file that connects to db and sets up a few 
              commonly useful function for celldb (also used by queue and 
              musicdb)
created 2002 - SVD
modified 7/28/2005 - SVD - hacking to work in Shamma lab
                     introduced internal password checking
                     (preserved old ypcheck code)
modified 9/20/2005 - SVD - built in alternative views
                     added user settings to facilitate configuration
modified 2005-10-10 - SVD - moved user settings out to config.php(.default)

***/




// define some useful functions
function parsetimestamp ($timestamp) {
  
  $year=substr($timestamp,0,4);
  $month=substr($timestamp,4,2);
  $day=substr($timestamp,6,2);
  
  return $month . "-" . $day . "-" . $year;
}
function parsetimestamplong ($timestamp) {
  
  if ("-"==$timestamp[4]) {
    $year=substr($timestamp,0,4);
    $month=substr($timestamp,5,2);
    $day=substr($timestamp,8,2);
    $hour=substr($timestamp,11,2);
    $min=substr($timestamp,14,2);
  } else {
    $year=substr($timestamp,0,4);
    $month=substr($timestamp,4,2);
    $day=substr($timestamp,6,2);
    $hour=substr($timestamp,8,2);
    $min=substr($timestamp,10,2);
  }
   return $month . "-" . $day . " " . $hour . ":" . $min;
}
function sec2hrs ($sec) {
  
  if ($sec<0) {
    return "0:00:00";
  } else {
    $ss=floor($sec % 60);
    $mm=(floor($sec/60) % 60);
    $hh=floor($sec/3600);
    
    return sprintf("%d:%02d:%02d",$hh,$mm,$ss);
  }
}
function stringfilt($s) {
   
   $temp=explode(chr(10),$s);
   $temp=implode("<br>",$temp);
   $temp=explode(chr(9),$temp);
   $temp=implode("&nbsp&nbsp&nbsp;",$temp);
   //$temp=explode(" ",$temp);
   //$temp=implode("&nbsp;",$temp);
   
   return $temp;
}
function dblog($s,$userid) {
  
  $d=date("d-M-Y H:i:s");

  $outfile="/home/tmp/queue/queuemasterlog.txt";
  $fid = fopen ($outfile, "a");
  fwrite($fid,"$d - (uid=$userid) $s\n");
  fclose($fid);
}

function checkpwd($userid,$passwd) {
  
  $errormsg="ERROR: Account $userid does not exist or incorrect password.";
  
  $sql="SELECT * FROM gUserPrefs WHERE userid=\"$userid\"";
  $userdata=mysql_query($sql);
  if (mysql_num_rows($userdata) > 0) {
    $row = mysql_fetch_array($userdata);
    $realpw=$row["password"];
    if ($passwd==$realpw) {
      // ok, password checks out (already encrypted)
      $seclevel=$row["seclevel"];
      
    } elseif (md5($passwd)==$realpw) {
      // md5 encrypted password checks out
      $refpage=getenv("SCRIPT_NAME");
      
      $_SESSION["sessuserid"]=$userid;
      $_SESSION["sessuidnum"]=$row["id"];
      $_SESSION["sesssessionid"]=md5($passwd);
      
      header("Location: $refpage?reqfmt=1");
      //echo("Location: $refpage?reqfmt=1");
      exit;
    } elseif (crypt($passwd,'my secret')==$realpw) {
     // newly entered password checks out
     //want to reload with encrypted pw in url
      $refpage=getenv("SCRIPT_NAME");
      
      $_SESSION["sessuserid"]=$userid;
      $_SESSION["sessuidnum"]=$row["id"];
      $_SESSION["sesssessionid"]=crypt($passwd,'my secret');
      
      header("Location: $refpage?reqfmt=1");
      exit;
    } else {
      echo("Location: /celldb/index.php?errormsg=".rawurlencode($errormsg));
      //header("Location: /celldb/index.php?errormsg=".rawurlencode($errormsg));
      //$_SESSION["sessuserid"]="guest";
      //$_SESSION["sesssessionid"]="";
      //return 0;
      exit;
    }
    
  } else {
    header("Location: /celldb/index.php?errormsg=".rawurlencode($errormsg));
    //$_SESSION["sessuserid"]="guest";
    //$_SESSION["sesssessionid"]="";
    //return 0;
    exit;
  }
  
  return $seclevel;
}

function checkpwdold($userid,$passwd,$crpwd) {
   
  $errormsg="ERROR: Account does not exist or incorrect password.";

  $cmd="ypmatch $userid passwd";
  $t=split(":",exec("$cmd 2>&1", $output));
  $cpwd=$t[1];
  
  if ($crpwd==$cpwd) {
    //echo("passwd accepted<br>");
  } elseif (crypt($passwd,$cpwd)==$cpwd) {
    
    //want to reload with encrypted pw in url
    $refpage=getenv("SCRIPT_NAME");
    
    header("Location: $refpage?userid=" . $userid ."&sessionid=" . 
           crypt($passwd,$cpwd));
    exit;
  } else {
    //echo("bad passwd");
    $cpwd="";
    header("Location: ./?errormsg=".rawurlencode($errormsg));
  }
  
  return $cpwd;
}

function tidystr($instr) {
  $testchar="\"";
  $outstr=explode($testchar,$instr);
  $outstr=implode($testchar.$testchar,$outstr);
  
  $testchar="\\";
  $outstr=explode($testchar,$outstr);
  $outstr=implode($testchar.$testchar,$outstr);
  
  return $outstr;
}

function savedata($tablename,$id,$formdata) {
  global $userid;
  
  // check to see if gDataRaw entry exists yet
  $sql="SELECT * FROM $tablename WHERE id=$id";
  $rawfiledata = mysql_query($sql);
  $rawfilerows=mysql_num_rows($rawfiledata);
  
  $tabledata=mysql_query("DESCRIBE $tablename");
  
  if (0==$rawfilerows) {
    // entry doesn't exist yet. create a new one
    $sqlfields="INSERT INTO $tablename (";
    $sqldata=" VALUES (";
    $useinfo=0;
    while ($trow=mysql_fetch_array($tabledata)) {
      $key=$trow["Field"];
      $type=$trow["Type"];
      if (isset($formdata[$key]) && !is_array($formdata[$key])) {
        $data=$formdata[$key];
        $sqlfields=$sqlfields . $key . ", ";
        if (strstr($type,"double")) {
          $sqldata=$sqldata . floatval($data) . ", ";
        } elseif (strstr($type,"int")) {
          $sqldata=$sqldata . intval($data) . ", ";
        } else {
          $sqldata=$sqldata . "\"" .tidystr($data) . "\", ";
        }
      }
      if ("info"==$key) {
        $useinfo=1;
      }
    }
    if ($useinfo) {
      $sqlfields=$sqlfields . "info)";
      $sqldata=$sqldata . "\"" . $siteinfo. "\")";
    } else {
      $sqlfields=$sqlfields . "addedby,dateadded)";
      $sqldata=$sqldata . "\"" . $userid. "\",now())";
    }
    $sql=$sqlfields . $sqldata;
    
    //echo $sql . "<br>";
    $result=mysql_query($sql);
    $id=mysql_insert_id();
  } else {
    // tablename entry does exist.  update with posted values
    
    $sql="UPDATE $tablename SET ";
    
    $useinfo=0;
    while ($trow=mysql_fetch_array($tabledata)) {
      $key=$trow["Field"];
      $type=$trow["Type"];
      if (isset($formdata[$key]) && !is_array($formdata[$key])) {
        $data=$formdata[$key];
        $sql=$sql . $key . "=";
        if (strstr($type,"double")) {
          $sql=$sql . floatval($data) . ", ";
        } elseif (strstr($type,"int")) {
          $sql=$sql . intval($data) . ", ";
        } else {
          $sql=$sql . "\"" .tidystr($data) . "\", ";
        }
      }
      if ("info"==$key) {
        $useinfo=1;
      }
    }
    if ($useinfo) {
      $sql=$sql . "info=\"$siteinfo\" WHERE id=$id";
    } else {
      $sql=$sql . "addedby=\"$userid\" WHERE id=$id";
    }
    //echo $sql . "<br>";
    $result=mysql_query($sql);
  }
  if (!$result) {
    $errormsg=mysql_error() . "<br>($sql)";
  } else {
    $errormsg=$id;
  }
  return $errormsg;
}

function checksec($userid,$seclevel,$uidmatch,$secmin) {
  
  if ($seclevel>=$secmin || ($userid==$uidmatch && $seclevel>0)) {
    return 1;
  } else {
    return 0;
  }
}

function queuefooter() {
  
  echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>");
  
  $sql="SELECT *,TIME_TO_SEC(NOW())-TIME_TO_SEC(daemonclick) as sec_ago FROM tGlobalData";
  $globaldata=mysql_query($sql);
  $row=mysql_fetch_array($globaldata);
  if (120<$row["sec_ago"]) {
    echo("<b>NOTICE!  Last tick on queue daemon host " .
         $row["daemonhost"] . " was " . $row["sec_ago"] .
         " seconds ago (" . $row["daemonclick"] . "!)</b><br>");
  } else {
    echo("<em>Queue daemon host:</em> " . $row["daemonhost"] .
         "  <em>Last tick:</em> " . $row["sec_ago"] . " sec (" . 
         $row["daemonclick"] . ")<br>");
  }
  
}

function cellheader($rpath="") {
  
  global $siteinfo;
  global $userid;
  
  $ss=explode(".",getenv("SCRIPT_NAME"));
  $ss=explode("/",$ss[0]);
  $ss=$ss[count($ss)-1];
  
  echo("<p><b>$siteinfo</b>\n");
  if ("celllist"==$ss && "cells"==$_GET["showdata"]) {
    echo("&nbsp;<b>Cell list</b>\n");
  } else {
    echo("&nbsp;<a href=\"".$rpath."celllist.php?showdata=cells\">Cell list</a>\n");
  }
  if ("celllist"==$ss && "cells"!=$_GET["showdata"]) {
    echo("&nbsp;<b>Behavior</b>\n");
  } else {
    echo("&nbsp;<a href=\"".$rpath."celllist.php?showdata=behavior\">Behavior</a>\n");
  }
  if ("animals"==$ss) {
    echo("&nbsp;<b>Animal details</b>\n");
  } else {
    echo("&nbsp;<a href=\"".$rpath."animals.php?animal=$animal\">Animal details</a>\n");
  }
  if ("weights"==$ss) {
    echo("&nbsp;<b>Weights</b>\n");
  } else {
    echo("&nbsp;<a href=\"".$rpath."weights.php\">Weights</a>\n");
  }
  if ("tools"==$ss) {
    echo("&nbsp;<b>Tools</b>\n");
  } else {
    echo("&nbsp;<a href=\"".$rpath."tools.php\">Tools</a>\n");
  }
  if ("editrunclass"==$ss) {
    echo("&nbsp;<b>Run classes</b>\n");
  } else {
    echo("&nbsp;<a href=\"".$rpath."editrunclass.php\">Run classes</a>\n");
  }
  if ("cellfilelist"==$ss) {
    echo("&nbsp;<b>File query</b>\n");
  } else {
    echo("&nbsp;<a href=\"".$rpath."cellfilelist.php\">File query</a>\n");
  }
  if ("batch"==$ss) {
    echo("&nbsp;<b>Analysis</b>\n");
  } else {
    echo("&nbsp;<a href=\"".$rpath."batch\">Analysis</a>\n");
  }
  echo("&nbsp;<a href=\"".$rpath."queuemonitor.php?complete=-1\">Jobs</a>\n");
  echo("&nbsp;<a href=\"".$rpath."order_history.php\">Orders</a>\n" );
  if ("celldbusers"==$ss) {
    echo("&nbsp;<b>Settings</b>\n");
  } else {
    echo("&nbsp;<a href=\"".$rpath."celldbusers.php?activeusers=1&edituserid=$userid\">Settings</a>\n");
  }
  echo("&nbsp;<a href=\"".$rpath."index.php?logout=1\">Logout</a>\n" );
  echo("</p>\n");
  echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
}

function orderheader() {
  
  global $siteinfo;
  
  echo("<p><a href=\"celllist.php\">$siteinfo</a> - ");
  echo("<a href=\"order_history.php\">order history</a> - ");
  echo("<a href=\"order_companies.php\">companies</a> - ");
  echo("<a href=\"order_items.php\">items</a> - ");
  echo("<a href=\"order_editcompany.php\">new company</a> - ");
  echo("<a href=\"order_editorder.php\">new order</a> - ");
  echo("<a href=\"order_edititem.php\">new item</a>");
  echo("</p>\n");
  echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>\n");
}

function cellfooter($extraline="") {
  
  echo("<HR ALIGN=CENTER SIZE=1 WIDTH=100% NOSHADE>");
  
  if (""!=$extraline) {
    echo($extraline . "<br>\n");
  }
  
  $userid=$_SESSION["sessuserid"];
  $uidnum=$_SESSION["sessuidnum"];
  
  if (""==$userid) {
    echo("<em>Not logged in.</em> ");
    echo("<a href=\"index.php\">Log in.</a>\n");
  } else {
    echo("<em>Currently logged in as</em> $userid ($uidnum). ");
    echo("<a href=\"index.php?logout=1\">Log out</a>\n");
    echo("&nbsp;<a href=\"calendar.php\">Dooty calendar</a>\n");
  }
}

function fatal_error($errormsg) {

   header("Location: error.php?errormsg=".rawurlencode($errormsg));
   exit();

}

// read in user settings
include_once "config.php";

if (!isset($dbserver)) {
  fatal_error("Database server not specified. <tt>config.php</tt> missing?");
}

//version 0.3: added multi-unit per recording site support!
// version 0.4, moved over to NSL, own user management system
//              what to do about different types of physiology data?
// version 0.5  added alternate views
$siteinfo="CELLDB v0.5";

// don't log warnings because SVD is too lazy to pre-declare all variables
//error_reporting(E_ALL & ~E_NOTICE);

// automatically parse html posted variables (i think?)
//import_request_variables("GP", "");
extract($_REQUEST, EXTR_PREFIX_ALL|EXTR_REFS, '');

// initial db connection needed basically for anything
//$dbcnx=mysql_connect($dbserver.":3306",$dbuser,$dbpassword);

// initial db connection needed basically for anything
$dbcnx=@mysql_connect($dbserver.":3306",$dbuser,$dbpassword);

if (!$dbcnx) {
  fatal_error("Could not connect to database server $dbserver (uid=$dbuser, pw=$dbpassword). Make sure settings in <tt>config.php</tt> are valid.");
 }

// use database called $dbname (specified in config.php)
if (!mysql_select_db($dbname, $dbcnx)) {
  fatal_error("Could connect to db server but could not open database $dbname. Make dbname specification is correct in <tt>config.php</tt>.");
 }

// start session if it's not started
$tid=session_id();
if (""==$tid) {
  session_start();
}
if (""==$userid) {
  $userid=$_SESSION["sessuserid"];
}
if (""==$sessionid && ""==$passwd) {
  $sessionid=$_SESSION["sesssessionid"];
}
if (""==$uidnum) {
  $uidnum=$_SESSION["sessuidnum"];
}

// check password 
if (1==$newaccount) {
  $seclevel=0;
  if (""==$userid) {
    $userid="guest";
  }
} elseif ("guest"==$userid) {
  if (""==$sessionid) {
    $sessionid="guest";
  }
  $seclevel=checkpwd($userid,$sessionid);
} else {
  if (""==$sessionid) {
    $sessionid=$passwd;
  }
  if (1==$ypaccounts) {
    $sessionid=checkpwdold($userid,$passwd,$sessionid);
    $seclevel=0;
    if (""!=$sessionid) {
      $sql="SELECT * FROM gUserPrefs WHERE userid=\"$userid\"";
      $userdata=mysql_query($sql);
      if (mysql_num_rows($userdata) > 0) {
        $row = mysql_fetch_array($userdata);
        $seclevel=$row["seclevel"];
      } else {
        // valid linux account but not in database yet 
        // .. set up with default values
        $sql="INSERT INTO gUserPrefs (userid,seclevel) values (\"$userid\",2)";
        mysql_query($sql);
        $seclevel=2;
      }
      //echo("passwd accepted $seclevel<br>");
    } else {
    }
  } else {
    $seclevel=checkpwd($userid,$sessionid);
  }
}

if ((isset($min_sec_level) && $seclevel<$min_sec_level) ||
    (!$newaccount && $seclevel<1)) {
  header("Location: error.php?errormsg=".
         rawurlencode("ERROR: Cannot load page") .
         "&refurl=celllist.php");
}

if (""!=$userid) {
   $userdata = mysql_query("SELECT * FROM gUserPrefs WHERE userid=\"$userid\"");
   $rowcount=mysql_num_rows($userdata);
   if ($rowcount>0) {
     $userrow = mysql_fetch_array($userdata);
     if (""==$animal) {
       $animal=$userrow["lastanimal"];
     }
     if (""==$well) {
       $well=$userrow["lastwell"];
     }
     if (""==$queryspecies) {
       $queryspecies=$userrow["lastspecies"];
     }
     if (""==$recstat) {
       $recstat=$userrow["lasttraining"];
     }
     $uidnum=$userrow["id"];
     $lastjobcomplete=$userrow["lastjobcomplete"];
     $lastjobuser=$userrow["lastjobuser"];
     $lastallowqueuemaster=$userrow["lastallowqueuemaster"];
     $lastmachinesort=$userrow["lastmachinesort"];
     
     $userbg=$userrow["bgcolor"];
     $userfg=$userrow["fgcolor"];
     $linkfg=$userrow["linkfg"];
     $vlinkfg=$userrow["vlinkfg"];
     $alinkfg=$userrow["alinkfg"];
     $birthday=$userrow["birthday"];
     
   } elseif ($seclevel>0) {
     $animal="All";
     $well=0;
     mysql_query("INSERT INTO gUserPrefs (userid,lastanimal,lastwell) VALUES (\"$userid\",\"$animal\",$well)");
     $userdata = mysql_query("SELECT * FROM gUserPrefs WHERE userid=\"$userid\"");
     $userrow=mysql_fetch_array($userdata);
   }
} elseif (""==$animal || ""==$well) {
  $animal="All";
  $well=0;
}

$_SESSION["sessuserid"]=$userid;
$_SESSION["sessuidnum"]=$uidnum;
$_SESSION["sesssessionid"]=$sessionid;


// SET SOME GLOBALS
$SpeciesList=array("ferret","rat","mouse","monkey","human");
$SpeciesStatusList=array("ferret,active","ferret,all","rat,active","rat,all",
                         "mouse,active","mouse,all","monkey,all","human,all");

if (""==$userbg) {
  $userbg="#FFFFFF";
}
if (""==$userfg) {
  $userfg="#000000";
}
if (""==$linkfg) {
  $linkfg="#6666FF";
}
if (""==$vlinkfg) {
  $vlinkfg="#3333FF";
}
if (""==$alinkfg) {
  $alinkfg="#FFFF00";
}

// set up link names for alternative views:

if ("eye"==$view) {
  $fnpenedit="penedit.php";
  $fnpendump=$view . "_pendump.php";
  $fnpeninfo=$view . "_peninfo.php";
  $fncelledit="celledit.php";
  $fncellfileedit="cellfileedit.php";
  $fncellfilelist=$view . "_cellfilelist.php";
 } else {
  $fnpenedit="penedit.php";
  $fnpendump="pendump.php";
  $fnpeninfo="peninfo.php";
  $fncelledit="celledit.php";
  $fncellfileedit="cellfileedit.php";
  $fncellfilelist="cellfilelist.php";
 }

?>
