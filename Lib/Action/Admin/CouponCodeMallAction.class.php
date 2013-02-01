<?php
/**
 * CouponCodeMallAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 01:06:50 CST 2012
 */
/**
 * 商城
 *
 */
class CouponCodeMallAction extends AdminCommonAction
{
	private $_is_active = null;
	public function index()
	{
		$page = isset($_REQUEST['page']) && $_REQUEST['page'] >= 1 ? $_REQUEST['page'] : 1;
    	$pageLimit = 15;
    	$ccmModel = D('CouponCodeMall');
    	$cccService = service('CouponCodeCategory');
    	$params = array(
    					'c_id'		=>	isset($_REQUEST['c_id']) && $_REQUEST['c_id'] ? intval($_REQUEST['c_id']) : 0,
    					'kw'		=>	isset($_REQUEST['kw']) && $_REQUEST['kw'] ? $_REQUEST['kw'] : '',
    					'is_active' =>	$this->_is_active,
    					);
    	$keys = array('id,c_id,name,tel,website,is_active,sort_order');
    	$res = $ccmModel->getAll($keys, $params, array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit));
    	$malls = array();
    	foreach ($res['data'] as $rs){
    		$category = $cccService->info($rs['c_id']);
    		$rs['cates'] = $category['parents'];
    		$malls[] = $rs;
    	}
    	$this->assign('malls', $malls);
    	$page_url = "?g=".GROUP_NAME."&m=".MODULE_NAME."&a=".ACTION_NAME."&page=[page]";
    	foreach ($params as $key => $val){
    		$page_url .= "&$key=$val";
    	}
    	$p=new Page($page,
    			$pageLimit,
    			$res['count'],
    			$page_url,
    			5,
    			5);
    	$pagelink=$p->showStyle(3);
    	$this->assign('pagelink', $pagelink);
		$this->assign('ur_href', '商家管理 &gt; 商家列表');
		$categorys = array();
		$data = $cccService->getTree();
		foreach ($data as $rs){
			$categorys[$rs['id']] = $rs;
			$categorys[$rs['id']]['prefix'] = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$rs['level']);
		}
		$this->assign('categorys', $categorys);
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
	
	public function view()
	{
		$id = intval($_REQUEST['id']);
		$ccmModel = D('CouponCodeMall');
		$mall = $ccmModel->info($id);
		$mall or die('id invalid');
		$cccService = service('CouponCodeCategory');
		import('@.Com.Util.Ubb');
		$mall['how2use'] = Ubb::ubb2html($mall['how2use']);
		$cate = $cccService->info($mall['c_id']);
		$mall['cates'] = $cate['parents'];
		$this->assign('mall', $mall);
		$this->assign('ur_href', '商家管理 &gt; 商家详情');
		$this->display();
	}
	
	public function add()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['name'] || ! $_REQUEST['c_id']
			   || ! $_REQUEST['website'] || ! $_REQUEST['tel'] || ! $_REQUEST['description']){
				$this->error('请填写所有的必填项');
			}
			if($_FILES['logo']['size'] <= 0 && $_FILES['logo']['error'] > 0){
				$this->error('请上传LOGO');
			}
			if($_FILES['figure_image']['size'] <= 0 && $_FILES['figure_image']['error'] > 0){
				$this->error('请上传形象图');
			}
			$logo='';
			if($_FILES['logo']['size'] > 0 && $_FILES['logo']['error'] == 0){
				$upfile = array();
				$upfile = upload_one_file($_FILES['logo']);
				if($upfile['error']){
					$this->error($upfile['error']);
				}
				$logo = $upfile['file_name'];
			}
			$figure_image='';
			if($_FILES['figure_image']['size'] > 0 && $_FILES['figure_image']['error'] == 0){
				$upfile = array();
				$upfile = upload_one_file($_FILES['figure_image']);
				if($upfile['error']){
					$this->error($upfile['error']);
				}
				$figure_image = $upfile['file_name'];
			}
			$data = array(
						'c_id'			=>	intval($_REQUEST['c_id']),
						'name'			=>	$_REQUEST['name'],
						'website'		=>	$_REQUEST['website'],
						'gourl'			=>	$_REQUEST['gourl'],
						'tel'			=>	$_REQUEST['tel'],
						'description'	=>	$_REQUEST['description'],
						'how2use'		=>	$_REQUEST['how2use'],
						'logo'			=>	$logo,
						'figure_image'	=>	$figure_image,
						);
			$ccmModel = D('CouponCodeMall');
			if($ccmModel->_add($data)){
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		$categorys = array();
		$cccService = service('CouponCodeCategory');
		$data = $cccService->getTree();
		foreach ($data as $rs){
			$categorys[$rs['id']] = $rs;
			$categorys[$rs['id']]['prefix'] = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$rs['level']);
		}
		$this->assign('categorys', $categorys);
		$this->assign('ur_href', '商家管理 &gt; 添加商家');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	public function edit()
	{
		$id = intval($_REQUEST['id']);
		$ccmModel = D('CouponCodeMall');
		$mall = $ccmModel->info($id);
		$mall or die('id invalid');
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['name'] || ! $_REQUEST['c_id']
			   || ! $_REQUEST['website'] || ! $_REQUEST['tel'] || ! $_REQUEST['description']){
				$this->error('请填写所有的必填项');
			}
			$logo='';
			if($_FILES['logo']['size'] > 0 && $_FILES['logo']['error'] == 0){
				$upfile = array();
				$upfile = upload_one_file($_FILES['logo']);
				if($upfile['error']){
					$this->error($upfile['error']);
				}
				$logo = $upfile['file_name'];
			}
			$figure_image='';
			if($_FILES['figure_image']['size'] > 0 && $_FILES['figure_image']['error'] == 0){
				$upfile = array();
				$upfile = upload_one_file($_FILES['figure_image']);
				if($upfile['error']){
					$this->error($upfile['error']);
				}
				$figure_image = $upfile['file_name'];
			}
			$data = array(
						'c_id'			=>	intval($_REQUEST['c_id']),
						'name'			=>	$_REQUEST['name'],
						'website'		=>	$_REQUEST['website'],
						'gourl'			=>	$_REQUEST['gourl'],
						'tel'			=>	$_REQUEST['tel'],
						'description'	=>	$_REQUEST['description'],
						'how2use'		=>	$_REQUEST['how2use'],
						);
			if($logo){
				$data['logo'] = $logo;
			}
			if($figure_image){
				$data['figure_image'] = $figure_image;
			}
			if($ccmModel->_edit($id, $data)){
				//更新旗下所有优惠券的商家名称
				M('CouponCode')->where("m_id='$id'")->save(array('m_name'=>$_REQUEST['name']));
				M('MallPromotion')->where("m_id='$id'")->save(array('m_name'=>$_REQUEST['name']));
				$params = array('id' => $id);
				B('CouponCodeMall', $params);
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('编辑成功');
			}else{
				$this->error('编辑失败');
			}
		}
		$this->assign('mall', $mall);
		$categorys = array();
		$cccService = service('CouponCodeCategory');
		$data = $cccService->getTree();
		foreach ($data as $rs){
			$categorys[$rs['id']] = $rs;
			$categorys[$rs['id']]['prefix'] = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$rs['level']);
		}
		$this->assign('categorys', $categorys);
		$this->assign('ur_href', '商家管理 &gt; 编辑商家');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	public function del()
	{
		if($this->isAjax()){
			$id = intval($_REQUEST['id']);
			$ccmModel = D('CouponCodeMall');
			if($ccmModel->_delete($id)){
				//删除所有的优惠券、推荐信息及其他相关数据
				$codes = M('coupon_code')->field('c_id')->where("m_id='$id'")->select();
				foreach ($codes as $code){
					$c_id = $code['c_id'];
					M('coupon_code_best')->where("c_id='$c_id'")->delete();
					M('coupon_code_codes')->where("c_id='$c_id'")->delete();
					M('coupon_code_data')->where("c_id='$c_id'")->delete();
					M('coupon_code')->where("c_id='$c_id'")->delete();
				}
				M('coupon_mall_rec')->where("c_id='$id'")->delete();
				//清除缓存
				$params = array('id' => $id);
				B('CouponCodeMall', $params);
				$this->ajaxReturn('', '', 1);
			}else{
				$this->ajaxReturn('', '删除失败', 0);
			}
		}
	}
	
	/**
	 * 激活
	 *
	 */
	public function active()
	{
		if($this->isAjax()){
			$id = intval($_REQUEST['id']);
			$ccmModel = D('CouponCodeMall');
			$data = array('is_active' => 1);
			if($ccmModel->_edit($id, $data)){
				//清除缓存
				$params = array('id' => $id);
				B('CouponCodeMall', $params);
				$this->ajaxReturn('', '', 1);
			}else{
				$this->ajaxReturn('', '激活失败', 0);
			}
		}
	}
	
	/**
	 * 屏蔽
	 *
	 */
	public function unactive()
	{
		if($this->isAjax()){
			$id = intval($_REQUEST['id']);
			$ccmModel = D('CouponCodeMall');
			$data = array('is_active' => 0);
			if($ccmModel->_edit($id, $data)){
				//屏蔽旗下所有优惠券、删除推荐信息
				M("coupon_code")->where("m_id='$id'")->save(array('is_active' => 0));
				M('coupon_mall_rec')->where("c_id='$id'")->delete();
				//清除缓存
				$params = array('id' => $id);
				B('CouponCodeMall', $params);
				$params = null;
				B('CouponCodeMallRecs', $params);
				$this->ajaxReturn('', '', 1);
			}else{
				$this->ajaxReturn('', '屏蔽失败', 0);
			}
		}
	}
	
	/**
	 * 推荐
	 *
	 */
	public function rec()
	{
		$id = intval($_REQUEST['id']);
		$ccmModel = D('CouponCodeMall');
		$mall = $ccmModel->info($id);
		if($mall['is_active'] == 0){
			$this->error('该商家已被屏蔽');
		}
		if($this->isPost()){
			$position = $_REQUEST['position'];
			if(M('coupon_mall_rec')->where("c_id='$id' AND position='$position'")->find()){
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME.'&a=recs');
				$this->success('推荐成功');
			}
			$data = array(
						'c_id'			=>	$id,
						'position'		=>	$position
						);
			if(M('coupon_mall_rec')->data($data)->add()){
				//清除缓存
				$params = null;
				B('CouponCodeMallRecs', $params);
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME.'&a=recs');
				$this->success('推荐成功');
			}else{
				$this->error('操作失败');
			}
		}
		$this->assign('mall', $mall);
		$mall_rec_position_conf = CouponCodeConf::mall_rec_position_conf();
		$this->assign('mall_rec_position_conf', $mall_rec_position_conf);
		$this->assign('ur_href', '商家管理 &gt; 推荐商家');
		$this->display();
	}
	
	/**
	 * 取消推荐到首页
	 *
	 */
	public function unrec()
	{
		if($this->isAjax()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(empty($_REQUEST['id'])){
				$this->ajaxReturn('', '请选择商家', 0);
			}
			$id = $_REQUEST['id'];
			if(M('coupon_mall_rec')->where("id IN ($id)")->delete()){
				//清除缓存
				$params = null;
				B('CouponCodeMallRecs', $params);
				$this->ajaxReturn('', buildFormToken(), 1);
			}else{
				$this->ajaxReturn('', '操作失败', 0);
			}
		}
	}
	
	/**
	 * 推荐商家列表
	 *
	 */
	public function recs()
	{
		$mall_rec_position_conf = CouponCodeConf::mall_rec_position_conf();
		$ccrsService = service('CouponCodeMallRecs');
		$malls = array();
		$res = $ccrsService->getAll();
		foreach ($res as $rs){
			foreach ($rs as $r){
				$r['position'] = $mall_rec_position_conf[$r['position']];
				$malls[] = $r;
			}
		}
		$this->assign('malls', $malls);
		$this->assign('ur_href', '商家管理 &gt; 推荐商家列表');
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
	
	public function select()
	{
		$this->_is_active = 1;
		$this->index();
	}
}