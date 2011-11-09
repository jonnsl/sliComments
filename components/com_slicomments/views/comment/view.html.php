<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class sliCommentsViewComment extends JView
{
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->params = $this->get('params');

		parent::display($tpl);
	}
}