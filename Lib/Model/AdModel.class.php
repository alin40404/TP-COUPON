<?php
/**
 * 广告模型类
 */
class AdModel extends Model
{
	public function getAll(
    						array $limit = array())
	{
		$result = array('count'=>0,'data'=>array());
		$fields = "ad_id";
    	$result['count'] = $this->field($fields)->count();
    	$result['data'] = $this->field($fields)
    							->order("ad_id DESC")
    							->limit("$limit[begin],$limit[offset]")
    							->select();
    	return $result;
	}
	
	public function getAdsByPositionId($position_id)
	{
		return $this->field("ad_id")->where("position_id='$position_id'")->select();
	}
	
    /**
     * 添加
     * 
     * @int				$id
     * @param array     $params
     * @return bool
     */
    public function addAd(&$id, array $params)
    {
        $id = $this->data($params)->add();
        return true;
    }
    
    /**
     * 编辑
     * 
     * @param int				$id
     * @param array				$params
     * @return bool
     */
    public function editAd($id, array $params)
    {
    	if(empty($params)){
    		return false;
    	}
    	$this->where("ad_id='$id'")->save($params);
    	return true;
    }
    
    public function update($id, $params)
    {
    	return $this->editAd($id, $params);
    }
    
    /**
     * 删除
     * 
     * @param int	$id
     *@return bool
     */
    public function del($id)
    {
    	$this->where("ad_id='$id'")->delete();
    	return true;
    }
    
    public function info($id)
    {
    	return $this->where("ad_id='$id'")->find();
    }
}