<?php
/**
 * @package		sliComments
 * @subpackage	Front end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class JFormFieldsliTextarea extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'Textarea';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Field attributes.
		$attr = array(
			'name' => $this->name,
			'id' => $this->id,
			'placeholder' => $this->element['placeholder'] ? JText::_((string) $this->element['placeholder']) : '',
			'cols' => $this->element['cols'] ? (int) $this->element['cols'] : '',
			'rows' => $this->element['rows'] ? (int) $this->element['rows'] : '',
			'class' => $this->element['class'] ? (string) $this->element['class'] : '',
			'readonly' => (string) $this->element['readonly'] == 'true',
			'disabled' => (string) $this->element['disabled'] == 'true',
			'data-maxlength' => $this->element['maxlength'] ? (int) $this->element['maxlength'] : ''
		);

		return '<textarea ' . $this->attributes($attr) . '>' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '</textarea>';
	}

	private function attributes($array)
	{
		$s = '';

		foreach ($array as $key => $value)
		{
			if ($value === true)
			{
				$value = $key;
			}
			elseif (empty($value) || $value === false)
			{
				continue;
			}
			$s .= "$key=\"$value\" ";
		}

		return $s;
	}
}
