<?php
/**
 * @package		sliComments
 * @subpackage	Front end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class sliModel extends JModelList
{
	/**
	 * Parse bbcode into safe HTML
	 *
	 * @access protected
	 * @param  string $text
	 * @return string
	 */
	protected function _parseBBcode($text)
	{
		JLoader::register('Decoda', JPATH_ADMINISTRATOR . '/components/com_slicomments/libraries/decoda/Decoda.php');
		$code = new Decoda($text);
		$code->setEscapeHtml(!$this->params->get('html.allow', true));

		$filters = $this->params->get('bbcode.filters', array('default' => 1, 'text' => 1, 'image' => 1, 'quote' => 1, 'url' => 1, 'video' => 1));
		foreach ($filters as $filter => $enabled)
		{
			if ($enabled)
			{
				$class = ucfirst($filter).'Filter';
				$code->addFilter(new $class());
			}
		}

		$whitelist = $this->params->get('bbcode.whitelist', '');
		$whitelist = array_map('trim', explode(',', $whitelist));
		call_user_func_array(array($code, 'whitelist'), $whitelist);

		return $code->parse();
	}

	protected function _parse($text)
	{
		if ($this->params->get('bbcode.enabled', true)) {
			$text = $this->_parseBBcode($text);
		}
		if ($this->params->get('emoticons_enabled', true)) {
			$text = $this->_parseEmoticons($text);
		}
		if ($this->params->get('html.allow', true)) {
			$text = $this->_filterHtml($text);
		} else if (!$this->params->get('bbcode.enabled', true)) {
			$text = nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
		}

		return $text;
	}

	protected function _parseEmoticons($text)
	{
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/emoticon.php';
		$emoticon = new sliCommentsEmoticon($this->params->get('emoticons', array()));

		return $emoticon->parse($text);
	}

	public function _filterHtml($text)
	{
		// Filter settings
		$user		= JFactory::getUser();
		$userGroups	= JAccess::getGroupsByUser($user->get('id'));

		$filters = $this->params->get('html.filters');

		$blackListTags			= array();
		$blackListAttributes	= array();

		$whiteListTags			= array();
		$whiteListAttributes	= array();

		$noHtml		= false;
		$whiteList	= false;
		$blackList	= false;
		$unfiltered	= false;

		// Cycle through each of the user groups the user is in.
		// Remember they are include in the Public group as well.
		foreach ($userGroups as $groupId)
		{
			// May have added a group by not saved the filters.
			if (!isset($filters->$groupId)) {
				continue;
			}

			// Each group the user is in could have different filtering properties.
			$filterData = $filters->$groupId;
			$filterType	= strtoupper($filterData->filter_type);

			if ($filterType == 'NH') {
				// Maximum HTML filtering.
				$noHtml = true;
			}
			else if ($filterType == 'NONE') {
				// No HTML filtering.
				$unfiltered = true;
			}
			else {
				// Black or white list.
				// Preprocess the tags and attributes.
				$tags			= explode(',', $filterData->filter_tags);
				$attributes		= explode(',', $filterData->filter_attributes);
				$tempTags		= array_filter(array_map('trim', $tags));
				$tempAttributes	= array_filter(array_map('trim', $attributes));


				// Collect the black or white list tags and attributes.
				// Each list is cummulative.
				if ($filterType == 'BL') {
					$blackList				= true;
					$blackListTags			= array_merge($blackListTags, $tempTags);
					$blackListAttributes	= array_merge($blackListAttributes, $tempAttributes);
				}
				else if ($filterType == 'WL') {
					$whiteList				= true;
					$whiteListTags			= array_merge($whiteListTags, $tempTags);
					$whiteListAttributes	= array_merge($whiteListAttributes, $tempAttributes);
				}
			}
		}

		// Remove duplicates before processing (because the black list uses both sets of arrays).
		$blackListTags			= array_unique($blackListTags);
		$blackListAttributes	= array_unique($blackListAttributes);
		$whiteListTags			= array_unique($whiteListTags);
		$whiteListAttributes	= array_unique($whiteListAttributes);

		// Unfiltered assumes first priority.
		if ($unfiltered) {
			// Dont apply filtering.
		}
		else {
			// Black lists take second precedence.
			if ($blackList) {
				// Remove the white-listed attributes from the black-list.
				$filter = JFilterInput::getInstance(
					array_diff($blackListTags, $whiteListTags), 			// blacklisted tags
					array_diff($blackListAttributes, $whiteListAttributes), // blacklisted attributes
					1,														// blacklist tags
					1														// blacklist attributes
				);
			}
			// White lists take third precedence.
			else if ($whiteList) {
				$filter	= JFilterInput::getInstance($whiteListTags, $whiteListAttributes, 0, 0, 0);  // turn off xss auto clean
			}
			// No HTML takes last place.
			else {
				$filter = JFilterInput::getInstance();
			}

			$text = $filter->clean($text, 'html');
		}

		return $text;
	}

	public function validate($data)
	{
		$strlen = JString::strlen(JString::trim(strip_tags($data['text'])));

		if ($strlen < ($p = $this->params->get('minimum_chars', 5))) {
			$this->setError(JText::sprintf('COM_COMMENTS_ERROR_COMMENT_MINLENGTH', $p, $strlen));
			return false;
		}
		if ($strlen > ($p = $this->params->get('maximum_chars', 500))) {
			$this->setError(JText::sprintf('COM_COMMENTS_ERROR_COMMENT_MAXLENGTH', $p, $strlen));
			return false;
		}
		return true;
	}
}
