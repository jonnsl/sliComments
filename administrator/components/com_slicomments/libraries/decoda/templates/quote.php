<?php
// No direct access
defined('_JEXEC') or die;
?>
<blockquote class="slicomments-quote">
	<?php if (!empty($author) || !empty($date)): ?>
		<div class="slicomments-quoteHead">
			
			<?php if (!empty($author)): ?>
				<span class="slicomments-quoteAuthor">
					<?php echo $this->message('quoteBy', array(
						'author' => htmlentities($author, ENT_QUOTES, 'UTF-8')
					)); ?>
				</span>
			<?php endif ?>
	
			<?php if (!empty($date)): ?>
				<span class="slicomments-quoteDate">
					<?php echo JHtml::_('date', $date); ?>
				</span>
			<?php endif ?>

			<span class="clear"></span>
		</div>
	<?php endif; ?>
	
	<div class="slicomments-quoteBody">
		<?php echo $content; ?>
	</div>
</blockquote>
