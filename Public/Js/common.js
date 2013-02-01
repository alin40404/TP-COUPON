// JavaScript Document
document.write('<scr'+'ipt type="text/javascript" src="'+_public_+'/Js/jquery-1.4.2.min.js"><'+'/sc'+'ript>');
document.write('<scr'+'ipt type="text/javascript" src="'+_public_+'/Js/utils.js"><'+'/sc'+'ript>');
document.write('<link type="text/css" href="'+_public_+'/Js/jquery-ui-1.8.16.custom/css/ui-lightness/jquery-ui-1.8.16.custom.css" rel="stylesheet" />');
document.write('<scr'+'ipt type="text/javascript" src="'+_public_+'/Js/jquery-ui-1.8.16.custom/jquery-ui-1.8.16.custom.min.js"><'+'/sc'+'ript>');
/**
 * 示例：
 * <link href="__PUBLIC__/Js/uploadify2.1.4/uploadify.css" rel="stylesheet" type="text/css" />
 * <script type="text/javascript" src="__PUBLIC__/Js/swfobject.js"></script>
 * <script type="text/javascript" src="__PUBLIC__/Js/uploadify2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
 * var sid = '{$sid}';
   var timestamp = '{$timestamp}';
	var authcode = '{$authcode}';
	var uploadify214_url = '__PUBLIC__/Js/uploadify2.1.4/';
	var upload_dir = '';
	var script_url = '__APP__?'+encodeURIComponent('g=Public&m=Public&a=Upload');
	var buttonImg = '__PUBLIC__/Images/Home/swfimg.gif';
	var option = {
			uploadify214_url:uploadify214_url,
			script_url:script_url,
			sid:sid,
			timestamp:timestamp,
			authcode:authcode,
			cancelImg:uploadify214_url + 'cancel.png',
			buttonImg:buttonImg,
			buttonHeight:30,
			buttonWidth:80,
			upload_dir:upload_dir,
			sizeLimit:5*1024*1024,
			auto:true,
			multi:false,
			fileQueueId:'fileQueue'
			};
	uploadifyInit('uploadify',option);
	function uploadify_complete_handler(event,queueId,fileObj,response,data)
	{
		$('#tab0>#form_share_0>.indeximg>img').attr('src', response);
		$('input#imageUrl0').val(response);
	}
 */
function uploadifyInit(objId,option)
{
	$("#"+objId).uploadify({
		'uploader'       : option.uploadify214_url + 'uploadify.swf?var=' + (new Date()).getTime(),
		'script'         : option.script_url,
		'scriptData'     : {'sid' : option.sid,'timestamp':option.timestamp,'authcode':option.authcode},
		'method'		 : 'POST',
		'cancelImg'      : option.cancelImg,
		'buttonImg'		 : option.buttonImg,
		'height'		 : option.buttonHeight,
		'width'		 	 : option.buttonWidth,
		'folder'         : option.upload_dir,
		'queueID'        : option.fileQueueId,
		'fileExt'        : '*.jpg;*.jpeg;*.gif;*.bmp;*.png', //允许文件上传类型,和fileDesc一起使用.
		'fileDesc'       : '选择文件(*.jpg;*.jpeg;*.gif;*.bmp;*.png)',  //将不允许文件类型,不在浏览对话框的出现.
		'sizeLimit'		 : option.sizeLimit,
		'auto'           : option.auto,
		'multi'          : option.multi,
		'onComplete':function(event,queueId,fileObj,response,data){
			uploadify_complete_handler(event,queueId,fileObj,response,data);
		},
		'onAllComplete' : function(event, data){
			if(option.fileQueueId != null) $('#'+option.fileQueueId).css('display','none');
		},
		'onSelect': function(event,queueId,fileObj){
			if(option.fileQueueId != null) $('#'+option.fileQueueId).css('display','none');
		},
		'onClearQueue' : function(event){
			if(option.fileQueueId != null) $('#'+option.fileQueueId).css('display','none');
		},
		'onCancel' : function(event,queueId,fileObj,data){
			if(data.fileCount == 0){
				if(option.fileQueueId != null) $('#'+option.fileQueueId).css('display','none');
			}
		},
		'onProgress' : function(event,queueId,fileObj,data){
			return false;
		}
	});
}

/**
 * 示例：
 * <script type="text/javascript" src="__PUBLIC__/Js/xheditor-1.1.10/xheditor-1.1.10-zh-cn.min.js"></script>
 * <script type="text/javascript" src="__PUBLIC__/Js/xheditor-1.1.10/xheditor_plugins/ubb.min.js"></script>
 * var upscript_url = '__APP__?g=Public&m=Public&a=upload4xheditor&immediate=1';
 * var options = {elm:'#elm1',tools:'full',upscript_url:upscript_url};
 *	   editorInit(options);
 */
function editorInit(options)
{
	var plugins={
		
	},emots={
		msn:{name:'MSN',count:40,width:22,height:22,line:8},
		pidgin:{name:'Pidgin',width:22,height:25,line:8,list:{smile:'微笑',cute:'可爱',wink:'眨眼',laugh:'大笑',victory:'胜利',sad:'伤心',cry:'哭泣',angry:'生气',shout:'大骂',curse:'诅咒',devil:'魔鬼',blush:'害羞',tongue:'吐舌头',envy:'羡慕',cool:'耍酷',kiss:'吻',shocked:'惊讶',sweat:'汗',sick:'生病',bye:'再见',tired:'累',sleepy:'睡了',question:'疑问',rose:'玫瑰',gift:'礼物',coffee:'咖啡',music:'音乐',soccer:'足球',good:'赞同',bad:'反对',love:'心',brokenheart:'伤心'}},
		ipb:{name:'IPB',width:20,height:25,line:8,list:{smile:'微笑',joyful:'开心',laugh:'笑',biglaugh:'大笑',w00t:'欢呼',wub:'欢喜',depres:'沮丧',sad:'悲伤',cry:'哭泣',angry:'生气',devil:'魔鬼',blush:'脸红',kiss:'吻',surprised:'惊讶',wondering:'疑惑',unsure:'不确定',tongue:'吐舌头',cool:'耍酷',blink:'眨眼',whistling:'吹口哨',glare:'轻视',pinch:'捏',sideways:'侧身',sleep:'睡了',sick:'生病',ninja:'忍者',bandit:'强盗',police:'警察',angel:'天使',magician:'魔法师',alien:'外星人',heart:'心动'}}
	};
	return $(options.elm).xheditor({plugins:plugins,tools:options.tools,showBlocktag:false,forcePtag:false,beforeSetSource:ubb2html,beforeGetSource:html2ubb,emots:emots,emotMark:true,upImgUrl:options.upscript_url,upImgExt:"jpg,jpeg,gif,png"});
}