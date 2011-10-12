<?php
// no direct access
defined('_JEXEC') or die;
$user = JFactory::getUser();
JHtml::_('script', 'slicomments/comments_form.min.js', true, true);
?>
<div class="comments_form no-js">
	<form id="comments_form" action="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.post'); ?>" method="post">
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="return" value="<?php echo base64_encode(JFactory::getURI()->toString()); ?>"/>
		<input type="hidden" name="article_id" value="<?php echo JRequest::getInt('id'); ?>"/>
		<ul class="comments_form_inputs">
			<?php if ($user->guest): ?>
			<li>
				<label><?php echo JText::_('COM_COMMENTS_LABEL_NAME'); ?></label>
				<input id="comments_form_name" type="text" placeholder="<?php echo JText::_('COM_COMMENTS_LABEL_NAME'); ?>" name="name" class="required validation-failed"/>
			</li>
			<li>
				<label><?php echo JText::_('COM_COMMENTS_LABEL_EMAIL'); ?></label>
				<input id="comments_form_email" type="text" placeholder="<?php echo JText::_('COM_COMMENTS_LABEL_EMAIL'); ?>" name="email" class="validate-email"/>
			</li>
			<?php else: ?>
				<input id="comments_form_name" type="hidden" name="name" value="<?php echo $user->name; ?>"/>
				<input id="comments_form_email" type="hidden" name="email" value="<?php echo $user->email; ?>"/>
			<?php endif; ?>
			<li>
				<label for="comments_form_textarea"><?php echo JText::_('COM_COMMENTS_LABEL_TEXT'); ?></label>
				<textarea id="comments_form_textarea" name="text" placeholder="<?php echo JText::_('COM_COMMENTS_LABEL_TEXT'); ?>" class="required minLength:5"></textarea>
			</li>
		</ul>
		<p class="comments-remaining">
			<?php echo JText::sprintf('COM_COMMENTS_CHARACTERS_REMAINING', '<span id="comments-remaining-count">500</span>'); ?>
		</p>
		<p class="comments-post-buttons">
			<button type="submit" id="comments_form_send"><?php echo JText::_('COM_COMMENTS_SUBMIT'); ?></button>
		</p>
	</form>
</div>