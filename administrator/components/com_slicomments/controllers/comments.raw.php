<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class sliCommentsControllerComments extends JController
{
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
}