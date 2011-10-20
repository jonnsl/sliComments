<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class sliCommentsModelComments extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'name', 'a.name',
				'article_id', 'a.article_id',
				'created', 'a.created'
			);
		}

		parent::__construct($config);
	}
	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState()
	{
		$status = $this->getUserStateFromRequest($this->context.'.status', 'filter_status', '');
		$this->setState('filter.status', $status);
	
		// List state information.
		parent::populateState('a.created', 'desc');
	}

	/**
	 *
	 *
	 * @return	string
	 */
	function getListQuery()
	{
		// Create a new query object.
		$db = $this->_db;
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.id, CASE WHEN a.user_id = 0 THEN a.name ELSE u.name END as name, CASE WHEN a.user_id = 0 THEN a.email ELSE u.email END as email, a.text, a.created, a.status, c.id as article_id, c.alias, c.title, c.catid');
		$query->from('#__slicomments AS a');

		$query->leftjoin('#__users AS u ON u.id = a.user_id');
		$query->leftjoin('#__content AS c ON c.id = a.article_id');

		// Filter by status
		$status = $this->getState('filter.status', '');
		if ($status == '') {
			$query->where('status >= 0');
		} else if ($status != '*') {
			$query->where('status = '.$db->getEscaped($status));
		}

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'a.created')).' '.$db->getEscaped($this->getState('list.direction', 'DESC')));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	public function getTable()
	{
		return parent::getTable('sliComment', 'JTable');
	}

	/**
	 * Method to delete one or more comments.
	 *
	 * @param   array    $pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 * @since   11.1
	 */
	public function delete(&$pks)
	{
		// Initialise variables.
		$user = JFactory::getUser();
		$table = $this->getTable();

		if ($user->authorise('core.delete'))
		{
			// Iterate the items to delete each one.
			foreach ($pks as $i => $pk)
			{
				if (!$table->delete($pk)) {
					throw new JException($table->getError()->get('message'), 500, E_WARNING);
				}
			}
		} else {
			throw new JException(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), 403, E_WARNING);
		}

		return true;
	}

	/**
	 * Method to change the status of one or more comments.
	 *
	 * @param   array    $pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 * @since   11.1
	 */
	public function status($pks, $status)
	{
		// Initialise variables.
		$values = array(
			'approve' => 1,
			'unapprove' => 0,
			'spam' => -1,
			'trash' => -2
		);
		if (isset($values[$status])) {
			$value = $values[$status];
		} else {
			throw new JException(JText::sprintf('COM_COMMENTS_NOT_VALID_STATUS', $status), 500, E_WARNING);
		}
		$user = JFactory::getUser();
		$table = $this->getTable();
		if (!is_array($pks)){
			$pks = array($pks);
		}

		if ($user->authorise('core.manage'))
		{
			foreach ($pks as $pk)
			{
				if (!$table->load($pk)) {
					throw new JException($table->getError()->get('message'), 500, E_WARNING);
				}

				JPluginHelper::importPlugin('slicomments');
				$dispatcher = JDispatcher::getInstance()
					->trigger('onBeforeChangeCommentState', array($table, $value));

				if (!$table->status($value)) {
					throw new JException($table->getError()->get('message'), 500, E_WARNING);
				}
			}
			
		} else {
			throw new JException(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), 403, E_WARNING);
		}

		return true;
	}

	public function filter($data)
	{
		$filter['text'] = preg_replace('/\n/', '<br />', htmlspecialchars($data['text'], ENT_COMPAT, 'UTF-8'), 10);
		return $filter;
	}

	public function validate(&$data)
	{
		if (($n = JString::strlen($data['text'])) < 5) {
			$this->setError(JText::sprintf('COM_COMMENTS_ERROR_COMMENT_MINLENGTH', $n));
			return false;
		}
		if (($n = JString::strlen($data['text'])) > 500) {
			$this->setError(JText::sprintf('COM_COMMENTS_ERROR_COMMENT_MAXLENGTH', $n));
			return false;
		}
		return true;
	}

	public function save($id, $data)
	{
		$table = $this->getTable();
		if (!$table->load($id)) {
			$this->setError($table->getError());
			return false;
		}
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}
		if (!$table->store($data)) {
			$this->setError($table->getError());
			return false;
		}
		return true;
	}
}