<?php
/**
 * CouponCodeModel.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 01:41:24 CST 2012
 */
/**
 * 优惠码模型类
 */
class CouponCodeModel extends RelationModel 
{
	protected $_link = array(
							'Data'	=>	array(
											'mapping_type'	=>	HAS_ONE,
											'class_name'	=>	'CouponCodeData',
											'foreign_key'	=>	'c_id',
											'mapping_name'	=>	'data',
											'mapping_fields'=>	'fetch_limit,directions,prompt',
											),
							);
							
    /**
     * 添加
     * 
     * @int				$id
     * @param array     $params
     * @return bool
     */
    public function _add(array $params)
    {
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
    	$this->where("c_id='$id'")->save($params);
    	return true;
    }
    
    public function update($id, array $params)
    {
    	return $this->_edit($id, $params);
    }
    
    /**
     * 删除信息
     * 
     * @param array				$id
     * @return bool
     */
    public function _delete($id)
    {
    	$this->where("c_id='$id'")->delete();
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
    	return $this->where("c_id='$id'")->relation(true)->find();
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
    	if(isset($params['kw']) && $params['kw']){
    		$where .= " AND `m_name` LIKE '%$params[kw]%'";
    	}
    	$result['count'] = $this->where($where)->count();
    	$result['data'] = $this->field($fields)->where($where)
    							->order('c_id DESC')
    							->limit("$limit[begin], $limit[offset]")->select();
    	return $result;
	}
	
	/**
     * 前台优惠券列表
     *
     * @param array $keys
     * @param array $params
     * @param array $limit
     */
    public function front(array $params=array(), array $limit=array())
    {
    	$result = array('count'=>0,'data'=>array());
    	$fields = 'c.*, m.logo';
    	$sql = " FROM " . $this->getTableName() . " AS c LEFT JOIN " . M('coupon_code_mall')->getTableName() . " AS m ON m.id=c.m_id";
    	$sql .= " WHERE c.is_active=1";
    	if(isset($params['addtime']) && $params['addtime']){
    		$sql .= " AND c.addtime>=$params[addtime]";
    	}
    	if(isset($params['cate_id']) && $params['cate_id']){
    		$sql .= " AND m.c_id IN ($params[cate_id])";
    	}
	    $res = $this->query("SELECT COUNT(*) AS c_count" . $sql . " LIMIT 1");
	    $result['count'] = empty($res) ? 0 : $res[0]['c_count'];
	    $sql .= " ORDER BY c.sort_order ASC, c.c_id DESC, c.expiry DESC";
	    if(isset($limit['begin']) && isset($limit['offset'])){
    		$sql .= " LIMIT $limit[begin],$limit[offset]";
    	}
    	$result['data'] = $this->query("SELECT $fields" . $sql);
    	return $result;
    }
    
    /**
     * 最新优惠券列表
     *
     * @param int	$m_id
     * @param int $limit
     */
    public function latest($m_id=null, $limit=10)
    {
    	$fields = 'c.*, m.logo';
    	$sql = " FROM " . $this->getTableName() . " AS c LEFT JOIN " . M('coupon_code_mall')->getTableName() . " AS m ON m.id=c.m_id";
    	$sql .= " WHERE c.is_active=1";
    	if($m_id){
    		$sql .= " AND c.m_id='$m_id'";
    	}
    	$sql .= " ORDER BY c.c_id DESC,c.expiry DESC LIMIT $limit";
    	$res = $this->query("SELECT $fields" . $sql);
    	return $res;
    }
    
    /**
     * 商家所有优惠券
     *
     * @param int	$m_id
     * @param int $limit
     */
    public function all4mall($m_id)
    {
    	return $this->where("is_active=1 AND m_id='$m_id'")->order("c_id DESC,expiry DESC")->select();
    }
    
    /**
     * 最新分类优惠券列表
     *
     * @param int	$m_id
     * @param int $limit
     */
    public function coupons4cate($cate_id, $limit=4)
    {
    	$fields = 'c.*, m.logo';
    	$sql = " FROM " . $this->getTableName() . " AS c LEFT JOIN " . M('coupon_code_mall')->getTableName() . " AS m ON m.id=c.m_id";
    	$sql .= " WHERE c.is_active=1 AND m.c_id IN ($cate_id)";
    	$sql .= " ORDER BY c.sort_order ASC,c.c_id DESC,c.expiry DESC LIMIT $limit";
    	$res = $this->query("SELECT $fields" . $sql);
    	return $res;
    }
}