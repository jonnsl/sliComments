<?php
/**
 * @version		$Id: joomla.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Example Content Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Content.joomla
 * @since		1.6
 */
class plgContentComments extends JPlugin
{
	public function onContentAfterDisplay()
	{
		if (strtolower(JRequest::getWord('format', 'html')) !== 'html' ||
		strtolower(JRequest::getCmd('view')) != 'article') return;

		// Include dependencies
		jimport('application.component.controller');
		
		$old_view = JRequest::getCmd('view');
		$old_task = JRequest::getVar('task');
		JRequest::setVar('view', 'comments');
		JRequest::setVar('task', 'comments.display');
		ob_start();
		$config = array('base_path'=> JPATH_SITE.'/components/com_comments');
		require_once $config['base_path'].'/controllers/comments.php';
		$controller = new CommentsControllerComments($config);
		$controller->execute('display');
		JRequest::setVar('view', $old_view);
		JRequest::setVar('task', $old_task);
		return ob_get_clean();
	}
}