<?php
// no direct access
defined('_JEXEC') or die;

class sliCommentsHelper
{
	public static function statusOptions()
	{
		$options[]	= JHtml::_('select.option', '1', 'COM_COMMENTS_OPTION_APPROVED');
		$options[]	= JHtml::_('select.option', '0', 'COM_COMMENTS_OPTION_PENDING');
		$options[]	= JHtml::_('select.option', '-1', 'COM_COMMENTS_OPTION_SPAM');
		$options[]	= JHtml::_('select.option', '-2', 'JTRASHED');
		$options[]	= JHtml::_('select.option', '*', 'JALL');
		return $options;
	}

	public static function highlight($string, $searchTerm)
	{
		if (stripos($searchTerm, 'id:') === false && stripos($searchTerm, 'author:') === false) {
			return preg_replace("/(".preg_quote($searchTerm).")/ui" ,'<span style="background:#FF0;"><b>$1</b></span>' , $string );
		}
		return $string;
	}
}