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
 * @var int                $total
 * @var MessageTable[]     $rows
 * @var array              $input
 * @var UserTable          $user
 * @var cbPageNav          $pageNav
 * @var bool               $searching
 *
 * @var string             $returnUrl
 * @var string             $type
 * @var bool               $allowTypeFilter
 * @var int                $unread
 */

global $_CB_framework, $_PLUGINS;

$pageTitle			=	null;

if ( $type != 'modal' ) {
	$pageTitle		=	( $type == 'sent' ? CBTxt::T( 'Sent Messages' ) : CBTxt::T( 'Received Messages' ) );

	if ( $pageTitle ) {
		$_CB_framework->setPageTitle( $pageTitle );
	}
}
?>
<div class="<?php echo ( $type == 'modal' ? 'd-flex flex-column h-100 mh-100 ' : null ); ?>pmMessages pmMessagesDefault">
	<?php echo implode( '', $_PLUGINS->trigger( 'pm_onBeforeDisplayMessages', array( &$rows, &$input, $type, $user ) ) ); ?>
	<?php if ( $pageTitle ) { ?>
	<div class="mb-3 border-bottom cb-page-header pmMessagesTitle"><h3 class="m-0 p-0 mb-2 cb-page-header-title"><?php echo $pageTitle; ?></h3></div>
	<?php } ?>
	<div class="<?php echo ( $type == 'modal' ? 'm-2 flex-shrink-0' : 'mb-3' ); ?> row no-gutters pkbHeader pmMessagesHeader">
		<?php if ( PMSHelper::canMessage( $user->getInt( 'id', 0 ), false ) || ( ( $type != 'sent' ) && $unread ) ) { ?>
		<div class="col-12 col-sm-6 text-center text-sm-left">
			<?php if ( PMSHelper::canMessage( $user->getInt( 'id', 0 ), false ) ) { ?>
			<button type="button" onclick="window.location.href='<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'messages', 'func' => 'new', 'return' => $returnUrl ) ); ?>';" class="btn<?php echo ( $type == 'modal' ? ' btn-sm' : null ); ?> btn-sm-block btn-success pmButton pmButtonNew"><span class="fa fa-plus-circle"></span> <?php echo CBTxt::T( 'Create New Message' ); ?></button>
			<?php } ?>
			<?php if ( ( $type != 'sent' ) && $unread ) { ?>
			<a href="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'message', 'func' => 'read', 'return' => $returnUrl ) ); ?>" class="align-middle pmButton pmButtonRead"><?php echo CBTxt::T( 'Mark All Read' ); ?></a>
			<?php } ?>
		</div>
		<?php } ?>
		<?php if ( $type == 'modal' ) { ?>
		<div class="col-12 col-sm-6 mt-2 mt-sm-0 text-center text-sm-right">
			<a href="javascript:void(0);" class="align-middle pmButton pmButtonClose cbTooltipClose"><span class="fa fa-times"></span> <span class="d-inline-block d-sm-none"><?php echo CBTxt::T( 'Close' ); ?></span></a>
		</div>
		<?php } elseif ( $input['search'] || $input['type'] ) { ?>
		<div class="col-12 col-sm-6 mt-2 mt-sm-0 text-sm-right" role="search">
			<form action="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'messages', 'func' => ( ! $allowTypeFilter ? $type : null ) ) ); ?>" method="post" name="pmMessagesForm" class="m-0 pmMessagesForm">
				<?php echo $pageNav->getLimitBox( false ); ?>
				<?php if ( $input['search'] ) { ?>
				<div class="input-group">
					<?php echo $input['search']; ?>
					<?php echo $input['type']; ?>
					<div class="input-group-append">
						<button type="submit" class="btn btn-light border" aria-label="<?php echo htmlspecialchars( CBTxt::T( 'Search' ) ); ?>"><span class="fa fa-search"></span></button>
					</div>
				</div>
				<?php } else { ?>
					<?php echo $input['type']; ?>
				<?php } ?>
			</form>
		</div>
		<?php } ?>
	</div>
	<div class="<?php echo ( $type == 'modal' ? 'p-2 flex-grow-1 ' : null ); ?>pmMessagesRows" role="grid">
		<?php
		$i							=	0;

		if ( $rows ) foreach ( $rows as $row ) {
			$i++;

			$menu					=	array();

			if ( $row->getInt( 'from_user', 0 ) == $user->getInt( 'user_id', 0 ) ) {
				$read				=	$row->getRead();

				if ( ! $read ) {
					$menu[]			=	'<li class="pmMessagesMenuItem" role="presentation"><a href="' . $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'message', 'func' => 'edit', 'id' => $row->getInt( 'id', 0 ), 'return' => $returnUrl ) ) . '" class="dropdown-item" role="menuitem"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';
				}

				$readTooltip		=	cbTooltip( null, ( $row->getRead() ? CBTxt::T( 'READ_ON_DATE', 'Read on [date]', array( '[date]' => cbFormatDate( $read ) ) ) : CBTxT::T( 'Unread' ) ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple" aria-label="' . htmlspecialchars( ( $row->getRead() ? CBTxt::T( 'Read' ) : CBTxT::T( 'Unread ' ) ) ) . '"' );

				$avatar				=	$row->getTo( 'avatar' );
				$name				=	$row->getTo( 'name' );
				$status				=	$row->getTo( 'status' );
			} else {
				$read				=	$row->getRead( $user->getInt( 'user_id', 0 ) );
				$readTooltip		=	cbTooltip( null, ( $read ? CBTxt::T( 'Mark Unread' ) : CBTxT::T( 'Mark Read' ) ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple" aria-label="' . htmlspecialchars( ( $read ? CBTxt::T( 'Mark Unread' ) : CBTxT::T( 'Mark Read' ) ) ) . '"' );

				$avatar				=	$row->getFrom( 'avatar' );
				$name				=	$row->getFrom( 'name' );
				$status				=	$row->getFrom( 'status' );
			}

			$_PLUGINS->trigger( 'pm_onDisplayMessage', array( &$row, &$avatar, &$name, &$menu, $user ) );

			if ( ( $row->getInt( 'from_user', 0 ) == $user->getInt( 'user_id', 0 ) ) || ( $row->getInt( 'to_user', 0 ) == $user->getInt( 'user_id', 0 ) ) || Application::MyUser()->isGlobalModerator() ) {
				$menu[]				=	'<li class="pmMessagesMenuItem" role="presentation"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this message?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'message', 'func' => 'delete', 'id' => $row->getInt( 'id', 0 ), Application::Session()->getFormTokenName() => Application::Session()->getFormTokenValue(), 'return' => $returnUrl ) ) ) . '\'; })" class="dropdown-item" role="menuitem"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
			}

			if ( ( $row->getInt( 'to_user', 0 ) === $user->getInt( 'user_id', 0 ) ) && ( ! $row->getBool( 'from_system', false ) ) ) {
				$menu[]				=	'<li class="pmMessagesMenuItem" role="presentation"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to report this message?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'message', 'func' => 'report', 'id' => $row->getInt( 'id', 0 ), Application::Session()->getFormTokenName() => Application::Session()->getFormTokenValue(), 'return' => $returnUrl ) ) ) . '\'; })" class="dropdown-item" role="menuitem"><span class="fa fa-warning"></span> ' . CBTxt::T( 'Report' ) . '</a></li>';
			}

			if ( $menu ) {
				$menuItems			=	'<ul class="list-unstyled dropdown-menu d-block position-relative m-0 pmMessagesMenuItems" role="menu">'
									.		implode( '', $menu )
									.	'</ul>';

				$menuAttr			=	cbTooltip( null, $menuItems, null, 'auto', null, null, null, 'class="text-body cbDropdownMenu pmMessagesMenu" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle" data-cbtooltip-open-classes="active" aria-label="' . htmlspecialchars( CBTxt::T( 'Message Options' ) ) . '"' );
			}
		?>
		<?php if ( ( $i > 1 ) || ( ( $i > 1 ) && ( $i == count( $rows ) ) ) ) { ?>
		<hr class="mt-2 mb-2" role="presentation" />
		<?php } ?>
		<div class="media pmMessagesRow <?php echo ( $read ? 'pmMessagesRowRead' : 'pmMessagesRowUnread' ); ?>" data-pm-url="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'message', 'func' => 'show', 'id' => $row->getInt( 'id', 0 ), 'return' => $returnUrl ) ); ?>" role="row">
			<div class="media-left pmMessagesRowImg" role="gridcell">
				<?php echo $avatar; ?>
			</div>
			<div class="pl-3 media-body pmMessagesRowMsg" role="gridcell">
				<div class="row no-gutters">
					<div class="text-wrap col pmMessagesRowMsgUser">
						<?php if ( $row->getInt( 'from_user', 0 ) == $user->getInt( 'user_id', 0 ) ) { ?>
						<span class="ml-n1 pl-1 pt-1 pb-1 pr-1 text-large fa fa-envelope<?php echo ( $read ? '-open text-muted' : ' text-primary' ); ?>"<?php echo $readTooltip; ?>></span>
						<?php } else { ?>
						<a href="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'message', 'func' => ( $read ? 'unread' : 'read' ), 'id' => $row->getInt( 'id', 0 ), 'return' => $returnUrl ) ); ?>"<?php echo $readTooltip; ?>><span class="ml-n1 pl-1 pt-1 pb-1 pr-1 text-large fa fa-envelope<?php echo ( $read ? '-open text-muted' : ' text-primary' ); ?>"></span></a>
						<?php } ?>
						<a href="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'message', 'func' => 'show', 'id' => $row->getInt( 'id', 0 ), 'return' => $returnUrl ) ); ?>" class="text-large"><?php echo $name; ?></a>
						<?php echo $status; ?>
					</div>
					<?php if ( $menu ) { ?>
					<div class="col-auto pmMessagesRowMsgMenu">
						<span class="d-none d-sm-inline align-text-bottom pmMessagesRowDate"><?php echo cbFormatDate( $row->getString( 'date', '' ), true, false ); ?></span>
						<a href="javascript: void(0);" <?php echo trim( $menuAttr ); ?>><span class="pt-1 pb-1 pl-3 pr-3 text-large fa fa-ellipsis-v"></span></a>
					</div>
					<?php } ?>
				</div>
				<div class="mt-1 row no-gutters">
					<div class="col-sm text-wrap pmMessagesRowMsgIntro" tabindex="0">
						<a href="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'message', 'func' => 'show', 'id' => $row->getInt( 'id', 0 ), 'return' => $returnUrl ) ); ?>" class="text-inherit text-plain"><?php echo $row->getMessage( 200 ); ?></a>
					</div>
					<div class="col-sm-auto d-block d-sm-none pmMessagesRowDate">
						<?php echo cbFormatDate( $row->getString( 'date', '' ), true, false ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php } else { ?>
		<div class="pmMessagesRow pmMessagesRowEmpty" role="row">
		<?php if ( $searching ) { ?>
			<?php echo CBTxt::T( 'No message search results found.' ); ?>
		<?php } else { ?>
			<?php echo CBTxt::T( 'You currently have no messages.' ); ?>
		<?php } ?>
		</div>
		<?php } ?>
	</div>
	<?php if ( $this->params->getBool( 'messages_paging', true ) && ( $pageNav->total > $pageNav->limit ) ) { ?>
	<div class="<?php echo ( $type == 'modal' ? 'm-2' : 'mt-2' ); ?> pmMessagesPaging">
		<?php echo $pageNav->getListLinks(); ?>
	</div>
	<?php } ?>
	<?php echo implode( '', $_PLUGINS->trigger( 'pm_onAfterDisplayMessages', array( $rows, $input, $type, $user ) ) ); ?>
</div>
<?php if ( $type != 'modal' ) { ?>
	<?php $_CB_framework->setMenuMeta(); ?>
<?php } ?>
