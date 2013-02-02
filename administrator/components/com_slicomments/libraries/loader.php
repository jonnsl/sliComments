<?php

// No direct access
defined('_JEXEC') or die;

function __sliAutoLoad($class)
{
	static $version;
	if ($version === null) $version = preg_replace('/\.[0-9]+$/', "", JVERSION);

	switch ($class)
	{
		case 'sliController':
			return include __DIR__ . '/' . $version . '/controller.php';
		case 'sliView':
			return include __DIR__ . '/' . $version . '/view.php';
		case 'sliModel':
			return include __DIR__ . '/' . $version . '/model.php';
	}

	return false;
}
spl_autoload_register('__sliAutoLoad');
