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
				$this->setMessage('ID do comentário inválido.', 'error');
			}
		}

		$this->setRedirect(base64_decode($return));
	}
}