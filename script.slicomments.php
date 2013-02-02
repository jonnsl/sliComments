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
		<table class="adminlist table-striped">
			<thead>
				<tr>
					<th class="title"><?php echo JText::_('Extension'); ?></th>
					<th width="30%"><?php echo JText::_('Status'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="row0">
					<td class="key">sliComments</td>
					<td><strong><?php echo JText::_('Installed'); ?></strong></td>
				</tr>
			</tbody>
		</table>
		<?php if (count($status->plugins)) : ?>
		<table class="adminlist table-striped">
			<thead>
				<tr>
					<th><?php echo JText::_('Plugin'); ?></th>
					<th><?php echo JText::_('Group'); ?></th>
					<th><?php echo JText::_('Status'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($status->plugins as $plugin) : ?>
				<tr class="row<?php echo (++ $rows % 2); ?>">
					<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
					<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
					<td><strong><?php echo ($plugin['result'])?JText::_('Installed'):'<span style="color:red;">'.JText::_('Not installed').'</span>'; ?></strong></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
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

		// sliComments - Jomsocial
		$result = false;
		if ($this->componentIsEnabled('com_community'))
		{
			$installer = new JInstaller;
			$result = $installer->install($src.'/plugins/slicomments/jomsocial');
		}
		$status->plugins[] = array('name' => 'sliComments - Jomsocial','group' => 'slicomments', 'result' => $result);

		return $status;
	}

	public function preflight($type, $adapter)
	{
		if ($type !== 'update') return true;

		// "Fix" Joomla! bug
		$row = JTable::getInstance('extension');
		$eid = $row->find(array('element' => strtolower($adapter->get('element')), 'type' => 'component'));

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('version_id')
			->from('#__schemas')
			->where('extension_id = ' . $eid);
		$db->setQuery($query);

		if ($db->loadResult()) return true;

		// Get the previous version
		$old_manifest = null;
		// Create a new installer because findManifest sets stuff
		// Look in the administrator first
		$tmpInstaller = new JInstaller;
		$tmpInstaller->setPath('source', JPATH_ADMINISTRATOR . '/components/com_slicomments');

		if (!$tmpInstaller->findManifest())
		{
			echo 'Could not find old manifest.';
			return false;
		}

		$old_manifest = $tmpInstaller->getManifest();
		$version = (string) $old_manifest->version;

		// Store
		$data = new stdClass;
		$data->extension_id = $eid;
		$data->version_id = $version;
		$db->insertObject('#__schemas', $data);

		return true;
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
		$rules	= new JAccessRules($defaultRules);
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

	protected function componentIsEnabled($option)
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