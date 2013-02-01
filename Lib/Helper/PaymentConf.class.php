<?php
/**
 * PaymentConf.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Thu May 03 13:34:25 CST 2012
 */
class PaymentConf
{
	/**
	 * 支付状态配置
	 *
	 */
	public static function status_conf()
	{
		return $conf = array(
							'101'		=>	'发起交易',
							'102'		=>	'等待卖家发货',
							'103'		=>	'等待买家付款',
							'104'		=>	'交易完成',
							'105'		=>	'等待买家确认收货',
							);
	}
}