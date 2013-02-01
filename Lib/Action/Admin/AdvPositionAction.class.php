<?php
/**
 * AdvPositionAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 00:08:15 CST 2012
 */
/**
 * 广告位管理
 *
 */
class AdvPositionAction extends AdminCommonAction
{
	/**
	 * 列表
	 *
	 */
	public function index()
	{
		$page = isset($_REQUEST['page']) && $_REQUEST['page'] >= 1 ? $_REQUEST['page'] : 1;
    	$pageLimit = 15;
    	$apModel = D('AdPosition');
    	$res = $apModel->getAll(array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit));
    	$positions = array();
    	foreach ($res['data'] as $rs){
    		$position = $apModel->info($rs['position_id']);
    		$positions[] = $position;
    	}
    	$this->assign('positions', $positions);
    	$page_url = "?g=".GROUP_NAME."&m=".MODULE_NAME."&a=".ACTION_NAME."&page=[page]";
    	$p=new Page($page,
    			$pageLimit,
    			$res['count'],
    			$page_url,
    			5,
    			5);
    	$pagelink=$p->showStyle(3);
    	$this->assign('pagelink', $pagelink);
		$this->assign('ur_href', '广告位管理 &gt; 广告位列表');
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
			if(! $_REQUEST['position_name'] || ! $_REQUEST['ad_width'] || ! $_REQUEST['ad_height']){
				die('data invalid.');
			}
			$apModel = D('AdPosition');
			$id = 0;
			$data = array(
						'position_name'					=>	$_REQUEST['position_name'],
						'ad_width'						=>	$_REQUEST['ad_width'],
						'ad_height'						=>	$_REQUEST['ad_height'],
						'position_style'				=>	htmlentities($_REQUEST['position_style']),
						'position_desc'					=>	$_REQUEST['position_desc'],
						);
			if($apModel->addPosition($id, $data)){
				//生成模板
				$this->assign('position_style', $_REQUEST['position_style']);
				$this->buildHtml("Adv/{$id}",'','template');
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		$this->assign('ur_href', '广告位管理 &gt; 添加广告位');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	/**
	 * 编辑
	 *
	 */
	public function edit()
	{
		$apModel = D('AdPosition');
		$position_id = intval($_REQUEST['position_id']);
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['position_name'] || ! $_REQUEST['ad_width'] || ! $_REQUEST['ad_height']){
				die('data invalid.');
			}
			$data = array(
						'position_name'					=>	$_REQUEST['position_name'],
						'ad_width'						=>	$_REQUEST['ad_width'],
						'ad_height'						=>	$_REQUEST['ad_height'],
						'position_style'				=>	htmlentities($_REQUEST['position_style']),
						'position_desc'					=>	$_REQUEST['position_desc'],
						);
			if($apModel->editPosition($position_id, $data)){
				//生成模板
				$this->assign('position_style', $_REQUEST['position_style']);
				$this->buildHtml("Adv/{$position_id}",'','template');
				//更新缓存
				$params = array('pos_id'=>$position_id);
				B('Adv', $params);
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('编辑成功');
			}else{
				$this->assign('jumpUrl', $this->_refererUrl);
				$this->error('编辑失败');
			}
		}
		$position = $apModel->info($position_id);
		$this->assign('position', $position);
		$this->assign('ur_href', '广告位管理 &gt; 编辑广告位');
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
			$position_id = intval($_REQUEST['id']);
			$apModel = D('AdPosition');
			if($apModel->del($position_id)){
				//删除模板
				if(is_file(HTML_PATH . 'Adv/'.$position_id.'.html')){
					@unlink(HTML_PATH . 'Adv/'.$position_id.'.html');
				}
				//删除所有相关的广告
				$adModel = D('Ad');
				$ads = $adModel->getAdsByPositionId($position_id);
				$upload_path = DOC_ROOT_PATH . get_upload_path();
				foreach ($ads as $a){
					$ad = $adModel->info($a['ad_id']);
					if(is_file($upload_path . $ad['ad_code'])){
						@unlink($upload_path . $ad['ad_code']);
					}
					$adModel->del($a['ad_id']);
					//更新缓存
					$params = array('ad_id'=>$a['ad_id']);
					B('Adv', $params);
				}
				//更新缓存
				$params = array('pos_id'=>$position_id);
				B('Adv', $params);
				$this->ajaxReturn('',buildFormToken(),1);
			}else{
				$this->ajaxReturn('','',0);
			}
		}
	}
}