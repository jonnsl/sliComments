<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 *
 */
class CommentsModelComments extends JModelList
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
		// Initialise variables.
		$app = JFactory::getApplication();

		$article_id = JRequest::getInt('id');
		$this->setState('filter.article_id', $article_id);

		// List state information.
		parent::populateState('a.created', 'desc');
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
		$id	.= ':'.$this->getState('filter.article_id');
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
		$query->from('#__comments AS a');
		
		$query->leftjoin('#__users AS u ON u.id = a.user_id');

		// Filter by article
		$query->where('a.article_id = '.(int) $this->getState('filter.article_id'));

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'a.created')).' '.$db->getEscaped($this->getState('list.direction', 'DESC')));

		// echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	public function getTable()
	{
		return parent::getTable('comment', 'JTable');
	}
}