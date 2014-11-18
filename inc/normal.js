/*
#---------------------------
# PHP Navigator 4.0
# dated: 03-8-2006
# Coded by: Cyril Sebastian,
# web: navphp.sourceforge.net
#---------------------------*/

function rename(f)
{
if(fname=="") alert("First select a file by clicking on it");
else{
 oldname=fname;
 newname=window.prompt("Rename- Enter the new file name:",oldname);
 if(newname&&(newname!=oldname))
 window.location.href="?action=Rename&file="+oldname+"&change="+base64_encode(newname)+"&dir="+f.dir.value;
 }
}

function delet(f)
{
if(fname=="") alert("First select a file by clicking on it");
else{ msg="";
 if(oldficon.getAttribute("spec").indexOf("d")>0) msg="All files/folder inside this will be deleted!";
 if(confirm("Delete file '"+ fname_real+"' ?\n"+msg))
 window.location.href="?action=Delete&file="+fname+"&dir="+f.dir.value;
 }
}

function chmode(f,i)
{
change=f.mode.value;
if(fname=="") alert("First select a file by clicking on it");
else{
 window.location.href="?action=Chmode&change="+f.mode[i].value+"&file="+fname+"&dir="+f.dir.value;
 }
}

function copy(f) 
{
if(fname=="") alert("First select a file by clicking on it");
else{
 sourcedir=f.dir.value;
 destdir=window.prompt("Copy to folder:",base64_decode(sourcedir));
 if(destdir&&(destdir!=sourcedir))
	window.location.href="?action=Copy&file="+fname+"&change="+base64_encode(destdir)+"&dir="+f.dir.value;
  }	
}

function newfolder(f)
{
 newname=window.prompt("Enter the new folder name:","new_folder");
 if(newname)
 window.location.href="?action=New folder&change="+base64_encode(newname)+"&dir="+f.dir.value;
}

function newfile(f)
{
 newname=window.prompt("Enter the new file name:","new_file");
 if(newname)
 window.location.href="?action=New file&change="+base64_encode(newname)+"&dir="+f.dir.value;
}

function extract()
{
if(fname=="") alert("First select a file by clicking on it");
else if(oldficon.getAttribute("spec").indexOf("z")>0)
	{
	if(confirm("Extract all files '"+fname_real+"' to the current folder?"))
	window.location.href='?action=Extract&file='+fname+"&dir="+f.dir.value;
	}
}

function getzipinfo()	//dummy fn
{
}
function getfolderinfo()	//dummy fn
{
}