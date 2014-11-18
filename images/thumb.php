<?php
#=================================
#  PHP Navigator 3.2
#  Coded by: Cyril Sebastian
#  9:36 PM; August 16, 2006	
#  http://navphp.sourceforge.net
#=================================
#---------------------------
# PHP Navigator 4.42
# dated: 20-07-2007
# edited: 31-05-2011
# Modified by: Paul Wratt,
# Melbourne,Australia
# web: phpnav.isource.net.nz
#---------------------------

$dir = @$_REQUEST['dir'];
$file= @$_REQUEST['file'];

include_once("../config.php");
include_once("../functions.php");

authenticate();


#---------Thumbnail generator-------------

if(isset($_GET['img'])) $src_file = urldecode($_GET['img']);
else $src_file = $dir."/".$file;
//$src_file = urldecode($src_file);
//$src_file = base64_decode($dir)."/".base64_decode($file);

$x = @$_GET['size']; $y=$x;
if(!$x) {$x=@$_GET['x']; $y=@$_GET['y'];}
if(!$x) {$x=32; $y=$x;}

if(!extension_loaded('gd')) @dl('gd.so');
if(!extension_loaded('gd')){
  header("Content-type: image/gif");
  header("Content-Length: ".filesize("nogd.gif"));
  print file_get_contents("nogd.gif");
  die();
}

if(strtolower(strrchr($src_file,"."))=='.bmp')
  include_once("../lib/fromBMP.php");

$imginfo = @getimagesize($src_file);
if(!$imginfo) die();

$srcX = $imginfo[0];
$srcY = $imginfo[1];
$type = $imginfo[2];

if($srcX<$x) $destX = $srcX;
else $destX = $x;
if($srcY<$y) $destY = $srcY;
else $destY = $y;

#-----Calculate Max Upload Size--
  $size_str = ini_get('upload_max_filesize');
  $z=0;
  while(ctype_digit($size_str[$z])) {$size.=$size_str[$z]; $z++;}
  if($size_str[$z]=="M"||$size_str[$z]=="m") $size = $size*1024*1024;
  else if($size_str[$z]=="K"||$size_str[$z]=="k") $size = $size*1024;
  else $size = 1024*1024*1024;

if(filesize($src_file)>$size)
	{
	print file_get_contents("exceeds.gif");
	die();
	}

switch($type)
    {
        case 1:
            if(function_exists('imagecreatefromgif'))
                $srcImage = imagecreatefromgif($src_file);
            break;
        case 2:
            if(function_exists('imagecreatefromjpeg'))
                $srcImage = imagecreatefromjpeg($src_file);
            break;
        case 3:
            if(function_exists('imagecreatefrompng'))
                $srcImage = imagecreatefrompng($src_file);
            break;
        case 6:
            if(function_exists('imagecreatefrombmp'))
                $srcImage = imagecreatefrombmp($src_file);
            break;
        case 15:
            if(function_exists('imagecreatefromwbmp'))
                $srcImage = imagecreatefromwbmp($src_file);
            break;
        case 16:
            if(function_exists('imagecreatefromxbm'))
                $srcImage = imagecreatefromxbm($src_file);
            break;
    }
if (!$srcImage) $srcImage = @imagecreatefromstring(file_get_contents($src_file));
if (!$srcImage) {
  header("Content-type: image/gif");
  header("Content-Length: ".filesize("cantpreview.gif"));
  print file_get_contents("cantpreview.gif");
  die();
}

if(isset($_GET['size'])){
  if($_GET['size']=='real'){
    header("Content-type: image/png");
    imagepng($srcImage);
    imagedestroy($srcImage);
    die();
  }
}

$destImage = imagecreatetruecolor($x, $x);
  $bgcolor= imagecolorallocate($destImage, 255, 255, 255);
  imagefill( $destImage, 0, 0, $bgcolor);
  imagecopyresampled($destImage, $srcImage, ($x-$destX)/2, ($x-$destY)/2, 0, 0,  $destX, $destY, $srcX, $srcY);

if(isset($_GET['border'])){
  if($_GET['border']!='false') imagecolortransparent($destImage,$bgcolor);
}else{
  $grey = imagecolorallocate($destImage, 175, 175, 175);
  imagerectangle($destImage, 0, 0,$x-1, $x-1, $grey);
}

if (function_exists("imagegif")) {
    header("Content-type: image/gif");
    imagegif($destImage);
}elseif (function_exists("imagejpeg")) {
    header("Content-type: image/jpeg");
    imagejpeg($destImage, "", 30);
} elseif (function_exists("imagepng")) {
    header("Content-type: image/png");
    imagepng($destImage);
} elseif (function_exists("imagewbmp")) {
    header("Content-type: image/vnd.wap.wbmp");
    imagewbmp($destImage);
} else {
    print file_get_contents("nogd.gif");
}

@imagedestroy($srcImage);
@imagedestroy($destImage);
?>