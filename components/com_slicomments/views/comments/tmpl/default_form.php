<?php
/**
 * @package		sliComments
 * @subpackage	Front end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
$user = JFactory::getUser();
$canComment = $user->authorise('post', 'com_slicomments');
$maximum_chars = $this->params->get('maximum_chars', 500);
?>
<form class="comments_form" action="<?php echo JRoute::_('index.php?option=com_slicomments'); ?>" method="post" data-logged="<?php echo (!$user->guest ? '1' : '0');?>">
	<?php if ($this->avatar) : ?>
		<div class="profile-image-container">
			<img class="profile-image" src="<?php echo $this->escape($this->avatar); ?>" alt="<?php echo $this->escape($user->name); ?>">
		</div>
	<?php endif; ?>

	<div class="comments_form_inputs">
		<input type="hidden" name="task" value="comments.post"/>
		<?php echo JHtml::_('form.token'); ?>
		<?php foreach($this->form->getFieldset() as $field): ?>
			<?php if ($field->hidden): ?>
				<?php echo $field->input; ?>
			<?php else : ?>
				<div class="field">
					<?php echo $field->label; ?>
					<?php echo $field->input; ?>
				</div>
			<?php endif; ?>
		<?php endforeach;?>
	</div>

	<?php if ($canComment && $this->params->get('show_character_counter', true)): ?>
	<p class="comments-remaining">
		<?php echo JText::sprintf('COM_COMMENTS_CHARACTERS_REMAINING', '<span class="chars-count">'.$maximum_chars.'</span>'); ?>
	</p>
	<?php endif; ?>
	<p class="comments-post-buttons">
		<span><a href="#" class="cancel-reply">Cancel</a> or </span>
		<button type="submit" class="comment-submit" <?php if (!$canComment) echo ' disabled';?> data-sending="<?php echo JText::_('COM_COMMENTS_LABEL_SENDING'); ?>">
			<?php echo JText::_('COM_COMMENTS_SUBMIT'); ?>
		</button>
	</p>
</form>
