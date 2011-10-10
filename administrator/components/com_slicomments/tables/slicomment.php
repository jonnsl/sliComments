<?php

defined('_JEXEC') or die;

class JTableSliComment extends JTable
{
	public function __construct($db)
	{
		parent::__construct('#__slicomments', 'id', $db);
	}
}