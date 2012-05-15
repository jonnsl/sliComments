<?php
/**
 * @package		sliComments
 * @subpackage	Back end Component
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$token = '&'.JSession::getFormToken().'=1';
?>
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
