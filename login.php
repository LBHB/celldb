<HTML>
<HEAD>
<TITLE>CellDB Login</TITLE>
</HEAD>
<body bgcolor="#FFFFFF">
<P>Welcome to CellDB. (Date:
<?php
echo( date("l, F dS Y") );
?>
)</p>
<p>
<FORM ACTION="celllist.php" METHOD=POST>
User ID: <INPUT TYPE=TEXT NAME="userid">
Password: <INPUT TYPE=PASSWORD NAME="passwd">
<INPUT TYPE=SUBMIT VALUE="GO">
</FORM>
</p>
</BODY>
</HTML>
