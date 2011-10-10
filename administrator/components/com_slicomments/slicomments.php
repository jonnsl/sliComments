<?php
// No direct access
defined('_JEXEC') or die;


// Include dependencies
jimport('joomla.application.component.controller');

$controller = JController::getInstance('sliComments');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();