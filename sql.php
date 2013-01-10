<?php
 /*** CELLDB
sql.php - illustrate some basic programming structures in celldb

created 2007-07-12 - SVD
***/

// global include: connect to db and get important basic info about 
// the user who's currently logged in
include_once "./celldb.php";

?>
<HTML>
<HEAD>
<TITLE><?php echo($siteinfo)?> - Demo page</TITLE>
</HEAD>
<BODY bgcolor="#FFFFFF">
<?php

// display header bar at the top of the page (cellheader
// function defined in celldb.php)
cellheader();

// file paths/names are stored in linux format in the db. flag to convert
$windowsfmt=1;
$excludetest=1;  // exclude "test" animal from searches

// define the form for choosing search options
// calls this page using html GET so that search parameters
// are encoded in the URL
echo("<FORM ACTION=\"sql.php\" METHOD=GET>\n");

$sql=stripslashes($sql);

echo("SQL: <textarea NAME=\"sql\" rows=4 cols=60>$sql</textarea>");
echo("<INPUT TYPE=SUBMIT VALUE=\"Go\">");
echo("</FORM><br>");

if (""<>$sql && strtoupper(substr($sql,0,6))=="SELECT") {
  $sqldata=mysql_query($sql);
  
  if (!$sqldata) {
    echo 'Could not run query: ' . mysql_error();
    exit;
  } else {
    
    
    echo("<table>");
    echo("<tr>\n");
    for ($ii=0; $ii<mysql_num_fields($sqldata); $ii++) {
      echo("<td><b>" . mysql_field_name($sqldata, $ii) . "</b></td>");
    }
    echo("</tr>\n");
    
    // loop through each row returned by the search
    while ( $row = mysql_fetch_array($sqldata) ) {
      
      echo("<tr>");
      for ($ii=0; $ii<mysql_num_fields($sqldata); $ii++) {
        echo("<td>" . $row[$ii] . "</td>");
      }
      echo("<tr>\n");
    }
    echo("</table>\n");
  }
}

echo("<p>sql: $sql</p>\n");

// display page footer
cellfooter();

?>

</BODY>
</HTML>
