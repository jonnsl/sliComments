<?php

// No direct access
defined('_JEXEC') or die;

function __sliAutoLoad($class)
{
	static $version;
	if ($version === null) {
		$version = (new JVersion)->isCompatible('3.0') ? '3.0' : '2.5';
	}

	switch ($class)
	{
		case 'sliController':
			return include dirname(__FILE__) . '/' . $version . '/controller.php';
		case 'sliView':
			return include dirname(__FILE__) . '/' . $version . '/view.php';
		case 'sliModel':
			return include dirname(__FILE__) . '/' . $version . '/model.php';
	}

	return false;
}
spl_autoload_register('__sliAutoLoad');
