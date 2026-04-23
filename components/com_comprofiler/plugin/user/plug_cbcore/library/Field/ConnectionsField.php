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
use cbConnection;
use cbSqlQueryPart;

\defined( 'CBLIB' ) or die();

class ConnectionsField extends CounterField
{
	/**
	 * Formatter:
	 * Returns a field row in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output      'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $formatting  'tr', 'td', 'div', 'span', 'none',   'table'??
	 * @param  string      $reason      'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getFieldRow( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		global $ueConfig;

		if ( $ueConfig['allowConnections'] ) {
			return parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
		}
		return null;
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

		$oReturn							=	null;

		if ( $ueConfig['allowConnections'] && is_object( $user ) ) {
			$cbCon							=	new cbConnection( $_CB_framework->myId() );
			$value							=	$cbCon->getConnectionsCount( $user->id );

			switch ( $output ) {
				case 'html':
				case 'rss':
					$oReturn				=	$this->formatFieldValueLayout( $value, $reason, $field, $user );
					break;

				case 'htmledit':
					// $oReturn				=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
					$oReturn				=	null;		//TBD for now no searches...not optimal in SQL anyway.
					break;

				case 'json':
				case 'php':
				case 'xml':
				case 'csvheader':
				case 'fieldslist':
				case 'csv':
				default:
					$oReturn				=	$this->_formatFieldOutputIntBoolFloat( $field->name, $value, $output );;
					break;
			}
		}
		return $oReturn;
	}

	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
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
		global $ueConfig;

		$searchMode						=	$this->_bindSearchMode( $field, $searchVals, $postdata, 'none', $list_compare_types );
		$query							=	array();
		if ( $ueConfig['allowConnections'] && $searchMode ) {
			$col						=	$field->name;
			$minNam						=	$col . '__minval';
			$maxNam						=	$col . '__maxval';
			$minVal						=	(int) cbGetParam( $postdata, $minNam, 0 );
			$maxVal						=	(int) cbGetParam( $postdata, $maxNam, 0 );
			if ( $minVal && ( $minVal != 0 ) ) {
				$searchVals->$minNam	=	$minVal;
				$query[]				=	$this->_intToSql( $field, $col, $minVal, '>=', $searchMode );
			}
			if ( $maxVal && ( $maxVal != 0 ) ) {
				$searchVals->$maxNam	=	$maxVal;
				$query[]				=	$this->_intToSql( $field, $col, $maxVal, '<=', $searchMode );
			}
		}
		return $query;
	}
	/**
	 * Internal function to build SQL request
	 * @access private
		<data name="change_logs" type="sql:count" distinct="id"  table="#__cpay_history" class="cbpaidHistory">
			<joinkeys dogroupby="true">
				<column name="table_name"   operator="=" value="#__cpay_payment_baskets" type="sql:field" valuetype="const:string" />
				<column name="table_key_id" operator="=" value="id" type="sql:field" valuetype="sql:field" />
			</joinkeys>
		</data>

		<where>
			<column name="id"     operator="=" value="plan_id" type="int"       valuetype="sql:formula">
				<data name="plan_id" type="sql:field" table="#__cpay_payment_items" class="cbpaidPayementItem" key="plan_id" value="id" valuetype="sql:field">
					<data name="basket_id" type="sql:field" table="#__cpay_payment_baskets" class="cbpaidPayementBasket" key="id" value="payment_basket_id" valuetype="sql:field">
						<where>
							<column name="payment_status" operator="=" value="Completed" type="sql:field" valuetype="const:string" />
						</where>
					</data>
				</data>
		    </column>
		</where>

		<column name="id"     operator="=" value="plan_id" type="int"       valuetype="sql:formula">
			<data name="plan_id" type="sql:field" table="#__cpay_payment_items" class="cbpaidPayementItem" key="plan_id" value="id" valuetype="sql:field">
				<data name="basket_id" type="sql:field" table="#__cpay_payment_baskets" class="cbpaidPayementBasket" key="id" value="payment_basket_id" valuetype="sql:field">
					<where>
						<column name="payment_status" operator="=" value="Completed" type="sql:field" valuetype="const:string" />
					</where>
				</data>
			</data>
	    </column>

	 * @param  FieldTable      $field
	 * @param  string          $col
	 * @param  int             $value
	 * @param  string          $operator
	 * @param  string          $searchMode
	 * @return cbSqlQueryPart
	 */
	function _intToSql( &$field, $col, $value, $operator, $searchMode )
	{
		$value							=	(int) $value;
		// $this->validate( $field, $user, $col, $value, $postdata, $reason );
		$sql							=	new cbSqlQueryPart();
		$sql->tag						=	'column';
		$sql->name						=	$field->name;
		$sql->table						=	'#__comprofiler_members';
		$sql->type						=	'sql:count';
		$sql->distinct					=	'memberid';
		$sql->operator					=	$operator;
		$sql->value						=	$value;
		$sql->valuetype					=	'const:int';
		$sql->searchmode				=	$searchMode;
		$sql->key						=	'id';
		$sql->keyvalue					=	'referenceid';
		return $sql;
	}
}