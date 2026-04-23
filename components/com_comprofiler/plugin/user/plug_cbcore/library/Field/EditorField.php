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
use CBLib\Application\Application;
use CBLib\Input\Get;
use CBLib\Registry\GetterInterface;

\defined( 'CBLIB' ) or die();

class EditorField extends cbFieldHandler
{
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
		global $_CB_framework;

		$value							=	$user->get( implode( '', $field->getTableColumns() ) );

		switch ( $output ) {
			case 'html':
			case 'rss':
				$html					=	$this->formatFieldValueLayout( Get::clean( $value, GetterInterface::HTML ), $reason, $field, $user, false );
				unset( $cbFields );
				break;
			case 'htmledit':
				if ( $reason == 'search' ) {
					$rows				=	$field->rows;
					if ( $rows > 5 ) {
						$field->rows	=	5;
					}
					$html				=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'textarea', $value, '' );
					$field->rows		=	$rows;
					$html				=	$this->_fieldSearchModeHtml( $field, $user, $html, 'text', $list_compare_types );
				} elseif ( ! ( $this->_isReadOnly( $field, $user, $reason ) ) ) {
					$value				=	Get::clean( $value, GetterInterface::HTML );
					unset( $cbFields );

					$translatedTitle	=	$this->getFieldTitle( $field, $user, 'html', $reason );
					$htmlDescription	=	$this->getFieldDescription( $field, $user, 'htmledit', $reason );
					$trimmedDescription	=	trim( strip_tags( $htmlDescription ) );
					$inputDescription	=	$field->params->get( 'fieldLayoutInputDesc', 1, GetterInterface::INT );

					$editor				=	Application::Cms()->displayCmsEditor( $field->name, $value, 600, 350, $field->cols, $field->rows );

					$html				=	$this->formatFieldValueLayout( ( $trimmedDescription && $inputDescription ? cbTooltip( $_CB_framework->getUi(), $htmlDescription, $translatedTitle, null, null, $editor, null, 'class="d-block clearfix"' ) : $editor ), $reason, $field, $user, false )
										.	$this->_fieldIconsHtml( $field, $user, $output, $reason, null, $field->type, $value, 'input', null, true, ( $this->_isRequired( $field, $user, $reason ) && ( ! $this->_isReadOnly( $field, $user, $reason ) ) ) );
					$this->_addSaveAndValidateCode( $field, $user, $reason );
				} else {
					$html				=	null;
				}
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
		return $html;
	}

	/**
	 * Adds validation and saving Javascript
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $reason
	 * @return void
	 */
	function _addSaveAndValidateCode( $field, $user, $reason )
	{
		global $_CB_framework;

		$js				=	null;

		if ( $this->_isRequired( $field, $user, $reason ) ) {
			$js			.=	"$( '#" . addslashes( $field->name ) . "' ).addClass( 'required' );";
		}

		if ( $js ) {
			$_CB_framework->outputCbJQuery( $js );
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
			$value						=	stripslashes( cbGetParam( $postdata, $col, '', _CB_ALLOWRAW ) );
			if ( $value !== null ) {
				$value					=	Get::clean( $value, GetterInterface::HTML );
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