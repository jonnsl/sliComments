<?php
// no direct access
defined('_JEXEC') or die;
require_once JPATH_ADMINISTRATOR.'/components/com_slicomments/helpers/comments.php';
$user = JFactory::getUser();
$comment = (object) $this->data;
?>
<li class="comment">
	<div class="comment-body">
		<div class="profile-image-container">
			<img class="profile-image" src="//www.gravatar.com/avatar/<?php echo md5($comment->email); ?>?s=40" alt="<?php echo $this->escape($comment->name); ?>">
		</div>
		<div class="content-container">
			<ul class="comments-actions">
				<?php if ($this->params->get('ratings', true) && $user->authorise('vote', 'com_slicomments')) : ?>
					<li><a class="comment-like" href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.vote&v=1&id='.$comment->id.'&'.JUtility::getToken().'=1&return='.base64_encode(JFactory::getURI()->toString())); ?>"><?php echo JText::_('COM_COMMENTS_ACTION_LIKE'); ?></a></li>
					<li><a class="comment-dislike" href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.vote&v=-1&id='.$comment->id.'&'.JUtility::getToken().'=1&return='.base64_encode(JFactory::getURI()->toString())); ?>"><?php echo JText::_('COM_COMMENTS_ACTION_DISLIKE'); ?></a></li>
				<?php endif; ?>
				<?php if ($user->authorise('delete', 'com_slicomments') || ($user->authorise('delete.own', 'com_slicomments') && $comment->user_id == $user->id)): ?>
				<li><a class="comment-delete" href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.delete&id='.$comment->id.'&'.JUtility::getToken().'=1&return='.base64_encode(JFactory::getURI()->toString())); ?>" data-id="<?php echo $comment->id; ?>">
					<?php echo JText::_('COM_COMMENTS_ACTION_DELETE'); ?>
				</a></li>
				<?php endif; ?>
			</ul>
			<div class="metadata">
				<span class="author">
					<?php echo $this->escape($comment->name); ?>
				</span>
				<span class="created">
					<?php echo sliCommentsHelper::human_time_diff($comment->created);?>
				</span>
			</div>
			<div class="content">
				<?php echo $comment->text; ?>
			</div>
		</div>
	</div>
	<div class="clr"></div>
</li>