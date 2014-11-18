<?php
#---------------------------
# PHP Navigator 4.0
# dated: January 23, 2009
# Coded by: Cyril Sebastian,
# Kerala,India
# web: navphp.sourceforge.net
#---------------------------

session_start();
session_unset();
session_destroy();
$_SESSION['loggedin'] = 0;
		
header("Location:login.php");
?>