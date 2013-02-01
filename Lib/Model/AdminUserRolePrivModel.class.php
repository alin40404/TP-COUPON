<?php
/**
 * AdminUserRolePrivModel.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Wed May 02 17:49:07 CST 2012
 */
class AdminUserRolePrivModel extends Model 
{	
	/**
	 * 根据权限控制符查找相关的角色ID
	 *
	 * @param string $module
	 * @param string $priv
	 */
	public function getRoleidByPriv($module, $priv)
	{
		$res = $this->field('roleid')->where("value='$module' AND priv='$priv'")->select();
		$return = array();
		foreach ($res as $rs){
			$return[] = $rs['roleid'];
		}
		return $return;
	}
}