<?php
class ArticleModel extends Model 
{
	/**
     * 添加
     * 
     * @param array     $params
     * @return bool
     */
    public function _add(&$id, array $params)
    {
    	$id = $this->data($params)->add();
    	return true;
    }
    
	/**
     * 更新信息
     * 
     * @param int				$id
     * @param array				$params
     * @return bool
     */
    public function _edit($id, array $params)
	{
		$this->where("article_id='$id'")->save($params);
    	return true;
	}
	
	public function _delete($id)
	{
		$this->where("article_id='$id'")->delete();
    	return true;
	}
	
	public function info($id)
	{
		return $this->where("article_id='$id'")->find();
	}
	
	public function getAll(array $params, array $limit = array())
	{
		$result = array('count'=>0,'data'=>array());
		$where = '1=1';
		if(isset($params['cate_id'])){
			$where .= " AND cate_id='$params[cate_id]'";
		}
		if(isset($params['kw'])){
			$where .= " AND (title LIKE '%$params[kw]%' OR content LIKE '%$params[kw]%')";
		}
    	$result['count'] = $this->where($where)->count();
    	$result['data'] = $this->where($where)
    							->order("article_id DESC")
    							->limit("$limit[begin],$limit[offset]")
    							->select();
    	return $result;
	}
	
	public function getByCateId($cate_id, array $keys = array())
	{
		return $this->field($keys)->where("cate_id='$cate_id'")->order("article_id DESC")->select();
	}
	
	public function delByCateId($cate_id)
	{
		return $this->where("cate_id='$cate_id'")->delete();
	}
}