<?php
/**
 * SysConfAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 00:15:04 CST 2012
 */
class SysConfAction extends AdminCommonAction
{
	/**
	 * 系统设置
	 *
	 */
	public function setting()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_POST)){
				die('hack attemp.');
			}
			/* 保存变量值 */
			$count = count($_POST['value']);
			$configModel = M('site_config');
			$arr = array();
			$res = $configModel->field('id,value')->select();
			foreach ($res as $row){
				$arr[$row['id']] = $row['value'];
			}
			foreach ($_POST['value'] AS $key => $val)
			{
				if($arr[$key] != $val)
				{
					$configModel->where("id=$key")->save(array('value'=>trim($val)));
				}
			}
			F('site_config', null);
			$this->success('编辑成功');
		}
		/* 可选语言 */
		$_LANG = L('SysConfig');
		$lang_list = array();
		$this->assign('group_list',   $this->_get_settings(null, null, $_LANG));
		$this->assign('lang_list',    array());
		$this->assign('lang',    $_LANG);
		$this->assign('ur_href', '系统管理 &gt; 系统设置');
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
	
	public function del_sysconfigcode()
	{
		/* 检查权限 */

		/* 取得参数 */
		$code          = trim($_GET['code']);

		$filename =  $this->_CFG[$code];

		//更新设置
		$this->_update_configure($code, '');

		/*
		del_dir(C('DATA_CACHE_PATH'));
        mk_dir(C('DATA_CACHE_PATH'));
        */
		F('site_config', null);
		$this->success('编辑成功');
	}
	
	/**
	 * 获得设置信息
	 *
	 * @param   array   $groups     需要获得的设置组
	 * @param   array   $excludes   不需要获得的设置组
	 *
	 * @return  array
	 */
	private function _get_settings($groups=null, $excludes=null, $_LANG)
	{
		$config_groups = '';
		$excludes_groups = '';

		if (!empty($groups))
		{
			foreach ($groups AS $key=>$val)
			{
				$config_groups .= " AND (id='$val' OR parent_id='$val')";
			}
		}

		if (!empty($excludes))
		{
			foreach ($excludes AS $key=>$val)
			{
				$excludes_groups .= " AND (parent_id<>'$val' AND id<>'$val')";
			}
		}

		/* 取出全部数据：分组和变量 */
		$where = "`type`<>'hidden' $config_groups $excludes_groups";
		$item_list = M('site_config')->where($where)->order('parent_id, sort_order, id')->select();

		/* 整理数据 */
		$group_list = array();
		foreach ($item_list AS $key => $item)
		{
			$pid = $item['parent_id'];
			$item['name'] = isset($_LANG['cfg_name'][$item['code']]) ? $_LANG['cfg_name'][$item['code']] : $item['code'];
			$item['desc'] = isset($_LANG['cfg_desc'][$item['code']]) ? $_LANG['cfg_desc'][$item['code']] : '';

			if ($pid == 0)
			{
				/* 分组 */
				if ($item['type'] == 'group')
				{
					$group_list[$item['id']] = $item;
				}
			}
			else
			{
				/* 变量 */
				if (isset($group_list[$pid]))
				{
					if ($item['store_range'])
					{
						$item['store_options'] = explode(',', $item['store_range']);

						foreach ($item['store_options'] AS $k => $v)
						{
							$item['display_options'][$k] = isset($_LANG['cfg_range'][$item['code']][$v]) ?
							$_LANG['cfg_range'][$item['code']][$v] : $v;
						}
					}
					$group_list[$pid]['vars'][] = $item;
				}
			}

		}

		return $group_list;
	}

	/**
	 * 设置系统设置
	 *
	 * @param   string  $key
	 * @param   string  $val
	 *
	 * @return  boolean	
	 */
	private function _update_configure($key, $val='')
	{
		$configModel = M('site_config');
		if (!empty($key))
		{
			return $configModel->where("code='$key'")->save(array('value'=>$val));
		}

		return true;
	}
}