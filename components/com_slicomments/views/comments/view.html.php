<?php
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
		$this->total		= $this->get('Total');
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tpl);
	}
}