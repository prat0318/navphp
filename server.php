<?php
#---------------------------
# PHP Navigator 4.44
# dated: December 10, 2011
# Coded by: Cyril Sebastian,
# Kerala,India
# Updated by: Paul Wratt
# web: navphp.sourceforge.net
#---------------------------
?><head>
<link href='inc/windows.css' rel=stylesheet type=text/css>
</head>
<body><center>
<table width=70% cellspacing=0 cellpadding=4>
<tr><td class=head>
<b>Server Information </td><td class=head> - PHP Navigator <font color=orange><i>xp</i></font></b>
</td></tr>
<tr><td class=info>
<?php 
print "<br>Server Name: ";
print "<br><br>Operating System: ";
print "<br><br>Processor: ";
print "<br><br>Server Software: ";
print "<br><br>Server Protocol: ";
print "<br><br>Server Port: ";
print "<br><br>Server Host: ";
print "<br><br>PHP Version: ";
print "<br><br>Document Root: ";
print "<br><br>Script Filename: ";
print "<br>";
?>
</td><td class=info>
<?php
print "<b><br>".$_SERVER['SERVER_NAME'];
print "<br><br>".$_ENV['OS'];
print "<br><br>".$_ENV['PROCESSOR_IDENTIFIER'];
print "<br><br>".$_SERVER['SERVER_SOFTWARE'];
print "<br><br>".$_SERVER['SERVER_PROTOCOL'];
print "<br><br>".$_SERVER['SERVER_PORT'];
print "<br><br>".$_SERVER['HTTP_HOST'];
print "<br><br>".phpversion();
print "<br><br>".$_SERVER['DOCUMENT_ROOT'];
print "<br><br>".$_SERVER['SCRIPT_FILENAME'];
print "<br></b>";
?>
</td></tr>
</table>
</body>