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

$multi_user = false;
		# if 'false' single user settings will be used.
		# else if 'true' ONLY multiple user settings will be used!!

#--- single user settings (no database required) -----#

$user = "admin";	# Login username
$passwd = "admin";	# Login password

$homedir = $_SERVER['DOCUMENT_ROOT'];
//$homedir = "..";
		# Default directory for single user. Use absolute path.
$enable_login = true;
		# Caution!! 'false' means everyone have access to your files!
$rdonly = false;
		# Read-only access to the single user.

#--- multiple user settings (database required) -------#

$mysql_server="localhost";		
$mysql_user = "root";
$mysql_passwd = "password";
$mysql_db = "navphp";		# database name
$mysql_table = "navphp_users";	# table name

#--- Other Options -------#

$cols = "auto";	
		# Number of icon columns. Leave as auto for auto-detection.
		# 5 best suited for 800x600 and 7 for 1024x768

$thumb = false;	
		# Force thumbnail view for all supported images.
		# (This has no relation with the thumbnail in the left pane)

$max_edit_size = 200000; 
		# maximum file size that can be edited (in Bytes).

$uploads = 6;
		# No. of files that can be uploaded at a time

$mode = "auto";
		# Can take three values 'auto', 'ajax' and 'normal'
		# If you don't know about this, select 'auto'.
		# 'auto' - Your browser compatibility will be automatically detected. (Recommended)
		
$compress = true;
		# Compress page using gzip deflate encoding. Recommended.

$EditableFiles = "php php4 php3 phtml phps conf cf sh shar csh ksh tcl cgi pl js vbs txt inc html htm shtml css xml xsl ini inf cfg log nfo bat tex sql java c cpp cs";
		# Editable Files (code editor)

	
$HTMLfiles = "htm html phtml shtml";
		# Enabled file types for WYSIWYG HTML editor.


#--- from navphp 4.12.15+ prtotype -----#


# use "View Server Info" to find the required "$server_root"
# the following are culminative, allowing multiple server urls with different paths to the same files
# add new $server_root[] + $browser_root[] pairs for each url you access the same folder or file

#$server_root[] = "/www/110mb.com/p/a/u/l/w/r/a/t/paulwratt/htdocs/ownurl";
#$browser_root[] = "http://www.ownurl.com";

$server_root[] = "/www/110mb.com/p/a/u/l/w/r/a/t/paulwratt/htdocs";
$browser_root[] = "";

/*
# server/browser pairs for View in Browser
# if you use the same phpnav on 2 urls
# or if you have a localhost & production servers
# Note: the order of pairs is important

# examples for linux
$server_root[] = "/home/users/username/www";
$browser_root[] = "~/username/";
$server_root[] = "/var/www/html/www.example.com";
$browser_root[] = "http://www.example.com/";
$server_root[] = "/var/www/html";
$browser_root[] = "";

# examples for windows
$server_root[] = "G:\\webserver\\";
$browser_root[] = "http://manns/";
$server_root[] = "G:\\iis\\wwwroot\\www.example.com";
$browser_root[] = "http://www.example.com/";
$server_root[] = "G:\\iis\\wwwroot\\";
$browser_root[] = "http://localhost/";
*/


# the following is used to patch the final html output
# where you server has added code that may affect display or interaction
$patch_output = true;
$output_patch = "<div style='display:none'><noscript><!--";

$view_charset = "UTF-8";
/*
Supported charsets:
Charset		Aliases		Description
ISO-8859-1	ISO8859-1	 Western European, Latin-1
ISO-8859-15	ISO8859-15	 Western European, Latin-9. Adds the Euro sign,
				 French and Finnish letters missing in Latin-1(ISO-8859-1).
UTF-8	 			 ASCII compatible multi-byte 8-bit Unicode.
cp866		ibm866, 866	 DOS-specific Cyrillic charset.
				 This charset is supported in 4.3.2.
cp1251		Windows-1251,	 Windows-specific Cyrillic charset.
		win-1251, 1251	 This charset is supported in 4.3.2.
cp1252		Windows-1252,	 Windows specific charset for Western European.
		1252
KOI8-R		koi8-ru, koi8r	 Russian. This charset is supported in 4.3.2.
BIG5		950		 Traditional Chinese, mainly used in Taiwan.
GB2312		936		 Simplified Chinese, national standard character set.
BIG5-HKSCS	 		 Big5 with Hong Kong extensions, Traditional Chinese.
Shift_JIS	SJIS, 932	 Japanese
EUC-JP		EUCJP		 Japanese
Note: Any other character sets are not recognized and ISO-8859-1 will be used instead.
*/

?>