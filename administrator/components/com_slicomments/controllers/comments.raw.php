<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class sliCommentsControllerComments extends sliController
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('approve', 'status');
		$this->registerTask('unapprove', 'status');
		$this->registerTask('trash', 'status');
		$this->registerTask('spam', 'status');
		$this->registerTask('delete', 'status');
		$this->registerTask('unflag', 'status');
	}

	/**
	 * Display comments in json.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  sliCommentsControllerComments  This object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_SITE.'/components/com_content/helpers/route.php';
		require_once JPATH_COMPONENT.'/helpers/comments.php';

		$model = $this->getModel('comments');
		$view = $this->getView('Comments', 'html');
		$view->state = $model->getState();
		$view->items = $model->getItems();
		$view->pagination = $model->getPagination();
		$view->partial('ajax');
		//sleep(rand(2,5));
		return $this;
	}

	public function status()
	{
		try {
			// Check for request forgeries.
			if (!JRequest::checkToken('get')) {
				throw new Exception(JText::_('JINVALID_TOKEN'), 500);
			}

			// Get items to remove from the request.
			$id = JRequest::getVar('id', array(), '', 'array');

			if (!is_array($id) || count($id) < 1) {
				throw new Exception(JText::_('COM_COMMENTS_NO_COMMENTS_SELECTED'));
			}

			// Get the model.
			$model = $this->getModel('comments');
			$user = JFactory::getUser();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($id);

			// Remove the items.
			switch ($this->task)
			{
				// Not actually a status change but it remain here to avoid code repetition
				case 'delete':
					if (!$user->authorise('edit', 'com_slicomments')){
						throw new Exception(JText::_('COM_COMMENTS_NO_AUTH'), 403);
					}
					if (!$model->delete($id)){
						throw new Exception($model->getError(), 500);
					}
					break;
				case 'unflag':
					if (!$user->authorise('core.manage', 'com_slicomments')){
						throw new Exception(JText::_('COM_COMMENTS_NO_AUTH'), 403);
					}
					if (!$model->unflag($cid)){
						throw new Exception($model->getError(), 500);
					}
					break;
				case 'approve':
				case 'unapprove':
				case 'trash':
				case 'spam':
					if (!$user->authorise('core.manage', 'com_slicomments')){
						throw new Exception(JText::_('COM_COMMENTS_NO_AUTH'), 403);
					}
					if (!$model->status($id, $this->task)){
						throw new Exception($model->getError(), 500);
					}
					break;
			}
		}
		catch(Exception $e)
		{
			JResponse::setHeader('status', $e->getCode());
			echo $e->getMessage();
		}
	}

	public function edit()
	{
		try {
			// Check for request forgeries.
			if (!JRequest::checkToken()) {
				throw new Exception(JText::_('JINVALID_TOKEN'), 500);
			}

			// Check for authorisation.
			if (!JFactory::getUser()->authorise('edit', 'com_slicomments')) {
				throw new Exception(JText::_('COM_COMMENTS_NO_AUTH'), 403);
			}

			$model = $this->getModel('comments');
			$data = JRequest::get('post', JREQUEST_ALLOWRAW);
			$id = (int) $data['id'];
			$data = $model->filter($data);
			if ($model->validate($data) && $model->save($id, $data)) {
				echo nl2br(htmlentities($data['raw'], ENT_QUOTES, 'UTF-8'));
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

	public function getAuthors()
	{
		JFactory::getDocument()
			->setMimeEncoding('application/json', false)
			->setCharset('');
		echo json_encode($this->getModel('comments')->getAuthors());
	}

	public function getArticles()
	{
		JFactory::getDocument()
			->setMimeEncoding('application/json', false)
			->setCharset('');
		echo json_encode($this->getModel('comments')->getArticles());
	}
}
