<?php
/**
 * @package		sliComments
 * @subpackage	Front end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class sliCommentsModelComments extends JModelList
{
	public function __construct($config = array())
	{
		$this->params = JComponentHelper::getParams('com_slicomments');
		return parent::__construct($config);
	}

	public function filter($data)
	{
		$user = JFactory::getUser();
		if (!$user->guest) {
			$filter['user_id'] = (int)$user->id;
			$filter['name'] = '';
			$filter['email'] = '';
		} else {
			$filter['user_id'] = 0;
			$filter['name'] = $this->params->get('name', 1) != -1 ? trim($data['name']) : '';
			$filter['email'] = $this->params->get('email', 1) != -1 ? $data['email'] : '';
		}
		$filter['raw'] = $data['text'];
		$filter['text'] = $this->_parse($data['text']);
		$filter['return'] = isset($data['return']) ? $data['return'] : '';
		$filter['article_id'] = (int)$data['article_id'];
		$filter['created'] = JFactory::getDate()->toMysql();
		$filter['status'] = (int) $data['status'];

		return $filter;
	}

	/**
	 * Parse bbcode into safe HTML
	 *
	 * @access protected
	 * @param  string $text
	 * @return string
	 */
	protected function _parseBBcode($text)
	{
		JLoader::register('Decoda', JPATH_COMPONENT_ADMINISTRATOR.'/libraries/decoda/Decoda.php');
		$code = new Decoda($text);
		$code->setEscapeHtml(!$this->params->get('html.allow', true));

		$filters = $this->params->get('bbcode.filters', array('default' => 1, 'text' => 1, 'image' => 1, 'quote' => 1, 'url' => 1, 'video' => 1));
		foreach ($filters as $filter => $enabled)
		{
			if ($enabled)
			{
				$class = ucfirst($filter).'Filter';
				$code->addFilter(new $class());
			}
		}

		$whitelist = $this->params->get('bbcode.whitelist', '');
		$whitelist = array_map('trim', explode(',', $whitelist));
		call_user_func_array(array($code, 'whitelist'), $whitelist);

		return $code->parse();
	}

	protected function _parse($text)
	{
		if ($this->params->get('bbcode.enabled', true)) {
			$text = $this->_parseBBcode($text);
		}
		if ($this->params->get('emoticons_enabled', true)) {
			$text = $this->_parseEmoticons($text);
		}
		if ($this->params->get('html.allow', true)) {
			$text = $this->_filterHtml($text);
		} else if (!$this->params->get('bbcode.enabled', true)) {
			$text = nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
		}

		return $text;
	}

	protected function _parseEmoticons($text)
	{
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/emoticon.php';
		$emoticon = new sliCommentsEmoticon($this->params->get('emoticons', array()));

		return $emoticon->parse($text);
	}

	public function _filterHtml($text)
	{
		// Filter settings
		$user		= JFactory::getUser();
		$userGroups	= JAccess::getGroupsByUser($user->get('id'));

		$filters = $this->params->get('html.filters');

		$blackListTags			= array();
		$blackListAttributes	= array();

		$whiteListTags			= array();
		$whiteListAttributes	= array();

		$noHtml		= false;
		$whiteList	= false;
		$blackList	= false;
		$unfiltered	= false;

		// Cycle through each of the user groups the user is in.
		// Remember they are include in the Public group as well.
		foreach ($userGroups as $groupId)
		{
			// May have added a group by not saved the filters.
			if (!isset($filters->$groupId)) {
				continue;
			}

			// Each group the user is in could have different filtering properties.
			$filterData = $filters->$groupId;
			$filterType	= strtoupper($filterData->filter_type);

			if ($filterType == 'NH') {
				// Maximum HTML filtering.
				$noHtml = true;
			}
			else if ($filterType == 'NONE') {
				// No HTML filtering.
				$unfiltered = true;
			}
			else {
				// Black or white list.
				// Preprocess the tags and attributes.
				$tags			= explode(',', $filterData->filter_tags);
				$attributes		= explode(',', $filterData->filter_attributes);
				$tempTags		= array_filter(array_map('trim', $tags));
				$tempAttributes	= array_filter(array_map('trim', $attributes));


				// Collect the black or white list tags and attributes.
				// Each list is cummulative.
				if ($filterType == 'BL') {
					$blackList				= true;
					$blackListTags			= array_merge($blackListTags, $tempTags);
					$blackListAttributes	= array_merge($blackListAttributes, $tempAttributes);
				}
				else if ($filterType == 'WL') {
					$whiteList				= true;
					$whiteListTags			= array_merge($whiteListTags, $tempTags);
					$whiteListAttributes	= array_merge($whiteListAttributes, $tempAttributes);
				}
			}
		}

		// Remove duplicates before processing (because the black list uses both sets of arrays).
		$blackListTags			= array_unique($blackListTags);
		$blackListAttributes	= array_unique($blackListAttributes);
		$whiteListTags			= array_unique($whiteListTags);
		$whiteListAttributes	= array_unique($whiteListAttributes);

		// Unfiltered assumes first priority.
		if ($unfiltered) {
			// Dont apply filtering.
		}
		else {
			// Black lists take second precedence.
			if ($blackList) {
				// Remove the white-listed attributes from the black-list.
				$filter = JFilterInput::getInstance(
					array_diff($blackListTags, $whiteListTags), 			// blacklisted tags
					array_diff($blackListAttributes, $whiteListAttributes), // blacklisted attributes
					1,														// blacklist tags
					1														// blacklist attributes
				);
			}
			// White lists take third precedence.
			else if ($whiteList) {
				$filter	= JFilterInput::getInstance($whiteListTags, $whiteListAttributes, 0, 0, 0);  // turn off xss auto clean
			}
			// No HTML takes last place.
			else {
				$filter = JFilterInput::getInstance();
			}

			$text = $filter->clean($text, 'html');
		}

		return $text;
	}

	public function validate($data)
	{
		$db = $this->_db;
		$query = $db->getQuery(true)
			->select('catid, attribs')
			->from('#__content')
			->where('id = '. (int) $data['article_id']);
		$db->setQuery($query);

		if (!($article = $db->loadAssoc())){
			$this->setError(JText::_('COM_COMMENTS_ERROR_ARTICLE_DONT_EXISTS'));
			return false;
		}

		$params = new JRegistry($article['attribs']);
		if (!$params->get('slicomments.enabled', true)){
			$this->setError(JText::_('COM_COMMENTS_ERROR_COMMENTS_DISABLED'));
			return false;
		}

		if (!$this->isCategoryEnabled($article['catid'])) {
			$this->setError(JText::_('COM_COMMENTS_ERROR_CATEGORY_DISABLED'));
			return false;
		}
		if ($data['user_id'] == 0)
		{
			if ($this->params->get('name', 1) == 1 && empty($data['name'])) {
				$this->setError(JText::_('COM_COMMENTS_ERROR_NAME_REQUIRED'));
				return false;
			}
			if ($this->params->get('check_names', true) && !$this->validateName($data['name'])) {
				$this->setError(JText::_('COM_COMMENTS_ERROR_INVALID_NAME'));
				return false;
			}
			if ($this->params->get('email', 0) == 1 && empty($data['email'])) {
				$this->setError(JText::_('COM_COMMENTS_ERROR_EMAIL_REQUIRED'));
				return false;
			}
		}
		if (($n = JString::strlen($data['raw'])) < ($p = $this->params->get('minimum_chars', 5))) {
			$this->setError(JText::sprintf('COM_COMMENTS_ERROR_COMMENT_MINLENGTH', $p, $n));
			return false;
		}
		if (($n = JString::strlen($data['raw'])) > ($p = $this->params->get('maximum_chars', 500))) {
			$this->setError(JText::sprintf('COM_COMMENTS_ERROR_COMMENT_MAXLENGTH', $p, $n));
			return false;
		}
		return true;
	}

	public function validateName($name)
	{
		$forbidden = $this->params->get('forbidden_names', '');
		$forbidden = array_filter(array_map('trim', explode(',', $forbidden)));

		if (in_array($name, $forbidden)) {
			return false;
		}

		$db = $this->_db;
		$query = $db->getQuery(true);
		$query->select('COUNT(*)')
			->from('#__users')
			->where('username = '.$db->quote($name), 'OR')
			->where('name = '.$db->quote($name));
		$db->setQuery($query);
		return !$db->loadResult();
	}

	public function isCategoryEnabled($id)
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

			if (!in_array($id, $catids)) {
				return false;
			}
		}
		return true;
	}

	public function save(&$data)
	{
		$table = $this->getTable();
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('slicomments');
		$result = $dispatcher->trigger('onBeforeSaveComment', array($table));
		if (in_array(false, $result, true)) {
			$this->setError($table->getError());
			return false;
		}

		if (!$table->store($data)) {
			$this->setError($table->getError());
			return false;
		}
		$user = JFactory::getUser();
		$data['id'] = $table->id;
		$data['rating'] = 0;
		$data['avatar'] = $this->getAvatar();
		$data['link'] = $this->getLink();
		if (!$user->guest) {
			$data['name'] = $user->name;
			$data['email'] = $user->email;
		}
		return true;
	}

	public function delete($id)
	{
		$table = $this->getTable();
		if (!$table->delete($id)) {
			$this->setError($table->getError());
			return false;
		}
		return true;
	}

	public function vote($comment_id, &$vote)
	{
		// Ratings enabled?
		if (!$this->params->get('ratings', true)) {
			$this->setError(JText::_('COM_COMMENTS_ERROR_RATINGS_DISABLED'));
			return false;
		}

		// Valid vote?
		if ($vote !== -1 && $vote !== 1) {
			$this->setError(JText::_('COM_COMMENTS_ERROR_INVALID_VOTE'));
			return false;
		}

		$db = $this->_db;

		// Valid comment?
		$query = $db->getQuery(true)
			->select('article_id')
			->from('#__slicomments')
			->where('id = '. (int) $comment_id)
			->where('status = 1');
		$db->setQuery($query);
		$article_id = $db->loadResult();

		if (!$article_id) {
			$this->setError(JText::_('COM_COMMENTS_ERROR_COMMENTS_DONT_EXISTS'));
			return false;
		}

		$query = $db->getQuery(true)
			->select('attribs')
			->from('#__content')
			->where('id = '. (int) $article_id);
		$db->setQuery($query);
		$params = new JRegistry($db->loadResult());
		if (!$params->get('slicomments.ratings', true)){
			$this->setError(JText::_('COM_COMMENTS_ERROR_RATINGS_DISABLED'));
			return false;
		}

		// Logged in user?
		$user = JFactory::getUser();
		if (!$user->guest)
		{
			// User already voted?
			$user_id = $user->get('id');
			$query = $db->getQuery(true)
				->select('vote')
				->from('#__slicomments_ratings')
				->where('user_id = '. (int) $user_id)
				->where('comment_id = '. (int) $comment_id);
			$db->setQuery($query);
			$voted = $db->loadResult();

			if ($voted == $vote){
				$this->setError(JText::_('COM_COMMENTS_ERROR_ALREADY_VOTED'));
				return false;
			}

			// Vote!
			if ($voted) {
				$query = $db->getQuery(true)
					->update('#__slicomments_ratings')
					->set('vote = '.(int) $vote)
					->where('user_id = '.(int) $user_id)
					->where('comment_id = '.(int) $comment_id);
				$db->setQuery($query);
				$stored = $db->query();
				$vote *= 2;
			}
			else {
				$data = (object) array(
					'user_id'	=> (int) $user_id,
					'vote'		=> (int) $vote,
					'comment_id'=> (int) $comment_id
				);
				$stored = $db->insertObject('#__slicomments_ratings', $data);
			}
		}
		else
		{
			$yesterday = $db->quote(JFactory::getDate('yesterday')->toMysql());
			// Guest already voted?
			if (time() % 2) {
				$query = $db->getQuery(true)
					->delete('#__slicomments_ratings')
					->where('user_id = 0')
					->where('date < '. $yesterday);
				$db->setQuery($query)->query();
			}
			$query = $db->getQuery(true)
				->select('count(*)')
				->from('#__slicomments_ratings')
				->where('user_id = 0')
				->where('ip = '. $db->quote($_SERVER['REMOTE_ADDR']))
				->where('date > '. $yesterday)
				->where('comment_id = '.(int) $comment_id);
			$db->setQuery($query);
			$voted = $db->loadResult();

			if ($voted){
				$this->setError(JText::_('COM_COMMENTS_ERROR_ALREADY_VOTED'));
				return false;
			}

			$data = (object) array(
				'user_id'	=> 0,
				'vote'		=> (int) $vote,
				'comment_id'=> (int) $comment_id,
				'ip'		=> $_SERVER['REMOTE_ADDR'],
				'date'		=> JFactory::getDate()->toMysql()
			);
			$stored = $db->insertObject('#__slicomments_ratings', $data);
		}

		if (!$stored){
			if (JDEBUG) {
				$this->setError(JText::sprintf('COM_COMMENTS_ERROR_COULD_NOT_STORE_VOTE_DEBUG', $db->getErrorMsg()));
			} else {
				$this->setError(JText::_('COM_COMMENTS_ERROR_COULD_NOT_STORE_VOTE'));
			}
		}

		// Update the cache
		$query = $db->getQuery(true)
			->update('#__slicomments')
			->set('rating = rating + '.$vote)
			->where('id = '.(int) $comment_id);
		$updated = $db->setQuery($query)->query();

		if (!$updated){
			if (JDEBUG) {
				$this->setError(JText::sprintf('COM_COMMENTS_ERROR_COULD_NOT_UPDATE_VOTE_CACHE', $db->getErrorMsg()));
			} else {
				$this->setError(JText::_('COM_COMMENTS_ERROR_COULD_NOT_STORE_VOTE'));
			}
			return false;
		}

		return true;
	}

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState()
	{
		$params = new JRegistry((string)$this->state->get('article.params'));
		$this->params->set('enabled', $params->get('slicomments.enabled', true));
		$this->params->set('ratings', $params->get('slicomments.ratings', true) && $this->params->get('ratings', true));
		$this->setState('params', $this->params);

		$limit = $this->params->get('limit', 20);
		$this->setState('list.limit', $limit);

		$value = JRequest::getInt('slicommentslimitstart', 0, 'GET');
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		$order = $this->params->get('ordering', 'DESC');
		if (!in_array($order, array('ASC', 'DESC'))) $order = 'DESC';
		$this->setState('list.order_dir', $order);
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
		$id	.= ':'.$this->getState('article.id');
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.order_dir');
		$id	.= ':'.implode(':', $this->getState('exclude.id', array()));
		return parent::getStoreId($id);
	}

	/**
	 *
	 *
	 * @return	string
	 */
	protected function getListQuery()
	{
		// Name or Username?
		$field = $this->params->get('field_name', 'name');
		if (!in_array($field, array('name', 'username'))) $field = 'name';

		// Create a new query object.
		$db = $this->_db;
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('CASE WHEN a.user_id = 0 THEN a.name ELSE u.'.$field.' END as name, CASE WHEN a.user_id = 0 THEN a.email ELSE u.email END as email, a.text, a.id, a.rating, a.user_id, a.created');
		$query->from('#__slicomments AS a');

		$query->leftjoin('#__users AS u ON u.id = a.user_id');

		$avatar = $this->params->get('avatar', 'gravatar');
		switch ($avatar)
		{
			case 'com_community':
				$query->leftjoin('#__community_users AS j ON j.userid = a.user_id');
				$query->select('j.thumb as avatar');
				break;
			case 'com_kunena':
				$query->leftjoin('#__kunena_users AS k ON k.userid = a.user_id');
				$query->select('k.avatar');
				break;
		}

		// Filter by article
		$query->where('a.article_id = '.(int) $this->getState('article.id'));

		// Show only approved comments
		$query->where('a.status = 1');

		// Don't show the top comments
		$exclude = $this->getState('exclude.id', array());
		if (count($exclude))
		{
			$query->where('a.id <> '.implode(' AND a.id <> ', $exclude));
		}

		// Add the list ordering clause.
		$query->order('a.created '.$this->getState('list.order_dir', 'DESC'));

		// echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	public function getTopComments()
	{
		if (($limit = (int) $this->params->get('top_comments', 0)) <= 0
			|| $this->getState('list.start', 0) > 0
			|| $this->getTotal() < $limit + $this->getState('list.limit', 20)) return array();

		$db = $this->_db;
		$query = $this->getListQuery()
			->clear('order')
			->where('a.rating > 2')
			->order('a.rating DESC, a.created '.$this->getState('list.order_dir', 'DESC'));

		$db->setQuery($query, 0, $limit);
		$comments = $db->loadObjectList();

		$exclude = array();
		foreach ($comments as $comment)
		{
			$exclude[] = $comment->id;
		}
		$this->setState('exclude.id', $exclude);

		return $this->preProcess($comments);
	}

	public function getComments()
	{
		JDEBUG && $GLOBALS['_PROFILER']->mark('beforePreProcessComments');
		$comments = $this->preProcess($this->getItems());
		JDEBUG && $GLOBALS['_PROFILER']->mark('afterPreProcessComments');
		return $comments;
	}

	/**
	 * Pre process the comments adding the avatar URI if any and the profile URL if any
	 */
	protected function preProcess($comments)
	{
		$link	= $this->params->get('link', false);
		$avatar	= $this->params->get('avatar', 'gravatar');

		if ($link == 'com_kunena') {
			require_once JPATH_ADMINISTRATOR.'/components/com_kunena/libraries/factory.php';
		}

		foreach ($comments as $k => $comment)
		{
			switch ($avatar)
			{
				case 'gravatar':
					$comments[$k]->avatar = '//www.gravatar.com/avatar/'.md5($comment->email);
					break;
				case 'com_kunena':
					if ($comment->avatar) {
						$comments[$k]->avatar = 'media/kunena/avatars/resized/size72/'.$comment->avatar;
					} else {
						$comments[$k]->avatar = 'media/kunena/avatars/resized/size72/s_nophoto.jpg';
					}
					break;
				case 'com_community':
					if (!$comment->avatar) {
						$comments[$k]->avatar = 'components/com_community/assets/default_thumb.jpg';
					}
					break;
			}
			if ($comment->user_id == 0) {
				if ($comment->name === '') $comments[$k]->name = JText::_('COM_COMMENTS_ANONYMOUS');
				continue;
			}
			switch ($link)
			{
				case 'com_kunena':
					$comments[$k]->link = KunenaFactory::getProfile()->getProfileURL($comment->user_id);
					break;
				case 'com_community':
					$comments[$k]->link = JRoute::_('index.php?option=com_community&view=profile&userid='.$comment->user_id);
					break;
			}
		}

		return $comments;
	}

	/**
	 * Get the avatar URI of the current logged-in user
	 */
	public function getAvatar()
	{
		$user = JFactory::getUser();
		$avatar	= $this->params->get('avatar', 'gravatar');
		switch ($avatar)
		{
			case 'gravatar':
				return '//www.gravatar.com/avatar/'.md5($user->email);
			case 'com_kunena':
				if ($user->guest) return 'media/kunena/avatars/resized/size72/s_nophoto.jpg';
				$query = $this->_db->getQuery(true)
					->select('avatar')
					->from('#__kunena_users')
					->where('userid = '.(int)$user->id);
				$this->_db->setQuery($query);
				$avatar = $this->_db->loadResult();

				if (!$avatar) {
					return 'media/kunena/avatars/resized/size72/s_nophoto.jpg';
				}
				return 'media/kunena/avatars/resized/size72/'.$avatar;
			case 'com_community':
				if ($user->guest) return 'components/com_community/assets/default_thumb.jpg';
				$query = $this->_db->getQuery(true)
					->select('thumb as avatar')
					->from('#__community_users')
					->where('userid = '.(int)$user->id);
				$this->_db->setQuery($query);
				$avatar = $this->_db->loadResult();

				if (!$avatar) {
					return 'components/com_community/assets/default_thumb.jpg';
				}
				return $avatar;
		}
		return '';
	}

	/**
	 * Get the profile URL of the current logged-in user
	 */
	protected function getLink()
	{
		$user = JFactory::getUser();
		$link = $this->params->get('link', false);
		if (!$link || $user->guest) return;
		switch ($link)
		{
			case 'com_kunena':
				require_once JPATH_ADMINISTRATOR.'/components/com_kunena/libraries/factory.php';
				return KunenaFactory::getProfile()->getProfileURL($user->id);
			case 'com_community':
				return JRoute::_('index.php?option=com_community&view=profile&userid='.$user->id);
		}
	}

	/**
	 * Returns a record count for the query
	 *
	 * @param    string  $query  The query.
	 *
	 * @return   integer  Number of rows for query
	 */
	protected function _getListCount($query)
	{
		$query = clone $query;
		$query->clear('select');
		$query->clear('order');
		$query->select('count(*)');
		$this->_db->setQuery($query);
		$this->_db->query();

		return $this->_db->loadResult();
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  JPagination  A JPagination object for the data set.
	 */
	public function getPagination()
	{
		if ($this->getState('list.limit', 20) == 0) return;
		$pagination = parent::getPagination();
		$pagination->prefix = 'slicomments';
		$url = ContentHelperRoute::getArticleRoute($this->getState('article.slug'), $this->getState('article.catid'));
		$uri = new JUri($url);
		$query = $uri->getQuery(true);
		foreach ($query as $key => $value)
		{
			$pagination->setAdditionalUrlParam($key, $value);
		}
		return $pagination;
	}

	public function getTable()
	{
		return parent::getTable('sliComment', 'JTable');
	}

	public function getParams()
	{
		return $this->params;
	}

	public function getData()
	{
		$session = JFactory::getSession();
		$data = $session->get('com_slicomments.data', array());

		$ret['name'] = isset($data['name']) ? $data['name'] : '';
		$ret['email'] = isset($data['email']) ? $data['email'] : '';
		$ret['text'] = isset($data['text']) ? preg_replace('/<br \/>/', "\n", $data['text'], 10) : '';

		// Reset the data
		$session->set('com_slicomments.data', array('name' => $ret['name']));

		return $ret;
	}
}
