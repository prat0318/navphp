/*
#---------------------------
# PHP Navigator 4.44
# Coded by: Cyril Sebastian
# Kerala, India
# dated: December 10, 2011
# Modified by: Paul Wratt
# Waitakere, New Zealand
# web: navphp.sourceforge.net
#---------------------------
*/

var i=-1;
var fname="",fname_real,ficon,oldficon,fo,tempY,tempX, timer;
var k=0;

//if(!document.rightClickDisabled) document.rightClickDisabled = true;
//document.oncontextmenu = showcontext;
document.onclick= hidecontext;
document.onblur= hidecontext;
document.onkeydown =shortcut;

function init_navphp()
{
 document.getElementById('filestable').oncontextmenu = showcontext;
 folderinfo.innerHTML = "Total files: " + f.total.value + "<br>Permissions: " + f.perms.value;
}

function encode(str){
  if (!str || str=='')
    return '';
  str = encodeURIComponent(str);
  return str;
}

function xTime(){
  return (new Date().getTime());
}

function browseHere(){
  if (fname=='')
    window.open('browse.php?dir='+f.dir.value,"","");
  else
    window.open('browse.php?dir='+f.dir.value + '&file='+fname,"","");
}

function upload()
{
i=0,flag=0;
while(f2.upfile[i]) {if(f2.upfile[i].value!="") flag=1; i++;}
if(!flag)
  {alert("Select the file to upload");
  return false;}
else return true;
}

function gotodir(f)
{ window.location.href="?go="+f.go.value;}

function refresh()
{ window.location.href="?dir="+f.dir.value;}

function thumbnail()
{
if(oldficon.getAttribute("spec").indexOf("t")>0) 
thumb.innerHTML="<center><img src='images/thumb.php?dir="+f.dir.value+"&file="+fname+"&size=150&ts="+xTime()+"' alt='Loading..'><br>"+lname;
}

function showinfo(file)
{info.innerHTML = file.getAttribute("info"); file.style.background="Highlight";}

function hideinfo(file)
{if(fname!=file.getAttribute("fname")) file.style.background="none";}

function loadfile(ficon)
{
if(oldficon) {oldficon.style.background="none"; //clear old icon
if(document.all) oldficon.style.filter='alpha(opacity=100)';
else oldficon.style.setProperty('-moz-opacity',1,'');}

lname = ficon.getAttribute("lname");
fname = ficon.getAttribute("fname");
fname_real = base64_decode(fname);
thestatus.innerHTML="Double click to open: <b>'"+lname+"'</b>";
ficon.style.background="Highlight";
if(document.all) ficon.style.filter='alpha(opacity=80)';
else ficon.style.setProperty('-moz-opacity',.8,'');

oldficon =ficon;
showinfo(ficon);
window.clearTimeout(timer);
if(oldficon.getAttribute("spec").indexOf("z")>0) 
	timer=window.setTimeout("getzipinfo()",2000);
if(oldficon.getAttribute("spec").indexOf("d")>0) 
	timer=window.setTimeout("getfolderinfo()",2000);	
if(oldficon.getAttribute("spec").indexOf("t")>0) 
	timer=window.setTimeout("getimginfo()",2000);
}

function unload()
{
fname="";
if(oldficon) oldficon.style.background="none"; //clear old icon
}


function loadtd(fobj)	//get the clicked 'td' obj
{
fo=fobj;
}

function opendir()
{
spec=oldficon.getAttribute("spec");
if(fname!=""){
if(spec.indexOf("d")>0) window.location.href="?action=Open&dir="+f.dir.value+"&file="+fname; 
else if(spec.indexOf("e")>0)
window.location.href="?action=Open&dir="+f.dir.value+"&file="+fname; 
//window.open("code_editor/editor.php?file=" + fname + "&dir="+f.dir.value, "Editor","width=750, height=500, left=10, top=10, resizable=yes, scrollbars=no, location=no, toolbar=no,menubar=no");
else not_editable();
	}
}

function go_up()
{
window.location.href="?action=Up&dir="+f.dir.value;
}

function edit()
{
spec=oldficon.getAttribute("spec");
if(fname!=""){
if(spec.indexOf("e")>0)
window.location.href="?action=Edit&dir="+f.dir.value+"&file="+fname; 
else not_editable();
	}
}

function openeditor()
{
if(fname!="") {
browser=navigator.userAgent;
if(browser.indexOf("pera")>0) alert("HTML Editor is not available in opera!");
else if(oldficon.getAttribute("spec").indexOf("h")>0) 
window.open("editor/editor.php?file=" + fname + "&dir="+f.dir.value, "Editor","width=750, height=500, left=10, top=10, resizable=yes, scrollbars=no, location=no, toolbar=no,menubar=no");
	}
}

function download_zip(filen)
{
if(confirm("Do you want to download folder '"+base64_decode(filen)+"' as zip?"))
  window.location.href="?action=Download&file="+filen+"&dir="+f.dir.value;
}


function download(filen)
{
  window.location.href="?action=Download&file="+filen+"&dir="+f.dir.value;
}


function arrange(arrang)
{
document.cookie="navphp_arrange="+arrang.value;
top.location.href=top.location.href;
}

function not_editable()
{
newtooltip(info_string+" This file type is not editable!!<br>To download this click the filename below its icon.",8000);
}

function centerWinStr(xWidth,xHeight){
  return 'width=' + xWidth + ', height=' + xHeight + ', left=' + ((screen.width/2)-(xWidth/2)) + ', top=' + ((screen.height/2)-(xHeight/2));
}

function config()
{
  w = 300; h = 120;
  window.open('settings.htm', 'Settings', centerWinStr(w,h) + ', resizable=no, scrollbars=no, location=no, status=no, toolbar=no, menubar=no, titlebar=no ');
}

function help()
{
  w = 500; h = 500;
  window.open('help.html', 'Help', centerWinStr(w,h) + 'resizable=no, scrollbars=no, location=no, status=no, dirctories=no, toolbar=no, menubar=no, titlebar=no ');
}

function searchfile(){
  w = 400; h = 145;
  window.open('search_form.php?action=Search&dir=' + f.dir.value + '&file=' + fname, 'Search', centerWinStr(w,h) + ', resizable=no, scrollbars=no, location=no, status=no, dirctories=no, toolbar=no, menubar=no, titlebar=no ');
}

function shortcut(evt)
{
var key;
if(!evt) evt=event;
if(!evt.keyCode) key=evt.charCode;
else key=evt.keyCode;
hidecontext();

if(key==113) rename();
if(key==13) opendir();
if(evt.shiftKey&&evt.ctrlKey)
	{
	if(key==67) copy(f);
	if(key==70) newfile(f);
	if(key==72) openeditor();
	if(key==78) newfolder(f);
	if(key==82) rename();
	if(key==84) thumbnail();
	if(key==69) extract();
	if(key==88) delet();
	return false;
	}
if((key>=37)&&(key<=40)&&!fo&&filestable.rows[0].cells[0].innerHTML)	
	fo=filestable.rows[0].cells[0];
if(key==39)	//right arrow
		{
		if(fo&&fo.nextSibling)
			{
			sibling=fo.nextSibling;
			if(sibling.nodeType!=1) sibling=sibling.nextSibling; // a workaround for firefox
			if(!sibling) return 0; //right end
			var atags=sibling.getElementsByTagName("img");
			loadtd(sibling);
			if(!sibling.innerHTML) {unload(); return 0;} //Empty cell found!
			loadfile(atags[0]);
			}
		}
if(key==37)	//left arrow
		{
		if(fo&&fo.previousSibling)
			{
			sibling=fo.previousSibling;
			if(sibling.nodeType!=1) sibling=sibling.previousSibling; // a workaround for firefox
			if(!sibling) return 0; //left end
			var atags=sibling.getElementsByTagName("img");
			loadtd(sibling);
			if(!sibling.innerHTML) {unload(); return 0;}
			loadfile(atags[0]);
			}
		}
if(key==38) //up arrow
		{
		if(fo&&(fo.parentNode.rowIndex>0))
			{
			if(filestable.rows[fo.parentNode.rowIndex-1].cells[fo.cellIndex])
				{
				sibling=filestable.rows[fo.parentNode.rowIndex-1].cells[fo.cellIndex];
				var atags=sibling.getElementsByTagName("img");
				loadtd(sibling);
				if(!sibling.innerHTML){unload(); return 0;}
				loadfile(atags[0]);
				}
			}
		}
if(key==40) //down arrow
		{
		if(fo&&(fo.parentNode.rowIndex<filestable.rows.length-1))
			{
			if(filestable.rows[fo.parentNode.rowIndex+1].cells[fo.cellIndex])
				{
				sibling=filestable.rows[fo.parentNode.rowIndex+1].cells[fo.cellIndex];
				var atags=sibling.getElementsByTagName("img");
				loadtd(sibling);
				if(!sibling.innerHTML) {unload(); return 0;}
				loadfile(atags[0]);
				}
			}
		}
}


function showcontext(evt)	//right click context menu
{
if(!fname) return true;
if(!evt) evt=event;
if(document.cookie.indexOf("navphp_cont=no")!=-1) return true;
cont=document.getElementById("context");
getMouseXY(evt);

span=document.body.clientHeight+document.body.scrollTop;
if((tempY+150)>span) //ensure full y-visibilty
	{span-=162; cont.style.top=span+"px"; }
else
	cont.style.top=tempY+"px";
	
span=document.body.clientWidth+document.body.scrollLeft;	
if((tempX+90)>span) //ensure full x-visibilty
	{tempX-=90; cont.style.left=tempX+"px";}
else
	cont.style.left=tempX+"px";
	
cont.style.visibility="visible";

//cont.scrollIntoView(false);

//remove customization
for(i=1;i<cont.rows.length-7;i++) cont.deleteRow(i);

//customize context menu
if(oldficon.getAttribute("spec").length>=2) 
	{
	cont.insertRow(1);
	cont.rows[1].insertCell(0);
	cont.rows[1].insertCell(1);
	cont.rows[1].cells[0].className="contbar";
	cont.rows[1].cells[1].className="contitem";
	}
if(oldficon.getAttribute("spec").indexOf("z")>0) 
	{
	cont.rows[1].cells[0].innerHTML="<img src=images/extract.gif height=16 width=16 class=contbar>";
	cont.rows[1].cells[1].innerHTML="<a href='javascript:extract(f)'>Extract Here </a>";
	}
else if(oldficon.getAttribute("spec").indexOf("t")>0) 
	{
	cont.rows[1].cells[0].innerHTML="<img src=images/view.gif height=16 width=16 class=contbar>";
	cont.rows[1].cells[1].innerHTML="<a href='javascript:thumbnail()'>Thumbnail</a>";
	}
else if(oldficon.getAttribute("spec").indexOf("h")>0) 
	{
	cont.rows[1].cells[0].innerHTML="<img src=editor/images/insertunorderedlist.gif height=16 width=16 class=contbar>";
	cont.rows[1].cells[1].innerHTML="<a href='javascript:openeditor(f)'>Edit HTML</a>";
	}
else if(oldficon.getAttribute("spec").indexOf("e")>0) 
	{
	cont.rows[1].cells[0].innerHTML="<img src=images/edit.gif height=16 width=16 class=contbar>";
	cont.rows[1].cells[1].innerHTML="<a href='javascript:edit(f)'>Edit Code</a>";
	}	
return false;
}

function hidecontext()
{
cont=document.getElementById("context");
cont.style.visibility="hidden";
//zipinfo=document.getElementById("zipinfo");
zipinfo.style.visibility="hidden";
}

function getMouseXY(e)	//get mouse position
{
if(!e) e=event;
  if (document.all) { 
    tempX = event.clientX + document.body.scrollLeft
    tempY = event.clientY + document.body.scrollTop
  } 
  else {  
    tempX = e.pageX
    tempY = e.pageY
  }  
  if (tempX < 0){tempX = 0}
  if (tempY < 0){tempY = 0}  
  return true
}

var base64chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'.split("");
var base64inv = {}; for (var i = 0; i < base64chars.length; i++) { base64inv[base64chars[i]] = i; }

function base64_encode (s)
 {
   var r = ""; var p = ""; var c = s.length % 3;
   if (c > 0) { for (; c < 3; c++) { p += '='; s += "\0"; } }
   for (c = 0; c < s.length; c += 3) {
     if (c > 0 && (c / 3 * 4) % 76 == 0) { r += "\r\n"; }
     var n = (s.charCodeAt(c) << 16) + (s.charCodeAt(c+1) << 8) + s.charCodeAt(c+2);
     n = [(n >>> 18) & 63, (n >>> 12) & 63, (n >>> 6) & 63, n & 63];
     r += base64chars[n[0]] + base64chars[n[1]] + base64chars[n[2]] + base64chars[n[3]];
   } return r.substring(0, r.length - p.length) + p;
 }

function base64_decode (s)
 {
   var p = (s.charAt(s.length-1) == '=' ? (s.charAt(s.length-2) == '='
    ? 'AA' : 'A') : ""); var r = ""; s = s.substr(0, s.length - p.length) + p;
 
   s = s.replace(new RegExp('[^'+base64chars.join("")+']', 'g'), "");
 
   for (var c = 0; c < s.length; c += 4) {
     var n = (base64inv[s.charAt(c)] << 18) + base64inv[s.charAt(c+3)] +
      (base64inv[s.charAt(c+1)] << 12) + (base64inv[s.charAt(c+2)] << 6);
     r += String.fromCharCode((n >>> 16) & 255, (n >>> 8) & 255, n & 255);
   } return r.substring(0, r.length - p.length);
 }
