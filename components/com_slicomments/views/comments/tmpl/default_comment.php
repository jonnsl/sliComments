<?php
// no direct access
defined('_JEXEC') or die;

$user = JFactory::getUser();
?>
<li class="comment">
<div class="comment-body">
	<div class="profile-image-container">
		<img class="profile-image" src="//www.gravatar.com/avatar/<?php echo md5($email); ?>?s=40" alt="<?php echo $this->escape($name); ?>">
	</div>
	<div class="content-container">
		<ul class="comments-actions">
			<?php if ($this->params->get('ratings', true) && $user->authorise('vote', 'com_slicomments')) : ?>
				<li><a class="comment-like" href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.vote&v=1&id='.$id.'&'.JUtility::getToken().'=1&return='.base64_encode(JFactory::getURI()->toString())); ?>"><?php echo JText::_('COM_COMMENTS_ACTION_LIKE'); ?></a></li>
				<li><a class="comment-dislike" href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.vote&v=-1&id='.$id.'&'.JUtility::getToken().'=1&return='.base64_encode(JFactory::getURI()->toString())); ?>"><?php echo JText::_('COM_COMMENTS_ACTION_DISLIKE'); ?></a></li>
			<?php endif; ?>
			<?php if ($user->authorise('delete', 'com_slicomments') || ($user->authorise('delete.own', 'com_slicomments') && $user_id == $user->id)): ?>
			<li><a class="comment-delete" href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.delete&id='.$id.'&'.JUtility::getToken().'=1&return='.base64_encode(JFactory::getURI()->toString())); ?>" data-id="<?php echo $id; ?>">
				<?php echo JText::_('COM_COMMENTS_ACTION_DELETE'); ?></a></li>
			<?php endif; ?>
			<?php if ($user->authorise('post', 'com_slicomments') && ($user_id == 0 || $user_id != $user->id) && $this->params->get('enabled', true)): ?>
			<li><a class="comment-reply" href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.reply&name='.$this->escape($name).'&'.JUtility::getToken().'=1&return='.base64_encode(JFactory::getURI()->toString())); ?>">
				<?php echo JText::_('COM_COMMENTS_ACTION_REPLY'); ?></a></li>
			<?php endif; ?>
		</ul>
		<div class="metadata">
			<span class="author">
				<?php echo $this->linkToProfile($user_id, $name); ?>
			</span>
			<span class="created">
				<?php echo sliCommentsHelper::human_time_diff($created);?>
			</span>
			<?php if ($rating != 0) : ?>
			<span class="rating <?php echo ($rating > 0 ? 'positive' : 'negative'); ?>">
				<?php echo ($rating > 0 ? '+' : '').$rating; ?>
			</span>
			<?php endif; ?>
		</div>
		<div class="content">
			<?php echo $text; ?>
		</div>
	</div>
</div>
<div class="clr"></div>
</li>