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
use cbCalendars;
use CBLib\Application\Application;
use CBLib\Input\Get;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use cbSqlQueryPart;
use CBuser;

\defined( 'CBLIB' ) or die();

class TermsField extends CheckboxField
{
	/**
	 * Initializer:
	 * Puts the default value of $field into $user (for registration or new user in backend)
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $reason      'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 */
	public function initFieldToDefault( &$field, &$user, $reason )
	{
		foreach ( $field->getTableColumns() as $col ) {
			if ( ( $reason == 'search' ) || ( strpos( $col, 'consent' ) !== false ) ) {
				$user->$col							=	null;
			} else {
				$user->$col							=	$field->default;
			}
		}
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
		global $_CB_framework;

		$fieldName						=	$field->get( 'name', null, GetterInterface::STRING );
		$expired						=	$this->getConsentExpired( $field, $user );

		// Reset the value to unaccepted if consent has expired:
		if ( $expired ) {
			$user->set( $fieldName, 0 );
		}

		$value							=	$user->get( $fieldName, 0, GetterInterface::INT );
		$consent						=	$user->get( $fieldName . 'consent', null, GetterInterface::STRING );
		$return							=	null;

		if ( ( $output == 'htmledit' ) && ( $reason != 'search' ) ) {
			if ( Application::MyUser()->getUserId() != $user->get( 'id', 0, GetterInterface::INT ) ) {
				// Terms and Conditions should never be required to be accepted by a user other than the profile owner:
				$field->set( 'required', 0 );
			}

			// If consent has expired lets add a CSS class to the field for potential styling of expired consent fields:
			if ( $expired ) {
				$field->set( 'cssclass', trim( $field->get( 'cssclass', null, GetterInterface::STRING ) . ' cbTermsConsentExpired' ) );
			}

			$cbUser						=	CBuser::getMyInstance();
			$termsOutput				=	$field->params->get( 'terms_output', 'url' );
			$termsType					=	$cbUser->replaceUserVars( $field->params->get( 'terms_type', 'TERMS_AND_CONDITIONS' ) );
			$termsDisplay				=	$field->params->get( 'terms_display', 'modal' );
			$termsURL					=	cbSef( $cbUser->replaceUserVars( $field->params->get( 'terms_url', null ) ), false );
			$termsText					=	$cbUser->replaceUserVars( $field->params->get( 'terms_text', null ) );
			$termsWidth					=	$field->params->get( 'terms_width', 400 );
			$termsHeight				=	$field->params->get( 'terms_height', 200 );

			if ( ( ( $termsOutput == 'url' ) && ( ! $termsURL ) ) || ( ( $termsOutput == 'text' ) && ( ! $termsText ) ) ) {
				return parent::getField( $field, $user, $output, $reason, $list_compare_types );
			}

			if ( ! $termsType ) {
				$termsType				=	CBTxt::T( 'TERMS_AND_CONDITIONS', 'Terms and Conditions' );
			}

			if ( ! $termsWidth ) {
				$termsWidth				=	400;
			}

			if ( ! $termsHeight ) {
				$termsHeight			=	200;
			}

			if ( $termsDisplay == 'iframe' ) {
				if ( is_numeric( $termsHeight ) ) {
					$termsHeight		.=	'px';
				}

				if ( is_numeric( $termsWidth ) ) {
					$termsWidth			.=	'px';
				}

				if ( $termsOutput == 'url' ) {
					$return				.=	'<div class="embed-responsive mb-2 cbTermsFrameContainer" style="padding-bottom: ' . htmlspecialchars( $termsHeight ) . ';">'
										.		'<iframe class="embed-responsive-item d-block border rounded cbTermsFrameURL" style="width: ' . htmlspecialchars( $termsWidth ) . ';" src="' . htmlspecialchars( $termsURL ) . '"></iframe>'
										.	'</div>';
				} else {
					$return				.=	'<div class="bg-light border rounded p-2 mb-2 cbTermsFrameText" style="height:' . htmlspecialchars( $termsHeight ) . ';width:' . htmlspecialchars( $termsWidth ) . ';overflow:auto;">' . $termsText . '</div>';
				}

											// CBTxt::Th( 'TERMS_FIELD_I_AGREE_ON_THE_ABOVE_CONDITIONS', 'I Agree to the above [type].', array( '[type]' => $termsType ) )
				$label					=	CBTxt::Th( 'FIELD_' . $field->get( 'fieldid', 0, GetterInterface::INT ) . '_TERMS_FIELD_I_AGREE_ON_THE_ABOVE_CONDITIONS TERMS_FIELD_I_AGREE_ON_THE_ABOVE_CONDITIONS', 'I Agree to the above [type].', array( '[type]' => $termsType ) );
			} else {
				$attributes				=	' class="cbTermsLink"';

				if ( ( $termsOutput == 'text' ) && ( $termsDisplay == 'window' ) ) {
					$termsDisplay		=	'modal';
				}

				if ( $termsDisplay == 'modal' ) {
					// Tooltip height percentage would be based off window height (including scrolling); lets change it to be based off the viewport height:
					$termsHeight		=	( substr( $termsHeight, -1 ) == '%' ? (int) substr( $termsHeight, 0, -1 ) . 'vh' : $termsHeight );

					if ( $termsOutput == 'url' ) {
						$tooltip		=	'<iframe class="d-block m-0 p-0 border-0 cbTermsModalURL" height="100%" width="100%" src="' . htmlspecialchars( $termsURL ) . '"></iframe>';
					} else {
						$tooltip		=	'<div class="cbTermsModalText" style="height:100%;width:100%;overflow:auto;">' . $termsText . '</div>';
					}

					$url				=	'javascript:void(0);';
					$attributes			.=	' ' . cbTooltip( $_CB_framework->getUi(), $tooltip, $termsType, array( $termsWidth, $termsHeight ), null, null, null, 'data-hascbtooltip="true" data-cbtooltip-modal="true"' );
				} else {
					$url				=	htmlspecialchars( $termsURL );
					$attributes			.=	' target="_blank"';
				}

											// CBTxt::Th( 'TERMS_FIELD_ACCEPT_URL_CONDITIONS', 'Accept <!--suppress HtmlUnknownTarget --><a href="[url]"[attributes]>[type]</a>', array( '[url]' => $url, '[attributes]' => $attributes, '[type]' => $termsType ) )
				$label					=	CBTxt::Th( 'FIELD_' . $field->get( 'fieldid', 0, GetterInterface::INT ) . '_TERMS_FIELD_ACCEPT_URL_CONDITIONS TERMS_FIELD_ACCEPT_URL_CONDITIONS', 'Accept <!--suppress HtmlUnknownTarget --><a href="[url]"[attributes]>[type]</a>', array( '[url]' => $url, '[attributes]' => $attributes, '[type]' => $termsType ) );
			}

			$inputName					=	$field->name;
			$translatedTitle			=	$this->getFieldTitle( $field, $user, 'html', $reason );
			$htmlDescription			=	$this->getFieldDescription( $field, $user, 'htmledit', $reason );
			$trimmedDescription			=	trim( strip_tags( $htmlDescription ) );
			$inputDescription			=	$field->params->get( 'fieldLayoutInputDesc', 1, GetterInterface::INT );

			$attributes					=	null;

			if ( $this->_isRequired( $field, $user, $reason ) ) {
				$attributes				.=	' class="form-check-input required"';
			} else {
				$attributes				.=	' class="form-check-input"';
			}

			$attributes					.=	( $trimmedDescription && $inputDescription ? cbTooltip( $_CB_framework->getUi(), $htmlDescription, $translatedTitle, null, null, null, null, 'data-hascbtooltip="true"' ) : null );

			$return						.=	'<div class="cbSnglCtrlLbl form-check form-check-inline">'
										.		'<input type="checkbox" id="' . htmlspecialchars( $inputName ) . '" name="' . htmlspecialchars( $inputName ) . '" value="1"' . ( $value == 1 ? ' checked="checked"' : null ) . $attributes . ' />'
										.		'<label for="' . htmlspecialchars( $inputName ) . '" class="form-check-label">'
										.			$label
										.		'</label>'
										.	'</div>'
										.	$this->_fieldIconsHtml( $field, $user, $output, $reason, null, $field->type, $value, 'input', null, true, $this->_isRequired( $field, $user, $reason ) && ! $this->_isReadOnly( $field, $user, $reason ) );

			// Display the consent datetime or if expired the last datetime they consented:
			if ( $consent && ( $consent != '0000-00-00 00:00:00' ) ) {
				$return					.=	'<div class="text-small text-muted cbTermsConsented">';

				if ( $expired ) {
												// CBTxt::Th( 'TERMS_FIELD_LAST_ACCEPTED_ON', 'Last accepted on [consent].', array( '[consent]' => cbFormatDate( $consent ) ) )
					$return				.=		CBTxt::Th( 'FIELD_' . $field->get( 'fieldid', 0, GetterInterface::INT ) . '_TERMS_FIELD_LAST_ACCEPTED_ON TERMS_FIELD_LAST_ACCEPTED_ON', 'Last accepted on [consent]', array( '[consent]' => cbFormatDate( $consent ) ) );
				} else {
												// CBTxt::Th( 'TERMS_FIELD_ACCEPTED_ON', 'Accepted on [consent].', array( '[consent]' => cbFormatDate( $consent ) ) )
					$return				.=		CBTxt::Th( 'FIELD_' . $field->get( 'fieldid', 0, GetterInterface::INT ) . '_TERMS_FIELD_ACCEPTED_ON TERMS_FIELD_ACCEPTED_ON', 'Accepted on [consent]', array( '[consent]' => cbFormatDate( $consent ) ) );
				}

				$return					.=	'</div>';
			}
		} else {
			$return						=	parent::getField( $field, $user, $output, $reason, $list_compare_types );

			// If the user is a moderator and we're outputting search lets also output the consent date fields:
			if ( ( $output == 'htmledit' ) && ( $reason == 'search' ) && Application::MyUser()->isGlobalModerator() ) {
				$minNam					=	$fieldName . 'consent__minval';
				$maxNam					=	$fieldName . 'consent__maxval';

				$minVal					=	$user->get( $minNam, null, GetterInterface::STRING );
				$maxVal					=	$user->get( $maxNam, null, GetterInterface::STRING );
				$yMax					=	Application::Date( 'now', 'UTC' )->format( 'Y' );

				$calendars				=	new cbCalendars( ( Application::Cms()->getClientId() ? 2 : 1 ) );
				$minHtml				=	$this->formatFieldValueLayout( $calendars->cbAddCalendar( $minNam, CBTxt::Th( 'UE_SEARCH_FROM', 'Between' ) . ' ' . $this->getFieldTitle( $field, $user, 'text', $reason ), false, $minVal, false, true, $yMax, ( $yMax - 30 ) ), $reason, $field, $user );
				$maxHtml				=	$this->formatFieldValueLayout( $calendars->cbAddCalendar( $maxNam, CBTxt::Th( 'UE_SEARCH_TO', 'and' ) . ' ' . $this->getFieldTitle( $field, $user, 'text', $reason ), false, $maxVal, false, true, $yMax, ( $yMax - 30 ) ), $reason, $field, $user );

				$return					.=	$this->_fieldSearchRangeModeHtml( $field, $user, $output, $reason, $value, $minHtml, $maxHtml, $list_compare_types, 'mt-2' );
			}
		}

		return $return;
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

		$fieldName		=	$field->get( 'name', null, GetterInterface::STRING );
		$value			=	Get::get( $postdata, $fieldName, 0, GetterInterface::INT );

		// Reset the value to unaccepted if consent has expired to trigger field update, but only for the profile owner:
		if ( Application::MyUser()->getUserId() == $user->get( 'id', 0, GetterInterface::INT ) ) {
			if ( $this->getConsentExpired( $field, $user ) ) {
				$user->set( $fieldName, 0 );
			}
		}

		if ( $this->validate( $field, $user, $fieldName, $value, $postdata, $reason ) && ( $user->get( $fieldName, 0, GetterInterface::INT ) !== $value ) ) {
			$this->_logFieldUpdate( $field, $user, $reason, $user->get( $fieldName, 0, GetterInterface::INT ), $value );

			// Only the profile owner can actually give consent:
			if ( Application::MyUser()->getUserId() == $user->get( 'id', 0, GetterInterface::INT ) ) {
				if ( $value ) {
					$user->set( $fieldName . 'consent', Application::Database()->getUtcDateTime() );
				} else {
					$user->set( $fieldName . 'consent', '0000-00-00 00:00:00' );
				}
			}
		}

		$user->set( $fieldName, $value );
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
	 * @param  string      $reason      'edit' for save user edit, 'register' for save registration
	 * @return boolean                  True if validate, $this->_setErrorMSG if False
	 */
	public function validate( &$field, &$user, /** @noinspection PhpUnusedParameterInspection */ $columnName, &$value, /** @noinspection PhpUnusedParameterInspection */ &$postdata, $reason )
	{
		if ( Application::MyUser()->getUserId() != $user->get( 'id', 0, GetterInterface::INT ) ) {
			// Terms and Conditions should never be required to be accepted by a user other than the profile owner:
			$field->set( 'required', 0 );
		}

		return parent::validate( $field, $user, $columnName, $value, $postdata, $reason );
	}

	/**
	 * Checks if consent has expired
	 *
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @return bool
	 */
	private function getConsentExpired( $field, $user )
	{
		$fieldName				=	$field->get( 'name', null, GetterInterface::STRING );

		if ( ( ! $user->get( $fieldName, 0, GetterInterface::INT ) ) || ( ! $user->get( 'id', 0, GetterInterface::INT ) ) ) {
			// If not accepted or not registered then consent can't or hasn't been given so it can't be expired:
			return false;
		}

		$termsDuration			=	$field->params->get( 'terms_duration', 'forever', GetterInterface::STRING );

		if ( $termsDuration == 'custom' ) {
			$termsDuration		=	$field->params->get( 'terms_duration_custom', '+1 YEAR', GetterInterface::STRING );
		}

		if ( ( ! $termsDuration ) || ( $termsDuration == 'forever' ) ) {
			// Consent doesn't expire:
			return false;
		}

		$consent				=	$user->get( $fieldName . 'consent', null, GetterInterface::STRING );

		if ( ( ! $consent ) || ( $consent == '0000-00-00 00:00:00' ) ) {
			// Consent was never given so respond as expired:
			return true;
		} elseif (  Application::Date( 'now', 'UTC' )->getTimestamp() >= Application::Date( $consent, 'UTC' )->modify( strtoupper( $termsDuration ) )->getTimestamp() ) {
			// Consent was given, but current datetime is past the expiration date:
			return true;
		}

		return false;
	}

	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $searchVals  RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string      $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return cbSqlQueryPart[]
	 */
	function bindSearchCriteria( &$field, &$searchVals, &$postdata, $list_compare_types, $reason )
	{
		$query										=	array();

		$fieldName									=	$field->get( 'name', null, GetterInterface::STRING );
		$searchMode									=	$this->_bindSearchMode( $field, $searchVals, $postdata, 'none', $list_compare_types );

		if ( $searchMode ) {
			$value									=	Get::get( $postdata, $fieldName, null, GetterInterface::STRING );

			if ( $value === '0' ) {
				$value								=	0;
			} elseif ( $value === '1' ) {
				$value								=	1;
			} else {
				return $query;
			}

			$searchVals->$fieldName					=	$value;

			$sql									=	new cbSqlQueryPart();
			$sql->tag								=	'column';
			$sql->name								=	$fieldName;
			$sql->table								=	$field->get( 'table', null, GetterInterface::STRING );
			$sql->type								=	'sql:field';
			$sql->operator							=	'=';
			$sql->value								=	$value;
			$sql->valuetype							=	'const:int';
			$sql->searchmode						=	$searchMode;

			$query[]								=	$sql;
		}

		if ( ! Application::MyUser()->isGlobalModerator() ) {
			// Only moderators can search consent dates:
			return $query;
		}

		$minNam										=	$fieldName . 'consent__minval';
		$maxNam										=	$fieldName . 'consent__maxval';
		$searchMode									=	$this->_bindSearchRangeMode( $field, $searchVals, $postdata, $minNam, $maxNam, $list_compare_types );

		if ( $searchMode ) {
			$minVal									=	Get::get( $postdata, $minNam, null, GetterInterface::STRING );
			$maxVal									=	Get::get( $postdata, $maxNam, null, GetterInterface::STRING );
			$minValIn								=	$minVal;
			$maxValIn								=	$maxVal;

			if ( $field->type == 'datetime' ) {
				$minSearch							=	( $minVal && ( $minVal !== '0000-00-00 00:00:00' ) );
				$maxSearch							=	( $maxVal && ( $maxVal !== '0000-00-00 00:00:00' ) );
			} elseif ( $field->type == 'time' ) {
				$minSearch							=	( $minVal && ( $minVal !== '00:00:00' ) );
				$maxSearch							=	( $maxVal && ( $maxVal !== '00:00:00' ) );
			} else {
				$minSearch							=	( $minVal && ( $minVal !== '0000-00-00' ) );
				$maxSearch							=	( $maxVal && ( $maxVal !== '0000-00-00' ) );
			}

			$forceMin								=	( ( ! $minSearch ) && $maxSearch && ( ! in_array( $field->name, array( 'lastupdatedate', 'lastvisitDate' ) ) ) );

			if ( $minSearch || $forceMin ) {
				$min								=	new cbSqlQueryPart();
				$min->tag							=	'column';
				$min->name							=	$fieldName . 'consent';
				$min->table							=	$field->get( 'table', null, GetterInterface::STRING );
				$min->type							=	'sql:field';
				$min->operator						=	( ! $forceMin ? ( $searchMode == 'isnot' ? '<=' : '>=' ) : '>' );

				if ( $field->type == 'datetime' ) {
					$min->value						=	( ! $forceMin ? $minVal : '0000-00-00 00:00:00' );
					$min->valuetype					=	'const:datetime';
				} elseif ( $field->type == 'time' ) {
					$min->value						=	( ! $forceMin ? $minVal : '00:00:00' );
					$min->valuetype					=	'const:time';
				} else {
					$min->value						=	( ! $forceMin ? $minVal : '0000-00-00' );
					$min->valuetype					=	'const:date';
				}

				$min->searchmode					=	$searchMode;

				if ( ! $forceMin ) {
					if ( ( ! $maxVal ) && $maxValIn ) {
						$searchVals->$maxNam		=	$maxValIn;
					}

					$searchVals->$minNam			=	$minValIn;
				}
			}

			if ( $maxSearch ) {
				$max								=	new cbSqlQueryPart();
				$max->tag							=	'column';
				$max->name							=	$fieldName . 'consent';
				$max->table							=	$field->get( 'table', null, GetterInterface::STRING );
				$max->type							=	'sql:field';
				$max->operator						=	( $searchMode == 'isnot' ? '>=' : '<=' );
				$max->value							=	$maxVal;

				if ( $field->type == 'datetime' ) {
					$max->valuetype					=	'const:datetime';
				} elseif ( $field->type == 'time' ) {
					$max->valuetype					=	'const:time';
				} else {
					$max->valuetype					=	'const:date';
				}

				$max->searchmode					=	$searchMode;

				if ( ( ! $minVal ) && $minValIn ) {
					$searchVals->$minNam			=	$minValIn;
				}

				$searchVals->$maxNam				=	$maxValIn;
			}

			if ( isset( $min ) && isset( $max ) ) {
				$sql								=	new cbSqlQueryPart();
				$sql->tag							=	'column';
				$sql->name							=	$fieldName . 'consent';
				$sql->table							=	$field->get( 'table', null, GetterInterface::STRING );
				$sql->type							=	'sql:operator';
				$sql->operator						=	( $searchMode == 'isnot' ? 'OR' : 'AND' );
				$sql->searchmode					=	$searchMode;

				$sql->addChildren( array( $min, $max ) );

				$query[]							=	$sql;
			} elseif ( isset( $min ) ) {
				$query[]							=	$min;
			} elseif ( isset( $max ) ) {
				$query[]							=	$max;
			}
		}

		return $query;
	}
}