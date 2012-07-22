<?php
/**
 * @package		sliComments
 * @subpackage	Content Plugin
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
JLoader::register('sliComments', JPATH_SITE.'/components/com_slicomments/helper.php');

/**
 * sliComments Content Plugin
 *
 * @package		sliComments
 * @subpackage	Content Plugin
 * @since		1.0
 */
class plgContentSlicomments extends JPlugin
{
	/**
	 * Commentify com_k2.
	 *
	 * @param	object	The item object.
	 * @param	object	The item params
	 * @param	int		The page number
	 */
	public function onK2CommentsBlock($item, $params, $limitstart)
	{
		return '<span id="itemCommentsAnchor"></span>'.sliComments::commentify(compact('item', 'params'));
	}

	public function onK2CommentsCounter($item, $params, $limitstart)
	{
		$numOfComments = sliComments::getTotal(compact('item', 'params'));

		// If comments are disabled for this article, show a "space" as the link.
		if ($numOfComments !== false) {
			$item->numOfComments = $numOfComments;
		} else {
			return '&#32;';
		}
	}

	/**
	 * Commentify com_content.
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The item object.
	 * @param	object	The item params
	 * @param	int		The page number
	 */
	public function onContentAfterDisplay($context, $item, $params, $page = 0)
	{
		$contexts = array(
			'com_content.article' => 1
		);

		if (isset($contexts[$context]))
		{
			return sliComments::commentify(compact('item', 'params'));
		}
	}

	public function onContentAfterDelete($context, $table)
	{
		if ($context !== 'com_content.article'){
			return;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->delete()
			->from('#__slicomments')
			->where('article_id = '.(int)$table->id);
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseWarning(500, 'Error deleting comments from article "'.$table->id.'-'.$table->title.'". '.$db->getErrorMsg());
		}
	}

	public function onContentPrepareForm($form, $data)
	{
		if ($form->getName() != 'com_content.article') return;

		// Load the custom form
		$this->loadLanguage();
		$form->loadFile(dirname(__FILE__).'/article.xml');
	}
}
