<tr class="row<?php echo $i % 2; ?>">
	<td class="center">
		<input type="checkbox" id="comment_<?php echo $id; ?>" name="id[]" value="<?php echo $id; ?>" />
	</td>
	<td>
		<?php echo $name != '' ? $this->escape($name) : JText::_('COM_COMMENTS_ANONYMOUS'); ?>
		<div class="comment-email"><span title="<?php echo $this->escape($email); ?>"><?php echo $this->escape($email); ?></span></div>
	</td>
	<td class="comment">
		<span class="submitted"><?php echo JText::sprintf('COM_COMMENTS_SUBMITTED', sliCommentsHelper::human_time_diff($created)); ?></span>
		<?php
		/*if ($flagged) :
			$desc = implode('<br/>', array_map(array($this, 'escape'), $this->flaggedBy[$id]));
			if ($flagged > 5) {
				$desc .= '<br/>' . JText::sprintf('COM_COMMENTS_FLAGGED_BY_OTHERS', '<b>' . ($flagged - 5) . '</b>');
			}
		?>

			<img src="../media/slicomments/img/spam16.png" title="<?php echo JText::_('COM_COMMENTS_FLAGGED_BY'), '::', $desc; ?>" class="flagged hasTip"/>
		<?php endif;*/ ?>
		<span class="text"><?php
		if ($search = $this->state->get('filter.search')){
			echo sliCommentsHelper::highlight(nl2br($this->escape($raw)), $search);
		} else {
			echo nl2br($this->escape($raw));
		}
		?></span>
		<ul class="actions">
			<?php if ($status == 1): ?>
				<li><a href="index.php?option=com_slicomments&amp;task=comments.unapprove&amp;id=<?php echo $id, $token; ?>" class="unapprove-comment"><?php echo JText::_('COM_COMMENTS_ACTION_UNAPPROVE'); ?></a></li>
			<?php elseif ($status == 0) :?>
				<li><a href="index.php?option=com_slicomments&amp;task=comments.approve&amp;id=<?php echo $id, $token; ?>" class="approve-comment"><?php echo JText::_('COM_COMMENTS_ACTION_APPROVE'); ?></a></li>
			<?php elseif ($status == -1) :?>
				<li><a href="index.php?option=com_slicomments&amp;task=comments.approve&amp;id=<?php echo $id, $token; ?>" class="approve-comment"><?php echo JText::_('COM_COMMENTS_ACTION_NOT_SPAM'); ?></a></li>
			<?php elseif ($status == -2) :?>
				<li><a href="index.php?option=com_slicomments&amp;task=comments.approve&amp;id=<?php echo $id, $token; ?>" class="approve-comment"><?php echo JText::_('COM_COMMENTS_ACTION_RESTORE'); ?></a></li>
			<?php endif; ?>

			<?php if ($status != -2) :?>
				<li><a href="#" class="edit-comment"><?php echo JText::_('COM_COMMENTS_ACTION_EDIT'); ?></a></li>
			<?php endif; ?>

			<?php if ($status >= 0) :?>
				<li><a href="index.php?option=com_slicomments&amp;task=comments.spam&amp;id=<?php echo $id, $token; ?>" class="spam-comment"><?php echo JText::_('COM_COMMENTS_ACTION_SPAM'); ?></a></li>
				<li><a href="index.php?option=com_slicomments&amp;task=comments.trash&amp;id=<?php echo $id, $token; ?>" class="trash-comment"><?php echo JText::_('COM_COMMENTS_ACTION_TRASH'); ?></a></li>
			<?php else: ?>
				<li><a href="index.php?option=com_slicomments&amp;task=comments.delete&amp;id=<?php echo $id, $token; ?>" class="delete-comment"><?php echo JText::_('COM_COMMENTS_ACTION_DELETE_PERMANENTLY'); ?></a></li>
			<?php endif; ?>
		</ul>
	</td>
	<td>
		<a href="../<?php echo ContentHelperRoute::getArticleRoute($alias ? ($article_id . ':' . $alias) : $article_id, $catid); ?>">
			<?php echo $this->escape($title); ?>
		</a>
	</td>
</tr>