<?php
// no direct access
defined('_JEXEC') or die;
$user = JFactory::getUser();
$canComment = $user->authorise('post', 'com_slicomments');
JHtml::_('script', 'slicomments/comments_form.js', true, true);
?>
<div class="comments_form no-js">
	<form id="comments_form" action="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.post'); ?>" method="post" data-logged="<?php echo (!$user->guest ? '1' : '0');?>" data-position="<?php echo $this->state->get('list.order_dir', 'DESC') == 'DESC' ? 'top' : 'bottom'; ?>">
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="return" value="<?php echo base64_encode(JFactory::getURI()->toString()); ?>"/>
		<input type="hidden" name="article_id" value="<?php echo JRequest::getInt('id'); ?>"/>
		<ul class="comments_form_inputs">
			<?php if ($canComment && $user->guest): ?>
			<li>
				<label><?php echo JText::_('COM_COMMENTS_LABEL_NAME'); ?></label>
				<input id="comments_form_name" type="text" placeholder="<?php echo JText::_('COM_COMMENTS_LABEL_NAME'); ?>" name="name" class="required validation-failed"/>
			</li>
			<li>
				<label><?php echo JText::_('COM_COMMENTS_LABEL_EMAIL'); ?></label>
				<input id="comments_form_email" type="text" placeholder="<?php echo JText::_('COM_COMMENTS_LABEL_EMAIL'); ?>" name="email" class="validate-email"/>
			</li>
			<?php endif; ?>
			<li>
				<?php if (!$canComment): ?>
				<label class="login-to-post" for="comments_form_textarea">
					<?php echo preg_replace('/#([^#]*)#/i', '<a href="'.JRoute::_('index.php?option=com_users&view=login').'">$1</a>', JText::_('COM_COMMENTS_LOGIN_TO_POST_COMMENT'), 1); ?>
				</label>
				<?php else: ?>
				<label for="comments_form_textarea"><?php echo JText::_('COM_COMMENTS_LABEL_TEXT'); ?></label>
				<?php endif; ?>
				<textarea id="comments_form_textarea" name="text" class="required minLength:5"
				<?php if (!$canComment) {echo ' disabled';} else {echo 'placeholder="'.JText::_('COM_COMMENTS_LABEL_TEXT').'"';} ?>></textarea>
			</li>
		</ul>
		<?php if ($canComment): ?>
		<p class="comments-remaining">
			<?php echo JText::sprintf('COM_COMMENTS_CHARACTERS_REMAINING', '<span id="comments-remaining-count">500</span>'); ?>
		</p>
		<?php endif; ?>
		<p class="comments-post-buttons">
			<button type="submit" id="comments_form_send" <?php if (!$canComment) echo ' disabled';?>>
				<?php echo JText::_('COM_COMMENTS_SUBMIT'); ?>
			</button>
		</p>
	</form>
</div>