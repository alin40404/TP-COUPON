<?php
/**
 * UserModel.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 15 02:44:44 CST 2012
 */
class UserModel extends Model 
{
	/**
     * 添加
     * 
     * @param array     $params
     * @return bool
     */
    public function _add(array $params)
    {
    	return $id = $this->data($params)->add();
    }
    
    public function _edit($user_id, array $data)
    {
    	return $this->where("user_id='$user_id'")->save($data);
    }
    
    public function _delete($ids)
    {
    	return $this->where("user_id IN ($ids)")->delete();
    }
    
    public function update($user_id, array $data)
    {
    	return $this->_edit($user_id, $data);
    }
    
    public function info($user_id, array $keys=array())
    {
    	if(empty($keys)){
    		$fields = '*';
    	}else{
    		$fields = implode(',', $keys);
    	}
    	return $this->field($fields)->where("user_id='$user_id'")->find();
    }
    
    public function infoByNick($nick, array $keys=array())
    {
    	if(empty($keys)){
    		$fields = '*';
    	}else{
    		$fields = implode(',', $keys);
    	}
    	return $this->field($fields)->where("nick='$nick'")->find();
    }
    
    public function getAll(array $params, array $limit = array())
	{
		$result = array('count'=>0,'data'=>array());
		$where = '1=1';
		if(isset($params['nick'])){
			$where .= " AND nick LIKE '%$params[nick]%'";
		}
    	$result['count'] = $this->where($where)->count();
    	$result['data'] = $this->where($where)
    							->order("user_id DESC")
    							->limit("$limit[begin],$limit[offset]")
    							->select();
    	return $result;
	}
}

