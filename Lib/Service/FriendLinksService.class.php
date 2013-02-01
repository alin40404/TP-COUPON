<?php
/**
 * FriendLinksService.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Thu Apr 26 16:32:37 CST 2012
 */
class FriendLinksService
{
	public function getAll()
	{
		if(! C('DATA_CACHE_ON')){
			$data = $this->_getAll();
			return $data;
		}
		$data = F('friendlinks');
		if(! $data){
			$data = $this->_getAll();
			F('friendlinks', $data);
		}
		return $data;
	}
	
	public function _getAll()
	{
		$res = M('FriendLink')->select();
		$links = array('all'=>array());
    	foreach ($res as $link){
    		if($link['link_type'] == 2){
    			if(is_file(FixedUploadedFileUrl($link['link_code']))){
    				$link['link_code'] = FixedUploadedFileUrl($link['link_code']);
    			}
    		}
    		$links['all'][$link['link_id']] = $link;
    		if(!isset($links[$link['position_id']])) $links[$link['position_id']] = array();
    		$links[$link['position_id']][] = $link['link_id'];
    	}
    	return $links;
	}
	
	public function clearCache()
	{
		if(C('DATA_CACHE_ON')) F('friendlinks', null);
	}
}