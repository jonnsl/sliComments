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
		$this->params	= $this->get('params');

		return $this->loadTemplate();
	}
}