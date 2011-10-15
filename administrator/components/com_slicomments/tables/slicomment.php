<?php

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
}