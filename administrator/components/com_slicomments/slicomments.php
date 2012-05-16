<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_slicomments')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependencies
require_once __DIR__ . '/libraries/loader.php';

$controller = sliController::getInstance('sliComments');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
