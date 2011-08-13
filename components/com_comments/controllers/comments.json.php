<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 *
 */
class CommentsControllerComments extends JController
{
	public function post()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$model = $this->getModel();
		$data = JRequest::get('post');
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
				$data['email'] = md5($user->email);
			}
			$return['data'] = $data;
		}
		echo json_encode($return);
		return;
	}

	public function delete()
	{
		// Check for request forgeries.
		if (!JRequest::checkToken('get')) {
			$return['error'] = JText::_('JINVALID_TOKEN');
			$return['success'] = false;
			echo json_encode($return);
			return;
		}
		if (!JFactory::getUser()->authorise('core.admin')) {
			$return['error'] = 'Unauthorized.';
			$return['success'] = false;
			echo json_encode($return);
			return;
		}

		$model = $this->getModel();
		$id = JRequest::getInt('id', null, 'get');
		if ($id) {
			if (!$model->delete($id)) {
				$return['error'] = $model->getError();
				$return['success'] = false;
			} else {
				$return['success'] = true;
			}
		} else {
			$return['error'] = 'ID do comentário inválido.';
			$return['success'] = false;
		}
		echo json_encode($return);
		return;
	}
}