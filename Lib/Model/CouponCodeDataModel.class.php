<?php
/**
 * CouponCodeDataModel.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 01:41:24 CST 2012
 */
/**
 * 优惠码附属表模型类
 */
class CouponCodeDataModel extends Model 
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
     * @param array $keys
     * @return array
     */
    public function info($id, array $keys=array())
    {
    	$fields = empty($keys) ? '*' : implode(',', $keys);
    	return $this->field($fields)->where("c_id='$id'")->find();
    }
    
    /**
     * 前台热门优惠券列表
     *
     * @param array $keys
     * @param array $params
     * @param array $limit
     */
    public function front_hot(array $params=array(), array $limit=array())
    {
    	$result = array('count'=>0,'data'=>array());
    	$fields = 'c.*, m.logo';
    	$sql = " FROM " . $this->getTableName() . " AS cd ";
    	$sql .= " LEFT JOIN " . M('coupon_code')->getTableName() . " AS c ON c.c_id=cd.c_id";
    	$sql .= " LEFT JOIN " . M('coupon_code_mall')->getTableName() . " AS m ON m.id=c.m_id";
    	$sql .= " WHERE c.is_active=1";
    	if(isset($params['cate_id']) && $params['cate_id']){
    		$sql .= " AND m.c_id IN ($params[cate_id])";
    	}
	    $res = $this->query("SELECT COUNT(*) AS c_count" . $sql . " LIMIT 1");
	    $result['count'] = empty($res) ? 0 : $res[0]['c_count'];
	    $sql .= " ORDER BY cd.".$params['order']." DESC, c.sort_order ASC, c.c_id DESC, c.expiry DESC";
	    if(isset($limit['begin']) && isset($limit['offset'])){
    		$sql .= " LIMIT $limit[begin],$limit[offset]";
    	}
    	$result['data'] = $this->query("SELECT $fields" . $sql);
    	return $result;
    }
    
    /**
     * 领取最多的优惠券
     *
     * @param string $type:yesterday(昨日最多)、day（今日最多）、week（本周最多）、month（本月最多）
     * @param int $limit
     */
    public function hottest($type, $limit=10)
    {
    	$sql = "SELECT cd.c_id,c.title,c.c_type,c.m_name,c.money_max,c.money_reduce,c.money_amount,c.fetched_amount,m.logo";
    	$sql .= " FROM " . $this->getTableName() . " AS cd";
    	$sql .= " LEFT JOIN " . M('coupon_code')->getTableName() . ' AS c ON c.c_id=cd.c_id';
    	$sql .= " LEFT JOIN " . M('coupon_code_mall')->getTableName() . ' AS m ON m.id=c.m_id';
    	$sql .= " WHERE c.is_active=1 AND ".$type."fetched>0 ORDER BY ".$type."fetched DESC, c.expiry DESC LIMIT $limit";
    	return $this->query($sql);
    }
}