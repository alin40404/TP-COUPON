<?php
/**
 * AdvAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 00:05:58 CST 2012
 */
/**
 * 广告管理
 *
 */
class AdvAction extends AdminCommonAction
{
	private $_ad_media_type_conf;
	
	protected function _initialize()
	{
		parent::_initialize();
		$this->_ad_media_type_conf = AdTypeConf::ad_type_conf();
	}
	
	/**
	 * 列表
	 *
	 */
	public function index()
	{
		$page = isset($_REQUEST['page']) && $_REQUEST['page'] >= 1 ? $_REQUEST['page'] : 1;
    	$pageLimit = 15;
    	$apModel = D('AdPosition');
    	$adModel = D('Ad');
    	$localTimeObj = LocalTime::getInstance();
    	$res = $adModel->getAll(array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit));
    	$ads = array();
    	foreach ($res['data'] as $rs){
    		$ad = $adModel->info($rs['ad_id']);
    		$position = $apModel->info($ad['position_id']);
    		$ad['ad_position'] = $position['position_name'];
    		$ad['media_type'] = $this->_ad_media_type_conf[$ad['media_type']];
    		$ad['start_time'] = $localTimeObj->local_date($this->_CFG['time_format'], $ad['start_time']);
    		$ad['end_time'] = $localTimeObj->local_date($this->_CFG['time_format'], $ad['end_time']);
    		$ads[] = $ad;
    	}
    	$this->assign('ads', $ads);
    	$page_url = "?g=".GROUP_NAME."&m=".MODULE_NAME."&a=".ACTION_NAME."&page=[page]";
    	$p=new Page($page,
    			$pageLimit,
    			$res['count'],
    			$page_url,
    			5,
    			5);
    	$pagelink=$p->showStyle(3);
    	$this->assign('pagelink', $pagelink);
		$this->assign('ur_href', '广告管理 &gt; 广告列表');
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
	
	/**
	 * 添加
	 *
	 */
	public function add()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['ad_name'] || ! $_REQUEST['start_time'] || ! $_REQUEST['end_time']){
				die('data invalid.');
			}
			$media_type = intval($_REQUEST['media_type']);
			$localTimeObj = LocalTime::getInstance();
			$start_time = $localTimeObj->local_strtotime($_REQUEST['start_time']);
			$end_time = $localTimeObj->local_strtotime($_REQUEST['end_time']);
			$adModel = D('Ad');
			$id = 0;
			$data = array(
						'ad_name'						=>	$_REQUEST['ad_name'],
						'media_type'					=>	$media_type,
						'position_id'					=>	intval($_REQUEST['position_id']),
						'enabled'						=>	intval($_REQUEST['enabled']),
						'start_time'					=>	$start_time,
						'end_time'						=>	$end_time
						);
			//图片广告
			if($media_type == 101){
				if(! $_REQUEST['ad_link'] || (! $_REQUEST['img_url']
				 && (! $_FILES['ad_img'] || $_FILES['ad_img']['size'] == 0 || $_FILES['ad_img']['error'] != 0))){
					die('data invalid.');
				}
				$data['ad_link'] = $_REQUEST['ad_link'];
				if($_REQUEST['img_url']){
					$data['ad_code'] = $_REQUEST['img_url'];
				}
				if($_FILES['ad_img'] && $_FILES['ad_img']['size'] > 0 && $_FILES['ad_img']['error'] == 0){
					$upfile = array();
					$upfile = upload_one_file($_FILES['ad_img']);
					if($upfile['error']){
						$this->error($upfile['error']);
					}
					$data['ad_code'] = $upfile['file_name'];
				}
			}
			//Flash广告
			elseif($media_type == 102){
				if(! $_REQUEST['flash_url'] && (! $_FILES['upfile_flash']
				 || $_FILES['upfile_flash']['size'] == 0 || $_FILES['upfile_flash']['error'] != 0)){
					die('data invalid.');
				}
				if($_REQUEST['flash_url']){
					$data['ad_code'] = $_REQUEST['flash_url'];
				}
				if($_FILES['upfile_flash'] && $_FILES['upfile_flash']['size'] > 0 && $_FILES['upfile_flash']['error'] == 0){
					$upfile = array();
					$upfile = upload_one_file($_FILES['upfile_flash']);
					if($upfile['error']){
						$this->error($upfile['error']);
					}
					$data['ad_code'] = $upfile['file_name'];
				}
			}
			//代码
			elseif($media_type == 103){
				if(! $_REQUEST['ad_code']){
					die('data invalid.');
				}
				$data['ad_code'] = htmlentities($_REQUEST['ad_code']);
			}
			//文字
			elseif($media_type == 104){
				if(! $_REQUEST['ad_link2'] || ! $_REQUEST['ad_text']){
					die('data invalid.');
				}
				$data['ad_link'] = $_REQUEST['ad_link2'];
				$data['ad_code'] = $_REQUEST['ad_text'];
			}
			if($adModel->addAd($id, $data)){
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		$apModel = D('AdPosition');
    	$res = $apModel->getPositions();
    	$positions = array();
    	foreach ($res as $rs){
    		$position = $apModel->info($rs['position_id']);
    		$positions[] = $position;
    	}
    	$this->assign('positions', $positions);
    	$this->assign('ad_media_type_conf', $this->_ad_media_type_conf);
		$this->assign('ur_href', '广告管理 &gt; 添加广告');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	/**
	 * 编辑
	 *
	 */
	public function edit()
	{
		$adModel = D('Ad');
		$ad_id = intval($_REQUEST['ad_id']);
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['ad_name'] || ! $_REQUEST['start_time'] || ! $_REQUEST['end_time']){
				die('data invalid.');
			}
			$media_type = intval($_REQUEST['media_type']);
			$localTimeObj = LocalTime::getInstance();
			$start_time = $localTimeObj->local_strtotime($_REQUEST['start_time']);
			$end_time = $localTimeObj->local_strtotime($_REQUEST['end_time']);
			$upload_path = get_upload_path();
			$file_prefix = 'http://'.$_SERVER['HTTP_HOST'] . __ROOT__ .'/'. $upload_path;
			$adModel = D('Ad');
			$data = array(
						'ad_name'						=>	$_REQUEST['ad_name'],
						'media_type'					=>	$media_type,
						'position_id'					=>	intval($_REQUEST['position_id']),
						'enabled'						=>	intval($_REQUEST['enabled']),
						'start_time'					=>	$start_time,
						'end_time'						=>	$end_time
						);
			//图片广告
			if($media_type == 101){
				if(! $_REQUEST['ad_link'] || (! $_REQUEST['img_url']
				 && (! $_FILES['ad_img'] || $_FILES['ad_img']['size'] == 0 || $_FILES['ad_img']['error'] != 0))){
					die('data invalid.');
				}
				$data['ad_link'] = $_REQUEST['ad_link'];
				if($_REQUEST['img_url']){
					$data['ad_code'] = str_replace($file_prefix,'',$_REQUEST['img_url']);
				}
				if($_FILES['ad_img'] && $_FILES['ad_img']['size'] > 0 && $_FILES['ad_img']['error'] == 0){
					$upfile = array();
					$upfile = upload_one_file($_FILES['ad_img']);
					if($upfile['error']){
						$this->error($upfile['error']);
					}
					$data['ad_code'] = $upfile['file_name'];
				}
			}
			//Flash广告
			elseif($media_type == 102){
				if(! $_REQUEST['flash_url'] && (! $_FILES['upfile_flash']
				 || $_FILES['upfile_flash']['size'] == 0 || $_FILES['upfile_flash']['error'] != 0)){
					die('data invalid.');
				}
				if($_REQUEST['flash_url']){
					$data['ad_code'] = str_replace($file_prefix,'',$_REQUEST['flash_url']);
				}
				if($_FILES['upfile_flash'] && $_FILES['upfile_flash']['size'] > 0 && $_FILES['upfile_flash']['error'] == 0){
					$upfile = array();
					$upfile = upload_one_file($_FILES['upfile_flash']);
					if($upfile['error']){
						$this->error($upfile['error']);
					}
					$data['ad_code'] = $upfile['file_name'];
				}
			}
			//代码
			elseif($media_type == 103){
				if(! $_REQUEST['ad_code']){
					die('data invalid.');
				}
				$data['ad_code'] = htmlentities($_REQUEST['ad_code']);
			}
			//文字
			elseif($media_type == 104){
				if(! $_REQUEST['ad_link2'] || ! $_REQUEST['ad_text']){
					die('data invalid.');
				}
				$data['ad_link'] = $_REQUEST['ad_link2'];
				$data['ad_code'] = $_REQUEST['ad_text'];
			}
			if($adModel->editAd($ad_id, $data)){
				//更新缓存
				$params = array('ad_id'=>$ad_id);
				B('Adv', $params);
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('编辑成功');
			}else{
				$this->error('编辑失败');
			}
		}
		$localTimeObj = LocalTime::getInstance();
		$ad = $adModel->info($ad_id);
		$ad['start_time'] = $localTimeObj->local_date($this->_CFG['time_format'], $ad['start_time']);
		$ad['end_time'] = $localTimeObj->local_date($this->_CFG['time_format'], $ad['end_time']);
		$upload_path = get_upload_path();
		if(is_file(DOC_ROOT_PATH . $upload_path . $ad['ad_code'])){
			//$upload_path = str_replace('./','/',$upload_path);
			$ad['ad_code'] = 'http://'.$_SERVER['HTTP_HOST'] . FixedUploadedFileUrl($ad['ad_code']);
		}
		$this->assign('ad', $ad);
		$apModel = D('AdPosition');
    	$res = $apModel->getPositions();
    	$positions = array();
    	foreach ($res as $rs){
    		$position = $apModel->info($rs['position_id']);
    		$positions[] = $position;
    	}
    	$this->assign('positions', $positions);
    	$this->assign('ad_media_type_conf', $this->_ad_media_type_conf);
		$this->assign('ur_href', '广告管理 &gt; 编辑广告');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	/**
	 * 删除
	 *
	 */
	public function del()
	{
		if($this->isAjax()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			$ad_id = intval($_REQUEST['ad_id']);
			$adModel = D('Ad');
			$ad = $adModel->info($ad_id);
			if($adModel->del($ad_id)){
				$upload_path = DOC_ROOT_PATH . get_upload_path();
				if(is_file($upload_path . $ad['ad_code'])){
					@unlink($upload_path . $ad['ad_code']);
				}
				//更新缓存
				$params = array('ad_id'=>$ad_id);
				B('Adv', $params);
				$this->ajaxReturn('',buildFormToken(),1);
			}else{
				$this->ajaxReturn('','',0);
			}
		}
	}
}