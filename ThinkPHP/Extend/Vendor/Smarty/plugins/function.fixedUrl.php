<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {fixedUrl} function plugin
 *
 * Type:     function<br>
 * Name:     mailto<br>
 * Date:     2012-01-30
 * Author:	 anqiu xiao
 *
 * Examples:
 * <pre>
 * {fixedUrl url="Hotel/hall?id=`$item.hall_id`"}
 * </pre>
 * @param    array
 * @param    Smarty
 * @return   string
 */
function smarty_function_fixedUrl($params, &$smarty)
{
	return reUrl($params['url']);
}