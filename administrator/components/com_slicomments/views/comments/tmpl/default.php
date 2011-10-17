<?php
// no direct access
defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_content/helpers/route.php';
JHtml::_('core');
JHtml::_('behavior.framework', true);
JHtml::_('script', 'slicomments/comments_admin.js', true, true);
JHtml::_('script', 'slicomments/DynamicTextarea.js', true, true);
JHtml::_('stylesheet', 'slicomments/admin.css', array(), true);
$user		= JFactory::getUser();
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<form action="<?php echo JRoute::_('index.php?option=com_slicomments');?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(this)" />
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_COMMENTS_HEADING_AUTHOR', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JText::_('COM_COMMENTS_HEADING_COMMENT'); ?>
				</th>
				<th width="15%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_COMMENTS_HEADING_ARTICLE', 'a.article_id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
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
				<td class="comment">
					<span class="submitted">Submitted on: <?php echo JHtml::_('date', $item->created, 'l, d F Y H:i:s');?></span>
					<span class="text"><?php echo $this->escape($item->text); ?></span>
					<img class="edit-comment" src="../media/slicomments/img/edit.png" />
				</td>
				<td>
					<a href="../<?php echo ContentHelperRoute::getArticleRoute($item->alias ? ($item->article_id . ':' . $item->alias) : $item->article_id, $item->catid); ?>">
						<?php echo $this->escape($item->title); ?>
					</a>
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
