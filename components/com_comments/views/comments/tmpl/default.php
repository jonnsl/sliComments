<?php
/**
 * @version		$Id:$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.framework', true);
JHtml::_('stylesheet', 'comments/style.css', array(), true);
if (JFactory::getUser()->authorise('core.admin')) JHtml::_('script', 'comments/comments_form_delete.js', true, true);
?>
<div id="comments_section">
	<h4>Comentários (<span id="comments_counter" ><?php echo $this->total; ?></span>)</h4>
	<?php echo $this->loadTemplate('form'); ?>
	<ul id="comments_list" class="comment-list">
	<?php foreach ($this->items as $i => $item) : ?>
		<li class="comment">
			<?php if (JFactory::getUser()->authorise('core.admin')): ?>
			<a class="comment-delete" href="<?php echo JRoute::_('index.php?option=com_comments&task=comments.delete&id='.$item->id.'&'. JUtility::getToken() .'=1&return='.base64_encode(JFactory::getURI()->toString())); ?>" data-id="<?php echo $item->id; ?>">
				<?php echo JHtml::_('image.site', 'remove.png', 'media/comments/img/'); ?>
			</a>
			<?php endif; ?>
			<div class="comment-body">
				<div class="profile-image-container">
					<img class="profile-image" src="//www.gravatar.com/avatar/<?php echo ($item->not_guest ? md5($item->email) : $item->email); ?>?s=40" alt="<?php echo $this->escape($item->name); ?>">
				</div>
				<div class="content-container">
					<div class="content">
						<div class="author">
							<?php echo $this->escape($item->name); ?>:
						</div>
						<div class="comment-text">
							<?php echo $item->text; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="clr"></div>
		</li>
	<?php endforeach; ?>
	</ul>
</div>