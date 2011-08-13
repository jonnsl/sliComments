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
		$query->select('a.id, CASE WHEN a.user_id = 0 THEN a.name ELSE u.name END as name, CASE WHEN a.user_id = 0 THEN a.email ELSE u.email END as email, a.text');
		$query->from('#__comments AS a');
		
		$query->leftjoin('#__users AS u ON u.id = a.user_id');

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