<?php
#---------------------------
# PHP Navigator 4.43
# dated: June 2, 2011
# Coded by: Cyril Sebastian,
# Kerala,India
# Modified by: Paul Wratt
# web: navphp.sourceforge.net
#---------------------------

session_start();
include_once("config.php");
include_once("functions.php");

// verifies the login

if(@$_SESSION['loggedin']) 
{
header("Location:index.php"); 	// redirect if already logged in
exit;
}

#------Not logged in --------- 
// cleans up the users' input
$user_i = clean_input(@$_POST['user']);
$passwd_i = clean_input(@$_POST['passwd']);
$action = clean_input(@$_POST['action']);

/// user verification
if(!$multi_user&&!$enable_login) $login=true;

else if(!$multi_user&&($user_i==$user)&&($passwd_i==$passwd)&&$enable_login) 
{
	$login=true;
}
else if($multi_user)
{ 
	mysql_connect($mysql_server,$mysql_user,$mysql_passwd) or die("MySQL authentication failure!");
	mysql_query("use $mysql_db");
	$result=mysql_query("select * from $mysql_table where user='$user_i' AND passwd='$passwd_i'");
	  
	if(mysql_num_rows($result)>0)
	   {
	   $login=true;
	   $row=mysql_fetch_array($result,MYSQL_NUM);
	   $homedir=$row[2];
	   $rdonly=$row[3];

	   # allow short paths in db
	     if(strpos($homedir,"/")===false)
		$homedir = $_SERVER['DOCUMENT_ROOT']."/".$homedir;
	     elseif(strstr($homedir,"/")!=$homedir || strstr($homedir,"./")!=$homedir || strstr($homedir,"../")!=$homedir)
		$homedir = $_SERVER['DOCUMENT_ROOT']."/".$homedir;
	   }
	else $login=false;
	
}

	
if (!$login) {
		session_unset();
		session_destroy();
		$_SESSION['loggedin'] = 0;
		$_SESSION['homedir'] = "";
		$_SESSION['rdonly'] = "";
		if($action) $msg= "Authentication failed!";
} else {
		$_SESSION['loggedin'] = 1;
		$_SESSION['homedir'] = $homedir;
		$_SESSION['rdonly'] = $rdonly;
		header("Location:index.php"); 
		exit;
}
?><html>
<head>
<title>PHP Navigator- Login</title>
<link href="inc/windows.css" rel="stylesheet" type="text/css">
<link href="inc/skin.css" rel="stylesheet" type="text/css">
<style>
td{vertical-align:middle;}
</style>
</head>
</body>
<table cellspacing=0 cellpading=0 border=0 width=100% height=100% align=center valign=middle>
  <tr>
    <td width=100% height=100% align=center valign=middle><table height=100 width=300><tr>
      <td width=100% height=100% align=center valign=middle><form  method="post" action="">
<table width="300"  border="0" class="window">
  <tr>
    <td><table width="300"  border="0" align="center" cellpadding="0" cellspacing="0" class="lefthead">
      <tr>
        <td class="head" valign=middle><center class="title"><img src=images/logoff.gif align=left><strong>PHP Navigator - Login</strong></center></td>
      </tr>
    </table>
    <table width="300"  border="0" align="center" cellpadding="6" cellspacing="0">
      <tr>
        <td align="right" >User</td>
        <td align="left" ><input name="user" type="text" value="<?php print $user_i ?>" size="20" id=user></td>
      </tr>
      <tr>
       <td align="right">Password</td>
       <td align="left" ><input name="passwd" type="password" size="20"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align="left" ><input name="action" type="submit" id="action" value="Login">&nbsp;<?php print "<font color=red>$msg</font>"; ?></td>
      </tr></table></td></table></form></td>
    </tr></table></td>
  </tr>
</table>
<script>
document.forms[0].user.focus();
</script>
</body>
</html>
<?php if($patch_output) print $output_patch; ?>