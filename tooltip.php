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

//if(!$dir) $dir=$homedir;
authenticate();	//user login
chdir($dir);

#----for zip tooltip-------

if($action=="zipinfo")  
 {
 if(is_file($file))
	{
	$zip=new PclZip($file);
	$info=$zip->properties();
	$files=$zip->listContent();
	expired(); //header
	$msg="<img src=images/zip.gif width=16 height=16> <b>$file</b><br>";
	
	if($info)
	{
	$msg.= "Files/Folders in zip file: ".$info['nb']."<br>Comment: ".substr($info['comment'],0,120)."...<br>Files: ";
    for($i=0;$i<3&&$i<count($files);$i++)
		{$path_parts=pathinfo($files[$i]['filename']); $msg.=$path_parts["basename"].", ";}
    print"|1|$msg...|";}
    else print"|1|$msg Corrupted zip file|";
	}
 else print"|0||";	
 die(); 
 }

#------------ For normal folder tooltip----------------

if($action=="dirinfo")
{
$msg="<img src=images/dir.gif width=16 height=16> <b>$file</b><br>";
$dir="$dir/$file";
$dir_total=0;
$file_total=0;

if(file_exists($dir)) $reply=1;

if (is_dir($dir)) 
 {
 if($dh = opendir($dir)) 
  {
	while (($file = readdir($dh)))  {$files[] = $file;}
	sort($files);
	foreach($files as $file){
	if(is_dir("$dir/$file")&&$file!="."&&$file!="..")
		{
		if($dir_no<3){ $dir_msg.=$file.", "; $dir_no++;}
		$dir_total++;
		}
	else if(!is_dir("$dir/$file"))
		{
		if($file_no<3){ $file_msg.=$file.", "; $file_no++;}
		$file_total++;
		}		
	}
  }
 }

$msg.="$dir_total Folders and $file_total Files<br>";
if($dir_total) $msg.="Folders: $dir_msg...<br>";
if($file_total) $msg.="Files: $file_msg...";
}

#------------ For image tooltip----------------
if($action=="imginfo") {
  if($size=getimagesize($file)) {
    $reply = 1;
    $msg = "<img src=images/image.gif width=16 height=16> <b>$file</b><br>"; 
    $msg .= "Dimensions: ".$size[0]." x ".$size[1]."<br>";
    $type = array("","GIF","JPG","PNG");
    $msg .= "Type: ".$type[$size[2]]." Image";
  }
}

if($ajax)
	{expired();
	print"|$reply|$msg|";
	}
?> 