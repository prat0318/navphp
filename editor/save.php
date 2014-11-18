<?php
#=================================
#  PHP Navigator 3.2
#  9:36 PM; August 16, 2006	
#  http://navphp.sourceforge.net
#=================================

$dir = @$_REQUEST['dir'];
$file = @$_REQUEST['file'];

include_once("../functions.php");
include_once("../config.php");

authenticate();

chdir($dir);

  if(get_magic_quotes_gpc()){
    $data = stripslashes($_POST['data']);
  } else {
    $data = $_POST['data'];
  }
  $f=fopen("$file","w");
  if(fwrite($f,$data)!==FALSE) $msg= "File $file saved!";
  else $msg  = "Error: Cannot write into the file $dir/$file ";
  fclose($f);
print  "<link href='../inc/windows.css' rel=stylesheet type=text/css>
	<link href='../inc/skin.css' rel=stylesheet type=text/css>
	<body style='background: ButtonFace; margin:0; border:none;'></body><h4>$msg</h4>";
?>
