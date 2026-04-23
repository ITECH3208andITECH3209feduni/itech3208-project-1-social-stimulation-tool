<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Core\Field;

use CB\Database\Table\FieldTable;
use CB\Database\Table\UserTable;
use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use cbSqlQueryPart;
use cbValidator;

\defined( 'CBLIB' ) or die();

class PasswordField extends TextField
{
	/**
	 * formats variable array into data attribute string
	 *
	 * @param  FieldTable   $field
	 * @param  UserTable    $user
	 * @param  string       $output
	 * @param  string       $reason
	 * @param  array        $attributeArray
	 * @return null|string
	 */
	protected function getDataAttributes( $field, $user, $output, $reason, $attributeArray = array() )
	{
		if ( $field->params->get( 'passTestSrength', 0 ) && ( ! isset( $field->_identicalTo ) ) && ( ! isset( $field->_requiredIf ) ) ) {
			$attributeArray[]		=	cbValidator::getRuleHtmlAttributes( 'passwordstrength' );
		}

		if ( $field->params->get( 'fieldVerifyCurrent', 1 ) && ( isset( $field->_requiredIf ) ) ) {
			$attributeArray[]		=	cbValidator::getRuleHtmlAttributes( 'requiredif', '#' . $field->_requiredIf );

			// Only validate the required state so turn off the other validations:
			$field->maxlength		=	0;

			$field->params->set( 'fieldValidateExpression', '' );
			$field->params->set( 'fieldMinLength', 0 );
		}

		return parent::getDataAttributes( $field, $user, $output, $reason, $attributeArray );
	}

	/**
	 * Returns a PASSWORD field in specified format
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
		global $ueConfig, $_CB_OneTwoRowsStyleToggle;

		$results										=	null;

		if ( $output == 'htmledit' ) {
			if ( ( $field->name != 'password' ) || ( $reason != 'register' ) || ! ( isset( $ueConfig['emailpass'] ) && ( $ueConfig['emailpass'] == "1" ) ) ) {
				$fieldTitle								=	$this->getFieldTitle( $field, $user, 'text', $reason );
				$fieldEditTitle							=	$fieldTitle;

				if ( $reason == 'edit' ) {
					$editTitle							=	$field->params->get( 'fieldEditTitle', null, GetterInterface::STRING );

					if ( $editTitle ) {
						$field->set( 'title', CBTxt::Th( $editTitle, null, array( '[title]' => $fieldTitle ) ) );

						$fieldEditTitle					=	$this->getFieldTitle( $field, $user, 'text', $reason );
					}
				}

				if ( $field->params->get( 'fieldVerifyInput', false, GetterInterface::BOOLEAN ) ) {
					$verifyField						=	new FieldTable( $field->getDbo() );

					foreach ( array_keys( get_object_vars( $verifyField ) ) as $k ) {
						$verifyField->$k				=	$field->$k;
					}

					$verifyField->set( 'name', $field->get( 'name', null, GetterInterface::STRING ) . '__verify' );
					$verifyField->set( 'fieldid', $field->get( 'fieldid', 0, GetterInterface::INT ) . '__verify' );
					$verifyField->set( 'params', new Registry( $field->params->asArray() ) );

					$titleOfVerifyField					=	$field->params->get( 'verifyPassTitle', null, GetterInterface::STRING );
					$descOfVerifyField					=	$field->params->get( 'verifyPassDesc', 'Please verify your new password.', GetterInterface::HTML );

					if ( $titleOfVerifyField ) {
						// Handles B/C legacy language strings and legacy %s usage in language string
						// CBTxt::Th( 'UE_VPASS', 'Verify Password' )
						// CBTxt::Th( '_UE_VERIFY_SOMETHING', 'Verify %s' )
						$verifyField->set( 'title', CBTxt::Th( $titleOfVerifyField, null, array( '%s' => $fieldEditTitle, '[title]' => $fieldEditTitle ) ) );
					} else {
						$verifyField->set( 'title', CBTxt::Th( '_UE_VERIFY_SOMETHING', 'Verify %s', array( '%s' => $fieldEditTitle, '[title]' => $fieldEditTitle ) ) );
					}

					if ( $descOfVerifyField ) {
						$verifyField->set( 'description', $descOfVerifyField );
					}

					$placeholderOfVerifyField			=	$field->params->get( 'verifyPassPlaceholder', null, GetterInterface::STRING );

					if ( $placeholderOfVerifyField ) {
						$verifyField->params->set( 'fieldPlaceholder', $placeholderOfVerifyField );
					}

					$verifyField->set( '_identicalTo', $field->get( 'name', null, GetterInterface::STRING ) );
				}

				$verifyCurr								=	( $field->params->get( 'fieldVerifyCurrent', 1 ) && ( $reason == 'edit' ) && ( ! Application::Cms()->getClientId() ) && ( $user->id == Application::MyUser()->getUserId() ) );

				if ( $verifyCurr ) {
					$verifyCurrField					=	new FieldTable( $field->getDbo() );

					foreach ( array_keys( get_object_vars( $verifyCurrField ) ) as $k ) {
						$verifyCurrField->$k			=	$field->$k;
					}

					$verifyCurrField->set( 'name', $field->get( 'name', null, GetterInterface::STRING ) . '__current' );
					$verifyCurrField->set( 'fieldid', $field->get( 'fieldid', 0, GetterInterface::INT ) . '__current' );
					$verifyCurrField->set( 'params', new Registry( $field->params->asArray() ) );

					$titleOfVerifyCurrentField			=	$field->params->get( 'verifyCurrentTitle', null, GetterInterface::STRING );
					$descOfVerifyCurrentField			=	$field->params->get( 'verifyCurrentDesc', 'Please verify your current password.', GetterInterface::HTML );

					if ( $titleOfVerifyCurrentField ) {
						$verifyCurrField->set( 'title', CBTxt::Th( $titleOfVerifyCurrentField, null, array( '[title]' => $fieldTitle ) ) );
					} else {
						$verifyCurrField->set( 'title', CBTxt::Th( 'VERIFY_CURRENT_SOMETHING', 'Current [title]', array( '[title]' => $fieldTitle ) ) );
					}

					if ( $descOfVerifyCurrentField ) {
						$verifyCurrField->set( 'description', $descOfVerifyCurrentField );
					}

					$placeholderOfVerifyCurrentField	=	$field->params->get( 'verifyCurrentPlaceholder', null, GetterInterface::STRING );

					if ( $placeholderOfVerifyCurrentField ) {
						$verifyCurrField->params->set( 'fieldPlaceholder', $placeholderOfVerifyCurrentField );
					}

					$verifyCurrField->set( '_requiredIf', $field->get( 'name', null, GetterInterface::STRING ) );
				}

				$toggleState							=	$_CB_OneTwoRowsStyleToggle;

				if ( $verifyCurr ) {
					$_CB_OneTwoRowsStyleToggle			=	$toggleState;

					$results							.=	parent::getFieldRow( $verifyCurrField, $user, $output, $formatting, $reason, $list_compare_types );

					unset( $verifyCurrField );
				}

				$results								.=	parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );

				if ( $field->params->get( 'fieldVerifyInput', false, GetterInterface::BOOLEAN ) ) {
					$_CB_OneTwoRowsStyleToggle			=	$toggleState;

					$results							.=	parent::getFieldRow( $verifyField, $user, $output, $formatting, $reason, $list_compare_types );

					unset( $verifyField );
				}
			} else {
				// case of "sending password by email" at registration time for main password field:
				$results								=	parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
			}
		} else {
			$results									=	parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
		}

		return $results;
	}
	/**
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
		global $ueConfig;

		$value									=	'';			// passwords are never sent back to forms.

		switch ( $output ) {
			case 'htmledit':
				if ( $reason == 'search' ) {
					return null;
				}

			if ( ( $field->name != 'password' ) || ( $reason != 'register' ) || ! ( isset( $ueConfig['emailpass'] ) && ( $ueConfig['emailpass'] == "1" ) ) ) {

					$req							=	$field->required;
					if ( ( $reason == 'edit' ) && in_array( $field->name, array( 'password', 'password__verify', 'password__current' ) ) ) {
						if ( $user->requireReset ) {
							$field->required		=	1;
						} else {
							$field->required		=	0;
						}
					}

					$html							=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', $field->type, $value, $this->getDataAttributes( $field, $user, $output, $reason ) );
					$field->required				=	$req;

				} else {
					// case of "sending password by email" at registration time for main password field:
					$html							=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'html', CBTxt::Th( 'SENDING_PASSWORD', 'Your password will be sent to the above e-mail address.<br />Once you have received your new password you can log in and change it.' ), '' );
				}
				return $html;
				break;

			case 'html':
				return CBTxt::T( 'HIDDEN_CHARACTERS', '********' );
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
		global $_CB_framework, $ueConfig;

		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		// For CB main password don't save if it's on registration and passwords are auto-generated.
		if ( ( $reason == 'register' ) && ( $field->name == 'password' ) ) {
			if ( isset( $ueConfig['emailpass'] ) && ( $ueConfig['emailpass'] == "1" ) ) {
				return;
			}
		}

		foreach ( $field->getTableColumns() as $col ) {
			$value					=	stripslashes( cbGetParam( $postdata, $col,				'', _CB_ALLOWRAW ) );
			$valueVerify			=	stripslashes( cbGetParam( $postdata, $col . '__verify',	'', _CB_ALLOWRAW ) );
			$valueCurrent			=	stripslashes( cbGetParam( $postdata, $col . '__current',	'', _CB_ALLOWRAW ) );

			$fieldRequired			=	$field->required;

			if ( $_CB_framework->getUi() == 2 ) {
				$field->required	=	0;
			} elseif ( ( $reason == 'edit' ) && ( $user->id != 0 ) && ( $user->$col || ( $field->name == 'password' ) ) ) {
				if ( $user->requireReset ) {
					// Password is required for password reset:
					$field->required	=	1;
				} else {
					$field->required	=	0;
				}
			}

			$this->validate( $field, $user, $col, $value, $postdata, $reason );

			if ( ( ( $reason == 'edit' ) && ( $user->id != 0 ) && ( $user->$col || ( $field->name == 'password' ) ) ) || ( $_CB_framework->getUi() == 2 ) ) {
				$field->required	=	$fieldRequired;
			}

			$fieldMinLength			=	$this->getMinLength( $field );

			$user->$col				=	null;		// don't update unchanged (hashed) passwords unless typed-in and all validates:
			if ( $value ) {
				if ( cbIsoUtf_strlen( $value ) < $fieldMinLength ) {
					$this->_setValidationError( $field, $user, $reason, sprintf( CBTxt::T( 'UE_VALID_PASS_CHARS', 'Please enter a valid %s.  No spaces, at least %s characters and contain lower and upper-case letters, numbers and special signs' ), CBTxt::T( 'UE_PASS', 'Password' ), $fieldMinLength ) );
				} elseif ( $field->params->get( 'fieldVerifyInput', false, GetterInterface::BOOLEAN ) && ( $value != $valueVerify ) ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'UE_REGWARN_VPASS2', 'Password and verification do not match, please try again.' ) );
				} elseif ( $field->params->get( 'fieldVerifyCurrent', 1 ) && ( $reason == 'edit' ) && ( ! Application::Cms()->getClientId() ) && ( $user->id == Application::MyUser()->getUserId() ) && ( ! $user->verifyPassword( $valueCurrent ) ) ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'Incorrect password, please try again.' ) );
				} else {
					if ( $user->requireReset ) {
						// Password was changed and passed validation so turn off resetting:
						$user->requireReset	=	0;
					}

					// There is no event for password changes on purpose here !
					$user->$col		=	$value;			// store only if validated
				}
			}
		}
	}
	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string      $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return cbSqlQueryPart[]
	 */
	function bindSearchCriteria( &$field, &$user, &$postdata, $list_compare_types, $reason )
	{
		return array();
	}

	/**
	 * Returns the minimum field length as set
	 *
	 * @param  FieldTable  $field
	 * @return int
	 */
	function getMinLength( $field )
	{
		$defaultMin					=	6;
		return $field->params->get( 'fieldMinLength', $defaultMin );
	}
}