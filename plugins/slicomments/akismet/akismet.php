<?php
// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
JLoader::register('Akismet', __DIR__.'/Akismet.class.php');

class plgSlicommentsAkismet extends JPlugin
{
	public function onBeforeSaveComment($comment)
	{
		$akismet = new Akismet($this->params->get('site_url'), $this->params->get('api_key'));
		if (!$akismet->isKeyValid()){
			if (JDEBUG) JError::raiseWarning(500, 'The Wordpress API key passed to the Akismet plugin is invalid.  Please obtain a valid one from http://wordpress.com/api-keys/');
			return;
		}
		$user = JFactory::getUser();
		$akismet->setCommentAuthor($comment->user_id ? $user->name : $comment->name);
		$akismet->setCommentAuthorEmail($comment->user_id ? $user->email : $comment->email);
		$akismet->setCommentContent($comment->text);
		$akismet->setCommentType('comment');
		try {
			if ($akismet->isCommentSpam()) {
				// store the comment but mark it as spam (in case of a mis-diagnosis)
				$comment->status = -1;
			}
		} catch (Exception $e) {
			if (JDEBUG) JError::raiseWarning($e);
			return;
		}

		return true;
	}

	public function onBeforeChangeCommentState($comment, $newState)
	{
		try {
			if ($newState == -1) {
				// Submit spam that is incorrectly tagged as ham.
				$akismet = $this->getAkismet($comment);
				$akismet->submitSpam();
			}
			elseif ($comment->state == -1 && $newState == 1) {
				// Submit ham that is incorrectly tagged as spam.
				$akismet = $this->getAkismet($comment);
				$akismet->submitHam();
			}
		} catch (Exception $e) {
			if (JDEBUG) JError::raiseWarning($e->getMessage());
			return false;
		}

		return true;
	}

	private function getAkismet($comment)
	{
		$akismet = new Akismet($this->params->get('site_url'), $this->params->get('api_key'));
		if (!$akismet->isKeyValid()){
			throw new Exception('The Wordpress API key passed to the Akismet plugin is invalid.  Please obtain a valid one from http://wordpress.com/api-keys/');
		}
		$user = JFactory::getUser();
		$akismet->setCommentAuthor($comment->user_id ? $user->name : $comment->name);
		$akismet->setCommentAuthorEmail($comment->user_id ? $user->email : $comment->email);
		$akismet->setCommentContent($comment->text);
		$akismet->setCommentType('comment');
		return $akismet;
	}
}
