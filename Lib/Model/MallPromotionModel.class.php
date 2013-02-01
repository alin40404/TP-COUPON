<?php
/**
 * MallPromotionModel.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 01:41:24 CST 2012
 */
/**
 * 商家促销活动模型类
 */
class MallPromotionModel extends Model
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
    public function update($id, array $params)
    {
    	$this->where("id='$id'")->save($params);
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
    public function info(array $keys=array(),$id)
    {
    	$fields = empty($keys) ? '*' : implode(',', $keys);
    	return $this->where("id='$id'")->find();
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
    	if(isset($params['cate_id']) && $params['cate_id']){
    		$where .= " AND cate_id='$params[cate_id]'";
    	}
    	if(isset($params['kw']) && $params['kw']){
    		$where .= " AND `title` LIKE '%$params[kw]%'";
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
    	$where = '1=1';
    	if(isset($params['cate_id']) && $params['cate_id']){
    		$where .= " AND cate_id IN ($params[cate_id])";
    	}
    	$result['count'] = $this->field($fields)->where($where)->count();
    	$result['data'] = $this->field($fields)->where($where)
    							->order('sort_order ASC, id DESC')
    							->limit("$limit[begin], $limit[offset]")->select();
    	return $result;
	}
}