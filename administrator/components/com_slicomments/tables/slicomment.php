<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

defined('_JEXEC') or die;

class JTableSliComment extends JTable
{
	public function __construct($db)
	{
		parent::__construct('#__slicomments', 'id', $db);
	}

	public function delete($pk = null)
	{
		if (!parent::delete($pk)) {
			return false;
		}

		$query = $this->_db->getQuery(true);
		$query->delete();
		$query->from('#__slicomments_ratings');
		$query->where('comment_id = '.$this->_db->quote($pk));
		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->query()) {
			$e = new JException(JText::sprintf('COM_COMMENTS_ERROR_DELETE_RATINGS_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

		return true;
	}

	public function status($status)
	{
		// If no primary key is given, return false.
		if ($this->id === null) {
			$e = new JException(JText::_('JLIB_DATABASE_ERROR_NULL_PRIMARY_KEY'));
			$this->setError($e);
			return false;
		}
		$query = $this->_db->getQuery(true)
			->update('#__slicomments')
			->set('status = '.(int)$status)
			->where('id = '.(int)$this->id);
		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->query()) {
			$e = new JException(JText::sprintf('COM_COMMENTS_ERROR_CHANGE_STATUS_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

		if ($status == -1)
		{
			$query = $this->_db->getQuery(true)
				->delete()
				->from('#__slicomments_flags')
				->where('comment_id = '.(int)$this->id);
			$this->_db->setQuery($query);
			// Check for a database error.
			if (!$this->_db->query()) {
				$e = new JException(JText::sprintf('COM_COMMENTS_ERROR_REMOVE_FLAGS_FAILED', get_class($this), $this->_db->getErrorMsg()));
				$this->setError($e);
				return false;
			}
		}

		return true;
	}
}
