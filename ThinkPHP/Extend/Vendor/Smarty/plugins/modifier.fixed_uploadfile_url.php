<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty count_paragraphs modifier plugin
 *
 * Type:     modifier<br>
 * Name:     fixed_uploadfile_url<br>
 * Purpose:  fixed the url of uploaded file
 * @author   anqiu xiao
 * @param string
 * @return string
 */
function smarty_modifier_fixed_uploadfile_url($string)
{
    return FixedUploadedFileUrl($string);
}