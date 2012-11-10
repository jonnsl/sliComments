<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_content/helpers/route.php';
require_once JPATH_COMPONENT.'/helpers/comments.php';

$Joomla3 = version_compare(JVERSION, "3.0", "ge");
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
JHtml::_('script', 'slicomments/comments_admin.js', true, true);
JHtml::_('script', 'slicomments/DynamicTextarea.js', true, true);
JHtml::_('stylesheet', 'slicomments/admin.css', array(), true);

$user		= JFactory::getUser();
$token		= '&'.JSession::getFormToken().'=1';
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

if ($Joomla3) {
	JHtml::_('formbehavior.chosen', 'select');
	?>
<script type="text/javascript">
	window.addEvent('domready', function(){
		var form = $('adminForm'),
			table = $("sortTable"),
			direction = $("directionTable");

		Joomla.orderTable = function(){
			var order = table.getSelected().get('value'),
				dirn;
			if (order != '<?php echo $listOrder; ?>') {
				dirn = 'asc';
			} else {
				dirn = direction.getSelected().get('value');
			}

			form.filter_order.value = order;
			form.filter_order_Dir.value = dirn;
			form.task.value = '';
			form.fireEvent('submit');
			form.submit();
		};
	});
</script>
<?php
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_slicomments');?>" method="post" name="adminForm" id="adminForm">
	<?php if ($Joomla3): ?>
	<div id="j-sidebar-container" class="span2">
		<h4 class="page-header"><?php echo JText::_('JSEARCH_FILTER_LABEL');?></h4>
		<div class="filter-select fltrt">
			<select name="filter_status" id="filter_status" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', sliCommentsHelper::statusOptions() , 'value', 'text', $this->state->get('filter.status'), true);?>
			</select>
		</div>
	</div>
	<?php else: ?>
	<fieldset id="filter-bar" style="">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />

			<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_status" id="filter_status" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', sliCommentsHelper::statusOptions() , 'value', 'text', $this->state->get('filter.status'), true);?>
			</select>
		</div>
	</fieldset>
	<div class="clr"></div>
	<?php endif;?>

	<div id="j-main-container" class="span10">
	<?php if ($Joomla3): ?>
		<fieldset id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>" />
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button class="btn tip hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn tip hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);?>
				</select>
			</div>
		</fieldset>
		<div class="clearfix"></div>
	<?php endif;?>
	<table class="adminlist table table-striped">
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
					<?php echo $item->name != '' ? $this->escape($item->name) : JText::_('COM_COMMENTS_ANONYMOUS'); ?>
					<div class="comment-email"><span title="<?php echo $this->escape($item->email); ?>"><?php echo $this->escape($item->email); ?></span></div>
				</td>
				<td class="comment">
					<span class="submitted"><?php echo JText::sprintf('COM_COMMENTS_SUBMITTED', sliCommentsHelper::human_time_diff($item->created)); ?></span>
					<?php
					if ($item->flagged) :
						$desc = implode('<br/>', array_map(array($this, 'escape'), $this->flaggedBy[$item->id]));
						if ($item->flagged > 5) {
							$desc .= '<br/>' . JText::sprintf('COM_COMMENTS_FLAGGED_BY_OTHERS', '<b>' . ($item->flagged - 5) . '</b>');
						}
					?>

						<img src="../media/slicomments/img/spam16.png" title="<?php echo JText::_('COM_COMMENTS_FLAGGED_BY'), '::', $desc; ?>" class="flagged hasTip"/>
					<?php endif; ?>
					<span class="text"><?php
					if ($search = $this->state->get('filter.search')){
						echo sliCommentsHelper::highlight(nl2br($this->escape($item->raw)), $search);
					} else {
						echo nl2br($this->escape($item->raw));
					}
					?></span>
					<ul class="actions">
						<?php if ($item->status == 1): ?>
							<li><a href="index.php?option=com_slicomments&amp;task=comments.unapprove&amp;cid[]=<?php echo $item->id,$token; ?>" class="unapprove-comment"><?php echo JText::_('COM_COMMENTS_ACTION_UNAPPROVE'); ?></a></li>
						<?php elseif ($item->status == 0) :?>
							<li><a href="index.php?option=com_slicomments&amp;task=comments.approve&amp;cid[]=<?php echo $item->id,$token; ?>" class="approve-comment"><?php echo JText::_('COM_COMMENTS_ACTION_APPROVE'); ?></a></li>
						<?php elseif ($item->status == -1) :?>
							<li><a href="index.php?option=com_slicomments&amp;task=comments.approve&amp;cid[]=<?php echo $item->id,$token; ?>" class="approve-comment"><?php echo JText::_('COM_COMMENTS_ACTION_NOT_SPAM'); ?></a></li>
						<?php elseif ($item->status == -2) :?>
							<li><a href="index.php?option=com_slicomments&amp;task=comments.approve&amp;cid[]=<?php echo $item->id,$token; ?>" class="approve-comment"><?php echo JText::_('COM_COMMENTS_ACTION_RESTORE'); ?></a></li>
						<?php endif; ?>

						<?php if ($item->status != -2) :?>
							<li><a href="#" class="edit-comment"><?php echo JText::_('COM_COMMENTS_ACTION_EDIT'); ?></a></li>
						<?php endif; ?>

						<?php if ($item->status >= 0) :?>
							<li><a href="index.php?option=com_slicomments&amp;task=comments.spam&amp;cid[]=<?php echo $item->id,$token; ?>" class="spam-comment"><?php echo JText::_('COM_COMMENTS_ACTION_SPAM'); ?></a></li>
							<li><a href="index.php?option=com_slicomments&amp;task=comments.trash&amp;cid[]=<?php echo $item->id,$token; ?>" class="trash-comment"><?php echo JText::_('COM_COMMENTS_ACTION_TRASH'); ?></a></li>
						<?php else: ?>
							<li><a href="index.php?option=com_slicomments&amp;task=comments.delete&amp;cid[]=<?php echo $item->id,$token; ?>" class="delete-comment"><?php echo JText::_('COM_COMMENTS_ACTION_DELETE_PERMANENTLY'); ?></a></li>
						<?php endif; ?>
					</ul>
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

	<p class="copyright"><?php echo JText::_('COM_COMMENTS_COPYRIGHT'); ?></p>

	<input type="hidden" name="controller" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
