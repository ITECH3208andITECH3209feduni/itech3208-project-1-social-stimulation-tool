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
use CB\Plugin\PMS\PMSHelper;
use CB\Plugin\PMS\Table\MessageTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var CBplug_pmsmypmspro $this
 * @var MessageTable       $row
 * @var array              $input
 * @var UserTable          $user
 *
 * @var string             $returnUrl
 * @var bool               $toMultiple
 * @var int                $toLimit
 * @var int                $messageEditor
 * @var int                $messageLimit
 */

global $_CB_framework, $_PLUGINS;

$pageTitle			=	( $row->getInt( 'id', 0 ) ? CBTxt::T( 'Edit Message' ) : CBTxt::T( 'New Message' ) );

if ( $pageTitle ) {
	$_CB_framework->setPageTitle( $pageTitle );
}
?>
<div class="pmMessageEdit pmMessageEditDefault">
	<form action="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'message', 'func' => 'save', 'id' => $row->getInt( 'id', 0 ), 'return' => PMSHelper::getReturn( true ) ) ); ?>" method="post" enctype="multipart/form-data" name="pmMessageEditForm" class="form-auto m-0 cb_form pmMessageEditForm cbValidation">
		<?php if ( $pageTitle ) { ?>
		<div class="mb-3 border-bottom cb-page-header pmMessageEditTitle">
			<h3 class="m-0 p-0 mb-2 cb-page-header-title"><?php echo $pageTitle; ?></h3>
		</div>
		<?php } ?>
		<?php if ( $input['from_name'] || $input['from_email'] ) { ?>
		<div class="cbft_text cbtt_input form-group row no-gutters cb_form_line">
			<label for="from_name" class="col-form-label col-sm-3 pr-sm-2"><?php echo CBTxt::T( 'Name' ); ?></label>
			<div class="cb_field col-sm-9">
				<?php echo $input['from_name']; ?>
				<?php echo getFieldIcons( null, 1, null, CBTxt::T( 'Input your name to be sent with your message.' ) ); ?>
			</div>
		</div>
		<div class="cbft_text cbtt_input form-group row no-gutters cb_form_line">
			<label for="from_email" class="col-form-label col-sm-3 pr-sm-2"><?php echo CBTxt::T( 'Email Address' ); ?></label>
			<div class="cb_field col-sm-9">
				<?php echo $input['from_email']; ?>
				<?php echo getFieldIcons( null, 1, null, CBTxt::T( 'Input your email address to be sent with your message. Note the user you are messaging will see your email address and replies to your message will be emailed to you.' ) ); ?>
			</div>
		</div>
		<hr class="mt-0 mb-3" />
		<?php } ?>
		<?php echo implode( '', $_PLUGINS->trigger( 'pm_onBeforeDisplayMessageEdit', array( &$row, &$input, $user ) ) ); ?>
		<?php if ( $input['to'] ) { ?>
		<div class="cbft_text cbtt_input form-group row no-gutters cb_form_line">
			<label for="to" class="col-form-label col-sm-3 pr-sm-2"><?php echo CBTxt::T( 'To' ); ?></label>
			<div class="cb_field col-sm-9">
				<?php if ( $input['conn'] || $input['global'] ) { ?>
				<div class="input-group d-inline-flex flex-nowrap w-auto mw-100 pmMessageEditToGroup">
					<?php echo $input['to']; ?>
					<div class="input-group-append">
						<?php echo $input['conn']; ?>
						<?php echo $input['global']; ?>
					</div>
				</div>
				<?php } else { ?>
				<?php echo $input['to']; ?>
				<?php } ?>
				<?php echo getFieldIcons( null, 1, null, ( $toMultiple ? ( $toLimit ? CBTxt::T( 'PM_MESSAGE_TO_LIMIT', 'Input the username of the user you want to send a message to. Separate multiple usernames with a comma. You may send this message up to a maximum of [limit] users.', array( '[limit]' => $toLimit ) ) : CBTxt::T( 'Input the username of the user you want to send a message to. Separate multiple usernames with a comma.' ) ) : CBTxt::T( 'Input the username of the user you want to send a message to.' ) ) ); ?>
			</div>
		</div>
		<?php } elseif ( $input['user'] ) { ?>
		<div class="cbft_delimiter form-group row no-gutters cb_form_line">
			<label class="col-form-label col-sm-3 pr-sm-2"><?php echo CBTxt::T( 'To' ); ?></label>
			<div class="cb_field col-sm-9">
				<div class="form-control-plaintext">
					<?php echo $input['user']; ?>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="cbft_textarea cbtt_textarea form-group row no-gutters cb_form_line pmMessageEditMessage">
			<label for="message" class="col-form-label col-sm-3 pr-sm-2"><?php echo CBTxt::T( 'Message' ); ?></label>
			<div class="cb_field col-sm-9">
				<?php echo $input['message']; ?>
				<?php echo $input['message_limit']; ?>
				<?php echo getFieldIcons( null, 0, null, CBTxt::T( 'Input your private message.' ) ); ?>
			</div>
		</div>
		<?php if ( $input['system'] ) { ?>
		<div class="cbft_yesno cbtt_input form-group row no-gutters cb_form_line">
			<label for="system" class="col-form-label col-sm-3 pr-sm-2"><?php echo CBTxt::T( 'System Message' ); ?></label>
			<div class="cb_field col-sm-9">
				<?php echo $input['system']; ?>
				<?php echo getFieldIcons( null, 0, null, CBTxt::T( 'Select if this message should be sent from the system. It will not link back to you personally, but the message will still belong to you.' ) ); ?>
			</div>
		</div>
		<?php } ?>
		<?php echo implode( '', $_PLUGINS->trigger( 'pm_onAfterDisplayMessageEdit', array( $row, $input, $user ) ) ); ?>
		<?php if ( $input['captcha'] ) { ?>
		<div class="cbft_delimiter form-group row no-gutters cb_form_line">
			<label class="col-form-label col-sm-3 pr-sm-2"><?php echo CBTxt::T( 'Captcha' ); ?></label>
			<div class="cb_field col-sm-9">
				<?php echo $input['captcha']; ?>
			</div>
		</div>
		<?php } ?>
		<div class="row no-gutters">
			<div class="offset-sm-3 col-sm-9">
				<input type="submit" value="<?php echo htmlspecialchars( ( $row->getInt( 'id', 0 ) ? CBTxt::T( 'Update Message' ) : CBTxt::T( 'Send Message' ) ) ); ?>" class="btn btn-sm-block btn-primary pmButton pmButtonSubmit" <?php echo cbValidator::getSubmitBtnHtmlAttributes(); ?> />
				<input type="button" value="<?php echo htmlspecialchars( CBTxt::T( 'Cancel' ) ); ?>" class="btn btn-sm-block btn-secondary pmButton pmButtonCancel" onclick="cbjQuery.cbconfirm( '<?php echo addslashes( CBTxt::T( 'Are you sure you want to cancel? All unsaved data will be lost!' ) ); ?>' ).done( function() { window.location.href = '<?php echo addslashes( htmlspecialchars( $returnUrl ) ); ?>'; })" />
			</div>
		</div>
		<?php echo Application::Session()->getFormTokenInput(); ?>
	</form>
</div>
<?php $_CB_framework->setMenuMeta(); ?>