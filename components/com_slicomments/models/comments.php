<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class sliCommentsModelComments extends JModelList
{
	public function filter($data)
	{
		$user = JFactory::getUser();
		if (!$user->guest) {
			$filter['user_id'] = (int)$user->id;
			$filter['name'] = '';
			$filter['email'] = '';
		} else {
			$filter['user_id'] = 0;
			$filter['name'] = $data['name'];
			$filter['email'] = md5($data['email']);
		}
		$filter['text'] = preg_replace('/\n/', '<br />', htmlspecialchars($data['text'], ENT_COMPAT, 'UTF-8'), 10);
		$filter['return'] = isset($data['return']) ? $data['return'] : '';
		$filter['article_id'] = (int)$data['article_id'];
		$filter['created'] = JFactory::getDate()->toMysql();

		return $filter;
	}

	public function validate(&$data)
	{
		if ($data['user_id'] == 0)
		{
			if (!$data['name'] || !preg_match('/^[\w\s]*$/i', $data['name'])) {
				$this->setError('Nome Inválido.');
				return false;
			}
		}
		if (JString::strlen($data['text']) < 5) {
			$this->setError('Comentário muito curto ou inexistente.');
			return false;
		}
		if (JString::strlen($data['text']) > 500) {
			$this->setError('Comentário muito grande.');
			return false;
		}
		return true;
	}

	public function save($data)
	{
		$table = $this->getTable();
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

	public function delete($id)
	{
		$table = $this->getTable();
		return $table->delete($id);
	}

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();

		$limit = 20;
		$this->setState('list.limit', $limit);

		$value = JRequest::getInt('slicommentslimitstart', 0, 'GET');
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @param	string	$id	A prefix for the store id.
	 * @return	string
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('article.id');
		return parent::getStoreId($id);
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
		$query->select('CASE WHEN a.user_id = 0 THEN a.name ELSE u.name END as name, CASE WHEN a.user_id = 0 THEN a.email ELSE u.email END as email, a.text, a.id, a.user_id AS not_guest');
		$query->from('#__slicomments AS a');
		
		$query->leftjoin('#__users AS u ON u.id = a.user_id');

		// Filter by article
		$query->where('a.article_id = '.(int) $this->getState('article.id'));

		// Add the list ordering clause.
		$query->order('a.created DESC');

		// echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  JPagination  A JPagination object for the data set.
	 *
	 * @since   11.1
	 */
	public function getPagination()
	{
		$pagination = parent::getPagination();
		$pagination->prefix = 'slicomments';
		$url = ContentHelperRoute::getArticleRoute($this->getState('article.slug'), $this->getState('article.catid'));
		$uri = new JUri($url);
		$query = $uri->getQuery(true);
		foreach ($query as $key => $value)
		{
			$pagination->setAdditionalUrlParam($key, $value);
		}
		return $pagination;
	}

	public function getTable()
	{
		return parent::getTable('sliComment', 'JTable');
	}
}