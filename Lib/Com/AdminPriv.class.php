<?php
/**
 * AdminPriv.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Wed May 02 17:43:12 CST 2012
 */
/**
 * 角色权限类
 *
 */
class AdminPriv
{
	private static $_instance = null;
	 
    /**
     * 模型对象
     *
     * @var Module
     */
    private $_m = null;

	private function __construct()
	{
	    $this->_m = M('admin_user_role_priv');
	}
	
	public static function getInstance()
	{
		if(self::$_instance !== null){
			return self::$_instance;
		}
		self::$_instance = new self();
		return self::$_instance;
	}

    public function _add($field, $value, $priv, $roleid)
    {
		$roleid = intval($roleid);
		$data = array(
					'roleid'	=>	$roleid,
					'field'	=>	$field,
					'value'	=>	$value,
					'priv'	=>	$priv,
					);
		return $this->_m->data($data)->add();
    }

	public function _delete($field, $value, $priv = '', $roleid = 0)
	{
		$where = '';
		if($roleid) $where .= "AND `roleid`='$roleid' ";
		if($field) $where .= "AND `field`='$field' ";
		if($value) $where .= "AND `value`='$value' ";
		if($priv) $where .= "AND `priv`='$priv' ";
		if($where)
		{
            $where = substr($where, 3);
            return $this->_m->where($where)->delete();
		}
		return false;
	}

	public function _update($field, $value, $priv_role)
	{
		if(!$field || !$value) return false;
		$this->_delete($field, $value);
		if(!is_array($priv_role)) return true;
		foreach($priv_role as $priv_roleid)
		{
            if(is_numeric($priv_roleid))
			{
				$priv = '';
				$roleid = $priv_roleid;
			}
			else
			{
				list($priv, $roleid) = explode(',', $priv_roleid);
			}
			$this->_add($field, $value, $priv, $roleid);
		}
		return true;
	}

	public function check(array $user_roles, $field, $value, $priv = '')
	{
	    if(empty($user_roles)){
	        return false;
	    }
		$where = " `field`='$field' AND `value`='$value' AND `roleid` IN(".implode(',',$user_roles).")";
		if($priv) $where .= is_array($priv) ? " AND `priv` IN('".implode("','", $priv)."') " : " AND `priv`='$priv' ";
		$res = $this->_m->field("roleid")->where($where)->find();
		if(! $res){
			return false;
		}
		return $res['roleid'];
	}
	
	public function module(array $user_roles, $mod, $action)
	{
		$privs_conf = C('_privs_.Admin');
		$privs = $privs_conf[$mod];
		if(!$privs) return true;
		if($this->check($user_roles, 'module', $mod, 'all')){
		    return true;
		}

		$actions = array();
        foreach($privs as $priv=>$v)
        {
			if(!$v['action'] || in_array($action, explode(',', $v['action']))){
			     $actions[] = $priv;
			 }
        }
	
		return $actions && $this->check($user_roles, 'module', $mod, $actions);
	}

	public function get_roleid($field, $value, $priv = '')
	{
		$roleids = array();
		$array = $this->_m->field("roleid")->where("field='$field' AND value='$module' AND priv='$priv'")->select();
		foreach($array as $k=>$v)
		{
			$roleids[$k] = $v['roleid'];
		}
		return $roleids;
	}
	
	/**
	 * 获取模块的权限数据
	 * 
	 * @param string       $module     模块
	 * @return array
	 */
	public function getPrivs($module, $field = 'module')
	{
		return $this->_m->field("roleid,priv")->where("field='$field' AND value='$module'")->select();
	}
	
	/**
	 * 验证用户权限
	 * @param string		$module		模块名
	 * @param string		$action		操作名
	 */
	public static function checkPrive($module, $action)
	{
		if($_SESSION[C('SESSION_PREFIX') . 'is_super']) return true;
		//当前用户的角色
    	$user_roles = isset($_SESSION[C('SESSION_PREFIX') . 'user_roles']) ? $_SESSION[C('SESSION_PREFIX') . 'user_roles'] : array();
		$adminPrvObj = self::getInstance();
		return $adminPrvObj->module($user_roles, $module, $action);
	}
}