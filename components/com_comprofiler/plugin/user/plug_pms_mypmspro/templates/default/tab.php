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
 * @var getmypmsproTab $this
 * @var MessageTable   $row
 * @var array          $input
 * @var UserTable      $user
 * @var UserTable      $viewer
 *
 * @var int            $messageEditor
 * @var int            $messageLimit
 */

global $_CB_framework, $_PLUGINS;
?>
<div class="pmMessageQuick pmMessageQuickDefault">
	<form action="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'message', 'func' => 'quick' ) ); ?>" method="post" enctype="multipart/form-data" name="pmMessageQuickForm" class="form-auto m-0 cb_form pmMessageQuickForm cbValidation">
		<input type="hidden" name="to" value="<?php echo $user->getInt( 'id', 0 ); ?>" />
		<?php if ( $input['from_name'] || $input['from_email'] ) { ?>
		<div class="cbft_text cbtt_input form-group row no-gutters cb_form_line cbtwolines">
			<label for="from_name" class="col-form-label col-12"><?php echo CBTxt::T( 'Name' ); ?></label>
			<div class="cb_field col-12">
				<?php echo $input['from_name']; ?>
				<?php echo getFieldIcons( null, 1, null, CBTxt::T( 'Input your name to be sent with your message.' ) ); ?>
			</div>
		</div>
		<div class="cbft_text cbtt_input form-group row no-gutters cb_form_line cbtwolines">
			<label for="from_email" class="col-form-label col-12"><?php echo CBTxt::T( 'Email Address' ); ?></label>
			<div class="cb_field col-12">
				<?php echo $input['from_email']; ?>
				<?php echo getFieldIcons( null, 1, null, CBTxt::T( 'Input your email address to be sent with your message. Note the user you are messaging will see your email address and replies to your message will be emailed to you.' ) ); ?>
			</div>
		</div>
		<hr class="mt-0 mb-3" />
		<?php } ?>
		<?php echo implode( '', $_PLUGINS->trigger( 'pm_onBeforeDisplayMessageQuick', array( &$row, &$input, $user, $viewer ) ) ); ?>
		<div class="cbft_textarea cbtt_textarea form-group row no-gutters cb_form_line cbtwolines pmMessageEditMessage">
			<?php if ( $input['from_name'] || $input['from_email'] || $input['captcha'] ) { ?>
			<label for="message" class="col-form-label col-12"><?php echo CBTxt::T( 'Message' ); ?></label>
			<?php } ?>
			<div class="cb_field col-12">
				<?php echo $input['message']; ?>
				<?php echo $input['message_limit']; ?>
				<?php echo getFieldIcons( null, 0, null, CBTxt::T( 'Input your private message.' ) ); ?>
			</div>
		</div>
		<?php echo implode( '', $_PLUGINS->trigger( 'pm_onAfterDisplayMessageQuick', array( $row, $input, $user, $viewer ) ) ); ?>
		<?php if ( $input['captcha'] ) { ?>
		<div class="cbft_delimiter form-group row no-gutters cb_form_line cbtwolines">
			<label class="col-form-label col-12"><?php echo CBTxt::T( 'Captcha' ); ?></label>
			<div class="cb_field col-12">
				<?php echo $input['captcha']; ?>
			</div>
		</div>
		<?php } ?>
		<div class="row no-gutters">
			<div class="col-12">
				<input type="submit" value="<?php echo htmlspecialchars( CBTxt::T( 'Send Message' ) ); ?>" class="btn btn-sm btn-sm-block btn-primary pmButton pmButtonSubmit" <?php echo cbValidator::getSubmitBtnHtmlAttributes(); ?> />
			</div>
		</div>
		<?php echo Application::Session()->getFormTokenInput(); ?>
	</form>
</div>