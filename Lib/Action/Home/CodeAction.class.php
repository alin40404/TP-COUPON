<?php
/**
 * CodeAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Tue Apr 03 14:56:43 CST 2012
 */
class CodeAction extends HomeCommonAction
{
	/**
     * 最新优惠码
     * 
     */
	public function latest()
	{
		$page = isset($_REQUEST['p']) && $_REQUEST['p'] >= 1 ? $_REQUEST['p'] : 1;
		$pageLimit = 20;
		$localTimeObj = LocalTime::getInstance();
		$today = $localTimeObj->local_strtotime(date('Y-m-d 23:59:59'));
		$addtime = 0;
		$t_type = isset($_REQUEST['t_type']) ? intval($_REQUEST['t_type']) : 0;
		$cate_id = isset($_REQUEST['cate_id']) ? intval($_REQUEST['cate_id']) : 0;
		$cate_id2 = isset($_REQUEST['cate_id2']) ? intval($_REQUEST['cate_id2']) : 0;
		//商家分类
		$cccService = service('CouponCodeCategory');
		$cates = $cccService->getTree();
		//商家子分类
		$children = $cate_ids = array();
		if(is_array($cates[$cate_id]['childs'])){
			foreach ($cates[$cate_id]['childs'] as $v){
				$c = $cccService->info($v);
				$children[] = array('id' => $v,'name' => $c['name']);
			}
		}
		if($cate_id2 == 0){
			$cate_ids = is_array($cates[$cate_id]['childs'])
			? $cates[$cate_id]['childs']
			: array();
			$cate_ids[] = $cate_id;
			$cate_ids = implode(',', $cate_ids);
		}else{
			$cate_ids = $cate_id2;
		}
		switch ($t_type){
			case 1:
				$addtime = $localTimeObj->local_strtotime(date('Y-m-d 00:00:00'));
				break;
			case 2:
				$addtime = $localTimeObj->local_strtotime(date('Y-m-d 00:00:00', strtotime('-3 day')));
				break;
			case 3:
				$addtime = $localTimeObj->local_strtotime(date('Y-m-d 00:00:00', strtotime('-7 day')));
				break;
			case 4:
				$addtime = $localTimeObj->local_strtotime(date('Y-m-d 00:00:00', strtotime('-30 day')));
				break;
		}
		$params = array(
		'cate_id'		=>	$cate_ids,
		'addtime'		=>	$addtime
		);
		$limit = array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit);
		$codeModel = D('CouponCode');
		$res = $codeModel->front($params, $limit);
		$codes = array();
		foreach ($res['data'] as $rs){
			if($rs['expiry_type'] == 1){
				$rs['expiry_timestamp'] = $rs['expiry'] + $this->_CFG['timezone']*3600;
				if(($rs['expiry'] - $today) == 0){
					$rs['expiry'] = 1;
				}else{
					$rs['expiry'] = ($rs['expiry'] - $today) > 0 ? ceil(($rs['expiry'] - $today)/(3600*24)) : 0;
				}
			}
			$codes[] = $rs;
		}
		$this->assign('codes', $codes);
		$page_url = reUrl(MODULE_NAME."/".ACTION_NAME."?cate_id=$cate_id&t_type=$t_type&cate_id2=$cate_id2&p=[page]");
		$page_url = str_replace('%5bpage%5d', '[page]', $page_url);
		$p=new Page($page,
		$pageLimit,
		$res['count'],
		$page_url,
		5,
		5);
		$pagelink=$p->showStyle(3);
		$this->assign('pagelink', $pagelink);
		$this->assign('cates', $cates);
		$this->assign('cate_children', $children);
		$this->assign('t_type', $t_type);
		$this->assign('cate_id', $cate_id);
		$this->assign('cate_id2', $cate_id2);
		$this->assign('page_title', '最新优惠券 - ');
		$this->assign('page_keywords', $this->_CFG['site_keywords']);
		$this->assign('page_description', $this->_CFG['site_description']);
		$this->display();
	}
	
	/**
     * 热门优惠码
     * 
     */
	public function hot()
	{
		$page = isset($_REQUEST['p']) && $_REQUEST['p'] >= 1 ? $_REQUEST['p'] : 1;
		$pageLimit = 20;
		$localTimeObj = LocalTime::getInstance();
		$today = $localTimeObj->local_strtotime(date('Y-m-d 23:59:59'));
		$type = isset($_REQUEST['type']) ? intval($_REQUEST['type']) : 2;
		$cate_id = isset($_REQUEST['cate_id']) ? intval($_REQUEST['cate_id']) : 0;
		$cate_id2 = isset($_REQUEST['cate_id2']) ? intval($_REQUEST['cate_id2']) : 0;
		$order = '';
		//商家分类
		$cccService = service('CouponCodeCategory');
		$cates = $cccService->getTree();
		//商家子分类
		$children = $cate_ids = array();
		if(is_array($cates[$cate_id]['childs'])){
			foreach ($cates[$cate_id]['childs'] as $v){
				$c = $cccService->info($v);
				$children[] = array('id' => $v,'name' => $c['name']);
			}
		}
		if($cate_id2 == 0){
			$cate_ids = is_array($cates[$cate_id]['childs'])
			? $cates[$cate_id]['childs']
			: array();
			$cate_ids[] = $cate_id;
			$cate_ids = implode(',', $cate_ids);
		}else{
			$cate_ids = $cate_id2;
		}
		switch ($type){
			case 1:
				$order = 'yesterdayfetched';
				break;
			case 2:
				$order = 'dayfetched';
				break;
			case 3:
				$order = 'weekfetched';
				break;
			case 4:
				$order = 'monthfetched';
				break;
		}
		$params = array(
					'cate_id'		=>	$cate_ids,
					'order'		=>	$order
					);
		$limit = array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit);
		$cdModel = D('CouponCodeData');
		$res = $cdModel->front_hot($params, $limit);
		$codes = array();
		foreach ($res['data'] as $rs){
			if($rs['expiry_type'] == 1){
				$rs['expiry_timestamp'] = $rs['expiry'] + $this->_CFG['timezone']*3600;
				if(($rs['expiry'] - $today) == 0){
					$rs['expiry'] = 1;
				}else{
					$rs['expiry'] = ($rs['expiry'] - $today) > 0 ? ceil(($rs['expiry'] - $today)/(3600*24)) : 0;
				}
			}
			$codes[] = $rs;
		}
		$this->assign('codes', $codes);
		$page_url = reUrl(MODULE_NAME."/".ACTION_NAME."?cate_id=$cate_id&type=$type&cate_id2=$cate_id2&p=[page]");
		$page_url = str_replace('%5bpage%5d', '[page]', $page_url);
		$p=new Page($page,
		$pageLimit,
		$res['count'],
		$page_url,
		5,
		5);
		$pagelink=$p->showStyle(3);
		$this->assign('pagelink', $pagelink);
		$this->assign('cates', $cates);
		$this->assign('cate_children', $children);
		$this->assign('type', $type);
		$this->assign('cate_id', $cate_id);
		$this->assign('cate_id2', $cate_id2);
		$this->assign('page_title', '热门优惠券 - ');
		$this->assign('page_keywords', $this->_CFG['site_keywords']);
		$this->assign('page_description', $this->_CFG['site_description']);
		$this->display();
	}
	
	/**
	 * 最近被领取的优惠券
	 *
	 */
	public function lastestpulled()
	{
		$page = isset($_REQUEST['p']) && $_REQUEST['p'] >= 1 ? $_REQUEST['p'] : 1;
		$pageLimit = 20;
		$localTimeObj = LocalTime::getInstance();
		$today = $localTimeObj->local_strtotime(date('Y-m-d 23:59:59'));
		$limit = array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit);
		$cccModel = D('CouponCodeCodes');
		$res = $cccModel->fetch_lists($limit);
		$codes = array();
		foreach ($res['data'] as $rs){
			if($rs['expiry_type'] == 1){
				$rs['expiry_timestamp'] = $rs['expiry'] + $this->_CFG['timezone']*3600;
				if(($rs['expiry'] - $today) == 0){
					$rs['expiry'] = 1;
				}else{
					$rs['expiry'] = ($rs['expiry'] - $today) > 0 ? ceil(($rs['expiry'] - $today)/(3600*24)) : 0;
				}
			}
			$codes[] = $rs;
		}
		$this->assign('codes', $codes);
		$page_url = reUrl(MODULE_NAME."/".ACTION_NAME."?p=[page]");
		$page_url = str_replace('%5bpage%5d', '[page]', $page_url);
		$p=new Page($page,
		$pageLimit,
		$res['count'],
		$page_url,
		5,
		5);
		$pagelink=$p->showStyle(3);
		$this->assign('pagelink', $pagelink);
		$this->assign('page_title', '最近被领取的优惠券 - ');
		$this->assign('page_keywords', $this->_CFG['site_keywords']);
		$this->assign('page_description', $this->_CFG['site_description']);
		$this->display();
	}

	/**
     * 优惠券详情
     *
     */
	public function view()
	{
		$c_id = intval($_REQUEST['id']);
		$c_id or die('id invalid.');
		$ccModel = D('CouponCode');
		$detail = $ccModel->info($c_id);
		if(! $detail){
			$this->error('该优惠券已下架，请选择商家其他的优惠券');
		}
		if($detail['is_active'] == 0){
			$this->error('该优惠券已下架，请选择商家其他的优惠券');
		}
		$ccmService = service('CouponCodeMall');
		$mall = $ccmService->info($detail['m_id']);
		$localTimeObj = LocalTime::getInstance();
		$today = $localTimeObj->local_strtotime(date('Y-m-d 23:59:59'));
		if($detail['expiry_type'] == 1){
			$detail['expiry_timestamp'] = $detail['expiry'] + $this->_CFG['timezone']*3600;
			if(($detail['expiry'] - $today) == 0){
				$detail['expiry'] = 1;
			}else{
				$detail['expiry'] = ($detail['expiry'] - $today) > 0 ? ceil(($detail['expiry'] - $today)/(3600*24)) : 0;
			}
		}
		$title = '';
		if($detail['title']){
			$title .= $detail['title'];
		}else{
			$title .= $mall['name'];
			if($detail['c_type'] ==1){
				$title .= '满'.$detail['money_max'].'减'.$detail['money_reduce'].'元优惠券';
			}else{
				$title .= $detail['money_amount'] . '元代金券';
			}
		}
		$detail['title'] = $title;
		import('@.Com.Util.Ubb');
		$detail['data']['directions'] = Ubb::ubb2html($detail['data']['directions']);
		$detail['data']['prompt'] = Ubb::ubb2html($detail['data']['prompt']);
		$mall['description'] = Ubb::ubb2html($mall['description']);
		$mall['how2use'] = Ubb::ubb2html($mall['how2use']);
		$this->assign('detail', $detail);
		$this->assign('mall', $mall);
		//前100位领取此优惠券的会员
		$cccModel = D('CouponCodeCodes');
		$record_top100 = $cccModel->record_top($c_id, 100);
		$record_top100 = $record_top100 ? $record_top100 : array();
		$this->assign('record_top100', $record_top100);
		$this->assign('record_top_amount', count($record_top100));
		$this->assign('nowtime', $localTimeObj->gmtime()-intval($this->_CFG['code_in_secret'])*60+intval($this->_CFG['timezone'])*3600);
		$this->assign('page_title', $title . ' - ');
		$this->assign('page_keywords', $this->_CFG['site_keywords']);
		$this->assign('page_description', $this->_CFG['site_description']);
		$this->display();
	}

	/**
     * 领取优惠券
     *
     */
	public function pull()
	{
		if($this->isAjax()){
			$this->_check_login();
			$c_id = intval($_REQUEST['c_id']);
			$c_id or die('id invalid.');
			$ccModel = D('CouponCode');
			$detail = $ccModel->info($c_id);
			$detail or die('id invalid.');
			if($detail['is_active'] == 0){
				$this->ajaxReturn('', '该优惠券已下架，请选择商家其他的优惠券', 0);
			}
			$localTimeObj = LocalTime::getInstance();
			$nowtime = $localTimeObj->gmtime();
			$today = $localTimeObj->local_strtotime(date('Y-m-d 23:59:59'));
			//是否过期
			if($detail['expiry_type'] == 1 && $detail['expiry'] < $today){
				$this->ajaxReturn('', '该优惠券已过期，请选择商家其他的优惠券', 0);
			}
			//剩余数量
			if($detail['fetched_amount'] >= $detail['amount']){
				$this->ajaxReturn('', '该优惠券已发放完毕，请选择其他的优惠券', 0);
			}
			//领取限制
			$cccModel = D('CouponCodeCodes');
			//每个账户一张
			if($detail['data']['fetch_limit'] == 101){
				if($cccModel->getOneByUid($this->_user['user_id'], $c_id)){
					$this->ajaxReturn('', '您已领取过该优惠券，请选择其他的优惠券', 0);
				}
			}
			//每个账户每天一张
			else if($detail['data']['fetch_limit'] == 102){
				$b_time = $localTimeObj->local_strtotime(date('Y-m-d 00:00:00'));
				$e_time = $today;
				$params = array(
								'b_time'	=>	$b_time,
								'e_time'	=>	$e_time
								);
				if($cccModel->getOneByUid($this->_user['user_id'], $c_id, $params)){
					$this->ajaxReturn('', '您今天已领取过该优惠券，请选择其他的优惠券', 0);
				}
			}
			//付费情况
			if($detail['price_type'] != 1){
				$userModel = D('User');
				$user = $userModel->info($this->_user['user_id'], array('money', 'credit'));
				//付费
				if($detail['price_type'] == 2){
					if($user['money'] < $detail['price']){
						$this->ajaxReturn('', '您的账户金额不足，请先到帐号中心充值再来购买。请点击<a href="'.reUrl('Payment/pay').'" target="_blank">在线充值</a>', 0);
					}
					$spend = Consume::spend($this->_user['user_id'], $detail['price'], Consume::TYPE_MONEY);
				}
				//积分
				else if($detail['price_type'] == 3){
					if($user['credit'] < $detail['price']){
						$this->ajaxReturn('', '您的账户积分不足，请选择其他的优惠券', 0);
					}
					$spend = Consume::spend($this->_user['user_id'], $detail['price'], Consume::TYPE_CREDIT);
				}
				if($spend !== 1){
					$this->ajaxReturn('', '支付失败，请重试', 0);
				}
			}
			$code = $cccModel->pull($c_id, $this->_user['user_id'], $this->_user['nick'], $nowtime);
			if($code){
				//更新领取数量
				$ccModel->update($c_id, array('fetched_amount'=>($detail['fetched_amount']+1)));
				//更新昨日、今日、本周、本月等领取数量
				$yestoday = $nowtime-24*3600;
				$ccdModel = D('CouponCodeData');
				$r = $ccdModel->info($c_id, array('yesterdayfetched', 'dayfetched', 'weekfetched', 'monthfetched', 'updatetime'));
				$yesterdayfetched = (date('Ymd', $r['updatetime']) == date('Ymd', $yestoday)) ? $r['dayfetched'] : $r['yesterdayfetched'];
				$dayfetched = (date('Ymd', $r['updatetime']) == date('Ymd', $nowtime)) ? ($r['dayfetched'] + 1) : 1;
				$weekfetched = (date('YW', $r['updatetime']) == date('YW', $nowtime)) ? ($r['weekfetched'] + 1) : 1;
				$monthfetched = (date('Ym', $r['updatetime']) == date('Ym', $nowtime)) ? ($r['monthfetched'] + 1) : 1;
				$data = array(
							'yesterdayfetched'		=>	$yesterdayfetched,
							'dayfetched'			=>	$dayfetched,
							'weekfetched'			=>	$weekfetched,
							'monthfetched'			=>	$monthfetched,
							'updatetime'			=>	$nowtime
							);
				$ccdModel->update($c_id, $data);
				//发表一条微博
				if(($this->_CFG['sina_wb_open'] && $_SESSION['sina']['token']['access_token'])
					|| ($this->_CFG['qq_open'] && $_SESSION['qq']["access_token"])){
					$ccmService = service('CouponCodeMall');
					$mall = $ccmService->info($detail['m_id']);
					$title = '';
					if($detail['title']){
						$title .= $detail['title'];
					}else{
						$title .= $mall['name'];
						if($detail['c_type'] ==1){
							$title .= '满'.$detail['money_max'].'减'.$detail['money_reduce'].'元优惠券';
						}else{
							$title .= $detail['money_amount'] . '元代金券';
						}
					}
					$url = 'http://' . $_SERVER['HTTP_HOST'] . reUrl('Code/view?id='.$c_id);
					$pic_path = 'http://' . $_SERVER['HTTP_HOST'] . FixedUploadedFileUrl($mall['figure_image']);
					$text = '我刚刚在#'.$this->_CFG['site_name'].'#领取了一张【'.$title.'】，数量有限，抢完为止，一般人我不告诉！'.$url;
					if($this->_CFG['sina_wb_open'] && $_SESSION['sina']['token']['access_token']){
						include_once( DOC_ROOT_PATH . 'Addons/plugins/login/sina.class.php' );
						$sina = new sina();
						$sina->upload($text, $pic_path);
					}else if ($this->_CFG['qq_open'] && $_SESSION['qq']["access_token"]){
						include_once( DOC_ROOT_PATH . 'Addons/plugins/login/qq.class.php' );
						$qq = new qq();
						//发送微博
						$qq->add_t($text);
						//发送空间分享
						$title = '我刚刚在'.$this->_CFG['site_name'].'领取了一张【'.$title.'】，数量有限，抢完为止，一般人我不告诉！';
						$site = $_SERVER['HTTP_HOST'];
						$qq->add_share($title, $url, $site, $pic_path);
					}
				}
				$this->ajaxReturn(array('code'=>$code), '领取成功', 1);
			}else{
				$this->ajaxReturn('', '领取失败', 0);
			}
		}
	}
}