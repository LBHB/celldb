<?php

// Note: requires php5-gd

function LoadJpeg($imgname)
{
    $im = @imagecreatefromjpeg($imgname); /* Attempt to open */
    if (!$im) { /* See if it failed */
        $im  = imagecreatetruecolor(150, 30); /* Create a black image */
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
        /* Output an errmsg */
        imagestring($im, 1, 5, 5, "Error loading $imgname", $tc);
    }
    return $im;
}

// global include: connect to db and get important basic info about user prefs
$min_sec_level=6;
include_once "./celldb.php";
if (!isset($showdata) || !isset($siteid)) {
  $showdata="location";
}
if ("location"==$showdata && !isset($siteid)) {
  $sql="SELECT * FROM gPenetration WHERE id=$penid";
} else {
  $sql="SELECT gPenetration.*,bf,area,depth".
    " FROM gPenetration INNER JOIN gCellMaster".
    " ON gPenetration.id=gCellMaster.penid".
    " WHERE gCellMaster.siteid='$siteid'";
}

$pendata=mysql_query($sql);

if ($row=mysql_fetch_array($pendata)) {
  
  $coverfile=$row["wellimfile"];
  
  $sizepix=60;
  //$im     = imagecreate($sizepix,$sizepix);
  $im = imagecreatefromjpeg($coverfile); /* Attempt to open */
  //$background_color = imagecolorallocate($im, 255,255,255);
  //$ln_color = imagecolorallocate($im, 0,0,0);
  $text_color = imagecolorallocate($im, 255,255,255);
  //imagestring($im, 2, 0,0, 
  //            $coverfile, $text_color);
  
  $cc=explode("+",$row["wellposition"]);
  $bf=explode(",",$row["bf"]);
  $area=explode(",",$row["area"]);
  $depth=explode(",",$row["depth"]);
  for ($ii=0;$ii<count($cc)-1;$ii++) {
    if ("location"==$showdata) {
      $str=($ii+1);
    } elseif ("bf"==$showdata) {
      $str=$bf[$ii];
    } elseif ("depth"==$showdata) {
      $str=$depth[$ii];
    } elseif ("area"==$showdata) {
      $str=$area[$ii];
    }
    $str="$str";
    $xx=explode(",",$cc[$ii]);
    imagestring($im, 2, $xx[0]-imagefontheight(2)/2, 
                $xx[1]-imagefontheight(2)/2, 
		$str, $text_color);
    
  }

  //imageline($im, $cx, $cy-$rmax, $cx, $cy+$rmax, $ln_color);
  //imageline($im, $cx-$rmax, $cy, $cx+$rmax, $cy, $ln_color);
  header("Content-type: image/png");
  imagepng($im);
  imagedestroy($im);
  
} else {
  $coverfile="/var/www/celldb/images/black.jpg";
  header('Content-Type: image/jpeg');
  
  readfile($coverfile);
}

