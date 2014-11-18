<?php
#---------------------------
# PHP Navigator 4.40
# from PHP Navigator 4.12
# dated: 12-11-2007
# edited: January 14, 2011
# Modified by: Paul Wratt,
# Melbourne,Australia
# web: phpnav.isource.net.nz
#---------------------------
# PHP Navigator
# by: Cyril Sebastian
# web: navphp.sourceforge.net
#---------------------------

#----------FUNCTION SEARCH----------

function searchstatus($filepath)
{
global $dir,  $realdir, $no_icn, $icn_size, $use_layout;
$skin = $GLOBALS['skin'];
$gi   = $GLOBALS['groupimgs'];
if(is_array($icn_size) && $use_layout){
  $w = $icn_size[0];
  $h = $icn_size[1];
}else{
  $w = '32';
  $h = $w;
}
if(!is_array($icn_size) || $no_icn) $skin = "";

$scale = array(" Bytes"," KB"," MB"," GB");
$stat = stat($filepath);
$size = $stat[7];
for($s=0;$size>1024&&$s<4;$s++) $size=$size/1024;	//Calculate in Bytes,KB,MB etc.
if($s>0) $size= number_format($size,2).$scale[$s];
else $size= number_format($size).$scale[$s];

//$data = pathinfo($filepath);
//$folder = $data['dirname'];
//$file = $data['basename'];
//$fldrs = explode('/',$filepath);
//$last2fold = $fldrs[count($fldrs)-2]."/".end($fldrs);

$fldrs = explode('/',$filepath);
$file = array_pop($fldrs);
$last2fold = $fldrs[count($fldrs)-2]."/".end($fldrs);
$folder = implode('/',$fldrs);

//$filename_t = htmlentities($file,ENT_QUOTES);
$filename_t = htmlentities($file,ENT_QUOTES,"utf-8");
$filename_e = base64_encode($file);
$filename_x = $file;
//$filename_e = urlencode($file);
//$pathname_e = urlencode($folder);
$pathname_x = $folder;
$pathname_e = base64_encode($folder);
//$dir_e = urlencode($dir);
$dir_e      = base64_encode($dir);
$filename   = wordwrap($filename_t, 15, "<br>\n",1);

$o = posix_getpwuid($stat[4]);
$owner = (is_array($o)) ? $o['name'] : $stat[4];
$g = posix_getgrgid($stat[5]);
$group = (is_array($g)) ? $g['name'] : $stat[5];

// ".decoct(fileperms($filepath)%01000)."
$pa = preg_split('//', base_convert((decoct(fileperms($filepath)%01000)),8,2), -1, PREG_SPLIT_NO_EMPTY);
for($i=0;$i<9;$i+=3){
  $pa[0+$i] = ($pa[0+$i]=='1') ? 'r' : '-' ;
  $pa[1+$i] = ($pa[1+$i]=='1') ? 'w' : '-' ;
  $pa[2+$i] = ($pa[2+$i]=='1') ? 'x' : '-' ;
}
$perms = implode('',$pa);
//$perms = decoct(fileperms($filepath)%01000);

$dblclick="location.href='?action=Open&dir=$pathname_e&file=$filename_e';";
//$dblclick="opendir()";

$spec=filespec($file);

if(is_dir($filepath)){
	$img = "skins/{$skin}dir{$gi}";
	if (!file_exists($realdir.$img)) $img = "images/dir.gif";
	print "<center><a class=icon><img
	src=\"$img\" width=$w height=$h
	info=\"<b>$filename</b><br>Folder in: $last2fold<br><br>
	    Permissions: $perms<br>
	    Owner: $owner<br>
	    Group: $group<br>
	    C Time: ".date('d-m-y, G:i', $stat[10])."<br>
	    Modified: ".date('d-m-y, G:i', $stat[9])."\" 
	fname='$filename_e'
	onMouseDown=\"loadfile(this);\" id=file title=\"$filename_t in $last2fold\" onDblClick=\"$dblclick\"  spec=\"$spec\" 
	onError=\"this.src='images/dir.gif';\"></a><br><a 
	class=name href=\"?action=Download&file=$filename_e&dir=$pathname_e\" 
	title=\" Download as zip \">$filename</a>";
}else{
	if(!is_editable($file)) $dblclick="location='?go=$pathname_e';";
	$ficon = fileicon($file);
	$img = "skins/{$skin}$ficon";
	if (!file_exists($realdir.$img)) $img = "images/$ficon";
	if (strstr($ficon,"thumb")==$ficon) $img = $ficon;
	print"<center><a class=icon><img
	src=\"$img\" width=$w height=$h 
	info=\"<b>$filename</b><br>in: $last2fold<br>Size: $size<br>
	    Permissions: $perms<br>
	    Owner: $owner<br>
	    Group: $group<br>
	    C Time: ".date('d-m-y, G:i', $stat[10])."<br>
	    Modified: ".date('d-m-y, G:i', $stat[9])."<br>
	    Accessed: ".date('d-m-y, G:i', $stat[8])."\" 
	fname='$filename_e'
	onMouseDown=\"loadfile(this);\" title=\"$filename_t in $last2fold\" 
	id=file onDblClick=\"$dblclick\" spec=\"$spec\" 
	onError=\"this.src='images/$ficon';\"></a><br><a 
	class=name href=\"?action=Download&file=$filename_e&dir=$pathname_e\" 
	title=\" Download \">$filename</a>";
 }
}

function search($dir)
{
global $cols, $uploads, $i, $arrange_by, $msg;
print"<table cellspacing=8 id=filestable><tr class=center>";

$sf = urldecode($_REQUEST['search']);
$sd = isset($_REQUEST['subdir']);
$fn = urldecode($_REQUEST['file']);
$cn = urldecode($_REQUEST['content']);
$ifn = false;
if ($fn & $cn) {
  $ifn = true;
}elseif (!$fn & !$cn) {
  $fn = $sf;
  $cn = $sf;
}

if (is_dir($dir)) 
 {

$files = array();
if (is_dir($dir) && !$sd){ // search directory
  if ($handle=opendir($dir)){
    chdir($dir);
    $d = $f = 0;
    while (false!==($file=readdir($handle))){
      if ($file!='.' && $file!='..') {
        $f++;
        if ($ifn) {
          if (stripos($file,$fn)!==false) {
            $filecontents = file_get_contents($file);
            $filecontains = stripos($filecontents,$cn);
            if ($filecontains!==false) {
              $files[] = "$dir/$file";
            }
          }
        }else{
          if (@stripos($file,$fn)!==false) {
            $files[] = "$dir/$file";
          }
          if (!is_dir($file) && $cn && !$ifn && !in_array("$dir/$file",$files)) {
            $filecontents = file_get_contents($file);
            $filecontains = stripos($filecontents,$cn);
            if ($filecontains!==false) {
              $files[] = "$dir/$file";
            }
          }
        }
      }
    }
    closedir($handle);
    $msg[]= "$i Entries searched";
  }
}elseif (is_dir($dir) && $sd) { // search directory & sub directories
  try{
    chdir("/");
    $l = strlen($dir); $d = $f = 0;
    $searchdir = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($dir), true);
    foreach ($searchdir as $searchfile) {
      $filepath = $searchfile->getPathname();
//      $filepath = $searchfile;
//      $data = pathinfo($filepath);
//      $file = $data['basename'];
      $file = end(explode('/',$filepath));
      if(is_dir($filepath)){
        $d++;
        if (!$ifn) {
          if (@stripos($file,$fn)!==false) {
            $files[] = $filepath;
          }
        }
      }else{
        $f++;
        if ($ifn) {
          if (stripos($file,$fn)!==false) {
            $filecontents = file_get_contents($filepath);
            $filecontains = stripos($filecontents,$cn);
            if ($filecontains!==false) {
              $files[] = $filepath;
            }
          }
        }else{
          if (@stripos($file,$fn)!==false) {
            $files[] = $filepath;
          }
          if ($cn && !$ifn && !in_array($file,$files)) {
            $filecontents = file_get_contents($filepath);
            $filecontains = stripos($filecontents,$cn);
            if ($filecontains!==false) {
              $files[] = $filepath;
            }
          }
        }
      }
    }
    $msg[]= "$j Files in $i Directories searched";

  }catch (Exception $ex){
//echo "owch";
//throw $ex;
  }
}

$names = array();
foreach($files as $file){ // sort a-z by filename
  $data=pathinfo($file);
  $names[]=strtolower($data["basename"]);
}
//array_multisort($names,SORT_STRING,SORT_ASC,$files); 

$srch = ($fn) ? $fn : $cn;
$msg[]= "Search results: <b>".count($files)."</b> matches for <b>srch</b>";

// below code from explore()
  $i=1;
  foreach($files as $file)
   {
   if($file!="."&&$file!=".."&&is_dir($file))
    {
     print "<td onmousedown=loadtd(this)>";
     searchstatus($file);	# function to print file icon & details
     print "</td>\r\n";
	 if($i%$cols==0)
      print"</tr><tr class=center>";
     $i++;       	 
    }
   }
  if($arrange_by=="type")	#sort by type
  {
	  foreach($files as $file)
		{
		$data=pathinfo($file);
		$exts[]=strtolower($data["extension"]);
		}
	  array_multisort($exts,SORT_STRING ,SORT_ASC,$files); 
  }
  elseif($arrange_by=="size")	#sort by size
  {
	  foreach($files as $file)
		{
		$sizes[]=0+filesize($file);
		}
	  array_multisort($sizes,SORT_NUMERIC ,SORT_DESC,$files); 
  }
  foreach($files as $file)	#default is sort by name
   {
   if($file!="."&&$file!=".."&&!is_dir($file))
    {
     print "<td onmousedown=loadtd(this)>";
     searchstatus($file);	# function to print file icon & details
     print "</td>\r\n";
     if($i%$cols==0)
      print"</tr><tr class=center>";
     $i++;       	 
    }
   }
  print"\r\n";
  while($i%$cols!=0){
    print "<td></td>";
    $i++;       	 
  }
  print"<td></td></tr>";
 }
else
 $msg[]= "Directory $dir does not exist!";
$total = $d+$f;
$perms = count($files);
print"</table><input type=hidden name=total value='$total'>
      <input type=hidden name=perms value='$perms'></form><br>";
print"<table class=window width=100%><tr><td align=center class=buttonrow nowrap>";
printbuttons($dir,1);
print"</table>\r\n";

/*
#-----Calculate Max Upload Size--
  $size_str = ini_get('upload_max_filesize');
  $z=0;
  while(ctype_digit($size_str[$z])) {$size.=$size_str[$z]; $z++;}
  $max_size = $size.$size_str[$z];
  if($size_str[$z]=="M"||$size_str[$z]=="m") $size = $size*1024*1024;
  else if($size_str[$z]=="K"||$size_str[$z]=="k") $size = $size*1024;
  else $size = 1024*1024*1024;

#--------UPLOAD FORM----------
print"<form id=f2 enctype=multipart/form-data method=POST action='windows.php' onSubmit='return upload();'>
      <input type=hidden name=MAX_FILE_SIZE value='$size'><input type=hidden name=dir value='$dir'>";
for($i=1;$i<=$uploads;$i++)
 {
 print"<input type=file name=upfile[] id=upfile>&nbsp;";
 if($i%2==0) print"<br>";
 }
print"<input type=submit name=action value=Upload title=' max file size $max_size '></form><br>";
*/

print"<link rel='icon' href='skins/search.gif' type='image/x-icon' />";
}
?>
