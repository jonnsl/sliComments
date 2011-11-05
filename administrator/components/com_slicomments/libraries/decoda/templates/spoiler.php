<?php
// No direct access
defined('_JEXEC') or die;

$show = $this->message('spoiler') .' ('. $this->message('show') .')';
$hide = $this->message('spoiler') .' ('. $this->message('hide') .')';
?>

<div class="slicomments-spoiler spoiler-hide">
	<button type="button" data-hide="<?php echo $hide; ?>" data-show="<?php echo $show; ?>"><?php echo $show; ?></button>
	<div class="slicomments-spoilerContent">
		<?php echo $content; ?>
	</div>
</div>