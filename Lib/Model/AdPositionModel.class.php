<?php
/**
 * 广告位模型类
 */
class AdPositionModel extends Model
{
	protected $tableName		=	'ad_position';
	
	public function getAll(
    						array $limit = array())
	{
		$result = array('count'=>0,'data'=>array());
		$fields = "position_id";
    	$result['count'] = $this->field($fields)->count();
    	$result['data'] = $this->field($fields)
    							->order('position_id DESC')
    							->limit("$limit[begin],$limit[offset]")
    							->select();
    	return $result;
	}
	
	public function getPositions()
	{
    	return $this->field("position_id")->select();
	}
	
    /**
     * 添加
     * 
     * @int				$id
     * @param array     $params
     * @return bool
     */
    public function addPosition(&$id, array $params)
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
    public function editPosition($id, array $params)
    {
    	if(empty($params)){
    		return false;
    	}
    	$this->where("position_id='$id'")->save($params);
    	return true;
    }
    
    /**
     * 删除
     * 
     * @param int	$id
     *@return bool
     */
    public function del($position_id)
    {
    	$this->where("position_id='$position_id'")->delete();
    	return true;
    }
    
    public function info($id)
    {
    	return $this->where("position_id='$id'")->find();
    }
}