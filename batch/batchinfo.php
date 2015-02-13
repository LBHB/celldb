<?php
/*** CELLDB
batchinfo.php - list/edit analysis batches with data in celldb
created 2009-11-30 - SVD
***/

// global include: connect to db and get important basic info about user prefs
include_once "../celldb.php";

?>

<HTML>
<HEAD>
<TITLE><?php echo("$siteinfo"); ?> - Batches</TITLE>
  <link rel="shortcut icon" href="favicon.ico" />
<style type="text/css">
	A {text-decoration:none}
	A:visited {text-decoration: none}
	A:active {ctext-decoration: none}
	A:hover {background-color: yellow; }
td {
  font-size: 10pt;
  cursor: pointer;
}
</style>
</HEAD>

<?php


echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

// header
cellheader("../");

if (""!=$errormsg) {
  echo("<p><b><font color=\"#CC0000\">$errormsg</font></b></p>\n");
}

$bmaxdata=mysql_query("SELECT max(id) as maxid FROM sBatch");
if ($row=mysql_fetch_array($bmaxdata)) {
  $maxid=$row["maxid"];
}
if (""==$batchid || $batchid < 1 || $batchid>$maxid) {
  $batchid=$maxid;
}
if (""==$show) {
  $show="summ";
}
if (!isset($modelmask)) {
  $modelmask="";
}

echo("<FORM ACTION=\"batchinfo.php\" METHOD=GET>\n");
echo("<input type=\"hidden\" name=\"show\" value=\"$show\">\n");
echo("<a href=\"batchinfo.php?show=$show&batchid=1\">&lt;&lt;--</a>");
if ($batchid>1) {
  echo(" <a href=\"batchinfo.php?show=$show&batchid=".($batchid-1)."\">&lt;-</a>");
}
echo(" <INPUT TYPE=TEXT SIZE=5 NAME=\"batchid\" value=\"$batchid\">\n");
if ($batchid<$maxid) {
  echo(" <a href=\"batchinfo.php?show=$show&batchid=".($batchid+1)."\">-&gt;</a> ");
}
echo(" <a href=\"batchinfo.php?show=$show&batchid=$maxid\">--&gt;&gt;</a> ");
echo(" (mask: <INPUT TYPE=TEXT SIZE=15 NAME=\"modelmask\" value=\"$modelmask\">)\n");
echo(" <INPUT TYPE=SUBMIT NAME=\"Go\" VALUE=\"Go\">\n");
echo(" - <a href=\"batchinfo.php?show=summ&batchid=$batchid\">summary</a>");
echo(" - <a href=\"batchinfo.php?show=edit&batchid=$batchid\">edit</a>");
echo(" - <a href=\"batchinfo.php?show=cells&batchid=$batchid\">cells</a>");
echo("</FORM>");

$batchdata=mysql_query("SELECT * FROM sBatch where id=".$batchid);

if (""==$sortcell) {
  $narfdata=mysql_query("SELECT modelname,count(id) as count,".
                        " round(avg(r_test),3) as rmean,".
                        " round(avg(n_parms),0) as nparms,".
                        " round(avg(r_ceiling),3) as rceiling".
                        " FROM NarfResults".
                        " WHERE batch=$batchid".
                        " AND modelname like '%$modelmask%'".
                        " GROUP BY modelname ORDER BY count DESC,rmean DESC");
} else {
  $narfdata=mysql_query("SELECT modelname,count(id) as count,".
                        " round(avg(r_test),3) as rmean,".
                        " round(avg(n_parms),0) as nparms,".
                        " round(avg(r_ceiling),3) as rceiling".
                        " FROM NarfResults".
                        " WHERE batch=$batchid".
                        " AND cellid='$sortcell'".
                        " AND modelname like '%$modelmask%'".
                        " GROUP BY modelname ORDER BY rmean DESC");
}

if ("summ"==$show) {

  echo("<table>\n");
  echo("<tr>\n");
  echo("  <td><b>Model Name</b></td>\n");
  echo("  <td><b>Count</b></td>\n");
  echo("  <td><b>Mean(r_test)</b></td>\n");
  //echo("  <td><b>Mean(r_ceiling)</b></td>\n");
  echo("  <td><b>n_parms</b></td>\n");
  echo("</tr>\n");

  while ($row = mysql_fetch_array($narfdata)) {
     echo("<tr>\n");
     echo("  <td>" . $row["modelname"] . "</td>\n");
     echo("  <td>". $row["count"] . "</td>\n");
     echo("  <td>". $row["rmean"] . "</td>\n");
     //echo("  <td>". $row["rceiling"] . "</td>\n");
     echo("  <td>". $row["nparms"] . "</td>\n");
     echo("</tr>\n");
  }
  echo("</table>\n");

  echo("<table>\n");
  
  if ($row = mysql_fetch_array($batchdata)) {
    $keys=array_keys($row);
    for ($ii=0;$ii<count($keys);$ii++) {
      if (!is_numeric($keys[$ii]) && "id"!=$keys[$ii]){
        $parms=str_replace(";",";<br>",$row[$keys[$ii]]);
        echo("<tr>\n");
        echo("  <td><b>" . $keys[$ii] . "</b></td>\n");
        echo("  <td>". $parms . "</td>\n");
        echo("</tr>\n");
      }
    }
  }
  echo("</table>\n");

} elseif ("edit"==$show) {
  //edit batch info

} elseif ("cells"==$show && mysql_num_rows($narfdata)>0 ) {

  echo("<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n");
  if (""==$sortmodel) {
    $celldata=mysql_query("SELECT cellid FROM sRunData WHERE batch=$batchid ORDER BY cellid");
  } else {
    $celldata=mysql_query("SELECT cellid FROM NarfResults WHERE batch=$batchid AND modelname='".$sortmodel."' ORDER BY r_test DESC");
  }
  
  while ($row=mysql_fetch_array($narfdata)) {
    $batchdata=mysql_query("SELECT NarfResults.cellid,".
                           " round(NarfResults.r_test,3) as r".
                           " FROM NarfResults".
                           " WHERE NarfResults.modelname='".$row["modelname"]."'".
                           " AND NarfResults.batch=$batchid ORDER BY cellid");
    echo("  <tr><td><a href=\"?show=cells&batchid=$batchid&modelmask=".
         urlencode($modelmask)."&sortmodel=".urlencode($row["modelname"]).
         "\">". $row["modelname"] . "</a></td>\n");
    mysql_data_seek($celldata,0);
    $crow=mysql_fetch_array($celldata);
    while ($crow=mysql_fetch_array($celldata)) {
      mysql_data_seek($batchdata,0);
      $ii=0;
      while (!$ii && $brow=mysql_fetch_array($batchdata)) {
        if ($crow["cellid"]==$brow["cellid"]) {
          $ii=1;
        }
      }
      if ($brow) {
        $r_norm=(1-$brow["r"]/0.7)*255;
        if ($r_norm<0) { $r_norm=0;}
        if ($r_norm>255) { $r_norm=255; }
        if ($r_norm<16) {
          $cc="0".dechex($r_norm);
        } else {
          $cc=dechex($r_norm);
        }
        echo("<td bgcolor=\"#$cc$cc$cc\">".
             "<a href=\"?show=cells&batchid=$batchid&modelmask=".
             urlencode($modelmask)."&sortmodel=".urlencode($row["modelname"]).
             "&sortcell=".$crow["cellid"].
             "\" title=\"".$crow["cellid"].":".
             $brow["r"]."\">&nbsp;&nbsp;</a></td>");
        //echo("<td bgcolor=\"#$cc$cc$cc\"><a title=\"".$crow["cellid"].":".
        //     $brow["r"]."(".$row["modelname"].")\">&nbsp;</a></td>");
      } else {
        echo("<td bgcolor=\"#FF0000\"><a title=\"".$ii.$crow["cellid"].
             ":nan\">&nbsp;</a></td>");
        //echo("<td bgcolor=\"#FF0000\"><a title=\"".$crow["cellid"].
        //     ":nan (".$row["modelname"].")\">&nbsp;</a></td>");
      }
    }
    echo("</tr>\n");
  }
  echo("</table>\n");
  
} elseif ("cells"==$show) {
  // list all cells in batch
  if ($row = mysql_fetch_array($batchdata)) {
    echo("Batch $batchid - ".$row["name"]." - ".$row["details"]."<br>");
  }
  $sql="SELECT sRunData.*,gCellMaster.penid,sResults.matstr,sResults.lastmod".
                       " FROM (sRunData INNER JOIN gCellMaster".
                       " ON sRunData.masterid=gCellMaster.id)".
                       " LEFT JOIN sResults ON sRunData.id=sResults.runid".
                       " WHERE sRunData.batch=".$batchid.
    " ORDER BY sRunData.cellid";
  
  $rundata=mysql_query($sql);
  
  echo("<table>\n");
  
  while ($row=mysql_fetch_array($rundata)) {
    echo("<tr>\n");
    echo("  <td><a href=\"cellinfo.php?cellid=".$row["cellid"]."&batchid=$batchid\"><b>" . $row["cellid"] . "</b></a></td>\n");
    echo("  <td><a href=\"../peninfo.php?penid=".$row["penid"]."\">Pen</a></td>\n");
    $lastmod=substr($row["lastmod"],2,8);
    echo("  <td>$lastmod</td>\n");
    
    $matstr=$row["matstr"];
    $predstr=stristr($matstr,"predxc");
    $ee=strpos($predstr,"];");
    $predstr=substr($predstr,8,($ee-8));
    $predstr=explode(";",$predstr);
    $errstr=stristr($matstr,"prederr");
    $ee=strpos($errstr,"];");
    $errstr=substr($errstr,9,($ee-9));
    $errstr=explode(";",$errstr);
    for ($ii=0;$ii<count($predstr);$ii++) {
      $pp= explode(" ",$predstr[$ii]);
      $ee= explode(" ",$errstr[$ii]);
      if ("NaN"==$pp[0]) {
        $ps="NaN";
      } else {
        $ps=round(1.0*$pp[0],3) . "+/-" . round(1.0*$ee[0],3);
        if (1.0*$ee[0]>0 && (1.0*$pp[0]) / (1.0*$ee[0]) > 2) {
          $ps=$ps."*";
        }
      }
      echo("  <td><a href=\"results/batch".$batchid."/".$row["cellid"].".3.jpg\">$ps</a></td>\n");
    }
    echo("</tr>\n");
    

  }
  echo("</table>\n");
  
}

 

cellfooter();

?>
