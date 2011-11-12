<?php
// No direct access
defined('_JEXEC') or die;

require_once __DIR__.'/../comments/view.html.php';

class sliCommentsViewComment extends sliCommentsViewComments
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		$app = JFactory::getApplication();
		$this->_path['template'] = array();
		$this->_addPath('template', $this->_basePath.'/views/comments/tmpl');
		$this->_addPath('template', JPATH_THEMES.'/'.$app->getTemplate().'/html/com_slicomments/comments');
	}

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->params = $this->get('params');
		require_once JPATH_ADMINISTRATOR.'/components/com_slicomments/helpers/comments.php';

		parent::partial('comment', $this->data);
	}
}
