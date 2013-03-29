<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.form.formfield');
jimport('joomla.filesystem.folder');

class JFormFieldEmoticons extends JFormField
{
	protected $type = 'Emoticons';

	protected function getInput()
	{
		$this->options = $this->getOptions();
		if (empty($this->options)) return JText::_('COM_COMMENTS_EMOTICONS_NO_FILES_FOUND');
		JHtml::_('script', 'slicomments/emoticons.js', true, true);
		JHtml::_('stylesheet', 'slicomments/emoticons.css', array(), true);
		$emoticons = !empty($this->value) ? $this->value : $this->getDefault();

		$html[] = '<button id="add-emoticon" class="btn">'.JText::_('COM_COMMENTS_EMOTICONS_ADD').'</button>';
		$html[] = '<ul id="emoticons-list" data-root="'.htmlentities(JURI::root(true), ENT_QUOTES, 'UTF-8').'">';
		foreach ($emoticons as $i => $emoticon)
		{
			$emoticon = (object) $emoticon;
			if (!file_exists(JPATH_ROOT.'/'.$emoticon->file)) continue;
			$html[] = '<li class="emoticon">';
			$html[] = JHtml::_('image', 'media/slicomments/img/delete16.png', '', 'class="delete"', false);
			$html[] = '<input type="text" name="'.$this->name.'['.$i.'][emoticon]" value="'.htmlspecialchars($emoticon->emoticon, ENT_QUOTES, 'UTF-8').'"/>';
			$html[] = $this->getImageList($emoticon->file, $i);
			$html[] = JHtml::_('image', $emoticon->file, $emoticon->emoticon, (empty($emoticon->file) ? 'style="display:none"' : '') . ' class="emoticon-preview"');
			$html[] = '</li>';
		}
		$html[] = '</ul>';
		$html[] = '<button id="add-all-emoticons" class="btn">'.JText::_('COM_COMMENTS_EMOTICONS_ADD_ALL').'</button>';
		$html[] = '<button id="remove-all-emoticons" class="btn">'.JText::_('COM_COMMENTS_EMOTICONS_REMOVE_ALL').'</button>';

		return implode($html);
	}

	protected function getLabel()
	{
		return '';
	}

	protected function getDefault()
	{
		return json_decode('{"0":{"emoticon":"","file":""}}');
	}


	protected function getImageList($value, $i)
	{
		return JHtml::_('select.genericlist', $this->options, $this->name.'['.$i.'][file]', '', 'value', 'text', $value, $this->id.'_'.$i);
	}

	protected function getOptions()
	{
		$options = array();
		$files = array();
		$filter = '\.png$|\.gif$|\.jpg$|\.bmp$|\.ico$|\.jpeg$|\.psd$|\.eps$';

		// Get the default emoticons.
		$path = JPATH_ROOT .'/media/slicomments/img/emoticons';
		if (is_dir($path) && ($files1 = JFolder::files($path, $filter, false, true)))
		{
			$files = array_merge($files, $files1);
		}

		// Get the custom emoticons.
		$custom_path = JPATH_ROOT.'/images/emoticons';
		if (is_dir($custom_path) && ($files2 = JFolder::files($custom_path, $filter, false, true)))
		{
			$files = array_merge($files, $files2);
		}

		// Build the options list from the list of files.
		if (!empty($files))
		{
			$options[] = JHtml::_('select.option', '', '');
			foreach($files as $file)
			{
				$name = pathinfo($file);
				$name = preg_replace('/'.preg_quote('.'.$name['extension']).'$/', '', $name['filename']);
				$file = preg_replace('/^'.preg_quote(JPATH_ROOT.'/', '/').'/i', '', $file);
				$file = str_replace(DIRECTORY_SEPARATOR, '/', $file);
				$options[] = JHtml::_('select.option', $file, $name);
			}
		}

		return $options;
	}
}
