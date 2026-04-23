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

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var CBplug_pmsmypmspro $this
 * @var MessageTable       $row
 * @var array              $input
 * @var UserTable          $user
 *
 * @var string             $returnUrl
 */

global $_CB_framework, $_PLUGINS;
?>
<hr class="mt-3 mb-3" role="presentation" />
<form action="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'message', 'func' => 'save' ) ); ?>" method="post" enctype="multipart/form-data" name="pmMessageReplyForm" class="form-auto m-0 cb_form pmMessageReplyForm cbValidation">
	<input type="hidden" name="reply" value="<?php echo $row->getInt( 'id', 0 ); ?>" />
	<?php echo implode( '', $_PLUGINS->trigger( 'pm_onBeforeDisplayMessageReply', array( &$row, &$input, $user ) ) ); ?>
	<div class="cbft_textarea cbtt_textarea form-group cb_form_line cbtwolines pmMessageEditMessage">
		<div class="cb_field">
			<?php echo $input['message']; ?>
			<?php echo $input['message_limit']; ?>
			<?php echo getFieldIcons( null, 0, null, CBTxt::T( 'Input your reply.' ) ); ?>
		</div>
	</div>
	<?php echo implode( '', $_PLUGINS->trigger( 'pm_onAfterDisplayMessageReply', array( $row, $input, $user ) ) ); ?>
	<?php if ( $input['captcha'] ) { ?>
	<div class="cbft_delimiter form-group cb_form_line cbtwolines">
		<div class="cb_field">
			<?php echo $input['captcha']; ?>
		</div>
	</div>
	<?php } ?>
	<div class="row no-gutters">
		<?php if ( ! $row->getInt( 'from_user', 0 ) ) { ?>
		<div class="col-12 mb-2">
			<div class="m-0 p-2 text-small alert alert-info"><?php echo CBTxt::T( 'This message is from an unregistered user. Replying to this message will email them directly. They will also be able to email you in response.' ); ?></div>
		</div>
		<?php } ?>
		<div class="col-12 col-sm-6">
			<input type="submit" value="<?php echo htmlspecialchars( CBTxt::T( 'Send Reply' ) ); ?>" class="btn btn-sm btn-sm-block btn-primary pmButton pmButtonSubmit" <?php echo cbValidator::getSubmitBtnHtmlAttributes(); ?> />
		</div>
		<div class="col-12 col-sm-6 mt-1 mt-sm-0 text-right">
			<input type="button" value="<?php echo htmlspecialchars( CBTxt::T( 'Back' ) ); ?>" class="btn btn-sm btn-sm-block btn-secondary pmButton pmButtonBack" onclick="window.location.href = '<?php echo addslashes( htmlspecialchars( $returnUrl ) ); ?>';" />
		</div>
	</div>
	<?php echo Application::Session()->getFormTokenInput(); ?>
</form>