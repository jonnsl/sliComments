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
		$this->params = JComponentHelper::getParams('com_slicomments');

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState()
	{
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

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
		$query->select('a.id, CASE WHEN a.user_id = 0 THEN a.name ELSE u.name END as name, CASE WHEN a.user_id = 0 THEN a.email ELSE u.email END as email, a.text, a.created, a.status, c.id as article_id, c.alias, c.title, c.catid, a.raw');
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

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			else if (stripos($search, 'author:') === 0) {
				$search = $db->Quote('%'.$db->getEscaped(substr($search, 7), true).'%');
				$query->where('(a.name LIKE '.$search.' OR u.name LIKE '.$search.' OR u.username LIKE '.$search.')');
			}
			else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('a.raw LIKE '.$search);
			}
		}

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'a.created')).' '.$db->getEscaped($this->getState('list.direction', 'DESC')));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	/**
	 * Returns a record count for the query
	 *
	 * @param    string  $query  The query.
	 *
	 * @return   integer  Number of rows for query
	 * @since    11.1
	 */
	protected function _getListCount($query)
	{
		$query = clone $query;
		$query->clear('select');
		$query->select('count(*)');
		$this->_db->setQuery($query);
		$this->_db->query();

		return $this->_db->loadResult();
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

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if (!$table->delete($pk)) {
				throw new JException($table->getError()->get('message'), 500, E_WARNING);
			}
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

		return true;
	}

	public function filter($data)
	{
		$filter['raw'] = $data['text'];
		$filter['text'] = $this->_parse($data['text']);
		return $filter;
	}

	/**
	 * Parse bbcode into safe HTML
	 * 
	 * @access protected
	 * @param  string $text
	 * @return string
	 */
	protected function _parseBBcode($text)
	{
		if (!$this->params->get('bbcode.enabled', true)) return nl2br(htmlentities($text, ENT_QUOTES, 'UTF-8'));
	
		JLoader::register('Decoda', JPATH_COMPONENT_ADMINISTRATOR.'/libraries/decoda/Decoda.php');
		$code = new Decoda($text);

		$filters = $this->params->get('bbcode.filters');
		foreach ($filters as $filter => $enabled)
		{
			if ($enabled)
			{
				$class = ucfirst($filter).'Filter';
				$code->addFilter(new $class());
			}
		}

		$whitelist = $this->params->get('bbcode.whitelist', '');
		$whitelist = array_map('trim', explode(',', $whitelist));
		call_user_func_array(array($code, 'whitelist'), $whitelist);

		return $code->parse();
	}

	protected function _parse($text)
	{
		$text = $this->_parseBBcode($text);
		$text = $this->_parseEmoticons($text);

		return $text;
	}

	protected function _parseEmoticons($text)
	{
		if (!$this->params->get('emoticons_enabled', true)) return $text;
	
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/emoticon.php';
		$emoticon = new sliComments\Emoticon($this->params->get('emoticons'));

		return $emoticon->parse($text);
	}

	public function validate(&$data)
	{
		if (($n = JString::strlen($data['raw'])) < ($p = $this->params->get('minimum_chars', 5))) {
			$this->setError(JText::sprintf('COM_COMMENTS_ERROR_COMMENT_MINLENGTH', $p, $n));
			return false;
		}
		if (($n = JString::strlen($data['raw'])) > ($p = $this->params->get('maximum_chars', 500))) {
			$this->setError(JText::sprintf('COM_COMMENTS_ERROR_COMMENT_MAXLENGTH', $p, $n));
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