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
				<label>Nome</label>
				<input id="comments_form_name" type="text" placeholder="Nome" name="name"/>
			</li>
			<li>
				<label>E-Mail</label>
				<input id="comments_form_email" type="text" placeholder="E-Mail" name="email"/>
			</li>
			<?php else: ?>
				<input id="comments_form_name" type="hidden" name="name" value="<?php echo $user->name; ?>"/>
				<input id="comments_form_email" type="hidden" name="email" value="<?php echo $user->email; ?>"/>
			<?php endif; ?>
			<li>
				<label for="comments_form_textarea">Digite seu comentário aqui...</label>
				<textarea id="comments_form_textarea" name="text" placeholder="Digite seu comentário aqui..."></textarea>
			</li>
		</ul>
		<p class="comments-remaining">
			<span id="comments-remaining-count">500</span> caracteres restantes
		</p>
		<p class="comments-post-buttons">
			<button type="submit" id="comments_form_send">Enviar</button>
		</p>
	</form>
</div>