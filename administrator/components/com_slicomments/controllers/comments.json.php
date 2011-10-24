<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class sliCommentsControllerComments extends JController
{
	public function edit()
	{
		// Check for request forgeries.
		if (!JRequest::checkToken()) {
			$return['error'] = JText::_('JINVALID_TOKEN');
			$return['success'] = false;
			echo json_encode($return);
			return;
		}

		// Check for authorisation.
		if (!JFactory::getUser()->authorise('edit', 'com_slicomments')) {
			$return['error'] = JText::_('COM_COMMENTS_NO_AUTH');
			$return['success'] = false;
			echo json_encode($return);
			return;
		}

		$model = $this->getModel('comments');
		$data = JRequest::get('post');
		$id = (int) $data['id'];
		$data = $model->filter($data);
		if (!$model->validate($data)){
			$return['error'] = $model->getError();
			$return['success'] = false;
		}
		elseif(!$model->save($id, $data)) {
			$return['error'] = $model->getError();
			$return['success'] = false;
		} else {
			$return['success'] = true;
			$return['data'] = $data['text'];
		}
		echo json_encode($return);
	}
}