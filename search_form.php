<?php
#-----------------------------
# PHP Navigator 4.42
# date: 06-03-2008
# last: June 1, 2011
# Created by: Paul Wratt
# Modified by:
# from: phpnav.isource.net.nz
#-----------------------------

$dir = @$_REQUEST['dir'];
$file = @$_REQUEST['file'];
$action = @$_REQUEST['action'];

$odir = $dir;

//$file = urldecode($file);
//$dir = urldecode($dir);


include_once("config.php");
include_once("functions.php");

authenticate();	//user login & other restrictions

if($action!='Search' || !$dir) die('nope');

$browse = end(explode("/",$dir));

print <<<HTML
<html>
<head>
<!--[if lte IE 6]>
<link rel=stylesheet type=text/css href=inc/pngfix.css />
<![endif]-->
<link rel='icon' href='./favicon.png' type='image/x-icon' />
<link href='inc/windows.css' rel=stylesheet type=text/css />
<link href='inc/skin.css' rel=stylesheet type=text/css />
<style>
body { background-color:ButtonFace; }
td   { vertical-align:middle; }
</style>
<script language=JavaScript>
function encode(str){
  if (!str || str=='')
    return '';
  str = encodeURIComponent(str);
  return str;
}

function xTime(){
  return (new Date().getTime());
}
function check(xObj){
  if(xObj.content.value=='') { xObj.content.focus(); return (false); }

  search4 = window.opener.encode(xObj.content.value);
  searchopt = '';
  if (xObj.subdir.checked==true) searchopt = '&subdir=yes';
  if (xObj.fnac.checked==true && xObj.file.value=='') searchopt = searchopt + '&search=' + search4;
  if (xObj.fnac.checked==true && xObj.file.value!='') searchopt = searchopt + '&content=' + search4 + '&file=' + encode(xObj.file.value); 
  if (xObj.fnac.checked==false) searchopt = searchopt + '&content=' + search4;

  window.opener.location.href = window.opener.location.pathname + '?action=Search&dir={$odir}' + searchopt + '&ts=' + xTime;
  return (false);
}
</script>
</head>
<body>
<form onSubmit="return(check(this));">
<fieldset>
<legend>Search&nbsp;in&nbsp;<b>{$browse}</b>&nbsp;</legend>
<table width=100% border=0 cellspacing=0 cellpadding=2>
<tr><td align=right><input name=subdir type=checkbox value=yes disabled=true disabled>&nbsp;in subfolders,&nbsp;&nbsp;Search for&nbsp;</td><td><input name=content type=text></td></tr>
<tr><td align=right><input name=fnac type=checkbox value=true checked onClick="if(!this.checked){this.form.file.value='';}">&nbsp;and filenames,&nbsp;&nbsp; files with&nbsp;</td><td><input type=text name=file value="{$file}"></td></tr>
<tr><td align=center colspan=2><input type=button value=" close " onClick="window.close();">&nbsp;&nbsp;<input type=button value=" search " onClick="check(this.form);" style="padding-left:16px; background-image: url('thumb.php?size=20&border=false&img=images/search.gif'); background-repeat:no-repeat;"></td></tr>
</table>
</fieldset>
</form>
</body>
</html>
HTML;

?>