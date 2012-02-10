<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class sliCommentsHelper
{
	public static function statusOptions()
	{
		$options[]	= JHtml::_('select.option', '1', 'COM_COMMENTS_OPTION_APPROVED');
		$options[]	= JHtml::_('select.option', '0', 'COM_COMMENTS_OPTION_PENDING');
		$options[]	= JHtml::_('select.option', '-1', 'COM_COMMENTS_OPTION_SPAM');
		$options[]	= JHtml::_('select.option', '-2', 'JTRASHED');
		$options[]	= JHtml::_('select.option', '-3', 'COM_COMMENTS_OPTION_FLAGGED');
		$options[]	= JHtml::_('select.option', '*', 'JALL');
		return $options;
	}

	public static function highlight($string, $searchTerm)
	{
		if (stripos($searchTerm, 'id:') === false && stripos($searchTerm, 'author:') === false) {
			return preg_replace("/(".preg_quote($searchTerm, '/').")/ui" ,'<span style="background:#FF0;"><b>$1</b></span>' , $string );
		}
		return $string;
	}

	protected static $times = array(
		'y' => 'COM_COMMENTS_YEARS_AGO',
		'm' => 'COM_COMMENTS_MONTHS_AGO',
		'd' => 'COM_COMMENTS_DAYS_AGO',
		'h' => 'COM_COMMENTS_HOURS_AGO',
		'i' => 'COM_COMMENTS_MINUTES_AGO',
		's' => 'COM_COMMENTS_SECONDS_AGO',
	);

	/**
	 * Returns the difference from the current time in the format X time ago
	 *
	 * @param int $from Time from which the difference begins.
	 * @param int $to   Optional. Time to end the time difference. Default becomes now if not set.
	 *
	 * @note   Borrowed from PHP.net
	 * @return string
	 * @see    http://www.php.net/manual/en/function.time.php#105257
	 */
	public static function human_time_diff($from, $to = 'now')
	{
		$to   = JFactory::getDate($to);
		$from = JFactory::getDate($from);
		if (version_compare(PHP_VERSION, '5.3.0', '<')) {
			$diff = self::dateTimeDiff($from, $to);
		} else {
			$diff = $from->diff($to);
		}

		foreach (self::$times as $key => $value)
		{
			$since = $diff->$key;
			if ($since > 0){
				return JText::plural($value, $since);
			}
		}

		return JText::_('COM_COMMENTS_SECONDS_AGO_1');
	}

	protected static function dateTimeDiff($to, $from)
	{
		$date = new stdClass();

		foreach (self::$times as $format => $string)
		{
			$date->$format = $from->format($format, true) - $to->format($format, true);
		}

		return $date;
	}
}
