<?php
/**
 * @package		sliComments
 * @subpackage	Front end Component
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
		$this->total		= $this->get('Total');
		$this->state		= $this->get('State');
		$this->params		= $this->state->get('params');
		$this->topComments	= $this->get('topComments');
		$this->comments		= $this->get('Comments');
		$this->pagination	= $this->get('Pagination');
		$this->data			= $this->get('Data');
		$this->avatar		= $this->get('Avatar');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tpl);
	}
}
