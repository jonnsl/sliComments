<?php
// no direct access
defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_content/helpers/route.php';
JHtml::_('core');
JHtml::_('behavior.framework', true);
JHtml::_('script', 'comments/comments_admin.js', true, true);
JHtml::_('script', 'comments/DynamicTextarea.js', true, true);
$user		= JFactory::getUser();
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<style>
	.icon-48-comments {
		background-image: url(../media/comments/img/icon48.png)
	}
	#adminForm .copyright {
		text-align: center;
		margin: 15px 0 0 0
	}
	.adminlist td {
		position: relative
	}
	.edit-comment {
		position: absolute;
		top: 5px;
		right: 5px;
		float: right;
		cursor: pointer;
		display: none
	}
	td:hover .edit-comment {
		display: block
	}
	.editing .edit-comment {
		display: none !important
	}
	.edit {
		width:100%;
		box-sizing:border-box;
		padding: 0
	}
	.actions {
		float: right;
		margin: 5px 5px 0 0
	}
	.actions .save-button {
		cursor: pointer;
		font-size: 1.1em
	}
</style>
<form action="<?php echo JRoute::_('index.php?option=com_comments');?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(this)" />
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_COMMENTS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_COMMENTS_HEADING_TEXT', 'a.text', $listDirn, $listOrder); ?>
				</th>
				<th width="15%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_COMMENTS_HEADING_ARTICLE', 'a.article_id', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php echo $this->escape($item->name); ?>
				</td>
				<td>
					<span><?php echo $this->escape($item->text); ?></span>
					<img class="edit-comment" src="http://a.dryicons.com/images/icon_sets/minimalistica_icons/png/16x16/pencil.png" />
				</td>
				<td>
					<a href="../<?php echo ContentHelperRoute::getArticleRoute($item->alias ? ($item->article_id . ':' . $item->alias) : $item->article_id, $item->catid); ?>">
						<?php echo $this->escape($item->title); ?>
					</a>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<p class="copyright">sliComments is free software released under the <a href="http://www.gnu.org/licenses/gpl-3.0.html">GNU General Public License</a>. Icons by <a href="http://dryicons.com">DryIcons</a>.</p>

	<input type="hidden" name="controller" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
