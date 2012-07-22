<?php
// No direct access
defined('_JEXEC') or die;

echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
	<title><?php echo JText::sprintf('COM_COMMENTS_RSS_TITLE', $this->escape($this->item->title)); ?></title>
	<description></description>
	<link><?php echo $this->link; ?>#comments</link>
	<atom:link href="<?php echo JRoute::_('index.php?option=com_slicomments&task=comments.feed&format=raw&item_id=' . $this->state->get('item.id') . '&extension='.$this->state->get('extension'), true, -1); ?>" rel="self" type="application/rss+xml" />
	<generator>sliComments</generator>
	<language><?php echo $this->document->language; ?></language>
	<?php foreach($this->comments as $comment): ?>
		<item>
		<title><?php echo sprintf('RE: %s', $this->escape($this->item->title)); ?></title>
		<link><?php echo $this->link, '#comment-', $comment->id; ?></link>
		<guid isPermaLink="false"><?php echo $this->link, '#comment-', $comment->id; ?></guid>
		<description><?php echo $this->escape($comment->text); ?></description>
		<pubDate><?php echo JFactory::getDate($comment->created)->toRFC822(true); ?></pubDate>
		<dc:creator><?php echo $this->escape($comment->name); ?></dc:creator>
		</item>
	<?php endforeach; ?>
</channel>
</rss>
