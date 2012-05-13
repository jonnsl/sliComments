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
if (JDEBUG){
	JHtml::_('script', 'slicomments/DynamicTextarea.js', true, true);
	JHtml::_('script', 'slicomments/meio.autocomplete.js', true, true);
	JHtml::_('script', 'slicomments/chosen.js', true, true);
	JHtml::_('script', 'slicomments/comments_admin.uncompressed.js', true, true);
} else {
	JHtml::_('script', 'slicomments/comments_admin.js', true, true);
}
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
array_diff($this->state->get('filter.status'), array(0,1)) != array();

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
						<i class="icon-sort<?php if($listOrder == 'name') echo ' sort-dir-', strtolower($listDirn); ?>"></i>
					</a>
				</th>
				<th>
					<a href="#" class="sort-column" title="Click to sort by Submitted date" data-sort="created">
						<?php echo JText::_('COM_COMMENTS_HEADING_COMMENT'); ?>
						<i class="icon-sort<?php if($listOrder == 'created') echo ' sort-dir-', strtolower($listDirn); ?>"></i>
					</a>
				</th>
				<th width="15%" class="nowrap">
					<a href="#" class="sort-column" title="Click to sort by Article" data-sort="article_id">
						<?php echo JText::_('COM_COMMENTS_HEADING_ARTICLE'); ?>
						<i class="icon-sort<?php if($listOrder == 'article_id') echo ' sort-dir-', strtolower($listDirn); ?>"></i>
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
