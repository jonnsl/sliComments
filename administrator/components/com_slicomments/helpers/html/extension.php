<?php

jimport('joomla.filesystem.folder');

class JHtmlsliCommentsExtension
{
	public function options()
	{
		$options = array();
		$files = JFolder::folders(JPATH_SITE . '/components/com_slicomments/plugins');
		foreach ($files as $extension)
		{
			if (self::isEnabled($extension)) {
				$options[] = JHtml::_('select.option', $extension, JText::_('COM_COMMENTS_'.$extension));
			}
		}
		return $options;
	}

	protected static function isEnabled($element)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('enabled')
			->from('#__extensions')
			->where($query->qn('type').' = '.$db->quote('component'))
			->where($query->qn('element').' = '.$db->quote($element));
		$db->setQuery($query);

		return (bool) $db->loadResult();
	}
}