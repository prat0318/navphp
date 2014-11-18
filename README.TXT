====================================
#  PHP Navigator 4.41
#  ------------------
#  Author: Cyril Sebastian,
#          Kerala, India.
#  Co-Developer:Paul Wratt,
#          Melbourne, Australia.
#  4:41 PM, May 30, 2011
#  http://navphp.sourceforge.net
===================================

 Web based open source file management system in PHP & AJAX.
 With Explorer style operation. You will feel you are on your local system!

*-* Warning: Provided as is! ABSOLUTELY NO WARRANTY.*-*
*-* The use of this software is at the risk of the user.*-*

Features
--------
 » User friendly Explorer style navigation and view. Easy to install and configure.
 » Advanced AJAX & DOM to avoid reloading of the full page.
 » Multiple user(need MySQL) or Single user operation.
 » Built in HTML editor, Code editor
 » gzip deflate encoding gives you lightning speed data transfer!
 » It inherits the colour scheme of your window system. See the magic of css2!
 » Thumbnail view for wbmp,jpg,png,gif.
 » Right click context menu.
 » Keyboard shortcuts
 » Download folder as zip.
 » Extract zip archive to current folder.
 » And the BEST OF ALL, it's FREE!! 
 

Why PHP Navigator?
------------------
	Why yet another web file manager? Because the technology changed, browsers changed,
and file managers too! When internet flies on Web 2.0, AJAX and DOM, why use the same old 
sluggish file manager?


Requirements
------------
PHP 4.3, gd2 library (optional) for thumbnail view.
MySQL server for multi-user operation (optional).
The web browser should support AJAX for the best functioning. (IE6, Mozilla Firefox etc)
It works best with Firefox. It should work on all other browsers in normal
mode (No AJAX).


Installing
----------
1.Unzip and upload the files to your server.

2.Edit the 'config.php' to customize to your needs. Set single-user/multi-user mode in
  config.php. For configuring multi-user settings see database section.

3.Then access 'index.php' in your browser. If you directly access 'windows.php', proper
  browser detection may NOT OCCUR.

---[OR]----
If you want to install in a remote server, you can use the 'navphp-zip-installer' to
save time & energy. Installer is available from the project website.


Database (only for multi-user)
------------------------
Use PHPMyAdmin to execute this query.

CREATE TABLE `navphp_users` (
`user` VARCHAR( 20 ) NOT NULL ,
`passwd` VARCHAR( 20 ) NOT NULL ,
`homedir` VARCHAR( 255 ) NOT NULL ,
`rdonly` INT NOT NULL
) ENGINE=MYISAM;

A sample set of record will look like: 'cyril', '123456', 'F:/Cyril/', '0'
While inserting values, use only forward slashes(/) for 'home' path.
And.. this feature is meant for expert Webmasters;). Curently there is no
admin module included with PHP Navigator. You should create users manually!

Note: We don't need MySQL in case of single user operation.


Known Issues
------------
 » HTML editor, Code editor and context menu won't work in Opera (<=9).
   It's better not to use Opera for editing HTML files.
 » Internet Explorer does not provide full DOM support (<=v6). This is 
   especially apparent when we create a new folder/file. 
   Get Firefox instead! (It's insanely better)
 » saved file can be 0 bytes in Chrome (on Linux) when file changes 
   charset type. only happens when copy & paste entire line. 
   Its a bug in Chrome, use history arrow to get edited file, Copy all 
   text, Exit, open the file again Paste, Save.


Trouble-Shooting
----------------
 My picture Thumbnails won't work.
	- Load the gd2 library for PHP. It allows use of thumbnails.

 How do I password protect PHP Navigator?
	- You have two methods, single-user and multi-user modes. See 'config.php'
 
 HTML Editor/Code editor/Context menu not working in Opera
	- Use IE or Firefox ;)
 
 I cannot write into any file on Linux!
	- You need 777 permission on both the file and the working folder for writing 
	  something. You need to chmod the dir either manually or using ftp.
 
 My browser doesn't display anything or I just see some erroneous characters.
	- Edit config.php and set '$compress' to false.
	

License:
--------
 Copyright (C) 2006-09 Cyril Sebastian <cyril2net@yahoo.com>
 Distributed under GNU General Public License


Thanks
-------
 » Timothy Alexander, USA

 This software uses zip library by Eric Mueller <eric@themepark.com>,
 PhpConcept Library - Zip Module 2.1 by Vincent Blavet- http://www.phpconcept.net,
 OEdit javascript library by Peter Andreas Harteg - http://www.harteg.dk
 CodePress by Fernando M.A.d.S. <fermads@gmail.com>- http://codepress.org/
 
 Feel free to inform me if you use PHP Navigator in your server.

------------------------------------------------------------------------
Contact: cyril2net@yahoo.com
Guestbook: 'http://navphp.sourceforge.net'
