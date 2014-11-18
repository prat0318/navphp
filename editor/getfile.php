<?php
#=================================
#  PHP Navigator 3.2
#  9:36 PM; August 16, 2006	
#  http://navphp.sourceforge.net
#=================================

if($compress) ob_start("ob_gzhandler");

$dir = @$_REQUEST['dir'];
$file = @$_REQUEST['file'];

include_once("../functions.php");
include_once("../config.php");
authenticate();

if(is_file("$dir/$file")) echo file_get_contents("$dir/$file");
else print "<h3>File $dir_relative/$file not found!</h3>";

?>
