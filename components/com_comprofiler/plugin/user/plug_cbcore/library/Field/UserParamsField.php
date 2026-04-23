<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Core\Field;

use ActionlogsModelActionlog;
use CB\Database\Table\FieldTable;
use CB\Database\Table\UserTable;
use cbFieldHandler;
use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use Exception;
use JAuthenticationHelper;
use JFactory;
use JForm;
use JFormField;
use JHtml;
use JModelLegacy;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Actionlogs\Administrator\Model\ActionlogModel;
use Joomla\Component\Users\Administrator\Helper\Mfa;
use Joomla\Component\Users\Administrator\Model\UserModel;
use Joomla\Component\Users\Site\Model\ProfileModel;
use Joomla\Component\Users\Site\Model\RegistrationModel;
use JPluginHelper;
use JRegistry;
use JText;
use JUser;
use moscomprofilerHTML;
use stdClass;
use UsersModelProfile;
use UsersModelUser;

\defined( 'CBLIB' ) or die();

class UserParamsField extends cbFieldHandler
{
	/**
	 * Initializer:
	 * Puts the default value of $field into $user (for registration or new user in backend)
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $reason      'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 */
	public function initFieldToDefault( &$field, &$user, $reason )
	{
	}

	/**
	 * Returns a USERPARAMS field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output      'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $formatting  'table', 'td', 'span', 'div', 'none'
	 * @param  string      $reason      'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getFieldRow( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		global $_CB_framework;

		// Load com_users language files so the pseudo fields will translate:
		JFactory::getLanguage()->load( 'com_users' );

		$clientId									=	Application::Cms()->getClientId();
		$userParams									=	$this->_getUserParams( $user );
		$pseudoFields								=	array();

		if ( $userParams && ( $clientId || Application::Config()->get( 'frontend_userparams', true, GetterInterface::BOOLEAN ) || isset( $userParams['jform_privacyconsent_privacy'] ) || isset( $userParams['privacyconsent_privacy'] ) || isset( $userParams['jform_terms_terms'] ) || isset( $userParams['terms_terms'] ) ) ) {
			if ( $clientId ) {
				$excludeParams						=	array();
			} else {
				$excludeParams						=	explode( '|*|', $field->params->getString( 'hide_userparams', '' ) );
			}

			// Loop through user params and convert them to psuedo fields:
			foreach ( $userParams as $paramId => $userParam ) {
				$formParamId						=	( $userParam->control ? str_replace( $userParam->control . '_', '', $paramId ) : $paramId );

				// Privacy Consent and Terms are output as params fields through same API so we need to allow them to bypass frontend_userparams setting as it can't block them:
				if ( ( ! Application::Config()->get( 'frontend_userparams', true, GetterInterface::BOOLEAN ) ) && ( ! in_array( $formParamId, array( 'privacyconsent_privacy', 'terms_terms' ) ) )
					 || ( $excludeParams && in_array( $formParamId, $excludeParams ) )
				) {
					continue;
				}

				$paramName							=	$userParam->name;

				if ( ! $paramName ) {
					continue;
				}

				$paramField							=	new FieldTable( $field->getDbo() );
				$paramField->fieldid				=	$paramId;
				$paramField->name					=	$paramName;
				$paramField->type					=	'param';
				$paramField->title					=	JText::_( $userParam->title );
				$paramField->description			=	JText::_( $userParam->description );
				$paramField->required				=	( $userParam->required !== null ? ( $userParam->required ? 1 : 0 ) : null );
				$paramField->_html					=	$userParam->input;

				// Check if the label has a modal and if it does convert it to a cbtooltip modal:
				if ( preg_match( '/<a href="([^"]+)" class="modal"/i', $userParam->label, $matches ) ) {
					$modalWidth						=	800;
					$modalHeight					=	500;

					if ( preg_match( '/size: {x:(\d+), y:(\d+)}}/i', $userParam->label, $sizes ) ) {
						$modalWidth					=	(int) $sizes[1];
						$modalHeight				=	(int) $sizes[2];
					}

					$modal							=	'<iframe class="d-block m-0 p-0 border-0 cbTermsModalURL" height="100%" width="100%" src="' . $matches[1] . '"></iframe>'; // URL already escaped

					$paramField->title				=	cbTooltip( null, $modal, null, array( $modalWidth, $modalHeight ), null, $paramField->title, 'javascript:void(0);', 'class="cbTermsLink" data-hascbtooltip="true" data-cbtooltip-modal="true"' );
				}

				$description						=	null;

				if ( $paramField->description ) {
					if ( strpos( $paramField->name, 'webauthn' ) !== false ) {
						$paramField->_html			.=	$paramField->description;
						$paramField->description	=	null;
					} else {
						$description				=	cbTooltip( null, $paramField->description, $paramField->title, null, null, null, null, 'data-hascbtooltip="true"' );
					}
				}

				// Ensure text, select, and textarea fields are CB styled:
				if ( ! preg_match( '/<(?:input type="text"|select|textarea)[^>]*class[^>]*>/i', $paramField->_html ) ) {
					$paramField->_html				=	preg_replace( '/<(input type="text"|select|textarea)/i', '<$1 class="form-control"' . $description, $paramField->_html );
				} elseif ( checkJversion( '4.0+' ) ) {
					$paramField->_html				=	str_replace( 'form-select', 'form-control', $paramField->_html );
				}

				// Remove the helpsite refresh button as it doesn't do anything here:
				if ( $paramId == 'params_helpsite' ) {
					$paramField->_html				=	preg_replace( '%<button.*>.*</button>%si', '', $paramField->_html );
				}

				$pseudoFields[]						=	$paramField;
			}
		}

		// Two factor authentication params (only available to already registered users):
		if ( $user->get( 'id', 0, GetterInterface::INT ) ) {
			$twoFactorMethods					=	JAuthenticationHelper::getTwoFactorMethods();

			if ( count( $twoFactorMethods ) > 1 ) {
				$js								=	"$( '.JoomlaTwoFactorMethod' ).on( 'change', function() {"
												.		"$( this ).nextAll( 'div' ).hide();"
												.		"$( '#twofactor_' + $( this ).val() ).show();"
												.	"});";

				$_CB_framework->outputCbJQuery( $js );

				if ( checkJversion( '4.0+' ) ) {
					/** @var UserModel $model */
					$model						=	Factory::getApplication()->bootComponent( 'com_users' )->getMVCFactory()->createModel( 'User', 'Administrator', array( 'ignore_request' => true ) );
				} else {
					require_once ( $_CB_framework->getCfg( 'absolute_path' ) . '/components/com_users/models/profile.php' );

					$model						=	new UsersModelProfile();
				}

				Factory::getApplication()->getLanguage()->load( 'com_users', JPATH_ADMINISTRATOR );

				$otpConfig						=	$model->getOtpConfig( $user->id );
				$twoFactorForms					=	$model->getTwofactorform( $user->id );
				$twoFactor						=	moscomprofilerHTML::selectList( $twoFactorMethods, 'jform[twofactor][method]', 'class="form-control JoomlaTwoFactorMethod"', 'value', 'text', (string) $otpConfig->method, false, false, false, false );

				foreach ( $twoFactorForms as $twoFactorForm ) {
					$twoFactor					.=	'<div id="twofactor_' . htmlspecialchars( $twoFactorForm['method'] ) . '" style="' . ( $twoFactorForm['method'] == $otpConfig->method ? 'display: block;' : 'display: none;' ) . ' margin-top: 10px;">'
												.		str_replace( 'input-small', 'form-control', $twoFactorForm['form'] )
												.	'</div>';
				}

				$label							=	JText::_( 'COM_USERS_PROFILE_TWOFACTOR_LABEL' );

				if ( $label === 'COM_USERS_PROFILE_TWOFACTOR_LABEL' ) {
					$label						=	CBTxt::T( 'Authentication Method' );
				}

				$desc							=	JText::_( 'COM_USERS_PROFILE_TWOFACTOR_DESC' );

				if ( $desc === 'COM_USERS_PROFILE_TWOFACTOR_DESC' ) {
					$desc						=	CBTxt::T( 'Select the two factor authentication method you want to use.' );
				}

				$paramField						=	new FieldTable( $field->getDbo() );
				$paramField->fieldid			=	'twofactor';
				$paramField->name				=	null;
				$paramField->title				=	$label;
				$paramField->description		=	$desc;
				$paramField->type				=	'param';
				$paramField->_html				=	$twoFactor;

				$pseudoFields[]					=	$paramField;
			}

			if ( checkJversion( '4.2+' ) ) {
				Factory::getApplication()->getLanguage()->load( 'com_users' );
				Factory::getApplication()->getDocument()->getWebAssetManager()->getRegistry()->addExtensionRegistryFile( 'com_users' );

				$cmsUser						=	Application::Cms()->getCmsUser( $user->getInt( 'id', 0 ) )->asCmsUser();

				try {
					$multiFactor				=	( Mfa::canShowConfigurationInterface( $cmsUser ) ? Mfa::getConfigurationInterface( $cmsUser ) : '' );
				} catch ( Exception $e ) {
					$multiFactor				=	'';
				}

				if ( $multiFactor ) {
					$label						=	JText::_( 'COM_USERS_PROFILE_MULTIFACTOR_AUTH' );

					if ( $label === 'COM_USERS_PROFILE_MULTIFACTOR_AUTH' ) {
						$label					=	CBTxt::T( 'Multi-factor Authentication' );
					}

					$paramField					=	new FieldTable( $field->getDbo() );
					$paramField->fieldid		=	'multifactor';
					$paramField->name			=	null;
					$paramField->title			=	$label;
					$paramField->description	=	'';
					$paramField->type			=	'param';
					$paramField->_html			=	$multiFactor;

					$pseudoFields[]				=	$paramField;
				}
			}
		}

		if ( $clientId ) {
			$i_am_super_admin				=	Application::MyUser()->isSuperAdmin();
			$canBlockUser					=	Application::MyUser()->isAuthorizedToPerformActionOnAsset( 'core.edit.state', 'com_users' );
			$canEmailEvents					=	( ( $user->id == 0 ) && $canBlockUser )
												|| Application::User( (int) $user->id )->isAuthorizedToPerformActionOnAsset( 'core.edit.state', 'com_users' )
												|| Application::User( (int) $user->id )->canViewAccessLevel( Application::Config()->get( 'moderator_viewaccesslevel', 3, \CBLib\Registry\GetterInterface::INT ) );

			$lists							=	array();

			if ( $canBlockUser ) {

				// ensure user can't add group higher than themselves
				$gtree						=	$_CB_framework->acl->get_groups_below_me();

				if ( ( ! $i_am_super_admin )
					&& $user->id
					&& Application::User( (int) $user->id )->isAuthorizedToPerformActionOnAsset( 'core.manage', 'com_users' )
					&& ( Application::User( (int) $user->id )->isAuthorizedToPerformActionOnAsset( 'core.edit', 'com_users' )
						 ||  Application::User( (int) $user->id )->isAuthorizedToPerformActionOnAsset( 'core.edit.state', 'com_users' )
					   )
				)
				{
					$disabled				=	' disabled="disabled"';
				} else {
					$disabled				=	'';
				}
				if ( $user->id ) {
					$gids					=	cbToArrayOfInt( Application::User( (int) $user->id )->getAuthorisedGroups( false ) );
				} else {
					$gids					=	(int) $_CB_framework->getCfg( 'new_usertype' );
				}
				$lists['gid']				=	moscomprofilerHTML::selectList( $gtree, 'gid[]', 'class="form-control" size="11" multiple="multiple"' . $disabled, 'value', 'text', $gids, 2, false, null, false );

				// build the html select lists:
				$lists['block']					=	moscomprofilerHTML::yesnoSelectList( 'block', 'class="form-control"', (int) $user->block, CBTxt::T( 'No' ), CBTxt::T( 'Yes' ) );

				$list_banned					=	array();
				$list_banned[]					=	moscomprofilerHTML::makeOption( '0', CBTxt::T( 'No' ) );
				$list_banned[]					=	moscomprofilerHTML::makeOption( '1', CBTxt::T( 'Yes' ) );
				$list_banned[]					=	moscomprofilerHTML::makeOption( '2', CBTxt::T( 'Pending' ) );
				$lists['banned']				=	moscomprofilerHTML::selectList( $list_banned, 'banned', 'class="form-control"', 'value', 'text', (int) $user->banned, 2, false, null, false );

				$list_approved					=	array();
				$list_approved[]				=	moscomprofilerHTML::makeOption( '0', CBTxt::T( 'No' ) );
				$list_approved[]				=	moscomprofilerHTML::makeOption( '1', CBTxt::T( 'Yes' ) );
				$list_approved[]				=	moscomprofilerHTML::makeOption( '2', CBTxt::T( 'Rejected' ) );
				$lists['approved']				=	moscomprofilerHTML::selectList( $list_approved, 'approved', 'class="form-control"', 'value', 'text', (int) $user->approved, 2, false, null, false );

				$lists['confirmed']				=	moscomprofilerHTML::yesnoSelectList( 'confirmed', 'class="form-control"', (int) $user->confirmed );

				$lists['sendEmail']				=	moscomprofilerHTML::yesnoSelectList( 'sendEmail', 'class="form-control"', (int) $user->sendEmail );

				$lists['requireReset']			=	moscomprofilerHTML::yesnoSelectList( 'requireReset', 'class="form-control"', (int) $user->requireReset );

				// build the pseudo field objects:
				$paramField					=	new FieldTable( $field->getDbo() );
				$paramField->title			=	'Group';								// For translation parser:  CBTxt::T( 'Group' );
				$paramField->_html			=	$lists['gid'];
				$paramField->description	=	'';
				$paramField->name			=	'gid';
				$paramField->required		=	1;
				$pseudoFields[]				=	$paramField;

				$paramField					=	new FieldTable( $field->getDbo() );
				$paramField->title			=	'Enabled';							// For translation parser:  CBTxt::T( 'Enabled' );
				$paramField->_html			=	$lists['block'];
				$paramField->description	=	'';
				$paramField->name			=	'block';
				$pseudoFields[]				=	$paramField;

				$paramField					=	new FieldTable( $field->getDbo() );
				$paramField->title			=	'Approved';								// For translation parser:  CBTxt::T( 'Approved' );
				$paramField->_html			=	$lists['approved'];
				$paramField->description	=	'';
				$paramField->name			=	'approved';
				$pseudoFields[]				=	$paramField;

				$paramField					=	new FieldTable( $field->getDbo() );
				$paramField->title			=	'Confirmed';								// For translation parser:  CBTxt::T( 'Confirmed' );
				$paramField->_html			=	$lists['confirmed'];
				$paramField->description	=	'';
				$paramField->name			=	'confirmed';
				$pseudoFields[]				=	$paramField;

				$paramField					=	new FieldTable( $field->getDbo() );
				$paramField->title			=	'Banned';								// For translation parser:  CBTxt::T( 'Banned' );
				$paramField->_html			=	$lists['banned'];
				$paramField->description	=	'';
				$paramField->name			=	'banned';
				$pseudoFields[]				=	$paramField;

				$paramField						=	new FieldTable( $field->getDbo() );
				$paramField->title				=	'Reset Password';								// For translation parser:  CBTxt::T( 'Reset Password' );
				$paramField->_html				=	$lists['requireReset'];
				$paramField->description		=	'';
				$paramField->name				=	'requireReset';
				$pseudoFields[]					=	$paramField;

				$paramField						=	new FieldTable( $field->getDbo() );
				$paramField->title				=	'Receive Moderator Emails';				// For translation parser:  CBTxt::T( 'Receive Moderator Emails' );
				if ($canEmailEvents || $user->sendEmail) {
					$paramField->_html			=	$lists['sendEmail'];
				} else {
					$paramField->_html			=	CBTxt::T( 'No (User\'s group-level doesn\'t allow this)' )
												.	'<input type="hidden" name="sendEmail" value="0" />';
				}
				$paramField->description		=	'';
				$paramField->name				=	'sendEmail';
				$pseudoFields[]					=	$paramField;
			}

			if( $user->id) {
				$paramField					=	new FieldTable( $field->getDbo() );
				$paramField->title			=	'Register Date';								// For translation parser:  CBTxt::T( 'Register Date' );
				$paramField->_html			=	cbFormatDate( $user->registerDate );
				$paramField->description	=	'';
				$paramField->name			=	'registerDate';
				$pseudoFields[]				=	$paramField;

				$paramField					=	new FieldTable( $field->getDbo() );
				$paramField->title			=	'Last Visit Date';								// For translation parser:  CBTxt::T( 'Last Visit Date' );
				$paramField->_html			=	cbFormatDate( $user->lastvisitDate );
				$paramField->description	=	'';
				$paramField->name			=	'lastvisitDate';
				$pseudoFields[]				=	$paramField;

				$paramField					=	new FieldTable( $field->getDbo() );
				$paramField->title			=	'Last Reset Time';								// For translation parser:  CBTxt::T( 'Last Reset Time' );
				$paramField->_html			=	cbFormatDate( $user->lastResetTime );
				$paramField->description	=	'';
				$paramField->name			=	'lastResetTime';
				$pseudoFields[]				=	$paramField;

				$paramField					=	new FieldTable( $field->getDbo() );
				$paramField->title			=	'Password Reset Count';							// For translation parser:  CBTxt::T( 'Password Reset Count' );
				$paramField->_html			=	(int) $user->resetCount;
				$paramField->description	=	'';
				$paramField->name			=	'resetCount';
				$pseudoFields[]				=	$paramField;
			}
		}

		switch ( $output ) {
			case 'htmledit':
				$return						=	null;

				foreach ( $pseudoFields as $paramField ) {
					$paramField->required	=	( $paramField->required === null ? 0 : $paramField->required ); // if the pseudo field doesn't explicitly have required state then it can't be set required
					$paramField->profile	=	0; // pseudo fields have no display output
					$paramField->params		=	$field->params; // prevents api errors accessing non-registry params object
					$paramField->type		=	'';

					$return					.=	parent::getFieldRow( $paramField, $user, $output, $formatting, $reason, $list_compare_types );
				}

				return $return;
				break;
			default:
				return null;
				break;
		}
	}

	/**
	 * Accessor:
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		switch ( $output ) {
			case 'htmledit':
				return $field->_html . $this->_fieldIconsHtml( $field, $user, $output, $reason, 'input', 'text', $field->_html, '', null, true, $this->_isRequired( $field, $user, $reason ) && ! $this->_isReadOnly( $field, $user, $reason ) );
				break;

			default:
				return null;
				break;
		}
	}

	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	public function prepareFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		global $_CB_framework;

		// Nb. frontend registration setting of usertype, gid, block, sendEmail, confirmed, approved
		// are handled in UserTable::bindSafely() so they are available to other plugins.

		$clientId							=	Application::Cms()->getClientId();
		$userParams							=	$this->_getUserParams( $user );
		$userId								=	$user->get( 'id', null, GetterInterface::INT );

		// this is (for now) handled in the core of CB... except params and block/email/approved/confirmed:

		if ( $_CB_framework->getUi() == 2 ) {
			$canBlockUser					=	Application::MyUser()->isAuthorizedToPerformActionOnAsset( 'core.edit.state', 'com_users' );
			if ( $canBlockUser ) {
				$user->gids					=	cbGetParam( $postdata, 'gid', array( 0 ) );

				if ( isset( $postdata['block'] ) ) {
					$user->block			=	cbGetParam( $postdata, 'block', 0 );
				}
				if ( isset( $postdata['approved'] ) ) {
					$user->approved			=	cbGetParam( $postdata, 'approved', 0 );
				}
				if ( isset( $postdata['confirmed'] ) ) {
					$user->confirmed		=	cbGetParam( $postdata, 'confirmed', 0 );
				}
				if ( isset( $postdata['banned'] ) ) {
					$banned					=	cbGetParam( $postdata, 'banned', 0 );

					if ( $banned != $user->banned ) {
						if ( $banned == 1 ) {
							$user->bannedby			=	(int) $_CB_framework->myId();
							$user->banneddate		=	$_CB_framework->getUTCDate();
						} elseif ( $banned == 0 ) {
							$user->unbannedby		=	(int) $_CB_framework->myId();
							$user->unbanneddate		=	$_CB_framework->getUTCDate();
						}
					}

					$user->banned			=	$banned;
				}
				if ( isset( $postdata['requireReset'] ) ) {
					$user->requireReset		=	cbGetParam( $postdata, 'requireReset', 0 );
				}
				if ( isset( $postdata['sendEmail'] ) ) {
					$user->sendEmail		=	cbGetParam( $postdata, 'sendEmail', 0 );
				}
			}
		}

		// User params storage:
		if ( $userParams && ( $clientId || Application::Config()->get( 'frontend_userparams', true, GetterInterface::BOOLEAN ) ) ) {
			// Load existing user params to avoid wiping out any 3rd party custom params on store:
			$params								=	new Registry( $user->get( 'params', null, GetterInterface::RAW ) );

			if ( $clientId ) {
				$excludeParams					=	array();
			} else {
				$excludeParams					=	explode( '|*|', $field->params->get( 'hide_userparams', '', GetterInterface::STRING ) );
			}

			// Loop through user params and prepare them for storage to ensure we only store what is allowed:
			foreach ( $userParams as $paramId => $userParam ) {
				$formParamId					=	( $userParam->control ? str_replace( $userParam->control . '_', '', $paramId ) : $paramId );

				// Privacy Consent and Terms have separate storage behavior so skip them:
				if ( in_array( $formParamId, array( 'privacyconsent_privacy', 'terms_terms' ) ) || ( $excludeParams && in_array( $formParamId, $excludeParams ) ) ) {
					continue;
				}

				if ( ! $userParam->name ) {
					continue;
				}

				if ( $userParam->control ) {
					$inputParamId				=	str_replace( [ $userParam->control . '_', 'params_' ], [ $userParam->control . '.', 'params.' ], $paramId );
				} else {
					$inputParamId				=	str_replace( 'params_', 'params.', $paramId );
				}

				// Just validate all user params to strings to at least ensure they're safe:
				$params->set( str_replace( 'params_', '', $formParamId ), $this->input( $inputParamId, null, GetterInterface::STRING ) );
			}

			$value								=	$params->asJson();

			if ( ( (string) $user->get( 'params', null, GetterInterface::RAW ) ) !== (string) $value ) {
				$this->_logFieldUpdate( $field, $user, $reason, $user->get( 'params', null, GetterInterface::RAW ), $value );
			}

			$user->set( 'params', $value );
		}

		// Two factor authentication params (only available to already registered users):
		if ( $userId ) {
			$twoFactorMethods					=	JAuthenticationHelper::getTwoFactorMethods();

			if ( count( $twoFactorMethods ) > 1 ) {
				if ( checkJversion( '4.0+' ) ) {
					/** @var UserModel $model */
					$model						=	Factory::getApplication()->bootComponent( 'com_users' )->getMVCFactory()->createModel( 'User', 'Administrator', array( 'ignore_request' => true ) );
				} else {
					require_once ( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_users/models/user.php' );

					$model						=	new UsersModelUser();
				}

				$otpConfig						=	$model->getOtpConfig( $userId );
				$twoFactorMethod				=	$this->input( 'jform.twofactor.method', 'none', GetterInterface::COMMAND );
				$twoFactorSaved					=	false;

				if ( $twoFactorMethod != 'none' ) {
					if ( cbGetParam( $postdata, 'jform[twofactor][' . $twoFactorMethod . '][securitycode]' ) !== null ) {
						PluginHelper::importPlugin( 'twofactorauth' );

						$otpConfigReplies		=	Factory::getApplication()->triggerEvent( 'onUserTwofactorApplyConfiguration', array( $twoFactorMethod ) );

						// Look for a valid reply
						foreach ( $otpConfigReplies as $reply ) {
							if ( ( ! is_object( $reply ) ) || empty( $reply->method ) || ( $reply->method != $twoFactorMethod ) ) {
								continue;
							}

							$otpConfig->method	=	$reply->method;
							$otpConfig->config	=	$reply->config;

							break;
						}

						// Save OTP configuration.
						$model->setOtpConfig( $userId, $otpConfig );

						// Generate one time emergency passwords if required (depleted or not set)
						if ( empty( $otpConfig->otep ) ) {
							$model->generateOteps( $userId );
						}

						$twoFactorSaved			=	true;
					}
				} else {
					$otpConfig->method			=	'none';
					$otpConfig->config			=	array();

					$model->setOtpConfig( $userId, $otpConfig );

					$twoFactorSaved				=	true;
				}

				if ( $twoFactorSaved ) {
					$jUser						=	JUser::getInstance();

					// Reload the user record with the updated OTP configuration
					$jUser->load( $userId );

					$user->otpKey				=	$jUser->get( 'otpKey' );
					$user->otep					=	$jUser->get( 'otep' );
				}
			}
		}

		// Privacy Policy and Terms validation (frontend registration only; they do not output in CB profile edit and can not be consented by admins):
		if ( ( ! Application::Cms()->getClientId() ) && ( $reason == 'register' ) && ( ! $userId ) ) {
			// Validate privacy:
			if ( JPluginHelper::isEnabled( 'system', 'privacyconsent' ) && ( ! $this->input( ( checkJversion( '4.0+' ) ? 'jform.' : '' ) . 'privacyconsent.privacy', 0, GetterInterface::INT ) ) ) {
				$this->_setValidationError( $field, $user, $reason, JText::_( 'PLG_SYSTEM_PRIVACYCONSENT_FIELD_ERROR' ) );
			}

			// Terms is a user plugin so be sure user plugins have been imported before attempting to check it:
			JPluginHelper::importPlugin( 'user' );

			// Validate terms:
			if ( JPluginHelper::isEnabled( 'user', 'terms' ) && ( ! $this->input( ( checkJversion( '4.0+' ) ? 'jform.' : '' ) . 'terms.terms', 0, GetterInterface::INT ) ) ) {
				$this->_setValidationError( $field, $user, $reason, JText::_( 'PLG_USER_TERMS_FIELD_ERROR' ) );
			}
		}
	}

	/**
	 * Mutator:
	 * Prepares field data commit
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save user edit, 'register' for save registration
	 */
	public function commitFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		global $_CB_database;

		$userId				=	$user->get( 'id', 0, GetterInterface::INT );

		// Privacy Policy and Terms storage (frontend registration only; they do not output in CB profile edit and can not be consented by admins):
		if ( ( ! Application::Cms()->getClientId() ) && ( $reason == 'register' ) && $userId ) {
			// Joomla has this view scoped in its onUserAfterSave usage so we have to handle the storage instead
			if ( JPluginHelper::isEnabled( 'system', 'privacyconsent' ) && $this->input( ( checkJversion( '4.0+' ) ? 'jform.' : '' ) . 'privacyconsent.privacy', 0, GetterInterface::INT ) ) {
				// Get the user's IP address
				$ip			=	$this->getInput()->getRequestIP();

				// Get the user agent string
				$userAgent	=	$this->getInput()->getNamespaceRegistry( 'server' )->get( 'HTTP_USER_AGENT', null, GetterInterface::STRING );

				// Create the user note
				$query		=	"INSERT INTO " . $_CB_database->NameQuote( '#__privacy_consents' )
							.	"\n ("
							.		$_CB_database->NameQuote( 'user_id' )
							.		", " . $_CB_database->NameQuote( 'subject' )
							.		", " . $_CB_database->NameQuote( 'body' )
							.		", " . $_CB_database->NameQuote( 'created' )
							.	")"
							.	"\n VALUES ("
							.		$userId
							.		", " . $_CB_database->Quote( 'PLG_SYSTEM_PRIVACYCONSENT_SUBJECT' )
							.		", " . $_CB_database->Quote( JText::sprintf( 'PLG_SYSTEM_PRIVACYCONSENT_BODY', $ip, $userAgent ) )
							.		", " . $_CB_database->Quote( Application::Database()->getUtcDateTime() )
							.	")";
				$_CB_database->setQuery( $query );
				$_CB_database->query();

				$message	=	array(	'action'		=>	'consent',
										'id'			=>	$userId,
										'title'			=>	$user->get( 'name', null, GetterInterface::STRING ),
										'itemlink'		=>	'index.php?option=com_users&task=user.edit&id=' . $userId,
										'userid'		=>	$userId,
										'username'		=>	$user->get( 'username', null, GetterInterface::STRING ),
										'accountlink'	=>	'index.php?option=com_users&task=user.edit&id=' . $userId
									);

				JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_actionlogs/models', 'ActionlogsModel' );

				/* @var ActionlogModel|ActionlogsModelActionlog $model */
				$model		=	JModelLegacy::getInstance( 'Actionlog', 'ActionlogsModel' );

				$model->addLog( array( $message ), 'PLG_SYSTEM_PRIVACYCONSENT_CONSENT', 'plg_system_privacyconsent', $userId );
			}

			// Terms is a user plugin so be sure user plugins have been imported before attempting to check it:
			JPluginHelper::importPlugin( 'user' );

			// Only necessary on Joomla 3 as Joomla 4 plugin will handle storage and logging itself in its onUserAfterSave usage
			if ( checkJversion( '<4.0' ) && JPluginHelper::isEnabled( 'user', 'terms' ) && $this->input( 'terms.terms', 0, GetterInterface::INT ) ) {
				$message	=	array(	'action'		=>	'consent',
										'id'			=>	$userId,
										'title'			=>	$user->get( 'name', null, GetterInterface::STRING ),
										'itemlink'		=>	'index.php?option=com_users&task=user.edit&id=' . $userId,
										'userid'		=>	$userId,
										'username'		=>	$user->get( 'username', null, GetterInterface::STRING ),
										'accountlink'	=>	'index.php?option=com_users&task=user.edit&id=' . $userId
									);

				JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_actionlogs/models', 'ActionlogsModel' );

				/* @var ActionlogModel|ActionlogsModelActionlog $model */
				$model		=	JModelLegacy::getInstance( 'Actionlog', 'ActionlogsModel' );

				$model->addLog( array( $message ), 'PLG_USER_TERMS_LOGGING_CONSENT_TO_TERMS', 'plg_user_terms', $userId );
			}
		}
	}

	/**
	 * Retrieve joomla standard user parameters so that they can be displayed in user edit mode.
	 *
	 * @param  UserTable $user the user being displayed
	 * @return object[]        of user parameter attributes (title,value)
	 */
	private function _getUserParams( $user )
	{
		global $_CB_framework;

		static $cache					=	array();

		$userId							=	$user->get( 'id', 0, GetterInterface::INT );
		$clientId						=	Application::Cms()->getClientId();

		if ( ! isset( $cache[$userId][$clientId] ) ) {
			$jUser						=	$_CB_framework->_getCmsUserObject( $userId );

			// Include jQuery
			JHtml::_( 'jquery.framework' );

			$params						=	new JRegistry( $jUser->params );

			$data						=	new stdClass();
			$data->id					=	$userId;
			$data->params				=	$params->toArray();

			$fields						=	array();

			if ( Application::Cms()->getClientId() ) {
				// Backend user edit settings tab:
				// Joomla 4:
				$path					=	JPATH_ADMINISTRATOR . '/components/com_users/forms';
				if ( ! file_exists( $path ) ) {
					// Joomla 3:
					$path				=	JPATH_ADMINISTRATOR . '/components/com_users/models/forms';
				}
				JForm::addFormPath( $path );

				JPluginHelper::importPlugin( 'user' );

				$form					=	JForm::getInstance( 'com_users.params', 'user', array( 'load_data' => true ) );

				$form->bind( $data );

				$settings				=	$form->getFieldset( 'settings' );

				if ( count( $settings ) ) {
					$fields				=	$settings;
				}
			} else {
				// Frontend profile edit params:
				if ( checkJversion( '4.0+' ) ) {
					// Joomla 4:
					JForm::addFormPath( JPATH_ROOT . '/components/com_users/forms' );

					if ( ! $userId ) {
						/** @var RegistrationModel $model */
						$model				=	Factory::getApplication()->bootComponent( 'com_users' )->getMVCFactory()->createModel( 'Registration', 'Site', [ 'ignore_request' => true ] );
					} else {
						/** @var ProfileModel $model */
						$model				=	Factory::getApplication()->bootComponent( 'com_users' )->getMVCFactory()->createModel( 'Profile', 'Site', [ 'ignore_request' => true ] );

						$model->setState( 'user.id', $userId );
					}

					$form					=	$model->getForm();
				} else {
					// Joomla 3:
					JForm::addFormPath( JPATH_ROOT . '/components/com_users/models/forms' );

					JPluginHelper::importPlugin( 'user' );

					if ( ! $userId ) {
						$context			=	'com_users.registration';
					} else {
						$context			=	'com_users.profile';
					}

					$form					=	JForm::getInstance( $context, 'frontend' );

					if ( Application::MyUser()->isAuthorizedToPerformActionOnAsset( 'core.login.admin', 'root' ) ) {
						$form->loadFile( 'frontend_admin', false );
					}

					JFactory::getApplication()->triggerEvent( 'onContentPrepareForm', array( $form, $data ) );
					JFactory::getApplication()->triggerEvent( 'onContentPrepareData', array( $context, $data ) );

					$form->bind( $data );
				}

				foreach ( $form->getFieldsets() as $group => $fieldset ) {
					// For now we need to strictly only allow certain groups as this API also gives custom Joomla fields:
					if ( ! in_array( $group, array( 'params', 'privacyconsent', 'terms', 'webauthn' ) ) ) {
						continue;
					}

					$fieldsetFields		=	$form->getFieldset( $group );

					if ( ! count( $fieldsetFields ) ) {
						continue;
					}

					$fields				+= $fieldsetFields;
				}
			}

			$result						=	array();

			/** @var JFormField $field */
			foreach ( $fields as $fieldId => $field ) {
				ob_start();
				echo $field->label;
				$fieldLabel				=	ob_get_contents();
				ob_end_clean();

				ob_start();
				echo $field->input;
				$fieldInput				=	ob_get_contents();
				ob_end_clean();

				$cmsField				=	new stdClass();
				$cmsField->control		=	$field->formControl;
				$cmsField->id			=	$fieldId;
				$cmsField->name			=	$field->name;
				$cmsField->title		=	$field->title;
				$cmsField->description	=	$field->description;
				$cmsField->label		=	$fieldLabel;
				$cmsField->input		=	$fieldInput;
				$cmsField->required		=	$field->required;

				$result[$fieldId]		=	$cmsField;
			}

			$cache[$userId][$clientId]	=	$result;
		}

		return $cache[$userId][$clientId];
	}
}