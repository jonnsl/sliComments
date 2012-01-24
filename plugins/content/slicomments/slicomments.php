<?php
/**
 * @package		sliComments
 * @subpackage	Content Plugin
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * sliComments Content Plugin
 *
 * @package		sliComments
 * @subpackage	Content Plugin
 * @since		1.0
 */
class plgContentSlicomments extends JPlugin
{
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
		$this->loadLanguage('plg_content_slicomments.sys');
	}

	public function onContentAfterDisplay($context, &$row, &$params, $page = 0)
	{
		if (strtolower(JRequest::getWord('format', 'html')) !== 'html') return;
		
		$view = strtolower(JRequest::getCmd('view'));
		if ($this->params->get('comments_count', false) && ($view == 'featured' || $view == 'category'))
		{
			$attribs = new JRegistry($row->attribs);
			$model = $this->getModel();
			$model->setState('article.id', $row->id);
			$total = $model->getTotal();
			if (($total > 0 || $attribs->get('slicomments.enabled', true)) && $model->isCategoryEnabled($row->catid)) {
				return '<a href="'.JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid)).'#comments">'.JText::sprintf('PLG_CONTENT_SLICOMMENTS_COMMENTS_COUNT', $total).'</a>';
			}
		}
		elseif ($view == 'article')
		{
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
			$model->setState('article.params', $row->params);
			ob_start();
			$controller->execute('display');
			JRequest::setVar('view', $old_view);
			JRequest::setVar('task', $old_task);
			return ob_get_clean();
		}
	}

	public function onContentAfterDelete($context, $table)
	{
		if ($context !== 'com_content.article'){
			return;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->delete()
			->from('#__slicomments')
			->where('article_id = '.(int)$table->id);
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseWarning(500, 'Error deleting comments from article "'.$table->id.'-'.$table->title.'". '.$db->getErrorMsg());
		}
	}

	public function onContentPrepareForm($form, $data)
	{
		if ($form->getName() != 'com_content.article') return;

		// Load the custom form
		$this->loadLanguage();
		$form->loadFile(dirname(__FILE__).'/article.xml');
	}

	protected function getModel()
	{
		static $model;
		if ($model === null)
		{
			JModel::addIncludePath(JPATH_SITE.'/components/com_slicomments/models', 'sliCommentsModel');
			$model = JModel::getInstance('Comments', 'sliCommentsModel', array('ignore_request' => true));
		}
		return $model;
	}
}
