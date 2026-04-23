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
use cbFieldHandler;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use cbValidator;

\defined( 'CBLIB' ) or die();

class TextField extends cbFieldHandler
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
		$pregExp						=	$this->_getRegexp( $field );

		if ( $pregExp ) {
			$attributeArray[]			=	cbValidator::getRuleHtmlAttributes( 'pattern', $pregExp );
			$attributeArray[]			=	cbValidator::getMsgHtmlAttributes( $this->pregExpSuccessText( $field, $user, $reason ), $this->pregExpErrorText( $field, $user, $reason ) );
		}

		if ( ( $reason == 'register' ) && ( in_array( $field->type, array( 'emailaddress', 'primaryemailaddress', 'textarea', 'text', 'webaddress', 'predefined' ) ) ) ) {
			if ( $field->getString( 'type', '' ) === 'webaddress' ) {
				$defaultForbidden		=	'mailto:,//.[url],<a,</a>,&#';
			} else {
				$defaultForbidden		=	'http:,https:,mailto:,//.[url],<a,</a>,&#';
			}
		} else {
			$defaultForbidden			=	'';
		}

		$forbiddenContent				=	CBTxt::T( $field->params->get( 'fieldValidateForbiddenList_' . $reason, $defaultForbidden ) );

		if ( $forbiddenContent != '' ) {
			$forbiddenWords				=	array();

			foreach ( explode( ',', $forbiddenContent ) as $forbiddenWord ) {
				if ( $forbiddenWord === '' ) {
					$forbiddenWords[]	=	',';
				} else {
					$forbiddenWords[]	=	$forbiddenWord;
				}
			}

			$attributeArray[]			=	cbValidator::getRuleHtmlAttributes( 'forbiddenwords', $forbiddenWords, CBTxt::T( 'UE_INPUT_VALUE_NOT_ALLOWED', 'This input value is not authorized.' ) );
		}

		if ( $field->get( 'type', null, GetterInterface::STRING ) == 'text' ) {
			$inputMask					=	$field->params->get( 'fieldInputMask', null, GetterInterface::STRING );

			if ( $inputMask ) {
				$mask					=	'mask';
				$params					=	null;
				$direction				=	0;

				switch ( $inputMask ) {
					case 'validation':
						$mask			=	'pattern';
						$params			=	$pregExp;
						break;
					case 'phone':
						$params			=	array(	'mask'	=>	array(	'999-9999',
																		'(999) 999-9999',
																		'+ 9 9{1,3} 999 9999',
																		'+ 99 9{1,3} 999 9999',
																		'+ 999 9{1,3} 999 9999'
																	) );
						break;
					case 'ip':
					case 'mac':
					case 'vin':
						$mask			=	'alias';
						$params			=	$inputMask;
						break;
					case 'customstring':
						$params			=	CBTxt::T( $field->params->get( 'fieldInputMaskString', null, GetterInterface::STRING ) );
						$direction		=	$field->params->get( 'fieldInputMaskDir', 0, GetterInterface::INT );
						break;
					case 'customregex':
						$mask			=	'pattern';
						$params			=	CBTxt::T( $field->params->get( 'fieldInputMaskRegexp', null, GetterInterface::RAW ) );
						break;
				}

				if ( $params ) {
					$attributeArray[]	=	cbValidator::getMaskHtmlAttributes( $mask, $params, $direction );
				}
			}
		}

		return parent::getDataAttributes( $field, $user, $output, $reason, $attributeArray );
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
	 * @return boolean                  True if validate, $this->_setErrorMSG if False
	 */
	public function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason )
	{
		$validated						=	parent::validate( $field, $user, $columnName, $value, $postdata, $reason );
		if ( $validated && ( $value !== '' ) && ( $value !== null ) ) {		// empty values (e.g. non-mandatory) are treated in the parent validation.
			$pregExp					=	$this->_getRegexp( $field );
			if ( $pregExp ) {
				$validated				=	preg_match( $pregExp, $value );
				if ( ! $validated ) {
					$pregExpError		=	$this->pregExpErrorText( $field, $user, $reason );
					$this->_setValidationError( $field, $user, $reason, $pregExpError );
				}
			}
		}
		return $validated;
	}

	/**
	 * Gets the regular expression to validate
	 * @param  FieldTable  $field  Field
	 * @return string
	 */
	protected function _getRegexp( $field )
	{
		switch ( $field->params->get( 'fieldValidateExpression', null, GetterInterface::STRING ) ){
			case 'singleword':
				return '/^[a-z]*$/iu';
				break;
			case 'multiplewords':
				return '/^([a-z]+ *)*$/iu';
				break;
			case 'singleaznum':
				return '/^[a-z]+[a-z0-9_]*$/iu';
				break;
			case 'atleastoneofeach':
				return '/^(?=.*\d)(?=.*(\W|_))(?=.*[a-z])(?=.*[A-Z]).{6,255}$/u';
				break;
			case 'phone':
				return '/^((?:\d{3}-\d{4})|(?:\(\d{3}\) \d{3}-\d{4})|(?:\+ ?\d{1,3} \d{1,3} \d{3} \d{4}))$/';
				break;
			case 'ip':
				return '/^((?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3})$/';
				break;
			case 'mac':
				return '/^([0-9A-Fa-f]{2}:[0-9A-Fa-f]{2}:[0-9A-Fa-f]{2}:[0-9A-Fa-f]{2}:[0-9A-Fa-f]{2}:[0-9A-Fa-f]{2})$/';
				break;
			case 'vin':
				return '/^([A-HJ-NPR-Z\d]{13}\d{4})$/';
				break;
			case 'customregex':
				return CBTxt::T( $field->params->get( 'pregexp', '/^.*$/', GetterInterface::RAW ) );
				break;
		}

		return null;
	}

	/**
	 * Returns a translated validation success message
	 *
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $reason
	 * @return string
	 */
	protected function pregExpSuccessText( $field, $user, $reason )
	{
		$pregExpSuccess		=	$field->params->get( 'pregexpsuccess', '', GetterInterface::HTML );

		if ( $pregExpSuccess ) {
			return CBTxt::T( $pregExpSuccess , null, array( '[FIELDNAME]' => $this->getFieldTitle( $field, $user, 'text', $reason ) ) );
		}

		return null;
	}

	/**
	 * Returns translated or generic 'Not a valid input' validation failed message
	 *
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $reason
	 * @return string
	 */
	protected function pregExpErrorText( $field, $user, $reason )
	{
		$pregExpError		=	$field->params->get( 'pregexperror', '', GetterInterface::HTML );

		if ( $pregExpError ) {
			return CBTxt::T( $pregExpError , null, array( '[FIELDNAME]' => $this->getFieldTitle( $field, $user, 'text', $reason ) ) );
		}

		return CBTxt::T( 'NOT_A_VALID_INPUT', 'Not a valid input', array( '[FIELDNAME]' => $this->getFieldTitle( $field, $user, 'text', $reason ) ) );
	}
}