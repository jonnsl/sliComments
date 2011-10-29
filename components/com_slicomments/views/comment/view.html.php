<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class sliCommentsViewComment extends JView
{
	/**
	 * Display the view
	 */
	public function display($data)
	{
		$this->data		= (object) $data;
		$this->state	= $this->get('State');
		$this->params	= $this->state->get('component.params');

		return $this->loadTemplate();
	}
}