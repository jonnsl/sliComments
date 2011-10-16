<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * sliComments Content Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Content.joomla
 * @since		1.6
 */
class plgContentSlicomments extends JPlugin
{
	public function onContentAfterDisplay($context, &$row, &$params, $page = 0)
	{
		if (strtolower(JRequest::getWord('format', 'html')) !== 'html' ||
		strtolower(JRequest::getCmd('view')) != 'article') return;

		// Include dependencies
		jimport('application.component.controller');
		
		$old_view = JRequest::getCmd('view');
		$old_task = JRequest::getVar('task');
		JRequest::setVar('view', 'comments');
		JRequest::setVar('task', 'comments.display');
		$config = array('base_path'=> JPATH_SITE.'/components/com_slicomments');
		require_once $config['base_path'].'/controllers/comments.php';
		JFactory::getLanguage()->load('com_slicomments', JPATH_BASE, null, false, false);
		$controller = new sliCommentsControllerComments($config);
		$model = $controller->getModel('comments');
		if (!$model->isCategoryEnabled($row->catid)) {
			return;
		}
		$model->setState('article.id', $row->id);
		$model->setState('article.slug', $row->slug);
		$model->setState('article.catid', $row->catid);
		ob_start();
		$controller->execute('display');
		JRequest::setVar('view', $old_view);
		JRequest::setVar('task', $old_task);
		return ob_get_clean();
	}
}