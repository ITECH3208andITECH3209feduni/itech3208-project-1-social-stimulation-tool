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
use cbFieldHandler;
use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use cbSqlQueryPart;
use cbValidator;
use moscomprofilerHTML;

\defined( 'CBLIB' ) or die();

class DateField extends cbFieldHandler
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
	protected function getDataAttributes( $field, $user, $output, $reason, $attributeArray = [] )
	{
		$fieldId						=	$field->getInt( 'fieldid', 0 );

		if ( \in_array( $field->getString( 'type', '' ), [ 'date', 'datetime', 'time' ], true )
			 && ( ( ( $field->params->getInt( 'field_edit_format', 0 ) === 2 ) && \in_array( $reason, [ 'edit', 'registers' ], true ) )
				  || ( ( $field->params->getInt( 'field_search_by', 0 ) === 3 ) && ( $reason === 'search' ) )
			 )
		) {
			if ( $field->getString( 'type', '' ) !== 'time' ) {
				[ $yMin, $yMax, $yDesc ]	=	$this->_yearsRange( $field, 0 );

				$min						=	( $yDesc ? $yMax : $yMin );
				$max						=	( $yDesc ? $yMin : $yMax );

				if ( $min ) {
					if ( $field->getString( 'type', '' ) === 'date' ) {
						$attributeArray[]	=	' min="' . $min . '-01-01"';
					} else {
						$attributeArray[]	=	' min="' . $min . '-01-01T00:00"';
					}
				}

				if ( $max ) {
					if ( $field->getString( 'type', '' ) === 'date' ) {
						$attributeArray[]	=	' max="' . $max . '-12-31"';
					} else {
						$attributeArray[]	=	' max="' . $max . '-12-31T00:00"';
					}
				}
			}

			// Failsafe fallback pattern and masking checks to ensure if text input fallback happens the value will be supplied in the expected format
			if ( $field->getString( 'type', '' ) === 'time' ) {
				// CBTxt::T( 'VALIDATION_ERROR_FIELD_TIME_PATTERN', 'Please enter a HH:MM formatted time.' )
				$attributeArray[]			=	cbValidator::getRuleHtmlAttributes( 'pattern', '/^\d{2}:\d{2}$/iu', CBTxt::T( 'FIELD_' . $fieldId . '_VALIDATION_ERROR_FIELD_TIME_PATTERN VALIDATION_ERROR_FIELD_TIME_PATTERN', 'Please enter a HH:MM formatted time.' ) );
				$attributeArray[]			=	cbValidator::getMaskHtmlAttributes( 'mask', '99:99' );
			} elseif ( $field->getString( 'type', '' ) === 'date' ) {
				// CBTxt::T( 'VALIDATION_ERROR_FIELD_DATE_PATTERN', 'Please enter a YYYY-MM-DD formatted date.' )
				$attributeArray[]			=	cbValidator::getRuleHtmlAttributes( 'pattern', '/^\d{4}-\d{2}-\d{2}$/iu', CBTxt::T( 'FIELD_' . $fieldId . '_VALIDATION_ERROR_FIELD_DATE_PATTERN VALIDATION_ERROR_FIELD_DATE_PATTERN', 'Please enter a YYYY-MM-DD formatted date.' ) );
				$attributeArray[]			=	cbValidator::getMaskHtmlAttributes( 'mask', '9999-99-99' );
			} else {
				// CBTxt::T( 'VALIDATION_ERROR_FIELD_DATETIME_PATTERN', 'Please enter a YYYY-MM-DD HH:MM formatted date.' )
				$attributeArray[]			=	cbValidator::getRuleHtmlAttributes( 'pattern', '/^\d{4}-\d{2}-\d{2}(T| )\d{2}:\d{2}$/iu', CBTxt::T( 'FIELD_' . $fieldId . '_VALIDATION_ERROR_FIELD_DATETIME_PATTERN VALIDATION_ERROR_FIELD_DATETIME_PATTERN', 'Please enter a YYYY-MM-DD HH:MM formatted date.' ) );
				$attributeArray[]			=	cbValidator::getMaskHtmlAttributes( 'mask', '9999-99-99 99:99' );
			}
		}

		if ( ( $field->getString( 'type', '' ) === 'date' ) && \in_array( $field->params->getInt( 'field_display_by', 0 ), [ 1, 3 ], true ) ) {
			$minAge							=	$field->params->getInt( 'age_min', 0 );
			$maxAge							=	$field->params->getInt( 'age_max', 0 );

			if ( $minAge && $maxAge ) {
				$attributeArray[]			=	cbValidator::getRuleHtmlAttributes( 'rangeage', [ $minAge, $maxAge ], CBTxt::T( 'FIELD_' . $fieldId . '_VALIDATION_ERROR_FIELD_AGE_RANGE', '' ) );
			} elseif ( $minAge ) {
				$attributeArray[]			=	cbValidator::getRuleHtmlAttributes( 'minage', $minAge, CBTxt::T( 'FIELD_' . $fieldId . '_VALIDATION_ERROR_FIELD_MIN_AGE', '' ) );
			} elseif ( $maxAge ) {
				$attributeArray[]			=	cbValidator::getRuleHtmlAttributes( 'maxage', $maxAge, CBTxt::T( 'FIELD_' . $fieldId . '_VALIDATION_ERROR_FIELD_MAX_AGE', '' ) );
			}
		}

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
		global $_CB_framework;

		$value								=	$user->get( $field->get( 'name' ) );
		$return								=	null;

		switch ( $output ) {
			case 'html':
			case 'rss':
				if ( ( $value != null ) && ( $value != '' ) && ( $value != '0000-00-00 00:00:00' ) && ( $value != '0000-00-00' ) && ( $value != '00:00:00' ) ) {
					$offset					=	( $field->get( 'type', null, GetterInterface::STRING ) == 'date' ? false : $field->params->get( 'date_offset', true, GetterInterface::BOOLEAN ) );
					$showTime				=	( $field->get( 'type', null, GetterInterface::STRING ) == 'datetime' ? true : ( $field->get( 'type', null, GetterInterface::STRING ) == 'time' ? 2 : false ) );

					switch ( $field->params->get( 'field_display_by', 0, GetterInterface::INT ) ) {
						case 1: // Age
							$dateDiff		=	$_CB_framework->getUTCDateDiff( 'now', $value );
							$age			=	null;

							if ( $dateDiff ) {
								$age		=	$dateDiff->y;

								if ( $age < 0 ) {
									$age	=	null;
								}
							}

							$return			=	$this->formatFieldValueLayout( $age, $reason, $field, $user );
							break;
						case 2: // Timeago, with Ago
							$return			=	$this->formatFieldValueLayout( cbFormatDate( $value, $offset, 'timeago' ), $reason, $field, $user, false );
							break;
						case 6: // Timeago, without Ago
							$return			=	$this->formatFieldValueLayout( cbFormatDate( $value, $offset, 'exacttimeago' ), $reason, $field, $user, false );
							break;
						case 3: // Birthdate
							$return			=	$this->formatFieldValueLayout( htmlspecialchars( cbFormatDate( $value, $offset, $showTime, 'F d', ' g:i A', Application::User( $user->getInt( 'id', 0 ) )->getTimezone() ) ), $reason, $field, $user );
							break;
						case 4: // Date
							$return			=	$this->formatFieldValueLayout( htmlspecialchars( cbFormatDate( $value, $offset, false ) ), $reason, $field, $user );
							break;
						case 5: // Custom
							$dateFormat		=	CBTxt::T( $field->params->get( 'custom_date_format', 'Y-m-d', GetterInterface::STRING ) );
							$timeFormat		=	CBTxt::T( $field->params->get( 'custom_time_format', 'H:i:s', GetterInterface::STRING ) );

							$return			=	$this->formatFieldValueLayout( htmlspecialchars( cbFormatDate( $value, $offset, $showTime, $dateFormat, $timeFormat ) ), $reason, $field, $user );
							break;
						default: // Date/Datetime
							$return			=	$this->formatFieldValueLayout( htmlspecialchars( cbFormatDate( $value, $offset, $showTime ) ), $reason, $field, $user );
							break;
					}
				} else {
					$return					=	$this->formatFieldValueLayout( '', $reason, $field, $user );
				}
				break;
			case 'htmledit':
				global $_CB_framework;

				$offset						=	( $field->getString( 'type', '' ) === 'date' ? false : $field->params->getBool( 'date_offset', true ) );
				$displayBy					=	$field->params->getInt( 'field_display_by', 0 );
				$editBy						=	$field->params->getInt( 'field_edit_format', 0 );
				$searchBy					=	$field->params->getInt( 'field_search_by', 0 );

				if ( $displayBy === 1 ) { // Age
					$offset					=	false;
				}

				$dateFormat					=	null;
				$timeFormat					=	null;

				if ( $reason === 'search' ) {
					if ( $searchBy === 2 ) {
						$dateFormat			=	CBTxt::T( $field->params->getString( 'custom_date_search_format', 'Y-m-d' ) );

						if ( $field->getString( 'type', '' ) !== 'date' ) {
							$timeFormat		=	CBTxt::T( $field->params->getString( 'custom_time_search_format', 'H:i:s' ) );
						}
					}
				} elseif ( $editBy === 1 ) {
					$dateFormat				=	CBTxt::T( $field->params->getString( 'custom_date_edit_format', 'Y-m-d' ) );

					if ( $field->getString( 'type', '' ) !== 'date' ) {
						$timeFormat			=	CBTxt::T( $field->params->getString( 'custom_time_edit_format', 'H:i:s' ) );
					}
				}

				$showTime					=	( $field->getString( 'type', '' ) === 'datetime' ? true : ( $field->getString( 'type', '' ) === 'time' ? 2 : false ) );
				$translatedTitle			=	$this->getFieldTitle( $field, $user, 'html', $reason );
				$htmlDescription			=	$this->getFieldDescription( $field, $user, 'htmledit', $reason );
				$trimmedDescription			=	trim( strip_tags( $htmlDescription ) );
				$inputDescription			=	$field->params->getInt( 'fieldLayoutInputDesc', 1 );

				$tooltip					=	( $trimmedDescription && $inputDescription ? cbTooltip( $_CB_framework->getUi(), $htmlDescription, $translatedTitle, null, null, null, null, 'data-hascbtooltip="true"' ) : null );

				if ( $reason === 'search' ) {
					$minNam					=	$field->get( 'name' ) . '__minval';
					$maxNam					=	$field->get( 'name' ) . '__maxval';

					$minVal					=	$user->get( $minNam );
					$maxVal					=	$user->get( $maxNam );

					[ $yMin, $yMax, $yDesc ]	=	$this->_yearsRange( $field, $searchBy );

					if ( $searchBy === 1 ) {
						// Search by age range:
						$choices			=	[];

						for ( $i = $yMin ; $i <= $yMax ; $i++ ) {
							$choices[]		=	moscomprofilerHTML::makeOption( $i, $i );
						}

						if ( $minVal === null ) {
							$minVal			=	$yMin;
						}

						if ( $maxVal === null ) {
							$maxVal			=	$yMax;
						}

						$additional			=	' class="form-control"' . ( trim( $tooltip ) ? ' ' . $tooltip : null );
						$minHtml			=	moscomprofilerHTML::selectList( $choices, $minNam, $additional, 'text', 'value', $minVal, 2 );
						$maxHtml			=	moscomprofilerHTML::selectList( $choices, $maxNam, $additional, 'text', 'value', $maxVal, 2 );
					} else {
						if ( $minVal !== null ) {
							if ( $field->getString( 'type', '' ) === 'datetime' ) {
								$minVal		=	Application::Date( $minVal, 'UTC' )->format( 'Y-m-d H:i:s' );
							} elseif ( $field->getString( 'type', '' ) === 'time' ) {
								$minVal		=	Application::Date( $minVal, 'UTC' )->format( 'H:i:s' );
							} else {
								$minVal		=	Application::Date( $minVal, 'UTC' )->format( 'Y-m-d' );
							}
						}

						if ( $maxVal !== null ) {
							if ( $field->getString( 'type', '' ) === 'datetime' ) {
								$maxVal		=	Application::Date( $maxVal, 'UTC' )->format( 'Y-m-d H:i:s' );
							} elseif ( $field->getString( 'type', '' ) === 'time' ) {
								$maxVal		=	Application::Date( $maxVal, 'UTC' )->format( 'H:i:s' );
							} else {
								$maxVal		=	Application::Date( $maxVal, 'UTC' )->format( 'Y-m-d' );
							}
						}

						// Search by date range:
						$calendars			=	new cbCalendars( $_CB_framework->getUi(), $field->params->getString( 'calendar_type' ), $dateFormat, $timeFormat );

						$minHtml			=	$this->formatFieldValueLayout( $calendars->cbAddCalendar( $minNam, CBTxt::Th( 'UE_SEARCH_FROM', 'Between' ) . ' ' . $this->getFieldTitle( $field, $user, 'text', $reason ), false, $minVal, false, $showTime, ( $yDesc ? $yMax : $yMin ), ( $yDesc ? $yMin : $yMax ), $tooltip, $offset ), $reason, $field, $user );
						$maxHtml			=	$this->formatFieldValueLayout( $calendars->cbAddCalendar( $maxNam, CBTxt::Th( 'UE_SEARCH_TO', 'and' ) . ' ' . $this->getFieldTitle( $field, $user, 'text', $reason ), false, $maxVal, false, $showTime, ( $yDesc ? $yMax : $yMin ), ( $yDesc ? $yMin : $yMax ), $tooltip, $offset ), $reason, $field, $user );
					}

					$return					=	$this->_fieldSearchRangeModeHtml( $field, $user, $output, $reason, $value, $minHtml, $maxHtml, $list_compare_types );
				} elseif ( ! \in_array( $field->getString( 'name', '' ), [ 'registerDate', 'lastvisitDate', 'lastupdatedate' ], true ) ) {
					if ( $editBy === 2 ) {
						return parent::getField( $field, $user, $output, $reason, $list_compare_types );
					}

					[ $yMin, $yMax, $yDesc ]	=	$this->_yearsRange( $field, 0 );

					// Check for age validation:
					$tooltip				.=	$this->getDataAttributes( $field, $user, $output, $reason );

					$timeZone				=	null;

					if ( $displayBy === 3 ) { // Birthdate
						$timeZone			=	Application::User( $user->getInt( 'id', 0 ) )->getTimezone();
					}

					$return					=	$this->formatFieldValueLayout( ( new cbCalendars( $_CB_framework->getUi(), $field->params->getString( 'calendar_type' ), $dateFormat, $timeFormat ) )->cbAddCalendar( $field->get( 'name' ), $this->getFieldTitle( $field, $user, 'text', $reason ), $this->_isRequired( $field, $user, $reason ), $value, $this->_isReadOnly( $field, $user, $reason ), $showTime, ( $yDesc ? $yMax : $yMin ), ( $yDesc ? $yMin : $yMax ), $tooltip, $offset, $timeZone ), $reason, $field, $user )
											.	$this->_fieldIconsHtml( $field, $user, $output, $reason, null, $field->get( 'type' ), $value, 'input', null, true, $this->_isRequired( $field, $user, $reason ) && ! $this->_isReadOnly( $field, $user, $reason ) );
				}
				break;
			case 'json':
			case 'php':
			case 'xml':
			case 'csvheader':
			case 'fieldslist':
			case 'csv':
			default:
				$return						=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}

		return $return;
	}

	/**
	 * @param  FieldTable  $field
	 * @param  int         $outputMode
	 * @return array
	 */
	function _yearsRange( &$field, $outputMode )
	{
		if ( $field->get( 'type', null, GetterInterface::STRING ) == 'time' ) {
			return array( 0, 0, false );
		}

		$yMin					=	$this->_yearSetting( $field->params->get( 'year_min', '-110' ), $outputMode );
		$yMax					=	$this->_yearSetting( $field->params->get( 'year_max', '+25' ), $outputMode );
		$yDesc					=	false;

		// Reverse min and max year for age display or if min year is greater than max year:
		if ( ( $outputMode == 1 ) || ( $yMin > $yMax ) ) {
			$temp				=	$yMin;
			$yMin				=	$yMax;
			$yMax				=	$temp;
			$yDesc				=	true;
		}

		return array( $yMin, $yMax, $yDesc );
	}

	/**
	 * @param  string  $setParam
	 * @param  int     $outputMode
	 * @return int|null
	 */
	function _yearSetting( $setParam, $outputMode )
	{
		$yearSetting			=	trim( $setParam );
		$offset					=	null;
		$fullYear				=	null;

		if ( ! $yearSetting ) {
			$offset				=	0;
		} else {
			$sign				=	$yearSetting[0];

			if ( $sign == '+' ) {
				$offset			=	(int) substr( $yearSetting, 1 );
			} elseif ( $sign == '-' ) {
				$offset			=	- (int) substr( $yearSetting, 1 );
			} else {
				$fullYear		=	(int) $yearSetting;
			}
		}

		if ( $outputMode == 1 ) {
			if ( $offset === null ) {
				$offset			=	( $fullYear - (int) cbFormatDate( 'now', false, false, 'Y' ) );
			}

			return -$offset;
		} else {
			if ( $offset !== null ) {
				$fullYear		=	( (int) cbFormatDate( 'now', false, false, 'Y' ) + $offset );
			}

			return $fullYear;
		}
	}

	/**
	 * Labeller for title:
	 * Returns a field title
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output  'text' or: 'html', 'htmledit', (later 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist')
	 * @param  string      $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @return string
	 */
	public function getFieldTitle( &$field, &$user, $output, $reason )
	{
		$title			=	'';
		$byAge			=	( ( ( $output == 'html' ) || ( $output == 'rss' ) ) && ( $field->params->get( 'field_display_by', 0 ) > 0 ) ) || ( ( $reason == 'search' ) && ( $field->params->get( 'field_search_by', 0 ) == 1 ) );

		if ( $byAge ) {
			$title		=	$field->params->get( 'duration_title' );
		}

		if ( $title != '' ) {
			if ( $output === 'text' ) {
				return strip_tags( cbReplaceVars( $title, $user, true, true, array( 'reason' => $reason ) ) );
			} else {
				return cbReplaceVars( $title, $user, true, true, array( 'reason' => $reason ) );
			}
		} else {
			return parent::getFieldTitle( $field, $user, $output, $reason );
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

		if ( ( ! in_array( $field->name, array( 'registerDate', 'lastvisitDate', 'lastupdatedate' ) ) ) ) {
			foreach ( $field->getTableColumns() as $col ) {
				$value				=	stripslashes( cbGetParam( $postdata, $col ) );

				if ( $value && ( $field->params->getInt( 'field_edit_format', 0 ) === 2 ) ) {
					// Reformat from native to SQL
					if ( $field->getString( 'type', '' ) === 'datetime' ) {
						$value		=	Application::Date( (string) $value, 'UTC', ( strpos( (string) $value, 'T' ) !== false ? 'Y-m-d\TH:i' : 'Y-m-d H:i' ) )->format( 'Y-m-d H:i:s' );
					} elseif ( $field->getString( 'type', '' ) === 'time' ) {
						$value		=	Application::Date( (string) $value, 'UTC', 'H:i' )->format( 'H:i:s' );
					}
				}

				$validated			=	$this->validate( $field, $user, $col, $value, $postdata, $reason );

				if ( $value !== null ) {
					if ( $validated && isset( $user->$col ) && ( ( (string) $user->$col ) !== (string) $value ) && ! ( ( ( $user->$col === '0000-00-00' ) || ( $user->$col === '00:00:00' ) || ( $user->$col === '0000-00-00 00:00:00' ) ) && ( $value == '' ) ) ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
					}

					if ( $value === '' ) {
						// Convert empty string to zero date
						$value		=	Application::Database()->getNullDate( $field->getString( 'type' ) );
					}

					$user->$col		=	$value;
				}
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
	 * @return boolean                  True if validate, $this->_setErrorMSG if False
	 */
	public function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason )
	{
		$validate								=	parent::validate( $field, $user, $columnName, $value, $postdata, $reason );

		if ( $validate && ( $value !== null ) ) {
			if ( $field->get( 'type', null, GetterInterface::STRING ) == 'time' ) {
				$hour							=	substr( $value, 0, 2 );

				if ( ( $hour == '' ) || ( $hour == '00' ) ) {
					if ( $this->_isRequired( $field, $user, $reason ) ) {
						$this->_setValidationError( $field, $user, $reason, cbUnHtmlspecialchars( CBTxt::T( 'UE_REQUIRED_ERROR', 'This field is required!' ) ) );

						$validate				=	false;
					}
				}
			} else {
				$year							=	substr( $value, 0, 4 );

				if ( ( $year == '' ) || ( $year == '0000' ) ) {
					if ( $this->_isRequired( $field, $user, $reason ) ) {
						$this->_setValidationError( $field, $user, $reason, cbUnHtmlspecialchars( CBTxt::T( 'UE_REQUIRED_ERROR', 'This field is required!' ) ) );

						$validate				=	false;
					}
				} else {
					// check range:
					list( $yMin, $yMax )		=	$this->_yearsRange( $field, 0 );

					if ( ( $year < $yMin ) || ( $year > $yMax ) ) {
						$this->_setValidationError( $field, $user, $reason, sprintf( CBTxt::T( 'UE_YEAR_NOT_IN_RANGE', 'Year %s is not between %s and %s' ), (int) $year, (int) $yMin, (int) $yMax ) );
						$validate				=	false;
					}

					// check age:
					if ( ( $field->get( 'type', null, GetterInterface::STRING ) == 'date' ) && in_array( $field->params->get( 'field_display_by', 0, GetterInterface::INT ), array( 1, 3 ) ) ) {
						$fieldId				=	$field->get( 'fieldid', 0, GetterInterface::INT );
						$minAge					=	$field->params->get( 'age_min', 0, GetterInterface::INT );
						$maxAge					=	$field->params->get( 'age_max', 0, GetterInterface::INT );

						if ( $minAge || $maxAge ) {
							$dateDiff			=	Application::Date( 'now', 'UTC' )->diff( $value );
							$age				=	0;

							if ( $dateDiff ) {
								$age			=	(int) $dateDiff->y;

								if ( $age < 0 ) {
									$age		=	0;
								}
							}

							if ( $minAge && $maxAge ) {
								if ( ( $age < $minAge ) || ( $age > $maxAge ) ) {
									// CBTxt::T( 'AGE_TOO_YOUNG_OR_OLD', 'Age [age] is too young or old. You must be at least [min] years old, but not older than [max].', array( '[age]' => $age, '[min]' => $minAge, '[max]' => $maxAge ) )
									$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'FIELD_' . $fieldId . '_AGE_TOO_YOUNG_OR_OLD AGE_TOO_YOUNG_OR_OLD', 'Age [age] is too young or old. You must be at least [min] years old, but not older than [max].', array( '[age]' => $age, '[min]' => $minAge, '[max]' => $maxAge ) ) );

									$validate	=	false;
								}
							} elseif ( $minAge ) {
								if ( $age < $minAge ) {
									// CBTxt::T( 'AGE_TOO_YOUNG', 'Age [age] is too young. You must be at least [min] years old.', array( '[age]' => $age, '[min]' => $minAge, '[max]' => $maxAge ) )
									$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'FIELD_' . $fieldId . '_AGE_TOO_YOUNG AGE_TOO_YOUNG', 'Age [age] is too young. You must be at least [min] years old.', array( '[age]' => $age, '[min]' => $minAge, '[max]' => $maxAge ) ) );

									$validate	=	false;
								}
							} elseif ( $maxAge ) {
								if ( $age > $maxAge ) {
									// CBTxt::T( 'AGE_TOO_OLD', 'Age [age] is too old. You must be no more than [max] years old.', array( '[age]' => $age, '[min]' => $minAge, '[max]' => $maxAge ) )
									$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'FIELD_' . $fieldId . '_AGE_TOO_OLD AGE_TOO_OLD', 'Age [age] is too old. You must be no more than [max] years old.', array( '[age]' => $age, '[min]' => $minAge, '[max]' => $maxAge ) ) );

									$validate	=	false;
								}
							}
						}
					}
				}
			}
		}

		return $validate;
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
		global $_CB_framework;

		$searchBy										=	$field->params->get( 'field_search_by', 0 );

		list( $yMinMin, $yMaxMax )						=	$this->_yearsRange( $field, $searchBy );

		$query											=	array();

		foreach ( $field->getTableColumns() as $col ) {
			$minNam										=	$col . '__minval';
			$maxNam										=	$col . '__maxval';
			$searchMode									=	$this->_bindSearchRangeMode( $field, $searchVals, $postdata, $minNam, $maxNam, $list_compare_types );

			if ( $searchMode ) {
				if ( $searchBy == 1 ) {
					// search by years:
					if ( $field->type == 'datetime' ) {
						list( $y, $c, $d, $h, $m, $s )	=	sscanf( $_CB_framework->getUTCDate( 'Y-m-d H:i:s' ), '%d-%d-%d %d:%d:%d' );
					} elseif ( $field->type == 'time' ) {
						list( $h, $m, $s )				=	sscanf( $_CB_framework->getUTCDate( 'H:i:s' ), '%d:%d:%d' );
						$y								=	null;
						$c								=	null;
						$d								=	null;
					} else {
						list( $y, $c, $d )				=	sscanf( $_CB_framework->getUTCDate( 'Y-m-d' ), '%d-%d-%d' );
						$h								=	null;
						$m								=	null;
						$s								=	null;
					}

					$minValIn							=	(int) cbGetParam( $postdata, $minNam, 0 );
					$maxValIn							=	(int) cbGetParam( $postdata, $maxNam, 0 );
					$ageMin								=	$minValIn;
					$ageMax								=	$maxValIn;

					if ( $ageMin == $ageMax ) {
						// We're searching for an exact age (e.g. min 30 and max 30) which causes >= 30 <= 30 and does not make sense for age date ranges; so lets add 1 year to the max:
						$ageMax++;
					}

					if ( ( $ageMax && ( $ageMax <= $yMaxMax ) ) && ( $ageMin && ( $ageMin > $yMinMin ) ) ) {
						$yMax							=	( $y - $ageMin );

						if ( $field->type == 'datetime' ) {
							$maxVal						=	sprintf( '%04d-%02d-%02d %02d:%02d:%02d', $yMax, $c, $d, $h, $m, $s );
						} elseif ( $field->type == 'time' ) {
							$maxVal						=	sprintf( '%02d:%02d:%02d', $h, $m, $s );
						} else {
							$maxVal						=	sprintf( '%04d-%02d-%02d', $yMax, $c, $d );
						}
					} else {
						$maxVal							=	null;
					}

					if ( ( $ageMin && ( $ageMin >= $yMinMin ) ) && ( $ageMax && ( $ageMax < $yMaxMax ) ) ) {
						$yMin							=	( $y - $ageMax );

						if ( $field->type == 'datetime' ) {
							$minVal						=	sprintf( '%04d-%02d-%02d %02d:%02d:%02d', $yMin, $c, $d, $h, $m, $s );
						} elseif ( $field->type == 'time' ) {
							$minVal						=	sprintf( '%02d:%02d:%02d', $h, $m, $s );
						} else {
							$minVal						=	sprintf( '%04d-%02d-%02d', $yMin, $c, $d );
						}
					} else {
						$minVal							=	null;
					}
				} else {
					$minVal								=	stripslashes( cbGetParam( $postdata, $minNam ) );
					$maxVal								=	stripslashes( cbGetParam( $postdata, $maxNam ) );

					if ( $searchBy === 3 ) {
						// Reformat from native to SQL
						if ( $minVal ) {
							if ( $field->getString( 'type', '' ) === 'datetime' ) {
								$minVal					=	Application::Date( (string) $minVal, 'UTC', ( strpos( (string) $minVal, 'T' ) !== false ? 'Y-m-d\TH:i' : 'Y-m-d H:i' ) )->format( 'Y-m-d H:i:s' );
							} elseif ( $field->getString( 'type', '' ) === 'time' ) {
								$minVal					=	Application::Date( (string) $minVal, 'UTC', 'H:i' )->format( 'H:i:s' );
							}
						}

						if ( $maxVal ) {
							if ( $field->getString( 'type', '' ) === 'datetime' ) {
								$maxVal					=	Application::Date( (string) $maxVal, 'UTC', ( strpos( (string) $maxVal, 'T' ) !== false ? 'Y-m-d\TH:i' : 'Y-m-d H:i' ) )->format( 'Y-m-d H:i:s' );
							} elseif ( $field->getString( 'type', '' ) === 'time' ) {
								$maxVal					=	Application::Date( (string) $maxVal, 'UTC', 'H:i' )->format( 'H:i:s' );
							}
						}
					}

					$minValIn							=	$minVal;
					$maxValIn							=	$maxVal;
				}

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
					$min->name							=	$col;
					$min->table							=	$field->table;
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
					$max->name							=	$col;
					$max->table							=	$field->table;
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
					$sql->name							=	$col;
					$sql->table							=	$field->table;
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
		}

		return $query;
	}
}