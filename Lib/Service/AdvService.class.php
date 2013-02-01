<?php
/**
 * AdvService.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Wed May 02 14:39:44 CST 2012
 */
class AdvService
{
	public function info($id)
	{
		$ad = F('adv_'.$id);
		if(! $ad){
			$adModel = D('Ad');
			$ad = $adModel->info($id);
			F('adv_'.$id, $ad);
		}
		return $ad;
	}
	
	public function clearCache($id)
	{
		return F('adv_'.$id, null);
	}
	
	public function position($pos_id)
	{
		$data = F('adv_pos_'.$pos_id);
		if(! $data){
			$adPosModel = D('AdPosition');
			$data = $adPosModel->info($pos_id);
			F('adv_pos_'.$pos_id, $data);
		}
		return $data;
	}
	
	public function clearPosCache($pos_id)
	{
		return F('adv_pos_'.$pos_id, null);
	}
}