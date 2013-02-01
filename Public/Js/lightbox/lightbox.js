// JavaScript Document
//提示层
function _lightbox(id,h,w,css)
{
	h+=20;
	var Wheight=$(document).height();
	var Wwidth=$(document).width();
	var MaskDiv=document.createElement("div");
	    document.body.appendChild(MaskDiv);
		MaskDiv.id='_lightboxMask';
		MaskDiv.className='lightboxMask';
		MaskDiv.style.height=Wheight+"px";
		MaskDiv.style.width=Wwidth+"px";
	
	var _lightbox_ele = document.createElement("div");
    document.body.appendChild(_lightbox_ele);
    _lightbox_ele.id=id;
	if(css)
	{
		_lightbox_ele.className=css;
	}
	mTop="-"+(h+5)/2+"px";
	mLeft="-"+(w+5)/2+"px";
	_lightbox_ele.style.height=h+"px";
	_lightbox_ele.style.width=w+"px";
	_lightbox_ele.style.margin=mTop+" 0px 0px "+mLeft;
	_lightbox_ele.style.padding='5px';
	
	var ContentDiv=document.createElement("div");
	    _lightbox_ele.appendChild(ContentDiv);
		ContentDiv.id = id + '_content';
		ContentDiv.className='content';
		ContentDiv.style.height=h+"px";
		ContentDiv.style.width=w+"px";
	
	var CloseDiv=document.createElement("div");
	    ContentDiv.appendChild(CloseDiv);
		CloseDiv.className='close';
		CloseDiv.innerHTML='<a href="javascript:;" onclick="_lightbox_close(\''+id+'\')">关 闭</a>';

}

function _lightbox_html(html, id, h, w, css, title)
{
	if(title !== null){
		h = h - 33;
		html = '<div style="cursor: move;" class="d_header"><h4 class="title">'+title+'</h4><div class="options"></div></div>' + html;
	}
	_lightbox(id,h,w,css);
	document.getElementById(id + '_content').innerHTML = html;
}

function _lightbox_iframe(url, id, h, w, css)
{
	_lightbox(id,h,w,css);
	var lightbox_iframe_ele = document.createElement("iframe");
	document.getElementById(id + '_content').appendChild(lightbox_iframe_ele);
	lightbox_iframe_ele.setAttribute('scrolling', 'no');
	lightbox_iframe_ele.setAttribute('marginheight', 0);
	lightbox_iframe_ele.setAttribute('marginwidth', 0);
	lightbox_iframe_ele.setAttribute('frameborder', 0);
	lightbox_iframe_ele.height = h - 20;
	lightbox_iframe_ele.width = w;
	lightbox_iframe_ele.src = url;
}

function _lightbox_close(id)
{
	document.body.removeChild(document.getElementById(id));
	document.body.removeChild(document.getElementById('_lightboxMask'));
  	var i = 0;
  	var sel_obj = document.getElementsByTagName('select');
  	while (sel_obj[i])
  	{
    	sel_obj[i].style.visibility = "visible";
    	i++;
  	}
}