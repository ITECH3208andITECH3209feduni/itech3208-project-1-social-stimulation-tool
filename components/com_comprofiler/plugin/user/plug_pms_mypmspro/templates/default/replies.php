<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CB\Plugin\PMS\Table\MessageTable;
use CB\Plugin\PMS\PMSHelper;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var CBplug_pmsmypmspro $this
 * @var MessageTable       $row
 * @var MessageTable       $reply
 * @var UserTable          $user
 *
 * @var int                $depth
 */

global $_CB_framework, $_PLUGINS;

$maxDepth	=	$this->params->getInt( 'messages_replies_depth', 5 );
$name		=	$reply->getFrom( 'profile' );

$_PLUGINS->trigger( 'pm_onBeforeDisplayReply', array( &$reply, $depth, &$name, $user ) );
?>
<div class="<?php echo ( $depth > 1 ? 'ml-1 ' : null ) ?>mt-3 pl-2 border-left blockquote text-wrap text-small pmMessageReply pmMessageReply<?php echo $depth; ?>">
	<?php echo $reply->getMessage(); ?>
	<div class="blockquote-footer"><?php echo $name; ?></div>
	<?php
	if ( ( ( $maxDepth && ( $depth < $maxDepth ) ) || ( ! $maxDepth ) ) && $reply->getReplyTo() ) {
		$reply	=	$reply->getReplyTo();

		$depth++;

		require PMSHelper::getTemplate( null, 'replies' );
	}
	?>
</div>