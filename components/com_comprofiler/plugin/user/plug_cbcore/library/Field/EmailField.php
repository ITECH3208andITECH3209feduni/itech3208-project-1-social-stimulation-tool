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
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use cbValidator;
use moscomprofilerHTML;

\defined( 'CBLIB' ) or die();

class EmailField extends TextField
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
		if ( $field->params->get( 'field_check_email', 0 ) && ( ! isset( $field->_identicalTo ) ) ) {
			$attributeArray[]	=	cbValidator::getRuleHtmlAttributes( 'cbfield', array( 'user' => (int) $user->id, 'field' => htmlspecialchars( $field->name ), 'reason' => htmlspecialchars( $reason ) ) );
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
		global $_CB_OneTwoRowsStyleToggle;

		$results								=	null;

		if ( $output == 'htmledit' ) {
			if ( ( $reason != 'search' ) && $field->params->get( 'fieldVerifyInput', 0 ) ) {
				$verifyField					=	new FieldTable( $field->getDbo() );

				foreach ( array_keys( get_object_vars( $verifyField ) ) as $k ) {
					$verifyField->$k			=	$field->$k;
				}

				$verifyName						=	$field->name . '__verify';
				$verifyField->name				=	$verifyName;
				$verifyField->fieldid			=	$field->fieldid . '__verify';

				// cbReplaceVars to be done only once later:
				$titleOfVerifyField			=	$field->params->get( 'verifyEmailTitle' );
				if ( $titleOfVerifyField ) {
					$verifyField->title		=	CBTxt::Th( $titleOfVerifyField, null, array( '%s' => CBTxt::T( $field->title ) ) );
				} else {
					$verifyField->title		=	CBTxt::Th( '_UE_VERIFY_SOMETHING', 'Verify %s', array( '%s' => CBTxt::T( $field->title ) ) );
				}

				$verifyField->_identicalTo		=	$field->name;

				$toggleState					=	$_CB_OneTwoRowsStyleToggle;

				$results						=	parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );

				$_CB_OneTwoRowsStyleToggle		=	$toggleState;

				$user->set( $verifyName, $user->get( $field->name ) );

				$results						.=	parent::getFieldRow( $verifyField, $user, $output, $formatting, $reason, $list_compare_types );

				unset( $verifyField );
				unset( $user->$verifyName );
			} else {
				$results						=	parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
			}
		} else {
			$results							=	parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
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
		global $_CB_framework, $ueConfig;

		$value								=	$user->get( $field->name );

		switch ( $output ) {
			case 'html':
			case 'rss':
				$useLayout						=	true;

				if ( $field->type == 'primaryemailaddress' ) {
					$imgMode					=	$field->get( '_imgMode', null, GetterInterface::INT ); // For B/C

					if ( $imgMode === null ) {
						$imgMode				=	$field->params->get( ( $reason == 'list' ? 'displayModeList' : 'displayMode' ), 0, GetterInterface::INT );
					}

					if ( ( $ueConfig['allow_email_display'] == 3 ) || ( $imgMode != 0 ) ) {
						$oValueText				=	CBTxt::T( 'UE_SENDEMAIL', 'Send Email' );
					} else {
						$oValueText				=	htmlspecialchars( $value );
					}

					$emailIMG					=	'<span class="fa fa-envelope"' . ( $ueConfig['allow_email_display'] != 1 ? ' title="' . htmlspecialchars( CBTxt::T( 'UE_SENDEMAIL', 'Send Email' ) ) . '"' : null ) . '></span>';

					switch ( $imgMode ) {
						case 1:
							$useLayout			=	false; // We don't want to use layout for icon only display as we use it externally
							$linkItemImg		=	$emailIMG;
							$linkItemSep		=	null;
							$linkItemTxt		=	null;
							break;
						case 2:
							$linkItemImg		=	$emailIMG;
							$linkItemSep		=	' ';
							$linkItemTxt		=	$oValueText;
							break;
						case 0:
						default:
							$linkItemImg		=	null;
							$linkItemSep		=	null;
							$linkItemTxt		=	$oValueText;
							break;
					}
					$oReturn					=	'';
					//if no email or 4 (do not display email) then return empty string
					if ( ( $value == null ) || ( $ueConfig['allow_email_display'] == 4 ) || ( ( $imgMode == 1 ) && ( $ueConfig['allow_email_display'] == 1 ) ) ) {
						// $oReturn				=	'';
					} else {
						switch ( $ueConfig['allow_email_display'] ) {
							case 1: //display email only
								$oReturn		=	( $linkItemImg ? $linkItemImg . $linkItemSep : null )
												.	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 0 );
								break;
							case 2: //mailTo link
								// cloacking doesn't cloack the text of the hyperlink, if that text does contain email addresses		//TODO: fix it.
								if ( ! $linkItemImg && $linkItemTxt == htmlspecialchars( $value ) ) {
									$oReturn	=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 1, '', 0 );
								} elseif ( $linkItemImg && $linkItemTxt != htmlspecialchars( $value ) ) {
									$oReturn	=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 1, $linkItemImg . $linkItemSep . $linkItemTxt, 0 );
								} elseif ( $linkItemImg && $linkItemTxt == htmlspecialchars( $value ) ) {
									$oReturn 	=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 1, $linkItemImg, 0 ) . $linkItemSep;
									$oReturn	.=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 1, '', 0 );
								} elseif ( ! $linkItemImg && $linkItemTxt != htmlspecialchars( $value ) ) {
									$oReturn	=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 1, $linkItemTxt, 0 );
								}
								break;
							case 3: //email Form (with cloacked email address if visible)
								$oReturn		=	"<a href=\""
												.	$_CB_framework->viewUrl( array( 'emailuser', 'uid' => $user->id ) )
												.	"\" title=\"" . CBTxt::T( 'UE_MENU_SENDUSEREMAIL_DESC', 'Send an Email to this user' ) . "\">" . $linkItemImg . $linkItemSep;
								if ( $linkItemTxt && ( $linkItemTxt != CBTxt::T( 'UE_SENDEMAIL', 'Send Email' ) ) ) {
									$oReturn	.=	moscomprofilerHTML::emailCloaking( $linkItemTxt, 0 );
								} else {
									$oReturn	.=	$linkItemTxt;
								}
								$oReturn		.=	"</a>";
								break;
						}
					}

				} else {

					// emailaddress:
					if ( $value == null ) {
						$oReturn				=	'';
					} else {
						if ( $ueConfig['allow_email'] == 1 ) {
							$oReturn			=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 1, "", 0 );
						} else {
							$oReturn			=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 0 );
						}
					}

				}

				if ( $useLayout ) {
					$oReturn					=	$this->formatFieldValueLayout( $oReturn, $reason, $field, $user );
				}
				break;

			case 'htmledit':
				$oReturn						=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'email', $value, ( $reason != 'search' ? $this->getDataAttributes( $field, $user, $output, $reason ) : null ) );

				if ( $reason == 'search' ) {
					$oReturn					=	$this->_fieldSearchModeHtml( $field, $user, $oReturn, 'text', $list_compare_types );
				}
				break;

			case 'json':
			case 'php':
			case 'xml':
			case 'csvheader':
			case 'fieldslist':
			case 'csv':
			default:
				$oReturn				=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}
		return $oReturn;
	}

	/**
	 * Direct access to field for custom operations, like for Ajax
	 *
	 * WARNING: direct unchecked access, except if $user is set, then check
	 * that the logged-in user has rights to edit that $user.
	 *
	 * @param FieldTable     $field
	 * @param null|UserTable $user
	 * @param array          $postdata
	 * @param string         $reason 'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches (always public!)
	 * @return string                  Expected output.
	 */
	public function fieldClass( &$field, &$user, &$postdata, $reason )
	{
		global $_CB_framework, $_CB_database, $ueConfig, $_GET;

		parent::fieldClass( $field, $user, $postdata, $reason ); // Performs spoof check

		if ( ! in_array( $reason, array( 'edit', 'register' ) ) ) {
			return null; // wrong reason; do nothing
		}

		$function								=	cbGetParam( $_GET, 'function', '' );

		if ( ! in_array( $function, array( 'checkvalue', 'testexists' ) ) ) {
			return null; // wrong funcion; do nothing
		}

		$emailChecker							=	$field->params->getInt( 'field_check_email', 0 );

		if ( ! $emailChecker ) {
			return null; // email checking is disabled; do nothing
		}

		$valid									=	true;
		$message								=	null;
		$email									=	stripslashes( cbGetParam( $postdata, 'value', '' ) );
		$emailConfirmation						=	( ( $field->name == 'email' ) && $ueConfig['reg_confirmation'] );

		foreach ( $field->getTableColumns() as $col ) {
			if ( ( ! $user ) || ( strtolower( trim( $email ) ) != strtolower( trim( $user->$col ) ) ) ) {
				if ( ! $this->validate( $field, $user, $col, $email, $postdata, $reason ) ) {
					global $_PLUGINS;

					$valid						=	false;
					$message					=	$_PLUGINS->getErrorMSG( '<br />' );
				} else {
					// Advanced:
					if ( in_array( $emailChecker, [ 2, 3 ], true ) ) {
						$query					=	'SELECT COUNT(*)'
												.	"\n FROM " . $_CB_database->NameQuote( $field->table );
						if ( $_CB_database->isDbCollationCaseInsensitive() ) {
							$query				.=	"\n WHERE " . $_CB_database->NameQuote( $col ) . " = " . $_CB_database->Quote( trim( $email ) );
						} else {
							$query				.=	"\n WHERE LOWER( " . $_CB_database->NameQuote( $col ) . " ) = " . $_CB_database->Quote( strtolower( trim( $email ) ) );
						}
						$_CB_database->setQuery( $query );
						$exists					=	$_CB_database->loadResult();

						if ( $function == 'testexists' ) {
							if ( $exists ) {
								$message		=	CBTxt::Th( 'UE_EMAIL_EXISTS_ON_SITE', "The email '[email]' exists on this site.", array( '[email]' =>  htmlspecialchars( $email ) ) );
							} else {
								$valid			=	false;
								$message		=	CBTxt::Th( 'UE_EMAIL_DOES_NOT_EXISTS_ON_SITE', "The email '[email]' does not exist on this site.", array( '[email]' =>  htmlspecialchars( $email ) ) );
							}
						} else {
							if ( $exists ) {
								$valid			=	false;
								$message		=	CBTxt::Th( 'UE_EMAIL_NOT_AVAILABLE', "The email '[email]' is already in use.", array( '[email]' =>  htmlspecialchars( $email ) ) );
							} else {
								$message		=	CBTxt::Th( 'UE_EMAIL_AVAILABLE', "The email '[email]' is available.", array( '[email]' =>  htmlspecialchars( $email ) ) );
							}
						}
					}

					// Simple:
					if ( ( $function != 'testexists' ) && $valid && in_array( $emailChecker, [ 1, 2 ], true ) ) {
						$checkResult			=	cbCheckMail( $_CB_framework->getCfg( 'mailfrom' ), $email );

						switch ( $checkResult ) {
							case -2: // Wrong Format
								$valid			=	false;
								$message		=	CBTxt::Th( 'UE_EMAIL_NOVALID', 'This is not a valid email address.', array( '[email]' =>  htmlspecialchars( $email ) ) );
								break;
							case -1: // Couldn't Check
								break;
							case 0: // Invalid
								$valid			=	false;

								if ( $emailConfirmation ) {
									$message	=	CBTxt::Th( 'UE_EMAIL_INCORRECT_CHECK_NEEDED', 'This address does not accept email: Needed for confirmation.', array( '[email]' =>  htmlspecialchars( $email ) ) );
								} else {
									$message	=	CBTxt::Th( 'UE_EMAIL_INCORRECT_CHECK', 'This email does not accept email: Please check.', array( '[email]' =>  htmlspecialchars( $email ) ) );
								}
								break;
						}
					}
				}
			}
		}

		return json_encode( array( 'valid' => $valid, 'message' => $message ) );
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

		foreach ( $field->getTableColumns() as $col ) {
			$value					=	stripslashes( cbGetParam( $postdata, $col, '' ) );
			$valueVerify			=	stripslashes( cbGetParam( $postdata, $col . '__verify', '' ) );

			$value					=	str_replace( array( 'mailto:', 'http://', 'https://' ), '', $value );
			$valueVerify			=	str_replace( array( 'mailto:', 'http://', 'https://' ), '', $valueVerify );

			$validated				=	$this->validate( $field, $user, $col, $value, $postdata, $reason );

			if ( $value !== null ) {
				if ( $validated && isset( $user->$col ) ) {
					if ( ( $reason != 'search' ) && $field->params->get( 'fieldVerifyInput', 0 ) && ( $value != $valueVerify ) ) {
						$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'Email and verification do not match, please try again.' ) );
					} elseif ( ( (string) $user->$col ) !== (string) $value ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
					}
				}

				$user->$col			=	$value;
			}
		}
	}

	/**
	 * Validator:
	 * Validates $value for $field->required and other rules
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user        RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  string      $columnName  Column to validate
	 * @param  string      $value       (RETURNED:) Value to validate, Returned Modified if needed !
	 * @param  array       $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return boolean                            True if validate, $this->_setErrorMSG if False
	 */
	public function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason )
	{
		$validate	=	parent::validate( $field, $user, $columnName, $value, $postdata, $reason );
		if ( $validate && ( $value != null ) ) {
			if ( ! cbIsValidEmail( $value ) ) {
				$this->_setValidationError( $field, $user, $reason, sprintf( CBTxt::T( 'UE_EMAIL_NOVALID', 'This is not a valid email address.' ), htmlspecialchars( $value ) ) );
				$validate				=	false;
			}
		}
		return $validate;
	}
}