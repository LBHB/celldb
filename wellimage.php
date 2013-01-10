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

$sql="SELECT * FROM gPenetration WHERE id=$penid";
$songdata=mysql_query($sql);

if ($row=mysql_fetch_array($songdata)) {
  
  $coverfile=$row["wellimfile"];

  $sizepix=60;
  //$im     = imagecreate($sizepix,$sizepix);
  $im = imagecreatefromjpeg($coverfile); /* Attempt to open */

  //$background_color = imagecolorallocate($im, 255,255,255);
  //$ln_color = imagecolorallocate($im, 0,0,0);
  $text_color = imagecolorallocate($im, 255,255,255);
  
  $cc=explode("+",$row["wellposition"]);
  for ($ii=0;$ii<count($cc)-1;$ii++) {
    $xx=explode(",",$cc[$ii]);
    imagestring($im, 2, $xx[0]-imagefontheight(2)/2, 
                $xx[1]-imagefontheight(2)/2, 
		($ii+1), $text_color);
    
  }

  //imageline($im, $cx, $cy-$rmax, $cx, $cy+$rmax, $ln_color);
  //imageline($im, $cx-$rmax, $cy, $cx+$rmax, $cy, $ln_color);
  header("Content-type: image/png");
  imagepng($im);
  imagedestroy($im);
  exit();


  if (!file_exists($coverfile)) {
    $coverfile="/var/www/music/tmp/404.jpg";
  }
  
  if (1) {
    header('Content-Type: image/jpeg');
    
    readfile($coverfile);
    exit();
  }
  
  header ("Content-type: image/png");
  $im = @imagecreatetruecolor(120, 20)
    or die("Cannot Initialize new GD image stream");
  $text_color = imagecolorallocate($im, 233, 14, 91);
  imagestring($im, 1, 5, 5,  "A Simple Text String", $text_color);
  imagepng($im);
  imagedestroy($im);
  exit();

  echo($coverfile);
  $im = imagecreatefromjpeg($coverfile); /* Attempt to open */
  
  $x=imagesx($im);
  $y=imagesy($im);
  
  echo("imagesize: $x , $y");
  exit();


  // content-type depends on the file
  header('Content-Type: image/jpeg');
  imagejpeg($im);
  
  exit();
  
  echo($coverfile);
  phpinfo();
  exit();
  

  header ("Content-type: image/png");
  $im = @imagecreatetruecolor(120, 20)
    or die("Cannot Initialize new GD image stream");
  $text_color = imagecolorallocate($im, 233, 14, 91);
  imagestring($im, 1, 5, 5,  "A Simple Text String", $text_color);
  imagepng($im);
  imagedestroy($im);
  exit();

  
  header ("Content-type: image/png");
  $im = @imagecreatetruecolor(120, 20)
    or die("Cannot Initialize new GD image stream");
  $text_color = imagecolorallocate($im, 233, 14, 91);
  imagestring($im, 1, 5, 5,  "A Simple Text String", $text_color);
  imagepng($im);
  imagedestroy($im);
  exit();

  //if (!$im) { /* See if it failed */
  if (0) { /* See if it failed */
    $im  = @imagecreatetruecolor(150, 30); /* Create a black image */
    $bgc = imagecolorallocate($im, 255, 255, 255);
    $tc  = imagecolorallocate($im, 0, 0, 0);
    imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
    /* Output an errmsg */
    imagestring($im, 1, 5, 5, $coverfile, $tc);
  }
  

  // generate a filename
  //header('Content-Disposition: attachment; filename="' . 
  //       $row["artist"] . '-' . $row["album"] . '.jpg"');
  
  // load the file and send binary data out to client
  //readfile($coverfile);
    
} else {
  $coverfile="/var/www/celldb/images/black.jpg";
  header('Content-Type: image/jpeg');
  
  readfile($coverfile);
  exit();
}

