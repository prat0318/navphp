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

#----------OTHER FUNCTIONS------- 


function upload()
 {
 global $msg,$uploads,$dir;
 
 if($GLOBALS['rdonly'])
  { $msg[]="Warning: Working in read-only mode!";  return 1;  }
  
 #-----Calculate Max Upload Size--
 $size_str = ini_get('upload_max_filesize');
 $i=0;
 while(ctype_digit($size_str[$i])) {$size.=$size_str[$i]; $i++;}
 if($size_str[$i]=="M"||$size_str[$i]=="m") $size = $size*1024*1024;
 else if($size_str[$i]=="K"||$size_str[$i]=="k") $size = $size*1024;
 else $size = 1024*1024*1024;
 
 #----Start Upload--
 for($i=0;$i<=$uploads;$i++)
  if($_FILES['upfile']['name'][$i]!="") 
  {
   if ($_FILES['upfile']['size'][$i]!=0 and $_FILES['upfile']['size'][$i]<=$size)
   {
   $file=$_FILES['upfile']['name'][$i];
   $uploadfile = $dir."/".$file;
   if(move_uploaded_file($_FILES['upfile']['tmp_name'][$i], $uploadfile))
    $msg[]="$file uploaded";
   else
    $msg[]="Upload failed for $file!";
   }
   else
   $msg[]="Upload failed for $file due to exceeding file size limits, or zero length file!"; 
  }
 } 

function view_code($file,$dir)	// calling code editor
 {
 global $dir_relative;
 
 $data=pathinfo($file);
 $ext=strtolower($data["extension"]);
  if($ext=="htm"||$ext=="html"||$ext=="xml"||$ext=="shtml"||$ext=="mht") {
  $lan="html";
  }
  else if($ext=="js") {
  $lan="javascript";
  }
  else if($ext=="php"||$ext=="php3"||$ext=="php4"){
  $lan="php";
  }
  else if($ext=="c"||$ext=="cpp"){
  $lan="generic";
  }
  else if($ext=="css") {
  $lan="css";
  }
  else if($ext=="sql") {
  $lan="sql";
  }
  else if($ext=="java") {
  $lan="java";
  }
  else {
  $lan="text";
  }

 global $msg, $max_edit_size, $deflate, $view_charset;
 if(filesize("$dir/$file")>$max_edit_size) 
  print"File size exceeds the limit of $max_edit_size bytes<br>Have the Site Admin edit config.php to customize this";
 else
  {print"<b><center>$dir_relative/$file</center></b>
  <style>body{background-color:ThreeDFace;}</style><center>";
 print("<script src=\"code_editor/codepress.js\" type=\"text/javascript\"></script>
	<script language=\"javascript\" type=\"text/javascript\">
	function submitform()
	{
	dataBox.toggleEditor();
	return(true);
	}
	</script>
 <form action='?dir=".base64_encode($dir_relative)."&file=".base64_encode($file)."' method=POST>
       <textarea rows=22 cols=80 class=\"codepress $lan\" id='dataBox' name='data'>".htmlentities(file_get_contents("$dir/$file"), ENT_QUOTES, $view_charset)."</textarea>
       <input type=hidden name=dir value='".base64_encode($dir_relative)."'>
       <input type=hidden name=file value='".base64_encode($file)."'>
	   <input type=hidden name=action value=Save><br>
       <input type=Submit value=Save onclick=submitform();>
	   <input type=button onClick='location.href=\"?dir=\"+this.form.dir.value;' value=Exit></form>
	   <script language=JavaScript>
		function fixResize(){
			xObj = document.getElementById('dataBox');
			xObj.style.width = document.body.clientWidth-40;
			xObj.style.height = document.body.clientHeight-100;
		}
		fixResize();
		</script></center>");
   }
 if($patch_output) print $output_patch;
 if($deflate){
 $data= ob_get_clean();
 echo gzdeflate($data);} 
 }


function view($file,$dir)	//for unsupported browsers
 {
 global $msg, $max_edit_size, $deflate, $dir_relative, $view_charset;
 if(filesize("$dir/$file")>$max_edit_size) 
  print"File size exceeds the limit of $max_edit_size bytes<br>Have the Site Admin edit config.php to customize this";
 else
  {
  print"<b>$dir_relative/$file</b><center>";
  print("<style>body{background-color:ThreeDFace;}</style>
		<form action='?dir=".base64_encode($dir_relative)."&file=".base64_encode($file)."' method=POST>
       <textarea rows=22 cols=80 name=data id=dataBox>".htmlentities(file_get_contents("$dir/$file"), ENT_QUOTES, $view_charset)."</textarea>
       <input type=hidden name=dir value='".base64_encode($dir_relative)."'>
       <input type=hidden name=file value='".base64_encode($file)."'><br>
       <input type=submit name=action value=Save>
       <input type=button onClick='location.href=\"?dir=\"+this.form.dir.value;' value=Exit></form>
       <script language=JavaScript>
	function fixResize(){
		xObj = document.getElementById('dataBox');
		xObj.style.width = document.body.clientWidth-40;
		xObj.style.height = document.body.clientHeight-100;
	}
	
	fixResize();
    </script></center>");
   }
 if($patch_output) print $output_patch;
 if($deflate){
 $data= ob_get_clean();
 echo gzdeflate($data);} 
 }

function save($file)
 {
  global $msg;
  if(get_magic_quotes_gpc()){
  $data = stripslashes($_POST['data']);
  } else {
   $data = $_POST['data'];
  }
  $f=fopen($file,"w");
  if(fwrite($f,$data)) $msg= "File $file saved!";
  fclose($f);
 }


function fileicon($file)
 {
 $data=pathinfo($file);
 $ext=strtolower($data["extension"]);
 global $thumb, $dir;
 
 if($ext=="htm"||$ext=="html"||$ext=="xml"||$ext=="shtml"||$ext=="mht"||$ext=="phtml")
  $img="web.gif";
 else if($ext=="cgi"||$ext=="pl"||$ext=="sql"||$ext=="txt"||$ext=="cf"||$ext=="asp"||$ext=="aspx"||$ext=="jsp"||$ext=="py")
  $img="cgi.gif";
 else if($ext=="zip"||$ext=="rar"||$ext=="gz"||$ext=="tar"||$ext=="tgz"||$ext=="bz2")
  $img="zip.gif";
 else if($ext=="exe"||$ext=="bin"||$ext=="bat"||$ext=="sh"||$ext=="com"||$ext=="dll")
  $img="bin.gif";
 else if($ext=="doc"||$ext=="pdf"||$ext=="ps"||$ext=="odf"||$ext=="docx")
  $img="doc.gif";
 else if($ext=="js"||$ext=="vbs")
  $img="js.gif";
 else if($ext=="css")
  $img="css.gif";
 else if($ext=="php"||$ext=="php3")
  $img="php.gif";
 else if($ext=="psd"||$ext=="svg"||$ext=="gd"||$ext=="gd2"||$ext=="gd2part"||$ext=="iff"||$ext=="swf"||$ext=="swc"||$ext=="tif"||$ext=="tiff"||$ext=="xpm")
  $img="image.gif";
 else if($ext=="gif"||$ext=="jpg"||$ext=="jpeg"||$ext=="png"||$ext=="bmp")
  {
   if($thumb)
   {
   $info = @getimagesize($file);
   if(!$info) $img="image.gif";
   else if($info[2]==1||$info[2]==2||$info[2]==3||$info[2]==15) 
    {
    $img = "thumb.php?img=".urlencode("$dir/$file");	//thumbnail path
    return $img;
    }
   }  
   $img="image.gif";
  }
 else
  $img="file.gif";
 return $img;
 }


 function up($dir)
 {
 //$dir = base64_decode($dir);
 global $homedir;

 $dirup = substr($dir,0,-1);
 $pos = strrpos($dir, "/");
 if($pos===false) $pos = strrpos($dir, "\\"); #for windows
 
 if($pos!=0) $up = substr($dir,0,-(strlen($dir)-$pos));
 else $up="/";
 
 if(strpos($up,$homedir)!==0) $up=$homedir; #restrict to home dir!
 chdir($up); 
 
 
 }


function printbuttons($dir,$i) 
{
 global $homedir;
 
 if($_COOKIE['navphp_arrange']=="type") $arr_type="selected";
 else if($_COOKIE['navphp_arrange']=="size") $arr_size="selected";
 else $arr_name="selected";
 
 print"<a href= './'><img src=images/home.gif class=button title=Home></a>
 <img src=images/up.gif border='0' onClick='go_up()' class=button title=Up>
 <img src=images/reload.gif onClick='refresh()' class=button title='Refresh'>
 <img width=1 height=24 class=seperator>
 <img src=images/copy.gif onClick='copy(f)' class=button title='Copy [Shift+Ctrl+C]'>
 <img src=images/move.gif onClick='move(f)' class=button title='Move'>
 <img src=images/delete.gif onClick='delet(f)' class=button title='Delete [Shift+Ctrl+X]'>
 <img src=images/rename.gif onClick='rename(f)' class=button title='Rename [F2]'>
 <img width=1 height=24 class=seperator>
 <img src=images/newfolder.gif  onClick='newfolder(f)' class=button title='New Folder [Shift+Ctrl+N]'>
 <img src=images/newfile.gif  onClick='newfile(f)' class=button title='New File [Shift+Ctrl+F]'>
 <img width=1 height=24 class=seperator>";
 if($i==0) print"<select name='mode' style='margin-top:2px; vertical-align:top;'>
  <option value=0777>777</option>
  <option value=0770>770</option>
  <option value=0755 selected>755</option>
  <option value=0750>750</option>
  <option value=0666>666</option>
  <option value=0660>660</option>
  <option value=0644>644</option>
  <option value=0600>600</option>
  <option value=755>default</option>
  <option value=666>readonly</option>
  <option value=777>readwrite</option>
 </select>
 <img src=images/chmode.gif  onClick='chmode(f)' class=button title='Change Permissions'>
 <img width=1 height=24 class=seperator>
 <select name='arr' style='margin-top:2px; vertical-align:top;' onChange='arrange(this)'>
 <option value=name $arr_name>By Name</option>
 <option value=type $arr_type>By Type</option>
 <option value=size $arr_size>By Size</option>
 </select>
 <img width=1 height=24 class=seperator>
 <a href=logoff.php><img src=images/logoff.gif   class=button title='Logoff'></a>
 <img width=1 height=24 class=seperator>
 ";
 print "<img src=images/extract.gif onClick='extract(f)' class=button title='Extract Here'>";
}

function leftdata()
{
global $dir_relative, $mode, $compress;
$dir_e = base64_encode($dir_relative);
$ajax="<br>Working in <b>'$mode'</b> mode";
if($compress) $encoding="yes"; else $encoding="no";

print"<table cellspacing=0 width=100%>";
print"<tr><td class=lefthead><b>This Folder</b></td><tr>";
print"<tr><td class=leftsub><div id=folderinfo name=folderinfo width=100% class=info></div></td><tr></table><br>";
print"<table cellspacing=0 width=100%><tr><td class=lefthead><b>File Properties</b></td><tr>";
print"<tr><td class=leftsub><div id=info name=info width=100% class=info></div></td></tr>";
print"</table><br>";
print"<table cellspacing=0 width=100%>";
print"<tr><td class=lefthead ><b>File and Folder tasks</b></td><tr>";
print"<tr><td class=leftsub><div   width=100% class=info id=tasks>
<a href='javascript:thumbnail();'><img src=images/view.gif width=16 height=16> <u>View as thumbnail</u></a><br>
<a href='javascript:browseHere();'><img src=images/html.gif width=16 height=16> <u>View in Browser</u></a><br>
<a href='javascript:extract();'><img src=images/extract.gif width=16 height=16> <u>Extract Here</u></a><br>
<a href='javascript:searchfile();'><img src=images/search.gif width=16 height=16> <u>File Search</u></a><br>
<a href='javascript:openeditor();' title='Edit HTML [Shift+Ctrl+H]'><img src=editor/images/insertunorderedlist.gif width=16 height=16 > <u>Open in HTML Editor</u></a><br>
<a href='javascript:edit();'><img src=images/edit.gif width=16 height=16> <u>Open in Code Editor</u></a><br>
<a href='windows.php?dir=$dir_e' target=_new><img src=images/explore.gif width=16 height=16> <u>Explore from Here</u></a><br>
</div></td></tr></table><br>";
print"<table cellspacing=0 width=100%>";
print"<tr><td class=lefthead onClick='thumbnail();'><b>Thumbnail View</b></td><tr>";
print"<tr><td class=leftsub><div   width=100% class=info id=thumb></div></td></tr></table><br>";
print"<table cellspacing=0 width=100%><tr><td class=lefthead><b>User Info</b></td><tr>";
print"<tr><td class=leftsub><div   width=100% class=info>User IP: ".$_SERVER['REMOTE_ADDR']."$ajax<br>Compression: <b>$encoding</b><br>
&bull; <a href='javascript:config();'><u>Configure PHP Navigator</u></a><br>
&bull; <a href=server.php target='_blank'><u>View Server Info</u></a><br>
&bull; <a href='javascript:help();'><u>Quick Help</u></a></div></td></tr>";
print"</table><br><center>&copy; Cyril Sebastian<br><a href=http://navphp.sourceforge.net><b>navphp.sourceforge.net</b></a>";
}

function filestatus($file)
{
global $dir,$ajax, $action;
$scale = array(" Bytes"," KB"," MB"," GB");
$stat = stat($file);

$size = $stat[7];
for($s=0;$size>1024&&$s<4;$s++) $size=$size/1024;	//Calculate in Bytes,KB,MB etc.
if($s>0) $size= number_format($size,2).$scale[$s];
else $size= number_format($size).$scale[$s];
if(is_editable($file)) $dblclick="opendir()"; else $dblclick="not_editable()";
$spec=filespec($file);

//$filename   = wordwrap(htmlentities($file,ENT_QUOTES,"utf-8"), 15, "<br>\n",1);
$filename   = wordwrap($file, 15, "\r\n",1);
$filename_t = base64_encode($file);
$filename_e = htmlentities($file,ENT_QUOTES,"utf-8");
$filename_l = str_replace('<','&lt;',$filename);
$filename_h = str_replace("\r\n",'<br>',$filename_l);

if(is_dir($file))
	print " \n<center><a class=icon><img
	src=images/dir.gif width=32 height=32
	id=file fname=\"$filename_t\" lname=\"$filename_l\" spec=\"$spec\"
	onMouseDown=loadfile(this) onDblClick=opendir()
	info=\"<b>$filename_h</b><br>File Folder<br><br>
	    Permissions:".decoct(fileperms($file)%01000)."<br>
	    Modified: ".date('d-m-y, G:i', $stat[9])."\"
	></a><br><a class=name title=\"Download as zip\" href=\"javascript:download_zip('$filename_t')\"
	>$filename_h</a>\r\n";
else
	print"  \n<center><a class=icon><img
	src=\"images/".fileicon($file)."\" width=32 height=32
	id=file fname=\"$filename_t\" lname=\"$filename_l\" spec=\"$spec\"
	onMouseDown=loadfile(this) onDblClick=$dblclick
	info=\"<b>$filename_h</b><br><br>Size: $size<br>
	    Permissions:".decoct(fileperms($file)%01000)."<br><br>
	    Modified: ".date('d-m-y, G:i', $stat[9])."<br>
	    Accessed: ".date('d-m-y, G:i', $stat[8])."\"
	></a><br><a class=name title=download href=\"javascript:download('$filename_t')\"
	>$filename_h</a>\r\n";
}

function filespec($file)	# Attributes z-zip, t-thumb, d-dir, h-html, e-editable
{
global $HTMLfiles;
$spec="f";

if(is_dir($file)) $spec.="d";
if(is_editable($file)) $spec.="e";
 $data=pathinfo($file);
 $ext=strtolower($data["extension"]);
if($ext=="png"||$ext=="gif"||$ext=="jpg"||$ext=="jpeg"||$ext=="bmp") $spec.="t";
if($ext=="zip") $spec.="z";

foreach(explode(" ", $HTMLfiles) as $type)
  if ($ext == $type) $html=true;
if($html==true) $spec.="h";
return $spec;
}

function authenticate()
{
 
 verify_login();	//check user auth

 global $user, $passwd, $dir, $homedir, $action, $file, $change, $msg, $rdonly, $ajax, $mode,$go,$lang, $dir_relative, $multi_user;
 
// require_once("lang/$lang.declare.php");
 
 $homedir = $_SESSION['homedir'];
 $rdonly = $_SESSION['rdonly'];
 $homedir=realpath($homedir);
  
 if(!$dir) $dir="$homedir";
 else $dir=realpath("$homedir/".base64_decode("$dir"));
 
 if($go&&!$action) $dir=realpath("$homedir/".urldecode($go));
  
// $file = basename(base64_decode($file));
 $file = base64_decode($file);
// $change = basename(base64_decode($change));
 $change = base64_decode($change);
 $dir=str_replace("\\","/",$dir); #For Windows
 $homedir=str_replace("\\","/",$homedir); #For Windows 

 $warning="<img src=images/warning.gif style='margin-top:-4px;'>";
 
 if(strpos($dir,$homedir)!==0) 	# restrict to homedir!
 {
 $msg[]="$warning<b>Warning: Access restricted to home dir!</b>"; 
 $dir=$homedir;
// chdir($dir);
 }
 
 if(!is_dir($dir)) {$msg[]="$warning<b>Warning: Reference to invalid directory!</b>"; $dir="$homedir";}
 
 if($rdonly&&($action!="Open")&&($action!="Up")&&($action!="Download")&&($mode=="normal")) 
	{ $action="rdonly"; $msg="$warning<b>Warning: Working in read-only mode!<br>The operation was not completed!</b><br>";	}


 $dir_relative = substr($dir,strlen($homedir));
}  


function verify_login()	#--check login-----
{
session_start();
if(!$_SESSION['loggedin']) 
  {
  header("Location:login.php"); 	// redirect if not logged in
  exit;
  }
}

#---clean input---
function clean_input($str, $allow_html=false)
{
if (!get_magic_quotes_gpc()) $str = addslashes($str);
if(!$allow_html) $str = strip_tags($str);
return $str;
}

#---clean output---
function clean_output($str, $allow_html=false)
{
$str = stripslashes($str);
if(!$allow_html){
	$str = htmlentities($str);
	$str = nl2br($str);
	}
return $str;	
}


function download()	# Download file and folder-zip;
{
global $dir, $file; //print $dir."/".$file;

if(is_file($dir."/".$file))
  {
  header("Content-Disposition: attachment; filename=\"".utf8_encode($file)."\";");
  header("Content-Type: file/x-msdownload");
  header("Content-Length: ".filesize($dir."/".$file));
  //echo file_get_contents($dir."/".$file);
  $handle = fopen($dir."/".$file, "rb");
  while (!feof($handle)) echo fread($handle, 8192);
  fclose($handle);
  }
else if(is_dir($dir."/".$file))
	{
	include_once("lib/zip.lib.php");
	
	$newzip = new zipfile();
	chdir($dir);
	$name=$file;
	add_dir($name,$newzip);
	
	header("Content-Disposition: attachment; filename=".$name."_navphp.zip");
	header("Content-Type: file/x-msdownload");
	$data=$newzip->file();
	header("Content-Length: ".strlen($data));
	echo $data;
  }  
}

function add_dir($dir,$newzip)	# recursive adding of files to zip
{
static $no;
$no=$no+1;
if(($no>10)|| (strlen($newzip->file())>5000000)) die("Too many sub directories (>$no) or Total size > 5MB!<br>Try them by parts. [Some security measures!] ");
if($dh = opendir($dir)) 
  {
  $newzip->addFile("","$dir/",0);
  while (($file = readdir($dh)))  {$files[] = $file;}

  foreach($files as $file)
   {
	if($file!="."&&$file!=".."&&!is_dir("$dir/$file")&&is_readable("$dir/$file"))
	{
	$data=file_get_contents("$dir/$file");
	$newzip->addFile($data,"$dir/$file",0);
	}
   }
  foreach($files as $file)
   {
   if($file!="."&&$file!=".."&&is_dir("$dir/$file"))
	{
	add_dir("$dir/$file",$newzip);
	}
   }
 closedir($dh);
  }
}

function expired()
{
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");  
}    

// I assume only IE6 and mozilla support AJAX.
// This is called only if client side test fails..

function ajax_enabled()
{
            $agt=strtolower($_SERVER['HTTP_USER_AGENT']);

            $brwsr['ie6']     = (strpos($agt, 'msie 6.') !== false);
            $brwsr['ie7']     = (strpos($agt, 'msie 7.') !== false); 
            $brwsr['ie8']     = (strpos($agt, 'msie 8.') !== false);
            $brwsr['firefox'] = (strpos($agt, 'firefox') !== false);
            $brwsr['opera']   = (strpos($agt, 'opera') !== false);
            
if(($brwsr['ie6']||$brwsr['ie7']||$brwsr['ie8']||$brwsr['firefox'])&&!$brwsr['opera'] )
	return 1;
else return 0;
}

function is_editable($filename)	# Checks whether a file is editable
{
 global $EditableFiles;
 $ext = strtolower(substr(strrchr($filename, "."),1));

 foreach(explode(" ", $EditableFiles) as $type)
  if ($ext == $type)
   return TRUE;

 return FALSE;
}

?>