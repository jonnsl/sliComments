<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<tr class="row<?php echo $i % 2; ?>">
	<td class="center">
		<input type="checkbox" id="comment_<?php echo $id; ?>" name="id[]" value="<?php echo $id; ?>" />
	</td>
	<td>
		<?php echo $name != '' ? $this->escape($name) : JText::_('COM_COMMENTS_ANONYMOUS'); ?>
		<div class="comment-email"><span title="<?php echo $this->escape($email); ?>"><?php echo $this->escape($email); ?></span></div>
	</td>
	<td class="comment <?php echo sliCommentsHelper::getClass($status); ?>">
		<div class="submitted" title="<?php echo JHtml::_('date', $created, JText::_('DATE_FORMAT_LC2'))?>"><?php echo JText::sprintf('COM_COMMENTS_SUBMITTED', sliCommentsHelper::human_time_diff($created)); ?></div>
		<?php
		/*if ($flagged) :
			$desc = implode('<br/>', array_map(array($this, 'escape'), $this->flaggedBy[$id]));
			if ($flagged > 5) {
				$desc .= '<br/>' . JText::sprintf('COM_COMMENTS_FLAGGED_BY_OTHERS', '<b>' . ($flagged - 5) . '</b>');
			}
		?>

			<img src="../media/slicomments/img/spam16.png" title="<?php echo JText::_('COM_COMMENTS_FLAGGED_BY'), '::', $desc; ?>" class="flagged hasTip"/>
		<?php endif;*/ ?>
		<div class="text"><?php
		if ($search = $this->state->get('filter.search')){
			echo sliCommentsHelper::highlight(nl2br($this->escape($raw)), $search);
		} else {
			echo nl2br($this->escape($raw));
		}
		?></div>
		<ul class="actions">
			<li class="unapprove-comment">
				<a href="index.php?option=com_slicomments&amp;task=comments.unapprove&amp;id=<?php echo $id, $token; ?>"><?php echo JText::_('COM_COMMENTS_ACTION_UNAPPROVE'); ?></a>
			<li class="approve-comment">
				<a href="index.php?option=com_slicomments&amp;task=comments.approve&amp;id=<?php echo $id, $token; ?>"><?php echo JText::_('COM_COMMENTS_ACTION_APPROVE'); ?></a>
			<li class="not-spam-comment">
				<a href="index.php?option=com_slicomments&amp;task=comments.approve&amp;id=<?php echo $id, $token; ?>"><?php echo JText::_('COM_COMMENTS_ACTION_NOT_SPAM'); ?></a>
			<li class="restore-comment">
				<a href="index.php?option=com_slicomments&amp;task=comments.approve&amp;id=<?php echo $id, $token; ?>"><?php echo JText::_('COM_COMMENTS_ACTION_RESTORE'); ?></a>
			<li class="edit-comment">
				<a href="#"><?php echo JText::_('COM_COMMENTS_ACTION_EDIT'); ?></a>
			<li class="spam-comment">
				<a href="index.php?option=com_slicomments&amp;task=comments.spam&amp;id=<?php echo $id, $token; ?>"><?php echo JText::_('COM_COMMENTS_ACTION_SPAM'); ?></a>
			<li class="trash-comment">
				<a href="index.php?option=com_slicomments&amp;task=comments.trash&amp;id=<?php echo $id, $token; ?>"><?php echo JText::_('COM_COMMENTS_ACTION_TRASH'); ?></a>
			<li class="delete-comment">
				<a href="index.php?option=com_slicomments&amp;task=comments.delete&amp;id=<?php echo $id, $token; ?>"><?php echo JText::_('COM_COMMENTS_ACTION_DELETE_PERMANENTLY'); ?></a>
		</ul>
		<?php if ($ip): ?>
		<span class="comment-ip <?php echo in_array($ip, $this->blocked_ips) ? 'blocked-ip' : ''; ?>">
			<?php if (in_array($ip, $this->blocked_ips)): ?>
			<a href="index.php?option=com_slicomments&amp;task=comments.unblockIp&amp;ip=<?php echo $this->escape($ip), $token; ?>" title="Unblock IP">
				IP: <?php echo $this->escape($ip); ?>
			</a>
			<?php else: ?>
				<a href="index.php?option=com_slicomments&amp;task=comments.blockIp&amp;ip=<?php echo $this->escape($ip), $token; ?>" title="Block IP">
					IP: <?php echo $this->escape($ip); ?>
				</a>
			<?php endif; ?>
		</span>
		<?php endif; ?>
	</td>
	<td>
		<a href="../<?php echo $this->escape($link); ?>#comments" target="_blank">
			<?php echo $this->escape($title); ?>
		</a>
		<?php if (!$this->state->get('extension')): ?>
			<div class="extension">(<?php echo JText::_('COM_COMMENTS_'.$extension); ?>)</div>
		<?php endif; ?>
	</td>
</tr>
