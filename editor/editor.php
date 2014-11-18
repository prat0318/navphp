<?php
#=================================
#  PHP Navigator 3.2
#  9:36 PM; August 16, 2006	
#  http://navphp.sourceforge.net
#=================================

 $dir=$_GET['dir'];
 $file=$_GET['file'];
 
include_once("../functions.php");
include_once("../config.php");
authenticate();

$ext = strtolower(substr(strrchr($file, "."),1));
foreach(explode(" ", $HTMLfiles) as $type)
  if ($ext == $type) $html=true;
if(!$html) die("<h3>File $file is not an HTML file!</h3>");
if(!is_file("$dir/$file")) die("<h3>File $dir/$file does not exists!</h3>");
?>
<html>
<head>
<title>HTML Editor</title>
<link href='../inc/windows.css' rel=stylesheet type=text/css>
<script type="text/javascript" src=editor.js></script>
</head>
<body style="background: ButtonFace;" onload="init()">
<center>
<font size=4 color=#336699>PHP Navigator <font color=orange><i>xp</i></font> - HTML Editor</font>
<?php
print "<br>$dir_relative/$file";
?>
<hr>
<table class="edit" width=100% border="0" cellpadding="0" cellspacing="0"><tr><td>
<table border="0" cellpadding="0" cellspacing="0">
<tr><td><script type="text/javascript">
tables();
</script></td></tr></table><hr>
</td></tr><tr><td bgcolor=white><iframe id="f" src='getfile.php?<?php
 print "dir=".base64_encode($dir_relative)."&file=".base64_encode($file);
 ?>' width=100% height=350 style="border: 1px inset"></iframe><br>
 <iframe name="result" id="result" width=100% height=30 style="border:none; background: ButtonFace; z-index:100" ></iframe>
</td></tr></table>
<form method="post" id="ta" action="save.php" target="result">
 <?php
 print "<input type=hidden name=dir value='".base64_encode($dir_relative)."'>
 <input type=hidden name=file value='".base64_encode($file)."'>";
 ?>
<p style="visibility:hidden;position:absolute;left:0;top:0">
<textarea name="data" id="text" style="width: 0;height: 0;visibility: hidden;"></textarea></p>
</form>


</td></tr></table>

</body></html>