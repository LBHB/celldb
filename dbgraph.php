<?php

// global include: connect to db and get important basic info about user prefs
include_once "./celldb.php";

/***
  Class Name : svdChar Class
  Ripped off somewhat from : easyChart Class
  Create Date: Oct 4, 2004
  ***/
  class svdChart  {
    var $title;
    var $xlabel;
    var $ylabel;
    var $legend;
    var $width;
    var $height;
    var $bartitle;
    var $barheight;
    var $barwidth;
    var $background;
    var $multiply;
    var $spacebar;
    var $leftspace;
    var $rightspace;
    var $upspace;
    var $bottomspace;
    var $leftlegend;
    var $linecolor;
    var $barcolor;
    var $titlecolor;
    var $legendcolor;
    var $barinfocolor;
    function svdChart () {
      $this->title = "Chart Title";
      $this->xlabel = "X-axis";
      $this->ylabel = "Y-axis";
      $this->legend[0] = "Category 1";
      $this->background   = Array(153,204,102);
      $this->linecolor    = Array(240,240,240);
      $this->barcolor     = Array(255,255,153);
      $this->titlecolor   = Array(111,166,55);
      $this->legendcolor  = Array(0,0,0);
      $this->barinfocolor = Array(0,0,0);
      $this->width        = 600;
      $this->height       = 500;
      $this->barwidth     = 20;
      $this->leftspace    = 75;
      $this->rightspace   = 25;
      $this->upspace      = 50;
      $this->bottomspace  = 100;
      $this->leftlegend   = 5;
      $this->chartlegend  = "UG93ZXJlZCBieTogSGVyb";
      $this->barstacked   = 0;
    }
    function setTitleColor ($color) {
      $this->titlecolor = explode(",",$color);
    }
    function setLegendColor ($color) {
      $this->legendcolor = explode(",",$color);
    }
    function setBarInfoColor ($color) {
      $this->barinfocolor = explode(",",$color);
    }
    function setBarColor ($color) {
      $this->barcolor = explode(",",$color);
    }
    function setLineColor ($color) {
      $this->linecolor = explode(",",$color);
    }
    function setLeftLegend ($leftlegend) {
      $this->leftlegend = $leftlegend;
    }
    function setLeftSpace ($leftspace) {
      $this->leftspace = $leftspace;
    }
    function setRightSpace ($rightspace) {
      $this->rightspace = $rightspace;
    }
    function setTitle ($title) {
      $this->title = $title;
    }
    function setLegend ($legend,$barcat) {
      $this->legend[$barcat]= $legend;
    }
    function setXlabel ($title) {
      $this->xlabel = $title;
    }
    function setYlabel ($title) {
      $this->ylabel = $title;
    }
    function setBackground ($color) {
      $this->background = explode (",", $color);
    }
    function setWidth ($width) {
      $this->width = $width;
    }
    function setHeight ($height) {
      $this->height = $height;
    }
    function setBarWidth ($barwidth) {
      $this->barwidth = $barwidth;
    }
    function setBarStacked ($barstacked) {
      $this->barstacked = $barstacked;
    }
    function addBar ($bartitle, $barheight, $barcat="") {
      if (""==$barcat) {
        $barcat=0;
      }
      $this->barheight[$barcat][] = $barheight;
      $this->bartitle[count($this->barheight[$barcat])-1] = $bartitle;
    }
    function prepare() {
      $this->chartlegend .= "WF3YW4gSGFyeWFudG8gZWFzeUNoYXJ0LmNs";
      
      for($gr=0;$gr<count($this->barheight);$gr++){
        $arr = $this->barheight[$gr];
        sort  ($arr);
        reset ($arr);
        if ($gr==0 || end($arr) > $this->highestvalue) {
          $this->highestvalue = end($arr);
        }
      }
      $this->valueincrement = $this->highestvalue / ($this->leftlegend+1);
      if ($this->highestvalue>0) {
        $this->multiply = ( ($this->height-($this->upspace + $this->bottomspace)) / $this->highestvalue);
      } else {
        $this->multiply=0;
      }
      $this->spacebar = ( ($this->width - ($this->leftspace + $this->rightspace)) - ($this->barwidth * count($arr) ) ) / (count($arr)+1);
      $this->legendspace = ($this->height - ($this->upspace+$this->bottomspace)) / $this->leftlegend;
      if ($this->barwidth * count($this->barheight) > ($this->width-($this->upspace+$this->bottomspace))) $err = "Bar width is to large";
      if ($err!="") {
        print $err;
        exit;
      }
    }
    
    function generateChart() {
      $im = ImageCreate($this->width,$this->height);
      //$white = ImageColorAllocate($im,255,255,255);
      $white = ImageColorAllocate($im,0,0,0);
      $background = ImageColorAllocate($im,$this->background[0],$this->background[1],$this->background[2]);
      $black = ImageColorAllocate($im,0,0,0);
      $gray = ImageColorAllocate($im,100,100,100);
      $linecolor = ImageColorAllocate($im,$this->linecolor[0],$this->linecolor[1],$this->linecolor[2]);
      $barcolor = ImageColorAllocate($im,$this->barcolor[0],$this->barcolor[1],$this->barcolor[2]);
      $titlecolor = ImageColorAllocate($im,$this->titlecolor[0],$this->titlecolor[1],$this->titlecolor[2]);
      $legendcolor = ImageColorAllocate($im,$this->legendcolor[0],$this->legendcolor[1],$this->legendcolor[2]);
      $barinfocolor = ImageColorAllocate($im,$this->barinfocolor[0],$this->barinfocolor[1],$this->barinfocolor[2]);
      @ImageFilledRectangle($im,0,25,$this->width,$this->height-15,$background);
      @ImageFilledRectangle($im,1,26,$this->width-2,$this->height-16,$white);
      @ImageFilledRectangle($im,2,27,$this->width-3,$this->height-17,$background);
      @ImageFilledRectangle($im,$this->leftspace,$this->upspace-10,$this->width-$this->rightspace,$this->height-($this->bottomspace-20),$white);
      $titlewidth = strlen($this->title) * ImageFontWidth(5);
      $titlexpos = ($this->width - $titlewidth)/2;
      @ImageString ($im, 5, $titlexpos, 5, $this->title, $titlecolor);
      @ImageString ($im, 1, 0, $this->height - 10, base64_decode($this->chartlegend."YXNzIC0gaHR0cDovL2hlcm1hd2FuLmRtb25zdGVyLmNvbSAtIEdQTA=="), $black);
      for($i=0;$i<=$this->leftlegend;$i++){
        $legendx = 5;
        $legendy = $this->upspace + ($this->legendspace * $i) - 6;
        @ImageString($im, 2, $legendx, $legendy, $this->highestvalue-($this->valueincrement*$i), $legendcolor);
        @ImageLine ($im, $this->leftspace, $legendy+6, $this->width - $this->rightspace, $legendy+6, $linecolor);
      }
      for($i=0;$i<count($this->barheight);$i++){
        $j=$i+1;
        $x1 = ($j * $this->spacebar) + ($i * $this->barwidth) + $this->leftspace;
        $y1 = $this->height - ($this->barheight[$i]*$this->multiply) - $this->upspace;
        $x2 = $x1 + $this->barwidth;
        $y2 = $this->height - $this->bottomspace;
        $bartitlewidth = strlen($this->bartitle[$i]) * ImageFontWidth(2);
        $centerofbar = $x1 + ($this->barwidth / 2);
        $bartitlex = $centerofbar - ($bartitlewidth/2);
        $bartitley = $y2 + 5;
        @ImageFilledRectangle($im, $x1, $y1, $x2, $y2, $barcolor);
        @ImageString($im, 2, $bartitlex, $bartitley, $this->bartitle[$i], $barinfocolor);
      }
      @ImageJPEG($im);
      @ImageDestroy($im);
    }
    
    
    function generateChartHtml() {
      
      $maxbarheight=($this->height-($this->upspace + $this->bottomspace));

      if ($this->width > 0) {
        echo("<table cellspacing=0 border=1 width=" . $this->width . ">\n");
      } else {
        echo("<table>\n");
      }
      
      echo("<tr><td colspan=".(count($this->bartitle)+1)." align=center>\n");
      echo($this->title . "</td></tr>\n");
      echo("<tr>\n");
      
      $fn=array("images/blue.jpg","images/red.jpg","images/green.jpg",
                "images/black.jpg",
                "images/orange.jpg","images/purple.jpg","limages/blue.jpg",
                "images/blue.jpg","images/green.jpg","images/red.jpg",
                "images/black.jpg",
                "images/orange.jpg","images/purple.jpg","images/lblue.jpg");
      
      echo("<td valign=top align=right height=\"".round($maxbarheight/($this->leftlegend+1))."\">\n");
      echo(round($this->highestvalue,2) . "</td>\n");
      
      for($i=0;$i<count($this->barheight[0]);$i++){
        echo("<td align=center valign=bottom rowspan=". 
             ($this->leftlegend+1) .">\n");
        echo("<img border=0 src=\"images/white.jpg\"" .
             " width=". $this->barwidth . " height=1><br>");
        for($gr=0;$gr<count($this->barheight);$gr++){
          if ($this->barheight[$gr][$i]>0) {
            echo("<img border=0 src=\"" . $fn[$gr] . "\"" .
                 " width=". $this->barwidth . " height=" . 
                 round($this->barheight[$gr][$i]*$this->multiply+1) . ">");
            if ($this->barstacked && $gr<count($this->barheight)-1) {
              echo("<br>");
            }
          }
        }
        
        echo("</td>\n");
      }
      echo("</tr>\n");
      
      // rest of number axis
      for($i=1;$i<=$this->leftlegend;$i++){
        echo("<tr><td valign=top align=right height=\"".round($maxbarheight/($this->leftlegend+1))."\">\n");
        echo(round($this->highestvalue-($this->valueincrement*$i),2) . "</td></tr>\n");
      }
      
      // label bottom of graph
      echo("<tr><td></td>\n");
      for($i=0;$i<count($this->bartitle);$i++){
        echo("<td align=center>\n");
        
        echo($this->bartitle[$i]);
        echo("</td>\n");
      }
      echo("</tr>\n");
      echo("<tr><td></td><td colspan=". (count($this->barheight[0])) . 
           " align=center>\n");
      echo($this->xlabel . "</td></tr>\n");
      echo("<tr>\n");
      
      echo("<tr><td></td><td colspan=". (count($this->barheight[0])) . 
           " valign=center align=center>\n");
      $bw=10; // $this->barwidth
      for($gr=0;$gr<count($this->barheight);$gr++){
        echo("<img border=0 src=\"" . $fn[$gr] . "\"" .
             " width=$bw height=$bw>");
        echo("&nbsp;" . $this->legend[$gr] . "&nbsp;");
      }
      echo("</td></tr>\n");
      echo("<tr>\n");
      
      echo("</table>\n");
    }
  };

function testchart() {
  // HERE IS THE EXAMPLE TO USE THE CHART CLASS
  $chart = new easyChart();                        // Object initialized
  $chart->setTitle("Requests per month");  // Title of Chart
  $chart->setBackground("0,0,0");            // Background of chart  [opt]
  $chart->setLineColor("0,0,0");             // Line color separator [opt]
  $chart->setBarColor("0,0,255");              // Bar color  [opt]
  $chart->setTitleColor("0,0,0");             // Color Title   [opt]
  $chart->setLegendColor("255,255,255");     // Legend Color [opt]
  $chart->setBarInfoColor("255,255,255");   // Bar Info Color       [opt]
  $chart->setWidth(600);                           // Width of Chart
  $chart->setHeight(500);                          // Height of Chart
  $chart->setBarWidth(20);                         // Bar Width
  $chart->setLeftSpace(75);                        // Left Space
  $chart->setRightSpace(25);                       // Right Space
  $chart->setLeftLegend(10);                       // Left Legend

$sql="SELECT count(id) as rcount,month(startdate) as mon,".
   " min(startdate) as mindate" .
   " FROM dPlaylist" .
   " WHERE startdate + INTERVAL 1 YEAR > now()".
   " AND user<>\"xmms\"".
   " GROUP BY month(startdate)" .
   " ORDER BY mindate";
//   " AND user<>\"xmms\"".
$reqdata=mysql_query($sql);

while ( $row = mysql_fetch_array($reqdata) ) {
  $chart->addBar($row["mon"],$row["rcount"]);
}

$chart->prepare();                               // Chart Preparation
$chart->generateChartHtml();                         // Chart Generation

}
?> 
