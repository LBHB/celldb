<?php
/*** CELLDB
cellfileedit.php - edit information about a single experimental data file
created 2002 - SVD
modified 8/4/05 - to work with NSL data - SVD
modified 10/02/05 - generic view interface - SVD
***/

// userid - string id of user to go in added by
// masterid - id in gCellMaster
// rawid - id in gDataRaw (-1 for add new entry)
// action 0 - add file
//        1 - edit file
//        2 - delete file? (not used)

// global include: connect to db and get important basic info about user prefs
$min_sec_level=6;
include_once "./celldb.php";

$errormsg="";

if (""!=$masterid) {
   $celldata = mysql_query("SELECT cellid,penid,penname,siteid,animal,well".
                           " FROM gCellMaster WHERE id=$masterid");
   $masterdata=mysql_fetch_array($celldata);
   extract($masterdata,EXTR_SKIP); 
}

// conform to appropriate channel naming scheme
$sql="SELECT * FROM gPenetration WHERE id=$penid";
$pendata=mysql_query($sql);
if ($penrow=mysql_fetch_array($pendata)) {
  $numchans=$penrow["numchans"];
} else {
  $numchans=8;
}
if ($numchans<=8){
  $chanstrings=array("a","b","c","d","e","f","g","h"); 
  $chanvals=array(1,2,3,4,5,6,7,8);
  
 } elseif ($numchans<=99) {
  $chanstrings=array_fill(0,$numchans,0);
  $chanvals=array_fill(0,$numchans,0);
  for ($ii=0; $ii<$numchans; $ii++) {
    $chanstrings[$ii]=sprintf("%02d-",$ii+1);
    $chanvals[$ii]=$ii+1;
  }
 } else {
  $chanstrings=array_fill(0,$numchans,0);
  $chanvals=array_fill(0,$numchans,0);
  for ($ii=0; $ii<$numchans; $ii++) {
    $chanstrings[$ii]=sprintf("%03d-",$ii+1);
    $chanvals[$ii]=$ii+1;
  }
 }

if (2==$action) {
  // get data posted by user
  $formdata=$_REQUEST;
  
  // figure out runclass to insert/update in gDataRaw
  $sql="SELECT DISTINCT id,name FROM gRunClass where id=$runclassid";
  $runclassdata=mysql_query($sql);
  $row=mysql_fetch_array($runclassdata);
  $runclass=$row["name"];
  $formdata["runclass"]=$runclass;
  $formdata["bad"]=1.0 * $formdata["bad"];
  $formdata["stimconf"]=1.0 * $formdata["stimconf"];
  $formdata["syncpulse"]=1.0 * $formdata["syncpulse"];
  $formdata["healthy"]=1.0 * $formdata["healthy"];
  
  // check to see if new task or stimclass added
  if ($formdata["task"]=="NEW") {
    $formdata["task"]=$formdata["otask"];
  }
  if ($formdata["stimclass"]=="NEW") {
    $formdata["stimclass"]=$formdata["ostimclass"];
  }

  // actually save/update the data
  $errormsg=savedata("gDataRaw",$rawid,$formdata);
  
  if (is_numeric($errormsg)) {
    $rawid=$errormsg;
    $errormsg="";
  }
  
  // save single cell-specific data
  for ($ii = 1; $ii <= $cellcount; $ii++) {
    
    $isolation[$ii]=$isolation[$ii]*1;
    $crap[$ii]=$crap[$ii]*1;
    $unit[$ii]=$unit[$ii]*1;
    $channum[$ii]=$channum[$ii]*1;
    $channel[$ii]=$chanstrings[$channum[$ii]-1];
    
    $sql="SELECT * FROM gSingleCell WHERE id=" . $singleid[$ii];
    $singledata=mysql_query($sql);
    $singrow=mysql_fetch_array($singledata);
    $cellid0=$singrow["cellid"];
    if (-1==$singlerawid[$ii]) {
       // need to create a new gSingleRaw entry
       
       $sql="INSERT INTO gSingleRaw" .
         " (cellid,masterid,singleid,penid,".
         "rawid,channel,unit,channum,".
         "isolation,crap,".
         "addedby,info)" .
         " VALUES (\"$cellid0\",$masterid," . $singleid[$ii] . ",$penid,".
         "$rawid,\"" . $channel[$ii] . "\"," . $unit[$ii] . ",".
         $channum[$ii] . ",".
         $isolation[$ii] . ",". $crap[$ii] . ",".
         "\"$addedby\",\"$siteinfo\")";
       //echo($sql . "<br>");
       $result=mysql_query($sql);
       $singleid[$ii]=mysql_insert_id();
       
     } else {
       $sql="UPDATE gSingleRaw SET ".
         "cellid=\"$cellid0\",".
         "rawid=" . $rawid . ",".
         "isolation=" . $isolation[$ii] . ",".
         "channel=\"" . $channel[$ii] . "\",".
         "unit=" . $unit[$ii] . ",".
         "channum=" . $channum[$ii] . ",".
         "crap=" . $crap[$ii] . ",".
         "addedby=\"$addedby\",".
         "info=\"$siteinfo\"".
         " WHERE id=" . $singlerawid[$ii];
       $result=mysql_query($sql);
     }
   }
   
   header ("Location: $fnpeninfo?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&masterid=$masterid&penid=$penid#$cellid");
   exit;                 /* Make sure that code below does not execute */
}

// load values for the form
if ($rawid>0) {
  // raw data entry already exists.  load info from gDataRaw
  $sql="SELECT * FROM gDataRaw WHERE id=$rawid";
  $rawdata=mysql_query($sql);
  $rowcount=mysql_num_rows($rawdata);
  
  if ($rowcount==0) {
    $rawid=-1; // no entry currently exists in gDataRaw
  } else {
    $row = mysql_fetch_array($rawdata);
    // don't overwrite variables that have already been defined. 
    // hopefully this won't be a problem
    extract($row,EXTR_SKIP); 
    $baseparmname=explode("_",$parmfile);
    $baseparmname=$baseparmname[0];
    $baseparmname=explode("/",$baseparmname);
    $baseparmname=$baseparmname[count($baseparmname)-1];
  }
} else {
   $rowcount=0;
}

$sql="SELECT gPenetration.* FROM gPenetration,gCellMaster" .
  " WHERE gPenetration.id=gCellMaster.penid" .
  " AND gCellMaster.id=$masterid";
$pendata=mysql_query($sql);
$penrow=mysql_fetch_array($pendata);
if ($penrow) {
  $training=$penrow["training"];
 } else {
  $training=0;
 }

if (-1==$rawid and $masterid>0) {
   // new rawdata. guess info from previous gDataRaw entry for this cell
   $sql="SELECT max(gDataRaw.id) as maxid,".
     " sum(masterid=$masterid) as rawcount".
     " FROM gDataRaw, gPenetration, gCellMaster" .
     " WHERE gPenetration.id=gCellMaster.penid" .
     " AND gDataRaw.masterid=gCellMaster.id" .
     " AND gPenetration.animal=\"$animal\"";
   //echo("$sql<br>");

   $rawdata=mysql_query($sql);
   $row=mysql_fetch_array($rawdata);
   
   if (""==$row["maxid"]) {
     $sql="SELECT max(id) as maxid FROM gDataRaw WHERE masterid=$masterid";
     $rawdata=mysql_query($sql);
     $row=mysql_fetch_array($rawdata);
     $rawcount=0;
   } else {
     $rawcount=$row["rawcount"];
   }
   
   if (""<>$row["maxid"]) {
     $rowcount=1;
     $sql="SELECT * FROM gDataRaw WHERE id=" . $row["maxid"];
     $lastrawdata=mysql_query($sql);
     $lastrow=mysql_fetch_array($lastrawdata);
     
     $stimspeedid=$lastrow["stimspeedid"];
     //$stimpath=$lastrow["stimpath"];
     $resppath=$lastrow["resppath"];
     if ($lastrow["masterid"]==$masterid) {
       $lastsamemaster=1;
     } else {
       $lastsamemaster=0;
     }
     
     $respfile=$lastrow["respfile"];
     
     // pype file format-specific code:
     
     // increment resp file name by one to guess the new entry
     $ii=strlen($respfile);
     if ($ii>5 and strcmp($respfile[$ii-4],'.')==0 and
         is_numeric(substr($respfile,$ii-3,3))) {
       $ext=(substr($respfile,$ii-3,3) * 1.0) + 1;
       $respfile=substr($respfile,0,$ii-3);
       $respfile=sprintf("%s%03d",$respfile,$ext);
     }
     
     // nsl file format-specific code
     
     $parmpath=dirname($lastrow["parmfile"]);
     if ("."==$parmpath) {
       $parmpath="";
     } elseif (""!=$parmpath) {
       $parmpath=$parmpath . "/";
     }
     
     $task=$lastrow["task"];
     $stimclass=$lastrow["stimclass"];
     $behavior=$lastrow["behavior"];
     $runclassid=$lastrow["runclassid"];
     $runclass=$lastrow["runclass"];
     $parameters=$lastrow["parameters"];
     
     $monitorfreq=$lastrow["monitorfreq"];
     $eyewin=$lastrow["eyewin"];
     $timejuice=$lastrow["timejuice"];
     $fixtime=$lastrow["fixtime"];
     
     if (strlen($lastrow["respfile"])>0) {
       $errormsg="Guessing info from " . basename($lastrow["respfile"]);
     } else {
       $errormsg="Guessing info from " . basename($lastrow["parmfile"]);
     }
   } else {
     $lastsamemaster=0;
     $rowcount=0;
     $errormsg="Guessing info from scratch";
   }
   $time=date("Y-m-d g:i a");
   
   // nsl file format-specific code
   if ("ear" == $view) {
     if (0==$training) {
       $baseparmname=sprintf("%s%02d",$siteid,$rawcount+1);
       if ("passive"==$task) {
         $parmfile=$parmpath . $baseparmname . "_p_" . strtolower($runclass) . ".m";
       } else {
         $parmfile=$parmpath . $baseparmname . "_a_" . strtolower($runclass) . ".m";
       }
       $respfile="";
     } else {
       //increment resp file name by one to guess the new entry
       $ii=strlen($respfile);
       if ($ii>5 && strcmp($respfile[$ii-6],'-')==0) {
         if ($lastsamemaster) {
           $ext=(substr($respfile,$ii-5,1) * 1.0) + 1;
           $respfile=substr($respfile,0,$ii-6);
           $respfile=sprintf("%s-%d.mat",$respfile,$ext);
         } else {
           $rpc=explode("-",$respfile);
           $respfile=$rpc[0] . "-" . date("Y-m-d") . "-" . $rpc[4] . "-1.mat";
         }
       }
     }
   }
}

if (0==$rowcount) {
  $errormsg="Guessing info from scratch";

  if ($penrow) {
    $resppath=$userrow["dataroot"] . $penrow["penname"] . "/";
  } else {
    $resppath=$userrow["dataroot"];
  }
  
  $time=date("Y-m-d g:i a");
  $stimspeedid=60;
  $stimfile="";
  $respfile="";
  $matlabfile="";
  $eyecalfile="";
  $plexonfile="";
  $respfileevp="";
  $respfileraw="";
  $stimpath="";
  $reps="";
  $seclength="";
  $isolation0="";
  $snr="";
  $bad="";
  $syncpulse="";
  $monitorfreq="";
  $stimconf="";
  $healthy="";
  $eyewin="";
  $timejuice="";
  $maxrate="";
  $parameters="";
  $comments="";
  $corrtrials="";
  $trials="";
  
  $task="none";
  $stimclass="pure tones";
  $behavior="passive";
  $runclassid=1;
  $runclass="TOR";
  $baseparmname=sprintf("%s01",$siteid);
  $parmfile=$baseparmname . "_p_" . strtolower($runclass) . ".m";

}


?>
<HTML>
<HEAD>
<TITLE>celldb - Edit file - <?php echo($cellid)?></TITLE>

<script language="javascript">

function updateguesses() {

   task=document.forms[0].behavior.options[document.forms[0].behavior.selectedIndex].value
   runclass=document.forms[0].runclassid.options[document.forms[0].runclassid.selectedIndex].text

   //document.forms[0].stimpath.value=document.forms[0].parmfile.value

   var path=document.forms[0].parmfile.value
   if (path.lastIndexOf("/")>-1) {
     path=path.substr(0,path.lastIndexOf("/")+1)
   } else {
     path="";
   }
   
   var str1="<?php echo($baseparmname)?>"
   if (task=="active") {
     str1=str1.concat("_a_")
   } else {
     str1=str1.concat("_p_")
   }
   str1=str1.concat(runclass.toLowerCase())
   str1=str1.concat(".m")
   str1=path.concat(str1)
   document.forms[0].parmfile.value=str1
   
   // not called when parmfile value is updated?
   updateguesses2()
}

function updateguesses2()
{
  
  var bn=document.forms[0].parmfile.value
  bn=bn.substr(0,bn.indexOf(".m"))
  
  var bnd=bn
  bnd=bnd.replace("_","-")
  bnd=bnd.replace("_","-")
  var pend=bnd.lastIndexOf("/")
  bnd=bnd.substr(pend+1)
  
  //document.forms[0].respfile.value=bnd.concat(".tar.gz")
  document.forms[0].respfileevp.value=bn.concat(".evp")

  // don't insert spike file automatically. isn't genereated during recording
  //document.forms[0].matlabfile.value=bn.concat(".spk.mat")
  
}

function gotoUrl(url) {
  if (url == "")
    return;
  location.href = url;
}
</script>

</HEAD>

<?php
if (-1==$rawid && 0==$training && "ear"==$view) {
  echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
       " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">\n");
  // removed onload=\"updateguesses()\"
 } else {
  echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
       " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">\n");
 }
if (""==$userid or ""==$masterid) {
   echo("<p><b>ERROR masterid and userid must be specified</b></p>");
}
echo( "<p><b>Editing raw file for site $cellid</b>" );
if ($training) {
  echo(" (training)");
 }

echo("&nbsp;&nbsp;(<a href=\"$fnpeninfo?userid=$userid&sessionid=$sessionid&bkmk=$bkmk&masterid=$masterid&penid=$penid#$cellid\">Pen info</a>)\n");
echo("  (<a href=\"celllist.php?userid=$userid&sessionid=$sessionid#$bkmk\">Cell list</a>)\n");

if (""!=$errormsg) {
  echo("<br><b><font color=\"#CC0000\">$errormsg</font></b>\n");
}

echo("</p>\n");

include "./" . $view . "_view_cellfileedit.php";


?>

</BODY>
</HTML>

