<?php
/**
 * AdAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Wed May 02 14:37:47 CST 2012
 */
class AdvAction extends HomeCommonAction
{
	public function show()
	{
		$id = intval($_REQUEST['id']);
		$advS = service('Adv');
		$ad_info = $advS->info($id);
		$ad_pos_info = $advS->position($ad_info['position_id']);
		$str = '';
		header('Content-type: application/x-javascript; charset=' . YF_CHARSET);
		$url = 'http://' . $_SERVER['HTTP_HOST'];
		$nowtime = LocalTime::getInstance()->gmtime();
		if (!empty($ad_info))
		{
			if($ad_info['end_time'] < $nowtime || $ad_info['enabled'] == 0){
				exit("document.writeln('The adv is timeout');");
			}
			switch ($ad_info['media_type'])
			{
				case 101:
					/* 图片广告 */
					$src = (strpos($ad_info['ad_code'], 'http://') === false && strpos($ad_info['ad_code'], 'https://') === false) ? $url .FixedUploadedFileUrl($ad_info[ad_code]) : $ad_info['ad_code'];
					$str = '<a href="' .$url. __ROOT__ . '/index.php?m=Adv&a=click&ad_id=' .$ad_info['ad_id']. '" target="_blank">' .
					'<img src="' . $src . '" border="0" alt="' . $ad_info['ad_name'] . '" height="'.$ad_pos_info['ad_height'].'" width="'.$ad_pos_info['ad_width'].'" /></a>';
					break;

				case 102:
					/* Falsh广告 */
					$src = (strpos($ad_info['ad_code'], 'http://') === false && strpos($ad_info['ad_code'], 'https://') === false) ? $url .FixedUploadedFileUrl($ad_info[ad_code]) : $ad_info['ad_code'];
					$str = '<object width="'.$ad_pos_info['ad_width'].'" height="'.$ad_pos_info['ad_height'].'" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0"> <param name="movie" value="'.$src.'"><param name="quality" value="high"><embed width="'.$ad_pos_info['ad_width'].'" height="'.$ad_pos_info['ad_height'].'" src="'.$src.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed></object>';
					break;

				case 103:
					/* 代码广告 */
					$str = $ad_info['ad_code'];
					break;

				case 104:
					/* 文字广告 */
					$str = '<a href="' .$url. __ROOT__ . '/index.php?m=Adv&a=click&ad_id=' .$ad_info['ad_id']. '" target="_blank">' . nl2br(htmlspecialchars(addslashes($ad_info['ad_code']))). '</a>';
					break;
			}
		}
		echo "document.writeln('$str');";
	}
	
	public function click()
	{
		$id = intval($_REQUEST['ad_id']);
		$adModel = D('Ad');
		$ad_info = $adModel->info($id);
		$nowtime = LocalTime::getInstance()->gmtime();
		if(! $ad_info || $ad_info['end_time'] < $nowtime || $ad_info['enabled'] == 0){
			exit("document.writeln('The adv is timeout');");
		}
		$adModel->update($id, array('click_count'=>($ad_info['click_count']+1)));
		redirect($ad_info['ad_link']);
	}
}