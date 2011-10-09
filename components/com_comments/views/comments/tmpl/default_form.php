<?php
/**
 * @version		$Id:$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$user = JFactory::getUser();
JHtml::_('script', 'comments/comments_form.min.js', true, true);
?>
<div class="comments_form no-js">
	<form action="<?php echo JRoute::_('index.php?option=com_comments&task=comments.post'); ?>" method="post">
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="return" value="<?php echo base64_encode(JFactory::getURI()->toString()); ?>"/>
		<input type="hidden" name="article_id" value="<?php echo JRequest::getInt('id'); ?>"/>
		<ul class="comments_form_inputs">
			<?php if ($user->guest): ?>
			<li>
				<label><?php echo JText::_('COM_COMMENTS_LABEL_NAME'); ?></label>
				<input id="comments_form_name" type="text" placeholder="<?php echo JText::_('COM_COMMENTS_LABEL_NAME'); ?>" name="name"/>
			</li>
			<li>
				<label><?php echo JText::_('COM_COMMENTS_LABEL_EMAIL'); ?></label>
				<input id="comments_form_email" type="text" placeholder="<?php echo JText::_('COM_COMMENTS_LABEL_EMAIL'); ?>" name="email"/>
			</li>
			<?php else: ?>
				<input id="comments_form_name" type="hidden" name="name" value="<?php echo $user->name; ?>"/>
				<input id="comments_form_email" type="hidden" name="email" value="<?php echo $user->email; ?>"/>
			<?php endif; ?>
			<li>
				<label for="comments_form_textarea"><?php echo JText::_('COM_COMMENTS_LABEL_TEXT'); ?></label>
				<textarea id="comments_form_textarea" name="text" placeholder="<?php echo JText::_('COM_COMMENTS_LABEL_TEXT'); ?>"></textarea>
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