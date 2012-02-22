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

		// Post comment
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

		// Comment liked
		$data = new stdClass();
		$data->rule_name = 'Comment liked';
		$data->rule_description = 'Give points when an user\'s comment receive a like.';
		$data->rule_plugin = 'com_slicomments';
		$data->action_string = 'slicomments.like';
		$data->component = '';
		$data->access = 1;
		$data->points = 1;
		$data->published = 1;
		$data->system = 0;

		$db->insertObject('#__community_userpoints', $data, 'id');

		// Comment disliked
		$data = new stdClass();
		$data->rule_name = 'Comment disliked';
		$data->rule_description = 'Deduct points when an user\'s comment receive a dislike.';
		$data->rule_plugin = 'com_slicomments';
		$data->action_string = 'slicomments.dislike';
		$data->component = '';
		$data->access = 1;
		$data->points = -1;
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