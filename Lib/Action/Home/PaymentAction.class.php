<?php
/**
 * PaymentAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sat Apr 28 10:05:45 CST 2012
 */
class PaymentAction extends HomeCommonAction 
{
	public function pay()
	{
		$localTimeObj = LocalTime::getInstance();
		//最新充值记录
		$res = M('payment')->where("`status`=104")->order('id DESC')->limit(10)->select();
		$payments = array();
		foreach ($res as $rs){
			$rs['addtime'] = $localTimeObj->local_date('m月d日 H:i:s', $rs['addtime']);
			$payments[] = $rs;
		}
		$this->assign('payments', $payments);
		$this->assign('page_title', '在线充值 - ');
		$this->assign('page_keywords', $this->_CFG['site_keywords']);
		$this->assign('page_description', $this->_CFG['site_description']);
		$this->display();
	}
	
	public function dopayment()
	{
		if($this->isPost()){
			C('TOKEN_ON', false);
			import('@.Com.payment.PaymentFactory');
			//订单号
			$out_trade_no = date('Ymdhis') . rand_string(4, 1);
			$subject      = $this->_CFG['site_name'] . '会员充值';
			$body		  = '';
			$total_fee    = floatval($_REQUEST['amount']);
			$show_url	  = 'http://'.$_SERVER['HTTP_HOST'] . __ROOT__;
			if($this->_CFG['alipay_type'] == 'direct'){
				$pay_type = 'alipay';
				$status = 103;
			}else if($this->_CFG['alipay_type'] == 'warrant'){
				$pay_type = 'AlipayWarrant';
				$status = 103;
			}
			$params = array(
							'out_trade_no'		=>	$out_trade_no,
							'subject'			=>	$subject,
							'body'				=>	$body,
							'total_fee'			=>	$total_fee,
							'show_url'			=>	$show_url
							);
			
			$data = array(
						'user_id'			=> $this->_user['user_id'],
						'nick'				=> $this->_user['nick'],
						'out_trade_no'		=> $out_trade_no,
						'amount'			=> $total_fee,
						'content'			=> '在线充值',
						'addtime'			=> LocalTime::getInstance()->gmtime(),
						'status'			=> $status
						);
			M('payment')->add($data);
			$payment = PaymentFactory::getPayment($pay_type);
			$html_text = $payment->buildForm($params);
			$this->assign('form_html', $html_text);
			$this->assign('page_title', '在线充值 - ');
			$this->display();
		}
	}

	public function pay_callback()
	{
		if(! defined('IN_PAYMENT')) exit('hack attempt.');
		$type = $_REQUEST['type'];
		if($this->_CFG['alipay_type'] == 'direct'){
			$this->_pay_callback_alipay();
		}else if($this->_CFG['alipay_type'] == 'warrant'){
			$this->_pay_callback_alipay_warrant();
		}
	}
	
	public function pay_notify()
	{
		if(! defined('IN_PAYMENT')) exit('hack attempt.');
		$type = $_REQUEST['type'];
		if($this->_CFG['alipay_type'] == 'direct'){
			$this->_pay_notify_alipay();
		}else if($this->_CFG['alipay_type'] == 'warrant'){
			$this->_pay_notify_alipay_warrant();
		}
	}
	
	private function _pay_callback_alipay()
	{
		import('@.Com.payment.PaymentFactory');
		$payment = PaymentFactory::getPayment('alipay');
		$result = $payment->verifyReturn();
		if($result !== false){
			$result['trade_status'] = trim($result['trade_status']);
			if($result['trade_status'] == 'TRADE_FINISHED' || $result['trade_status'] == 'TRADE_SUCCESS') {
				$record = M('payment')->where("out_trade_no='$result[out_trade_no]'")->find();
				if($record['status'] == 104){
					$this->assign('jumpUrl', reUrl('User/consume_records'));
					$this->success('支付成功');
				}
				if($record && $record['amount'] == $result['total_fee']){
					//记录日志
					Consume::increase($record['user_id'], $result['total_fee'], Consume::TYPE_MONEY);
					M('payment')->where("out_trade_no='$result[out_trade_no]'")->save(array('status'=>'104','trade_no'=>$result['trade_no']));
					$this->assign('jumpUrl', reUrl('User/consume_records'));
					$this->success('支付成功');
				}else{
					$this->assign('jumpUrl', reUrl('User/consume_records'));
					$this->error('支付失败<br />支付记录不存在或支付金额错误');
				}
			}else{
				$this->assign('jumpUrl', reUrl('User/consume_records'));
				$this->error('支付失败<br />' . $result['trade_status']);
			}
		}else{
			$this->assign('jumpUrl', reUrl('User/consume_records'));
			$this->error('支付失败');
		}
	}
	
	private function _pay_callback_alipay_warrant()
	{
		import('@.Com.payment.PaymentFactory');
		$payment = PaymentFactory::getPayment('AlipayWarrant');
		$result = $payment->verifyReturn();
		if($result !== false){
			$result['trade_status'] = trim($result['trade_status']);
			//等待发货
			if($result['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
				$record = M('payment')->where("out_trade_no='$result[out_trade_no]'")->find();
				if($record['status'] == 104){
					$this->assign('jumpUrl', reUrl('User/consume_records'));
					$this->success('支付成功');
				}
				if($record && $record['amount'] == $result['total_fee']){
					//自动发货
					$response = $payment->send_goods(array('trade_no'=>$result['trade_no']));
					if($response){
						M('payment')->where("out_trade_no='$result[out_trade_no]'")->save(array('status'=>105,'trade_no'=>$result['trade_no']));
						$this->assign('jumpUrl', reUrl('User/consume_records'));
						$this->success('请登录支付宝确认收货.<br />支付宝交易号：'.$result['trade_no']);
					}else{
						M('payment')->where("out_trade_no='$result[out_trade_no]'")->save(array('status'=>'102','trade_no'=>$result['trade_no']));
						$this->assign('jumpUrl', reUrl('User/consume_records'));
						$this->error('发生错误，请联系客服.');
					}
				}else{
					$this->assign('jumpUrl', reUrl('User/consume_records'));
					$this->error('支付失败<br />支付记录不存在或支付金额错误');
				}
			}
			//等待确认收货
			else if($result['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
					$this->assign('jumpUrl', reUrl('User/consume_records'));
					$this->success('请登录支付宝确认收货.<br />支付宝交易号：'.$result['trade_no']);
			}else{
				$this->assign('jumpUrl', reUrl('User/consume_records'));
				$this->error('支付失败<br />' . $result['trade_status']);
			}
		}else{
			$this->assign('jumpUrl', reUrl('User/consume_records'));
			$this->error('支付失败');
		}
	}
	
	private function _pay_notify_alipay()
	{
		import('@.Com.payment.PaymentFactory');
		$payment = PaymentFactory::getPayment('alipay');
		$result = $payment->verifyNotify();
		if($result !== false){
			$result['trade_status'] = trim($result['trade_status']);
			if($result['trade_status'] == 'TRADE_FINISHED' || $result['trade_status'] == 'TRADE_SUCCESS') {
				$record = M('payment')->where("out_trade_no='$result[out_trade_no]'")->find();
				if($record['status'] == 104){
					exit('success');
				}
				if($record && $record['amount'] == $result['total_fee']){
					//记录日志
					Consume::increase($record['user_id'], $result['total_fee'], Consume::TYPE_MONEY);
					M('payment')->where("out_trade_no='$result[out_trade_no]'")->save(array('status'=>'104','trade_no'=>$result['trade_no']));
					exit('success');
				}else{
					exit('fail');
				}
			}else{
				exit('fail');
			}
		}else{
			exit('fail');
		}
	}
	
	private function _pay_notify_alipay_warrant()
	{
		import('@.Com.payment.PaymentFactory');
		$payment = PaymentFactory::getPayment('AlipayWarrant');
		$result = $payment->verifyNotify();
		if($result !== false){
			$result['trade_status'] = trim($result['trade_status']);
			if($result['trade_status'] == 'WAIT_BUYER_PAY' || $result['trade_status'] == 'WAIT_SELLER_SEND_GOODS'
				|| $result['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS' || $result['trade_status'] == 'TRADE_FINISHED') {
				$record = M('payment')->where("out_trade_no='$result[out_trade_no]'")->find();
				if($record['status'] == 104){
					exit('success');
				}
				if($record && $record['amount'] == $result['total_fee']){
					$status = 102;
					//等待买家付款
					if($result['trade_status'] == 'WAIT_BUYER_PAY'){
						$status = 103;
					}
					//等待卖家发货
					else if($result['trade_status'] == 'WAIT_SELLER_SEND_GOODS'){
						//自动发货
						$response = $payment->send_goods(array('trade_no'=>$result['trade_no']));
						if($response){
							$status = 105;
						}else{
							$status = 102;
						}
					}
					//等待买家确认收货
					else if($result['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS'){
						$status = 105;
					}
					//交易完成
					else if($result['trade_status'] == 'TRADE_FINISHED'){
						if($record && $record['amount'] == $result['total_fee']){
							//记录日志
							Consume::increase($record['user_id'], $result['total_fee'], Consume::TYPE_MONEY);
							$status = 104;
						}else{
							$status = $record['status'];
						}
					}
					M('payment')->where("out_trade_no='$result[out_trade_no]'")->save(array('status'=>$status,'trade_no'=>$result['trade_no']));
					exit('success');
				}else{
					exit('fail');
				}
			}else{
				exit('fail');
			}
		}else{
			exit('fail');
		}
	}
}