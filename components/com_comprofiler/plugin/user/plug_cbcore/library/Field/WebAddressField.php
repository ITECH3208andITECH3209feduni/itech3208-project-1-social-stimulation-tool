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
use cbValidator;

\defined( 'CBLIB' ) or die();

class WebAddressField extends TextField
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
		$attributeArray[]	=	cbValidator::getRuleHtmlAttributes( 'cburl' );

		return parent::getDataAttributes( $field, $user, $output, $reason, $attributeArray );
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

		$value						=	$user->get( $field->name );

		switch ( $output ) {
			case 'html':
			case 'rss':
				if ( $value == null ) {
					return $this->formatFieldValueLayout( '', $reason, $field, $user );
				} elseif ( $ueConfig['allow_website'] == 1 ) {
					$oReturn		=	$this->_explodeCBvalues( $value );

					if ( count( $oReturn ) == 0 ) {
						$oReturn	=	array( '', '' );
					}

					if ( count( $oReturn ) < 2) {
						$oReturn[1]	=	$oReturn[0];
					}

					$scheme			=	parse_url( $oReturn[0], PHP_URL_SCHEME );

					if ( $scheme && ( ! in_array( $scheme, array( 'http', 'https' ) ) ) ) {
						// Stored scheme is invalid so remove it:
						$scheme		=	null;
						$oReturn[0]	=	preg_replace( '%^(?:(?:.(?<!^http|^https))+:(?://)?)%', '', $oReturn[0] );
					}

					return $this->formatFieldValueLayout( '<a href="' . htmlspecialchars( ( ! $scheme ? 'http://' : null ) . $oReturn[0] ) . '" target="_blank" rel="' . ( (int) $field->params->get( 'webaddress_nofollow', 1 ) ? 'nofollow ' : null ) . ( (int) $field->params->get( 'webaddress_noreferrer', 1 ) ? 'noreferrer ' : null ) . 'noopener">' . htmlspecialchars( $oReturn[1] ) . '</a>', $reason, $field, $user );
				} else {
					return $this->formatFieldValueLayout( htmlspecialchars( $value ), $reason, $field, $user );
				}
				break;

			case 'htmledit':
				if ( $field->params->get( 'webaddresstypes', 0 ) != 2 ) {
					$oReturn			=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, ( $reason != 'search' ? $this->getDataAttributes( $field, $user, $output, $reason ) : null ) );
				} else {
					$oValuesArr			=	$this->_explodeCBvalues( $value );

					if ( count( $oValuesArr ) == 0 ) {
						$oValuesArr		=	array( '', '' );
					}

					if ( count( $oValuesArr ) < 2 ) {
						$oValuesArr[1]	=	'';
					}

					$oReturn			=	'<div class="form-group row no-gutters cb_form_line">'
										.		'<label for="' . htmlspecialchars( $field->name ) . '" class="col-form-label col-sm-3 pr-sm-2">' . CBTxt::Th( 'UE_WEBURL', 'Address of Site' ) . '</label>'
										.		'<div class="cb_field col-sm-9">'
										.			$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $oValuesArr[0], ( $reason != 'search' ? $this->getDataAttributes( $field, $user, $output, $reason ) : null ) )
										.		'</div>'
										.	'</div>';

					$saveFieldName		=	$field->name;
					$saveFieldTitle		=	$field->title;
					$field->name		=	$saveFieldName . 'Text';
					$field->title		=	$field->title . ': ' . CBTxt::Th( 'UE_WEBTEXT', 'Name of Site');

					$oReturn			.=	'<div class="form-group row no-gutters mb-0 cb_form_line">'
										.		'<label for="' . htmlspecialchars( $field->name ) . '" class="col-form-label col-sm-3 pr-sm-2">' . CBTxt::Th( 'UE_WEBTEXT', 'Name of Site' ) . '</label>'
										.		'<div class="cb_field col-sm-9">'
										.			$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $oValuesArr[1], '' )
										.		'</div>'
										.	'</div>';

					$field->name		=	$saveFieldName;
					$field->title		=	$saveFieldTitle;
				}

				if ( $reason == 'search' ) {
					$oReturn			=	$this->_fieldSearchModeHtml( $field, $user, $oReturn, 'text', $list_compare_types );
				}
				return $oReturn;
				break;

			case 'json':
			case 'php':
			case 'xml':
			case 'csvheader':
			case 'fieldslist':
			case 'csv':
			default:
				return parent::getField( $field, $user, $output, $reason, $list_compare_types );
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

		foreach ( $field->getTableColumns() as $col ) {
			$value						=	stripslashes( cbGetParam( $postdata, $col, '' ) );
			$valueText					=	stripslashes( cbGetParam( $postdata, $col . 'Text', '' ) );

			if ( $value !== null ) {
				$value					=	preg_replace( '%^(?:(?:.(?<!^http|^https))+:(?://)?)%', '', $value );

				if ( $valueText ) {
					$oValuesArr			=	array();
					$oValuesArr[0]		=	$value;
					$oValuesArr[1]		=	preg_replace( '%^(?:(?:.(?<!^http|^https))+:(?://)?)%', '', $valueText );
					$value				=	$this->_implodeCBvalues( $oValuesArr );
				}
			}
			$validated					=	$this->validate( $field, $user, $col, $value, $postdata, $reason );
			if ( $value !== null ) {
				if ( $validated && isset( $user->$col ) && ( ( (string) $user->$col ) !== (string) $value ) ) {
					$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
				}
				$user->$col				=	$value;
			}
		}
	}
}