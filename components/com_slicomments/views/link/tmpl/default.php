<?php
/**
 * @package		sliComments
 * @subpackage	Front end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
?>
<a href="<?php echo $this->link; ?>#comments"><?php echo JText::sprintf('COM_COMMENTS_COMMENTS_COUNT', $this->total); ?></a>