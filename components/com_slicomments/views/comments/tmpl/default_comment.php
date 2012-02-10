<?php
/**
 * @package		sliComments
 * @subpackage	Front end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$user = JFactory::getUser();
?>
<li class="comment">
<div class="comment-body">
	<?php if (isset($avatar)) : ?>
	<div class="profile-image-container">
		<img class="profile-image" src="<?php echo $avatar; ?>" alt="<?php echo $this->escape($name); ?>">
	</div>
	<?php endif; ?>
	<div class="content-container">
		<ul class="comments-actions">
			<?php if ($this->params->get('ratings', true) && $user->authorise('vote', 'com_slicomments')) : ?>
				<li><a class="comment-like" href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.vote&v=1&id='.$id.'&'.JUtility::getToken().'=1'); ?>"><?php echo JText::_('COM_COMMENTS_ACTION_LIKE'); ?></a></li>
				<li><a class="comment-dislike" href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.vote&v=-1&id='.$id.'&'.JUtility::getToken().'=1'); ?>"><?php echo JText::_('COM_COMMENTS_ACTION_DISLIKE'); ?></a></li>
			<?php endif; ?>
			<?php if ($user->authorise('delete', 'com_slicomments') || ($user->authorise('delete.own', 'com_slicomments') && $user_id == $user->id)): ?>
			<li><a class="comment-delete" href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.delete&id='.$id.'&'.JUtility::getToken().'=1'); ?>" data-id="<?php echo $id; ?>">
				<?php echo JText::_('COM_COMMENTS_ACTION_DELETE'); ?></a></li>
			<?php endif; ?>
			<?php if ($user->authorise('flag', 'com_slicomments')): ?>
			<li><a class="comment-flag" href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.flag&id='.$id.'&'.JUtility::getToken().'=1'); ?>" data-id="<?php echo $id; ?>" title="<?php echo JText::_('COM_COMMENTS_ACTION_FLAG_TITLE'); ?>">
				<?php echo JText::_('COM_COMMENTS_ACTION_FLAG'); ?></a></li>
			<?php endif; ?>
			<?php if ($user->authorise('post', 'com_slicomments') && ($user_id == 0 || $user_id != $user->id) && $this->params->get('enabled', true)): ?>
			<li><a class="comment-reply" href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.reply&name='.$this->escape($name).'&'.JUtility::getToken().'=1'); ?>">
				<?php echo JText::_('COM_COMMENTS_ACTION_REPLY'); ?></a></li>
			<?php endif; ?>
		</ul>
		<div class="metadata">
			<span class="author">
				<?php
				if (isset($link) && !empty($link)) { ?>
				<a href="<?php echo $link; ?>"><?php echo $this->escape($name); ?></a>
				<?php
				} else {
					echo $this->escape($name);
				}
				?>
			</span>
			<span class="created">
				<?php echo sliCommentsHelper::human_time_diff($created);?>
			</span>
			<?php if ($rating != 0) : ?>
			<span class="rating <?php echo ($rating > 0 ? 'positive' : 'negative'); ?>">
				<?php echo ($rating > 0 ? '+' : '').$rating; ?>
			</span>
			<?php endif; ?>
			<?php if (isset($status) && $status == 0) : ?>
			<span class="pending">
				<?php echo JText::_('COM_COMMENTS_PENDING_APPROVAL'); ?>
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
