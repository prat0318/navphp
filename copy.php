<?php
#---------------------------
# PHP Navigator 4.42
# dated: June 1, 2011
# Coded by: Cyril Sebastian
# Modified by: Paul Wratt
# web: navphp.sourceforge.net
#---------------------------

$dir = @$_REQUEST['dir'];
$ajax=@$_REQUEST['ajax'];
$file=@$_REQUEST['file'];
$change = @$_REQUEST['change'];
$force = @$_REQUEST['force']; // <-- to be added (force overwrite)

include_once("config.php");
include_once("functions.php");

$reply = 0;

authenticate();	//user login

chdir($dir);

//$destdir = realpath(dodotpath($change,$dir));
$destdir = realpath($change);
$source = realpath($file);
$copyto = $destdir.DIRECTORY_SEPARATOR.$file;
$copyto = str_replace("\\",DIRECTORY_SEPARATOR,$copyto);

if(!file_exists($file)) $msg="Error: '$file' does not exists!";
elseif(is_dir($file)){
  if(!is_dir($destdir) && !$force) $msg="Error: Folder '$change' does not exist!";
  elseif(is_dir($copyto)) $msg="Error: Folder '$file' already exists in '$change'!";
  elseif(traverse($source,$copyto)) {$msg.=".\n Folder '$file' copied to '$change'."; $reply=0;}
  else $msg="Error: Folder '$file' could not be copied!";
}else{
  if(file_exists($copyto) && !$force) $msg="Error: File '$file' already exists in '$change'!"; 
  else if(@copy($file,$copyto)) {$msg="File '$file' copied to '$change'"; $reply = 0;}
  else $msg="Error: File copy failed!";
}

function dodotpath($change,$dir)
{
  if(is_dir(realpath($change))) return $change;

  $realdir = $dir;
  $newdir = $change;

# do parent loop until no more "../" or "./"
  $parentcount = substr_count($newdir,".".DIRECTORY_SEPARATOR);
  $parentcount = $parentcount + substr_count($newdir,DIRECTORY_SEPARATOR.".");
  if($parentcount>0){
    $dirarray = explode(DIRECTORY_SEPARATOR,$newdir);
    foreach($dirarray as $name){
      if ($name==".."){
        $realdir = substr($realdir,0,strrpos($realdir,DIRECTORY_SEPARATOR));
      }elseif ($name!='.' && $name!=''){
        $realdir = $realdir.DIRECTORY_SEPARATOR.$name;
      }
    }
  }
  return $realdir;
}

function traverse($dir,$todir)	# For recursive copying using realpath()
{
global $msg, $reply;
$l = strlen($dir); $i = 0; $j = 0; $k = 0;
if(!is_dir($todir)){
      if(!mkdir($todir)) return false;
}
try{
$copydir = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($dir), true);
foreach ($copydir as $copyfile)
 {
  $file = $copyfile->getPathname();
  $path2file = substr($file,$l);
  $i++;
  if(is_dir($file)){
    if(!is_dir("$todir$path2file")){
      if(mkdir("$todir$path2file")) $k++;
      else $j++;
    }
  }else{
      if(@copy($file,"$todir$path2file")) $k++;
      else $j++;
  }
 }
}catch (Exception $ex){
//throw $ex;
}
$msg = "$k of $i files copied";
if ($j>0) $msg .= ", $j failed";
return true;
}

if($ajax){
	expired();
	print "|$reply|$msg||";
}
?>