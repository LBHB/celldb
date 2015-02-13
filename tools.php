<?php
/*** CELLDB
tools.php - display information about a single penetration
created 2013 - SVD
***/

// global include: connect to db and get important basic info about user prefs
$min_sec_level=2;
include_once "./celldb.php";
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
  Center frequency (Hz): <INPUT TYPE=text NAME="f0" value="<?php echo($f0); ?>"><br>
  Bandwidth (oct): <INPUT TYPE=text NAME="bw" value="<?php echo($bw); ?>"><br>
<INPUT TYPE=SUBMIT VALUE="GO">
</FORM>
</td>
  <td>&nbsp;&nbsp;</td>
<td>
<?php 
  if ($f0>0 && $bw>0){
    $flo=round($f0/pow(2,($bw/2)),1);
    $fhi=round($f0*pow(2,($bw/2)),1);
    echo("For f0=$f0 and bw=$bw:<br>Flo=<b>$flo</b><br>Fhi=<b>$fhi</b><br><br>\n");
  }
?>
</td></tr>
<tr><td>
<b>Derivation:</b><br>
if bw=log2(Fhi-Flo), then:<br>
Flo = f0 * 2^(-bw/2)<br>
Fhi = f0 * 2^(bw/2)
</td></tr>
</td>
  <td>&nbsp;&nbsp;</td>
<td>

<tr><td>
<FORM ACTION="tools.php" METHOD=get>
    <b>Calculate Electrode Position</b><br>
    Tilt (&deg): <INPUT TYPE=text NAME="phi1" value="<?php echo($phi1); ?>">
                 <INPUT TYPE=text NAME="phi2" value="<?php echo($phi2); ?>"> <br>
    Rotation (&deg): <INPUT TYPE=text NAME="theta1" value="<?php echo($theta1); ?>">
                     <INPUT TYPE=text NAME="theta2" value="<?php echo($theta2); ?>"><br>
    Distance: <INPUT TYPE=text NAME="r" value="<?php echo($r); ?>"><br>
<INPUT TYPE=SUBMIT VALUE="GO">
</FORM>
</td>
    <td>&nbsp;&nbsp;</td>
<td>
<?php 
if ($phi1!=NULL && $theta1!=NULL && $r!=NULL) {
    //convert to radians
    $phrad1=$phi1/180*pi();
    $thrad1=$theta1/180*pi();
    //calculate electrode position
    $m1=round($r*sin($phrad1)*cos($thrad1));
    $a1=round($r*sin($phrad1)*sin($thrad1));
    echo("For phi=$phi1, theta=$theta1, r=$r:
            <br>ML Offset 1 = <b>$m1</b>
            <br>AP Offset 1 = <b>$a1</b>");
        if ($phi2!=NULL && $theta2!=NULL) {
            //convert to radians
            $phrad2=$phi2/180*pi();
            $thrad2=$theta2/180*pi();
            //calculate electrode position
            $m2=round($r*sin($phrad2)*cos($thrad2));
            $a2=round($r*sin($phrad2)*sin($thrad2));
            //calculate difference
            $dm=round($m2-$m1);
            $da=round($a2-$a1);
            echo("<br>For phi=$phi2, theta=$theta2, r=$r:
            <br>ML Offset 2 = <b>$m2</b>
            <br>AP Offset 2 = <b>$a2</b>
            <br>ML Offset Difference = <b>$dm</b>
            <br>AP Offset Difference = <b>$da</b>");
        }
}
?>
</td></tr>
<tr><td>
<b>Derivation:</b><br>
ML Offset = Distance * Sin(Tilt) * Cos(Rotation)<br>
AP Offset = Distance * Sin(Tilt) * Sin(Rotation)<br>
</tr></td>

</table>
</body>
</html>
   
