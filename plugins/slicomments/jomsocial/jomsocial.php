<?php
/**
 * @package		sliComments
 * @subpackage	Jomsocial Plugin
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('joomla.utilities.string');

class plgSlicommentsJomsocial extends JPlugin
{
	public function onAfterSaveComment($comment, $error)
	{
		if ($error || $comment->user_id === 0 || $comment->status !== 1 ) return;

		// User Points
		require_once JPATH_SITE . '/components/com_community/libraries/userpoints.php';
		CuserPoints::assignPoint('slicomments.new', $comment->user_id);

		// Activity Stream
		require_once JPATH_SITE . '/components/com_content/helpers/route.php';
		require_once JPATH_SITE . '/components/com_community/libraries/core.php';
		$this->loadLanguage('plg_slicomments_jomsocial.sys');

		$act = new stdClass();
		$act->app = 'slicomments';
		$act->cmd = 'slicomment.new';
		$act->cid = $comment->id;
		$act->actor = $comment->user_id;
		$act->target = 0;
		$act->title = JText::sprintf('PLG_SLICOMMENTS_JOMSOCIAL_ACTIVITY_STREAM',
			$this->getArticleLink($comment->article_id, $comment->id));

		$content = strip_tags($comment->text);
		if (JString::strlen($content) > 140)
		{
			$content = wordwrap($content, 140);
			$content = substr($content, 0, strpos($content, "\n"));
		}
		$act->content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

		CFactory::load('libraries', 'activities');
		CActivityStream::add($act);
	}

	public function onVote($comment_id, $vote)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('user_id')
			->from('#__slicomments')
			->where('id = '. (int) $comment_id);
		$db->setQuery($query);
		$user_id = $db->loadResult();

		if (!$user_id) {
			return;
		}

		require_once JPATH_SITE . '/components/com_community/libraries/userpoints.php';
		CuserPoints::assignPoint('slicomments.' . ($vote == 1) ? 'like' : 'dislike', $user_id);
	}

	private function getArticleLink($article_id, $comment_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('alias, id, catid, title')
			->from('#__content')
			->where('id = '. (int) $article_id);
		$db->setQuery($query);
		$article = $db->loadObject();
		$article->slug = $article->alias ? ($article->id . ':' . $article->alias) : $article->id;

		return '<a href="' . JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid)) . '#comment_' . $comment_id . '">'
			. htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8')
			. '</a>';
	}
}
