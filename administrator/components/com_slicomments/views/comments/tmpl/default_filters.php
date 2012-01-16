<div class="filter-search fltlft pull-left">
	<input type="text" class="filter-input" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
</div>

<!--div class="filter-select fltrt pull-right" title="Filter by extension">
	<button class="filter-button">
		<img src="templates/bluestork/images/menu/icon-16-component.png" alt="Extension" class="filter-icon"/>
		<span class="arrow-down"></span>
	</button>
	<div class="filter-container">
		<select name="filter_extension" id="filter-extension" class="chzn-select" data-placeholder="- Select Extension -">
			<option value=""></option>
			<option value="com_content">Articles</option>
		</select>
	</div>
</div-->

<div class="filter-select fltrt pull-right" title="Filter by status">
	<button class="filter-button">
		<img src="../media/slicomments/img/status16.png" alt="status" class="filter-icon"/>
		<span class="arrow-down"></span>
	</button>
	<div class="filter-container">
		<ul id="filter-status">
			<?php $status = $this->state->get('filter.status', array(1)); ?>
			<li>
				<input id="status-approved" type="checkbox" value="1" name="filter_status[]" <?php if(in_array(1, $status)) echo 'checked'; ?>/>
				<label for="status-approved"><?php echo JText::_('COM_COMMENTS_OPTION_APPROVED'); ?></label>
			<li>
				<input id="status-pending" type="checkbox" value="0" name="filter_status[]" <?php if(in_array(0, $status)) echo 'checked'; ?>/>
				<label for="status-pending"><?php echo JText::_('COM_COMMENTS_OPTION_PENDING'); ?></label>
			<!--li>
				<input id="status-flagged" type="checkbox" value="-3" name="filter_status[]" checked />
				<label for="status-flagged"><?php echo JText::_('COM_COMMENTS_OPTION_FLAGGED'); ?></label-->
			<li>
				<input id="status-spam" type="checkbox" value="-1" name="filter_status[]" <?php if(in_array(-1, $status)) echo 'checked'; ?>/>
				<label for="status-spam"><?php echo JText::_('COM_COMMENTS_OPTION_SPAM'); ?></label>
			<li>
				<input id="status-trashed" type="checkbox" value="-2" name="filter_status[]" <?php if(in_array(-2, $status)) echo 'checked'; ?>/>
				<label for="status-trashed"><?php echo JText::_('JTRASHED'); ?></label>
		</ul>
	</div>
</div>

<div class="filter-select fltrt pull-right" title="Filter by category">
	<button class="filter-button">
		<?php echo JHtml::_('image', ($Joomla3?'slicomments':'menu').'/icon-16-category.png', 'Category', array('class' => 'filter-icon'), true); ?>
		<span class="arrow-down"></span>
	</button>
	<div class="filter-container">
		<select name="filter_category" id="filter-category" class="chzn-select" data-placeholder="<?php echo JText::_('JOPTION_SELECT_CATEGORY');?>">
		<option value=""></option>
			<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', $this->state->get('filter.category'));?>
		</select>
	</div>
</div>

<div class="filter-select filter-text fltrt pull-right" title="Filter by article">
	<button class="filter-button">
		<?php echo JHtml::_('image', ($Joomla3?'slicomments':'menu').'/icon-16-article.png', 'Article', array('class' => 'filter-icon'), true); ?>
		<span class="arrow-down"></span>
	</button>
	<div class="filter-container">
		<input type="text" id="filter-article" class="filter-input" name="filter_article" placeholder="Filter by article" value="<?php echo $this->escape($this->state->get('filter.article')); ?>"/>
	</div>
</div>

<div class="filter-select filter-text fltrt pull-right" title="Filter by author">
	<button class="filter-button">
		<?php echo JHtml::_('image', ($Joomla3?'slicomments':'menu').'/icon-16-user.png', 'Author', array('class' => 'filter-icon'), true); ?>
		<span class="arrow-down"></span>
	</button>
	<div class="filter-container">
		<input type="text" class="filter-input" id="filter-author" name="filter_author" placeholder="Filter by author" value="<?php echo $this->escape($this->state->get('filter.author')); ?>"/>
	</div>
</div>