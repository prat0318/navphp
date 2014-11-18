<?php
$dir = @$_REQUEST['dir'];
$ajax=@$_REQUEST['ajax'];
$file=@$_REQUEST['file'];
$change = @$_REQUEST['change'];
$action = @$_REQUEST['action'];

include_once("config.php");
include_once("functions.php");
include_once("lib/pclzip.class.php");

$reply=0;

authenticate();	//user login
if($GLOBALS['rdonly']) die("|0|Warning: Working in read-only mode!|");
if($ajax) $refresh=" Refresh to view them. ";
if(!$dir) $dir=$homedir;
chdir($dir);

if(is_file($file)) //for zip extraction
 {
 $path_parts = pathinfo($file);
 if(eregi(".zip",$file))
  {
  $zip=new PclZip($file);
  $list = $zip->extract(".");
  if($list>0) 
   {
   $msg= count($list)." Files were extracted. $refresh";
   $reply=1;
   }
  else
    $msg= "Error: Unexpected error during extraction!";
  }
 else $msg="Error: '$file' is not a zip file!"; 
 }
else $msg="Error: File '$file' does not exists!";

if($ajax)
	{expired();
	print"|0|$msg|";
	}
?> 