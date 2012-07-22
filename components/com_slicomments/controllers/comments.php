<?php
/**
 * @package		sliComments
 * @subpackage	Front end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class sliCommentsControllerComments extends sliController
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		parent::__construct();

		// Set a base path for use by the controller
		if (isset($config['base_path'])) {
			$this->basePath = $config['base_path'];
		} else {
			$this->basePath = JPATH_SITE . '/components/com_slicomments';
		}

		// Set the default model search path
		if (isset($config['model_path'])) {
			$this->addModelPath($config['model_path'], $this->model_prefix);
		} else {
			$this->addModelPath($this->basePath . '/models', $this->model_prefix);
		}
	}

	public function display($cachable = false, $urlparams = false)
	{
		// No direct access.
		if (JRequest::getCmd('option') === 'com_slicomments'){
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return $this->setRedirect('index.php');
		}

		$model = sliComments::getModel();
		$extension = $model->extension;

		if ($extension->isItemView()) {
			$view = new sliCommentsViewComments(array('base_path' => $this->basePath));
		} elseif ($extension->isListView()) {
			$view = new sliCommentsViewLink(array('base_path' => $this->basePath));
			$view->link = $extension->getSefLink();
		} else {
			return;
		}

		// Push the model into the view (as default)
		$view->setModel($model, true);

		return $view->display();
	}

	public function post()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$user = JFactory::getUser();
		$data = JRequest::get('post', JREQUEST_ALLOWRAW);
		if (!$user->authorise('post', 'com_slicomments')){
			$this->setMessage(JText::_('COM_COMMENTS_NO_AUTH'), 'error');
		}
		else
		{
			$model = sliComments::getModel();
			$session = JFactory::getSession();
			$data['status'] = $user->authorise('auto_publish', 'com_slicomments') ? 1 : 0;
			$data = $model->filter($data);
			if ($user->guest) {
				$session->set('com_slicomments.data', array('name' => $data['name'], 'email' => $data['email']));
			}
			if (!$model->validate($data) || !$model->save($data))
			{
				$this->setMessage($model->getError(), 'error');
				$session->set('com_slicomments.data', $data);
			}
		}

		$this->setRedirect($this->getReferrer('comments'));
	}

	public function delete()
	{
		// Check for request forgeries.
		JRequest::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

		try {
			$user = JFactory::getUser();
			$model = sliComments::getModel();
			$table = $model->getTable();
			$id = JRequest::getInt('id', null, 'get');

			if (!$id) {
				throw new Exception(JText::_('COM_COMMENTS_ERROR_INVALID_ID'));
			}
			if (!$table->load($id)) {
				throw new Exception(JText::_('COM_COMMENTS_ERROR_COMMENT_DONT_EXIST'));
			}
			if (!(
				$user->authorise('edit', 'com_slicomments') ||
				($user->authorise('edit.own', 'com_slicomments') && $table->user_id == $user->id)
			)) {
				throw new Exception(JText::_('COM_COMMENTS_NO_AUTH'));
			}
			if (!$model->delete($id)) {
				throw new Exception($model->getError()->getMessage());
			}
		} catch (Exception $e) {
			$this->setMessage($e->getMessage(), 'error');
		}

		$this->setRedirect($this->getReferrer('comments'));
	}

	public function vote()
	{
		// Check for request forgeries.
		JRequest::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

		if (!JFactory::getUser()->authorise('vote', 'com_slicomments')){
			$this->setMessage(JText::_('COM_COMMENTS_NO_AUTH'), 'error');
		}
		else
		{
			$model = sliComments::getModel();
			$vote = (int) JRequest::getInt('v');
			$comment_id = JRequest::getInt('id');
			if ($model->vote($comment_id, $vote)) {
				$this->setMessage(JText::_('COM_COMMENTS_SUCCESS_RATE'));
			} else {
				$this->setMessage($model->getError(), 'error');
			}
		}

		$this->setRedirect($this->getReferrer());
	}

	public function flag()
	{
		// Check for request forgeries.
		JRequest::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

		if (!JFactory::getUser()->authorise('flag', 'com_slicomments')){
			$this->setMessage(JText::_('COM_COMMENTS_NO_AUTH'), 'error');
		}
		else
		{
			$model = sliComments::getModel();
			$comment_id = JRequest::getInt('id');
			if ($model->flag($comment_id)) {
				$this->setMessage(JText::_('COM_COMMENTS_SUCCESS_FLAG'));
			} else {
				$this->setMessage($model->getError(), 'error');
			}
		}

		$this->setRedirect($this->getReferrer());
	}

	public function reply()
	{
		// Check for request forgeries.
		JRequest::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

		$session = JFactory::getSession();
		$oldData = $session->get('com_slicomments.data', array());
		$session->set('com_slicomments.data', array_merge(array('text'=> '@'.trim(JRequest::getString('name')).' '), $oldData));

		$this->setRedirect($this->getReferrer('comments'));
	}

	protected function getReferrer($fragment = false)
	{
		$referrer = JRequest::getVar('HTTP_REFERER', null, 'server');
		if ($referrer && JURI::isInternal($referrer)) {
			$referrer = JURI::getInstance($referrer);
			// Avoid Loops
			if ($referrer->getVar('option') == 'com_slicomments'){
				$referrer = JURI::base();
			} else if ($fragment) {
				$referrer->setFragment($fragment);
			}
		} else {
			$referrer = JURI::base();
		}
		return (string) $referrer;
	}
}
