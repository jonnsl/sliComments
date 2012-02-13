<?php
// No direct access
defined('_JEXEC') or die;

$show = sprintf($legend, JText::_('COM_COMMENTS_SHOW'));
$hide = sprintf($legend, JText::_('COM_COMMENTS_HIDE'));
?>

<div class="slicomments-spoiler spoiler-hide">
	<button type="button" data-hide="<?php echo $hide; ?>" data-show="<?php echo $show; ?>"><?php echo $show; ?></button>
	<div class="slicomments-spoilerContent">
		<?php echo $text; ?>
	</div>
</div>