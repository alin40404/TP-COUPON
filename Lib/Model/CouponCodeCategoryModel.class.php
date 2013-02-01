<?php
/**
 * CouponCodeCategoryModel.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 01:41:24 CST 2012
 */
/**
 * 优惠码分类模型类
 */
class CouponCodeCategoryModel extends Model
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
    public function info($id)
    {
    	return $this->where("id='$id'")->find();
    }

    public function getAll()
    {
        return $this->order('sort_order ASC')->select();
    }
    
    public function getDeptsByPid($parent_id)
    {
    	return $this->where('parent_id='.$parent_id)->order('sort_order ASC')->select();
    }
    
    /**
     * 根据ID获取所有的上级分类
     * 
     * @param int			$id
     * @return array
     */
    public function getParents($id)
    {
    	static $res = array();
    	if(isset($res[$id])){
    		return $res[$id];
    	}
    	if(! function_exists('_getParentsHanlder')){
    		function _getParentsHanlder($id, $model, &$res)
    		{
    			$info = $model->info($id);
    			$res[] = $info;
    			if($info['parent_id']){
    				$count++;
    				_getParentsHanlder($info['parent_id'], $model, $res);
    			}
    		}
    	}
    	$data = array();
    	_getParentsHanlder($id, $this, $data);
    	$res[$id] = $data;
    	$res[$id] = array_reverse($res[$id]);
    	return $res[$id];
    }
}