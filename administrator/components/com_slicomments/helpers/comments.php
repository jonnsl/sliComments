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
}