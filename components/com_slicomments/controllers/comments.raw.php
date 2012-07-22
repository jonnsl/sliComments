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
	public function feed()
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$document->setMimeEncoding('application/rss+xml');
		$item_id = (int) JRequest::getInt('item_id');
		$extension = JRequest::getCmd('extension');

		// Get the view
		$view = $this->getView('comments', 'feed', '', array('layout' => 'rss'));

		// Get/Create the model
		$model = sliComments::getModel('comments', array('ignore_request' => true));
		$model->setState('item.id', $item_id);
		$model->setState('extension', $extension);
		$model->setState('list.limit', $app->getCfg('feed_limit'));
		$view->setModel($model, true);
		$extension = $model->extension;

		if ($extension->load($item_id) && $extension->isEnabled())
		{
			// Send data to the view
			$view->document = $document;
			$view->comments = $model->getItems();
			$view->state = $model->getState();
			$view->item = $extension->item;
			$view->link = $extension->getSefLink(true, -1);

			// Display the view
			$view->display();
		}

		return $this;
	}

	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_slicomments/helpers/comments.php';

		$model = sliComments::getModel('comments');
		$model->setState('item.id', (int) JRequest::getInt('item_id'));
		$model->setState('extension', JRequest::getCmd('extension'));
		$view = $this->getView('Comments', 'html');
		$view->params = $model->getParams();
		$view->state = $model->getState();
		$view->comments = $model->getComments();
		$view->pagination = $model->getPagination();

		$view->partial('ajax');

		return $this;
	}

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
			$model = sliComments::getModel();
			$user = JFactory::getUser();
			$session = JFactory::getSession();
			$data = JRequest::get('post', JREQUEST_ALLOWRAW);
			$data['status'] = $user->authorise('auto_publish', 'com_slicomments') ? 1 : 0;
			$data = $model->filter($data);
			if ($user->guest) {
				$session->set('com_slicomments.data', array('name' => $data['name'], 'email' => $data['email']));
			}
			if ($model->validate($data) && $model->save($data))
			{
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
			$model = sliComments::getModel();
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
				throw new Exception(JText::_('COM_COMMENTS_NO_AUTH'), 403);
			}
			$model = sliComments::getModel();
			$vote = (int) JRequest::getInt('v');
			$comment_id = JRequest::getInt('id');
			if (!$model->vote($comment_id, $vote)) {
				throw new Exception((string)$model->getError(), 500);
			}
			echo $vote;
		} catch (Exception $e) {
			JResponse::setHeader('status', $e->getCode());
			echo $e->getMessage();
		}
	}

	public function flag()
	{
		try {
			// Check for request forgeries.
			if (!JRequest::checkToken('get')) {
				throw new Exception(JText::_('JINVALID_TOKEN'), 500);
			}

			if (!JFactory::getUser()->authorise('flag', 'com_slicomments')){
				throw new Exception(JText::_('COM_COMMENTS_NO_AUTH'), 403);
			}
			$model = sliComments::getModel();
			$comment_id = JRequest::getInt('id');
			if (!$model->flag($comment_id)) {
				throw new Exception((string)$model->getError(), 500);
			}
			echo JText::_('COM_COMMENTS_SUCCESS_FLAG');
		} catch (Exception $e) {
			JResponse::setHeader('status', $e->getCode());
			echo $e->getMessage();
		}
	}

	public function edit()
	{
		try {
			$id = JRequest::getInt('id', null, 'get');
			if (!$id) {
				throw new Exception(JText::_('COM_COMMENTS_ERROR_INVALID_ID'), 500);
			}

			$model = sliComments::getModel();
			$table = $model->getTable();
			if (!$table->load($id)) {
				throw new Exception(JText::_('COM_COMMENTS_ERROR_COMMENT_DONT_EXIST'), 500);
			}

			$user = JFactory::getUser();
			if (!$user->authorise('edit', 'com_slicomments') &&
				!(!$user->guest && $user->authorise('edit.own', 'com_slicomments') && $table->user_id == $user->id)) {
				throw new Exception(JText::_('COM_COMMENTS_NO_AUTH'), 403);
			}

			echo $table->raw;
		} catch (Exception $e) {
			JResponse::setHeader('status', $e->getCode());
			echo $e->getMessage();
		}
	}

	public function save()
	{
		try {
			// Check for request forgeries.
			if (!JRequest::checkToken()) {
				throw new Exception(JText::_('JINVALID_TOKEN'), 500);
			}

			$id = JRequest::getInt('id', null, 'post');
			if (!$id) {
				throw new Exception(JText::_('COM_COMMENTS_ERROR_INVALID_ID'), 500);
			}

			// Check for authorisation.
			$user = JFactory::getUser();
			if (!$user->authorise('edit', 'com_slicomments') &&
				!(!$user->guest && $user->authorise('edit.own', 'com_slicomments') && $table->user_id == $user->id)) {
				throw new Exception(JText::_('COM_COMMENTS_NO_AUTH'), 403);
			}

			$model = sliComments::getModel();
			$table = $model->getTable();
			if (!$table->load($id)) {
				throw new Exception(JText::_('COM_COMMENTS_ERROR_COMMENT_DONT_EXIST'), 500);
			}

			$data = $table->getProperties();
			$text = JRequest::getVar('text', '', 'post', 'string', JREQUEST_ALLOWHTML);
			$data['raw'] = $text;
			$data['text'] = $model->parse($text);

			if (!$model->validate($data) || !$model->save($data))
			{
				throw new Exception((string)$model->getError(), 500);
			}

			echo $data['text'];
		}
		catch (Exception $e) {
			JResponse::setHeader('status', $e->getCode());
			echo $e->getMessage();
		}
	}

	public function getModel($name = 'comments', $prefix = 'sliCommentsModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}
}
