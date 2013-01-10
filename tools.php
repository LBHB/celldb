<?php
/*** CELLDB
peninfo.php - display information about a single pentration
created 2002 - SVD
***/

// global include: connect to db and get important basic info about user prefs
$min_sec_level=2;
include_once "./celldb.php";

function quadratic($a, $b, $c, $root){
  $precision = 1; // Change this value for a different decimal places rounding.
  
  $bsmfac = $b*$b-4*$a*$c;
  if ($bsmfac < 0) { // Accounts for complex roots.
    $plusminusone = " + "; $plusminustwo = " - ";
    $bsmfac *=-1;
    $complex=(sqrt($bsmfac)/(2*$a));
    if ($a < 0){ //if negative imaginary term, tidies appearance.
      $plusminustwo = " + ";
      $plusminusone = " - ";
      $complex *= -1;
    } // End if ($a < 0)
    $lambdaone = round(-$b/(2*$a), $precision).$plusminusone.round($complex, $precision).'i';
    $lambdatwo = round(-$b/(2*$a), $precision).$plusminustwo.round($complex, $precision).'i';
  } // End if ($bsmfac < 0)
  
  else if ($bsmfac == 0) { // Simplifies if b^2 = 4ac (real roots).
    $lambdaone = round(-$b/(2*$a), $precision);
    $lambdatwo = round(-$b/(2*$a), $precision);
  } // End else if (bsmfac == 0)
  
  else { // Finds real roots when b^2 != 4ac.
    $lambdaone = (-$b+sqrt($bsmfac))/(2*$a);
    $lambdaone = round($lambdaone, $precision);
    $lambdatwo = (-$b-sqrt($bsmfac))/(2*$a);
    $lambdatwo = round($lambdatwo, $precision);
  } // End else
  
  // Return what is asked for.
  if ($root == 'root1') {return $lambdaone;}
  if ($root == 'root2') {return $lambdatwo;}
  if ($root == 'both') {return $lambdaone. ' and ' .$lambdatwo;}
}



?>
<HTML>
<HEAD>
<TITLE>celldb - Tools</TITLE>
</HEAD>

<?php
echo("<BODY bgcolor=\"$userbg\" text=\"$userfg\"" .
     " link=\"$linkfg\" vlink=\"$vlinkfg\" alink=\"$alinkfg\">");
cellheader();

?>

<table border=0 cellpadding=1 cellspacing=1>
<tr><td>
<FORM ACTION="tools.php" METHOD=get>
  <b>Band Calculator</b><br>
  Center frequency (Hz): <INPUT TYPE=text NAME="f0" value="<? echo($f0); ?>"><br>
  Bandwidth (oct): <INPUT TYPE=text NAME="bw" value="<? echo($bw); ?>"><br>
<INPUT TYPE=SUBMIT VALUE="GO">
</FORM>
</td>
  <td>&nbsp;&nbsp;</td>
<td>
<? 
  if ($f0>0 && $bw>0){
    //$fhi=quadratic(1,-$f0*($bw)*1.5/2,-$f0*$f0,'root1');
    //$flo=quadratic(1,$f0*($bw)*1.5/2,-$f0*$f0,'root1');
    //$fhi=quadratic(1,-$f0*($bw-pow(2,-$bw/2)),-$f0*$f0,'root1');
    //$flo=quadratic(1,$f0*($bw-pow(2,-$bw/2)),-$f0*$f0,'root1');
    $flo=round($f0/pow(2,($bw/2)),1);
    $fhi=round($f0*pow(2,($bw/2)),1);
    
    echo("For f0=$f0 and bw=$bw:<br>Flo=<b>$flo</b><br>Fhi=<b>$fhi</b><br><br>\n");
    
  }
?>
</td>
</tr>
<tr><td>
<b>Derivation:</b><br>
if bw=log2(Fhi-Flo), then:<br>
Flo = f0 * 2^(-bw/2)<br>
Fhi = f0 * 2^(bw/2)
</td>
</tr>
</table>
</body>
</html>
   
