<?php
/**
 * 友情链接模型类
 */
class FriendLinkModel extends Model {
	protected $tableName = 'friend_link';
	public function getAll(array $limit = array()) {
		$result = array (
				'count' => 0,
				'data' => array () 
		);
		$result ['count'] = $this->count ();
		$result ['data'] = $this->order ( "sort_order ASC" )->limit ( "$limit[begin],$limit[offset]" )->select ();
		return $result;
	}
	
	/**
	 * 添加
	 *
	 * @int	$id
	 * 
	 * @param array $params        	
	 * @return bool
	 */
	public function addLink(&$id, array $params) {
		$id = $this->data ( $params )->add ();
		return true;
	}
	
	/**
	 * 编辑
	 *
	 * @param int $id        	
	 * @param array $params        	
	 * @return bool
	 */
	public function editLink($id, array $params) {
		if (empty ( $params )) {
			return false;
		}
		$this->where ( "link_id='$id'" )->save ( $params );
		return true;
	}
	
	/**
	 * 删除
	 *
	 * @param int $id        	
	 * @return bool
	 */
	public function del($id) {
		$this->where ( "link_id='$id'" )->delete ();
		return true;
	}
	public function info($id) {
		return $this->where ( "link_id='$id'" )->find ();
	}
}