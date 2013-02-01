<?php
/**
 * CouponCodeCodesModel.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 01:41:24 CST 2012
 */
/**
 * 优惠码模型类
 */
class CouponCodeCodesModel extends Model 
{							
    /**
     * 添加
     * 
     * @int				$id
     * @param array     $params
     * @return bool
     */
    public function _add(array $params)
    {
    	return  $this->data($params)->add();
    }
    
    /**
     * 更新信息
     * 
     * @param array				$id
     * @param array				$params
     * @return bool
     */
    public function _edit($id, array $params)
    {
    	$this->where("id='$id'")->save($params);
    	return true;
    }
    
    /**
     * 删除信息
     * 
     * @param array				$id
     * @return bool
     */
    public function _delete($id)
    {
    	$this->where("id='$id'")->delete();
        return true;
    }
    
    public function _deleteByCid($id)
    {
    	$this->where("c_id='$id'")->delete();
        return true;
    }
    
    /**
     * 获取信息
     * 
     * @param int	$id
     * @return array
     */
    public function info($id)
    {
    	return $this->where("id='$id'")->find();
    }
    
    public function getOneByUid($user_id, $c_id, array $params=array())
    {
    	$where = "c_id='$c_id' AND user_id='$user_id'";
    	if(isset($params['b_time']) && $params['b_time']){
    		$where .= " AND fetch_time>=$params[b_time]";
    	}
    	if(isset($params['e_time']) && $params['e_time']){
    		$where .= " AND fetch_time<=$params[e_time]";
    	}
    	return $this->where($where)->find();
    }
    
    public function pull($c_id, $user_id, $nick, $fetch_time)
    {
    	$code = $this->field("id,code")->where("c_id='$c_id' AND user_id=0")->find();
    	if(! $code) return null;
    	$data =array(
    				'user_id'		=>	$user_id,
    				'nick'			=>	$nick,
    				'fetch_time'	=>	$fetch_time
    				);
    	$this->where("id='$code[id]'")->save($data);
    	return $code['code'];
    }
    
    public function getAll($id, array $limit = array())
    {
    	$result = array('count'=>0,'data'=>array());
    	$fields = 'c.*,u.nick';
    	$where = "c.c_id='$id'";
    	$result['count'] = $this->where($where)->count();
    	$result['data'] = $this->field($fields)
    						->table($this->getTableName().' AS c')
    						->join(M('user')->getTableName() . ' AS u ON u.user_id=c.user_id')
    						->where($where)
    						->limit("$limit[begin], $limit[offset]")
    						->select();
    	return $result;
    }
    
    public function myCodes($user_id, array $limit = array())
    {
    	$result = array('count'=>0,'data'=>array());
    	$select = "SELECT ccc.c_id,ccc.code,ccc.fetch_time,ccc.user_id,ccc.nick,c.m_id,c.title,c.c_type,c.price_type
    				,c.price,c.m_name,c.money_max,c.money_reduce,c.money_amount,c.expiry_type,c.expiry,c.fetched_amount
    				,cd.directions,m.logo";
    	$sql = " FROM ".$this->getTableName() . ' AS ccc';
    	$sql .= " LEFT JOIN " . M('coupon_code')->getTableName() . ' AS c ON c.c_id=ccc.c_id';
    	$sql .= " LEFT JOIN " . M('coupon_code_data')->getTableName() . ' AS cd ON cd.c_id=ccc.c_id';
    	$sql .= " LEFT JOIN ".M('coupon_code_mall')->getTableName().' AS m ON m.id=c.m_id';
    	$sql .= " WHERE ccc.user_id='$user_id'";
    	$res = $this->query("SELECT COUNT(*) AS tp_count $sql LIMIT 1");
    	$result['count'] = empty($res) ? 0 : $res[0]['tp_count'];
    	$sql .= " ORDER BY ccc.fetch_time DESC LIMIT $limit[begin], $limit[offset]";
    	$result['data'] = $this->query($select . $sql);
    	return $result;
    }
    
    public function fetch_lists(array $limit = array())
    {
    	$result = array('count'=>0,'data'=>array());
    	$select = "SELECT `temp`.c_id,`temp`.fetch_time,`temp`.user_id,`temp`.nick,c.title,c.c_type";
    	$select .= ",c.m_name,c.price_type,c.price,c.money_max,c.money_reduce,c.expiry_type,c.expiry,c.money_amount,c.amount,c.fetched_amount,m.logo";
    	$sql = " FROM (SELECT c_id,fetch_time,user_id,nick FROM ".$this->getTableName()." WHERE user_id>0 ORDER BY fetch_time DESC) `temp`";
    	$sql .= " LEFT JOIN " . M('coupon_code')->getTableName() . ' AS c ON c.c_id=`temp`.c_id';
    	$sql .= " LEFT JOIN " . M('coupon_code_mall')->getTableName() . ' AS m ON m.id=c.m_id';
    	$sql .= " WHERE c.is_active=1";
    	$sql .= " GROUP BY `temp`.c_id";
    	$res = $this->query("SELECT COUNT(*) AS tp_count $sql LIMIT 1");
    	$result['count'] = empty($res) ? 0 : $res[0]['tp_count'];
    	$sql .= " ORDER BY `temp`.fetch_time DESC, c.expiry DESC LIMIT $limit[begin], $limit[offset]";
    	$result['data'] = $this->query($select . $sql);
    	return $result;
    }
    
    /**
     * 最新被领取的优惠券
     *
     * @param unknown_type $limit
     */
    public function fetch_latest($limit=10)
    {
    	$sql = "SELECT c_id,fetch_time,user_id,nick FROM (SELECT c_id,fetch_time,user_id,nick FROM ".$this->getTableName()." WHERE user_id>0 ORDER BY fetch_time DESC) `temp`";
    	$sql .= " GROUP BY c_id ORDER BY fetch_time DESC LIMIT $limit";
    	$res = $this->query($sql);
    	$data = array();
    	foreach ($res as $rs){
    		$sql = "SELECT c.title,c.c_type,c.m_name,c.money_max,c.money_reduce";
    		$sql .= ",c.money_amount,c.fetched_amount,m.logo";
    		$sql .= " FROM ".M('coupon_code')->getTableName().' AS c';
    		$sql .= " LEFT JOIN ".M('coupon_code_mall')->getTableName().' AS m ON m.id=c.m_id';
    		$sql .= " WHERE c.c_id='$rs[c_id]' LIMIT 1";
    		$r = $this->query($sql);
    		$data[] = array_merge($rs, $r[0]);
    	}
    	return $data;
    }
    
    /**
     * 最新领取记录
     *
     * @param int $limit
     * @return array
     */
    public function record_top($c_id, $limit=100)
    {
    	$_CFG = load_config();
    	$timestamp = intval($_CFG['timezone'])*3600;
    	$fields = 'user_id,nick,code,fetch_time+' .$timestamp . ' AS pull_time';
    	//$fields = 'user_id,nick,code,fetch_time AS pull_time';
    	return $this->field($fields)->where("c_id='$c_id' AND user_id>0")->order('fetch_time DESC')->limit($limit)->select();
    }
}