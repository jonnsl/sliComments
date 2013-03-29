<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
jimport('joomla.filesystem.folder');

class JFormFieldForm extends JFormField
{
	protected $type = 'Form';


	protected function getInput()
	{
		return version_compare(JVERSION, "3.0", "ge") ? $this->_getInput3() : $this->_getInput2();
	}

	private function _getInput3()
	{
		$extensions = JFolder::folders(JPATH_SITE . '/components/com_slicomments/plugins');

		$html1 = array();
		$html2 = array();

		$html1[] = '<ul class="nav nav-tabs" id="configTabs">';
		$html2[] = '<div class="tab-content">';
		foreach ($extensions as $k => $extension)
		{
			$enabled = self::isEnabled($extension);

			$html1[] = '<li class="'. ($k == 0 ? 'active' : '') .'">';
			if ($enabled) {
				$html1[] = '<a href="#' . $extension . '" data-toggle="tab">'. JText::_('COM_COMMENTS_'.strtoupper($extension)) . '</a>';
			} else {
				$html1[] = '<a href="javascript:void(0)" class="disabled">'. JText::_('COM_COMMENTS_'.strtoupper($extension)) . '</a>';
			}
			$html1[] = '</li>';

			if (!$enabled) {
				continue;
			}

			$form = $this->getForm($extension);
			if (isset($this->value[$extension])) {
				$form->bind($this->value[$extension]);
			}

			$html2[] = '<div class="tab-pane'. ($k == 0 ? ' active' : '') .'" id="' . $extension . '">';
			foreach ($form->getFieldset() as $field)
			{
				$field->group = 'extension.'.$extension;
				$field->name = $field->getName($field->fieldname);
				$field->id = $field->getId((string) $field->element['id'], $field->fieldname);
				$html2[] = '<div class="control-group">';
				if (!$field->hidden) {
					$html2[] = '<div class="control-label">';
					$html2[] = $field->getLabel();
					$html2[] = '</div>';
				}
				$html2[] = '<div class="controls">';
				$html2[] = $field->getInput();
				$html2[] = '</div>';
				$html2[] = '</div>';
			}
			$html2[] = '</div>';
		}
		$html1[] = '</ul>';
		$html2[] = '</div>';

		return implode($html1).implode($html2);
	}

	private function _getInput2()
	{
		$html = array();
		$html[] = JHtml::_('tabs.start', 'config-tabs-com_slicomments_configuration_extension', array('useCookie'=>1));
		$extensions = JFolder::folders(JPATH_SITE . '/components/com_slicomments/plugins');
		foreach ($extensions as $extension)
		{
			$enabled = self::isEnabled($extension);

			$html[] = JHtml::_('tabs.panel', JText::_('COM_COMMENTS_'.strtoupper($extension)), 'publishing-details' . (!$enabled ? ' disabled' : ''));

			if (!$enabled) {
				continue;
			}

			$form = $this->getForm($extension);
			if (isset($this->value[$extension])) {
				$form->bind($this->value[$extension]);
			}

			$html[] = '<ul class="config-option-list">';
			foreach ($form->getFieldset() as $field)
			{
				$field->group = 'extension.'.$extension;
				$field->name = $field->getName($field->fieldname);
				$field->id = $field->getId((string) $field->element['id'], $field->fieldname);
				$html[] = '<li>';
				if (!$field->hidden) {
					$html[] = $field->getLabel();
				}
				$html[] = $field->getInput();
				$html[] = '</li>';
			}
			$html[] = '</ul>';
			$html[] = '<div class="clr"></div>';
		}
		$html[] = JHtml::_('tabs.end');

		// Remove click events from disabled tabs
		$html[] = '<script type="text/javascript">';
		$html[] = 'window.addEvent(\'domready\', function(){$$(\'dl.tabs dt.tabs.disabled\').removeEvents(\'click\');});';
		$html[] = '</script>';

		return implode($html);
	}

	private function getForm($name)
	{
		// Add the search path for the admin component config.xml file.
		JForm::addFormPath(JPATH_SITE.'/components/com_slicomments/plugins/'.$name);

		try
		{
			$form = JForm::getInstance('com_slicomments.config.'.$name, 'config', array('control' => 'jform'), false, '/config');
		}
		catch (Exception $e)
		{
			JError::raiseError(0, $e->getMessage());
			return false;
		}

		return $form;
	}

	protected static function isEnabled($element)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('enabled')
			->from('#__extensions')
			->where($query->qn('type').' = '.$db->quote('component'))
			->where($query->qn('element').' = '.$db->quote($element));
		$db->setQuery($query);

		return (bool) $db->loadResult();
	}
}
