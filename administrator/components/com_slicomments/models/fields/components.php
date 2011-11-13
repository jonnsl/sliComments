<?php

defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');


class JFormFieldComponents extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Components';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		foreach ($this->element->children() as $option)
		{
			// Only add <option /> elements.
			if ($option->getName() != 'option') {
				continue;
			}

			$value = (string) $option['value'];

			// Create a new option object based on the <option /> element.
			$options[] = JHtml::_('select.option', (string) $option['value'], JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text', $value !== '' ? !$this->isEnabled($value) : false);
		}

		reset($options);

		return $options;
	}

	protected function isEnabled($option)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('enabled')
			->from('#__extensions')
			->where($query->qn('type').' = '.$db->quote('component'))
			->where($query->qn('element').' = '.$db->quote($option));
		$db->setQuery($query);

		return (bool) $db->loadResult();
	}
}
