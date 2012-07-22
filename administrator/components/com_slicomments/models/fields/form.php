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


class JFormFieldForm extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Form';

	protected function getInput()
	{
		$html = array();
		$html[] = JHtml::_('tabs.start', 'config-tabs-com_slicomments_configuration_extension', array('useCookie'=>1));
		$extensions = JFolder::folders(JPATH_SITE . '/components/com_slicomments/plugins');
		foreach ($extensions as $extension)
		{
			if (!self::isEnabled($extension)) {
				continue;
			}
			$form = $this->getForm($extension);
			if (isset($this->value[$extension])) {
				$form->bind($this->value[$extension]);
			}
			$html[] = JHtml::_('tabs.panel', JText::_('COM_COMMENTS_'.strtoupper($extension)), 'publishing-details');
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
