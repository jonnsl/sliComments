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
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'text', 'a.text',
				'article_id', 'a.article_id'
			);
		}

		parent::__construct($config);
	}
	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState()
	{
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
		$query->select('a.id, CASE WHEN a.user_id = 0 THEN a.name ELSE u.name END as name, CASE WHEN a.user_id = 0 THEN a.email ELSE u.email END as email, a.text, c.id as article_id, c.alias, c.title, c.catid');
		$query->from('#__comments AS a');

		$query->leftjoin('#__users AS u ON u.id = a.user_id');
		$query->leftjoin('#__content AS c ON c.id = a.article_id');

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'a.created')).' '.$db->getEscaped($this->getState('list.direction', 'DESC')));

		// echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	public function getTable()
	{
		return parent::getTable('comment', 'JTable');
	}

	/**
	 * Method to delete one or more records.
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
}