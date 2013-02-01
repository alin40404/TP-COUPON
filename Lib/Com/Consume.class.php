<?php
/**
 * 消费类
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Tue Apr 24 14:12:57 CST 2012
 */
class Consume
{
	const 	TYPE_MONEY			=	'money';
	const 	TYPE_CREDIT			=	'credit';
	
	/**
	 * 消费
	 * @param int		$amount			数量
	 * @param string	$type			类型
	 * @return int
	 *
	 */
	public static function spend($user_id, $amount, $type)
	{
		if(! self::check_type_valid($type)){
			return -1;
		}
		$userModel = D('User');
		$user = $userModel->info($user_id, array($type));
		if($user[$type] < $amount){
			return 0;
		}
		//更新本地积分或金钱
		$userModel->update($user_id, array($type => ($user[$type]-$amount)));
		//记录日志
		$data = array(
					'user_id'			=>	$user_id,
					'type'				=>	'spend',
					'money_type'		=>	$type,
					'amount'			=>	$amount,
					'addtime'			=>	LocalTime::getInstance()->gmtime()
					);
		M('consume_records')->add($data);
		return 1;
	}
	
	/**
	 * 增加
	 * @param int		$amount			数量
	 * @param string	$type			类型
	 * @return bool
	 *
	 */
	public static function increase($user_id, $amount, $type)
	{
		if(! self::check_type_valid($type)){
			return -1;
		}
		$userModel = D('User');
		$user = $userModel->info($user_id, array($type));
		//更新本地积分或金钱
		$userModel->update($user_id, array($type => ($user[$type]+$amount)));
		//记录日志
		$data = array(
					'user_id'			=>	$user_id,
					'type'				=>	'increase',
					'money_type'		=>	$type,
					'amount'			=>	$amount,
					'addtime'			=>	LocalTime::getInstance()->gmtime()
					);
		M('consume_records')->add($data);
		return 1;
	}
	
	public static function check_type_valid($type)
	{
		if($type !== self::TYPE_CREDIT && $type !== self::TYPE_MONEY){
			return false;
		}
		return true;
	}
}