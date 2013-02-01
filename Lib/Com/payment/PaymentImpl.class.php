<?php
/**
 * PaymentImpl.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Fri Apr 27 15:20:29 CST 2012
 */
interface PaymentImpl
{
	/**
	 * 构建支付表单html
	 *
	 * @param array $params
	 */
	public function buildForm(array $params);
	
	/**
	 * 验证通知结果
	 *
	 */
	public function verifyReturn();
	
	/**
	 * 验证异步通知结果
	 *
	 */
	public function verifyNotify();
}