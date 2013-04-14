<?php
/**
 * @package		sliComments
 * @subpackage	Front end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR.'/components/com_slicomments/helpers/comments.php';
JHtml::_('behavior.framework', true);
JHtml::_('stylesheet', 'slicomments/style.css', array(), true);
$user = JFactory::getUser();
JHtml::_('script', 'slicomments/slicomments.js', true, true);
if ($this->params->get('livecomments', false)) {
	JText::script('COM_COMMENTS_ACTION_LIKE', true);
	JText::script('COM_COMMENTS_ACTION_DISLIKE', true);
	JText::script('COM_COMMENTS_ACTION_EDIT', true);
	JText::script('COM_COMMENTS_ACTION_DELETE', true);
	JText::script('COM_COMMENTS_ACTION_FLAG', true);
	JText::script('COM_COMMENTS_ACTION_FLAG_TITLE', true);
	JText::script('COM_COMMENTS_ACTION_REPLY', true);
	JHtml::_('script', 'slicomments/livecomments.js', true, true);

$actions = array('vote', 'edit', 'delete', 'flag', 'post');
foreach ($actions as $action) {
	$permissions[$action] = $user->authorise($action, 'com_slicomments');
}
JFactory::getDocument()->addScriptDeclaration('
	var sliComments = {
		token: "'.JSession::getFormToken().'",
		user: '.json_encode(array(
		'id' => $user->id,
		'permissions' => $permissions
	))."\n\t};\n");
}
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
		<a href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.feed&format=raw&item_id='.$this->state->get('item.id').'&extension='.$this->state->get('extension')); ?>">
			<?php echo JHtml::image('media/slicomments/img/rss.png', 'RSS Icon'); ?></a>
		<?php endif; ?>
		<?php echo JText::sprintf('COM_COMMENTS_COMMENTS_COUNT', '<span id="comments_counter" >'.$this->total.'</span>'); ?></h4>
	<?php if ($user->authorise('post', 'com_slicomments') || $user->guest) echo $this->loadTemplate('form'); ?>

	<?php if ($this->params->get('livecomments', false)): ?>
	<div id="live-comments-info"><?php echo JText::sprintf('COM_COMMENTS_LIVE_COMMENTS_NEW', '<strong>0</strong>',
	'<a href="#" class="show_comments">'.JText::_('COM_COMMENTS_LIVE_COMMENTS_SHOW').'</a>',
	'<a href="#" class="update_comments">'.JText::_('COM_COMMENTS_LIVE_COMMENTS_UPDATE').'</a>');?></div>
	<div id="live-comments-disable"><a href="#"><?php echo JText::_('COM_COMMENTS_LIVE_COMMENTS_DISABLE'); ?></a></div>
	<?php endif; ?>

	<ul id="comments_list" class="comment-list<?php echo $classes; ?>">
	<?php
	foreach ($this->comments as $comment) {
		$this->partial('comment', $comment);
	}
	?>
	</ul>

	<?php if ($this->params->get('limit', 20) > 0): ?>
	<form id="comments_pagination" action="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.display'); ?>" method="get">
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
		<input type="hidden" name="item_id" value="<?php echo $this->state->get('item.id'); ?>" />
		<input type="hidden" name="extension" value="<?php echo $this->state->get('extension'); ?>" />
		<input type="hidden" name="slicommentslimitstart" value="" />
	</form>
	<?php endif; ?>
</div>
