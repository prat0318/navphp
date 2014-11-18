<?php
#---------------------------
# PHP Navigator 4.40
# dated: January 7, 2011
# Coded by: Paul Wratt,
# Auckland, New Zealand
# web: navphp.sourceforge.net
#---------------------------


$dir = @$_REQUEST['dir'];
$file = @$_REQUEST['file'];
$dir_relative = $dir;

@include_once("functions.php");
@include_once("config.php");


authenticate();	//user login & other restrictions

function browseHere($xPath){
  global $server_root, $browser_root;
  for ($i=0;$i<count($server_root);$i++){
    if (substr_count($xPath,$server_root[$i])>0)
      return array($server_root[$i],$browser_root[$i]);
  }
  return array('undefined','undefined');
}

$dir_bh = browseHere($dir);

header("Location: ".str_replace($dir_bh[0],$dir_bh[1],$dir)."/".$file);

?>