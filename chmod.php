<?php
$dir = @$_REQUEST['dir'];
$file=@$_REQUEST['file'];
$change = @$_REQUEST['change'];
$ajax = @$_REQUEST['ajax'];

include_once("config.php");
include_once("functions.php");
$reply=0;

authenticate(); //user login
if($GLOBALS['rdonly']) die("|0|Warning: Working in read-only mode!|");

if(!$dir) $dir=$homedir;
chdir($dir);
$change_mess=$change;
$change=octdec($change);
if(!file_exists($file)) $msg="File '$file' does not exist!";
else if(chmod($file, $change)) {$msg="chmod success on $change_mess"; $reply=1;}
else $msg="Error: Chmod failed!";

if($ajax)
{
expired();
print"|$reply|$msg|";
if($reply) filestatus($file)."|";
}
?>