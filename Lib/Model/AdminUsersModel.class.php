<?php
/**
 * 管理员模型类
 */
class AdminUsersModel extends Model
{
	const TABLE_ADMIN_USERS		=	'admin_users';
	protected $tableName		=	self::TABLE_ADMIN_USERS;
	
	public function getAdmins(array $params=array(), array $limit = array())
	{
		$result = array('count'=>0,'data'=>array());
    	$result['count'] = $this->count();
    	$result['data'] = $this->limit("$limit[begin],$limit[offset]")->select();
    	return $result;
	}
	
    /**
     * 添加用户
     * 
     * @int				$user_id
     * @param array     $params
     * @return bool
     */
    public function add_user(&$user_id, array $params)
    {
        $user_id = $this->data($params)->add();
        return true;
    }
    
    /**
     * 更新用户信息
     * 
     * @param int				$user_id
     * @param array				$params
     * @return bool
     */
    public function edit_user($user_id, array $params)
    {
    	if(empty($user_id) || empty($params)){
    		return false;
    	}
    	$this->where("user_id='$user_id'")->save($params);
    	return true;
    }
    
    public function _delete($user_id)
    {
    	return $this->where("user_id='$user_id'")->delete();
    }
    
    /**
     * 获取用户信息
     * @param int			$user_id
     * @return array
     */
    public function info($user_id)
    {
    	static $users = array();
    	if(isset($users[$user_id])){
    		return $users[$user_id];
    	}
    	$users[$user_id] = $this->where("user_id='$user_id'")->find();
    	return $users[$user_id];
    }
    
    public function infoByUserName($user_name)
    {
    	$user = $this->field("user_id")->where("user_name='$user_name'")->find();
    	if(! $user){
    		return null;
    	}
    	return $this->info($user['user_id']);
    }
}