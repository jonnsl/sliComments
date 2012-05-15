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
