<?php
/**
 * PaymentAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Tue May 01 22:49:57 CST 2012
 */
class PaymentAction extends AdminCommonAction 
{
	public function index()
	{
		$nick = isset($_REQUEST['nick']) && $_REQUEST['nick'] ? $_REQUEST['nick'] : '';
		$page = isset($_REQUEST['page']) && $_REQUEST['page'] >= 1 ? $_REQUEST['page'] : 1;
    	$pageLimit = 15;
    	$localTimeObj = LocalTime::getInstance();
    	$status_conf = PaymentConf::status_conf();
    	$where = "1=1";
    	if($nick){
    		$where .= " AND nick LIKE '%$_REQUEST[nick]%'";
    	}
    	$res = array('count'=>0,'data'=>array());
    	$res['count'] = M('payment')->where($where)->count();
    	$res['data'] = M('payment')->where($where)->order('id DESC')->limit(($page-1)*$pageLimit . ",$pageLimit")->select();
    	$records = array();
    	foreach ($res['data'] as $rs){
    		$rs['addtime'] = $localTimeObj->local_date($this->_CFG['time_format'], $rs['addtime']);
    		$rs['status_type'] = $status_conf[$rs['status']];
    		$records[] = $rs;
    	}
    	$this->assign('records', $records);
    	$page_url = "?g=".GROUP_NAME."&m=".MODULE_NAME."&a=".ACTION_NAME."&page=[page]&nick=$nick";
    	$p=new Page($page,
    			$pageLimit,
    			$res['count'],
    			$page_url,
    			5,
    			5);
    	$pagelink=$p->showStyle(3);
    	$this->assign('pagelink', $pagelink);
		$this->assign('_hash_', buildFormToken());
		$this->assign('ur_href', '充值管理 &gt; 充值记录');
		$this->display();
	}
	
	public function del()
	{
		if($this->isAjax()){
			$id = intval($_REQUEST['id']);
			$payment = M('payment')->where("id='$id'")->find();
			if($payment['status'] != 104){
				M('payment')->where("id='$id'")->delete();
				$this->ajaxReturn('', '删除成功' ,1);
			}else{
				$this->ajaxReturn('', '删除失败' ,0);
			}
		}
	}
	
	/**
	 * 发货
	 *
	 */
	public function send_goods()
	{
		if($this->isAjax()){
			$id = intval($_REQUEST['id']);
			$record = M('payment')->where("id='$id'")->find();
			if($record['status'] == 104 || $record['status'] == 105){
				$this->ajaxReturn('', '该交易已发货或已完成' ,0);
			}
			import('@.Com.payment.PaymentFactory');
			$payment = PaymentFactory::getPayment('AlipayWarrant');
			$response = $payment->send_goods(array('trade_no'=>$record['trade_no']));
			if($response){
				M('payment')->where("id='$id'")->save(array('status'=>105));
				$this->ajaxReturn('', '操作成功' ,1);
			}else{
				$this->ajaxReturn('', '操作失败' ,0);
			}
		}
	}
	
	/**
	 * 确认完成
	 *
	 */
	public function finish()
	{
		if($this->isAjax()){
			$id = intval($_REQUEST['id']);
			$record = M('payment')->where("id='$id'")->find();
			if($record['status'] == 104){
				$this->ajaxReturn('', '该交易已完成' ,0);
			}
			//记录日志
			Consume::increase($record['user_id'], $record['amount'], Consume::TYPE_MONEY);
			M('payment')->where("id='$id'")->save(array('status'=>104));
			$this->ajaxReturn('', '操作成功' ,1);
		}
	}
}