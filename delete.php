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

if(!$dir) $dir=$homedir;
chdir($dir);

if(!file_exists($file)) $msg="Error: File '$file' does not exist!";
else if(is_dir($file)) traverse("$dir/$file");
else if(unlink($file)) {$msg= "File: '$file' deleted succesfully"; $reply=1;}
else $msg="Error: Can't delete file: $file";

function traverse($dir)	# For recursive deleting
{
global $msg, $reply;

if($dh = opendir($dir)) 
  {
  while (($file = readdir($dh)))  {$files[] = $file;}
   foreach($files as $file)
   {
	if($file!="."&&$file!=".."&&!is_dir("$dir/$file"))
    {
     if(@unlink("$dir/$file")) {$msg= "File: '$file' deleted succesfully"; $reply=1;}
     else  { $msg="Error: Can't delete file $file"; $reply=0; return 0;}  	 
    }
   }
  foreach($files as $file)
   {
   if($file!="."&&$file!=".."&&is_dir("$dir/$file"))
    {
    traverse("$dir/$file");
    }
   }
 closedir($dh);
 if(rmdir("$dir")) {$msg= "Folder: '$dir' deleted"; $reply=1;}
 else { $msg="Error: Can't delete folder: $dir"; $reply=0; return 0;}
 
  }
}

if($ajax)
	{expired();
	print"|$reply|$msg||";}
?> 