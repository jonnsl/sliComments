<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class sliCommentsViewComments extends JView
{
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->flaggedBy	= $this->get('Flags');
		$this->pagination	= $this->get('Pagination');

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
		JToolBarHelper::title(JText::_('COM_COMMENTS_COMMENTS'), 'comments');

		$status = $this->state->get('filter.status');
		if ($status == '') $status = '*';
		if ($status == 0 || $status == '*') {
			JToolBarHelper::custom('comments.approve', 'approve', null, 'COM_COMMENTS_ACTION_APPROVE');
		}
		if ($status == 1 || $status == '*') {
			JToolBarHelper::custom('comments.unapprove', 'unapprove', null, 'COM_COMMENTS_ACTION_UNAPPROVE');
		}
		if ($status == -1) {
			JToolBarHelper::custom('comments.approve', 'approve', null, 'COM_COMMENTS_ACTION_NOT_SPAM');
		}
		if ($status == -2) {
			JToolBarHelper::custom('comments.approve', 'restore', null, 'COM_COMMENTS_ACTION_RESTORE');
		}

		if ($status != -2 || $status == '*') {
			//JToolBarHelper::custom('comment.edit', 'edit', null, 'COM_COMMENTS_ACTION_EDIT');
		}

		if ($status >= 0 || $status == '*') {
			JToolBarHelper::custom('comments.spam', 'spam', null, 'COM_COMMENTS_ACTION_SPAM');
			JToolBarHelper::custom('comments.trash', 'trash', null, 'JTOOLBAR_TRASH');
		} else {
			JToolBarHelper::custom('comments.delete', 'delete', null, 'JTOOLBAR_DELETE');
		}

		if (JFactory::getUser()->authorise('core.admin')) JToolBarHelper::preferences('com_slicomments');
	}
}
