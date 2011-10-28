<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class sliCommentsControllerComments extends JController
{
	public function post()
	{
		// Check for request forgeries.
		if (!JRequest::checkToken()) {
			$return['error'] = JText::_('JINVALID_TOKEN');
			$return['success'] = false;
			echo json_encode($return);
			return;
		}

		// Check for authorisation.
		$user = JFactory::getUser();
		if (!$user->authorise('post', 'com_slicomments')) {
			$return['error'] = JText::_('COM_COMMENTS_NO_AUTH');
			$return['success'] = false;
			echo json_encode($return);
			return;
		}

		$model = $this->getModel('comments');
		$data = JRequest::get('post');
		$data['status'] = $user->authorise('auto_publish', 'com_slicomments') ? 1 : 0;
		$data = $model->filter($data);
		if (!$model->validate($data)){
			$return['error'] = $model->getError();
			$return['success'] = false;
		}
		elseif(!$model->save($data)) {
			$return['error'] = $model->getError();
			$return['success'] = false;
		} else {
			$return['success'] = true;
			$user = JFactory::getUser();
			if (!$user->guest) {
				$data['name'] = $user->name;
				$data['email'] = $user->email;
			}
			$data['email'] = md5($data['email']);
			$return['data'] = $data;
		}
		echo json_encode($return);
		return;
	}

	public function delete()
	{
		try {
			// Check for request forgeries.
			if (!JRequest::checkToken('get')) {
				throw new Exception(JText::_('JINVALID_TOKEN'));
			}
			$user = JFactory::getUser();
			$model = $this->getModel('comments');
			$table = $model->getTable();
			$id = JRequest::getInt('id', null, 'get');

			if (!$id) {
				throw new Exception(JText::_('COM_COMMENTS_ERROR_INVALID_ID'));
			}

			if (!$table->load($id)) {
				throw new Exception(JText::_('COM_COMMENTS_ERROR_COMMENT_DONT_EXIST'));
			}

			if (!(
				$user->authorise('delete', 'com_slicomments') || 
				(!$user->guest && $user->authorise('delete.own', 'com_slicomments') && $table->user_id == $user->id)
			)) {
				throw new Exception(JText::_('COM_COMMENTS_NO_AUTH'));
			}

			if (!$model->delete($id)) {
				throw new Exception($model->getError()->getMessage());
			}

			$return['success'] = true;
		} catch (Exception $e) {
			$return['success'] = false;
			$return['error'] = $e->getMessage();
		}

		echo json_encode($return);
	}

	public function vote()
	{
		try {
			if (!JFactory::getUser()->authorise('vote', 'com_slicomments')){
				throw new JException(JText::_('COM_COMMENTS_NO_AUTH'));
			}
			$model = $this->getModel('comments');
			$vote = JRequest::getInt('v');
			$comment_id = JRequest::getInt('id');
			if (!$model->vote($comment_id, $vote)) {
				throw new JException($model->getError());
			}
			$return['success'] = true;
			$return['delta'] = $vote;
		} catch (JException $e) {
			$return['success'] = false;
			$return['error'] = $e->getMessage();
		}

		echo json_encode($return);
	}
}