<?php
/**
 * CouponCodeBestModel.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Wed Apr 25 15:29:19 CST 2012
 */
/**
 * 每日精选优惠券模型类
 */
class CouponCodeBestModel extends Model 
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
    
    public function getAll(array $limit)
    {
    	$result = array('count'=>0,'data'=>array());
    	$fields = 'cb.c_id,cb.expiry,cb.sort_order,c.title,c.c_type,c.m_name,c.money_max,c.money_reduce,c.money_amount';
    	$res = $this->table($this->getTableName() . " AS cb")
    				->field($fields)
    				->join(M('coupon_code')->getTableName() . ' AS c ON c.c_id=cb.c_id')
    				->limit($limit[begin], $limit[offset])
    				->select();
    	$result['count'] = $this->count();
    	$result['data'] = $res;
    	return $result;
    }
    
    public function top($time, $limit)
    {
    	$fields = 'cb.c_id,c.m_id,c.title,c.c_type,c.m_name,c.money_max,c.money_reduce,c.money_amount,m.logo';
    	$res = $this->table($this->getTableName() . " AS cb")
    				->field($fields)
    				->join(M('coupon_code')->getTableName() . ' AS c ON c.c_id=cb.c_id')
    				->join(M('coupon_code_mall')->getTableName() . ' AS m ON m.id=c.m_id')
    				->where("cb.expiry>='$time'")
    				->order('cb.sort_order ASC, c.expiry DESC')
    				->limit($limit)
    				->select();
    	return $res;
    }
}