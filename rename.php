<?php
$dir = @$_REQUEST['dir'];
$file = @$_REQUEST['file'];
$change = @$_REQUEST['change'];
$ajax = @$_REQUEST['ajax'];

include_once("config.php");
include_once("functions.php");
$reply=0;

authenticate();	//user login
if($GLOBALS['rdonly']) die("|0|Warning: Working in read-only mode!|");

if(!$dir) $dir=$homedir;
chdir($dir);

if(!file_exists($file))
	$msg="File named '$dir/$file' does not exist!";
elseif(file_exists($change)&&(strtolower($file)!=strtolower($change))) 
	$msg="File named '$change' already exists";
elseif(@rename($file, $change)) {$msg= "File '$file' renamed to '$change'"; $reply=1;}
else $msg= "Error: Rename failed!";

if($ajax)
	{expired();
	print"|$reply|$msg|";
	if($reply) filestatus($change)."|";
	}
?> 