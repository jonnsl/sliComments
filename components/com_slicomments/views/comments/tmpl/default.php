<?php
// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.framework', true);
JHtml::_('stylesheet', 'slicomments/style.css', array(), true);
$user = JFactory::getUser();
$form_position = $this->params->get('form_position', 'before');
JHtml::_('script', 'slicomments/comments_actions.js', true, true);
?>
<div id="comments_section">
	<h4><?php echo JText::sprintf('COM_COMMENTS_COMMENTS_COUNT', '<span id="comments_counter" >'.$this->total.'</span>'); ?></h4>
	<?php if ($form_position == 'before') echo $this->loadTemplate('form'); ?>
	<ul id="comments_list" class="comment-list">
	<?php foreach ($this->items as $i => $item) : ?>
		<li class="comment">
			
			<div class="comment-body">
				<div class="profile-image-container">
					<img class="profile-image" src="//www.gravatar.com/avatar/<?php echo md5($item->email); ?>?s=40" alt="<?php echo $this->escape($item->name); ?>">
				</div>
				<div class="content-container">
					<ul class="comments-actions">
						<?php if ($this->params->get('ratings', true) && $user->authorise('vote', 'com_slicomments')) : ?>
							<li><a class="comment-like" href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.vote&v=1&id='.$item->id.'&'.JUtility::getToken().'=1&return='.base64_encode(JFactory::getURI()->toString())); ?>"><?php echo JText::_('COM_COMMENTS_ACTION_LIKE'); ?></a></li>
							<li><a class="comment-dislike" href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.vote&v=-1&id='.$item->id.'&'.JUtility::getToken().'=1&return='.base64_encode(JFactory::getURI()->toString())); ?>"><?php echo JText::_('COM_COMMENTS_ACTION_DISLIKE'); ?></a></li>
						<?php endif; ?>
						<?php if ($user->authorise('delete', 'com_slicomments') || ($user->authorise('delete.own', 'com_slicomments') && $item->user_id == $user->id)): ?>
						<li><a class="comment-delete" href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.delete&id='.$item->id.'&'.JUtility::getToken().'=1&return='.base64_encode(JFactory::getURI()->toString())); ?>" data-id="<?php echo $item->id; ?>">
							<?php echo JText::_('COM_COMMENTS_ACTION_DELETE'); ?>
						</a></li>
						<?php endif; ?>
					</ul>
					<div class="metadata">
						<span class="author">
							<?php echo $this->escape($item->name); ?>
						</span>
						<?php if ($item->rating != 0) : ?>
						<span class="rating <?php echo ($item->rating > 0 ? 'positive' : 'negative'); ?>">
							<?php echo ($item->rating > 0 ? '+' : '').$item->rating; ?>
						</span>
						<?php endif; ?>
					</div>
					<div class="content">
						<?php echo $item->text; ?>
					</div>
				</div>
			</div>
			<div class="clr"></div>
		</li>
	<?php endforeach; ?>
	</ul>
	<?php if ($form_position == 'after') echo $this->loadTemplate('form'); ?>
	<?php if ($this->params->get('limit', 20) > 0): ?>
	<div id="pagination" class="clr">
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	</div>
	<?php endif; ?>
</div>