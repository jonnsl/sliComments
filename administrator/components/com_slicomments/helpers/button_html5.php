<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Renders a "html5" button in Joomla 2.5.*
 */
class JButtonHtml5
{
	/**
	 * Get the HTML to render the button
	 *
	 * @param   array  $definition  Parameters to be passed
	 *
	 * @return  string
	 */
	public function render($definition)
	{
		$html  = '<li class="button" id="toolbar-' . $definition[1] . '">';
		$html .= '<a href="#" class="toolbar" data-task="' . $definition[3] . '">';
		$html .= '<span class="icon-32-' . $definition[1] . '"></span>';
		$html .= JText::_($definition[2]);
		$html .= '</a>';
		$html .= '</li>';

		return $html;
	}
}

/**
 * Renders a "html5" button in Joomla! 3.*
 */
class JToolbarButtonHtml5
{
	/*
	 * Get the HTML to render the button
	 *
	 * @param   array  &$definition  Parameters to be passed
	 *
	 * @return  string
	 */
	public function render($definition)
	{
		$html  = '<div class="btn-group" id="toolbar-' . $definition[1] . '">';
		$html .= '<button class="btn btn-small" data-task="' . $definition[3] . '">';
		$html .= '<i class="icon-white icon-' . $definition[1] . '"></i>';
		$html .= JText::_($definition[2]);
		$html .= '</button>';
		$html .= '</div>';

		return $html;
	}
}
