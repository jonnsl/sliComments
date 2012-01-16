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
$name = $this->params->get('name', 1);
$email = $this->params->get('email', 0);
$maximum_chars = $this->params->get('maximum_chars', 500)
?>
<form class="comments_form" action="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.post'); ?>" method="post" data-logged="<?php echo (!$user->guest ? '1' : '0');?>" data-position="<?php echo $this->state->get('list.order_dir', 'DESC') == 'DESC' ? 'top' : 'bottom'; ?>">
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="article_id" value="<?php echo JRequest::getInt('id'); ?>"/>

	<?php if(!$user->guest): ?>
		<div class="profile-image-container">
			<img class="profile-image" src="//www.gravatar.com/avatar/<?php echo md5($user->email); ?>?s=40" alt="<?php echo $this->escape($user->name); ?>">
		</div>
	<?php endif; ?>
	<ul class="comments_form_inputs">
		<?php if ($canComment && $user->guest): ?>
		<?php if ($name != -1): ?>
		<li>
			<label for="comments_form_name"><?php echo JText::_('COM_COMMENTS_LABEL_NAME'); ?></label>
			<input id="comments_form_name" type="text" placeholder="<?php echo JText::_('COM_COMMENTS_LABEL_NAME'); ?>" name="name" class="<?php if ($name == 1) echo 'required ' ?>validation-failed" value="<?php echo $this->escape($this->data['name']); ?>"/>
		</li>
		<?php endif; ?>
		<?php if ($email != -1): ?>
		<li>
			<label for="comments_form_email"><?php echo JText::_('COM_COMMENTS_LABEL_EMAIL'); ?></label>
			<input id="comments_form_email" type="text" placeholder="<?php echo JText::_('COM_COMMENTS_LABEL_EMAIL'); ?>" name="email" class="<?php if ($email == 1) echo 'required ' ?>validate-email" value="<?php echo $this->escape($this->data['email']); ?>"/>
		</li>
		<?php endif; ?>
		<?php endif; ?>
		<li>
			<?php if (!$canComment): ?>
			<label class="login-to-post" for="comments_form_textarea">
				<?php echo preg_replace('/#([^#]*)#/i', '<a href="'.JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JFactory::getURI()->toString().'#comments')).'">$1</a>', JText::_('COM_COMMENTS_LOGIN_TO_POST_COMMENT'), 1); ?>
			</label>
			<?php else: ?>
			<label for="comments_form_textarea"><?php echo JText::_('COM_COMMENTS_LABEL_TEXT'); ?></label>
			<?php endif; ?>
			<textarea id="comments_form_textarea" name="text" class="required minLength:<?php echo $this->params->get('minimum_chars', 5); ?>"
			<?php if (!$canComment) {echo ' disabled';} else {echo 'placeholder="'.JText::_('COM_COMMENTS_PLACEHOLDER_TEXT').'"';} ?>
			data-maxlength="<?php echo $maximum_chars; ?>"><?php echo $this->escape($this->data['text']); ?></textarea>
		</li>
	</ul>
	<?php if ($canComment): ?>
	<p class="comments-remaining">
		<?php echo JText::sprintf('COM_COMMENTS_CHARACTERS_REMAINING', '<span class="chars-count">'.$maximum_chars.'</span>'); ?>
	</p>
	<?php endif; ?>
	<p class="comments-post-buttons">
		<span><a href="#" class="cancel-reply">Cancel</a> or </span>
		<button type="submit" class="comment-submit" <?php if (!$canComment) echo ' disabled';?>>
			<?php echo JText::_('COM_COMMENTS_SUBMIT'); ?>
		</button>
	</p>
</form>
