<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($_CFG["site_name"]); ?> — 后台管理系统</title>
<link rel="shortcut icon" href="favicon.ico" />
<link href="__PUBLIC__/Css/Admin/login.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">var _public_='__PUBLIC__';</script>
<script type="text/javascript" src="__PUBLIC__/Js/common.js"></script>
<style type="text/css">
<!--{literal}-->
input{ height:25px; line-height:25px; font-size:14px;}
<!--{/literal}-->
</style>
<script type="text/javascript">
<!--{literal}-->
//指定当前组模块URL地址 
var AJAX_LOADING = '提交请求中，请稍候...';
var AJAX_ERROR = 'AJAX请求发生错误！';
<!--{/literal}-->
</script>
</head>
<body>
<form method='post' name="login" id="login" action="?g=<?php echo ($smarty["const"]["GROUP_NAME"]); ?>&m=<?php echo ($smarty["const"]["MODULE_NAME"]); ?>&a=<?php echo ($smarty["const"]["ACTION_NAME"]); ?>" >
<div id="login-box">
	<div id="resultMsg"></div>
    <table width="300" border="0" cellspacing="0" cellpadding="0" style="position:absolute; left:300px; top:100px;">
  <tr>
    <td width="90" align="right" height="40" valign="middle">帐号：</td>
    <td valign="middle"><input type="text" name="admin_name" /></td>
  </tr>
  <tr>
    <td align="right" height="40" valign="middle">密码：</td>
    <td valign="middle"><input type="password" name="admin_pwd" /></td>
  </tr>
  <!--{if $captcha}-->
  <tr>
    <td align="right" height="50" valign="middle">验证码：</td>
    <td valign="middle"><input type="text" name="verify" size="8" />
	<img src="?g=Public&m=Public&a=verifycode&mt=<?php echo ($mt); ?>" align="absmiddle" alt="captcha" height="<?php echo ($_CFG["captcha_height"]); ?>" width="<?php echo ($_CFG["captcha_width"]); ?>" style="vertical-align: middle;cursor: pointer;" onClick="this.src='?g=Public&m=Public&a=verifycode&mt='+Math.random()" /></td>
  </tr>
  <!--{/if}-->
  <tr>
    <td>&nbsp;</td>
    <td><input type="image" src="__PUBLIC__/Css/Admin/Images/login_btn.png" /></td>
  </tr>
</table>
</div>
</form>
</body>
<script type="text/javascript">
var login_url = '?g=<?php echo ($smarty["const"]["GROUP_NAME"]); ?>&m=<?php echo ($smarty["const"]["MODULE_NAME"]); ?>&a=<?php echo ($smarty["const"]["ACTION_NAME"]); ?>&ajax=1';
var index_url = '?g=<?php echo ($smarty["const"]["GROUP_NAME"]); ?>';
var captcha = {if $captcha}true{else}false{/if};
<!--{literal}-->
jQuery(function($){
	if(top.location != self.location)
	{
		top.location.href = self.location.href;
		return;
	}
	
	$(document).keypress(function(e){
		if(e.keyCode == 13)
		{
			login()
		}
	});
	
	$("#login").submit(function(){
		login();
		return false;
	});
});

function login()
{
	$("#resultMsg").stop().removeClass('error').addClass('loading').html(AJAX_LOADING).show();
	
	$.ajax({
		url: login_url,
		type:"POST",
		cache: false,
		data:$("#login").serialize(),
		dataType:"json",
		error: function(){
			$("#resultMsg").addClass('error').html(AJAX_ERROR).show().fadeOut(5000);
		},
		success: function(result){
			$("#resultMsg").hide();
			if(result.status==1)
				location.href = index_url;
			else
			{
				$("#resultMsg").addClass('error').html(result.info).show().fadeOut(5000);
				if(captcha){
					fleshVerify();
				}
			}
		}
	});
}

function _login()
{
	$("#resultMsg").stop().removeClass('error').addClass('loading').html(AJAX_LOADING).show();
	var admin_name = $('#admin_name').val();
	var admin_pwd = $('#admin_pwd').val();
	if(admin_name == ''){
		$("#resultMsg").addClass('error').html('用户名不能为空').show().fadeOut(5000);
		return false;
	}
	if(admin_pwd == ''){
		$("#resultMsg").addClass('error').html('密码不能为空').show().fadeOut(5000);
		return false;
	}
	var url = login_url + '&admin_name='+encodeURIComponent(admin_name)+'&admin_pwd='+admin_pwd;
	if(captcha){
		var verify = $('#verify').val();
		if(verify == ''){
			$("#resultMsg").addClass('error').html('验证码不能为空').show().fadeOut(5000);
			return false;
		}
		url += '&verify='+verify;
	}
	$.getJSON(url, function(data){
									$("#resultMsg").hide();
									if(data.status == 1){
										location.href = index_url;
									}else{
										$("#resultMsg").addClass('error').html(result.info).show().fadeOut(5000);
										if(captcha){
											fleshVerify();
										}
									}
									});
}

function fleshVerify()
{
	var time = new Date().getTime();
	$("#verifyImg").attr('src',"?g=Public&m=Public&a=verifycode&mt="+time);
}
<!--{/literal}-->
</script>
</html>