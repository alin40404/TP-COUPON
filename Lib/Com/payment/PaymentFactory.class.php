<?php
/**
 * PaymentFactory.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Fri Apr 27 15:22:00 CST 2012
 */
class PaymentFactory
{
	public static function getPayment($type)
	{
		$type = ucfirst($type);
		require_once(LIB_PATH . 'Com/payment/PaymentImpl.class.php');
		require_once(LIB_PATH . 'Com/payment/'.$type.'.class.php');
		$payment = new $type();
		if(! $payment instanceof PaymentImpl){
			throw_exception('支付接口类型错误');
		}
		return $payment;
	}
}