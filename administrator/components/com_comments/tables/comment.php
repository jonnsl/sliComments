<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Content table
 *
 * @package     Joomla.Platform
 * @subpackage  Table
 * @since       11.1
 */
class JTableComment extends JTable
{
	public function __construct($db)
	{
		parent::__construct('#__comments', 'id', $db);
	}
}