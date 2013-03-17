<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.utilities.arrayhelper');
jimport('joomla.filesystem.folder');

class sliCommentsModelComments extends sliModel
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'name', 'item_id', 'created'
			);
		}
		$this->params = JComponentHelper::getParams('com_slicomments');

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		$old_state = $app->getUserState($this->context.'filter.extension');
		$extension = $this->getUserStateFromRequest($this->context.'filter.extension', 'filter_extension', null, 'CMD');
		$this->setState('extension', $extension);
		// If the extension filter change, reset the filters
		if ($old_state != $extension)
		{
			$this->resetFilters();
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$filter_author = $this->getUserStateFromRequest($this->context.'.filter.author', 'filter_author');
		$this->setState('filter.author', $filter_author);

		$filter_item = $this->getUserStateFromRequest($this->context.'.filter.item', 'filter_item');
		$this->setState('filter.item', $filter_item);

		$filter_category = $this->getUserStateFromRequest($this->context.'.filter.category', 'filter_category', null, 'INT');
		$this->setState('filter.category', $filter_category);

		$status = $this->getUserStateFromRequest($this->context.'.status', 'filter_status', array(), 'array');
		$status = array_filter($status, 'is_numeric');
		JArrayHelper::toInteger($status);
		$status = array_unique($status);
		if (empty($status)) $status = array(0,1);
		$this->setState('filter.status', $status);

		// Query to search for
		$q = JRequest::getString('q', '');
		$this->setState('filter.q', $q);

		// List state information.
		parent::populateState('created', 'DESC');
	}

	private function resetFilters()
	{
		$app = JFactory::getApplication();

		$filters = array(
			'filter.search' => 'filter_search',
			'filter.author' => 'filter_author',
			'filter.item' => 'filter_item',
			'filter.category' => 'filter_category',
			'status' => 'filter_status',
			'ordercol' => 'filter_order',
			'orderdirn' => 'filter_order_Dir'
		);

		foreach ($filters as $key => $filter)
		{
			$app->setUserState($this->context.'.'.$key, null);
			JRequest::setVar($filter, null);
		}
	}

	public function getAuthors()
	{
		// Create a new query object.
		$db = $this->_db;

		$query = $db->getQuery(true)
			->select('DISTINCT CASE WHEN a.user_id = 0 THEN a.name ELSE u.name END')
			->from('#__slicomments as a')
			->leftjoin('#__users AS u ON u.id = a.user_id')
			->order('text ASC');

		$q = $this->getState('filter.q', false);
		if ($q)
		{
			$search = $db->Quote($db->escape($q, true) . '%', false);
			$query->where('a.name LIKE '. $search . ' OR u.name LIKE ' . $search);
		}

		$this->_db->setQuery($query, 0, $this->getState('list.limit', 20));
		return $this->_db->loadColumn();
	}

	public function getItemsTitles()
	{
		// Create a new query object.
		$db = $this->_db;
		$query = $db->getQuery(true);

		$extension = $this->getHelper();
		$extension->getItemsTitles($query);

		$q = $this->getState('filter.q', false);
		if ($q)
		{
			$search = $db->Quote($db->escape($q, true) . '%', false);
			$extension->filterItem($query, $search);
		}

		$this->_db->setQuery($query, 0, $this->getState('list.limit', 20));
		return $this->_db->loadColumn();
	}

	public function getExtensionHelpers()
	{
		$extensions = JFolder::folders(JPATH_SITE . '/components/com_slicomments/plugins');
		foreach ($extensions as $extension)
		{
			$helpers[$extension] = $this->getHelper($extension);
		}

		return $helpers;
	}

	/**
	 *
	 *
	 * @return	string
	 */
	public function getListQuery()
	{
		// Create a new query object.
		$db = $this->_db;
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select('a.id, CASE WHEN a.user_id = 0 THEN a.name ELSE u.name END as name,'
					.'CASE WHEN a.user_id = 0 THEN a.email ELSE u.email END as email,'
					.'a.created, a.status, a.raw, a.extension, a.item_id');
		$query->from('#__slicomments AS a');

		$query->leftjoin('#__users AS u ON u.id = a.user_id');

		/*$query->select('COUNT(f.user_id) as flagged');
		$query->leftjoin('#__slicomments_flags AS f ON f.comment_id = a.id');*/

		// Filter by status
		$status = $this->getState('filter.status');
		if (!empty($status))
		{
			$query->where('(a.status = ' . implode(' OR a.status = ', $status) . ')');
		}

		// Filter by extension
		$extension = $this->getState('extension');
		if (!empty($extension))
		{
			$this->extension->queryHook($query);
			$query->where('a.extension = '. $db->Quote($extension));

			// Search item
			$item = $this->getState('filter.item');
			if (!empty($item))
			{
				$item = $db->Quote($db->getEscaped($item, true) . '%');
				$this->extension->filterItem($query, $item);
			}

			// Filter by article category
			$category = (int) $this->getState('filter.category');
			if (!empty($category))
			{
				$this->extension->filterCategory($query, $category);
			}
		}
		else
		{
			foreach ($this->getExtensionHelpers() as $helper)
			{
				if ($helper->isComponentEnabled()){
					$helper->queryHook($query);
				}
			}
		}

		// Search comment
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where('a.raw LIKE ' . $search);
		}

		// Search author
		$author = $this->getState('filter.author');
		if (!empty($author))
		{
			$author = $db->Quote('%' . $db->escape($author, true) . '%');
			$query->where('(a.name LIKE ' . $author . ' OR u.name LIKE ' . $author . ')');
		}

		$query->group('a.id');

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.created')).' '.$db->escape($this->getState('list.direction', 'DESC')));

		// echo nl2br(str_replace('#__', 'yhb1y_', $query));
		return $query;
	}

	public function getComments()
	{
		JDEBUG && $GLOBALS['_PROFILER']->mark('beforeGetComments');
		$comments = $this->getItems();
		JDEBUG && $GLOBALS['_PROFILER']->mark('beforePreProcessComments');
		$comments = $this->preProcess($comments);
		JDEBUG && $GLOBALS['_PROFILER']->mark('afterPreProcessComments');
		return $comments;
	}

	/**
	 * Pre process the comments adding the avatar URI if any and the profile URL if any
	 */
	public function preProcess($comments)
	{
		$extensions = $this->getExtensionHelpers();
		foreach ($comments as $comment)
		{
			$comment->link = $extensions[$comment->extension]->getLink($comment);
			$comment->title = $extensions[$comment->extension]->getTitle($comment);
		}
		return $comments;
	}

	public function getFlags()
	{
		$db = $this->_db;
		$flags = array();
		$comments = $this->getItems();

		foreach ($comments as $comment)
		{
			if (!$comment->flagged){
				continue;
			}
			$query = $db->getQuery(true)
				->select('u.name')
				->from('#__slicomments_flags AS a')
				->leftjoin('#__users as u ON u.id = a.user_id')
				->where('a.comment_id = ' . (int) $comment->id);
			$db->setQuery($query, 0, 5);
			$flags[$comment->id] = $db->loadColumn();
		}

		return $flags;
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
		$query->clear('select')
			->clear('group')
			->clear('order')
			->select('count(*)');
		$this->_db->setQuery($query);
		$this->_db->query();

		return $this->_db->loadResult();
	}

	public function getTable($name = 'sliComment', $prefix = 'JTable', $options = array())
	{
		return parent::getTable($name, $prefix, $options);
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

	public function unflag($pks)
	{
		$db = $this->_db;
		$query = $db->getQuery(true);
		$query->delete()
			->from('#__slicomments_flags')
			->where('comment_id = ' . implode(' OR comment_id = ', array_map(array($query, 'quote'), (array) $pks)));
		$db->setQuery($query);
		$db->query();

		return true;
	}

	public function filter($data)
	{
		$filter['raw'] = $data['text'];
		$filter['text'] = $this->parse($data['text']);
		return $filter;
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
