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
	public function feed()
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$document->setMimeEncoding('application/rss+xml');
		$article_id = JRequest::getInt('article_id');

		// Get some info about the article
		JModel::addIncludePath(JPATH_SITE . '/components/com_content/models');
		$model = $this->getModel('Article', 'ContentModel', array('ignore_request' => true));
		$model->setState('article.id', $article_id);
		$model->setState('params', $app->getParams());
		$model->setState('item.select', 'a.id, a.title, a.alias, a.catid, a.attribs, a.access, a.metadata, a.language');
		$article = $model->getItem($article_id);

		// Get the view
		$view = $this->getView('comments', 'feed', '', array('layout' => 'rss'));

		// Get/Create the model
		$model = $this->getModel('comments', '', array('ignore_request' => true));
		$model->setState('article.id', $article_id);
		$model->setState('list.limit', $app->getCfg('feed_limit'));
		$view->setModel($model, true);

		// Send data to the view
		$view->document = $document;
		$view->comments = $model->getItems();
		$view->article = $article;

		// Display the view
		$view->display();

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
			$model = $this->getModel();
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
				$view->addTemplatePath(JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/com_content/comments');
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
				throw new Exception(JText::_('COM_COMMENTS_NO_AUTH'), 403);
			}
			$model = $this->getModel();
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
			$model = $this->getModel();
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

	public function getModel($name = 'comments', $prefix = 'sliCommentsModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}
}
