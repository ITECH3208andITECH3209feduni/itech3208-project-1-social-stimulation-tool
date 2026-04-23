<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var UserTable $user
 * @var string    $inbox
 * @var int       $unread
 * @var string    $total
 */
?>
<?php if ( $inbox ) { ?>
<a href="<?php echo $inbox; ?>" class="p-2 text-plain <?php echo ( ! $unread ? 'text-muted' : 'text-primary' ); ?> cbPMSMessages">
	<span class="fa fa-lg fa-envelope<?php echo ( ! $unread ? '-open' : null ); ?>"></span>
	<?php echo ( $unread ? $total : null ); ?>
</a>
<?php } else { ?>
<a href="javascript: void(0);" class="p-2 d-none d-sm-inline-block text-plain <?php echo ( ! $unread ? 'text-muted' : 'text-primary' ); ?> cbTooltip cbPMSMessages" data-cbtooltip-open-event="click" data-cbtooltip-close-event="click" data-cbtooltip-button-close="false" data-cbtooltip-width="auto" data-cbtooltip-height="auto" data-cbtooltip-classes="pmMessagesModal pmMessagesModalLoad" data-cbtooltip-open-classes="text-primary"<?php echo ( ! $unread ? ' data-cbtooltip-close-classes="text-muted"' : null ); ?> data-cbtooltip-content-classes="p-0">
	<span class="fa fa-lg fa-envelope<?php echo ( ! $unread ? '-open' : null ); ?>"></span>
	<?php echo ( $unread ? $total : null ); ?>
</a>
<a href="javascript: void(0);" class="p-2 d-inline-block d-sm-none text-plain <?php echo ( ! $unread ? 'text-muted' : 'text-primary' ); ?> cbTooltip cbPMSMessages" data-cbtooltip-modal="true" data-cbtooltip-button-close="false" data-cbtooltip-width="90%" data-cbtooltip-height="90%" data-cbtooltip-open-solo=".pmMessagesModal" data-cbtooltip-classes="pmMessagesModal pmMessagesModalLoad" data-cbtooltip-open-classes="text-primary"<?php echo ( ! $unread ? ' data-cbtooltip-close-classes="text-muted"' : null ); ?> data-cbtooltip-content-classes="p-0">
	<span class="fa fa-lg fa-envelope<?php echo ( ! $unread ? '-open' : null ); ?>"></span>
	<?php echo ( $unread ? $total : null ); ?>
</a>
<?php } ?>