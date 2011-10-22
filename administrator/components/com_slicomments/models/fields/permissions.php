<?php
defined('_JEXEC') or die;

jimport('joomla.form.formfield');

class JFormFieldPermissions extends JFormField
{
	protected $type = 'Permissions';

	protected function getInput()
	{
		$left = '';
		$right = '';
		JHtml::_('stylesheet', 'slicomments/permissions.css', array(), true);
		JHtml::_('script', 'slicomments/permissions.js', true, true);
		$groups = $this->getUserGroups();
		$form = new JFormHook('com_slicomments.permissions');
		$form->loadFile(JPATH_ADMINISTRATOR.'/components/com_slicomments/actions.xml');
		$fieldSets = $form->getFieldsets();
		foreach ($groups as $group)
		{
			$left .= '<li class="group" data-id="'.$group->id.'">'.str_repeat('|—', $group->level).$group->title.'</li>';
			$right .= '<li class="action" data-id="'.$group->id.'">';
			$form->setFormControl('permissions['.$group->id.']');
			
			foreach ($fieldSets as $name => $fieldSet)
			{
				$right .= '<fieldset class="permissions-fieldset">';
				$right .= '<legend>'.$fieldSet->label.'</legend>';
				foreach ($form->getFieldset($name) as $field)
				{
					if ($field->type == 'Checkbox') {
						$right .= $field->getInput();
						$right .= $field->getLabel();
					} else {
						$right .= $field->getLabel();
						$right .= $field->getInput();
					}
				}
				$right .= '</fieldset>';
			}

			$right .= '</li>';
		}
		$html = '<ul id="groups">';
		$html .= $left;
		$html .= '</ul>';
		$html .= '<ul id="actions">';
		$html .= $right;
		$html .= '</ul>';
		$html .= '</ul>';
		return $html;
	}

	protected function getLabel()
	{
		return '';
	}

	protected function getUserGroups()
	{
		// Initialise variables.
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('a.id, a.title, COUNT(DISTINCT b.id) AS level')
			->from('#__usergroups AS a')
			->leftJoin($query->qn('#__usergroups').' AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			->group('a.id')
			->order('a.lft ASC');

		$db->setQuery($query);
		$options = $db->loadObjectList();

		return $options;
	}
}

class JFormHook extends JForm
{
	public function setFormControl($value)
	{
		return $this->options['control'] = (string) $value;
	}
}