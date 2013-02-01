<?php
class CityModel extends Model 
{
	/**
     * 添加
     * 
     * @param array     $params
     * @return bool
     */
    public function add_city(&$id, array $params)
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
    public function edit_city($id, array $params)
	{
		$this->where("id='$id'")->save($params);
		$this->_create_cache();
    	return true;
	}
	
	public function del_city($id)
	{
		$this->where("id='$id'")->delete();
		$this->_create_cache();
    	return true;
	}
	
	public function info($id)
	{
		$res = $this->_get_caches();
		$code = $res['indexs'][$id];
		return $this->infoByCode($code);
	}
	
	public function infoByCode($code)
	{
		$res = $this->_get_caches();
		if($res['all'][$code]){
			return $res['all'][$code];
		}
		return null;
	}
	
	public function getAreasByCityCode($code)
	{
		$city = $this->infoByCode($code);
		if($city){
			return $city['areas'];
		}
		return null;
	}
	
    public function getAllCity()
    {
    	static $return = null;
    	if($return !== null){
    		return $return;
    	}
		$caches = $this->_get_caches();
		if(! $caches){
			$caches = $this->_create_cache();
		}
		$return = array();
		foreach ($caches['all'] as $c){
			if($c['level'] == 1){
				$return[$c['code']] = $c;
			}
		}
    	return $return;
    }
    
    private function _get_caches()
    {
    	static $res = null;
    	if($res !== null){
    		return $res;
    	}
    	$cacheObj = Cache::getInstance('File');
		$res = $cacheObj->get('all_city');
		if(! $res){
			$res = $this->_create_cache();
		}
		return $res;
    }
    
    /**
     * 缓存
     * 
     * @return array
     */
    private function _create_cache()
    {
    	$cacheObj = Cache::getInstance('File');
    	$data = array('all'=>array(),'indexs'=>array());
    	$res = $this->order('sort_order ASC')->select();
    	foreach ($res as $rs){
    		$areas = $this->where("level=2 AND parent_id=$rs[id]")->order('sort_order ASC')->select();
    		$rs['areas'] 					= $areas ? $areas : array();
    		$data['all'][$rs['code']] 		= $rs;
    		$data['indexs'][$rs['id']]	= $rs['code'];
    	}
    	$cacheObj->set('all_city', $data);
    	return $data;
    }
}