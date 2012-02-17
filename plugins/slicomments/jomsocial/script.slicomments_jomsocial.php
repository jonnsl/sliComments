<?php
// No direct access
defined('_JEXEC') or die;

class plgsliCommentsJomsocialInstallerScript
{
	/**
	 * Register the user-point action in the database
	 */
	public function install($adapter)
	{
		$db = JFactory::getDbo();
		$data = new stdClass();

		$data->rule_name = 'Post comment';
		$data->rule_description = 'Give points when registered user post a new comment';
		$data->rule_plugin = 'com_slicomments';
		$data->action_string = 'slicomments.new';
		$data->component = '';
		$data->access = 1;
		$data->points = 1;
		$data->published = 1;
		$data->system = 0;

		$db->insertObject('#__community_userpoints', $data, 'id');
	}

	/**
	 * Activate itself
	 */
	public function postflight()
	{
		$db = JFactory::getDbo();
		$db->setQuery("UPDATE `#__extensions` SET enabled = 1 WHERE name = 'plg_slicomments_jomsocial'");
		$result = $db->query();
	}
}