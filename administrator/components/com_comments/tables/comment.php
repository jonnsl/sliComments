<?php

defined('_JEXEC') or die;

class JTableComment extends JTable
{
	public function __construct($db)
	{
		parent::__construct('#__comments', 'id', $db);
	}
}