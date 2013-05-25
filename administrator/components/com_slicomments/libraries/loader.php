<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

function __sliAutoLoad($class)
{
	static $version;
	if ($version === null) $version = preg_replace('/\.[0-9]+$/', "", JVERSION);

	if (strpos($class, 'sliCommentsModel') === 0)
	{
		$model = strtolower(substr($class, 16));
		@include JPATH_BASE.'/components/com_slicomments/models/'.$model.'.php';
	}
	elseif (strpos($class, 'sliCommentsController') === 0)
	{
		$format = JRequest::getWord('format');
		if ($format === 'html') {
			$format = '';
		}
		$controller = strtolower(substr($class, 21));
		@include JPATH_BASE.'/components/com_slicomments/controllers/'.$controller.$format.'.php';
	}
	elseif (strpos($class, 'sliCommentsView') === 0)
	{
		$format = JRequest::getWord('format', 'html');
		$view = strtolower(substr($class, 15));
		@include JPATH_BASE.'/components/com_slicomments/views/'.$view.'/view.'.$format.'.php';
	}
	elseif (strpos($class, 'sli') === 0)
	{
		$class = strtolower(substr($class, 3));
		if (substr($class, -6) == 'compat'){
			$file = substr($class, 0, -6);
			@include __DIR__ . '/' . $version . '/'.$file.'.php';
		} else {
			$file = $class;
			@include __DIR__.'/'.$file.'.php';
		}
	}

}
spl_autoload_register('__sliAutoLoad');
