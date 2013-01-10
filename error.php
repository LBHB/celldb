<?php
/*** CELLDB
error.php - display fatal error and sit there.

created 2005-10-10 - SVD

***/

?>
<HTML>
<HEAD>
<TITLE>CellDB Error</TITLE>
</HEAD>
<body bgcolor="#FFFFFF">

<?php

//automatically parse html posted variables (i think?)
import_request_variables("GP", "");

echo("<p>celldb problem:</p>");

if (""!=$errormsg) {
  echo("<p><b><font color=\"#CC0000\">$errormsg</font></b></p>\n");
}

if (""!=$refurl) {
  echo("<p><a href=\"$refurl\">Go back</a></p>\n");
}
?>

</BODY>
</HTML>
