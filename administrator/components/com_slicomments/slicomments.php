<?php
// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('manage', 'com_slicomments')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependencies
jimport('joomla.application.component.controller');

$controller = JController::getInstance('sliComments');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();