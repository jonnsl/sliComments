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
	public function __get($name)
	{
		switch ($name) {
			case 'extension':
				return $this->getHelper();
			default:
				$trace = debug_backtrace();
				trigger_error(
					'Undefined property via __get(): ' . $name .
					' in ' . $trace[0]['file'] .
					' on line ' . $trace[0]['line'],
					E_USER_NOTICE);
				return null;
		}
	}

	public function getHelper($component = '')
	{
		static $helpers;
		if (empty($component)) $component = $this->getState('extension');
		if (!isset($helpers[$component]))
		{
			$file = JPATH_SITE . '/components/com_slicomments/plugins/'.$component. '/'.$component.'.php';
			if (file_exists($file)) {
				require $file;
			} else {
				JError::raiseError(500, 'File "'.$file.'" not found.');
			}

			$class = 'sliComments' . substr($component, 4);
			if (class_exists($class)){
				$helpers[$component] = new $class($this->params);
			} else {
				JError::raiseError(500, 'Class "'.$class.'" not found.');
			}
		}

		return $helpers[$component];
	}

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

	public function parse($text)
	{
		if ($this->params->get('bbcode.enabled', true)) {
			$text = $this->_parseBBcode($text);
		}
		if ($this->params->get('emoticons_enabled', true)) {
			$text = $this->_parseEmoticons($text);
		}
		if ($this->params->get('html.allow', true)) {
			$text = JComponentHelper::filterText($text);
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
