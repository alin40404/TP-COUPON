<?php
/**
 * TempFileAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 00:18:22 CST 2012
 */
class TempFileAction extends AdminCommonAction
{
	/**
	 * 临时文件管理
	 *
	 */
	public function index()
	{
		$this->assign('ur_href', '临时文件管理');
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
	
	/**
	 * 清除今日以前的临时文件
	 *
	 */
	public function clean()
	{
		if($this->isPost() && $this->isAjax()){
			if(C('TOKEN_ON') && ! checkFormToken($_POST)){
				die('hack attemp.');
			}
			@set_time_limit(3600);
			if(function_exists('ini_set'))
			{
				ini_set('max_execution_time',3600);
				ini_set("memory_limit","256M");
			}
			$localTimeObj = LocalTime::getInstance();
			$today = $localTimeObj->local_strtotime(date('Y-m-d 00:00:00'));
			$upload_path = DOC_ROOT_PATH . get_upload_path();
			$dir = $upload_path . 'temp/';
			$dirhandle=opendir($dir);
			while(($file = readdir($dirhandle)) !== false)
			{
				if(($file!=".") && ($file!=".."))
				{
					if(filemtime($dir.$file) < $today){
						if(is_dir($dir.$file)){
							del_dir($dir.$file);
						}else{
							@unlink($dir.$file);
						}
					}
				}
			}
			@closedir($dirhandle);
			$this->ajaxReturn('', buildFormToken(), 1);
		}
	}
}