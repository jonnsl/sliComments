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
		$this->params		= $this->state->get('params');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->data			= $this->get('Data');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tpl);
	}

	/**
	 * Faster and lighter version of loadTemplate
	 *
	 * @param  string The name of the partial
	 * @param  array  An array of variables to be passed to the partial
	 *
	 * @return void
	 */
	public function partial($name, $vars)
	{
		try {
			$partial = $this->getPartial($name);
			extract((array)$vars);
	
			include $partial;
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

	public function linkToProfile($userid, $name)
	{
		static $option;
		if ($option == null) $option = $this->params->get('link', false);
		if ($option == false || $userid == 0) return $name != '' ? $this->escape($name) : JText::_('COM_COMMENTS_ANONYMOUS');

		switch ($option)
		{
			case 'com_kunena':
				JLoader::register('KunenaFactory', JPATH_ADMINISTRATOR.'/components/com_kunena/libraries/factory.php');
				$link = KunenaFactory::getProfile()->getProfileURL($userid);
				break;
			case 'com_community':
			default:
				$link = JRoute::_('index.php?option='.$option.'&view=profile&userid='.$userid);
		}

		return '<a href="'.$link.'">'.$this->escape($name).'</a>';
	}
}