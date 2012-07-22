<?php
/**
 * @package		sliComments
 * @subpackage	Front end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Include dependencies
require JPATH_COMPONENT . '/helper.php';

$controller = sliController::getInstance('sliComments');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();