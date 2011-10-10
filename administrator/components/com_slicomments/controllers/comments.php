<?php
// No direct access
defined('_JEXEC') or die;

class sliCommentsControllerComments extends JController
{
	/**
	 * Removes a comment.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JRequest::getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseWarning(500, JText::_('COM_COMMENTS_NO_COMMENTS_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			try {
				$model->delete($cid);
				JFactory::getApplication()->enqueueMessage(JText::plural('COM_COMMENTS_N_COMMENTS_DELETED', count($cid)));
			} catch(JException $e) {
				JError::throwError($e);
			}
		}

		$this->display();
	}
}