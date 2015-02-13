<?php
/*** CELLDB
cellinfo.php - list/edit info about analyzed cells
created 2009-11-30 - SVD
***/

// global include: connect to db and get important basic info about user prefs
include_once "../celldb.php";

if (""==$cellid) {
  $cellid="sir051a-a1";
}
if (""==$batchid) {
  $batchid=162;
}

?>

<HTML>
<HEAD>
<TITLE><?php echo("$siteinfo - $cellid"); ?></TITLE>
  <link rel="shortcut icon" href="favicon.ico" />
</HEAD>

<?php

echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

// header
cellheader("../");

if (""!=$errormsg) {
  echo("<p><b><font color=\"#CC0000\">$errormsg</font></b></p>\n");
}

if (""==$show) {
  $show="summ";
}

$sql="SELECT sRunData.cellid,sRunData.batch,sResults.matstr,sResults.lastmod,".
" sBatch.name, sBatch.details".
" FROM sRunData LEFT JOIN sResults".
" ON sRunData.id=sResults.runid".
" INNER JOIN sBatch ON sRunData.batch=sBatch.id".
" WHERE cellid='$cellid' ORDER BY sRunData.batch";
$celldata=mysql_query($sql);

if ("summ"==$show) {
  echo("<b>Cell $cellid</b>\n");
  echo("<table>\n");
  
  while ($row = mysql_fetch_array($celldata)) {
    echo("<tr>\n");
    echo("  <td><a href=\"batchinfo.php?batchid=" . $row["batch"] . "&show=cells\">" . $row["batch"] . "&nbsp;-&nbsp;" . $row["name"] . "</a></td>\n");
    $matstr=$row["matstr"];
    $lastmod=substr($row["lastmod"],2,8);
    echo("  <td>$lastmod</td>\n");
    $predstr=stristr($matstr,"predxc");
    $ee=strpos($predstr,"];");
    $predstr=substr($predstr,8,($ee-8));
    $predstr=explode(";",$predstr);
    for ($ii=0;$ii<count($predstr);$ii++) {
      $pp= explode(" ",$predstr[$ii]);
      if ("Nan"==$pp[0]) {
        $pp[0]="nan";
      } else {
        $pp=round(1.0*$pp[0],3);
      }
      echo("  <td><a href=\"results/batch".$row["batch"]."/$cellid.3.jpg\">". $pp . "</a></td>\n");
    }
    echo("</tr>\n");
  }
  echo("</table>\n");

} elseif ("edit"==$show) {
  //edit batch info

} elseif ("cells"==$show) {
  // list all cells in batch
  $rundata=mysql_query("SELECT sRunData.*, gCellMaster.penid,sResults.matstr".
                       " FROM sRunData INNER JOIN gCellMaster".
                       " ON sRunData.masterid=gCellMaster.id".
                       " LEFT JOIN sResults ON sRunData.id=sResults.runid".
                       " WHERE batch=".$batchid);
  echo("<table>\n");
  while ($row=mysql_fetch_array($rundata)) {
    echo("<tr>\n");
    echo("  <td><a href=\"cellinfo.php?cellid=".$row["cellid"]."&batchid=$batchid\"><b>" . $row["cellid"] . "</b></a></td>\n");
    echo("  <td><a href=\"../peninfo.php?penid=".$row["penid"]."\">Pen</a></td>\n");
    echo("  <td>". $row["resfile"] . "</td>\n");
    echo("</tr>\n");
    

  }

  echo("</table>\n");
  
}

cellfooter();

?>
