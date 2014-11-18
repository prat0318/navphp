/*
#---------------------------
# PHP Navigator 4.44
# Coded by: Cyril Sebastian
# dated: 03-8-2006
# modified: 10-12-2011
# Modified by: Paul Wratt
# web: navphp.sourceforge.net
#---------------------------*/

function newobj() {
    var ro;
    if(window.XMLHttpRequest){ // Non-IE browsers
        ro = new XMLHttpRequest();
    } else if (window.ActiveXObject){ // IE
        ro=new ActiveXObject("Msxml2.XMLHTTP");
        if (!ro){
            ro=new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    return ro;
}

var http = newobj();
var http_tooltip = newobj();
var nulloldficon = false;
var fo_new,error_string="<img src='images/error.gif' width=24 height=24> ";
var info_string="<img src='images/info.gif' width=24 height=24> ";

function delet() 
{
if(fname=="") alert("First select a file by clicking on it");
else{ msg="";
if(oldficon.getAttribute("spec").indexOf("d")>0) msg="All files/folders in this folder will be deleted!";
if(confirm("Delete '"+ fname_real+"' ?\n "+msg))
	{
	http.open('post', 'delete.php');
	http.setRequestHeader("Content-Type","application/x-www-form-urlencoded;");
	http.onreadystatechange = showresult;
	http.send("file="+fname+"&dir="+f.dir.value+"&ajax=1");
	thestatus.innerHTML = "<img src=images/working.gif> please wait...";
	fo_new=fo;
	nulloldficon=true;
	}
 }
}

function rename() 
{
if(fname=="") alert("First select a file by clicking on it");
else{
 newname=window.prompt("Rename- Enter the new file name:",fname_real);
 if(newname&&(newname!=fname))
	{
	http.open('post', 'rename.php');
	http.setRequestHeader("Content-Type","application/x-www-form-urlencoded;");
	http.onreadystatechange = showresult;
	http.send("file="+fname+"&change="+base64_encode(newname)+"&dir="+f.dir.value+"&ajax=1");
	fo_new=fo;
	}
 }
}

function chmode(f,i) 
{
change=f.mode.value;

if(fname=="") alert("First select a file by clicking on it");
else{
 http.open('post', 'chmod.php');
 http.setRequestHeader("Content-Type","application/x-www-form-urlencoded;");
 http.onreadystatechange = showresult;
 http.send("file="+fname+"&change="+base64_encode(change)+"&dir="+f.dir.value+"&ajax=1");
 fo_new=fo;
 }
}

function copy(f) 
{
if(fname=="") alert("First select a file by clicking on it");
else{
 sourcedir=f.dir.value;
 destdir=window.prompt("Copy to folder:",base64_decode(sourcedir));
 if(destdir&&(destdir!=sourcedir))
	{
	http.open('post', 'copy.php');
	http.setRequestHeader("Content-Type","application/x-www-form-urlencoded;");
	http.onreadystatechange = showresult;
	http.send("file="+fname+"&change="+base64_encode(destdir)+"&dir="+f.dir.value+"&ajax=1");
	fo_new=fo;
	}
  }	
}

function move(f) 
{
if(fname=="") alert("First select a file by clicking on it");
else{
 sourcedir=f.dir.value;
 destdir=window.prompt("Move to folder:",base64_decode(sourcedir));
 if(destdir&&(destdir!=sourcedir))
	{
	http.open('post', 'move.php');
	http.setRequestHeader("Content-Type","application/x-www-form-urlencoded;");
	http.onreadystatechange = showresult;
	http.send("file="+fname+"&change="+base64_encode(destdir)+"&dir="+f.dir.value+"&ajax=1");
	fo_new=fo;
	nulloldficon=true;
	}
  }	
}

function extract()
{

if(fname=="") alert("First select a file by clicking on it");
else if(oldficon.getAttribute("spec").indexOf("z")>0)
 {
 if(confirm("Extract all files from '"+fname_real+"' to the current folder?")){
 http.open('post', 'extract.php');
 http.setRequestHeader("Content-Type","application/x-www-form-urlencoded;");
 http.onreadystatechange = shownewfolder;
 http.send("file="+fname+"&dir="+f.dir.value+"&ajax=1");
 fo_new=fo;
  }
 }
}
function getzipinfo()
{

 http_tooltip.open('post', "tooltip.php");
 http_tooltip.setRequestHeader("Content-Type","application/x-www-form-urlencoded;");
 http_tooltip.onreadystatechange = showtooltip;
 http_tooltip.send("file="+fname+"&dir="+f.dir.value+"&ajax=1&action=zipinfo");
}

function getfolderinfo()
{
 http_tooltip.open('post', "tooltip.php");
 http_tooltip.setRequestHeader("Content-Type","application/x-www-form-urlencoded;");
 http_tooltip.onreadystatechange = showtooltip;
 http_tooltip.send("file="+fname+"&dir="+f.dir.value+"&ajax=1&action=dirinfo");
}

function getimginfo()
{
 http_tooltip.open('post', "tooltip.php");
 http_tooltip.setRequestHeader("Content-Type","application/x-www-form-urlencoded;");
 http_tooltip.onreadystatechange = showtooltip;
 http_tooltip.send("file="+fname+"&dir="+f.dir.value+"&ajax=1&action=imginfo");
}

function newfolder(f)
{ 
newname=window.prompt("Enter the new folder name:","new_folder");
if(newname)
 {
 if (window.ActiveXObject)
  window.location.href="?action=New folder&change="+base64_encode(newname)+"&dir="+f.dir.value;
 else{
 http.open('post', "newfolder.php");
 http.setRequestHeader("Content-Type","application/x-www-form-urlencoded;");
 http.onreadystatechange = shownewfolder;
 http.send("change="+base64_encode(newname)+"&dir="+f.dir.value+"&ajax=1");
 fo_new=fo;
 }
 }
 
}

function newfile(f)
{ 
newname=window.prompt("Enter the new file name:","new_file");
if(newname)
 {
 if (window.ActiveXObject)
  window.location.href="?action=New file&change="+base64_encode(newname)+"&dir="+f.dir.value;
 else{
 http.open('post', "newfile.php");
 http.setRequestHeader("Content-Type","application/x-www-form-urlencoded;");
 http.onreadystatechange = shownewfolder;
 http.send("change="+base64_encode(newname)+"&dir="+f.dir.value+"&ajax=1");
 fo_new=fo;
 }
 }
 
}

function dom_newfolder(file_status)	//fn for new <td> obj & display file_status
{
var tab=document.getElementById("filestable");
var i;
var cells;
firstrow= tab.rows[0];
if(firstrow.cells.length>4)
	{
	tab.insertRow(0);
	tab.rows[0].insertCell(0);
	}
else
	firstrow.insertCell(0);
firstrow= tab.rows[0];
firstrow.cells[0].setAttribute("onmousedown","loadtd(this)");
firstrow.cells[0].innerHTML=file_status;
}

function shownewfolder() {	//callback fn for newfolder
    if(http.readyState == 4){
		if(http.status!=200) {thestatus.innerHTML=error_string+"<font color=red><b>Error!:</b>  "+http.status+" "+http.statusText+". Please retry.</font>";
			newtooltip(thestatus.innerHTML,10000); return 0;}
		var reply = http.responseText;
        var update = new Array();
        update = reply.split('|');
        thestatus.innerHTML = update[2];
        if(update[1]==1) {
			dom_newfolder(update[3]);
			info.innerHTML="";
			unload();
			newtooltip(info_string+update[2],5000);	//show tooltip
			}
        else{
			newtooltip(error_string+update[2],5000);	//show tooltip
			alert(update[2]);   
        }
    }
   else thestatus.innerHTML = "<img src=images/working.gif> Processing..";
  
}

function showresult() {		//callback fn for all other fn's
    if(http.readyState == 4){
		if(http.status!=200) {thestatus.innerHTML=error_string+"<font color=red><b>HTTP Error!:</b>  "+http.status+" "+http.statusText+". Please retry.</font>"; 
			newtooltip(thestatus.innerHTML,10000); return 0;}
        var reply = http.responseText;
        var update = new Array();
        update = reply.split('|');
        thestatus.innerHTML = update[2];
        if(update[1]==1) {
			if(fo_new){
				fo_new.innerHTML = update[3];
				if(nulloldficon){
					oldficon=null;
					nulloldficon=false;
				}
			}else alert("Your browser does not provide full DOM support to display the results");
			info.innerHTML="";
			unload();
			newtooltip(info_string+update[2],5000);	//show tooltip
			}
        else {
			alert(update[2]);
        }
    }
   else thestatus.innerHTML = "<img src=images/working.gif> Processing..";
  
}

function showtooltip() {	//callback fn for getzipinfo() and getfolderinfo()
    if(http_tooltip.readyState == 4){
		if(http_tooltip.status==200) {
		var reply = http_tooltip.responseText;
        var update = new Array();
        update = reply.split('|');
        if(update[1]==1){
			newtooltip(update[2],20000);
			}
        }
    }
}       

function newtooltip(tip,timer)
{
zipinfo.innerHTML=tip;
zipinfo.style.visibility="visible";
zipinfo.style.top=document.body.scrollTop+100+"px";
window.setTimeout("hidecontext()",timer);
}