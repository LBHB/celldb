<?php
/*** CELLDB
drugcalc.php - calculate dosages
created 8/3/2005 - SVD
***/

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";

?>

<HTML>
<HEAD>
<TITLE><?php echo("$siteinfo - Weights - $animal"); ?></TITLE>
<link rel="shortcut icon" href="favicon.ico" />
</HEAD>

<?php

echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");

$sql="SELECT * FROM gHealth WHERE weight>0 AND animal like '$animal' ORDER BY date desc LIMIT 1";
$wdata=mysql_query($sql);

if ($row=mysql_fetch_array($wdata)) {
  echo("<table border=1 width=50% border=1 cellspacing=0 cellpadding=1>\n");
  echo("<tr><td align=center colspan=5><b><font size=4><center>Animal Drug Dosage Chart</font></center></b></td></tr>");
  echo("<tr><td><b>Name</b></td>");
  echo("<td>Date</td>");
  echo("<td>Weight</td>");
  echo("<td>Water(ml)</td>");
  echo("</tr>\n");
  
  $pendate=$row["date"];
  echo("<tr><td><b> $animal </b></td>");
  echo("<td>" . $pendate . "</td>");
  echo("<td>" . round($row["weight"],0) . "</td>");
  echo("<td>" . round($row["water"],0) . "</td>");
  echo("</tr>\n");
  
  echo("</table>\n");

  echo("<br><br><br>");

/*** Table 2 ***/
 
  			echo("<table border=1 width=100% border=1 cellspacing=0 cellpadding=1>\n");
  			echo("<tr><td align=center colspan=6> <b>Surgery/pre-Isoflurane Induction</b> </td></tr>");
 		   echo("<tr><td>Drug</td><td>Dose (mg/kg)</td><td>Concentration (mg/ml)</td><td>Amount to Give (ml)</td><td>Injection Type</td><td>Notes</td></tr>");

  echo("<tr><td>Xylazine</td><td>5</td><td>20</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *5.0 /20), 2) . "</b></td>");
  echo("<td>IM</td><td>Confirm 20mg/ml Concentration</tr>");

 
  echo("<tr><td>Ketamine</td><td>30</td><td>100</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *30.0 /100.0), 2) . "</b></td>");
  echo("<td>IM</td><td>&nbsp;</td></tr>");

  echo("<tr><td>Dexamethasone</td><td>2</td><td>2</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *2 /2), 2) . "</b></td>");
  echo("<td>SC</td><td>&nbsp;</td></tr>");

  echo("<tr><td>Atropine</td><td>0.05</td><td>0.54</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *0.05 /0.54), 2) . "</b></td>");
  echo("<td>SC</td><td>&nbsp;</td></tr>");

  echo("<tr><td>Baytril</td><td>10</td><td>100</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *10 /100), 2) . "</b></td>");
  echo("<td>SC</td><td>&nbsp;</td></tr>");

  echo("<tr><td>Flunixamine</td><td>0.3</td><td>50</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *0.3 /50), 5) . "</b></td>");
  echo("<td>SC</td><td>&nbsp;</td></tr>");

  echo("<tr><td>Buprenorphine</td><td>0.02</td><td>0.3</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *0.02 /0.3), 2) . "</b></td>");
  echo("<td>SC</td><td>&nbsp;</td></tr>");
  
  echo("<tr><td>Trimethoprim/Sulfa</td><td>15-30</td><td>40</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *15 /40), 2) . "-" . round(($row["weight"] /1000 *30 /40), 2) ."</b></td>");
  echo("<td>Oral</td><td>&nbsp;</td></tr>");
  
  
  			echo("<tr><td align=center colspan=6>&nbsp </tr></td>");
  
  
  
  			echo("<tr><td align=center colspan=6><b>Acepromazine Induction</b></td></tr>");

  echo("<tr><td>Acepromazine</td><td>0.15</td><td>10</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *0.15 /10), 2) . "</b></td>");
  echo("<td>IM</td><td>&nbsp;</td></tr>");
  
  echo("<tr><td>Ketamine</td><td>30</td><td>100</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *30.0 /100.0), 2) . "</b></td>");
  echo("<td>IM</td><td>&nbsp;</td></tr>");
  
  
    
  			echo("<tr><td align=center colspan=6>&nbsp </tr></td>");
  
  
  
  			echo("<tr><td align=center colspan=6><b>Dexmedetomidine Induction</b></td></tr>");

  echo("<tr><td>Dexmedetomidine</td><td>0.04-0.06</td><td>0.5</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *0.04 /0.5), 4) . "-" . round(($row["weight"] /1000.0 *0.06 /0.5), 4) . "</b></td>");
  echo("<td>IM</td><td>&nbsp;</td></tr>");
  
  echo("<tr><td>Ketamine</td><td>5</td><td>100</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *5 /100.0), 2) . "</b></td>");
  echo("<td>IM</td><td>&nbsp;</td></tr>");
  
  echo("<tr><td>Apitamezol (Antisedan)</td><td>0.2-0.3</td><td>5.0</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *0.2 /5), 4) . "-" . round(($row["weight"] /1000.0 *0.3 /5), 4) . "</b></td>");
  echo("<td>IM</td><td>Reversal agent for Dexmedetomidine</td></tr>");
  
  
  			
  			echo("<tr><td align=center colspan=6>&nbsp </tr></td>");
  
  
  
  			echo("<tr><td align=center colspan=6><b>Diazepam Induction</b></td></tr>");

  echo("<tr><td>Diazepam</td><td>0.05</td><td>5.0</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *0.05 /5), 4) . "</b></td>");
  echo("<td>IM</td><td>Human Diazepam Concentration</td></tr>");
  
  echo("<tr><td>Ketamine</td><td>5</td><td>100</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *30.0 /100.0), 2) . "</b></td>");
  echo("<td>IM</td><td>&nbsp;</td></tr>");
  
  
  
  
 
  			echo("<tr><td align=center colspan=6><b>***OPT/EMERGENCY***</b></td></tr>");

  echo("<tr><td>Dextrose</td><td>&nbsp</td><td>0.5</td>");
  echo("<td><b>&nbsp</b></td>");
  echo("<td>SC</td><td>&nbsp;</td></tr>");

  echo("<tr><td>Dopram</td><td>5</td><td>20</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *5 /20), 2) . "</b></td>");
  echo("<td>SL</td><td>&nbsp;</td></tr>");

  echo("<tr><td>Epinephrine</td><td>0.002</td><td>1</td>");
  echo("<td><b>" . round(($row["weight"] /1000.0 *0.002 /1), 2) . "</b></td>");
  echo("<td>SC</td><td>&nbsp;</td></tr>");

  //echo("Drug quantity (dosage  100 mg/kg ) : " . ($row["weight"] /1000.0 *100) . "<br");
} else {
  echo("Error: no weights found for animal ".$animal."<br>");
}



?>
