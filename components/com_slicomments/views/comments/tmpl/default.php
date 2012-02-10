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
JHtml::_('stylesheet', 'slicomments/style.css', array(), true);
$user = JFactory::getUser();
$form_position = $this->params->get('form_position', 'before');
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

	<h4><?php echo JText::sprintf('COM_COMMENTS_COMMENTS_COUNT', '<span id="comments_counter" >'.$this->total.'</span>'); ?></h4>
	<?php if ($form_position == 'before' && $enabled) echo $this->loadTemplate('form'); ?>

	<ul id="comments_list" class="comment-list">
	<?php
	foreach ($this->comments as $comment) {
		$this->partial('comment', $comment);
	}
	?>
	</ul>
	<?php if ($form_position == 'after' && $enabled) echo $this->loadTemplate('form'); ?>
	<?php if ($this->params->get('limit', 20) > 0): ?>
	<div id="pagination" class="clr">
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>
