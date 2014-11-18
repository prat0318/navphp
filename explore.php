<?php
#---------------------------
# PHP Navigator 4.44
# Coded by: Cyril Sebastian
# Kerala, India
# dated: December 10, 2011
# Modified by: Paul Wratt
# Waitakere, New Zealand
# web: navphp.sourceforge.net
#---------------------------

#----------FUNCTION EXPLORE----------
#This is the core of the script which lists the files and folders
#and display them in explorer style.
#------------------------------------

function explore($dir)
{
global $cols, $uploads,$i, $dir_relative;
print"<table cellspacing=8  id=filestable><tr class=center>";
     
if (is_dir($dir)) 
 {
 if($dh = opendir($dir)) 
  {
  while (($file = readdir($dh)))  {$files[] = $file;}
  sort($files);
  $i=1;
  foreach($files as $file)
   {
   if($file!="."&&$file!=".."&&is_dir($file))
    {
     print "<td onmousedown=loadtd(this)>";
     filestatus($file);	# function to print file icon & details
     print "</td>\n";
	 if($i%$cols==0)
      print"</tr><tr class=center>";
     $i=$i+1;       	 
    }
   }
  
  if($_COOKIE['navphp_arrange']=="type")	#sort by type
  {
	  foreach($files as $file)
		{
		$data=pathinfo($file);
		$exts[]=strtolower($data["extension"]);
		}
	  array_multisort($exts,SORT_STRING ,SORT_ASC,$files); 
  }
  else if($_COOKIE['navphp_arrange']=="size")	#sort by size
  {
	  foreach($files as $file)
		{
		$sizes[]=filesize($file);
		}
	  array_multisort($sizes,SORT_NUMERIC ,SORT_ASC,$files); print "size";
  }
  
  foreach($files as $file)
   {
if($file!="."&&$file!=".."&&!is_dir($file))
    {
     print "<td onmousedown=loadtd(this)>";
     filestatus($file);	# function to print file icon & details
     print "</td>\n";
     if($i%$cols==0)
      print"</tr><tr class=center>";
     $i=$i+1;       	 
    }
   }
  closedir($dh);
  }
 }
else
 $msg[]= "Directory $dir does not exist!";
$total = count($files)-2;
$perms = decoct(fileperms($file)%01000);
print"</table><input type=hidden name=total value='$total'>
      <input type=hidden name=perms value='$perms'></form><br>
      <table class=window width=100%><tr><td class=buttonrow align=center>";
printbuttons($dir,1);
print"</td></tr></table><br>";

}
?>
