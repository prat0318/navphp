// OEdit Ver. 3.6 - © 2004 Peter Andreas Harteg - http : // www.harteg.dk

var changemode = "This function is only available in layout mode. Do you want to change mode?";
var btns = [
["save", "Save", "Saves this document"],
[""],
["selectall", "Select all (Ctrl+A)", "Select the entire document"],
["cut", "Cut (Ctrl+X)", "Cut the selection to the clipboard"],
["copy", "Copy (Ctrl+C)", "Copy the selection to the clipboard"],
["paste", "Paste (Ctrl+V)", "Insert clipboard contents"],
[""],
["undo", "Undo (Ctrl+Z)", "Undo the last action"],
["redo", "Redo (Ctrl+Y)", "Redo the previously undone action"],
[""],
["html", "Change mode", "Change between lay-out and HTML mode"],
[""],
["justifyleft", "Justify left", "Apply left justification"],
["justifycenter", "Center", "Apply centered justification"],
["justifyright", "Justify right", "Apply right justification"],
[""],
["createlink", "Create or edit hyperlink", "Create or edit hyperlink"],
["unlink", "Remove hyperlink", "Remove the selected hyperlink"],
["h1", "Heading 1", "Format selected paragraph(s) as Heading 1"],
["h2", "Heading 2", "Format selected paragraph(s) as Heading 2"],
["h3", "Heading 3", "Format selected paragraph(s) as Heading 3"],
["h4", "Heading 4", "Format selected paragraph(s) as Heading 4"],
["p", "Paragraph", "Format as normal paragraph level"],
[""],
["bold", "Bold", "Format with bold font style"],
["italic", "Italic", "Format with italic font style"],
["underline", "Underlined", "Format with underlined font style"],
[""],
["insertunorderedlist", "Unsorted list", "Create or remove unsorted list"],
["insertorderedlist", "Ordered list", "Create or remove ordered list"],
[""],
["outdent", "Decrease indentation", "Decrease the indentation of selected text"],
["indent", "Increase indentation", "Increase the indentation of selected text"]
];
function getimage(image)
{
   return "images/" + image +".gif";
}
var format = "HTML"; var isNav = (navigator.appName == "Netscape");
function init()
{
   result.document.write("<body style='background: ButtonFace; margin:0; border:none;'>");
   document.getElementById("f").contentWindow.document.designMode = "on";
   document.getElementById("f").contentWindow.focus();
}
function chmode()
{
   if(format == "HTML")
   {
      if(isNav)
      {
         var html = document.createTextNode(document.getElementById("f").contentWindow.document.body.innerHTML);
         with(document.getElementById("f").contentWindow.document.body)
         {
            innerHTML = "";
            appendChild(html)
         }
      }
      else
      {
         with(document.getElementById("f").contentWindow)
         {
            with(document.body)
            {
               style.fontFamily = "Courier";
               style.fontSize = "10pt";
               innerText = innerHTML
            }
            focus();
            document.body.createTextRange().collapse(false)
         }
      }
      document.getElementById("html").src = img2.src;
      format = "Text"
   }
   else
   {
      if(isNav)
      {
         var html = document.getElementById("f").contentWindow.document.body.ownerDocument.createRange();
         html.selectNodeContents(document.getElementById("f").contentWindow.document.body);
         document.getElementById("f").contentWindow.document.body.innerHTML = html.toString()
      }
      else
      {
         with(document.getElementById("f").contentWindow)
         {
            with(document.body)
            {
               innerHTML = innerText;
               style.fontFamily = "";
               style.fontSize = ""
            }
            focus();
            document.body.createTextRange().collapse(false)
         }
      }
      document.getElementById("html").src = img1.src;
      format = "HTML"
   }
}
function cmd(c)
{
   if(c == "save")
   {
      if(format == "HTML")
      {
         result.document.write("Saving.....");
         document.getElementById("text").value = document.getElementById("f").contentWindow.document.body.innerHTML;
         document.getElementById("ta").submit()
      }
      else if(confirm(changemode))chmode()
   }
   else if(c == "selectall")document.getElementById("f").contentWindow.document.execCommand(c, false, null);
   else if(c == "html")chmode();
   else
   {
      if(format == "HTML" || (c == "cut" || c == "copy" || c == "paste" || c == "undo" || c == "redo"))
      {
         var t = null;
         if(c == "iimage")
         {
            t = document.forms[c].iimage.value; c = "insertimage"
         }
         
         if((c.search(/h[1-4]/) != - 1) || c == "p")
         {
            t = "<" + c + ">";
            c = "formatblock"
         }
         document.getElementById("f").contentWindow.focus();
         if(t == null && c == "createlink")
         {
            if(isNav)
            {
               t = prompt("Enter URL:", "");
               document.getElementById("f").contentWindow.document.execCommand("CreateLink", false, t)
            }
            else document.getElementById("f").contentWindow.document.selection.createRange().execCommand(c, true, t)
         }
         else if(c == "cut" || c == "copy" || c == "paste")document.getElementById("f").contentWindow.document.selection.createRange().execCommand(c, false, null);
         else document.getElementById("f").contentWindow.document.execCommand(c, false, t);
         document.getElementById("f").contentWindow.focus();
      }
   }
}
function tables()
{
   for(var i = 0; i < btns.length; i ++ )
   {
      if(btns[i][0] == "tr")document.write("</td></tr></table></td></tr><tr><td><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>");
      else
      {
         if(btns[i][0] != "")document.write("<img src=\""+getimage(btns[i][0])+"\" id=\""+btns[i][0]+"\" alt=\""+btns[i][1]+"\" title=\""+btns[i][1]+"\" onclick=\"cmd('"+btns[i][0]+"')\" width=\"20\" height=\"20\" style=\"border :ButtonFace 1px outset; \" onmouseover=\"this.style.border = 'ButtonFace 1px inset'; window.status = '"+btns[i][2]+"'\" onmouseout=\"this.style.border = 'ButtonFace 1px outset'; \">");
         else document.write("<img src=\"images/blank.gif\" width=\"8\" height=\"20\" style='border:none'>");
      }
   }
}
function sb(i, t)
{
   document.write("</td><td><form id=\""+btns[i][0]+"\"><select id=\""+btns[i][0]+"\">");
   for(var j = 0; j < t.length; j ++ )document.write("<option value=\""+ t[j][0]+"\">" + t[j][1] + "</option>");
   document.write("</select></form></td><td>")
}
function bloker()
{
   return false
}
document.ondragstart = bloker;
img1 = new Image();
img1.src = getimage("html");
img2 = new Image();
img2.src = getimage("layout");