<?php
/**
 * @package		sliComments
 * @subpackage	Front end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class sliView extends sliViewCompat
{

	/**
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
}
