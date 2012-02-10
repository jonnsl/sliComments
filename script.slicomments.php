<?php

// No direct access
defined('_JEXEC') or die;

class Com_sliCommentsInstallerScript
{
	public function __construct($installer)
	{
		$this->installer = $installer;
	}

	public function install($adapter)
	{
		$src = dirname(__FILE__);
		$status = $this->installPlugins();
		$db = JFactory::getDbo();

		// Activated the plugins
		$db->setQuery("UPDATE `#__extensions` SET enabled = 1 WHERE name = 'plg_content_slicomments'");
		$result = $db->query();
?>
		<table class="adminlist">
			<thead>
				<tr>
					<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
					<th width="30%"><?php echo JText::_('Status'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3"></td>
				</tr>
			</tfoot>
			<tbody>
				<tr class="row0">
					<td class="key" colspan="2">sliComments</td>
					<td><strong><?php echo JText::_('Installed'); ?></strong></td>
				</tr>
				<?php if (count($status->plugins)) : ?>
					<tr>
						<th><?php echo JText::_('Plugin'); ?></th>
						<th><?php echo JText::_('Group'); ?></th>
						<th></th>
					</tr>
					<?php foreach ($status->plugins as $plugin) : ?>
					<tr class="row<?php echo (++ $rows % 2); ?>">
						<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
						<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
						<td><strong><?php echo ($plugin['result'])?JText::_('Installed'):'<span style="color:red;">'.JText::_('Not installed').'</span>'; ?></strong></td>
					</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
<?php
		return true;
	}

	public function update($adapter)
	{
		// Make sure that the plugins are updated as well
		$this->installPlugins();
		return true;
	}

	protected function installPlugins()
	{
		$src = dirname(__FILE__);
		$status = new stdClass;

		// Content - sliComments
		$installer = new JInstaller;
		$result = $installer->install($src.'/plugins/content/slicomments');
		$status->plugins[] = array('name' => 'Content - sliComments','group' => 'content', 'result' => $result);

		// sliComments - Akismet
		$installer = new JInstaller;
		$result = $installer->install($src.'/plugins/slicomments/akismet');
		$status->plugins[] = array('name' => 'sliComments - Akismet','group' => 'slicomments', 'result' => $result);

		return $status;
	}

	public function postflight($type, $adapter)
	{
		if ($type !== 'install') return true;

		// Store the default rules in the database
		$db = JFactory::getDbo();
		$defaultRules = array(
			'post' => array('1' => 1, '2' => 1, '8' => 1),
			'auto_publish' => array('6' => 1, '2' => 1, '8' => 1),
			'vote' => array('6' => 1, '2' => 1, '8' => 1),
			'flag' => array('6' => 1, '2' => 1, '8' => 1),
			'edit.own' => array('6' => 1, '8' => 1),
			'delete.own' => array('6' => 1, '8' => 1),
			'edit' => array('4' => 1, '8' => 1),
			'delete' => array('8' => 1),
			'manage' => array('8' => 1)
		);
		jimport('joomla.access.rules');
		$rules	= new JRules($defaultRules);
		$asset	= JTable::getInstance('asset');

		if (!$asset->loadByName('com_slicomments')) {
			$root	= JTable::getInstance('asset');
			$root->loadByName('root.1');
			$asset->name = 'com_slicomments';
			$asset->title = 'com_slicomments';
			$asset->setLocation($root->id, 'last-child');
		}
		$asset->rules = (string) $rules;
		$asset->check();
		$asset->store();
	}
}