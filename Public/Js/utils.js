/* $Id : utils.js 5052 2007-02-03 10:30:13Z weberliu $ */

var Browser = new Object();

Browser.isMozilla = (typeof document.implementation != 'undefined') && (typeof document.implementation.createDocument != 'undefined') && (typeof HTMLDocument != 'undefined');
Browser.isIE = window.ActiveXObject ? true : false;
Browser.isFirefox = (navigator.userAgent.toLowerCase().indexOf("firefox") != - 1);
Browser.isSafari = (navigator.userAgent.toLowerCase().indexOf("safari") != - 1);
Browser.isOpera = (navigator.userAgent.toLowerCase().indexOf("opera") != - 1);

var Utils = new Object();

Utils.htmlEncode = function(text)
{
  return text.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

Utils.trim = function( text )
{
  if (typeof(text) == "string")
  {
    return text.replace(/^\s*|\s*$/g, "");
  }
  else
  {
    return text;
  }
}

Utils.isEmpty = function( val )
{
  switch (typeof(val))
  {
    case 'string':
      return Utils.trim(val).length == 0 ? true : false;
      break;
    case 'number':
      return val == 0;
      break;
    case 'object':
      return val == null;
      break;
    case 'array':
      return val.length == 0;
      break;
    default:
      return true;
  }
}

Utils.isNumber = function(val)
{
  var reg = /^[\d|\.|,]+$/;
  return reg.test(val);
}

Utils.isInt = function(val)
{
  if (val == "")
  {
    return false;
  }
  var reg = /\D+/;
  return !reg.test(val);
}

Utils.isEmail = function( email )
{
  var reg1 = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/;

  return reg1.test( email );
}

Utils.isTel = function ( tel )
{
  var reg = /^[\d|\-|\s|\_]+$/; //只允许使用数字-空格等

  return reg.test( tel );
}

Utils.fixEvent = function(e)
{
  var evt = (typeof e == "undefined") ? window.event : e;
  return evt;
}

Utils.srcElement = function(e)
{
  if (typeof e == "undefined") e = window.event;
  var src = document.all ? e.srcElement : e.target;

  return src;
}

Utils.isTime = function(val)
{
  var reg = /^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}$/;

  return reg.test(val);
}

Utils.x = function(e)
{ //当前鼠标X坐标
    return Browser.isIE?event.x + document.documentElement.scrollLeft - 2:e.pageX;
}

Utils.y = function(e)
{ //当前鼠标Y坐标
    return Browser.isIE?event.y + document.documentElement.scrollTop - 2:e.pageY;
}

Utils.request = function(url, item)
{
	var sValue=url.match(new RegExp("[\?\&]"+item+"=([^\&]*)(\&?)","i"));
	return sValue?sValue[1]:sValue;
}

Utils.$ = function(name)
{
    return document.getElementById(name);
}

function rowindex(tr)
{
  if (Browser.isIE)
  {
    return tr.rowIndex;
  }
  else
  {
    table = tr.parentNode.parentNode;
    for (i = 0; i < table.rows.length; i ++ )
    {
      if (table.rows[i] == tr)
      {
        return i;
      }
    }
  }
}

document.getCookie = function(sName)
{
  // cookies are separated by semicolons
  var aCookie = document.cookie.split("; ");
  for (var i=0; i < aCookie.length; i++)
  {
    // a name/value pair (a crumb) is separated by an equal sign
    var aCrumb = aCookie[i].split("=");
    if (sName == aCrumb[0])
      return decodeURIComponent(aCrumb[1]);
  }

  // a cookie with the requested name does not exist
  return null;
}

document.setCookie = function(sName, sValue, sExpires)
{
  var sCookie = sName + "=" + encodeURIComponent(sValue);
  if (sExpires != null)
  {
    sCookie += "; expires=" + sExpires;
  }

  document.cookie = sCookie;
}

document.removeCookie = function(sName,sValue)
{
  document.cookie = sName + "=; expires=Fri, 31 Dec 1999 23:59:59 GMT;";
}

function getPosition(o)
{
    var t = o.offsetTop;
    var l = o.offsetLeft;
    while(o = o.offsetParent)
    {
        t += o.offsetTop;
        l += o.offsetLeft;
    }
    var pos = {top:t,left:l};
    return pos;
}

function cleanWhitespace(element)
{
  var element = element;
  for (var i = 0; i < element.childNodes.length; i++) {
   var node = element.childNodes[i];
   if (node.nodeType == 3 && !/\S/.test(node.nodeValue))
     element.removeChild(node);
   }
}

//对象绑定
Function.prototype.bind = function(object) {
  var __method = this;
  return function()
  {
    __method.apply(object, arguments);
  }
}

//检测层是否已经存在
function docEle() 
{
  return document.getElementById(arguments[0]) || false;
}

function AddFavorite(sURL, sTitle)
{
    try
    {
        window.external.addFavorite(sURL, sTitle);
    }
    catch (e)
    {
        try
        {
            window.sidebar.addPanel(sTitle, sURL, "");
        }
        catch (e)
        {
            alert("加入收藏失败，请使用Ctrl+D进行添加");
        }
    }
}

//页面滚动
function scrollup(dest){
	dest = dest ? dest : 0;
	$body=(window.opera)? (document.compatMode=="CSS1Compat"? $('html') : $('body')) : $('html,body');
	$body.animate({scrollTop: parseInt(dest)}, 1000);
}

//查找网页内宽度太大的图片进行缩放
function ObjImgReSize(obj,mw,mh){ 
var MaxW=mw; //定义图片显示的最大宽度
var MaxH=mh; //定义图片显示的最大高度
var o=new Image();
o.src=obj.src;
var w=o.width;
var h=o.height;
var t;
if (w>MaxW){
t=MaxW;
}else{
t=w;
}
if ((h*t/w)>MaxH){
obj.height=MaxH;
obj.width=MaxH/h*w;
}else{
obj.width=t;
obj.height=t/w*h;
}
}

//查找网页内宽度太大的图片进行缩放以及PNG纠正 
function ReImgSize(obj,mw,mh){ 
var imgArr=document.getElementById(obj).getElementsByTagName("img");
for (i=0;i<imgArr.length;i++){

var MaxW=mw; //定义图片显示的最大宽度
var MaxH=mh; //定义图片显示的最大高度
var o=new Image();
o.src=imgArr[i].src;
var w=o.width;
var h=o.height;
var t;
if (w>MaxW){
t=MaxW;
}else{
t=w;
}
if ((h*t/w)>MaxH){
imgArr[i].height=MaxH;
imgArr[i].width=MaxH/h*w;
}else{
imgArr[i].width=t;
imgArr[i].height=t/w*h;
}
} 
}

//---------------------------------------------------  
// 日期格式化  
// 格式 YYYY/yyyy/YY/yy 表示年份  
// MM/M 月份  
// W/w 星期  
// dd/DD/d/D 日期  
// hh/HH/h/H 时间  
// mm/m 分钟  
// ss/SS/s/S 秒 
/*
var now = new Date();
document.write(now.Format('YYYY')+'年'+now.Format('MM')+'月'+now.Format('DD')+'日 星期'+now.Format('W')+' '+now.Format('HH')+':'+now.Format('mm')+':'+now.Format('SS'));
*/
//---------------------------------------------------   
Date.prototype.Format = function(formatStr)   
{   
var str = formatStr;   
var Week = ['日','一','二','三','四','五','六'];  
str=str.replace(/yyyy|YYYY/,this.getFullYear());   
str=str.replace(/yy|YY/,(this.getYear() % 100)>9?(this.getYear() % 100).toString():'0' + (this.getYear() % 100));   
str=str.replace(/MM/,this.getMonth()+1>9?(this.getMonth()+1).toString():'0' +(this.getMonth()+1));   
str=str.replace(/M/g,this.getMonth());   
str=str.replace(/w|W/g,Week[this.getDay()]);   
str=str.replace(/dd|DD/,this.getDate()>9?this.getDate().toString():'0' + this.getDate());   
str=str.replace(/d|D/g,this.getDate());   
str=str.replace(/hh|HH/,this.getHours()>9?this.getHours().toString():'0' + this.getHours());   
str=str.replace(/h|H/g,this.getHours());   
str=str.replace(/mm/,this.getMinutes()>9?this.getMinutes().toString():'0' + this.getMinutes());   
str=str.replace(/m/g,this.getMinutes());   
str=str.replace(/ss|SS/,this.getSeconds()>9?this.getSeconds().toString():'0' + this.getSeconds());   
str=str.replace(/s|S/g,this.getSeconds());   
return str;   
}

//获取多选框的值
function get_check_val(check_name){
	var checkboxs=document.getElementsByName(check_name);
	var res=new Array();
	for(var k=0;k<checkboxs.length;k++){
		if(checkboxs[k].checked){
			res.push(checkboxs[k].value);
		}
	}
	res=res.join(",");
	return res;
}
//全选多选框
function check_all(check_name,obj){
	var cbs=document.getElementsByName(check_name);
	if(!obj.checked){
		value=false;
	}else{
		value=true;
	}
	for(var k=0;k<cbs.length;k++){
		cbs[k].checked=value;
	}
}
//全选多选框
function check_all2(check_name,val){
	var cbs=document.getElementsByName(check_name);
	for(var k=0;k<cbs.length;k++){
		cbs[k].checked=val;
	}
}

//多选框已选择的个数
function check_count(check_name){
	var checkboxs=document.getElementsByName(check_name);
	var len=0;
	for(var k=0;k<checkboxs.length;k++){
		if(checkboxs[k].checked){
			len++;
		}
	}
	return len;
}
//获取单选框的值
function get_radio_val(check_name){
	var checkboxs=document.getElementsByName(check_name);
	var val;
	for(var k=0;k<checkboxs.length;k++){
		if(checkboxs[k].checked){
			val=checkboxs[k].value;
			break;
		}
	}
	return val;
}

//产生随机数
function generateMixed(n) {
	var chars = ['0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
     var res = "";
     for(var i = 0; i < n ; i ++) {
         var id = Math.ceil(Math.random()*35);
         res += chars[id];
     }
     return res;
}

//取标签的绝对位置
function Offset(e)
{
	var t = e.offsetTop;
	var l = e.offsetLeft;
	var w = e.offsetWidth;
	var h = e.offsetHeight-2;

	while(e=e.offsetParent)
	{
		t+=e.offsetTop;
		l+=e.offsetLeft;
	}
	return {
		top : t,
		left : l,
		width : w,
		height : h
	}
}