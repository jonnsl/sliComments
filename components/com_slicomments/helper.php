<?php
/**
 * @package		sliComments
 * @subpackage	Front end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_slicomments/libraries/loader.php';

class sliComments
{
	public static function commentify($config)
	{
		extract($config);
		// Load language files
		JFactory::getLanguage()->load('com_slicomments', JPATH_BASE, null, false, false);

		$model = self::getModel('comments');
		$model->setState('extension', isset($extension) ? $extension : JRequest::getCmd('option'));
		$model->setState('item.id', $item->id);
		$extension = $model->extension;
		$extension->item = $item;

		if ($extension->isEnabled())
		{
			ob_start();
			$controller = new sliCommentsControllerComments();
			$controller->display();
			return ob_get_clean();
		}
	}

	public static function getTotal($config)
	{
		extract($config);
		$model = self::getModel('comments');
		$model->setState('extension', isset($extension) ? $extension : JRequest::getCmd('option'));
		$model->setState('item.id', $item->id);
		$extension = $model->extension;
		$extension->item = $item;

		if ($extension->isEnabled()) {
			return $model->getTotal();
		}
		return false;
	}

	public static function getModel($name = 'Comments', $prefix = 'sliCommentsModel', $config = array())
	{
		static $models = array();
		if (is_array($prefix)) {
			$config = $prefix;
			$prefix = 'sliCommentsModel';
		}
		$className  = $prefix . ucfirst($name);
		if (!isset($models[$className]))
		{
			$models[$className] = new $className($config);
		}
		return $models[$className];
	}
}


