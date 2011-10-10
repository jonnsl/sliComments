<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CommentsViewComments extends JView
{
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
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
		JToolBarHelper::deleteList('', 'comments.delete', 'JTOOLBAR_DELETE');
	}
}