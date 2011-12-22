<?php
defined('_JEXEC') or die;

jimport('joomla.form.formfield');

class JFormFieldPermissions extends JFormField
{
	protected $type = 'Permissions';

	protected function getInput()
	{
		$user = JFactory::getUser();
		$left = '';
		$right = '';
		JHtml::_('stylesheet', 'slicomments/permissions.css', array(), true);
		JHtml::_('script', 'slicomments/permissions.js', true, true);
		$groups = $this->getUserGroups();
		$assetId = $this->getAssetId();
		$assetRules = JAccess::getAssetRules($assetId);
		$access = $this->getAccess();
		$guest_usergroup = JComponentHelper::getParams('com_users')->get('guest_usergroup', 1);
		foreach ($groups as $group)
		{
			$left  .= '<li class="group"  data-id="'.$group->id.'" data-parent-id="'.$group->parent_id.'">'.str_repeat('|—', $group->level).$group->title.'</li>';
			$right .= '<li class="action" data-id="'.$group->id.'">';

			foreach ($access as $label => $actions)
			{
				$right .= '<fieldset class="permissions-fieldset">';
				$right .= '<legend>'.JText::_('COM_COMMENTS_'.$label.'_FIELDSET_LABEL').'</legend>';
				foreach ($actions as $action)
				{
					if ($action->guest == false && $group->id == $guest_usergroup) {
						$disabled = true;
					} else {
						$disabled = false;
					}
					// Get the actual setting for the action for this group.
					$assetRule = $assetRules->allow($action->name, $group->id);
					// Get the inherited setting for the action for this group.
					$inheritedRule	= JAccess::checkGroup($group->id, $action->name, $assetId);

					if ($inheritedRule === true) {
						$inheritedRuleClass = "allowed";
					} else if ($inheritedRule === false || $inheritedRule === null) {
						$inheritedRuleClass = "denied";
					}

					if ($assetRule === true) {
						$class = "allowed";
					} else if ($assetRule === false){
						$class = "denied";
					}
					elseif ($assetRule === null)
					{
						$class = empty($inheritedRuleClass) ? '' : $inheritedRuleClass;
					}

					$right .= '<select name="'.$this->name.'['.$action->name.']['.$group->id.']" id="'.$this->id.'_'.$action->name.'_'.$group->id.'" title="'.JText::sprintf('JLIB_RULES_SELECT_ALLOW_DENY_GROUP', JText::_($action->title), trim($group->title)).'" class="'.$class.'" data-name="'.$action->name.'" '.($disabled ? 'disabled="disabled"': '').'>';

					// Build the dropdowns

					// The parent group has "Not Set", all children can rightly "Inherit" from that.
					$right .= '<option class="'.$inheritedRuleClass.'" value=""'.($assetRule === null ? ' selected="selected"' : '').'>'.JText::_('JLIB_RULES_INHERITED').'</option>';
					$right .= '<option class="allowed" value="1"'.($assetRule === true ? ' selected="selected"' : '').'>'.JText::_('JLIB_RULES_ALLOWED').'</option>';
					$right .= '<option class="denied" value="0"'.($assetRule === false || $disabled ? ' selected="selected"' : '').'>'.JText::_('JLIB_RULES_DENIED').'</option>';

					$right .= '</select>';
					$right .= '<label class="hasTip" for="'.$this->id.'_'.$action->name.'_'.$group->id.'" title="'.htmlspecialchars(JText::_($action->title).'::'.JText::_($action->description), ENT_COMPAT, 'UTF-8').'">';
					$right .= JText::_($action->title);
					$right .= '</label>';
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
		$query->select('a.id, a.title, COUNT(DISTINCT b.id) AS level, a.parent_id')
			->from('#__usergroups AS a')
			->leftJoin($query->qn('#__usergroups').' AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			->group('a.id')
			->order('a.lft ASC');

		$db->setQuery($query);
		$options = $db->loadObjectList();

		return $options;
	}

	protected function getAssetId()
	{
		// Need to find the asset id by the name of the component.
		$db = JFactory::getDbo();
		$db->setQuery('SELECT id FROM #__assets WHERE name = "com_slicomments"');
		$assetId = (int) $db->loadResult();

		if ($error = $db->getErrorMsg()) {
			JError::raiseError(500, $error);
		}
		return $assetId;
	}

	protected function getAccess()
	{
		$xml = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_slicomments/access.xml');

		foreach ($xml->children() as $fieldset)
		{
			$label = (string)$fieldset['label'];
			foreach ($fieldset->children() as $action)
			{
				$return[$label][] = (object) array(
					'name' => (string) $action['name'],
					'title' => (string) $action['title'],
					'description' => (string) $action['description'],
					'guest' => (((string) $action['guest']) == 'false' ? false : true)
				);
			}
		}

		return $return;
	}
}