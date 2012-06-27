<?php
/**
 * @package		sliComments
 * @subpackage	Front end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

if (($enabled = $this->params->get('enabled', true)) || $this->total > 0) :
require_once JPATH_ADMINISTRATOR.'/components/com_slicomments/helpers/comments.php';
JHtml::_('behavior.framework', true);
JHtml::_('stylesheet', 'slicomments/style.uncompressed.css', array(), true);
$user = JFactory::getUser();
JHtml::_('script', 'slicomments/slicomments.js', true, true);
?>
<div id="comments" class="no-js<?php if ($this->params->get('avatar', 'gravatar') === '0') echo ' no-avatar'; ?>">

	<?php if (count($this->topComments)): ?>
	<h4><?php echo JText::_('COM_COMMENTS_TOP_COMMENTS'); ?></h4>
	<ul id="top_comments_list" class="comment-list">
	<?php
	foreach ($this->topComments as $comment) {
		$this->partial('comment', $comment);
	}
	?>
	</ul>
	<?php endif; ?>

	<h4>
		<?php if ($this->params->get('show_rss_icon', false)): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.feed&format=raw&article_id=' . $this->state->get('article.id')); ?>">
			<?php echo JHtml::image('media/slicomments/img/rss.png', 'RSS Icon'); ?></a>
		<?php endif; ?>
		<?php echo JText::sprintf('COM_COMMENTS_COMMENTS_COUNT', '<span id="comments_counter" >'.$this->total.'</span>'); ?></h4>
	<?php if ($enabled && ($user->authorise('post', 'com_slicomments') || $user->guest)) echo $this->loadTemplate('form'); ?>

	<ul id="comments_list" class="comment-list">
	<?php
	foreach ($this->comments as $comment) {
		$this->partial('comment', $comment);
	}
	?>
	</ul>

	<?php if ($this->params->get('limit', 20) > 0): ?>
	<form id="comments_pagination" class="clr" action="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.display'); ?>" method="get">
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
		<input type="hidden" name="article_id" value="<?php echo $this->state->get('article.id'); ?>" />
		<input type="hidden" name="slicommentslimitstart" value="" />
	</form>
	<?php endif; ?>
</div>
<?php endif; ?>
