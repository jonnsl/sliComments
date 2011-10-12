<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class sliCommentsControllerComments extends JController
{
	public function display()
	{
		if (JRequest::getCmd('option') != 'com_content' && JRequest::getCmd('view') != 'article') {
			JError::raiseError(403, 'Direct access to this component is unauthorized.');
		}
		return parent::display();
	}
	public function post()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$model = $this->getModel();
		$data = JRequest::get('post');
		$data = $model->filter($data);
		if (!$model->validate($data)) {
			$this->setMessage($model->getError(), 'error');
		} elseif (!$model->save($data)) {
			$this->setMessage($model->getError(), 'error');
		}

		$this->setRedirect(base64_decode($data['return']));
	}

	public function delete()
	{
		// Check for request forgeries.
		JRequest::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$return = JRequest::getVar('return', null, 'get');
		if (!JFactory::getUser()->authorise('core.admin')) {
			$this->setMessage('Unauthorized.', 'error');
		} else {
			$model = $this->getModel();
			$id = JRequest::getInt('id', null, 'get');
			if ($id) {
				if (!$model->delete($id)) {
					$this->setMessage($model->getError(), 'error');
				}
			} else {
				$this->setMessage(JText::_('COM_COMMENTS_ERROR_INVALID_ID'), 'error');
			}
		}

		$this->setRedirect(base64_decode($return));
	}

	public function getModel()
	{
		static $model;
		if ($model == null)
		{
			$model = JModel::getInstance('Comments', 'sliCommentsModel');
			// Task is a reserved state
			$model->setState('task', $this->task);

			// Let's get the application object and set menu information if it's available
			$app	= JFactory::getApplication();
			$menu	= $app->getMenu();

			if (is_object($menu)) {
				if ($item = $menu->getActive()) {
					$params	= $menu->getParams($item->id);
					// Set default state data
					$model->setState('parameters.menu', $params);
				}
			}
		}
		return $model;
	}
}