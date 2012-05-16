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

	switch ($class)
	{
		case 'sliController':
			return include __DIR__ . '/' . $version . '/controller.php';
		case 'sliViewCompat':
			return include __DIR__ . '/' . $version . '/view.php';
		case 'sliView':
			return include __DIR__ . '/component/view.php';
		case 'sliModel':
			return include __DIR__ . '/component/model.php';
	}

	return false;
}
spl_autoload_register('__sliAutoLoad');
