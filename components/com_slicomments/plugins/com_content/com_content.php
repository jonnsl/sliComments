<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');

/**
 * com_content helper.
 */
class SlicommentsContent extends sliExtension
{
	public function getCategoryOptions()
	{
		return JHtml::_('category.options', 'com_content');
	}

	public function getLink($article)
	{
		return 'index.php?option=com_content&view=article&id='. $article->item_id;
	}

	public function getSefLink($xhtml = true, $ssl = null)
	{
		return JRoute::_(ContentHelperRoute::getArticleRoute($this->item->alias ? ($this->item->id . ':' . $this->item->alias) : $this->item->id, $this->item->catid), $xhtml, $ssl);
	}

	public function isEnabled()
	{
		if (!$this->isComponentEnabled()) {
			return false;
		}

		$article = $this->item;
		$params = new JRegistry($article->attribs);
		$global = $this->params->get('enabled', true);
		return ($global && $params->get('slicomments.enabled', true) && $this->isCategoryEnabled($article->catid));
	}

	public function isCategoryEnabled($id = null)
	{
		$catids = $this->params->get('catid');
		if ($catids[0])
		{
			if ($this->params->get('include_child'))
			{
				jimport('joomla.application.categories');
				JModel::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');
				// Get an instance of the generic categories model
				$categories = JModel::getInstance('Categories', 'ContentModel', array('ignore_request' => true));
				$categories->setState('params', JFactory::getApplication()->getParams());
				$categories->setState('filter.get_children', 9999);
				$categories->setState('filter.published', 1);
				$additional_catids = array();

				foreach($catids as $catid)
				{
					$categories->setState('filter.parentId', $catid);
					$items = $categories->getItems(true);

					if ($items)
					{
						foreach($items as $category)
						{
							$additional_catids[] = $category->id;
						}
					}
				}

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
		return (JRequest::getCmd('view') === 'article');
	}

	public function isListView()
	{
		$view = strtolower(JRequest::getCmd('view'));
		return ($this->params->get('comments_count', false) && ($view == 'featured' || $view == 'category'));
	}
}