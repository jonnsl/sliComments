<?php
/**
 * @package		sliComments
 * @subpackage	Akismet Plugin
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.form.formrule');

JLoader::register('Akismet', dirname(__FILE__).'/../Akismet.class.php');

class JFormRuleAkismetApi extends JFormRule
{
	protected $regex = '[a-z0-9]{12}';
	protected $modifiers = 'u';
	public function test(& $element, $value, $group = null, & $input = null, & $form = null)
	{
		if (!parent::test($element, $value, $group, $input , $form)) {
			return false;
		}

		$akismet = new Akismet($input->get('params.site_url'), $value);
		if (!$akismet->isKeyValid()) {
			return new JException(JText::_('PLG_SLICOMMENTS_AKISMET_INVALID_API_KEY'), 500, E_ERROR);
		}
		return true;
	}
}
