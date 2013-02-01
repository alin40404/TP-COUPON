<?php
class PublicAction extends CommonAction
{
	/**
	 * 系统升级
	 *
	 */
	public function upgrade()
	{
		
	}
	
	/**
	 * 获取文本内容关键词
	 *
	 */
	public function split_keywords()
	{
		$str = $_REQUEST['str'];
		if(empty($str)) $this->ajaxReturn('', '', 0);
		if(C('keywords_interface') == 'DISCUZ'){
			$keywords = SplitKeywords::discuz($str, 10);
		}else if(C('keywords_interface') == 'PHPCMS'){
			$str = iconv('utf-8', 'gbk', $str);
			$keywords = SplitKeywords::phpcms($str, 10);
			$keywords = iconv('gbk', 'utf-8', $keywords);
		}
		$this->ajaxReturn($keywords, '', 1);
	}
	
	/**
	 * 验证码
	 *
	 */
	public function verifycode()
	{
		import('@.ORG.Seccode');
		import('@.ORG.SeccodeUtil');
		@ob_end_clean(); //清除之前出现的多余输入
		$seccode = SeccodeUtil::make_seccode(CAPTCHA_CODE);//随机生成验证码内容并保存到session中
		$code = new Seccode();
		$code->root_path = APP_PATH;
		$code->code = $seccode;//验证码内容
		$code->type = 0;//验证码类型,0:英文图片、1：中文图片、2：Flash 验证码、3：语音验证码、4：位图验证码
		$code->width = $this->_CFG['captcha_width'];//验证码宽度
		$code->height = $this->_CFG['captcha_height'];//验证码高度
		$code->background = 0;//随机图片背景
		$code->adulterate = 1;//随机背景图形
		$code->ttf = 1;//验证码
		$code->angle = 0;//随机倾斜度
		$code->color = 1;//随机颜色
		$code->size = 0;//随机大小
		$code->shadow = 1;//文字阴影
		$code->animator = 1;//GIF 动画
		$code->warping = 0;//随机扭曲
		$code->fontpath = LIB_PATH . '/ORG/seccode/font/';//字体包路径
		$code->datapath = LIB_PATH . '/ORG/seccode/';//背景图片、字体、声音等文件路径
		$code->includepath = LIB_PATH . '/ORG/';
		$code->display();
	}
	
	/**
	 * xheditor Ajax上传后台处理
	 * 
	 */
	public function upload4xheditor()
	{
		$this->_chkLogin();
		import('@.ORG.Json');
		$jsonObj = new Json();
		$result = array(
						'err'	=>	'',
						'msg'	=>	'',
						);
		$upExt='txt,rar,zip,jpg,jpeg,gif,png';//上传扩展名
		$upload_path = DOC_ROOT_PATH . get_upload_path();
		$tempDir = $upload_path . 'temp/tmp/';
		if(!is_dir($tempDir)){
			mk_dir($tempDir, 0755);
		}
		$tempPath = $tempDir . date('YmdHis') . mt_rand(10000,99999) . '.tmp';
		$localName='';
		//在支持HTML5的浏览器中都将以HTML5方式上传
		if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])
			&& preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){
			file_put_contents($tempPath,file_get_contents("php://input"));
			$localName=$info[2];
		}
		//标准表单方式上传
		else{
			$upfile = @$_FILES['filedata'];
			if(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none'){
				$result['err'] = '无文件上传';
			}
			elseif(! empty($upfile['error'])){
				switch($upfile['error'])
				{
					case '1':
						$err = '文件大小超过了php.ini定义的upload_max_filesize值';
						break;
					case '2':
						$err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
						break;
					case '3':
						$err = '文件上传不完全';
						break;
					case '4':
						$err = '无文件上传';
						break;
					case '6':
						$err = '缺少临时文件夹';
						break;
					case '7':
						$err = '写文件失败';
						break;
					case '8':
						$err = '上传被其它扩展中断';
						break;
					case '999':
					default:
						$err = '无有效错误代码';
				}
				$result['err'] = $err;
			}else{
				move_upload_file($upfile['tmp_name'],$tempPath);
				$localName = $upfile['name'];
			}
		}
		if($result['err']){
			die($jsonObj->encode($result));
		}
		$fileInfo=pathinfo($localName);
		$extension=$fileInfo['extension'];
		if(! preg_match('/'.str_replace(',','|',$upExt).'/i',$extension)){
			$result['err'] = '上传文件扩展名必需为：'.$upExt;
			die($jsonObj->encode($result));
		}
		$newFilename=date("YmdHis").mt_rand(1000,9999).'.'.$extension;
		$attachDir = $upload_path . date('Ym') . '/';
		if(! is_dir($attachDir)){
			mk_dir($attachDir, 0755);
		}
		$targetPath = $attachDir.$newFilename;
		rename($tempPath,$targetPath);
		if(is_file($tempPath)){
			@unlink($tempPath);
		}
		$result['msg'] = str_replace(DOC_ROOT_PATH, '', $targetPath);
		die($jsonObj->encode($result));
	}
	
	/**
	 * uploadify2.14
	 * 批量上传文件
	 * 
	 */
	public function upload()
	{
		if(! $_POST['sid']){
			die('hack attempt.');
		}
		if(session_id() !== $_POST['sid']){
			$nowtimestamp = time();
			$timestamp = $_POST['timestamp'];
			$sessionid = $_POST['sid'];
			$authcode = $_POST['authcode'];
			$authdecode = md5($timestamp.$sessionid.C('AUTH'));
			if(($nowtimestamp - $timestamp) >= 60*10 || $authdecode !== $authcode){
				die('hack attempt.');
			}
		}else{
			$this->_chkLogin();
		}
		if (!empty($_FILES)) {
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$upload_path = DOC_ROOT_PATH . get_upload_path();
			$targetPath = $upload_path . 'temp/' . date('Ymd') . '/';
			if(! is_dir($targetPath)){
				mk_dir($targetPath,0755);
			}
			$new_file_name = $this->_new_name( $_FILES['Filedata']['name']);
			if(empty($new_file_name)){
				die('error');
			}
			$targetFile =  str_replace('//','/',$targetPath) . $new_file_name;

			move_uploaded_file($tempFile,iconv('utf-8','gbk', $targetFile));
			//echo str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
			echo str_replace(DOC_ROOT_PATH, '', $targetFile);
		}
	}
	
	private function _new_name($filename)
	{
		$ext = pathinfo($filename);
		$ext = strtolower($ext['extension']);
		if (in_array($ext, array('jpg','gif','png','bmp','jpeg','doc','docx','xls','xlsx','ppt','pptx','zip','rar')))
		{
			$name = basename($filename,$ext);
			$name = md5($name.time()).'.'.$ext;
			return $name;
		}
		else
		{
			return null;
		}
	}
	
	private function _chkLogin()
	{
		if((! isset($_SESSION['admin_user_id']) || ! $_SESSION['admin_user_id'])){
			die('pls login first.');
		}
	}
}