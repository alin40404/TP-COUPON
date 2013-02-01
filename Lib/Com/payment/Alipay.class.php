<?php
/**
 * Alipay.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Fri Apr 27 15:24:54 CST 2012
 */
class Alipay implements PaymentImpl
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
		require_once(LIB_PATH . "Com/payment/alipay/alipay_service.class.php");
		
		/**************************请求参数**************************/

		//必填参数//

		//请与贵网站订单系统中的唯一订单号匹配
		$out_trade_no = $params['out_trade_no'];
		//订单名称，显示在支付宝收银台里的“商品名称”里，显示在支付宝的交易管理的“商品名称”的列表里。
		$subject      = $params['subject'];
		//订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里
		$body         = $params['body'];
		//订单总金额，显示在支付宝收银台里的“应付总额”里
		$total_fee    = $params['total_fee'];

		//扩展功能参数——默认支付方式//
		//默认支付方式，取值见“即时到帐接口”技术文档中的请求参数列表
		$paymethod    = '';
		//默认网银代号，代号列表见“即时到帐接口”技术文档“附录”→“银行列表”
		$defaultbank  = '';


		//扩展功能参数——防钓鱼//

		//防钓鱼时间戳
		$anti_phishing_key  = '';
		//获取客户端的IP地址，建议：编写获取客户端IP地址的程序
		$exter_invoke_ip = '';
		//注意：
		//1.请慎重选择是否开启防钓鱼功能
		//2.exter_invoke_ip、anti_phishing_key一旦被使用过，那么它们就会成为必填参数
		//3.开启防钓鱼功能后，服务器、本机电脑必须支持SSL，请配置好该环境。
		//示例：
		//$exter_invoke_ip = '202.1.1.1';
		//$ali_service_timestamp = new AlipayService($aliapy_config);
		//$anti_phishing_key = $ali_service_timestamp->query_timestamp();//获取防钓鱼时间戳函数


		//扩展功能参数——其他//

		//商品展示地址，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		$show_url			= $params['show_url'];
		//自定义参数，可存放任何内容（除=、&等特殊字符外），不会显示在页面上
		$extra_common_param = '';

		//扩展功能参数——分润(若要使用，请按照注释要求的格式赋值)
		$royalty_type		= "";			//提成类型，该值为固定值：10，不需要修改
		$royalty_parameters	= "";
		//注意：
		//提成信息集，与需要结合商户网站自身情况动态获取每笔交易的各分润收款账号、各分润金额、各分润说明。最多只能设置10条
		//各分润金额的总和须小于等于total_fee
		//提成信息集格式为：收款方Email_1^金额1^备注1|收款方Email_2^金额2^备注2
		//示例：
		//royalty_type 		= "10"
		//royalty_parameters= "111@126.com^0.01^分润备注一|222@126.com^0.01^分润备注二"

		/************************************************************/

		//构造要请求的参数数组
		$parameter = array(
		"service"			=> "create_direct_pay_by_user",
		"payment_type"		=> "1",

		"partner"			=> trim($this->_aliapy_config['partner']),
		"_input_charset"	=> trim(strtolower($this->_aliapy_config['input_charset'])),
		"seller_email"		=> trim($this->_aliapy_config['seller_email']),
		"return_url"		=> trim($this->_aliapy_config['return_url']),
		"notify_url"		=> trim($this->_aliapy_config['notify_url']),

		"out_trade_no"		=> $out_trade_no,
		"subject"			=> $subject,
		"body"				=> $body,
		"total_fee"			=> $total_fee,

		"paymethod"			=> $paymethod,
		"defaultbank"		=> $defaultbank,

		"anti_phishing_key"	=> $anti_phishing_key,
		"exter_invoke_ip"	=> $exter_invoke_ip,

		"show_url"			=> $show_url,
		"extra_common_param"=> $extra_common_param,

		"royalty_type"		=> $royalty_type,
		"royalty_parameters"=> $royalty_parameters
		);

		//构造即时到帐接口
		$alipayService = new AlipayService($this->_aliapy_config);
		$html_text = $alipayService->create_direct_pay_by_user($parameter);
		return $html_text;
	}
	
	/**
	 * 验证通知结果
	 *
	 * @return bool|array
	 */
	public function verifyReturn()
	{
		require_once(LIB_PATH . "Com/payment/alipay/alipay_notify.class.php");
		$alipayNotify = new AlipayNotify($this->_aliapy_config);
		$verify_result = $alipayNotify->verifyReturn();
		if(! $verify_result) return false;
		$result = array(
						'out_trade_no'			=>	$_GET['out_trade_no'],//商家订单号
						'trade_no'				=>	$_GET['trade_no'],//支付宝交易号
						'total_fee'				=>	$_GET['total_fee'],//总价格
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
		require_once(LIB_PATH . "Com/payment/alipay/alipay_notify.class.php");
		$alipayNotify = new AlipayNotify($this->_aliapy_config);
		$verify_result = $alipayNotify->verifyNotify();
		if(! $verify_result) return false;
		$result = array(
						'out_trade_no'			=>	$_POST['out_trade_no'],//商家订单号
						'trade_no'				=>	$_POST['trade_no'],//支付宝交易号
						'total_fee'				=>	$_POST['total_fee'],//总价格
						'trade_status'			=>	$_POST['trade_status'],//交易状态
						);
		return $result;
	}
}