<?php
/**
 * @package		sliComments
 * @subpackage	Front end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class sliCommentsControllerComments extends JController
{
	public function post()
	{
		try {
			// Check for request forgeries.
			if (!JRequest::checkToken()) {
				throw new Exception(JText::_('JINVALID_TOKEN'), 500);
			}

			// Check for authorisation.
			$user = JFactory::getUser();
			if (!$user->authorise('post', 'com_slicomments')) {
				throw new Exception(JText::_('COM_COMMENTS_NO_AUTH'), 403);
			}

			// Initialise variables
			$model = $this->getModel();
			$user = JFactory::getUser();
			$session = JFactory::getSession();
			$data = JRequest::get('post', JREQUEST_ALLOWRAW);
			$data['status'] = $user->authorise('auto_publish', 'com_slicomments') ? 1 : 0;
			$data = $model->filter($data);
			if ($user->guest) {
				$session->set('com_slicomments.data', array('name' => $data['name']));
			}
			if ($model->validate($data) && $model->save($data))
			{
				if (!$user->guest) {
					$data['name'] = $user->name;
					$data['email'] = $user->email;
				}
				$data['rating'] = 0;
				$view = $this->getView('comments', 'html');
				require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/comments.php';
				$view->params = $model->params;
				$view->partial('comment', $data);
			}
			else {
				throw new Exception((string)$model->getError(), 500);
			}
		}
		catch(Exception $e)
		{
			JResponse::setHeader('status', $e->getCode());
			echo $e->getMessage();
		}
	}

	public function delete()
	{
		try {
			// Check for request forgeries.
			if (!JRequest::checkToken('get')) {
				throw new Exception(JText::_('JINVALID_TOKEN'), 500);
			}
			$user = JFactory::getUser();
			$model = $this->getModel();
			$table = $model->getTable();
			$id = JRequest::getInt('id', null, 'get');

			if (!$id) {
				throw new Exception(JText::_('COM_COMMENTS_ERROR_INVALID_ID'), 500);
			}

			if (!$table->load($id)) {
				throw new Exception(JText::_('COM_COMMENTS_ERROR_COMMENT_DONT_EXIST'), 500);
			}

			if (!$user->authorise('delete', 'com_slicomments') &&
				!(!$user->guest && $user->authorise('delete.own', 'com_slicomments') && $table->user_id == $user->id)) {
				throw new Exception(JText::_('COM_COMMENTS_NO_AUTH'), 403);
			}

			if (!$model->delete($id)) {
				throw new Exception((string)$model->getError(), 500);
			}

			JResponse::setHeader('status', 204);
		} catch (Exception $e) {
			JResponse::setHeader('status', $e->getCode());
			echo $e->getMessage();
		}
	}

	public function vote()
	{
		try {
			// Check for request forgeries.
			if (!JRequest::checkToken('get')) {
				throw new Exception(JText::_('JINVALID_TOKEN'), 500);
			}
			if (!JFactory::getUser()->authorise('vote', 'com_slicomments')){
				throw new JException(JText::_('COM_COMMENTS_NO_AUTH'), 403);
			}
			$model = $this->getModel();
			$vote = JRequest::getInt('v');
			$comment_id = JRequest::getInt('id');
			if (!$model->vote($comment_id, $vote)) {
				throw new JException((string)$model->getError(), 500);
			}
			echo $vote;
		} catch (JException $e) {
			JResponse::setHeader('status', $e->getCode());
			echo $e->getMessage();
		}
	}

	public function getModel($name = 'comments', $prefix = 'sliCommentsModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}
}
