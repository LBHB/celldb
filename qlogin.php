<HTML>
<HEAD>
<TITLE>dbQueuemon Login</TITLE>
</HEAD>
<body bgcolor="#FFFFFF">
<P>Welcome to the NSL queue monitor. (Date:
<?php
echo( date("l, F dS Y") );
?>
)</p>
<p>
<FORM ACTION="queuemonitor.php" METHOD=GET>
User ID: <INPUT TYPE=TEXT NAME="userid">
Password: <INPUT TYPE=PASSWORD NAME="passwd">
<INPUT TYPE=SUBMIT VALUE="GO">
</FORM>
</p>
</BODY>
</HTML>
