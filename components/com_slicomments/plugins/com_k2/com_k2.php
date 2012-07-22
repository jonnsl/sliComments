<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

JLoader::register('K2HelperRoute', JPATH_SITE . '/components/com_k2/helpers/route.php');

/**
 * com_k2 helper.
 */
class SlicommentsK2 extends sliExtension
{
	protected $table = '#__k2_items';

	public function getCategoryOptions()
	{
		require_once JPATH_ADMINISTRATOR.'/components/com_k2/models/categories.php';
		$categoriesModel = new K2ModelCategories;
		return $categoriesModel->categoriesTree(NULL, true, false);
	}

	public function getLink($article = null)
	{
		return 'index.php?option=com_k2&view=item&layout=item&id='.$article->item_id;
	}

	public function getSefLink($xhtml = true, $ssl = null)
	{
		return JRoute::_(K2HelperRoute::getItemRoute($this->item->id, $this->item->catid), $xhtml, $ssl);
	}

	public function isEnabled()
	{
		$article = $this->item;
		$global = $this->params->get('enabled', true);
		if (!$article->params instanceof JRegistry) {
			$article->params = new JRegistry($article->params);
		}
		return ($global && $article->params->get('itemComments', true) && $this->isCategoryEnabled($article->catid));
	}

	public function isCategoryEnabled($id = null)
	{
		$catids = $this->params->get('catid');
		if ($catids[0] != 0)
		{
			if ($this->params->get('include_child'))
			{
				require_once JPATH_SITE.'/components/com_k2/models/itemlist.php';
				$additional_catids = K2ModelItemlist::getCategoryTree($catids);

				$catids = array_unique(array_merge($catids, $additional_catids));
			}

			if (!in_array(empty($id) ? $this->item->catid : $id, $catids)) {
				return false;
			}
		}
		return true;
	}

	public function isItemView()
	{
		return (JRequest::getCmd('view') === 'item');
	}

	public function isListView()
	{
		// The comments link is handled in a diferrent event
		return false;
	}
}