<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Plugins
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;


// Include dependencies
jimport('joomla.application.component.controller');

$controller = JController::getInstance('Comments');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();