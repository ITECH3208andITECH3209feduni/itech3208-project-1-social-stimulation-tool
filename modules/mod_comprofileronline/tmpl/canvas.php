<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

?>
<?php echo modCBOnlineHelper::getPlugins( $params, 'start' ); ?>
<?php if ( $preText ) { ?>
	<div class="pretext">
		<p><?php echo $preText; ?></p>
	</div>
<?php } ?>
<?php echo modCBOnlineHelper::getPlugins( $params, 'beforeUsers' ); ?>
<?php if ( modCBOnlineHelper::getPlugins( $params, 'beforeLinks' ) || $cbUsers || modCBOnlineHelper::getPlugins( $params, 'afterUsers' ) ) { ?>
<div class="row no-gutters cbOnlineUsers <?php echo htmlspecialchars( $templateClass ); ?>">
	<?php echo modCBOnlineHelper::getPlugins( $params, 'beforeLinks' ); ?>
	<?php foreach ( $cbUsers as $cbUser ) { ?>
		<div class="card d-inline-block w-sm-100 mw-100 mr-0 mr-sm-2 mb-2 no-overflow cbCanvasLayout cbCanvasLayoutSm" style="width: 300px;">
			<div class="card-header p-0 position-relative cbCanvasLayoutTop">
				<div class="position-absolute cbCanvasLayoutBackground">
					<?php echo $cbUser->getField( 'canvas', null, 'html', 'none', 'list', 0, true ); ?>
				</div>
			</div>
			<div class="position-relative cbCanvasLayoutBottom">
				<div class="position-absolute cbCanvasLayoutPhoto">
					<?php echo $cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true ); ?>
				</div>
			</div>
			<div class="card-body p-2 position-relative cbCanvasLayoutBody">
				<?php if ( $params->get( 'usertext' ) ) { ?>
					<div class="cbCanvasLayoutContent">
						<?php echo $cbUser->replaceUserVars( $params->get( 'usertext' ) ); ?>
					</div>
				<?php } else { ?>
					<div class="text-truncate cbCanvasLayoutContent">
						<?php echo $cbUser->getField( 'onlinestatus', null, 'html', 'none', 'profile', 0, true, array( 'params' => array( 'displayMode' => 1 ) ) ); ?>
						<?php echo ' ' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true, array( 'params' => array( 'fieldHoverCanvas' => false ) ) ); ?>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php echo modCBOnlineHelper::getPlugins( $params, 'afterUsers' ); ?>
</div>
<?php } ?>
<?php echo modCBOnlineHelper::getPlugins( $params, 'almostEnd' ); ?>
<?php if ( $postText ) { ?>
	<div class="posttext">
		<p><?php echo $postText; ?></p>
	</div>
<?php } ?>
<?php echo modCBOnlineHelper::getPlugins( $params, 'end' ); ?>