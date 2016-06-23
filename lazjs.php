<?php
/*
 * ----------------------------------------------
 * Lazarus Guestbook
 * by Stewart Souter
 * URL: www.carbonize.co.uk 
 * Based on Advanced Guestbook 2.3.x (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * Last Modified: Thu, 7 July 2011 20:35:54 GMT
 * ----------------------------------------------
 */
 
// Admin Javascript

$jspage = (isset($_GET['jspage'])) ? htmlspecialchars($_GET['jspage']) : '';
?>
function getbyid(id) {
  itm = null;
  if (document.getElementById) {
    itm = document.getElementById(id);
  }
  else if (document.all) {
    itm = document.all[id];
  }
  else if (document.layers)   {
    itm = document.layers[id];
  }
  return itm;
}
function hide_div(itm) {
  if ( ! itm ) return;
  itm.style.display = "none";
}
function show_div(itm) {
  if ( ! itm ) return;
  itm.style.display = "";
}
function resizeTextarea(itm,direction) 
{
  var txtarea = document.getElementById(itm);
  if (direction == 'down') 
  {
   txtarea.rows = txtarea.rows + 3;
  } 
  else 
  {
   txtarea.rows = txtarea.rows - 3;
  }
}

function openCentered(theURL,winName,winWidth,winHeight,features) {
 var w = (screen.width - winWidth)/2;
 var h = (screen.height - winHeight)/2 - 30;
 features = features+',width='+winWidth+',height='+winHeight+',top='+h+',left='+w;
 window.open(theURL,winName,features);
}

function getElementsByClass(searchClass, node, tagName) {
  if(node == null) {
    node = document;
  }
  // For browsers that have this built in
  if(document.getElementsByClassName) {
      return node.getElementsByClassName(searchClass);
  }
  // at least try with querySelector (IE8 standards mode)
  // about 5x quicker than below
  if(node.querySelectorAll) {
    if (tagName == null) {
  	  tagName = '';
    }
    return node.querySelectorAll(tagName + '.' + searchClass);
  }
  // For everything else
	var classElements = new Array();
	if (tagName == null) {
		tagName = '*';
  }
	var els = node.getElementsByTagName(tagName);
	var elsLen = els.length;
	var pattern = new RegExp('(^|\\\\s)' + searchClass + '(\\\\s|$)');
	for (i = 0, j = 0; i < elsLen; i++) {
		if(pattern.test(els[i].className)) {
			classElements[j] = els[i];
			j++;
		}
	}
	return classElements;
}
<?php
if($jspage != 'body')
{
?>
function toggleview(id) {
  if ( ! id ) return;
  if ( itm = getbyid('options_' + id + '_open') )
  {
    if (itm.style.display == "none")  
    {
      show_div(itm);
      itmtext = getbyid('options_' + id);
      itmtext.innerHTML = 'hide options';
    } 
    else 
    {
      hide_div(itm);
      itmtext = getbyid('options_' + id);
      itmtext.innerHTML = 'show options';
    }
  }
}
<?php
}
if($jspage == 'admin')
{
?>
function makeStatic()
{
  if (document.all) 
  { 
    sidemenu.style.pixelTop = document.body.scrollTop + 50;
  }
  else if (document.getElementById) 
  {
    document.getElementById('sidemenu').style.top = window.pageYOffset + 50 + 'px';
  }
  else if (document.layers) 
  {
    eval(document.sidemenu.top = eval(window.pageYOffset + 50));
  }
  setTimeout('makeStatic()',0);
}
//window.onload = makeStatic;
<?php
}
elseif($jspage == 'entryform')
{
?>
function trim(value) 
{
  value = value.replace(/^\s+|\s+$/g,'');
  return(value);
}
function emoticon(string)
{
  string = ' ' + string + ' ';
  document.book.gb_comment.focus();
  if (typeof(document.selection) != 'undefined')
  {
   var range = document.selection.createRange();
   if (range.parentElement() != document.book.gb_comment)
   return;
   range.text = string;
   range.select();
  }
  else if (typeof(document.book.gb_comment.selectionStart) != 'undefined')
  {
   var start = document.book.gb_comment.selectionStart;
   document.book.gb_comment.value = document.book.gb_comment.value.substr(0, start) + string + document.book.gb_comment.value.substr(document.book.gb_comment.selectionEnd, document.book.gb_comment.value.length);
   start += string.length;
   document.book.gb_comment.setSelectionRange(start, start);
  }
  else
   document.book.gb_comment.value += string;

  smileyBox('none');
  document.book.gb_comment.focus();
}
function agCode(theTag)
{
  var text1 = '[' + theTag + ']';
  var text2 = '[/' + theTag + ']';
  if (typeof(document.book.gb_comment.caretPos) != "undefined" && document.book.gb_comment.createTextRange)
  {
    var caretPos = document.book.gb_comment.caretPos, temp_length = caretPos.text.length;

    caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text1 + caretPos.text + text2 + ' ' : text1 + caretPos.text + text2;

    if (temp_length == 0)
    {
      caretPos.moveStart("character", -text2.length);
      caretPos.moveEnd("character", -text2.length);
      caretPos.select();
    }
    else
    {
      document.book.gb_comment.focus(caretPos);
    }
  }
  else if (typeof(document.book.gb_comment.selectionStart) != "undefined")
  {
    var begin = document.book.gb_comment.value.substr(0, document.book.gb_comment.selectionStart);
    var selection = document.book.gb_comment.value.substr(document.book.gb_comment.selectionStart, document.book.gb_comment.selectionEnd - document.book.gb_comment.selectionStart);
    var end = document.book.gb_comment.value.substr(document.book.gb_comment.selectionEnd);
    var newCursorPos = document.book.gb_comment.selectionStart;
    var scrollPos = document.book.gb_comment.scrollTop;
    
    document.book.gb_comment.value = begin + text1 + selection + text2 + end;
    
    if (document.book.gb_comment.setSelectionRange)
    {
      if (selection.length == 0)
      {
        document.book.gb_comment.setSelectionRange(newCursorPos + text1.length, newCursorPos + text1.length);
      }
      else
      {
        document.book.gb_comment.setSelectionRange(newCursorPos, newCursorPos + text1.length + selection.length + text2.length);
      }
      document.book.gb_comment.focus();
    }
    document.book.gb_comment.scrollTop = scrollPos;
  }
  else
  {
    document.book.gb_comment.value += text1 + text2;
    document.book.gb_comment.focus(document.book.gb_comment.value.length - 1);
  }
}
function storeCaret(text)
{
  if (typeof(text.createTextRange) != "undefined")
  text.caretPos = document.selection.createRange().duplicate();
}
function findPos(obj) 
{
  var curleft = curtop = 0;
  if (obj.offsetParent) 
  {
    curleft = obj.offsetLeft;
    curtop = obj.offsetTop;
    while (obj = obj.offsetParent) 
    {
      curleft += obj.offsetLeft;
      curtop += obj.offsetTop;
    }
  }
  return [curleft,curtop];
}

function smileyBox(doWhat) 
{
  var boxBottom = 0, boxLeft = 0;
  if (document.getElementById) 
  {
    boxPos = findPos(document.getElementById('gb_comment'));
    document.getElementById('LazSmileys').style.top = (boxPos[1] - 20) + 'px';
    document.getElementById('LazSmileys').style.left = (boxPos[0] - 30) + 'px';
    document.getElementById('LazSmileys').style.borderRadius = '10px';
    document.getElementById('LazSmileys').style.display = doWhat;
    if(document.getElementById('theSmileys').offsetHeight > 200)
    {
      document.getElementById('theSmileys').style.height = 200 + 'px';
      document.getElementById('theSmileys').style.overflow = 'auto';
    }    
  }
  else if (document.all) 
  {
    boxPos = findPos(document.all['gb_comment']);
    document.all['LazSmileys'].style.top = (boxPos[1] - 20) + 'px';
    document.all['LazSmileys'].style.left = (boxPos[0] - 30) + 'px';
    document.all['LazSmileys'].style.display = doWhat;
    if(document.all['theSmileys'].offsetHeight > 200)
    {
      document.all['theSmileys'].style.height = 200 + 'px';
      document.all['theSmileys'].style.overflow = 'auto';
    }      
  }
  else if (document.layers)   
  {
    boxPos = findPos(document.layers['gb_comment']);
    document.layers['LazSmileys'].style.top = (boxPos[1] - 20) + 'px';
    document.layers['LazSmileys'].style.left = (boxPos[0] - 30) + 'px';
    document.layers['LazSmileys'].style.display = doWhat;
    if(document.layers['theSmileys'].offsetHeight > 200)
    {
      document.layers['theSmileys'].style.height = 200 + 'px';
      document.layers['theSmileys'].style.overflow = 'auto';
    }      
  }
}
function getStyle(oElm, strCssRule)
{
	var strValue = '';
	if(document.defaultView && document.defaultView.getComputedStyle)
  {
		strValue = document.defaultView.getComputedStyle(oElm, '').getPropertyValue(strCssRule);
	}
	else if(oElm.currentStyle)
  {
		strCssRule = strCssRule.replace(/\-(\w)/g, function (strMatch, p1){
			return p1.toUpperCase();
		});
		strValue = oElm.currentStyle[strCssRule];
	}
	return strValue;
}

function errorStyling(itm)
{
  if(lazFormStyle[itm] == '' || lazFormStyle[itm] == undefined) 
  {
    lazFormStyle[itm] = (getStyle(document.getElementById(itm), 'background-color') == undefined) ? '' : getStyle(document.getElementById(itm), 'background-color');
  }
  document.getElementById(itm).style.backgroundColor = input_error_color;
}
<?php
}
?>