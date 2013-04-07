<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class sliCommentsViewComments extends sliView
{
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Comments');
		//$this->flaggedBy	= $this->get('Flags');
		$this->pagination	= $this->get('Pagination');
		$this->params		= $this->getModel()->params;
		$this->blocked_ips	= array_filter(array_map('trim', explode(',', $this->params->get('blocked_ips'))));

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		if ($extension = $this->state->get('extension')) {
			$title = JText::sprintf('COM_COMMENTS_COMMENTS', JText::_('COM_COMMENTS_'.$extension));
		} else {
			$title = JText::_('COM_COMMENTS');
		}
		JToolBarHelper::title($title, 'comments');

		require_once __DIR__ . '/../../helpers/button_html5.php';

		self::toolbar_add_button('comments.approve', 'approve', 'COM_COMMENTS_ACTION_APPROVE');
		self::toolbar_add_button('comments.unapprove', 'unapprove', 'COM_COMMENTS_ACTION_UNAPPROVE');
		self::toolbar_add_button('comments.spam', 'spam', 'COM_COMMENTS_ACTION_SPAM');
		self::toolbar_add_button('comments.trash', 'trash', 'JTOOLBAR_TRASH');
		self::toolbar_add_button('comments.delete', 'delete', 'JTOOLBAR_DELETE');

		if (JFactory::getUser()->authorise('core.admin')) JToolBarHelper::preferences('com_slicomments');
	}

	protected function getSortFields()
	{
		return array(
			'a.name' => JText::_('COM_COMMENTS_HEADING_AUTHOR'),
			'a.article_id' => JText::_('COM_COMMENTS_HEADING_ARTICLE'),
			'a.created' => JText::_('COM_COMMENTS_HEADING_SUBMITTED')
		);
	}

	protected static function toolbar_add_button($task, $icon, $alt)
	{
		JToolBar::getInstance('toolbar')
			->appendButton('html5', $icon, $alt, $task);
	}
}
