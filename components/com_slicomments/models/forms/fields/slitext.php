<?php
/**
 * @package		sliComments
 * @subpackage	Front end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class JFormFieldsliText extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'Text';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Field attributes.
		$attr = array(
			'type' => 'text',
			'name' => $this->name,
			'id' => $this->id,
			'value' => htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'),
			'placeholder' => $this->element['placeholder'] ? JText::_((string) $this->element['placeholder']) : '',
			'size' => $this->element['size'] ? (int) $this->element['size'] : '',
			'maxlength' => $this->element['maxlength'] ? (int) $this->element['size'] : '',
			'class' => $this->element['class'] ? (string) $this->element['class'] : '',
			'readonly' => (string) $this->element['readonly'] == 'true',
			'disabled' => (string) $this->element['disabled'] == 'true',
		);

		return '<input ' . $this->attributes($attr) . '/>';
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
