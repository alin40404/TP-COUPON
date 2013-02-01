<?php
/**
 * CityAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 00:10:56 CST 2012
 */
/**
 * 城市
 *
 */
class CityAction extends AdminCommonAction
{
	/**
	 * 城市列表
	 *
	 */
	public function index()
	{
		$auModel = D('AdminUsers');
		$res = D('City')->getAllCity();
		$citys = array();
		foreach ($res as $c){
			if($c['admin_uid']){
				$u = $auModel->info($c['admin_uid']);
				$c['admin_uname'] = $u['user_name'];
			}
			$citys[] = $c;
		}
		$this->assign('citys', $citys);
		$this->assign('ur_href', '城市管理 &gt; 城市列表');
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
			if(C('TOKEN_ON') && ! checkFormToken($_POST)){
				die('hack attemp.');
			}
			if(! $_POST['code'] || ! $_POST['name'] || !$_POST['sort_order']){
				die('data invalid.');
			}
			$_POST['sort_order'] = intval($_POST['sort_order']);
			$cityModel = D('City');
			$id = 0;
			if($cityModel->add_city($id, $_POST)){
				if($_POST['admin_uid']){
					$auModel = D('AdminUsers');
					$auModel->edit_user($_POST['admin_uid'], array('city' => $_POST['code']));
				}
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('添加成功.');
			}else{
				$this->error('添加失败');
			}
		}
		$this->assign('ur_href', '城市管理 &gt; 添加城市');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	/**
	 * 编辑
	 *
	 */
	public function edit()
	{
		$id = intval($_REQUEST['id']);
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_POST)){
				die('hack attemp.');
			}
			if(! $_POST['code'] || ! $_POST['name'] || !$_POST['sort_order']){
				die('data invalid.');
			}
			$_POST['sort_order'] = intval($_POST['sort_order']);
			unset($_POST['id']);
			$cityModel = D('City');
			$city = $cityModel->info($id);
			if($cityModel->edit_city($id, $_POST)){
				$auModel = D('AdminUsers');
				//将原站长的城市属性清除
				if($city['admin_uid'] && $city['admin_uid'] != $_POST['admin_uid']){
					$auModel->edit_user($city['admin_uid'], array('city' => ''));
				}
				if($_POST['admin_uid']){
					$auModel->edit_user($_POST['admin_uid'], array('city' => $_POST['code']));
				}
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('编辑成功.');
			}else{
				$this->error('编辑失败');
			}
		}
		$cityModel = D('City');
		$city = $cityModel->info($id);
		$this->assign('city', $city);
		$this->assign('ur_href', '城市管理 &gt; 编辑城市');
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
			$id = intval($_REQUEST['id']);
			$cityModel = D('City');
			if($cityModel->del_city($id)){
				$this->ajaxReturn('',buildFormToken(),1);
			}else{
				$this->ajaxReturn('','',0);
			}
		}
	}
	
	public function areas()
	{
		$id = intval($_REQUEST['id']);
		$cModel = D('City');
		$city = $cModel->info($id);
		$this->assign('city', $city);
		$this->assign('city_id', $id);
		$this->assign('ur_href', '城市管理 &gt; 区域列表');
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
	
	/**
	 * 添加区域
	 *
	 */
	public function add_area()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_POST)){
				die('hack attemp.');
			}
			if(! $_POST['code'] || ! $_POST['name'] || !$_POST['sort_order']){
				die('data invalid.');
			}
			$_POST['sort_order'] = intval($_POST['sort_order']);
			$_POST['level']	=	2;
			$cityModel = D('City');
			$id = 0;
			if($cityModel->add_city($id, $_POST)){
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME.'&a=areas&id='.$_POST['parent_id']);
				$this->success('添加成功.');
			}else{
				$this->error('添加失败');
			}
		}
		$parent_id = intval($_REQUEST['city_id']);
		$cModel = D('City');
		$parent_city_info = $cModel->info($parent_id);
		$this->assign('parent_city_info', $parent_city_info);
		$this->assign('ur_href', '城市管理 &gt; 添加区域');
		$this->assign('_hash_', buildFormToken());
		$this->display('post_area');
	}
	
	/**
	 * 编辑区域
	 *
	 */
	public function edit_area()
	{
		$cModel = D('City');
		$city_id = intval($_REQUEST['id']);
		$city = $cModel->info($city_id);
		if(! $city){
			die('id invalid.');
		}
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_POST)){
				die('hack attemp.');
			}
			if(! $_POST['code'] || ! $_POST['name'] || !$_POST['sort_order']){
				die('data invalid.');
			}
			$_POST['sort_order'] = intval($_POST['sort_order']);
			$data = array(
						'code'		=>	$_POST['code'],
						'name'		=>	$_POST['name'],
						'sort_order'=>	$_POST['sort_order'],
						);
			$cityModel = D('City');
			if($cityModel->edit_city($city_id, $data)){
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME.'&a=areas&id='.$_POST['parent_id']);
				$this->success('编辑成功.');
			}else{
				$this->error('编辑失败');
			}
		}
		$parent_city_info = $cModel->info($city['parent_id']);
		$this->assign('parent_city_info', $parent_city_info);
		$this->assign('city', $city);
		$this->assign('ur_href', '城市管理 &gt; 编辑区域');
		$this->assign('_hash_', buildFormToken());
		$this->display('post_area');
	}
	
	/**
	 * 删除区域
	 *
	 */
	public function del_area()
	{
		if($this->isAjax()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			$id = intval($_REQUEST['id']);
			$cityModel = D('City');
			if($cityModel->del_city($id)){
				$this->ajaxReturn('',buildFormToken(),1);
			}else{
				$this->ajaxReturn('','',0);
			}
		}
	}
	
	/**
	 * 验证编码是否重复
	 *
	 */
	public function check_code_valid()
	{
		if($this->isAjax()){
			$parent_id = $_REQUEST['parent_id'] ? intval($_REQUEST['parent_id']) : null;
			$cModel = D('City');
			$city = $cModel->infoByCode($_REQUEST['code']);
			if($parent_id){
				if($city && $city['parent_id'] == $parent_id && $city['id'] != intval($_REQUEST['id'])){
					$this->ajaxReturn(array('is_exist'=>1),'',1);
				}else{
					$this->ajaxReturn(array('is_exist'=>0),'',1);
				}
			}else{
				if($city && $city['parent_id'] == 0 && $city['id'] != intval($_REQUEST['id'])){
					$this->ajaxReturn(array('is_exist'=>1),'',1);
				}else{
					$this->ajaxReturn(array('is_exist'=>0),'',1);
				}
			}
		}
	}
	
	/**
	 * Ajax方式获取城市的区域数据
	 *
	 */
	public function get_city_areas()
	{
		if($this->isAjax()){
			$cityCode = $_REQUEST['cityCode'];
			$cityModel = D('City');
			$areas = $cityModel->getAreasByCityCode($cityCode);
			$this->ajaxReturn($areas, '', 1);
		}
	}
}