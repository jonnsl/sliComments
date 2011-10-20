<?php
// No direct access
defined('_JEXEC') or die;

class sliCommentsControllerComments extends JController
{
	public $default_view = 'comments';

	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('approve', 'status');
		$this->registerTask('unapprove', 'status');
		$this->registerTask('trash', 'status');
		$this->registerTask('spam', 'status');
		$this->registerTask('delete', 'status');
	}

	/**
	 * Removes a comment.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function status()
	{
		// Check for request forgeries
		JRequest::checkToken() or JRequest::checkToken('get') or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JRequest::getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseWarning(500, JText::_('COM_COMMENTS_NO_COMMENTS_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel('comments');

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			try {
				switch ($this->task)
				{
					
					// Not actually a status change but it remain here to avoid code repetition
					case 'delete':
						$model->delete($cid);
						$message = 'COM_COMMENTS_N_COMMENTS_DELETED';
						break;
					case 'approve':
					case 'unapprove':
					case 'trash':
					case 'spam':
						$model->status($cid, $this->task);
						$message = 'COM_COMMENTS_N_COMMENTS_'.(strtoupper($this->task));
						break;
				}
				JFactory::getApplication()->enqueueMessage(JText::plural($message, count($cid)));
			}
			catch(JException $e) {
				JError::throwError($e);
			}
		}

		$this->display();
	}
}