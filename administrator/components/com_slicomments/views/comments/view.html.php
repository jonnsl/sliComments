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
		$this->items		= $this->get('Items');
		//$this->flaggedBy	= $this->get('Flags');
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
	 * TODO: share this function with front-end
	 * Faster and lighter version of loadTemplate
	 *
	 * @param  string The name of the partial
	 * @param  array  An array of variables to be passed to the partial
	 *
	 * @return void
	 */
	public function partial($name)
	{
		try {
			$_partial = $this->getPartial($name);

			$vars = func_get_args();
			array_shift($vars);
			foreach ($vars as $var) {
				extract((array) $var);
			}

			include $_partial;
		}
		catch (Exception $e) {
			return JError::raiseError(500, $e->getMessage());
		}
	}

	/**
	 * Cache of partials filenames found
	 */
	protected $_partials = array();

	/**
	 * Finds the partial filename
	 *
	 * @return string
	 */
	protected function getPartial($name)
	{
		if (isset($this->_partials[$name])) return $this->_partials[$name];

		// Create the template file name based on the layout
		$layout = $this->getLayout();
		$file = $layout.'_'.$name.'.php';

		// Load the template script
		jimport('joomla.filesystem.path');
		$partial = JPath::find($this->_path['template'], $file);

		// If alternate layout can't be found, fall back to default layout
		if ($partial == false)
		{
			$partial = JPath::find($this->_path['template'], 'default_'.$name);
			if ($partial == false) {
				throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $file));
			}
		}

		return $this->_partials[$name] = $partial;
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_COMMENTS_COMMENTS'), 'comments');

		$status = $this->state->get('filter.status', array());

		if (in_array(0, $status)) {
			JToolBarHelper::custom('comments.approve', 'approve', null, 'COM_COMMENTS_ACTION_APPROVE');
		}
		if (in_array(-3, $status)) {
			JToolBarHelper::custom('comments.unflag', 'approve', null, 'COM_COMMENTS_ACTION_UNFLAG');
		}
		if (in_array(1, $status) || in_array(-3, $status)) {
			JToolBarHelper::custom('comments.unapprove', 'unapprove', null, 'COM_COMMENTS_ACTION_UNAPPROVE');
		}
		if (in_array(-1, $status)) {
			JToolBarHelper::custom('comments.approve', 'approve', null, 'COM_COMMENTS_ACTION_NOT_SPAM');
		}
		if (in_array(-2, $status)) {
			JToolBarHelper::custom('comments.approve', 'restore', null, 'COM_COMMENTS_ACTION_RESTORE');
		}

		if (in_array(0, $status) || in_array(1, $status) || in_array(-3, $status)) {
			JToolBarHelper::custom('comments.spam', 'spam', null, 'COM_COMMENTS_ACTION_SPAM');
			JToolBarHelper::custom('comments.trash', 'trash', null, 'JTOOLBAR_TRASH');
		} else {
			JToolBarHelper::custom('comments.delete', 'delete', null, 'JTOOLBAR_DELETE');
		}

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
}
