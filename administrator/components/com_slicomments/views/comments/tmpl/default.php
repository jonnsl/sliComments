<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/helpers/comments.php';

$Joomla3 = version_compare(JVERSION, "3.0", "ge");
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
JHtml::_('script', 'slicomments/comments_admin.js', true, true);
JHtml::_('stylesheet', 'slicomments/chosen.css', array(), true);
JHtml::_('stylesheet', 'slicomments/admin.css', array(), true);

$user		= JFactory::getUser();
$token		= '&'.JSession::getFormToken().'=1';
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

$hasFilters =
$this->state->get('filter.search')||
$this->state->get('filter.category') ||
$this->state->get('filter.article') ||
$this->state->get('filter.author') ||
array_diff($this->state->get('filter.status',  array(0,1)), array(0,1)) != array();

?>
<form action="<?php echo JRoute::_('index.php?option=com_slicomments');?>" method="post" name="adminForm" id="adminForm" class="<?php if($hasFilters) echo 'hasFilters'; ?>">

	<div id="j-main-container" class="span12">
	<fieldset id="filter-bar" class="btn-toolbar">
		<?php $this->partial('filters', array('Joomla3'=>$Joomla3)); ?>
	</fieldset>
	<div class="clearfix clr"></div>

	<p class="info-filters">
		<span id="no-filters">No active filters. Use the filter toolbar to filter comments.</span>
		<a id="clear-filters" href="#">Clear all active filters.</a>
	</p>
	<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="toggle" value="" id="toggle-check" />
				</th>
				<th width="10%">
					<a href="#" class="sort-column" title="Click to sort by Author" data-sort="name">
						<?php echo JText::_('COM_COMMENTS_HEADING_AUTHOR'); ?>
					</a>
				</th>
				<th>
					<?php echo JText::_('COM_COMMENTS_HEADING_COMMENT'); ?>
					<div id="sort-by-options">
						<?php echo JText::_('COM_COMMENTS_SORT_BY'); ?>
						<a href="#" class="sort-column <?php if($listOrder == 'created') echo ' sort-dir-', strtolower($listDirn); ?>" title="<?php echo JText::_('COM_COMMENTS_SORT_BY_CREATED_TITLE'); ?>" data-sort="created"><?php echo JText::_('COM_COMMENTS_SORT_BY_CREATED'); ?></a>,
						<a href="#" class="sort-column <?php if($listOrder == 'positive_votes') echo ' sort-dir-', strtolower($listDirn); ?>" title="<?php echo JText::_('COM_COMMENTS_SORT_BY_POSITIVE_VOTES_TITLE'); ?>" data-sort="positive_votes"><?php echo JText::_('COM_COMMENTS_SORT_BY_POSITIVE_VOTES'); ?></a>,
						<a href="#" class="sort-column <?php if($listOrder == 'negative_votes') echo ' sort-dir-', strtolower($listDirn); ?>" title="<?php echo JText::_('COM_COMMENTS_SORT_BY_NEGATIVE_VOTES_TITLE'); ?>" data-sort="negative_votes"><?php echo JText::_('COM_COMMENTS_SORT_BY_NEGATIVE_VOTES'); ?></a>,
						<a href="#" class="sort-column <?php if($listOrder == 'hot') echo ' sort-dir-', strtolower($listDirn); ?>" title="<?php echo JText::_('COM_COMMENTS_SORT_BY_HOT_TITLE'); ?>" data-sort="hot"><?php echo JText::_('COM_COMMENTS_SORT_BY_HOT'); ?></a>,
						<a href="#" class="sort-column <?php if($listOrder == 'score') echo ' sort-dir-', strtolower($listDirn); ?>" title="<?php echo JText::_('COM_COMMENTS_SORT_BY_SCORE_TITLE'); ?>" data-sort="score"><?php echo JText::_('COM_COMMENTS_SORT_BY_SCORE'); ?></a>
					</div>
				</th>
				<th width="15%" class="nowrap">
					<a href="#" class="sort-column" title="<?php echo JText::_('COM_COMMENTS_HEADING_TITLE_'.$this->state->get('extension', 'ALL')); ?>" data-sort="item_id">
						<?php echo JText::_('COM_COMMENTS_HEADING_RESPONSE_TO'); ?>
					</a>
				</th>
			</tr>
		</thead>
		<tbody id="comments">
		<?php
		if (count($this->items))
		{
			foreach ($this->items as $i => $item){
				$this->partial('comment', array('i' => $i, 'token' => $token), $item);
			}
		}
		else
		{
			$this->partial('no_results');
		}

		?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<p class="copyright"><?php echo JText::_('COM_COMMENTS_COPYRIGHT'); ?></p>

	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
