<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
require_once dirname(__FILE__) . '/../helpers/LoremIpsum.class.php';

class sliCommentsControllerLorem extends JController
{
	public function ipsum()
	{
		$n = JRequest::getInt('n');
		$generator = new LoremIpsumGenerator();
		$db = JFactory::getDbo();
		$model = $this->getModel('comments');
		$article_ids = $this->getArticleIds();
		$users_ids = $this->getUsersId();

		for ($i = 0; $i < $n; $i++)
		{
			$data = new stdClass;
			if (rand(0,10) < 8) {
				$data->user_id = '';
				$data->name = trim(rtrim($generator->getContent(rand(1, 3), 'txt', false), ' .'));
				$data->email = md5(rand(0, 999)) . '@example.com';
			}
			else {
				$data->user_id = $users_ids[array_rand($users_ids)];
				$data->name = '';
				$data->email = '';
			}

			$filter = $model->filter(array('text' => trim($generator->getContent(rand(10, 50), 'plain'))));
			$data->raw = $filter['raw'];
			$data->text = $filter['text'];
			$data->created = $this->getRandDate();
			$data->article_id = $article_ids[array_rand($article_ids)];
			$data->status = $this->getRandStatus();

			$db->insertObject('#__slicomments', $data, 'id');
		}

		$this->setRedirect('index.php?option=com_slicomments', 'Successfully created ' . $n . ' new comments.');
	}

	protected function getArticleIds()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id')
			->from('#__content');
		$db->setQuery($query);
		return $db->loadColumn();
	}

	protected function getUsersId()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id')
			->from('#__users');
		$db->setQuery($query);
		return $db->loadColumn();
	}

	protected function getRandDate()
	{
		$seconds = strtotime('today') - strtotime('today - 6 months');
		$n = rand(0, $seconds);
		return date('Y-m-d H:i:s', strtotime("today - $n seconds"));
	}

	protected function getRandStatus()
	{
		$rand = rand(0, 100);
		if ($rand < 75) return 1;
		if ($rand < 85) return 0;
		if ($rand < 90) return -1;
		if ($rand < 95) return -2;
	}
}
