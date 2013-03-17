<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * extension helper.
 */
abstract class sliExtension
{
	public $item;

	public $option;

	public $sliParams;

	public $params;

	protected $table;

	protected $prefix;

	protected $pk = 'id';

	protected $fields = array('title', 'catid');

	public function __construct($params)
	{
		if (empty($this->option)) {
			$class = get_class($this);
			if (strpos($class, 'Slicomments') === 0) {
				$this->option = 'com_'.strtolower(substr($class, 11));
			}
		}

		if (empty($this->table)) {
			$this->table = preg_replace('/^com_/', '#__', $this->option);
		}
		$this->prefix = preg_replace('/^#__/', '', $this->table);
		$this->sliParams = $params;
		$this->params = new JRegistry($params->get('extension.'.$this->option));
	}

	public function queryHook($query)
	{
		foreach ($this->fields as $field)
		{
			$query->select("$this->table.$field AS {$this->prefix}_{$field}");
		}
		$query->leftjoin("$this->table ON a.extension = \"$this->option\" AND $this->table.$this->pk = a.item_id");
	}

	public function __call($name, $arguments)
	{
		if (strpos($name, 'get') === 0)
		{
			$field = strtolower(substr($name, 3));
			if (in_array($field, $this->fields)) {
				$var = $this->prefix.'_'.$field;
				if (isset($arguments[0]->$var)) {
					return $arguments[0]->$var;
				}
			}
		}
		JError::raiseError(500, 'Call to undefined method '.__CLASS__.'::'.$name.'()');
	}

	public function getItemsTitles($query)
	{
		$query->select('DISTINCT title')
			->from('#__slicomments as a')
			->leftjoin("$this->table ON $this->table.$this->pk = a.item_id")
			->order('title ASC');
	}

	public function filterItem($query, $item)
	{
		$query->where('title LIKE ' . $item);
	}

	public function filterCategory($query, $category)
	{
		$query->where('catid = ' . $category);
	}

	public function getItemIds()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($this->pk)
			->from($this->table);
		$db->setQuery($query);
		return $db->loadResultArray();
	}

	public function load($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($this->table)
			->where($this->pk.' = '.(int) $id);
		$db->setQuery($query);
		$item = $db->loadObject();
		if (!$item) {
			return false;
		}
		$this->item = $item;
		return true;
	}

	public function isComponentEnabled()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('enabled')
			->from('#__extensions')
			->where($query->qn('type').' = '.$db->quote('component'))
			->where($query->qn('element').' = '.$db->quote($this->option));
		$db->setQuery($query);

		return (bool) $db->loadResult();
	}

	abstract public function getCategoryOptions();

	abstract public function getLink($item);

	abstract public function getSefLink();

	abstract public function isEnabled();

	abstract public function isCategoryEnabled($id = null);

	abstract public function isItemView();

	abstract public function isListView();
}