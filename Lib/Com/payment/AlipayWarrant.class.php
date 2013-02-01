<?php
/**
 * Alipay.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Fri Apr 27 15:24:54 CST 2012
 */
class AlipayWarrant implements PaymentImpl
{
	private $_aliapy_config = array();
	
	public function __construct()
	{
		$cfg = load_config();
		//支付方式配置
		$aliapy_config = array();
		//合作身份者id，以2088开头的16位纯数字
		$aliapy_config['partner']      = $cfg['alipay_partner'];
		//安全检验码，以数字和字母组成的32位字符
		$aliapy_config['key']          = $cfg['alipay_key'];
		//签约支付宝账号或卖家支付宝帐户
		$aliapy_config['seller_email'] = $cfg['alipay_seller_email'];
		//页面跳转同步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		//return_url的域名不能写成http://localhost/create_direct_pay_by_user_php_utf8/return_url.php ，否则会导致return_url执行无效
		$aliapy_config['return_url']   = 'http://'.$_SERVER['HTTP_HOST'] . __ROOT__ . '/pay_return.php';
		//服务器异步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		$aliapy_config['notify_url']   = 'http://'.$_SERVER['HTTP_HOST'] . __ROOT__ . '/pay_notify.php';
		//签名方式 不需修改
		$aliapy_config['sign_type']    = 'MD5';
		//字符编码格式 目前支持 gbk 或 utf-8
		$aliapy_config['input_charset']= 'utf-8';
		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$aliapy_config['transport']    = 'http';
		$this->_aliapy_config = $aliapy_config;
	}
	
	/**
	 * 构建支付表单html
	 *
	 * @param array $params
	 */
	public function buildForm(array $params)
	{
		require_once(LIB_PATH . "Com/payment/alipay_warrant/alipay_service.class.php");

		/**************************请求参数**************************/

		//必填参数//

		$out_trade_no		= $params['out_trade_no'];		//请与贵网站订单系统中的唯一订单号匹配
		$subject			= $params['subject'];	//订单名称，显示在支付宝收银台里的“商品名称”里，显示在支付宝的交易管理的“商品名称”的列表里。
		$body				= $params['body'];	//订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里
		$price				= $params['total_fee'];	//订单总金额，显示在支付宝收银台里的“应付总额”里

		$logistics_fee		= "0.00";				//物流费用，即运费。
		$logistics_type		= "EXPRESS";			//物流类型，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
		$logistics_payment	= "SELLER_PAY";			//物流支付方式，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）

		$quantity			= "1";					//商品数量，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品。

		//选填参数//

		//买家收货信息（推荐作为必填）
		//该功能作用在于买家已经在商户网站的下单流程中填过一次收货信息，而不需要买家在支付宝的付款流程中再次填写收货信息。
		//若要使用该功能，请至少保证receive_name、receive_address有值
		//收货信息格式请严格按照姓名、地址、邮编、电话、手机的格式填写
		$receive_name		= "";			//收货人姓名，如：张三
		$receive_address	= "";			//收货人地址，如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号
		$receive_zip		= "";				//收货人邮编，如：123456
		$receive_phone		= "";		//收货人电话号码，如：0571-81234567
		$receive_mobile		= "";		//收货人手机号码，如：13312341234

		//网站商品的展示地址，不允许加?id=123这类自定义参数
		$show_url			= $params['show_url'];

		/************************************************************/

		//构造要请求的参数数组
		$parameter = array(
		"service"			=> "create_partner_trade_by_buyer",
		"payment_type"		=> "1",

		"partner"			=> trim($this->_aliapy_config['partner']),
		"_input_charset"	=> trim(strtolower($this->_aliapy_config['input_charset'])),
		"seller_email"		=> trim($this->_aliapy_config['seller_email']),
		"return_url"		=> trim($this->_aliapy_config['return_url']),
		"notify_url"		=> trim($this->_aliapy_config['notify_url']),

		"out_trade_no"		=> $out_trade_no,
		"subject"			=> $subject,
		"body"				=> $body,
		"price"				=> $price,
		"quantity"			=> $quantity,

		"logistics_fee"		=> $logistics_fee,
		"logistics_type"	=> $logistics_type,
		"logistics_payment"	=> $logistics_payment,

		"receive_name"		=> $receive_name,
		"receive_address"	=> $receive_address,
		"receive_zip"		=> $receive_zip,
		"receive_phone"		=> $receive_phone,
		"receive_mobile"	=> $receive_mobile,

		"show_url"			=> $show_url
		);

		//构造担保交易接口
		$alipayService = new AlipayService($this->_aliapy_config);
		$html_text = $alipayService->create_partner_trade_by_buyer($parameter);
		return $html_text;
	}
	
	/**
	 * 验证通知结果
	 *
	 * @return bool|array
	 */
	public function verifyReturn()
	{
		require_once(LIB_PATH . "Com/payment/alipay_warrant/alipay_notify.class.php");
		$alipayNotify = new AlipayNotify($this->_aliapy_config);
		$verify_result = $alipayNotify->verifyReturn();
		if(! $verify_result) return false;
		$result = array(
						'out_trade_no'			=>	$_GET['out_trade_no'],//商家订单号
						'trade_no'				=>	$_GET['trade_no'],//支付宝交易号
						'total_fee'				=>	$_GET['price'],//总价格
						'trade_status'			=>	$_GET['trade_status'],//交易状态
						);
		return $result;
	}
	
	/**
	 * 验证异步通知结果
	 *
	 * @return bool|array
	 */
	public function verifyNotify()
	{
		require_once(LIB_PATH . "Com/payment/alipay_warrant/alipay_notify.class.php");
		$alipayNotify = new AlipayNotify($this->_aliapy_config);
		$verify_result = $alipayNotify->verifyNotify();
		if(! $verify_result) return false;
		$result = array(
						'out_trade_no'			=>	$_POST['out_trade_no'],//商家订单号
						'trade_no'				=>	$_POST['trade_no'],//支付宝交易号
						'total_fee'				=>	$_POST['price'],//总价格
						'trade_status'			=>	$_POST['trade_status'],//交易状态
						);
		return $result;
	}
	
	public function send_goods($params)
	{
		require_once(LIB_PATH . "Com/payment/alipay_warrant/alipay_service.class.php");
		$aliapy_config = array();
		$aliapy_config['partner']      = $this->_aliapy_config['partner'];
		//安全检验码，以数字和字母组成的32位字符
		$aliapy_config['key']          = $this->_aliapy_config['key'];
		//签名方式 不需修改
		$aliapy_config['sign_type']    = $this->_aliapy_config['sign_type'];
		//字符编码格式 目前支持 gbk 或 utf-8
		$aliapy_config['input_charset']= $this->_aliapy_config['input_charset'];
		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$aliapy_config['transport']    = $this->_aliapy_config['transport'];
		/**************************请求参数**************************/
		//必填参数//
		//支付宝交易号。它是登陆支付宝网站在交易管理中查询得到，一般以8位日期开头的纯数字（如：20100419XXXXXXXXXX）
		$trade_no		= $params['trade_no'];
		//物流公司名称
		$logistics_name	= '无需物流';
		//物流发货单号
		$invoice_no		= '无需物流';
		//物流发货时的运输类型，三个值可选：POST（平邮）、EXPRESS（快递）、EMS（EMS）
		$transport_type	= 'EXPRESS';
		/************************************************************/
		//构造要请求的参数数组，无需改动
		$parameter = array(
		"service"			=> "send_goods_confirm_by_platform",
		"partner"			=> trim($aliapy_config['partner']),
		"_input_charset"	=> trim(strtolower($aliapy_config['input_charset'])),
		"trade_no"			=> $trade_no,
		"logistics_name"	=> $logistics_name,
		"invoice_no"		=> $invoice_no,
		"transport_type"	=> $transport_type
		);

		//构造确认发货接口
		$alipayService = new AlipayService($aliapy_config);
		$doc = $alipayService->send_goods_confirm_by_platform($parameter);
		//解析XML
		$response = '';
		if( ! empty($doc->getElementsByTagName( "response" )->item(0)->nodeValue) ) {
			$response= $doc->getElementsByTagName( "response" )->item(0)->nodeValue;
		}
		return $response;
	}
}