<?php
class AdminUserRoleModel extends Model 
{
	protected $tableName 	= "admin_user_role";
	
	private $_cache_path	= '';
	
	protected function _initialize()
	{
		parent::_initialize();
		$data_cache_path = C('DATA_CACHE_PATH');
		$this->_cache_path = $data_cache_path . 'roles/';
		if(! is_dir($this->_cache_path)){
			mk_dir($this->_cache_path, 0755);
		}
	}
	
	/**
     * 更新用户角色
     * @param int		$user_id
     * @param array     $params
     * @return bool
     */
    public function edit_ur($user_id, array $roles)
    {
    	//先删除
    	$this->_del($user_id);
    	foreach ($roles as $key => $val){
    		$data = array(
    					'userid'	=>	$user_id,
    					'roleid'	=>	$val,
    					);
    		$this->data($data)->add();
    	}
    	$this->cacheRoles($roles, $user_id);
    	return true;
    }
    
    /**
     * 删除用户原有的角色数据
     * 
     * @param int       $user_id        用户ID
     * @return bool
     */
    public function _del($user_id)
    {
        $this->where("userid=$user_id")->delete();
        return true;
    }
    
    /**
     * 根据角色ID删除记录
     * 
     * @param int       $role_id        角色ID
     * @return bool
     */
    public function delByRoleId($role_id)
    {
        $this->where("roleid=$role_id")->delete();
        del_dir($this->_cache_path);
        mk_dir($this->_cache_path, 0755);
        return true;
    }
    
    /**
     * 获取用户的角色数据
     * 
     * @param int   $user_id            用户ID
     * @return array
     */
    public function getUserRole($user_id)
    {
    	$res = F('u_r_'.$user_id, '', $this->_cache_path);
        if($res === false){
        	$data = $this->field('roleid')->where("userid=$user_id")->select();
            $res = array();
            if(is_array($data)){
            	foreach ($data as $rs){
            		$res[] = intval($rs['roleid']);
            	}
            }
            $this->cacheRoles($res, $user_id);
        }
        return $res;
    }
    
    /**
     * 根据角色ID查找相关的用户ID
     *
     * @param array $roles
     * @return array
     */
    public function getUidByRoles(array $roles)
    {
    	if(empty($roles)){
    		return array();
    	}
    	$res = $this->field('userid')->where("roleid IN (".implode(',', $roles).")")->select();
    	$return = array();
    	foreach ($res as $rs){
    		$return[] = $rs['userid'];
    	}
    	return $return;
    }
    
    /**
     * 缓存用户的角色数据
     * 
     * @param array             $roleIds
     * @param int               $user_id    用户ID
     * @return void
     */
    private function cacheRoles(array $roleIds, $user_id)
    {
    	if(! $user_id) return ;
    	F('u_r_'.$user_id, $roleIds, $this->_cache_path);
    }
}