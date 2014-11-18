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

$dir = @$_REQUEST['dir'];
$action = @$_REQUEST['action'];
$file = @$_REQUEST['file'];
$change = @$_REQUEST['change'];
$go = @$_REQUEST['go'];
$cookie_mode = @$_COOKIE['navphp'];
$cookie_cols = @$_COOKIE['navphp_cols'];
$cookie_thumb = @$_COOKIE['navphp_thumb'];
$cookie_arrange = @$_COOKIE['navphp_arrange'];
$dir_relative = $dir;

@include_once("functions.php");
//@include_once("explore.php"); # see below
@include_once("config.php");

authenticate();	//user login & other restrictions

if($action=="Download"){}
else if($compress) ob_start("ob_gzhandler");	// gzip/deflate encoding


if ($mode == "auto")
	{
	if($cookie_mode) $mode =$cookie_mode;
	elseif(ajax_enabled())$mode = "ajax";
	else $mode = "normal";
	}
if($cols ==	"auto")
	{
	if($cookie_cols) $cols =$cookie_cols;
	else $cols=5;
	}
if($cookie_thumb=="yes") $thumb =true;


if($action=="Download")
  {
  download();
  die();
  }

print '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> ';
print "<link href='inc/windows.css' rel=stylesheet type=text/css>
<link href='inc/skin.css' rel=stylesheet type=text/css>
<link rel='shortcut icon' href='favicon.ico'>";
if($action=="Open"&&!is_dir("$dir/$file")) print"<title>Edit- PHP Navigator</title>
<body topmargin=0 leftmargin=0 rightmargin=0><center>";
else  print"<script src=inc/windows.js></script><script src=inc/$mode.js></script>
<title>PHP Navigator</title><body onLoad=init_navphp() topmargin=0 leftmargin=0 rightmargin=0>";

if($action=="Open" && is_file("$dir/$file") && is_editable("$dir/$file"))
  {
  view($file,$dir);
  die();
  }
  
if($action=="Edit" && is_file("$dir/$file") && is_editable("$dir/$file"))
  {
  view_code($file,$dir);
  die();
  } 

print"<center><table width=100% class=window><tr><td colspan=3 class=head height=20>
	<center>PHP Navigator v4.44 <font color=orange><i>xp</i></font></td></tr>
	<form action='".$_SERVER['PHP_SELF']."' method=POST name=f><tr><td class=buttonrow>";

#------------------ACTIONS----------------

if ($action=="Search")
  require_once("search.php");
else
  require_once("explore.php");

if($dir)
 @chdir($dir);
if($action=="Open" && !is_file("$dir/$file"))
 @chdir($file);

if($action=="Up")
 up($dir);

if($action=="Upload")
 upload();

if($action=="Save")
 save($file);

if($action=="New folder")
 require_once("newfolder.php");

if($action=="New file")
 require_once("newfile.php");

if($action=="Chmode")
 require_once("chmod.php");

if($action=="Copy")
 require_once("copy.php");

if($action=="Delete")
 require_once("delete.php");

if($action=="Rename")
 require_once("rename.php");

if($action=="Extract")
 require_once("extract.php");


$dir=getcwd();
$dir_relative = substr($dir,strlen($homedir));


#---------------ALL BUTTONS--------------------
printbuttons($dir,0); 
print"</td><td></td></tr><TR><TD>
	<input type=hidden name=dir value=\"".base64_encode($dir_relative)."\">
      Address : ~ <input type=text value=\"".$dir_relative."\" size=80 name=go id=go></td>
	<td valign=middle><img src=images/go.gif alt=go  class=button onclick='gotodir(f)'>
      </td></tr></table></center>
      <script language=JavaScript>
		function fixResize(){
		  xObj = document.getElementById('go');
		  if (window.innerHeight) xObj.style.width = window.innerWidth-140;
		  else xObj.style.width = document.body.clientWidth-120;
		}
		fixResize();
		</script>
      <table width=100%><tr><td class=left>";
leftdata();
print"</td><td><center><br><div id=thestatus style='font-size:xx-small'>";
      
if(is_array($msg))							#printing all error messages
  foreach($msg as $m)
    print "$m<br>";
else
  print "$msg "; 
print "Click on a file icon to view its details</div>";

if($action!="Edit" && $action!="Search")				#exploring the files
{
	explore($dir); 

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
		<input type=hidden name=MAX_FILE_SIZE value='$size'>
		<input type=hidden name=dir value='".base64_encode($dir_relative)."'>";
	for($i=1;$i<=$uploads;$i++)
	  {
	    print"<input type=file name=upfile[] id=upfile>&nbsp;";
	    if($i%2==0) print"<br>";
	  }
	print"<input type=submit name=action value=Upload title=' max file size $max_size '></form><br>";

}elseif($action!="Edit" && $action=="Search")				# file & contents search
   search($dir);
?>
</td></tr></table>

<table id=context class="context" border="0" cellpadding="0" cellspacing="0" style="top:100px;">
<tr><td class=contbar><img src=images/dir.gif height=16 width=16></td><td><a href="javascript:opendir()" class="contitem"><b>Open </b></a></td></tr>
<tr><td class=contbar></td><td><hr></hr></td></tr>
<tr><td class=contbar><img src=images/rename.gif height=16 width=16></td><td><a href="javascript:rename()" class="contitem">Rename </a></td></tr>
<tr><td class=contbar><img src=images/delete.gif height=16 width=16></td><td><a href="javascript:delet()" class="contitem">Delete </a></td></tr>
<tr><td class=contbar><img src=images/copy.gif height=16 width=16></td><td><a href="javascript:copy(f)" class="contitem">Copy to</a></td></tr>
<tr><td class=contbar></td><td><hr></hr></td></tr>
<tr><td class=contbar><img src=images/newfile.gif height=16 width=16></td><td><a href="javascript:newfile(f)" class="contitem">New File </a></td></tr>
<tr><td class=contbar><img src=images/newfolder.gif height=16 width=16></td><td><a href="javascript:newfolder(f)" class="contitem">New Folder </a></td></tr>

</table>
<div id=zipinfo   style="top:100px; right:8px; position:absolute;   background:InfoBackground; border:1px solid black; font-size:8pt; padding:4px; visibility:hidden;" style="position:fixed;"></div>

<div style="position:absolute; visibility:hidden; top:100px; right:8px;">
<img src="images/working.gif">
<img src="images/info.gif">
<img src="images/error.gif"></div>
<?

# for web servers that add additional code to html output
# which interferes with layout or interaction

if($patch_output) print $output_patch;

?>
