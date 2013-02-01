<?php
class ArticleCategoryModel extends Model 
{
	protected $tableName		= 'article_category';
	/**
     * 添加
     * 
     * @param array     $params
     * @return bool
     */
    public function _add(&$id, array $params)
    {
    	$id = $this->data($params)->add();
    	$this->_create_cache();
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
		$this->where("cate_id='$id'")->save($params);
		$this->_create_cache();
    	return true;
	}
	
	public function _delete($id)
	{
		$this->where("cate_id='$id'")->delete();
		F('article_category', null);
    	return true;
	}
	
	public function info($id)
	{
		$res = F('article_category');
		if(! $res){
			$res = $this->_create_cache();
		}
		return $res[$id] ? $res[$id] : array();
	}
	
	public function getAll()
	{
		$res = F('article_category');
		if(! $res){
			$res = $this->_create_cache();
		}
    	return $res ? $res : array();
	}
	
	/**
	 * 生成缓存
	 *
	 * @param int $hotel_id
	 * @return array
	 * 
	 */
	private function _create_cache()
	{
    	$res = $this->order("sort_order ASC")->select();
    	$data = array();
    	foreach ($res as $rs){
    		$data[$rs['cate_id']] = $rs;
    	}
    	F('article_category', $data);
    	return $data;
	}
}