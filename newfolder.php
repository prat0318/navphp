<?php
$dir = @$_REQUEST['dir'];
$ajax=@$_REQUEST['ajax'];
$file=@$_REQUEST['file'];
$change = @$_REQUEST['change'];

include_once("config.php");
include_once("functions.php");
$reply=0;

authenticate();	//user login
if($GLOBALS['rdonly']) die("|0|Warning: Working in read-only mode!|");
chdir($dir);

if(file_exists($change)) $msg="Folder or file '$change' already exists!";
  elseif(mkdir($change,0777)) {$msg="New folder '$change' created"; $reply=1;}
  else $msg="Error: Can't create new folder!";

if($ajax)
	{expired();
	print"|$reply|$msg|";
	if($reply) filestatus($change)."|";
	}
?> 