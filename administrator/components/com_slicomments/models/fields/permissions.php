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
		foreach ($groups as $group)
		{
			$left  .= '<li class="group"  data-id="'.$group->id.'">'.str_repeat('|—', $group->level).$group->title.'</li>';
			$right .= '<li class="action" data-id="'.$group->id.'">';
			
			foreach ($access as $label => $actions)
			{
				$right .= '<fieldset class="permissions-fieldset">';
				$right .= '<legend>'.$label.'</legend>';
				foreach ($actions as $action)
				{
					// Get the actual setting for the action for this group.
					$assetRule = $assetRules->allow($action->name, $group->id);

					$right .= '<select name="'.$this->name.'['.$action->name.']['.$group->id.']" id="'.$this->id.'_'.$action->name.'_'.$group->id.'" title="'.JText::sprintf('JLIB_RULES_SELECT_ALLOW_DENY_GROUP', JText::_($action->title), trim($group->title)).'" class="'.($assetRule === true ? 'allowed': '').($assetRule === false ? 'denied': '').'">';

					// Build the dropdowns

					// The parent group has "Not Set", all children can rightly "Inherit" from that.
					if (!empty($group->parent_id)) {
						$right .= '<option value=""'.($assetRule === null ? ' selected="selected"' : '').'>'.JText::_('JLIB_RULES_INHERITED').'</option>';
					}
					$right .= '<option class="allowed" value="1"'.($assetRule === true ? ' selected="selected"' : '').'>'.JText::_('JLIB_RULES_ALLOWED').'</option>';
					$right .= '<option class="denied" value="0"'.($assetRule === false ? ' selected="selected"' : '').'>'.JText::_('JLIB_RULES_DENIED').'</option>';

					$right .= '</select>&#160; ';
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
					'description' => (string) $action['description']
				);
			}
		}

		return $return;
	}
}