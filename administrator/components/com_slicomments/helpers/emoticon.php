<?php
namespace sliComments;

// No direct access
defined('_JEXEC') or die;

class Emoticon
{
	protected $_emoticons = array();

	public function __construct($emoticons = array())
	{
		foreach ($emoticons as $emoticon)
		{
			$this->_emoticons[trim($emoticon->emoticon)] = trim($emoticon->file);
		}
	}

	/**
	 * Parse out the emoticons and replace with images.
	 * 
	 * @access public
	 * @param  string $content
	 * @return string
	 */
	public function Parse($content)
	{
		if (!empty($this->_emoticons))
		{
			foreach ($this->_emoticons as $smile => $file)
			{
				$content = preg_replace_callback('/(\s|^)('. preg_quote($smile, '/') .')(\s|$)/is', array($this, '_emoticonCallback'), $content);
			}
		}

		return $content;
	}

	/**
	 * Callback for smiley processing.
	 * 
	 * @access protected
	 * @param array $matches
	 * @return string 
	 */
	protected function _emoticonCallback($matches)
	{
		$smiley = $matches[2];

		if (!isset($this->_emoticons[$smiley])) {
			return $matches[0];
		}

		$image = \JHtml::_('image', $this->_emoticons[$smiley], $smiley, 'title="'.$smiley.'" class="emoticon"');

		return $matches[1] . $image . $matches[3];
	}
	
}
