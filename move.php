<?php
#--------------------------------
# PHP Navigator 4.12
# dated: 20-07-2007
#--------------------------------
# PHP Navigator 4.42
# dated: 01-06-2011
# edited: 01-06-2011
# Created by: Paul Wratt,
# Homeless,Melbourne,Australia
# web: phpnav.isource.net.nz
#--------------------------------

$dir = @$_REQUEST['dir'];
$ajax= @$_REQUEST['ajax'];
$file= @$_REQUEST['file'];
$change = @$_REQUEST['change'];
$force = @$_REQUEST['force']; // <-- to be added (force overwrite)

include_once("config.php");
include_once("functions.php");

$reply = 0;

authenticate();	//user login

chdir($dir);

//$destdir = realpath(dodotpath($changeto,$dir));
$destdir = realpath($change);
$source = realpath($file);
$copyto = $destdir.DIRECTORY_SEPARATOR.$file;
$copyto = str_replace("\\",DIRECTORY_SEPARATOR,$copyto);
$copyto = str_replace("//",DIRECTORY_SEPARATOR,$copyto);


if(!file_exists($file)) $msg="Error: '$file' does not exists!";
elseif(is_dir($file)){
  $c = $f = $g = $h = $i = $j = $k = 0;
  if(!is_dir($destdir) && !$force) $msg="Error: Folder '$changeto' does not exist!";
  elseif(is_dir($copyto)) $msg="Error: Folder '$file' already exists in '$changeto'!";
  elseif(traverse($source,$copyto)) {$reply=1;}
  else $msg="Error: Folder '$file' could not be created!";
  if($reply){
    $msg = "$c of $k files in $h folders";
    if($g==0 && $f==0 && $j==0){
      $h = $i = $j = $k = 0;
      if(recurse($source)){ $msg .= " moved<br>Folder '$file' moved to '$changeto'."; }
      else{ $msg .= " copied, <br>Error: Can't delete folder: $file"; $reply = 3; }
    }else{
      if ($f>0){ $tmsg = ", $f copied files not deleted"; $reply = 3; }
      if ($j>0){ $tmsg .= ", $j files failed to copy"; $reply = 3; }
      $msg .= " copied".$tmsg;
    }
  }
}else{
  if(file_exists($copyto) && !$force) $msg = "Error: File '$file' already exists in '$changeto'!"; 
  else if(@copy($file,$copyto)) {$msg = "File '$file' copied to '$changeto'"; $reply = 1;}
  else $msg = "Error: File move failed!";
  if($reply){
    if(unlink($file)) {$msg = "File '$file' moved to '$changeto'"; $reply=1;}
    else {$msg .= "Error: Can't delete file: $file"; $reply=3;}
  }
}

function dodotpath($changeto,$dir)
{
  if(is_dir(realpath($changeto))) return $changeto;

  $realdir = $dir;
  $newdir = $changeto;

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

function traverse($dir,$todir){	# For recursive moving using realpath()
global $msg, $reply, $c, $f, $g, $h, $i, $j, $k;
  $l = strlen($dir);
  if(!is_dir($todir)){
    if(!mkdir($todir)) return false;
  }
try{
  $copydir = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($dir), true);
  foreach ($copydir as $copyfile){
    $file = $copyfile->getPathname();
    $path2file = substr($file,$l);
    $i++;
    if(is_dir($file)){
      if(!is_dir("$todir$path2file")){
        if(@mkdir("$todir$path2file")) $h++;
        else $g++;
      }
    }else{
      if(@copy($file,"$todir$path2file")) {
        $k++;
        if(@unlink($file)) $c++;
        else $f++;
      }else $j++;
    }
  }
}catch (Exception $ex){
//throw $ex;
}
  return true;
}

function recurse($dir){	# For recursive directory deleting
  global $msg, $reply, $h, $i, $j, $k;
  if($dh = opendir($dir)){
    $i++;
    while (($file = readdir($dh))) {$files[] = $file;}
    foreach($files as $file){
      if($file!="."&&$file!=".."&&!is_dir("$dir/$file")){
        $h++;
      }
    }
    foreach($files as $file){
      if($file!="."&&$file!=".."&&is_dir("$dir/$file")){
        recurse("$dir/$file");
      }
    }
    closedir($dh);
    $i++;
    if(@rmdir("$dir")) {
      $k++;
    }else{
      $j++;
    }
    return true;
  }else{
    return false;
  }
}


if($ajax){
	expired();
	print "|$reply|$msg||";
}
?>