<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CB\Database\Table\UserTable;
use CB\Plugin\PMS\Table\MessageTable;
use CB\Plugin\PMS\PMSHelper;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var CBplug_pmsmypmspro $this
 * @var MessageTable       $row
 * @var array              $input
 * @var UserTable          $user
 *
 * @var string             $returnUrl
 * @var int                $toLimit
 * @var int                $messageEditor
 * @var int                $messageLimit
 */

global $_CB_framework, $_PLUGINS;

$pageTitle				=	CBTxt::T( 'Message' );

if ( $pageTitle ) {
	$_CB_framework->setPageTitle( $pageTitle );
}

$menu					=	array();

if ( $row->getInt( 'from_user', 0 ) == $user->getInt( 'user_id', 0 ) ) {
	$read				=	$row->getRead();

	if ( ! $read ) {
		$menu[]			=	'<li class="pmMessageMenuItem" role="presentation"><a href="' . $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'message', 'func' => 'edit', 'id' => $row->getInt( 'id', 0 ), 'return' => PMSHelper::getReturn() ) ) . '" class="dropdown-item" role="menuitem"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';
	}

	$avatar				=	$row->getTo( 'avatar' );
	$name				=	'<span class="text-large">' . $row->getTo( 'profile' ) . '</span>';
	$status				=	$row->getTo( 'status' );
} else {
	$read				=	$row->getRead( $user->getInt( 'user_id', 0 ) );
	$avatar				=	$row->getFrom( 'avatar' );
	$name				=	'<span class="text-large">' . $row->getFrom( 'profile' ) . '</span>';
	$status				=	$row->getFrom( 'status' );
}

$readTooltip			=	cbTooltip( null, ( $read ? CBTxt::T( 'READ_ON_DATE', 'Read on [date]', array( '[date]' => cbFormatDate( $read ) ) ) : CBTxT::T( 'Unread' ) ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple" aria-label="' . htmlspecialchars( ( $read ? CBTxt::T( 'Read' ) : CBTxT::T( 'Unread' ) ) ) . '"' );

$integrations			=	$_PLUGINS->trigger( 'pm_onBeforeDisplayMessage', array( &$row, &$avatar, &$name, &$menu, $user ) );

if ( ( $row->getInt( 'from_user', 0 ) == $user->getInt( 'user_id', 0 ) ) || ( $row->getInt( 'to_user', 0 ) == $user->getInt( 'user_id', 0 ) ) || Application::MyUser()->isGlobalModerator() ) {
	$menu[]				=	'<li class="pmMessageMenuItem" role="presentation"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this message?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'message', 'func' => 'delete', 'id' => $row->getInt( 'id', 0 ), Application::Session()->getFormTokenName() => Application::Session()->getFormTokenValue() ) ) ) . '\'; })" class="dropdown-item" role="menuitem"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
}

if ( ( $row->getInt( 'to_user', 0 ) === $user->getInt( 'user_id', 0 ) ) && ( ! $row->getBool( 'from_system', false ) ) ) {
	$menu[]				=	'<li class="pmMessagesMenuItem" role="presentation"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to report this message?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'message', 'func' => 'report', 'id' => $row->getInt( 'id', 0 ), Application::Session()->getFormTokenName() => Application::Session()->getFormTokenValue() ) ) ) . '\'; })" class="dropdown-item" role="menuitem"><span class="fa fa-warning"></span> ' . CBTxt::T( 'Report' ) . '</a></li>';
}

if ( $menu ) {
	$menuItems			=	'<ul class="list-unstyled dropdown-menu d-block position-relative m-0 pmMessageMenuItems" role="menu">'
						.		implode( '', $menu )
						.	'</ul>';

	$menuAttr			=	cbTooltip( null, $menuItems, null, 'auto', null, null, null, 'class="text-body cbDropdownMenu pmMessageMenu" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle" data-cbtooltip-open-classes="active" aria-label="' . htmlspecialchars( CBTxt::T( 'Message Options' ) ) . '"' );
}
?>
<div class="pmMessage pmMessageDefault">
	<div class="mb-2 row no-gutters border media pmMessageHeader">
		<div class="p-2 media-left pmMessageHeaderImg">
			<?php echo $avatar; ?>
		</div>
		<div class="p-2 media-body row no-gutters pmMessageHeaderDetails">
			<div class="col">
				<div class="text-wrap pmMessageHeaderUser">
					<span class="ml-n1 pl-1 pt-1 pb-1 pr-1 text-large fa fa-envelope<?php echo ( $read ? '-open text-muted' : ' text-primary' ); ?>"<?php echo $readTooltip; ?>></span>
					<?php echo $name; ?>
					<?php echo $status; ?>
				</div>
				<div class="mt-1 pmMessageHeaderDate">
					<?php echo cbFormatDate( $row->getString( 'date', '' ), true, true ); ?>
				</div>
			</div>
			<?php if ( $menu ) { ?>
			<div class="col-auto mr-n2 pmMessageHeaderMenu">
				<a href="javascript: void(0);" <?php echo trim( $menuAttr ); ?>><span class="pt-1 pb-1 pl-3 pr-3 text-large fa fa-ellipsis-v"></span></a>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php echo implode( '', $integrations ); ?>
	<div class="text-wrap pmMessageContent" tabindex="0">
		<?php
		echo $row->getMessage();

		if ( $this->params->getBool( 'messages_replies', true ) && $row->getReplyTo() ) {
			$reply	=	$row->getReplyTo();
			$depth	=	1;

			require PMSHelper::getTemplate( null, 'replies' );
		}
		?>
	</div>
	<?php echo implode( '', $_PLUGINS->trigger( 'pm_onAfterDisplayMessage', array( $row, $avatar, $name, $menu, $user ) ) ); ?>
	<?php
	if ( PMSHelper::canReply( $user->getInt( 'id', 0 ), $row->getInt( 'from_user', 0 ) )
		 && ( ! $row->getBool( 'from_system', false ) )
		 && ( $user->getInt( 'id', 0 ) == $row->getInt( 'to_user', 0 ) )
		 && ( $row->getInt( 'from_user', 0 ) || ( ( ! $row->getInt( 'from_user', 0 ) ) && $row->getString( 'from_email', '' ) ) )
	) {
		require PMSHelper::getTemplate( null, 'reply' );
	} else {
	?>
	<div class="mt-3 text-right">
		<input type="button" value="<?php echo htmlspecialchars( CBTxt::T( 'Back' ) ); ?>" class="btn btn-sm btn-sm-block btn-secondary pmButton pmButtonBack" onclick="window.location.href = '<?php echo addslashes( htmlspecialchars( $returnUrl ) ); ?>';" />
	</div>
	<?php } ?>
</div>
<?php $_CB_framework->setMenuMeta(); ?>