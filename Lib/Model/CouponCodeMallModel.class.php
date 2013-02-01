<?php
/**
 * CouponCodeMallModel.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 01:41:24 CST 2012
 */
/**
 * 优惠码商家模型类
 */
class CouponCodeMallModel extends Model
{   
    /**
     * 添加
     * 
     * @int				$id
     * @param array     $params
     * @return bool
     */
    public function _add(array $params)
    {
    	$name_match = segment($params['name']);
		$params['name_match'] = implode(' ', array_unique($name_match));
    	return  $this->data($params)->add();
    }
    
    /**
     * 更新信息
     * 
     * @param array				$id
     * @param array				$params
     * @return bool
     */
    public function _edit($id, array $params)
    {
    	$name_match = segment($params['name']);
		$params['name_match'] = implode(' ', array_unique($name_match));
    	$this->where("id='$id'")->save($params);
    	return true;
    }
    
    public function update($id, array $params)
    {
    	$this->where("id='$id'")->save($params);
    	return true;
    }
    
    /**
     * 更新全文索引
     * 
     * @return bool
     */
    public function _updateFullIndex()
	{
		$res = $this->field('id,name')->select();
		foreach ($res as $params){
			$data = array();
			$name_match = segment($params['name']);
			$data['name_match'] = implode(' ', array_unique($name_match));
			$this->where("id='$params[id]'")->save($data);
		}
    	return true;
	}
    
    /**
     * 删除信息
     * 
     * @param array				$id
     * @return bool
     */
    public function _delete($id)
    {
    	$this->where("id='$id'")->delete();
        return true;
    }
    
    /**
     * 获取信息
     * 
     * @param int	$id
     * @return array
     */
    public function info($id)
    {
    	return $this->where("id='$id'")->find();
    }
    
    public function search($kw)
    {
    	$where = 'is_active=1';
    	$match = segment($kw);
    	if(empty($match)){
    		$where .= " AND `name` LIKE '%$kw%'";
    	}else{
    		$match = implode(' ', array_unique($match));
    		$where .= " AND (`name` LIKE '%$kw%' ) OR (MATCH(name_match) AGAINST('*$match*' IN BOOLEAN MODE))";
    	}
    	$fields = "id,name,logo,description,yesterdaysearched,daysearched,weeksearched,monthsearched,updatetime";
    	return $this->field($fields)->where($where)->order('id DESC')->select();
    }
    
    public function hottest($type, $limit=10)
    {
    	$order = $type."searched DESC";
    	return $this->field("id,name")->order($order)->limit($limit)->select();
    }

    public function getAll(
    						array $keys = array(),
    						array $params = array(),
    						array $limit = array())
	{
		$result = array('count'=>0,'data'=>array());
		if(empty($keys)){
    		$fields = "*";
    	}else{
    		$fields = implode(',', $keys);
    	}
    	$where = '1=1';
    	if(isset($params['is_active']) && $params['is_active'] !== null){
    		$where .= " AND is_active='$params[is_active]'";
    	}
    	if(isset($params['c_id']) && $params['c_id']){
    		$where .= " AND c_id='$params[c_id]'";
    	}
    	if(isset($params['kw']) && $params['kw']){
    		$match = segment($params['kw']);
    		if(empty($match)){
    			$where .= " AND `name` LIKE '%$params[kw]%'";
    		}else{
    			$match = implode(' ', array_unique($match));
    			$where .= " AND ((`name` LIKE '%$params[kw]%' ) OR (MATCH(name_match) AGAINST('*$match*' IN BOOLEAN MODE)))";
    		}
    	}
    	$result['count'] = $this->field($fields)->where($where)->count();
    	$result['data'] = $this->field($fields)->where($where)
    							->order('sort_order ASC, id DESC')
    							->limit("$limit[begin], $limit[offset]")->select();
    	return $result;
	}
	
	public function front(
    						array $keys = array(),
    						array $params = array(),
    						array $limit = array())
	{
		$result = array('count'=>0,'data'=>array());
		if(empty($keys)){
    		$fields = "*";
    	}else{
    		$fields = implode(',', $keys);
    	}
    	$where = 'is_active=1';
    	if(isset($params['c_id']) && $params['c_id']){
    		$where .= " AND c_id IN ($params[c_id])";
    	}
    	$order = '';
    	if(isset($params['t_type']) && $params['t_type'] > 1){
    		switch (intval($params['t_type'])){
    			case 2:
    				$order = 'yesterdaysearched DESC,';
    				break;
    			case 3:
    				$order = 'daysearched DESC,';
    				break;
    			case 4:
    				$order = 'weeksearched DESC,';
    				break;
    			case 5:
    				$order = 'monthsearched DESC,';
    				break;
    		}
    	}
    	$result['count'] = $this->field($fields)->where($where)->count();
    	$result['data'] = $this->field($fields)->where($where)
    							->order($order . 'sort_order ASC, id DESC')
    							->limit("$limit[begin], $limit[offset]")->select();
    	return $result;
	}
	
	public function malls4cate($cate_ids)
	{
		return $this->field('id')->where("c_id IN ($cate_ids)")
    				->order('sort_order ASC, id DESC')
    				->select();
	}
}