<?php
/**
 * AdminRoleModel.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sat Apr 07 23:42:18 CST 2012
 */
class AdminRoleModel extends Model 
{
	/**
     * 添加角色
     * 
     * @int				$role_id
     * @param array     $params
     * @return bool
     */
    public function add_role(&$role_id, array $params)
    {
    	$role_id = $this->data($params)->add();
    	$this->_create_cache();
    	return true;
    }
    
	/**
     * 更新信息
     * 
     * @param int				$role_id
     * @param array				$params
     * @return bool
     */
    public function edit_role($role_id, array $params)
	{
		$this->where("role_id='$role_id'")->save($params);
		$this->_create_cache();
    	return true;
	}
    
    /**
     * 删除
     * 
     * @param int				$role_id
     * @return bool
     */
    public function del_role($role_id)
    {
    	$this->where("role_id='$role_id'")->delete();
    	$this->_create_cache();
    	return true;
    }
    
    /**
     * 获取信息
     * @param int	$role_id
     * @return array
     */
    public function info($role_id)
    {
    	$roles = $this->getAll();
    	return $roles[$role_id] ? $roles[$role_id] : array();
    }
    
    public function getAll()
    {
    	$caches = $this->_getCaches();
    	return $caches;
    }
    
    private function _getCaches()
    {
    	$caches = F('roles');
    	if(! $caches){
    		$caches = $this->_create_cache();
    	}
    	return $caches ? $caches : array();
    }
    
    /**
     * 缓存
     * 
     * @return array
     */
    private function _create_cache()
    {
    	$res = $this->select();
    	if(! $res){
    		return ;
    	}
    	$data = array();
    	foreach ($res as $rs){
    		$data[$rs['role_id']] = $rs;
    	}
    	F('roles', $data);
    	return $data;
    }
}