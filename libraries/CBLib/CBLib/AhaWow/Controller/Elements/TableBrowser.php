<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 11/26/13 1:02 AM $
* @package CBLib\AhaWow\Controller\Elements
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CBLib\AhaWow\Controller\Elements;

use CBLib\AhaWow\Access;
use CBLib\AhaWow\Controller\ActionController;
use CBLib\AhaWow\Controller\DrawController;
use CBLib\AhaWow\Model\XmlQuery;
use CBLib\AhaWow\Model\XmlTypeCleanQuote;
use CBLib\AhaWow\View\RegistryEditView;
use CBLib\Application\Application;
use CBLib\Input\InputInterface;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\RegistryInterface;
use CBLib\Session\SessionStateInterface;
use CBLib\Registry\ParamsInterface;
use CBLib\Xml\SimpleXMLElement;
use CBLib\Database\DatabaseDriverInterface;
use CBLib\Database\Table\TableInterface;
use CBLib\Database\Table\OrderedTable;
use CBLib\Database\Table\CheckedOrderedTable;
use CB\Database\Table\PluginTable;
use CBLib\Input\Get;
use CB\Database\Table\UserTable;

defined('CBLIB') or die();

/**
 * CBLib\AhaWow\Controller\Elements\TableBrowser Class implementation
 *
 */
class TableBrowser {
	/** database
	 * @var DatabaseDriverInterface */
	public $_db;
	/**
	 * xml <tablebrowser> node element
	 * @var SimpleXMLElement */
	public $_tableBrowserModel;
	/**
	 * xml <types> node element
	 * @var SimpleXMLElement */
	public $_types;
	/**
	 * xml <actions> node element
	 * @var SimpleXMLElement */
	public $_actions;
	/**
	 * xml <views> node element
	 * @var SimpleXMLElement */
	public $_views;
	public $_options;
	public $_pluginParams;
	/** plugin object
	 * @var PluginTable */
	public $_pluginObject = null;
	/** @var int */
	public $_tabid = null;

	public $name = null;
	public $table;
	public $label;
	public $description;
	public $class;
	public $fields;

	/**
	 * xml <rows> node element
	 * @var SimpleXMLElement */
	public $listFieldsRows;
	/**
	 * xml <where> node element
	 * @var SimpleXMLElement */
	public $whereColumns;
	/**
	 * xml <orderby> node element
	 * @var SimpleXMLElement */
	public $orderbyfields;
	/**
	 * xml <groupby> node element
	 * @var SimpleXMLElement */
	public $groupbyfields;
	/**
	 * xml <quicksearchfields> node element
	 * @var SimpleXMLElement */
	public $quicksearchfields;

	public $rows;
	public $total;

	public $limitstart;
	public $limit;
	public $limits;
	public $search;
	public $orderby;
	/**
	 * xml <filters> node element
	 * @var SimpleXMLElement */
	public $filters;
	/**
	 * xml <batchprocess> node element
	 * @var SimpleXMLElement */
	public $batchprocess;
	/**
	 * xml <export> node element
	 * @var SimpleXMLElement */
	public $export;
	/**
	 * xml <import> node element
	 * @var SimpleXMLElement */
	public $import;
	/**
	 * xml <statistics> node element
	 * @var SimpleXMLElement */
	public $statistics;
	/**
	 * xml <toolbarmenu> node element
	 * @var SimpleXMLElement */
	public $toolbarmenu;
	/**
	 * data of each statistic, indexed by statistics name attribute
	 *
	 * @var array of array( 'values' => stdClass, 'view' => SimpleXMLElement )
	 */
	public $_statisticsToDisplay;
	public $_batchPossibilitesArray;
	public $_filterPossibilitesArray;
	public $_filtered;

	/**
	 * @var  RegistryEditView  $registryEditVew
	 */
	protected $registryEditVew;

	/**
	 * @var InputInterface
	 */
	protected $input			=	null;

	/**
	 * @var SessionStateInterface
	 */
	protected $state			=	null;

	/**
	 * Possible operators for advanced search
	 * @var array
	 */
	protected $possibleOperators			=	array( '=', '<>||ISNULL', '<', '>', '<=', '>=', 'REGEXP', 'NOT REGEXP||ISNULL', 'LIKE', 'NOT LIKE||ISNULL', 'IN', 'NOT IN||ISNULL' );

	/**
	 * Constructor
	 *
	 * @param  InputInterface           $input            The user form input
	 * @param  SimpleXMLElement         $controllerModel  The model of the controller
	 * @param  array                    $options          The routing options
	 * @param  RegistryInterface        $pluginParams     The parameters of the plugin
	 * @param  SimpleXMLElement         $types            The types definitions in XML
	 * @param  SimpleXMLElement         $actions          The actions definitions in XML
	 * @param  SimpleXMLElement         $views            The views definitions in XML
	 * @param  PluginTable|null         $pluginObject     The plugin object
	 * @param  int|null                 $tabId            The tab id (if there is one)
	 * @param  DatabaseDriverInterface  $db               The tab id (if there is one)
	 * @param  RegistryEditView         $registryEditVew  The Registry Edit View (the calling object)
	 */
	public function __construct( InputInterface $input, SimpleXMLElement $controllerModel, $options,
								 RegistryInterface $pluginParams, SimpleXMLElement $types, SimpleXMLElement $actions,
								 SimpleXMLElement $views, PluginTable $pluginObject = null, $tabId = null,
								 DatabaseDriverInterface $db = null, RegistryEditView $registryEditVew = null )
	{
		$this->input				=	$input;
		$this->state				=	Application::DI()->get( 'CBLib\Session\SessionStateInterface', array( 'input' => $this->input ) );
		$this->_tableBrowserModel	=	$controllerModel;
		$this->_options				=	$options;
		$this->_pluginParams		=	$pluginParams;
		$this->_types				=	$types;
		$this->_actions				=	$actions;
		$this->_views				=	$views;
		$this->_pluginObject		=	$pluginObject;
		$this->_tabid				=	$tabId;
		$this->_db					=	$db;
		$this->registryEditVew		=	$registryEditVew;
	}

	/**
	 * Parses the XML
	 *
	 * @return void
	 */
	protected function parseXML( ) {
		global $_CB_framework;

		$this->registryEditVew->extendParamConditions( $this->_tableBrowserModel );

		$this->name					=	$this->_tableBrowserModel->attributes( 'name' );
		$this->table				=	$this->_tableBrowserModel->attributes( 'table' );
		$this->label				=	$this->_tableBrowserModel->attributes( 'label' );
		$this->description			=	$this->_tableBrowserModel->attributes( 'description' );
		$this->class				=	$this->_tableBrowserModel->attributes( 'class' );

		$this->fields				=	array();
		$this->listFieldsRows		=	$this->_tableBrowserModel->getElementByPath( 'listfields/rows' );

		$this->orderbyfields		=	$this->_tableBrowserModel->getElementByPath( 'orderby' );
		$this->groupbyfields		=	$this->_tableBrowserModel->getElementByPath( 'groupby' );
		$this->quicksearchfields	=	$this->_tableBrowserModel->getElementByPath( 'quicksearchfields' );
		$this->filters				=	$this->_tableBrowserModel->getElementByPath( 'filters' );
		$this->whereColumns			=	$this->_tableBrowserModel->getElementByPath( 'where' );
		$this->batchprocess			=	$this->_tableBrowserModel->getElementByPath( 'batchprocess' );
		$this->export				=	$this->_tableBrowserModel->getElementByPath( 'export' );

		if ( $this->export ) {
			$this->registryEditVew->extendParamAttributes( $this->export );
		}

		$this->import				=	$this->_tableBrowserModel->getElementByPath( 'import' );

		if ( $this->import ) {
			$this->registryEditVew->extendParamAttributes( $this->import );
		}

		$this->statistics			=	$this->_tableBrowserModel->getElementByPath( 'statistics' );
		$this->toolbarmenu			=	$this->_tableBrowserModel->getElementByPath( 'toolbarmenu' );

		if ( $this->toolbarmenu ) {
			$this->registryEditVew->extendParamConditions( $this->toolbarmenu );
		}

		$listFieldsPager			=	$this->_tableBrowserModel->getElementByPath( 'listfields/paging' );
		if ( $listFieldsPager ) {
			$this->registryEditVew->extendParamAttributes( $listFieldsPager );

		}

		if ( $this->listFieldsRows ) {
			$this->registryEditVew->extendParamAttributes( $this->listFieldsRows );

			$limit					=	$this->listFieldsRows->attributes( 'limit' );

			if ( ! $limit ) {
				$limit				=	$_CB_framework->getCfg( 'list_limit' );
			}

			$this->limit			=	$limit;

			$limits					= $this->listFieldsRows->attributes( 'limits' );
			if ( $limits ) {
				$this->limits		= explode( ',', $limits );
			}

			foreach ( $this->listFieldsRows as $field ) {
				/** @var SimpleXMLElement $field */
				if ( $field->getName() != 'field' ) {
					continue;
				}

				$this->registryEditVew->resolveXmlParamType( $field );

				$allowOrdering		=	$field->attributes( 'allowordering' );

				if ( $allowOrdering ) {
					$name			=	$field->attributes( 'name' );
					$label			=	CBTxt::T( $field->attributes( 'label' ) );
					$ordering		=	explode( ',', $allowOrdering );

					if ( in_array( 'ascending', $ordering ) && ( ! $this->orderbyfields->getChildByNameAttr( 'ordergroup', 'name', $name . '_asc' ) ) ) {
						$asc		=	'<?xml version="1.0" encoding="UTF-8"?>'
									.	'<ordergroup name="' . htmlspecialchars( $name ) . '_asc" label="' . htmlspecialchars( CBTxt::T( 'FIELD_ASCENDING', '[field] ascending', array( '[field]' => ( $label != '' ? $label : $name ) ) ) ) . '">'
									.		'<field name="' . htmlspecialchars( $name ) . '" ordering="ASC" />'
									.	'</ordergroup>';

						$ascXml		=	new SimpleXMLElement( $asc );

						$this->orderbyfields->addChildWithDescendants( $ascXml );
					}

					if ( in_array( 'descending', $ordering ) && ( ! $this->orderbyfields->getChildByNameAttr( 'ordergroup', 'name', $name . '_desc' ) ) ) {
						$desc		=	'<?xml version="1.0" encoding="UTF-8"?>'
									.	'<ordergroup name="' . htmlspecialchars( $name ) . '_desc" label="' . htmlspecialchars( CBTxt::T( 'FIELD_DESCENDING', '[field] descending', array( '[field]' => ( $label != '' ? $label : $name ) ) ) ) . '">'
									.		'<field name="' . htmlspecialchars( $name ) . '" ordering="DESC" />'
									.	'</ordergroup>';

						$descXml	=	new SimpleXMLElement( $desc );

						$this->orderbyfields->addChildWithDescendants( $descXml );
					}
				}
			}
		}
	}

	/**
	 * Loads the filters
	 *
	 * @return void
	 */
	protected function loadFilters() {
		$this->_filterPossibilitesArray		=	$this->loadXMLItems( $this->filters, 'filter' );
	}

	/**
	 * Loads the batchprocess
	 *
	 * @return void
	 */
	protected function loadBatchProcess() {
		$this->_batchPossibilitesArray		=	$this->loadXMLItems( $this->batchprocess, 'batch' );
	}

	/**
	 * Loads specifc xml row and its children
	 *
	 * @param  SimpleXMLElement  $row
	 * @param  string            $type
	 * @return array
	 */
	protected function loadXMLItems( $row = null, $type = null ) {
		$items							=	array();

		if ( $row && $type && Access::authorised( $row ) ) {
			foreach ( $row->children() as $o ) {
				/** @var $o SimpleXMLElement */
				if ( $o->getName() == $type ) {

					if ( ( ! Access::authorised( $o ) ) && ( ! ( ( $o->getName() == 'if' ) && ( $o->attributes( 'type' ) == 'permission' ) ) ) ) {
						continue;
					}

					$basetype			=	null;
					$valueType			=	null;
					$fieldValuesInDb	=	$this->_getFieldValues( $o, $basetype, $valueType );

					if ( $fieldValuesInDb && ( count( $fieldValuesInDb ) > 0 ) ) {
						$items[$type.'_'.$o->attributes( 'name' )]['selectValues']	=	$fieldValuesInDb;
					} else {
						$items[$type.'_'.$o->attributes( 'name' )]['selectValues']	=	null;
					}

					$items[$type.'_'.$o->attributes( 'name' )]['xml']				=	$o;
					$items[$type.'_'.$o->attributes( 'name' )]['xmlparent']			=	$row;
					$items[$type.'_'.$o->attributes( 'name' )]['type']				=	$o->attributes( 'type' );
					$items[$type.'_'.$o->attributes( 'name' )]['basetype']			=	$basetype;
					$items[$type.'_'.$o->attributes( 'name' )]['valuetype']			=	$valueType;
					$items[$type.'_'.$o->attributes( 'name' )]['blanktext']			=	$o->attributes( 'blanktext' );
					$items[$type.'_'.$o->attributes( 'name' )]['default']			=	$o->attributes( 'default' );
					$items[$type.'_'.$o->attributes( 'name' )]['name']				=	$o->attributes( 'name' );

					if ( $o->attributes( 'value' ) ) {
						$items[$type.'_'.$o->attributes( 'name' )]['valuefield']	=	$o->attributes( 'value' );
					} else {
						$items[$type.'_'.$o->attributes( 'name' )]['valuefield']	=	$o->attributes( 'name' );
					}

					if ( $o->attributes( 'operator' ) ) {
						$items[$type.'_'.$o->attributes( 'name' )]['operator']		=	$o->attributes( 'operator' );
					} else {
						if ( $o->attributes( 'multiple' ) == 'true' ) {
							$defaultOperator										=	'IN';
						} else{
							$defaultOperator										=	'=';
						}

						$items[$type.'_'.$o->attributes( 'name' )]['operator']		=	$defaultOperator;
					}

					$defaultValue		=	$o->attributes( 'default' );

					$items[$type.'_'.$o->attributes( 'name' )]['value']				=	$defaultValue;
					$items[$type.'_'.$o->attributes( 'name' )]['internalvalue']		=	$defaultValue;

					if ( $items[$type.'_'.$o->attributes( 'name' )]['selectValues'] ) {
						foreach ( $items[$type.'_'.$o->attributes( 'name' )]['selectValues'] as $selObj ) {
							if ( (string) $selObj->value === (string) $defaultValue ) {
								if ( isset( $selObj->internalvalue ) ) {
									$items[$type.'_'.$o->attributes( 'name' )]['internalvalue']		=	$selObj->internalvalue;
								}

								if ( isset( $selObj->operator ) ) {
									$items[$type.'_'.$o->attributes( 'name' )]['operator']			=	$selObj->operator;
								}
								break;
							}
						}
					}
				}
			}
		}

		return $items;
	}

	/**
	 * Loads the rows
	 *
	 * @return void
	 */
	protected function loadRows( ) {
		$xmlsql					=	$this->_prepareXmlSqlQuery();

		if ( $this->listFieldsRows ) {
			$this->total		=	$xmlsql->queryCount();								// get the total number of records
			if ($this->total <= $this->limitstart) {
				$this->limitstart = 0;
			}
		}

		try
		{
			$this->rows			=	$xmlsql->queryLoadObjectsList( $this->_tableBrowserModel , $this->limitstart, $this->limit );	// get the records
		}
		catch ( \RuntimeException $e )
		{
			global $_CB_framework;
			$_CB_framework->enqueueMessage( CBTxt::T( 'Database loading error. Check and repair your database in Components / Community Builder / Tools.' )
				. ( Application::MyUser()->isSuperAdmin() ? '<br />SQL Error (visible to super-admins only): ' . $e->getMessage() : '' ),
				'error' );

			$this->rows			=	array();
		}

		// statistics:

		if ( $this->statistics ) {

			// Check if ACL authorizes to view and to use that element:
			if ( Access::authorised( $this->statistics ) ) {
				$xmlsql			=	$this->_prepareXmlSqlQuery( false );
				foreach ( $this->statistics->children() as $statistic ) {
					/** @var $statistic SimpleXMLElement */
					if ( $statistic->getName() == 'statistic') {
						$result	=	$xmlsql->processQuery_statistic( $statistic );		// // <statistic><where /><model />
						if ( $result ) {
							$this->_statisticsToDisplay[$statistic->attributes( 'name' )]['values']	=	$result;
							$this->_statisticsToDisplay[$statistic->attributes( 'name' )]['view']	=	$statistic->getElementByPath( 'view' );
						}
					}
				}
			}
		}
	}

	/**
	 * Checks if supplied value is an empty array, null, or empty string
	 *
	 * @param  int|string|array  $value
	 * @return bool
	 */
	private function isValueEmpty( $value ) {
		if ( is_array( $value ) ) {
			return ( count( $value ) == 0 );
		}

		return ( $value == '' );
	}

	//TBD: Move this to cbpaidViewExtended class, and add using base attribute for type 'field_show_only_if_selected' in _form_field_show_only_if_select: see the T B D in _form_field_show_only_if_select.
	/**
	 * Generic function to get an array of option values for lists, radios, checkboxes params and filter fields:
	 *
	 * @param  SimpleXMLElement  $o
	 * @param  string              $basetype   RETURNED: base type
	 * @param  string              $valueType  RETURNED: valuetype type
	 * @return array|null
	 */
	protected function _getFieldValues( &$o, &$basetype, &$valueType ) {
		$valueType					=	$o->attributes( 'valuetype' );
		$fieldValuesInDb			=	null;

		$this->registryEditVew->resolveXmlParamType( $o );

		if ( $o->attributes( 'base' ) ) {
			$basetype				=	$o->attributes( 'base' );
		} else {
			$basetype				=	$o->attributes( 'type' );
		}

		switch ( $o->attributes( 'type' ) ) {
			case 'data':
				$data						=	$o->getElementByPath( 'data' );
				if ( $data ) {
					$dataTable				=	$data->attributes( 'table' );
					if ( ! $dataTable ) {
						$dataTable			=	$this->table;
					}

					$xmlsql					=	new XmlQuery( $this->_db, $dataTable, $this->_pluginParams );
					$xmlsql->process_orderby( $data->getElementByPath( 'orderby') );							// <data><orderby><field> fields
					$xmlsql->process_fields(  $data->getElementByPath( 'rows') );								// <data><rows><field> fields
					$xmlsql->process_where(   $data->getElementByPath( 'where') );								// <data><where><column> fields
					$groupby				=	$data->getElementByPath( 'groupby' );
					$xmlsql->process_groupby( ( $groupby ? $groupby : 'value' ) );								// <data><groupby><field> fields
					$fieldValuesInDb		=	$xmlsql->queryLoadObjectsList( $data );		// get the records
					// check for type="firstwords":
					$rows					=	$data->getElementByPath( 'rows');
					/** @var $rows SimpleXMLElement|null */
					if ( $rows ) {
						$textField			=	$rows->getChildByNameAttr( 'field', 'as', 'text' );
						/** @var $textField SimpleXMLElement|null */
						if ( $textField ) {
							if ( $textField->attributes( 'type' ) == 'firstwords' ) {
								$size		=	$textField->attributes( 'size' );
								if ( ! $size ) {
									$size	=	45;
								}
								foreach (array_keys( $fieldValuesInDb ) as $k ) {
									$strippedContent			=	trim( (string) $fieldValuesInDb[$k]->text );
									if ( cbIsoUtf_strlen( $strippedContent ) > $size ) {
										$strippedContent		=	cbIsoUtf_substr( $strippedContent, 0, $size ) . '...';
									}
									$fieldValuesInDb[$k]->text	=	$strippedContent;
								}

							}
						}
					}
					$data->addAttribute( 'dataprocessed', 'true' );
				} else {
					// echo 'filter type is data but no child data present !';
					$fieldName				=	$o->attributes( 'name' );
					if ( $o->attributes( 'value' ) ) {
						$valueFieldName		=	$o->attributes( 'value' );
					} else {
						$valueFieldName		=	$fieldName;
					}
					$dataTable				=	$o->attributes( 'table' );
					if ( ! $dataTable ) {
						$dataTable			=	$this->table;
					}
					$data				=	new SimpleXMLElement( <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<table table="$dataTable">
	<rows>
		<field name="$valueFieldName" as="value" type="sql:field" />
		<field name="$fieldName" as="text" type="sql:field" />
	</rows>
	<orderby>
		<field name="$fieldName" ordering="ASC" />
	</orderby>
	<groupby>
		<field name="$fieldName" />
	</groupby>
</table>
EOT
					);

					$xmlsql				=	new XmlQuery( $this->_db, $dataTable, $this->_pluginParams );
					$xmlsql->process_orderby( $data->getElementByPath( 'orderby') );							// <data><orderby><field> fields
					$xmlsql->process_fields(  $data->getElementByPath( 'rows') );								// <data><rows><field> fields
					$xmlsql->process_where(   $data->getElementByPath( 'where') );								// <data><where><column> fields
					$groupby			=	$data->getElementByPath( 'groupby' );
					$xmlsql->process_groupby( ( $groupby ? $groupby : 'value' ) );								// <data><groupby><field> fields
					$fieldValuesInDb	=	$xmlsql->queryLoadObjectsList( $data );		// get the records
				}

				$this->complementWithOptions( $fieldValuesInDb, $o );
				break;

			case 'field_show_only_if_selected':
				break;

			case 'list':
			case 'radio':
			case 'checkbox':
			case 'checkmark':
			case 'published':
			case 'usergroup':
			case 'viewaccesslevel':
			case 'tag':
				foreach ( $o->children() as $option ) {
					/** @var $option SimpleXMLElement */
					if ( $option->getName() == 'option' ) {
						$fieldValuesInDb[]			=	$this->optionToSelObject( $option );
					}
				}
				break;

			case 'field':
				global $_CB_database;

				$key							=	$o->attributes( 'key' );

				if ( ! $key ) {
					$key						=	'fieldid';
				}

				$query							=	"SELECT f." . $_CB_database->NameQuote( $key ) . " AS value"
												.	", f." . $_CB_database->NameQuote( 'name' )  . ' AS ' . $_CB_database->NameQuote( 'index' )
												.	", f." . $_CB_database->NameQuote( 'title' ) . ' AS ' . $_CB_database->NameQuote( 'text' )
												.	", f." . $_CB_database->NameQuote( 'table' ) . ' AS ' . $_CB_database->NameQuote( 'table' )
												.	", " . $_CB_database->Quote( 'id' ) . ' AS ' . $_CB_database->NameQuote( 'table_key' )
												.	", " . $_CB_database->Quote( '=' ) . " AS operator"
												.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_fields' ) . " AS f"
												.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_tabs' ) . " AS t"
												.	" ON t." . $_CB_database->NameQuote( 'tabid' ) . " = f." . $_CB_database->NameQuote( 'tabid' )
												.	"\n WHERE f." . $_CB_database->NameQuote( 'published' ) . " = 1"
												.	"\n AND f." . $_CB_database->NameQuote( 'name' ) . " != " . $_CB_database->Quote( 'NA' )
												.	"\n AND f." . $_CB_database->NameQuote( 'tablecolumns' ) . " != " . $_CB_database->Quote( '' )
												.	"\n ORDER BY t." . $_CB_database->NameQuote( 'position' ) . ", t." . $_CB_database->NameQuote( 'ordering' ) . ", f." . $_CB_database->NameQuote( 'ordering' );
				$_CB_database->setQuery( $query );
				$fieldValuesInDb				=	$_CB_database->loadObjectList();

				if ( $key == 'tablecolumns' ) {
					foreach ( $fieldValuesInDb as $k => $fieldValueInDb ) {
						$tableColumns			=	explode( ',', $fieldValueInDb->value );

						if ( count( $tableColumns ) <= 1 ) {
							continue;
						}

						foreach ( $tableColumns as $tableColumn ) {
							$cOpt				=	clone $fieldValueInDb;
							$cOpt->value		=	$tableColumn;
							$cOpt->index		=	$tableColumn;

							$fieldValuesInDb[]	=	$cOpt;
						}

						unset( $fieldValuesInDb[$k] );
					}

					$fieldValuesInDb			=	array_values( $fieldValuesInDb );
				}
				break;

			default:
				if ( substr( $o->attributes( 'type' ), 0, 4 ) == 'sql:' ) {
					// get list for dropdown filter

					$fieldName				=	$o->attributes( 'name' );
					if ( $o->attributes( 'value' ) ) {
						$valueFieldName		=	$o->attributes( 'value' );
					} else {
						$valueFieldName		=	$fieldName;
					}
					$dataTable				=	$o->attributes( 'table' );
					if ( ! $dataTable ) {
						$dataTable			=	$this->table;
					}
					$data				=	new SimpleXMLElement( <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<table table="$dataTable">
	<rows>
		<field name="$valueFieldName" as="value" type="sql:field" />
		<field name="$fieldName" as="text" type="sql:field" />
	</rows>
	<orderby>
		<field name="$fieldName" ordering="ASC" />
	</orderby>
	<groupby>
		<field name="$fieldName" />
	</groupby>
</table>
EOT
					);

					$xmlsql				=	new XmlQuery( $this->_db, $dataTable, $this->_pluginParams );
					$xmlsql->process_orderby( $data->getElementByPath( 'orderby') );							// <data><orderby><field> fields
					$xmlsql->process_fields(  $data->getElementByPath( 'rows') );								// <data><rows><field> fields
					$xmlsql->process_where(   $data->getElementByPath( 'where') );								// <data><where><column> fields
					$groupby			=	$data->getElementByPath( 'groupby' );
					$xmlsql->process_groupby( ( $groupby ? $groupby : 'value' ) );								// <data><groupby><field> fields
					$fieldValuesInDb	=	$xmlsql->queryLoadObjectsList( $data );		// get the records

					$o->addAttribute( 'type', 'list' );
					/*
										$fieldName	= $this->_db->getEscaped( $o->attributes( 'name' ) );
										if ( $o->attributes( 'value' ) ) {
											$valueFieldName		=	$this->_db->getEscaped( $o->attributes( 'value' ) );
										} else {
											$valueFieldName		=	$fieldName;
										}
										$tableName				=	$this->_db->getEscaped( $this->table );
										$query = "SELECT `" . $valueFieldName . "` AS value, `" . $fieldName . "` AS text"
										. "\n FROM `" . $tableName . "`"
										. "\n GROUP BY " . $fieldName
										. "\n ORDER BY " . $fieldName
										;
										$this->_db->setQuery( $query );
										$fieldValuesInDb = $this->_db->loadObjectList();
					*/
				}

				$this->complementWithOptions( $fieldValuesInDb, $o );
				break;
		}
		return $fieldValuesInDb;
	}

	/**
	 * Complements an array with <option>'s
	 *
	 * @param  array             $fieldValuesInDb
	 * @param  SimpleXMLElement  $field
	 * @return void
	 */
	protected function complementWithOptions( &$fieldValuesInDb, SimpleXMLElement $field )
	{
		$otherThanOptionSeen			=	false;

		foreach ( $field->children() as $option ) {
			/** @var $option SimpleXMLElement */
			if ( $option->getName() != 'option' ) {
				$otherThanOptionSeen	=	true;
				continue;
			}

			if ( $otherThanOptionSeen ) {
				$fieldValuesInDb[] = $this->optionToSelObject( $option );
				continue;
			}

			array_unshift( $fieldValuesInDb, $this->optionToSelObject( $option ) );
		}
	}

	/**
	 * Converts an <option> to a stdClass db result
	 * @param  SimpleXMLElement  $option
	 * @return \stdClass
	 */
	protected function optionToSelObject( SimpleXMLElement $option )
	{
		$hasIndex					=	( $option->attributes( 'index' ) !== '') && ( $option->attributes( 'index' ) !== null );

		$selObj						=	new \stdClass();
		$selObj->value				=	$hasIndex ? $option->attributes( 'index' ) : $option->attributes( 'value' );

		if ( $hasIndex ) {
			$selObj->internalvalue	=	$option->attributes( 'value' );
		}

		$selObj->valuetype			=	$option->attributes( 'valuetype' );
		$selObj->operator			=	$option->attributes( 'operator' );
		$selObj->text				=	$option->data();

		return $selObj;
	}

	/**
	 * Prepares the XML-SQL Query
	 *
	 * @param  boolean  $allFields
	 * @return XmlQuery
	 */
	protected function & _prepareXmlSqlQuery( $allFields = true ) {
		$xmlsql							=	new XmlQuery( $this->_db, $this->table, $this->_pluginParams );

		if ( $allFields === false ) {
			// in case of complex where, we might still need the joins and AS values:
			foreach ( $this->_filterPossibilitesArray as $value ) {						// <filters><filter> (preprocessed xml above)
				if ( ! $this->isValueEmpty( $value['internalvalue'] ) ) {
					/** @var SimpleXMLElement[] $value */
					$where				=	$value['xml']->getElementByPath( 'data/where');
					/** @var array $value */
					if ( $where ) {
						$allFields		=	true;
						break;
					}
				}
			}
		}

		if ( $allFields ) {
			$xmlsql->process_fields( $this->listFieldsRows );						// <fields><field> fields

			// now check for orderings :
			if ( $this->listFieldsRows ) {
				foreach ( $this->listFieldsRows->children() as $field ) {
					/** @var $field SimpleXMLElement */
					if ( $field->getName() != 'field' ) {
						continue;
					}

					$orderinggroups					=	$field->getElementByPath( 'orderinggroups');
					/** @var $orderinggroups SimpleXMLElement|null */
					if ( $orderinggroups ) {
						foreach ( $orderinggroups->children() as $group ) {
							/** @var $group SimpleXMLElement */
							if ( $group->getName() == 'ordering' ) {
								if ( count( $group->children() ) > 0 ) {
									$xmlsql->process_field( $group );
								}
							}
						}
					}
				}
			}
		} else {
			if ( $this->listFieldsRows ) {
				$fieldsToProcess					=	array();
				if ( $this->search ) {
					// search string defined: we need to process the corresponding fields:
					foreach ( $this->quicksearchfields->children() as $searchField ) {
						/** @var $searchField SimpleXMLElement */
						if ( $searchField->getName() == 'field' ) {
							$searchFieldName		=	$searchField->attributes( 'name' );
							if ( $searchFieldName ) {
								$fieldsToProcess[$searchFieldName]	=	true;
							}
						}
					}
				}
				foreach ( $this->_filterPossibilitesArray as $value ) {					// <filters><filter> (preprocessed xml above)
					if ( ! $this->isValueEmpty( $value['internalvalue'] ) ) {
						// filtering defined, need to process field:
						if ( is_array( $value['valuefield'] ) ) {
							foreach ( $value['valuefield'] as $valueField ) {
								$fieldsToProcess[$valueField]		=	true;
							}
						} else {
							$fieldsToProcess[$value['valuefield']]	=	true;
						}
					}
				}
				foreach ( array_keys( $fieldsToProcess ) as $fName ) {
					$selectField	=	$this->listFieldsRows->getChildByNameAttr( 'field', 'name', $fName );
					if ( $selectField ) {
						$xmlsql->process_field( $selectField );
					} else {
						// trying to assume it's in the main table...
						// most times it's ok: no error: trigger_error( sprintf( 'TableBrowser: Notice: trying to select field '%s' which is not in the fields list at main level.', $fName ), E_USER_NOTICE );
					}
				}
			}
		}
		$xmlsql->process_search_string( $this->quicksearchfields, $this->search );	// <quicksearch><field> fields
		$xmlsql->process_where( $this->whereColumns );								// <where><column> fields

		foreach ( $this->_filterPossibilitesArray as $value ) {						// <filters><filter> (preprocessed xml above)
			if ( ! $this->isValueEmpty( $value['internalvalue'] ) ) {
				$this->_filtered	=	true;

				if ( $value['valuetype'] ) {
					$valueType		=	$value['valuetype'];
				} else {
					if ( strpos( $value['basetype'], ':' ) === false ) {
						$valueType	=	'xml:' . $value['basetype'];
					} else {
						$valueType	=	$value['basetype'];
					}
				}

				$xmlsql->process_filter( $value['xml'], $value, $valueType );
				/*
								$where				=	$value['xml']->getElementByPath( 'data/where');
								if ( $where ) {
									$saveReverse	=	$xmlsql->setReverse( true );
									// $Tdata		=	$value['xml']->getElementByPath( 'data/where');
									// $xmlsql->process_data( $Tdata );
									$xmlsql->process_where( $where, $value );
									$xmlsql->setReverse( $saveReverse );
								} else {
									$joinkeys		=	$value['xml']->getElementByPath( 'data/joinkeys');
									if ( $joinkeys ) {
										$data		=	$value['xml']->getElementByPath( 'data');
										$xmlsql->changeJoinType( $data->attributes( 'name' ), $joinkeys->attributes( 'type' ) );
									} else {
										$xmlsql->addWhere( $value['valuefield'], $value['operator'], $value['internalvalue'], $valueType );
									}
								}
				*/
			}
		}

		$xmlsql->process_orderby( $this->orderbyfields, $this->orderby );			// <orderby><field> fields
		$xmlsql->process_groupby( $this->groupbyfields );							// <groupby><field> fields

		return $xmlsql;
	}

	protected function _getTableState()
	{
		$defaultOrderGroup					=	$this->orderbyfields->getChildByNameAttr( 'ordergroup', 'default', 'true' );

		if ( $defaultOrderGroup ) {
			$defaultOrderBy					=	$defaultOrderGroup->attributes( 'name' );
		} else {
			$defaultOrderBy					=	null;
		}

		if ( ! $this->state->get( $this->name, null, GetterInterface::RAW ) ) {
			$this->orderby					=	$defaultOrderBy;

			return;
		}

		// Pagination:
		$limit								=	(int) $this->state->get( $this->name . '.' . 'limit', 0, GetterInterface::INT );

		if ( $limit ) {
			$this->limit					=	$limit;
		}

		$this->limitstart					=	(int) $this->state->get( $this->name . '.' . 'limitstart', 0, GetterInterface::INT );

		// Quicksearch:
		$this->search						=	$this->state->get( $this->name . '.' . 'search', null, GetterInterface::STRING );

		// Orderby:
		$this->orderby						=	$this->state->get( $this->name . '.' . 'orderby', null, GetterInterface::STRING );

		if ( ! $this->orderby ) {
			$this->orderby					=	$defaultOrderBy;
		}

		// Filters:
		$this->_getTableStateItems( $this->state, $this->_filterPossibilitesArray );

		// Batch Process:
		$this->_getTableStateItems( $this->input, $this->_batchPossibilitesArray );
	}

	/**
	 * Gets table state for items in $rows
	 *
	 * @param  ParamsInterface  $input  The user form input
	 * @param  array            $rows   IN+OUT
	 * @return void
	 *
	 * @throws \UnexpectedValueException
	 */
	protected function _getTableStateItems( $input, &$rows = array() ) {
		foreach ( $rows as $name => $value ) {
			$postedValueRaw					=	$input->get( $this->name . '.' . $name, null, GetterInterface::RAW );

			if ( $postedValueRaw !== null ) {

				$postedValue				=	null;
				if ( ! is_array( $postedValueRaw ) ) {
					$postedValue			=	$input->get( $this->name . '.' . $name, null, GetterInterface::STRING );
				}

				if ( ( $rows[$name]['type'] != 'field_show_only_if_selected' ) && $rows[$name]['selectValues'] ) {

					if ( is_array( $postedValueRaw ) ) {
						// 'field' type:
						$subInputs										=	$input->subTree( $this->name . '.' . $name );

						if ( ! $subInputs->count() ) {
							continue;
						}

						$subInputsArray									=	$subInputs->asArray();

						if ( is_array( array_shift( $subInputsArray ) ) ) {
							// Parse Repeat usage:
							$rows[$name]['valuefield']					=	array();
							$rows[$name]['table']						=	array();
							$rows[$name]['table_key']					=	array();
							$rows[$name]['operator']					=	array();
							$rows[$name]['internalvalue']				=	array();
							$rows[$name]['value']						=	array();

							foreach ( $subInputs as $inputColOpVal ) {
								if ( ! $inputColOpVal instanceof ParamsInterface ) {
									throw new \UnexpectedValueException( 'Unexpected inputs in _getTableStateItems' );
								}

								$column									=	$inputColOpVal->get( 'column', null, GetterInterface::STRING );
								$operator								=	$inputColOpVal->get( 'operator', null, GetterInterface::RAW );
								$value									=	$inputColOpVal->get( 'value', null, GetterInterface::STRING );

								if ( ( $column == '' ) || ( $operator == '' ) ) {
									continue;
								}

								if ( ! in_array( $operator, $this->possibleOperators ) ) {
									throw new \UnexpectedValueException( 'Unexpected operator in _getTableStateItems: ' . var_dump( $input->get( $this->name, null, GetterInterface::RAW ) ) );
								}

								$internalValue							=	$value;
								if ( in_array( $operator, array( 'IN', 'NOT IN||ISNULL' ) ) ) {
									$internalValue						=	explode( ',', $internalValue );
								}

								if ( in_array( $operator, array( 'LIKE', 'NOT LIKE||ISNULL' ) ) ) {
									$internalValue						=	'%' . addcslashes( $internalValue, '%_' ) . '%';
								}

								foreach ( $rows[$name]['selectValues'] as $selObj ) {
									if  ( (string) $column === (string) $selObj->value ) {
										$rows[$name]['valuefield'][]	=	isset( $selObj->index ) ? $selObj->index : $selObj->value;
										$rows[$name]['table'][]			=	$selObj->table;
										$rows[$name]['table_key'][]		=	$selObj->table_key;

										$rows[$name]['operator'][]		=	$operator;

										$rows[$name]['internalvalue'][]	=	$internalValue;
										$rows[$name]['value'][]			=	array( 'column' => $column, 'operator' => $operator, 'value' => $value );

										break;
									}
								}
							}
						} else {
							// Pase multiselect usage:
							$values										=	array();
							$internalValues								=	array();
							$specialValueType							=	null;
							$specialOperator							=	null;

							// Make sure the values selected are actaully available to the input:
							foreach ( $subInputs as $inputVal ) {
								foreach ( $rows[$name]['selectValues'] as $selObj ) {
									if  ( ( $inputVal != '' ) && ( (string) $inputVal === (string) $selObj->value ) ) {
										$values[]						=	$selObj->value;

										if ( isset( $selObj->internalvalue ) && ( $selObj->internalvalue !== null ) ) {
											$internalValues[]			=	$selObj->internalvalue;
										} else {
											$internalValues[]			=	$selObj->value;
										}

										$specialValueType				=	isset( $selObj->valuetype ) ? $selObj->valuetype : null;
										$specialOperator				=	isset( $selObj->operator ) ? $selObj->operator : null;
										break;
									}
								}
							}

							if ( $specialValueType && $specialOperator && ( count( $internalValues ) == 1 ) ) {
								$rows[$name]['specialvaluetype']		=	$specialValueType;
								$rows[$name]['operator']				=	$specialOperator;
								$rows[$name]['value']					=	$values[0];
								$rows[$name]['internalvalue']			=	$internalValues[0];
								continue;
							}

							$rows[$name]['value']						=	$values;
							$rows[$name]['internalvalue']				=	$internalValues;
						}
						continue;
					}

					// check if value is in possible values list:
					foreach ( $rows[$name]['selectValues'] as $selObj ) {
						if  ( (string) $postedValue === (string) $selObj->value ) {
							$rows[$name]['value']				=	$selObj->value;
							$rows[$name]['specialvaluetype']	=	isset( $selObj->valuetype ) ? $selObj->valuetype : null;

							if ( isset( $selObj->internalvalue ) && ( $selObj->internalvalue !== null ) ) {
								$rows[$name]['internalvalue']	=	$selObj->internalvalue;
							} else {
								$rows[$name]['internalvalue']	=	$selObj->value;
							}

							if ( isset( $selObj->operator ) && ( $selObj->operator !== null ) ) {
								$rows[$name]['operator']		=	$selObj->operator;
							}
							break;
						}
					}
				} else {
					if ( is_array( $postedValueRaw ) ) {
						// Remove empty string and null as neither are acceptable multiselect filter values:
						$postedValueRaw							=	array_filter( $postedValueRaw,
																		function( $k ) {
																			return ( $k != '' );
																		}
																	);

						$rows[$name]['value']					=	$postedValueRaw;
					} elseif ( $rows[$name]['basetype'] == 'int' ) {
						$rows[$name]['value']					=	(int) $postedValue;
					} elseif ( $rows[$name]['basetype'] == 'float' ) {
						$rows[$name]['value']					=	(float) $postedValue;
					} else {
						$rows[$name]['value']					=	$postedValue;
					}

					$rows[$name]['internalvalue']				=	$rows[$name]['value'];
				}
			}
		}
	}

	/**
	 * Create the data Model and loads it
	 *
	 * @param  string       $dataModelClass
	 * @param  int          $dataModelValue
	 * @return TableInterface
	 */
	protected function createLoadClass( $dataModelClass, $dataModelValue ) {
		if ( strpos( $dataModelClass, '::' ) === false ) {
			$data					=	new $dataModelClass( $this->_db );		// normal clas="className"
			/** @var $data TableInterface */
			$data->load( $dataModelValue );
		} else {
			$dataModelSingleton		=	explode( '::', $dataModelClass );		// class object loader from singleton: class="loaderClass::loadStaticMethor" with 1 parameter, the key value.
			if ( is_callable( $dataModelSingleton ) ) {
				if ( is_callable( array( $dataModelSingleton[0], 'getInstance' ) ) ) {
					$instance		=	call_user_func_array( array( $dataModelSingleton[0], 'getInstance' ), array( &$this->_db ) );
					$rows			=	call_user_func_array( array( $instance, $dataModelSingleton[1] ), array( $dataModelValue ) );
				} else {
					$rows			=	call_user_func_array( $dataModelSingleton, array( $dataModelValue ) );
				}
			} else {
				trigger_error( sprintf( 'Missing singleton class creator %s', $dataModelClass ), E_USER_WARNING );
				$std				=	new \stdClass();
				$rows				=	array( $std );
			}
			$data					=	$rows[0];
		}
		return $data;
	}

	/**
	 * Performs a table action on a click in table
	 *
	 * @return void
	 * @throws \Exception
	 */
	protected function _performTableActions() {
		global $_CB_framework, $_PLUGINS;

		$subtask		=	$this->input->get( $this->name . '.' . 'subtask', '', GetterInterface::STRING );
		if ( ! $subtask ) {
			return;
		}
		$task_parsed	=	explode( '/', $subtask );

		$cid			=	$this->input->get( $this->name . '.' . 'idcid', array(), GetterInterface::RAW );
		if (!is_array( $cid )) {
			$cid		=	array( $cid );
		}

		switch ( $task_parsed[0] ) {
			case 'orderup':
			case 'orderdown':
			case 'saveorder':
				if ( $this->listFieldsRows ) {
					if ( isset( $task_parsed[1] ) ) {
						$field						=	$task_parsed[1];
						$fieldNode					=	$this->listFieldsRows->getChildByNameAttr( 'field', 'name', $field );

						if ( ! $fieldNode ) {
							$fieldNode				=	$this->listFieldsRows->getChildByNameAttr( 'param', 'name', $field );
						}
					} else {
						$field						=	null;
						$fieldNode					=	false;
					}

					if ( ( ! $fieldNode ) || ( $fieldNode->attributes( 'type' ) !== 'ordering' ) || ( ! Access::authorised( $fieldNode ) ) ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'This field can not ordered' ), 'error' );
						return;
					}

					$dataModelClass					=	$this->class;
					if ( $task_parsed[0] != 'saveorder' ) {
						$dataModelValue				=	$cid[0];
					} else {
						$dataModelValue				=	null;
					}
					$row							=	$this->createLoadClass( $dataModelClass, $dataModelValue );
					if ( ! $row ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'No row data found' ), 'error' );
						return;
					}
					if ( $task_parsed[0] == 'saveorder' ) {
						$order 						=	$this->input->get( $this->name . '.' . $field, array(0), GetterInterface::RAW );
					}

					$where							=	'';
					$orderinggroups					=	$fieldNode->getElementByPath( 'orderinggroups');
					/** @var $orderinggroups SimpleXMLElement|null */
					if ( $orderinggroups ) {
						foreach ( $orderinggroups->children() as $group ) {
							/** @var $group SimpleXMLElement */
							$orderingFieldName	=	$group->attributes( 'name' );
							if ( ( $group->getName() == 'ordering' ) && $orderingFieldName && array_key_exists( $orderingFieldName, get_object_vars( $row ) ) ) {
								if ( $task_parsed[0] != 'saveorder' ) {

									$where		.=	$this->_db->NameQuote( $orderingFieldName ) . ' = '
										.	XmlTypeCleanQuote::sqlCleanQuote( $row->$orderingFieldName, $group->attributes( 'type' ), $this->_pluginParams, $this->_db )
										.	' AND ';
								} else {
									$where		.=	$orderingFieldName . "='\$row->" . $orderingFieldName . "' AND ";
								}
							}
						}
					}
					if ( $task_parsed[0] != 'saveorder' ) {
						$inc						=	( ( $task_parsed[0] == 'orderup' ) ? -1 : 1 );
						/** @var OrderedTable $row */
						$row->move( $inc, $where . $field . " > -10000 AND " . $field . " < 10000 ", $field );
					} else {
						$this->saveOrder( $cid, $row, $order, "\$condition = \"" . $where . $field . " > -10000 AND " . $field . " < 10000 \";", $field );
					}
					$_CB_framework->enqueueMessage( CBTxt::T( 'ROW_COUNT_ORDER_SUCCESS', 'Row ordered successfully!|%%COUNT%% rows ordered successfully!', array( '%%COUNT%%' => count( $cid ) ) ) );
				}
				break;

			case 'publish':
			case 'unpublish':
			case 'enable':
			case 'disable':
			case 'setfield':
			case 'doaction':
				if ( $this->listFieldsRows ) {
					$field							=	null;

					switch ( $task_parsed[0] ) {
						case 'publish':
						case 'unpublish':
							$value 					=	( ( $task_parsed[0] == 'publish' ) ? 1 : 0 );
							$field					=	'published';
							break;

						case 'enable':
						case 'disable':
							$value 					=	( ( $task_parsed[0] == 'enable' ) ? 1 : 0 );
							$field					=	'enabled';
							break;

						case 'setfield':
							$value					=	$task_parsed[2];
							break;

						case 'doaction':
							$value					=	null;
							break;
						default:
							throw new \Exception( __FUNCTION__ . ': Impossible value' );
					}

					if ( isset( $task_parsed[1] ) ) {
						$field						=	$task_parsed[1];
					}

					/** @var SimpleXMLElement $fieldNode */
					$fieldNode						=	$this->listFieldsRows->xpath( '(//field[@name="' . $field . '"][@onclick="toggle"])[last()]' );

					if ( ! $fieldNode ) {
						$fieldNode					=	$this->listFieldsRows->xpath( '(//param[@name="' . $field . '"][@onclick="toggle"])[last()]' );
					}

					if ( ! $fieldNode ) {
						// We're not a field toggle so lets check if we're a menu item for permission/usage checks:
						$fieldNode					=	$this->toolbarmenu->xpath( '(//menu[@name="' . $field . '"])[last()]' );
					}

					if ( ( ! $fieldNode ) || ( ! Access::authorised( $fieldNode[0] ) ) ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'THIS_FIELD_CAN_NOT_TOGGLE_TASK', 'This field can not toggle: [task]', array( '[task]' => $task_parsed[0] ) ), 'error' );
						return;
					}

					$fieldNode						=	$fieldNode[0];

					$taskName						=	CBTxt::T( $fieldNode->attributes( 'label' ) );

					if ( $task_parsed[0] == 'setfield' ) {
						// Check field value if allowed:
						$this->registryEditVew->resolveXmlParamType( $fieldNode );

						if ( $fieldNode->getChildByNameAttributes( 'option' ) ) {
							$valueNode				=	$fieldNode->getAnyChildByNameAttr( 'option', 'index', $value );

							if ( ! $valueNode ) {
								$valueNode			=	$fieldNode->getAnyChildByNameAttr( 'option', 'value', $value );
							}

							if ( $valueNode ) {
								$valueLabel			=	CBTxt::T( $valueNode->data() );

								if ( $valueLabel ) {
									$taskName		=	$valueLabel;
								}
							} else {
								$_CB_framework->enqueueMessage( CBTxt::T( 'This field can not be set to that value' ), 'error' );
								return;
							}
						}
					}

					if ( ! $taskName ) {
						$taskName					=	$task_parsed[0];
					}

					if ( count( $cid ) < 1 ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'SELECT_A_ROW_TO_TASK', 'Select a row to [task]', array( '[task]' => strtolower( $taskName ) ) ), 'error' );
						return;
					}

					$dataModelClass					=	$this->class;

					foreach ( $cid as $c ) {
						$dataModelValue				=	$this->getDataModelValue( $c );

						$row						=	$this->createLoadClass( $dataModelClass, $dataModelValue );

						if ( ! $row ) {
							$_CB_framework->enqueueMessage( CBTxt::T( 'No row data found' ), 'error' );
							return;
						}

						if ( $task_parsed[0] == 'doaction' ) {
							$this->registryEditVew->pushModelOfData( $row );

							$toggle					=	$this->registryEditVew->_form_private( $field, $value, $fieldNode, null );

							$this->registryEditVew->popModelOfData();

							if ( ! $toggle ) {
								$_CB_framework->enqueueMessage( CBTxt::T( 'CANNOT_TASK_ROW_ID_ID_BECAUSE_ERROR', 'Cannot [task] row id [id] because: [error]', array( '[id]' => $dataModelValue, '[task]' => strtolower( $taskName ), '[error]' => $row->getError() ) ), 'error' );
								return;
							}
						} elseif ( $row->$field != $value ) {
							if ( is_callable( array( $row, 'historySetMessage' ) ) ) {
								$row->historySetMessage( ucfirst( $task_parsed[0] ) . ' ' . $field . ' from administration backend' );
							}

							if ( $fieldNode->attributes( 'class' ) && $fieldNode->attributes( 'method' ) ) {
								$this->registryEditVew->pushModelOfData( $row );

								$toggle				=	$this->registryEditVew->_form_private( $field, $value, $fieldNode, null );

								$this->registryEditVew->popModelOfData();

								if ( ! $toggle ) {
									$_CB_framework->enqueueMessage( CBTxt::T( 'CANNOT_TASK_ROW_ID_ID_BECAUSE_ERROR', 'Cannot [task] row id [id] because: [error]', array( '[id]' => $dataModelValue, '[task]' => strtolower( $taskName ), '[error]' => $row->getError() ) ), 'error' );
									return;
								}
							} elseif ( $row->hasFeature( 'checkout' ) ) {
								/** @var CheckedOrderedTable $row */
								if ( ! $row->isCheckedOut( $_CB_framework->myId() ) ) {
									$row->$field	=	$value;

									if ( $row->check() ) {
										if ( ! $row->store() ) {
											$_CB_framework->enqueueMessage( CBTxt::T( 'CANNOT_TASK_ROW_ID_ID_BECAUSE_ERROR', 'Cannot [task] row id [id] because: [error]', array( '[id]' => $dataModelValue, '[task]' => strtolower( $taskName ), '[error]' => $row->getError() ) ), 'error' );
											return;
										}
									} else {
										$_CB_framework->enqueueMessage( CBTxt::T( 'CANNOT_TASK_ROW_ID_ID_BECAUSE_ERROR', 'Cannot [task] row id [id] because: [error]', array( '[id]' => $dataModelValue, '[task]' => strtolower( $taskName ), '[error]' => $row->getError() ) ), 'error' );
										return;
									}

									$row->checkin();
								}
							} else {
								$row->$field		=	$value;

								if ( $row->check() ) {
									if ( ! $row->store() ) {
										$_CB_framework->enqueueMessage( CBTxt::T( 'CANNOT_TASK_ROW_ID_ID_BECAUSE_ERROR', 'Cannot [task] row id [id] because: [error]', array( '[id]' => $dataModelValue, '[task]' => strtolower( $taskName ), '[error]' => $row->getError() ) ), 'error' );
										return;
									}
								} else {
									$_CB_framework->enqueueMessage( CBTxt::T( 'CANNOT_TASK_ROW_ID_ID_BECAUSE_ERROR', 'Cannot [task] row id [id] because: [error]', array( '[id]' => $dataModelValue, '[task]' => strtolower( $taskName ), '[error]' => $row->getError() ) ), 'error' );
									return;
								}
							}
						}
					}

					$_CB_framework->enqueueMessage( CBTxt::T( 'ROW_COUNT_TASK_SUCCESS', '{1} Row [task] successfully!|%%COUNT%% rows [task] successfully!', array( '%%COUNT%%' => count( $cid ), '[task]' => strtolower( $taskName ) ) ) );
				}
				break;

			case 'editrows':
				if ( $this->listFieldsRows ) {

					if ( count( $cid ) != 1 ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'SELECT_A_ROW_TO_TASK', 'Select a row to [task]', array( '[task]' => 'edit' ) ), 'error' );
						return;
					}

					if ( isset( $task_parsed[1] ) ) {
						$field		=	$task_parsed[1];
					} else {
						$field		=	'tid';
					}

					if ( $this->_options['view'] == 'editPlugin' ) {
						$task		=	$this->_options['view'];
					} else {
						$task		=	'editrow';
					}

					$baseUrl		=	'index.php?option=' . $this->_options['option'] . '&view=' . $task;

					if ( isset( $this->_options['pluginid'] ) ) {
						$baseUrl	.=	'&cid=' . $this->_options['pluginid'];
					}

					$url			=	$baseUrl . '&table=' . $this->_tableBrowserModel->attributes( 'name' ) . '&action=editrow&' . urlencode( $field ) . '=' . urlencode( $cid[0] );

					cbRedirect( $url );
				}
				break;
			case 'deleterows':
				if ( $this->listFieldsRows ) {
					if ( count( $cid ) < 1 ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'SELECT_A_ROW_TO_TASK', 'Select a row to [task]', array( '[task]' => 'delete' ) ), 'error' );
						return;
					}
					$dataModelClass			=	$this->class;
					foreach ( $cid as $id ) {
						$dataModelValue		=	$this->getDataModelValue( $id );
						$row				=	$this->createLoadClass( $dataModelClass, $dataModelValue );

						if ( ! $row ) {
							$_CB_framework->enqueueMessage( CBTxt::T( 'No row data found' ), 'error' );
							return;
						}
						if ( $row->canDelete( $dataModelValue ) ) {
							if ( ! $row->delete( $dataModelValue ) ) {
								$_CB_framework->enqueueMessage( CBTxt::T( 'CANNOT_DELETE_ROW_ID_BECAUSE_ERROR', 'Cannot delete row id [id] because: [error]', array( '[id]' => $dataModelValue, '[error]' => $row->getError() ) ), 'error' );
								return;
							}
						} else {
							$_CB_framework->enqueueMessage( CBTxt::T( 'CANNOT_DELETE_ROW_ID_BECAUSE_ERROR', 'Cannot delete row id [id] because: [error]', array( '[id]' => $dataModelValue, '[error]' => $row->getError() ) ), 'error' );
							return;
						}
					}
					$_CB_framework->enqueueMessage( CBTxt::T( 'ROW_COUNT_DELETED_SUCCESS', 'Row deleted successfully!|%%COUNT%% rows deleted successfully!', array( '%%COUNT%%' => count( $cid ) ) ) );
				}
				break;
			case 'batchrows':
				if ( $this->listFieldsRows ) {
					if ( count( $cid ) < 1 ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'SELECT_A_ROW_TO_TASK', 'Select a row to [task]', array( '[task]' => 'batch' ) ), 'error' );
						return;
					}

					$postData						=	array();

					foreach ( $this->_batchPossibilitesArray as $key => $value ) { // <batchprocess><batch>
						if ( ! $this->isValueEmpty( $value['internalvalue'] ) ) {
							$field					=	$value['valuefield'];
							$postData[$field]		=	$value['internalvalue'];
						}

						// Reset back to null as we don't want the values reselected on display:
						$this->_batchPossibilitesArray[$key]['value']			=	null;
						$this->_batchPossibilitesArray[$key]['internalvalue']	=	$value['value'];
					}

					if ( count( $postData ) < 1 ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'Nothing to process' ), 'error' );
						return;
					}

					// Fix multi-selects and multi-checkboxes arrays to |*|-delimited strings:
					$postData						=	ActionController::recursiveMultiSelectFix( $postData );
					$dataModelClass					=	$this->class;

					foreach ( $cid as $id ) {
						$dataModelValue				=	$this->getDataModelValue( $id );

						/** @var $row TableInterface */
						$row						=	$this->createLoadClass( $dataModelClass, $dataModelValue );

						if ( ! $row ) {
							$_CB_framework->enqueueMessage( CBTxt::T( 'No row data found' ), 'error' );
							return;
						}

						$rowPost					=	array();

						foreach ( $postData as $key => $value ) {
							if ( property_exists( $row, $key ) ) {
								$rowPost[$key]		=	( is_array( $value ) ? json_encode( $value ) : $value );
							}
						}

						if ( count( $rowPost ) < 1 ) {
							$_CB_framework->enqueueMessage( CBTxt::T( 'Nothing to process' ), 'error' );
							return;
						}

						if ( ! $row->bind( $rowPost ) ) {
							$_CB_framework->enqueueMessage( CBTxt::T( 'CANNOT_BATCH_PROCESS_ROW_ID_ID_BECAUSE_ERROR', 'Cannot batch process row id [id] because: [error]', array( '[id]' => $dataModelValue, '[error]' => $row->getError() ) ), 'error' );
							return;
						}

						if ( ! $row->check() ) {
							$_CB_framework->enqueueMessage( CBTxt::T( 'CANNOT_BATCH_PROCESS_ROW_ID_ID_BECAUSE_ERROR', 'Cannot batch process row id [id] because: [error]', array( '[id]' => $dataModelValue, '[error]' => $row->getError() ) ), 'error' );
							return;
						}

						if ( ! $row->store() ) {
							$_CB_framework->enqueueMessage( CBTxt::T( 'CANNOT_BATCH_PROCESS_ROW_ID_ID_BECAUSE_ERROR', 'Cannot batch process row id [id] because: [error]', array( '[id]' => $dataModelValue, '[error]' => $row->getError() ) ), 'error' );
							return;
						}
					}

					$_CB_framework->enqueueMessage( CBTxt::T( 'ROW_COUNT_SAVED_SUCCESS', 'Row saved successfully!|%%COUNT%% rows saved successfully!', array( '%%COUNT%%' => count( $cid ) ) ) );
				}
				break;
			case 'copyrows':
				if ( $this->listFieldsRows ) {
					if ( count( $cid ) < 1 ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'SELECT_A_ROW_TO_TASK', 'Select a row to [task]', array( '[task]' => 'copy' ) ), 'error' );
						return;
					}
					$dataModelClass			=	$this->class;
					foreach ( $cid as $id ) {
						$dataModelValue		=	$this->getDataModelValue( $id );
						/** @var $row TableInterface */
						$row				=	$this->createLoadClass( $dataModelClass, $dataModelValue );
						if ( ! $row ) {
							$_CB_framework->enqueueMessage( CBTxt::T( 'No row data found' ), 'error' );
							return;
						}
						if ( $row->canCopy() ) {
							if ( ! $row->copy() ) {
								$_CB_framework->enqueueMessage( CBTxt::T( 'CANNOT_COPY_ROW_ID_ID_BECAUSE_ERROR', 'Cannot copy row id [id] because: [error]', array( '[id]' => $dataModelValue, '[error]' => $row->getError() ) ), 'error' );
								return;
							}
						} else {
							$_CB_framework->enqueueMessage( CBTxt::T( 'CANNOT_COPY_ROW_ID_ID_BECAUSE_ERROR', 'Cannot copy row id [id] because: [error]', array( '[id]' => $dataModelValue, '[error]' => $row->getError() ) ), 'error' );
							return;
						}
					}
					$_CB_framework->enqueueMessage( CBTxt::T( 'ROW_COUNT_COPIED_SUCCESS', 'Row copied successfully!|%%COUNT%% rows copied successfully!', array( '%%COUNT%%' => count( $cid ) ) ) );
				}
				break;
			case 'exportrows':
				if ( $this->listFieldsRows && $this->export && Access::authorised( $this->export ) ) {
					if ( count( $cid ) < 1 ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'SELECT_A_ROW_TO_TASK', 'Select a row to [task]', array( '[task]' => 'copy' ) ), 'error' );
						return;
					}

					$exportFormat			=	null;

					if ( isset( $task_parsed[1] ) ) {
						$exportFormat		=	$task_parsed[1];
					}

					if ( ! $exportFormat ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'Missing export format' ), 'error' );
						return;
					}

					$allowCSV				=	( $this->export->attributes( 'csv' ) != 'false' );
					$allowXML				=	( $this->export->attributes( 'xml' ) != 'false' );
					$allowJSON				=	( $this->export->attributes( 'json' ) != 'false' );

					switch ( $exportFormat ) {
						case 'csv':
							if ( ! $allowCSV ) {
								$_CB_framework->enqueueMessage( CBTxt::T( 'CSV export not supported' ), 'error' );
								return;
							}
							break;
						case 'xml':
							if ( ! $allowXML ) {
								$_CB_framework->enqueueMessage( CBTxt::T( 'XML export not supported' ), 'error' );
								return;
							}
							break;
						case 'json':
							if ( ! $allowJSON ) {
								$_CB_framework->enqueueMessage( CBTxt::T( 'JSON export not supported' ), 'error' );
								return;
							}
							break;
						default:
							$_CB_framework->enqueueMessage( CBTxt::T( 'Unknown export format' ), 'error' );
							return;
							break;
					}

					$include					=	array();
					$explicitInclude			=	true;
					$exclude					=	array();
					$orderBy					=	array();

					foreach ( $this->export->children() as $includeExclude ) {
						$dataName				=	$includeExclude->attributes( 'name' );

						if ( $includeExclude->getName() == 'include' ) {
							$explicitInclude	=	( $includeExclude->attributes( 'explicit' ) != 'false' );

							if ( ! $dataName ) {
								foreach ( $includeExclude->children() as $includeField ) {
									$dataName	=	$includeField->attributes( 'name' );

									if ( ! $dataName ) {
										continue;
									}

									$include[]	=	$dataName;
								}

								continue;
							}

							$include[]			=	$dataName;
						} elseif ( $includeExclude->getName() == 'exclude' ) {
							if ( ! $dataName ) {
								foreach ( $includeExclude->children() as $excludeField ) {
									$dataName	=	$excludeField->attributes( 'name' );

									if ( ! $dataName ) {
										continue;
									}

									$exclude[]	=	$dataName;
								}

								continue;
							}

							$exclude[]			=	$dataName;
						} elseif ( $includeExclude->getName() == 'orderby' ) {
							if ( ! $dataName ) {
								foreach ( $includeExclude->children() as $orderByField ) {
									$dataName	=	$orderByField->attributes( 'name' );

									if ( ! $dataName ) {
										continue;
									}

									$orderBy[]	=	$dataName;
								}

								continue;
							}

							$orderBy[]			=	$dataName;
						}
					}

					$dataModelClass			=	$this->class;
					$dataClass				=	null;
					$exportData				=	array();

					$_PLUGINS->loadPluginGroup( 'user' );

					foreach ( $cid as $id ) {
						$dataModelValue				=	$this->getDataModelValue( $id );

						/** @var $row TableInterface */
						$row						=	$this->createLoadClass( $dataModelClass, $dataModelValue );

						if ( ! $row ) {
							$_CB_framework->enqueueMessage( CBTxt::T( 'No row data found' ), 'error' );
							return;
						}

						$rowData					=	array();

						if ( method_exists( $row, 'asArray' ) ) {
							$dataArray				=	$row->asArray();
						} else {
							$dataArray				=	get_object_vars( $row );
						}

						if ( $orderBy ) {
							// An order was supplied so lets reorder the keys:
							$order					=	array();

							foreach ( $orderBy as $orderKey ) {
								if ( ! key_exists( $orderKey, $dataArray ) ) {
									continue;
								}

								$order[$orderKey]	=	$dataArray[$orderKey];
							}

							$dataArray				=	( $order + $dataArray );
						}

						$primaryKey					=	$row->getKeyName();

						if ( $primaryKey && isset( $dataArray[$primaryKey] ) ) {
							// Ensure the primary key is always ordered first:
							$dataArray				=	( array( $primaryKey => $dataArray[$primaryKey] ) + $dataArray );
						}

						foreach ( $dataArray as $k => $v ) {
							if ( ( ! $k ) || in_array( $k, $exclude ) || ( ( $k[0] == '_' ) && ( ! in_array( $k, $include ) ) ) ) {
								// Skip empty keys, excluded keys, and private variables (unless included):
								continue;
							} elseif ( $include && $explicitInclude && ( ! in_array( $k, $include ) ) ) {
								// Skip any data that isn't explicitly included:
								continue;
							}

							$rowData[$k]			=	$v;
						}

						if ( $dataClass === null ) {
							$reflection				=	new \ReflectionClass( $row );

							$dataClass				=	$reflection->getShortName();
						}

						// Class specific export trigger (e.g. onExportUserTable):
						$_PLUGINS->trigger( 'onExport' . $dataClass, array( $row, &$rowData, $exportFormat ) );

						if ( ! $rowData ) {
							// Skip export as this row has no data to export:
							continue;
						}

						$exportData[]				=	$rowData;
					}

					$exportName						=	preg_replace( '/[^-a-zA-Z0-9_]/', '', $this->export->attributes( 'name' ) );

					if ( ! $exportName ) {
						$exportName					=	'export';
					}

					$exportFormatted				=	null;

					switch ( $exportFormat ) {
						case 'csv':
							$csvHeaders						=	array();

							foreach ( $exportData as $rowId => $rowData ) {
								if ( ! $rowData ) {
									continue;
								}

								foreach ( $rowData as $dataName => $dataValue ) {
									if ( ! in_array( $dataName, $csvHeaders ) ) {
										$csvHeaders[]		=	$dataName;
									}
								}
							}

							$exportFormatted				.=	implode( ',', $csvHeaders ) . "\n";

							foreach ( $exportData as $rowId => $rowData ) {
								if ( ! $rowData ) {
									continue;
								}

								$csvData					=	array();

								foreach ( $csvHeaders as $csvHeader ) {
									if ( ! isset( $rowData[$csvHeader] ) ) {
										// Column doesn't exist for this row, but we need it anyway for proper CSV formatting so set to empty:
										$csvData[]			=	'""';
										continue;
									}

									$dataValue				=	$rowData[$csvHeader];

									if ( is_array( $dataValue ) || is_object( $dataValue ) ) {
										$dataValue			=	json_encode( $dataValue );
									} elseif ( is_object( $dataValue ) ) {
										if ( method_exists( $dataValue, 'asArray' ) ) {
											$dataValue		=	$dataValue->asArray();
										} else {
											$dataValue		=	get_object_vars( $dataValue );
										}

										if ( $dataValue ) {
											$dataValue		=	json_encode( $dataValue );
										} else {
											$dataValue		=	null;
										}
									}

									if ( is_bool( $dataValue ) ) {
										$dataValue			=	( $dataValue ? 'true' : 'false' );
									} elseif ( $dataValue && ( ! is_numeric( $dataValue ) ) ) {
										// Check if the value is a formula:
										$isFormula			=	( ( $dataValue[0] === '=' ) || ( $dataValue[0] === '+' ) || ( $dataValue[0] === '-' ) || ( $dataValue[0] === '@' ) );

										if ( ( Get::clean( $dataValue, GetterInterface::STRING ) != $dataValue ) // Check if contains any HTML
											 || ( strpos( $dataValue, ',' ) !== false ) // Check if contains a comma
											 || ( strpos( $dataValue, '"' ) !== false ) // Check if contains double quote
											 || ( strpos( $dataValue, "\r" ) !== false ) // Check if contains linebreak
											 || ( strpos( $dataValue, "\n" ) !== false ) // Check if contains linebreak
											 || $isFormula // Check if begins with a formula
										) {
											// Contains characters that could malform the CSV so enclose in quotes to treat as string; note quotes must be double quoted to reverse correctly:
											// For formulas we need to be sure to prefix with an empty space to cancel out the formula encase quoted text is not being treated as a string:
											$dataValue		=	'"' . ( $isFormula ? ' ' : null ) . str_replace( '"', '""', $dataValue ) . '"';
										}
									}

									if ( ( $dataValue === null ) || ( $dataValue === '' ) ) {
										// CSV can't have a completely empty space so just treat it as empty string:
										$dataValue			=	'""';
									}

									$csvData[]				=	$dataValue;
								}

								$exportFormatted			.=	implode( ',', $csvData ) . "\n";
							}
							break;
						case 'xml':
							$exportFormatted				.=	'<?xml version="1.0" encoding="utf-8"?>'
															.	"\n<rows>";

							foreach ( $exportData as $rowId => $rowData ) {
								if ( ! $rowData ) {
									continue;
								}

								$exportFormatted			.=		"\n\t<row>";

								foreach ( $rowData as $dataName => $dataValue ) {
									if ( is_array( $dataValue ) || is_object( $dataValue ) ) {
										$dataValue			=	json_encode( $dataValue );
									} elseif ( is_object( $dataValue ) ) {
										if ( method_exists( $dataValue, 'asArray' ) ) {
											$dataValue		=	$dataValue->asArray();
										} else {
											$dataValue		=	get_object_vars( $dataValue );
										}

										if ( $dataValue ) {
											$dataValue		=	json_encode( $dataValue );
										} else {
											$dataValue		=	null;
										}
									}

									if ( is_bool( $dataValue ) ) {
										$dataValue			=	( $dataValue ? 'true' : 'false' );
									} elseif ( $dataValue && ( ! is_numeric( $dataValue ) ) ) {
										if ( ( Get::clean( $dataValue, GetterInterface::STRING ) != $dataValue ) // Check if contains any HTML
											 || ( strpos( $dataValue, '<' ) !== false ) // Check if contains a lt
											 || ( strpos( $dataValue, '&' ) !== false ) // Check if contains a amp
											 || ( strpos( $dataValue, ']]>' ) !== false ) // Check if it contains CDATA closing token
										) {
											// Contains characters that could malform the XML so enclose in CDATA:
											$dataValue		=	'<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $dataValue ) . ']]>';
										}
									}

									if ( ( $dataValue === null ) || ( $dataValue === '' ) ) {
										$exportFormatted	.=		"\n\t\t<" . htmlspecialchars( $dataName ) . " />";
									} else {
										$exportFormatted	.=		"\n\t\t<" . htmlspecialchars( $dataName ) . ">"
															.			$dataValue
															.		'</' . htmlspecialchars( $dataName ) . '>';
									}
								}

								$exportFormatted			.=		"\n\t</row>";
							}

							$exportFormatted				.=	"\n</rows>";
							break;
						case 'json':
							$exportFormatted				=	json_encode( $exportData );
							break;
					}

					while ( @ob_end_clean() );

					header( 'HTTP/1.0 200 OK' );

					switch ( $exportFormat ) {
						case 'csv':
							header( 'Content-Disposition: attachment; filename="' . $exportName . '.csv"' );
							header( 'Content-Type: application/csv; charset=UTF-8' );
							break;
						case 'json':
							header( 'Content-Disposition: attachment; filename="' . $exportName . '.txt"' );
							header( 'Content-Type: text/plain; charset=UTF-8' );
							break;
						case 'xml':
							header( 'Content-Disposition: attachment; filename="' . $exportName . '.xml"' );
							header( 'Content-Type: application/xml; charset=UTF-8' );
							break;
					}

					header( 'Content-Transfer-Encoding: binary' );
					header( 'Pragma: private' );
					header( 'Cache-Control: private' );
					header( 'Content-Length: ' . strlen( $exportFormatted ) );
					header( 'Connection: close' );

					echo $exportFormatted;

					exit();
				}
				break;
			case 'importrows':
				if ( $this->import && Access::authorised( $this->import ) ) {
					$listFiles				=	$this->input->getNamespaceRegistry( 'files' )->subTree( $this->name );
					$fileName				=	$listFiles->get( 'name.import', null, GetterInterface::STRING );
					$filePath				=	$listFiles->get( 'tmp_name.import', null, GetterInterface::STRING );

					if ( ( ! $fileName ) || ( ! $filePath ) ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'Failed to import file! Error: Missing name or path' ), 'error' );
						return;
					}

					$importFormat			=	strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', pathinfo( $fileName, PATHINFO_EXTENSION ) ) );

					if ( ! $importFormat ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'Failed to import file! Error: Unknown extension' ), 'error' );
						return;
					}

					$override					=	$this->input->get( $this->name . '.' . 'import_override', 3, GetterInterface::INT );
					$notify						=	$this->input->get( $this->name . '.' . 'import_email', 1, GetterInterface::INT );
					$include					=	array();
					$explicitInclude			=	true;
					$exclude					=	array();

					foreach ( $this->import->children() as $includeExclude ) {
						$dataName				=	$includeExclude->attributes( 'name' );

						if ( $includeExclude->getName() == 'include' ) {
							$explicitInclude	=	( $includeExclude->attributes( 'explicit' ) != 'false' );

							if ( ! $dataName ) {
								foreach ( $includeExclude->children() as $includeField ) {
									$dataName	=	$includeField->attributes( 'name' );

									if ( ! $dataName ) {
										continue;
									}

									$include[]	=	$dataName;
								}

								continue;
							}

							$include[]			=	$dataName;
						} elseif ( $includeExclude->getName() == 'exclude' ) {
							if ( ! $dataName ) {
								foreach ( $includeExclude->children() as $excludeField ) {
									$dataName	=	$excludeField->attributes( 'name' );

									if ( ! $dataName ) {
										continue;
									}

									$exclude[]	=	$dataName;
								}

								continue;
							}

							$exclude[]			=	$dataName;
						}
					}

					$allowCSV				=	( $this->import->attributes( 'csv' ) != 'false' );
					$allowXML				=	( $this->import->attributes( 'xml' ) != 'false' );
					$allowJSON				=	( $this->import->attributes( 'json' ) != 'false' );
					$importData				=	array();

					switch ( $importFormat ) {
						case 'csv':
							if ( ! $allowCSV ) {
								$_CB_framework->enqueueMessage( CBTxt::T( 'CSV import not supported' ), 'error' );
								return;
							}

							try {
								$CSVFile						=	@fopen( $filePath, 'r' );
								$CSVHeaders						=	array();
								$i								=	0;

								while ( ( $CSVData = fgetcsv( $CSVFile, 0, ",", '"', '"' ) ) !== FALSE ) {
									if ( $i == 0 ) {
										// First row is always headers:
										$CSVHeaders				=	array_values( $CSVData );
									} else {
										$rowData				=	array();

										foreach ( array_values( $CSVData ) as $k => $v ) {
											if ( ! isset( $CSVHeaders[$k] ) ) {
												// CSV column doesn't exist so lets skip:
												continue;
											}

											$k					=	$CSVHeaders[$k];

											if ( $v === 'true' ) {
												$v				=	true;
											} elseif ( $v === 'false' ) {
												$v				=	false;
											}

											$rowData[$k]		=	$v;
										}

										if ( $rowData ) {
											$importData[]		=	$rowData;
										}
									}

									$i++;
								}

								fclose( $CSVFile );
							} catch ( \Exception $e ) {
								$_CB_framework->enqueueMessage( CBTxt::T( 'Failed to import file! Error: Invalid CSV file' ), 'error' );
								return;
							}
							break;
						case 'xml':
							if ( ! $allowXML ) {
								$_CB_framework->enqueueMessage( CBTxt::T( 'XML import not supported' ), 'error' );
								return;
							}

							try {
								$importXMLRows		=	new SimpleXMLElement( file_get_contents( $filePath ) );
							} catch ( \Exception $e ) {
								$_CB_framework->enqueueMessage( CBTxt::T( 'Failed to import file! Error: Invalid XML file' ), 'error' );
								return;
							}

							if ( $importXMLRows->getName() != 'rows' ) {
								// Top most node doesn't match export format so lets error:
								$_CB_framework->enqueueMessage( CBTxt::T( 'Failed to import file! Error: Invalid XML import file' ), 'error' );
								return;
							}

							foreach ( $importXMLRows as $importXMLRow ) {
								if ( $importXMLRow->getName() != 'row' ) {
									// Child node doesn't match export format so lets error:
									$_CB_framework->enqueueMessage( CBTxt::T( 'Failed to import file! Error: Invalid XML import file' ), 'error' );
									return;
								}

								$rowData			=	array();

								foreach ( $importXMLRow as $importXMLRowAttr ) {
									$k				=	$importXMLRowAttr->getName();

									if ( ( ! $k ) || in_array( $k, $exclude ) || ( ( $k[0] == '_' ) && ( ! in_array( $k, $include ) ) ) ) {
										// Skip empty keys, excluded keys, and private variables (unless included):
										continue;
									} elseif ( $include && $explicitInclude && ( ! in_array( $k, $include ) ) ) {
										// Skip any data that isn't explicitly included:
										continue;
									}

									$v				=	(string) $importXMLRowAttr;

									if ( $v === 'true' ) {
										$v			=	true;
									} elseif ( $v === 'false' ) {
										$v			=	false;
									}

									$rowData[$k]	=	$v;
								}

								$importData[]		=	$rowData;
							}
							break;
						case 'txt':
							if ( ! $allowJSON ) {
								$_CB_framework->enqueueMessage( CBTxt::T( 'JSON import not supported' ), 'error' );
								return;
							}

							try {
								$importJSONRows		=	json_decode( file_get_contents( $filePath ), true );
							} catch ( \Exception $e ) {
								$_CB_framework->enqueueMessage( CBTxt::T( 'Failed to import file! Error: Invalid JSON file' ), 'error' );
								return;
							}

							foreach ( $importJSONRows as $importJSONRow ) {
								$rowData			=	array();

								foreach ( $importJSONRow as $k => $v ) {
									if ( ( ! $k ) || in_array( $k, $exclude ) || ( ( $k[0] == '_' ) && ( ! in_array( $k, $include ) ) ) ) {
										// Skip empty keys, excluded keys, and private variables (unless included):
										continue;
									} elseif ( $include && $explicitInclude && ( ! in_array( $k, $include ) ) ) {
										// Skip any data that isn't explicitly included:
										continue;
									}

									$rowData[$k]	=	$v;
								}

								$importData[]		=	$rowData;
							}
							break;
						default:
							$_CB_framework->enqueueMessage( CBTxt::T( 'Unknown import format' ), 'error' );
							return;
							break;
					}

					$dataModelClass			=	$this->class;
					$dataClass				=	null;

					$success				=	array();
					$fail					=	array();

					foreach ( $importData as $rowId => $rowData ) {
						/** @var $row TableInterface */
						$row						=	$this->createLoadClass( $dataModelClass, null );

						if ( ! $row ) {
							$_CB_framework->enqueueMessage( CBTxt::T( 'No row data found' ), 'error' );
							return;
						}

						// Lets see if any of the primary keys exist and if data for them exists so we can load an existing row before editing it:
						$rowKeys					=	$row->getKeyName( true );
						$rowExists					=	false;

						if ( is_array( $rowKeys ) ) {
							$loadBy					=	array();

							foreach ( $rowKeys as $rowKey ) {
								if ( ( ! isset( $rowData[$rowKey] ) ) || ( ! $rowData[$rowKey] ) ) {
									continue;
								}

								$loadBy[$rowKey]	=	$rowData[$rowKey];
							}

							if ( $loadBy ) {
								if ( $override != 3 ) {
									// If we're not always importing as new rows then lets see if this row exists already:
									$row->load( $loadBy );

									$rowExists		=	$row->get( $row->getKeyName(), null, GetterInterface::RAW );
								}

								if ( ! $rowExists ) {
									// Row doesn't exist so lets clear the load data so it can just create a new row:
									foreach ( $loadBy as $k => $v ) {
										unset( $rowData[$k] );
									}
								} elseif ( $override == 2 ) {
									// Row exists, but we're skipping existing entries so skip this row:
									 continue;
								}
							}
						}

						if ( $dataClass === null ) {
							$reflection				=	new \ReflectionClass( $row );

							$dataClass				=	$reflection->getShortName();
						}

						// Class specific import trigger (e.g. onImportUserTable):
						$_PLUGINS->trigger( 'onImport' . $dataClass, array( &$row, &$rowData, $importFormat ) );

						if ( ! $rowData ) {
							// Skip import as this row has no data to import:
							continue;
						}

						// We only want to bind data that actually exists for this object (unless included) so lets build an array of bind data:
						$rowColumns					=	$row->getAllProperties();
						$bindData					=	array();

						foreach ( $rowData as $dataKey => $dataValue ) {
							if ( ( ! in_array( $dataKey, $rowColumns ) ) && ( ! in_array( $dataKey, $include ) ) ) {
								continue;
							}

							if ( $dataValue === null ) {
								if ( $rowExists ) {
									// Reset empty data to null for edits to allow reseting a columns value:
									$dataValue		=	null;
								} else {
									// Don't bother importing empty data; just let SQL default take over:
									continue;
								}
							}

							if ( is_string( $dataValue ) ) {
								// Be sure to clean leading and trailing spaces for all string values:
								$dataValue			=	trim( $dataValue );
							}

							$bindData[$dataKey]		=	$dataValue;
						}

						$index								=	( $rowId + 1 );

						try {
							$oldRow							=	null;
							$clearPassword					=	null;

							if ( $row instanceof UserTable ) {
								// Dynamic object data like fields won't exist as part of class properties so ->bind for this data needs to be directly added with ->set:
								$rowColumns					=	$row->getPublicProperties();

								foreach ( $bindData as $dataKey => $dataValue ) {
									if ( in_array( $dataKey, $rowColumns ) ) {
										continue;
									}

									$row->set( $dataKey, $dataValue );
								}

								$oldRow						=	new UserTable();

								foreach ( array_keys( get_object_vars( $row ) ) as $k ) {
									if ( substr( $k, 0, 1 ) != '_' ) {
										$oldRow->$k			=	$row->$k;
									}
								}

								if ( isset( $bindData['password'] ) && ( $bindData['password'] !== '' ) ) {
									// If password was supplied lets hash it since we don't export passwords we'll treat them as always being plaintext:
									if ( ! $rowExists ) {
										$clearPassword		=	$bindData['password'];
									}

									$bindData['password']	=	$row->hashAndSaltPassword( $bindData['password'] );
								}

								if ( ! $rowExists ) {
									// Fill in any necessary data for completing registration as best as possible if it's missing
									if ( ( ! isset( $bindData['password'] ) ) || ( $bindData['password'] === '' ) ) {
										// User doesn't exist and no password was supplied so lets generate one:
										$clearPassword				=	$row->getRandomPassword();

										$bindData['password']		=	$row->hashAndSaltPassword( $clearPassword );
									}

									if ( ( ! isset( $bindData['gids'] ) ) || ( $bindData['gids'] === '' ) ) {
										// No usergroups supplied so fallback to default registered usergroup
										$bindData['gids']			=	array( (int) $_CB_framework->getCfg( 'new_usertype' ) );
									}

									if ( ( ! isset( $bindData['approved'] ) ) || ( $bindData['approved'] === '' ) ) {
										// Set the default approved state based off configuration since it wasn't imported
										if ( Application::Config()->getInt( 'reg_admin_approval', 0 ) ) {
											$bindData['approved']	=	1;
										} else {
											$bindData['approved']	=	0;
										}
									}

									if ( ( ! isset( $bindData['confirmed'] ) ) || ( $bindData['confirmed'] === '' ) ) {
										// Set the default confirmed state based off configuration since it wasn't imported
										if ( Application::Config()->getInt( 'reg_confirmation', 1 ) ) {
											$bindData['confirmed']	=	1;
										} else {
											$bindData['confirmed']	=	0;
										}
									}

									if ( ( ! isset( $bindData['block'] ) ) || ( $bindData['block'] === '' ) ) {
										// Set the default block state based off confirmed/approved state since it wasn't imported
										if ( $bindData['confirmed'] && $bindData['approved'] ) {
											$bindData['block']		=	0;
										} else {
											$bindData['block']		=	1;
										}
									}

									if ( ( ! $bindData['confirmed'] ) && ( ( ! isset( $bindData['cbactivation'] ) || ( $bindData['cbactivation'] === '' ) ) ) ) {
										// User isn't confirmed and is missing a confirmation code so lets generate one
										$row->_setActivationCode();
									}

									if ( isset( $bindData['name'] ) && ( $bindData['name'] !== '' ) ) {
										// Form first/middle/last name from name if needed:
										$middleNamePos						=	strpos( $bindData['name'], ' ' );
										$lastNamePos						=	strrpos( $bindData['name'], ' ' );

										if ( $lastNamePos !== false ) {
											if ( ( ! isset( $bindData['firstname'] ) ) || ( $bindData['firstname'] === '' ) ) {
												$bindData['firstname']		=	substr( $bindData['name'], 0, $middleNamePos );
											}

											if ( ( ! isset( $bindData['lastname'] ) ) || ( $bindData['lastname'] === '' ) ) {
												$bindData['lastname']		=	substr( $bindData['name'], ( $lastNamePos + 1 ) );
											}

											if ( ( ! isset( $bindData['middlename'] ) ) || ( $bindData['middlename'] === '' ) ) {
												if ( $middleNamePos !== $lastNamePos ) {
													$bindData['middlename']	=	substr( $bindData['name'], ( $middleNamePos + 1 ), ( $lastNamePos - $middleNamePos - 1 ) );
												} else {
													$bindData['middlename']	=	'';
												}
											}
										} else {
											if ( ( ! isset( $bindData['firstname'] ) ) || ( $bindData['firstname'] === '' ) ) {
												$bindData['firstname']		=	'';
											}

											if ( ( ! isset( $bindData['middlename'] ) ) || ( $bindData['middlename'] === '' ) ) {
												$bindData['middlename']		=	'';
											}

											if ( ( ! isset( $bindData['lastname'] ) ) || ( $bindData['lastname'] === '' ) ) {
												$bindData['lastname']		=	$bindData['name'];
											}
										}
									} else {
										// Form name from first/middle/last name if needed:
										$nameParts							=	array();

										if ( isset( $bindData['firstname'] ) && ( $bindData['firstname'] !== '' ) ) {
											$nameParts[]					=	$bindData['firstname'];
										}

										if ( isset( $bindData['middlename'] ) && ( $bindData['middlename'] !== '' ) ) {
											$nameParts[]					=	$bindData['middlename'];
										}

										if ( isset( $bindData['lastname'] ) && ( $bindData['lastname'] !== '' ) ) {
											$nameParts[]					=	$bindData['lastname'];
										}

										if ( $nameParts ) {
											$bindData['name']				=	trim( implode( ' ', $nameParts ) );
										}
									}

									if ( ( ! isset( $bindData['username'] ) ) || ( $bindData['username'] === '' ) ) {
										// We need a username, but we're missing one so lets try to fallback
										$fallbackField						=	( isset( $ueConfig['usernamefallback'] ) && $ueConfig['usernamefallback'] ? $ueConfig['usernamefallback'] : 'name' );

										if ( $fallbackField === 'random' ) {
											$bindData['username']			=	$row->getRandomPassword();
										} elseif ( isset( $bindData[$fallbackField] ) && \is_string( $bindData[$fallbackField] ) && ( $bindData[$fallbackField] !== '' ) ) {
											$bindData['username']			=	$bindData[$fallbackField];
										}

										if ( ( ! isset( $bindData['username'] ) ) && isset( $bindData['email'] ) && ( $bindData['email'] !== '' ) ) {
											// Oh no! We still don't have one! Force to email as backup:
											$bindData['username']			=	$bindData['email'];
										}

										// Ok, one more try; lets see if we have a username now:
										if ( ( ! isset( $bindData['username'] ) ) && isset( $bindData['name'] ) && ( $bindData['name'] !== '' ) ) {
											// What in the world! We still don't have one! Force to name as backup:
											$bindData['username']			=	$bindData['name'];
										}

										// Now lets see if we finally have a username:
										if ( isset( $bindData['username'] ) && ( $bindData['username'] !== '' ) ) {
											// We do! Awesome! Now lets format it so it'll validate in Joomla by removing disallowed characters, all duplicate spacing, and replacing spaces with underscore:
											$bindData['username']			=	preg_replace( '/[<>\\\\"%();&\']+/', '', trim( $bindData['username'] ) );
										}
									}

									if ( ( ! isset( $bindData['name'] ) ) && isset( $bindData['username'] ) && ( $bindData['username'] !== '' ) ) {
										// Still don't have a name so fallback to username
										$bindData['name']					=	$bindData['username'];
									}
								}

								if ( $rowExists ) {
									$_PLUGINS->trigger( 'onBeforeUpdateUser', array( &$this, &$this, &$oldRow ) );
								} else {
									$_PLUGINS->trigger( 'onBeforeNewUser', array( &$this, &$this, false ) );
								}
							}

							if ( ! $row->bind( $bindData ) ) {
								$fail[$index]					=	CBTxt::T( 'CANNOT_IMPORT_ROW_BECAUSE_ERROR', 'Cannot import row [id] because: [error]', array( '[id]' => $index, '[error]' => $row->getError() ) );
								continue;
							}

							if ( ! $row->check() ) {
								$fail[$index]					=	CBTxt::T( 'CANNOT_IMPORT_ROW_BECAUSE_ERROR', 'Cannot import row [id] because: [error]', array( '[id]' => $index, '[error]' => $row->getError() ) );
								continue;
							}

							if ( ! $row->store( ( $rowExists ? true : false ) ) ) {
								$fail[$index]					=	CBTxt::T( 'CANNOT_IMPORT_ROW_BECAUSE_ERROR', 'Cannot import row [id] because: [error]', array( '[id]' => $index, '[error]' => $row->getError() ) );
								continue;
							}

							if ( $row instanceof UserTable ) {
								if ( $rowExists ) {
									$_PLUGINS->trigger( 'onAfterUpdateUser', array( &$row, &$row, $oldRow ) );
								} else {
									global $ueConfig;

									$emailPassCache				=	$ueConfig['emailpass'];
									$confirmCache				=	$ueConfig['reg_confirmation'];
									$approvalCache				=	$ueConfig['reg_admin_approval'];

									// Set a non-storing note for integrations and other internal behavior so they know this user is imported
									$row->set( '_isImported', true );

									if ( $clearPassword ) {
										// We've a cleartext password that we need to restore for activation emails:
										$row->set( 'password', $clearPassword );

										$ueConfig['emailpass']			=	1; // set this global to 1 to force password to be sent to new users.
									}

									if ( ! $row->getInt( 'confirmed', 0 ) ) {
										// User is not confirmed so toggle confirmation required so the activation email sends with the confirmation link
										$ueConfig['reg_confirmation']	=	1;
									}

									if ( ! $row->getInt( 'approved', 0 ) ) {
										// User is not approved so toggle approval required so the activation email informs them of the pending approval
										$ueConfig['reg_admin_approval']	=	1;
									}

									$_PLUGINS->trigger( 'onAfterNewUser', array( &$row, &$row, false, true ) );

									activateUser( $row, 2, 'NewUser', false, $notify );

									// Reset to cached values
									$ueConfig['emailpass']			=	$emailPassCache;
									$ueConfig['reg_confirmation']	=	$confirmCache;
									$ueConfig['reg_admin_approval']	=	$approvalCache;
								}
							}
						} catch ( \Exception $e ) {
							$fail[$index]						=	CBTxt::T( 'CANNOT_IMPORT_ROW_BECAUSE_ERROR', 'Cannot import row [id] because: [error]', array( '[id]' => $index, '[error]' => $e->getMessage() ) );
							continue;
						}

						$success[]								=	$index;
					}

					if ( $success && $fail ) {
						$message								=	CBTxt::T( 'ROWS_IMPORT_SUCCESS', '%%COUNT%% row successfully imported|%%COUNT%% rows successfully imported', array( '%%COUNT%%' => count( $success ) ) )
																.	'<br />' . CBTxt::T( 'ROWS_IMPORT_FAILED', '%%COUNT%% row failed to import|%%COUNT%% rows failed to import', array( '%%COUNT%%' => count( $fail ) ) )
																.	'<br /><br />' . implode( '<br />', $fail );

						$_CB_framework->enqueueMessage( $message, 'warning' );
					} elseif ( $success ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'ROWS_IMPORT_SUCCESS', '%%COUNT%% row successfully imported|%%COUNT%% rows successfully imported', array( '%%COUNT%%' => count( $success ) ) ), 'success' );
					} elseif ( $fail ) {
						$message								=	CBTxt::T( 'ROWS_IMPORT_FAILED', '%%COUNT%% row failed to import|%%COUNT%% rows failed to import', array( '%%COUNT%%' => count( $fail ) ) )
																.	'<br /><br />' . implode( '<br />', $fail );

						$_CB_framework->enqueueMessage( $message, 'error' );
					}
				}
				break;
			case 'action':
				if ( $this->listFieldsRows ) {

					if ( count( $cid ) != 1 ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'SELECT_A_ROW_TO_TASK', 'Select a row to [task]', array( '[task]' => ( isset( $task_parsed[1] ) ? $task_parsed[1] : 'action' ) ) ), 'error' );
						return;
					}

					if ( isset( $task_parsed[1] ) ) {
						if ( isset( $task_parsed[2] ) ) {
							$field		=	$task_parsed[2];
						} else {
							$field		=	'tid';
						}

						$baseUrl		=	'index.php?option=' . $this->_options['option'] . '&view=' . $this->_options['view'];

						if ( isset( $this->_options['pluginid'] ) ) {
							$baseUrl	.=	'&cid=' . $this->_options['pluginid'];
						}

						$url			=	$baseUrl . '&table=' . $this->_tableBrowserModel->attributes( 'name' ) . '&action=' . urlencode( $task_parsed[1] ) . '&' . urlencode( $field ) . '=' . urlencode( $cid[0] );

						cbRedirect( $url );
					}
				}
				break;
			default:
				break;
		}
		//TBD cbRedirect( $_CB_framework->backendUrl( 'index.php?option=com_comprofiler&task=showPlugins', $msg ) );
	}

	/**
	 * @param string $id
	 * @return string[]|string
	 */
	private function getDataModelValue( $id )
	{
		$allowedKeys			=	[];

		if ( $this->listFieldsRows ) {
			$primaryCheckbox	=	$this->listFieldsRows->getChildByNameAttr( 'field', 'type', 'primarycheckbox' );

			if ( $primaryCheckbox ) {
				$allowedKeys	=	explode( ',', (string) $primaryCheckbox->attributes( 'keys' ) );
			}
		}

		if ( \strpos( $id, '|*|' ) ) {
			$loadBy				=	[];

			foreach ( explode( '|*|', $id ) as $dataModelKeyValue ) {
				[ $idk, $idv ]	=	explode( '=', $dataModelKeyValue );

				if ( ! \in_array( $idk, $allowedKeys, true ) ) {
					continue;
				}

				$loadBy[$idk]	=	$idv;
			}

			return $loadBy;
		}

		if ( \strpos( $id, '=' ) ) {
			[ $idk, $idv ]		=	explode( '=', $id );

			if ( ! \in_array( $idk, $allowedKeys, true ) ) {
				return '';
			}

			return [ $idk => $idv ];
		}

		return $id;
	}

	/**
	 * Compacts the ordering sequence of the selected records
	 *
	 * @param  array           $cid                 array of string  table key ids which need to get saved ($row[]->ordering contains old ordering and $cid contains new ordering)
	 * @param  TableInterface  $row                 derived from TableInterface of corresponding class
	 * @param  array           $order               ?
	 * @param  string          $conditionStatement  Additional "WHERE" query to limit ordering to a particular subset of records
	 * @param  string          $orderingField       Field name for this ordering
	 */
	protected function saveOrder( $cid, &$row, &$order, $conditionStatement, $orderingField = 'ordering' ) {
		global $_CB_framework;

		$total		= count( $cid );
		$conditions = array();
		$cidsChanged	= array();

		// update ordering values
		for( $i=0; $i < $total; $i++ ) {
			$row->load( $cid[$i] );
			if ($row->$orderingField != $order[$i]) {
				$row->$orderingField = $order[$i];
				if (!$row->store()) {
					$_CB_framework->enqueueMessage( CBTxt::T( 'CANNOT_ORDER_ROW_ID_ID_BECAUSE_ERROR', 'Cannot order row id [id] because: [error]', array( '[id]' => $cid, '[error]' => $row->getError() ) ), 'error' );
					return;
				} // if
				$cidsChanged[] = $cid[$i];
				// remember to updateOrder this group if multiple groups (conditionStatement gives the group)
				if ($conditionStatement) {
					$condition=null;				// to make php checker happy: the next line defines $condition
					eval($conditionStatement);				//TODO remove eval() use (it's used a single time!)
					$found = false;
					foreach ( $conditions as $cond )
						if ($cond[1]==$condition) {
							$found = true;
							break;
						} // if
					if (!$found) $conditions[] = array($cid[$i], $condition);
				}
			} // if
		} // for

		if ($conditionStatement) {
			// execute updateOrder for each group
			foreach ( $conditions as $cond ) {
				$row->load( $cond[0] );

				if ( $row->hasFeature( 'ordered', $orderingField ) ) {
					/** @var CheckedOrderedTable $row */
					$row->updateOrder( $cond[1], $cidsChanged, $orderingField );
				}
			}
		} else if ($cidsChanged) {
			$row->load( $cidsChanged[0] );

			if ( $row->hasFeature( 'ordered', $orderingField ) ) {
				/** @var CheckedOrderedTable $row */
				$row->updateOrder( null, $cidsChanged, $orderingField );
			}
		}
	} // saveOrder

	/**
	 * Draws a list of a SQL table
	 *
	 * @param  string   $viewType   ( 'view', 'param', 'depends': means: <param> tag => param, <field> tag => view )
	 * @return string   HTML of table
	 */
	public function draw( $viewType = 'view' ) {
		global $_CB_Backend_Menu;

		if ( ! $this->name ) {
			$this->parseXML();		// get List scheme
		}

		$this->loadFilters();
		$this->loadBatchProcess();

		$this->_getTableState();

		$this->_performTableActions();

		if ( $this->limit < 1 ) {
			$this->limit = 10;
		}

		if ( ! $this->rows ) {
			$this->loadRows();			// get List content
		}

		$controller = new DrawController( $this->input, $this->_tableBrowserModel, $this->_actions, $this->_options );
		$controller->setControl_name( $this->name );
		if ( $this->listFieldsRows ) {
			$controller->createPageNvigator( $this->total, $this->limitstart, $this->limit, $this->limits );
		}
		$controller->setFilters( $this->_filterPossibilitesArray );
		$controller->setSearch( $this->search, ( $this->quicksearchfields && ( count( $this->quicksearchfields->children() ) > 0 ) ) );
		$controller->setOrderBy( $this->orderby );
		$controller->setBatchProcess( $this->_batchPossibilitesArray );
		$controller->setExport( $this->export );
		$controller->setImport( $this->import );
		$controller->setStatistics( $this->_statisticsToDisplay );

		if ( $this->toolbarmenu && ( count( $this->toolbarmenu->children() ) > 0 ) ) {
			$toolBarMenu					=	new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8"?><cbxml></cbxml>');

			foreach ( $this->toolbarmenu->children() as $menu ) {
				$this->registryEditVew->extendParamAttributes( $menu );

				$menuLink					=	$menu->attributes( 'link' );
				$menuAccess					=	true;
				$link						=	null;

				if ( $menuLink ) {
					$data					=	null;
					$link					=	$controller->drawUrl( $menuLink, $menu, $data, null );

					if ( ! $link ) {
						$menuAccess			=	false;
					}
				}

				if ( $menuAccess ) {
					/** @var $menu SimpleXMLElement */
					$child					=	$toolBarMenu->addChildWithAttr( 'menu', null, null, $menu->attributes() );

					if ( $link ) {
						$child->addAttribute( 'urllink', $link );
					}
				}
			}

			$_CB_Backend_Menu->menuItems[]	=	$toolBarMenu;
		}

		ob_start();
		$this->renderList( $this->_tableBrowserModel, $this->rows, $controller, $this->_options, $viewType );
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Renders as ECHO HTML code of a table
	 *
	 * @param SimpleXMLElement $modelView
	 * @param array $modelRows
	 * @param DrawController $controllerView
	 * @param array $options
	 * @param string $viewType ( 'view', 'param', 'depends': means: <param> tag => param, <field> tag => view )
	 */
	protected function renderList( &$modelView, &$modelRows, &$controllerView, &$options, $viewType = 'view'  ) {
		global $_CB_framework;

		static $JS_loaded					=	0;

		$pluginParams						=	$this->_pluginParams;

		$renderer							=	new RegistryEditView( $this->input, $this->_db, $pluginParams, $this->_types, $this->_actions, $this->_views, $this->_pluginObject, $this->_tabid );

		$renderer->setParentView( $modelView );
		$renderer->setModelOfDataRows( $modelRows );

		$name								=	$modelView->attributes( 'name' );
		$listFieldsRows						=	$modelView->getElementByPath( 'listfields/rows' );
		$listFieldsPager					=	$modelView->getElementByPath( 'listfields/paging' );
		$filtersArray						=	$controllerView->filters( $renderer, 'div');
		$batchArray							=	$controllerView->batchprocess( $renderer, 'div');
		$exportButton						=	$controllerView->export();
		$importUpload						=	$controllerView->import( $renderer );

		outputCbJs();

		$tableLabel							=	trim( CBTxt::Th( (string) $modelView->attributes( 'label' ) ) );
		$tableDescription					=	trim( CBTxt::Th( (string) $modelView->attributes( 'description' ) ) );
		$tableMenu							=	$modelView->getElementByPath( 'tablemenu' );
		$tableClasses						=	RegistryEditView::buildClasses( $modelView, array(), 'block' );

		if ( ! $JS_loaded++ ) {
			if ( $controllerView->pageNav !== null ) {
				$searchButtonJs				=	$controllerView->pageNav->limitstartJs( 0 );
			} else {
				$searchButtonJs				=	'cbParentForm( this ).submit();';
			}

			$js								=	"$( '.cbTableBrowserHeader' ).on( 'click', '.cbTableHeaderExpand', function() {"
											.		"$( this ).removeClass( 'btn-light border cbTableHeaderExpand' ).addClass( 'btn-primary cbTableHeaderCollapse' );"
											.		"$( this ).find( '.fa-caret-down' ).removeClass( 'fa-caret-down' ).addClass( 'fa-caret-up' );"
											.		"$( '.' + $( this ).data( 'toggle' ) ).hide().removeClass( 'hidden' ).slideDown();"
											.	"});"
											.	"$( '.cbTableBrowserHeader' ).on( 'click', '.cbTableHeaderCollapse', function() {"
											.		"var toggle = $( this ).data( 'toggle' );"
											.		"$( this ).removeClass( 'btn-primary cbTableHeaderCollapse' ).addClass( 'btn-light border cbTableHeaderExpand' );"
											.		"$( this ).find( '.fa-caret-up' ).removeClass( 'fa-caret-up' ).addClass( 'fa-caret-down' );"
											.		"$( '.' + toggle ).slideUp();"
											.		"if ( toggle == 'cbBatchTools' ) {"
											.			"$( '.' + toggle ).find( 'input,textarea,select' ).val( '' );"
											.			"if ( $.fn.cbselect ) {"
											.				"$( '.' + toggle ).find( 'select.cbSelect2' ).each( function() {"
											.					"$( this ).cbselect( 'set', '' );"
											.				"});"
											.			"}"
											.		"} else if ( toggle == 'cbSearchTools' ) {"
											.			"$( '.' + toggle ).find( 'input,textarea,select' ).each( function() {"
											.				"var value = null;"
											.				"if ( $( this ).hasClass( 'cbSelect2' ) ) {"
											.					"if ( $.fn.cbselect ) {"
											.						"value = $( this ).cbselect( 'get' );"
											.					"} else {"
											.						"value = $( this ).val();"
											.					"}"
											.				"} else {"
											.					"value = $( this ).val();"
											.				"}"
											.				"if ( ( value != null ) && ( value != '' ) ) {"
											.					"$( '.cbTableHeaderClear' ).click(); return;"
											.				"}"
											.			"});"
											.		"}"
											.	"});"
											.	"$( '.cbTableBrowserHeader' ).on( 'click', '.cbTableHeaderClear', function() {"
											.		"$( '.cbTableBrowserHeader' ).find( 'input,textarea,select' ).val( '' );"
											.		"if ( $.fn.cbselect ) {"
											.			"$( '.cbTableBrowserHeader' ).find( 'select.cbSelect2' ).each( function() {"
											.				"$( this ).cbselect( 'set', '' );"
											.			"});"
											.		"}"
											.		$searchButtonJs
											.	"});"
											.	"$( '.cbTableBrowserRowsHeader' ).on( 'click', '.cbTableBrowserSort', function() {"
											.		"$( '.cbTableBrowserHeader' ).find( '.cbTableBrowserSorting > select' ).val( $( this ).data( 'table-sort' ) ).change();"
											.	"});"
											.	( $this->_filtered ? "$( '.cbSearchToolsToggle' ).click();" : null );

			$_CB_framework->outputCbJQuery( $js );
		}

		$limitBoxOutput						=	false;

		$return								=	'<div class="mb-3 cbTableBrowserDiv' . ( $name ? ' cbDIV' . htmlspecialchars( $name ) : null ) . ( $tableClasses ? ' ' . $tableClasses : null ) . '">';

		if ( $tableLabel || $tableDescription ) {
			if ( $tableLabel ) {
				$return						.=		'<div class="mb-3 cb-page-header cbTableBrowserLabel' . ( $name ? ' cbTH' . htmlspecialchars( $name ) : null ) . '">'
											.			'<h3 class="m-0 p-0 cb-page-header-title cbTableBrowserTitle">' . $tableLabel . '</h3>'
											.			( $tableDescription ? '<div class="mt-2 cbTableBrowserDescription">' . $tableDescription . '</div>' : null )
											.		'</div>';
			} elseif ( $tableDescription ) {
				$return						.=		'<div class="mb-3 cb-page-header-description cbTableBrowserLabel' . ( $name ? ' cbTH' . htmlspecialchars( $name ) : null ) . ' cbTableBrowserDescription">'
											.			$tableDescription
											.		'</div>';
			}
		}

		if ( $tableMenu || $controllerView->hasSearchFields() || $controllerView->hasOrderbyFields() || ( count( $filtersArray ) > 0 ) || ( count( $batchArray ) > 0 ) || $exportButton || $importUpload ) {
			$return							.=		'<div class="row no-gutters mb-2 cbTableBrowserHeader' . ( $name ? ' cbTA' . htmlspecialchars( $name ) : null ) . '">';

			if ( $tableMenu ) {
				$return						.=			'<div class="col-sm-auto mb-2 mb-sm-0 pr-sm-2 cbTableBrowserMenu' . ( $name ? ' cbTM' . htmlspecialchars( $name ) : null ) . '">';

				$menuIndex					=	1;

				foreach ( $tableMenu->children() as $menu ) {
					/** @var SimpleXMLElement $menu */
					$menuAction				=	$menu->attributes( 'action' );
					$menuLabelHtml			=	trim( CBTxt::Th( htmlspecialchars( $menu->attributes( 'label' ) ) ) );
					$menuDesc				=	$menu->attributes( 'description' );

					if ( $menuDesc ) {
						$menuDesc			=	' title="' . trim( htmlspecialchars( CBTxt::T( $menuDesc ) ) ) . '"';
					}

					$return					.=					( $menuIndex > 1 ? ' ' : null );

					if ( $menuAction ) {
						$data				=	null;
						$link				=	$controllerView->drawUrl( $menuAction, $menu, $data, 0, true );

						if ( $link ) {
							$return			.=					'<a href="' . $link . '"' . $menuDesc . ' class="' . htmlspecialchars( RegistryEditView::buildClasses( $menu, array( 'btn', 'btn-sm-block', 'btn-' . ( $menuIndex == 1 ? 'success' : 'primary' ) ) ) ) . '">' . $menuLabelHtml . '</a>';
						}
					} elseif ( $menuDesc ) {
						$return				.=					'<span' . $menuDesc . '>' . $menuLabelHtml . '</span>';
					} else {
						$return				.=					$menuLabelHtml;
					}

					$menuIndex++;
				}

				$return						.=			'</div>';
			}

			if ( $controllerView->hasSearchFields() || $controllerView->hasOrderbyFields() || ( count( $filtersArray ) > 0 ) || ( count( $batchArray ) > 0 ) || $exportButton || $importUpload ) {
				$searchButtons				=	array();

				if ( count( $filtersArray ) > 0 ) {
					$searchButtons[]		=	'<button type="button" class="btn btn-light border btn-sm-block cbSearchToolsToggle cbTableHeaderExpand" data-toggle="cbSearchTools">' . CBTxt::Th( 'Search Tools' ) . ' <span class="fa fa-caret-down"></span></button>';
				}

				if ( count( $batchArray ) > 0 ) {
					$searchButtons[]		=	'<button type="button" class="btn btn-light border btn-sm-block cbBatchToolsToggle cbTableHeaderExpand" data-toggle="cbBatchTools">' . CBTxt::Th( 'Batch Tools' ) . ' <span class="fa fa-caret-down"></span></button>';
				}

				$return						.=			'<div class="col-sm cbTableHeaderTools">'
											.				'<div class="row no-gutters cbTableBrowserTools">'
											.					'<div class="col-sm cbTableBrowserSearching">';

				if ( $controllerView->hasSearchFields() || $searchButtons ) {
					$searchButtons[]		=	'<button type="button" class="btn btn-light border btn-sm-block cbTableHeaderClear">' . CBTxt::Th( 'Clear' ) . '</button>';;

					$return					.=						'<div class="row no-gutters">';

					if ( $controllerView->hasSearchFields() ) {
						$return				.=							'<div class="col-sm pr-sm-2">'
											.								$controllerView->quicksearchfields()
											.							'</div>'
											.							'<div class="col-sm-6' . ( ! $controllerView->hasOrderbyFields() && ( $exportButton || $importUpload ) ? ' col-lg-8' : null ) . ' mt-2 mt-sm-0">';
					} else {
						$return				.=							'<div class="col-sm">';
					}

					$return					.=								implode( ' ', $searchButtons )
											.							'</div>'
											.						'</div>';
				}

				$return						.=					'</div>';

				if ( $controllerView->hasOrderbyFields() ) {
					$return					.=					'<div class="text-left text-sm-right pl-sm-2 col-sm-auto' . ( ( $controllerView->hasSearchFields() || $searchButtons ) ? ' mt-2 mt-sm-0' : null ) . ' cbTableBrowserSorting">'
											.						( $controllerView->hasOrderbyFields() ? $controllerView->orderbyfields() : null );

					if ( $controllerView->pageNav !== null ) {
						$controllerView->pageNav->setClasses( array( 'cbPaginationLimit' => ( $controllerView->hasOrderbyFields() ? 'w-sm-100 ' : null ) . 'd-inline-block mt-2 mt-sm-0', 'cbPageLimitbox' => 'w-sm-100' ), true );

						$return				.=						$controllerView->pageNav->getLimitBox();

						$limitBoxOutput		=	true;
					}

					$return					.=					'</div>';
				}

				if ( $exportButton || $importUpload ) {
					$return					.=					'<div class="text-left text-sm-right pl-sm-2 col-sm-auto' . ( ( $controllerView->hasSearchFields() || $searchButtons || $controllerView->hasOrderbyFields() ) ? ' mt-2 mt-sm-0' : null ) . ' cbTableBrowserExportImport">'
											.						( $exportButton && $importUpload ? '<div class="w-sm-100 btn-group">' : null )
											.						$exportButton;

					if ( $importUpload ) {
						$return				.=						'<button type="button" class="w-sm-100 btn btn-light border cbImportToggle cbTableHeaderExpand" data-toggle="cbImportTools">'
											.							'<span class="fa fa-upload"></span><span class="d-inline-block d-sm-none pl-1">' . CBTxt::T( 'Import' ) . '</span>'
											.						'</button>';
					}

					$return					.=						( $exportButton && $importUpload ? '</div>' : null )
											.					'</div>';
				}

				$return						.=				'</div>';
				$return						.=			'</div>';

				if ( count( $filtersArray ) > 0 ) {
					$return					.=			'<div class="col-12 cbFilters cbSearchTools hidden">'
											.				'<fieldset class="border d-block p-2 w-100 cbFieldset">'
											.					'<legend class="border-0 w-auto pr-1 pl-1 cbFieldsetLegend">' . CBTxt::Th( 'Search Tools' ) . '</legend>'
											.					implode( ' ', $filtersArray )
											.				'</fieldset>'
											.			'</div>';
				}

				if ( count( $batchArray ) > 0 ) {
					$return					.=			'<div class="col-12 cbBatchProcess cbBatchTools hidden">'
											.				'<fieldset class="border d-block p-2 w-100 cbFieldset">'
											.					'<legend class="border-0 w-auto pr-1 pl-1 cbFieldsetLegend">' . CBTxt::Th( 'Batch Tools' ) . '</legend>'
											.					implode( ' ', $batchArray )
											.				'</fieldset>'
											.			'</div>';
				}

				if ( $importUpload ) {
					$return					.=			'<div class="col-12 cbImport cbImportTools hidden">'
											.				'<fieldset class="border d-block p-2 w-100 cbFieldset">'
											.					'<legend class="border-0 w-auto pr-1 pl-1 cbFieldsetLegend">' . CBTxt::Th( 'Import Tools' ) . '</legend>'
											.					$importUpload
											.				'</fieldset>'
											.			'</div>';
				}
			}

			$return							.=		'</div>';
		}

		if ( $listFieldsRows ) {
			$columnCount					=	0;

			$return							.=		'<div class="table-responsive mb-3">'
											.			'<table class="table table-hover table-striped mb-0 cbTableBrowserRows' . ( $name ? ' cbTL' . htmlspecialchars( $name ) : null ) . '">'
											.				'<thead>'
											.					'<tr class="cbTableBrowserRowsHeader">';

			foreach ( $listFieldsRows->children() as $field ) {
				/** @var SimpleXMLElement $field */
				if ( $field->getName() != 'field' ) {
					continue;
				}

				if ( ( $field->attributes( 'type' ) != 'hidden' ) && Access::authorised( $field ) ) {
					$classes				=	RegistryEditView::buildClasses( $field, array(), 'table-cell' );
					$attributes				=	( $classes ? ' class="' . htmlspecialchars( $classes ) . '"' : null )
											.	( $field->attributes( 'width' ) || $field->attributes( 'align' ) ? ' style="' . ( $field->attributes( 'width' ) ? 'width: ' . htmlspecialchars( $field->attributes( 'width' ) ) . ';' : null ) . ( $field->attributes( 'align' ) ? 'text-align: ' . htmlspecialchars( $field->attributes( 'align' ) ) . ';' : null ) . '"' : null )
											.	( $field->attributes( 'nowrap' ) ? ' nowrap="nowrap"' : null );
					$fieldName				=	$field->attributes( 'name' );
					$fieldOrdering			=	$field->attributes( 'allowordering' );

					$return					.=						'<th' . $attributes . ' scope="col">';

					if ( $field->attributes( 'type' ) == 'primarycheckbox' ) {
						$jsToggleAll		=	"cbToggleAll( this, " . count( $modelRows ) . ", '" . $controllerView->fieldId( 'id' ) . "' );";

						$return				.=							'<input type="checkbox" id="' . $controllerView->fieldId( 'toggle' ) . '" name="' . $controllerView->fieldName( 'toggle' ) . '" value="" onclick="' . $jsToggleAll . '" class="m-0" />';
					} else {
						$fieldIcon			=	null;

						if ( $fieldOrdering ) {
							$fieldSort		=	explode( ',', $fieldOrdering );
							$fieldAsc		=	in_array( 'ascending', $fieldSort );
							$fieldDesc		=	in_array( 'descending', $fieldSort );

							if ( $fieldAsc && ( $this->orderby == ( $fieldName . '_asc' ) ) ) {
								// If ascending is allowed and is already active then set click to descending if descending is allowed:
								if ( $fieldDesc ) {
									$return	.=							'<a href="javascript: void(0);" class="cbTableBrowserSort cbTableBrowserSortDesc" data-table-sort="' . htmlspecialchars( $fieldName . '_desc' ) . '">';
								} else {
									$return	.=							'<a href="javascript: void(0);">';
								}

								$fieldIcon	=							' <span class="fa fa-sort-alpha-asc text-secondary"></span>';
							} elseif ( $fieldDesc && ( $this->orderby == ( $fieldName . '_desc' ) ) ) {
								// If descending is allowed and is already active then set click to ascending if ascending is allowed:
								if ( $fieldAsc ) {
									$return	.=							'<a href="javascript: void(0);" class="cbTableBrowserSort cbTableBrowserSortAsc" data-table-sort="' . htmlspecialchars( $fieldName . '_asc' ) . '">';
								} else {
									$return	.=							'<a href="javascript: void(0);">';
								}

								$fieldIcon	=							' <span class="fa fa-sort-alpha-desc text-secondary"></span>';
							} elseif ( $fieldSort[0] == 'ascending' ) {
								// Default to ascending if this field allows it:
								$return		.=							'<a href="javascript: void(0);" class="cbTableBrowserSort cbTableBrowserSortAsc" data-table-sort="' . htmlspecialchars( $fieldName . '_asc' ) . '">';
							} elseif ( $fieldSort[0] == 'descending' ) {
								// Default to descending if this field allows it:
								$return		.=							'<a href="javascript: void(0);" class="cbTableBrowserSort cbTableBrowserSortDesc" data-table-sort="' . htmlspecialchars( $fieldName . '_desc' ) . '">';
							} else {
								$return		.=							'<a href="javascript: void(0);">';
							}
						}

						$return				.=							( $field->attributes( 'description' ) ? cbTooltip( 2, CBTxt::Th( $field->attributes( 'description' ) ), null, null, null, CBTxt::Th( $field->attributes( 'label' ) ), null, 'data-hascbtooltip="true"' ) : CBTxt::Th( $field->attributes( 'label' ) ) );

						if ( $fieldOrdering ) {
							$return			.=							$fieldIcon . '</a>';
						}
					}

					if ( $field->attributes( 'type' ) == 'ordering' ) {
						if ( ( ! $fieldOrdering ) || in_array( $this->orderby, array( $fieldName . '_asc', $fieldName . '_desc', $fieldName ) ) ) {
							if ( $fieldOrdering ) {
								$field->addAttribute( 'noordering', 'false' );
							}

							if ( strpos( $field->attributes( 'onclick' ), 'number' ) !== false ) {
								$jsOrderSave	=	"cbsaveorder( this, " . count( $modelRows ) . ", '" . $controllerView->fieldId( 'id', null, false ) . "', '" . $controllerView->taskName( false ). "', '" . $controllerView->subtaskName( false ). "', '" . $controllerView->subtaskValue( 'saveorder/' . $field->attributes( 'name' ), false ) . "' );";

								$return			.=							' <a href="javascript: void(0);" onclick="' . $jsOrderSave . '">'
												.								'<span class="fa fa-save text-secondary" title="' . htmlspecialchars( CBTxt::T( 'Save Order' ) ) . '"></span>'
												.							'</a>';
							}
						} else {
							if ( $fieldOrdering ) {
								$field->addAttribute( 'noordering', 'true' );
							}
						}
					}

					$return					.=						'</th>';

					$columnCount++;
				}
			}

			$return							.=					'</tr>'
											.				'</thead>'
											.				'<tbody>';

			$total							=	count( $modelRows );

			$controllerView->pageNav->setRowsNumber( $total );

			if ( $total ) for ( $i = 0; $i < $total; $i++ ) {
				$controllerView->pageNav->setRowIndex( $i );
				$renderer->setModelOfDataRowsNumber( $i );

				$row						=	$modelRows[$i];
				$rowlink					=	$listFieldsRows->attributes( 'link' );

				if ( $rowlink ) {
					$hrefRowEdit			=	$controllerView->drawUrl( $rowlink, $listFieldsRows, $row, $row->id, false );

					if ( $hrefRowEdit ) {
						if ( $listFieldsRows->attributes( 'target' ) == '_blank' ) {
							$onclickJS		=	'window.open(\'' . htmlspecialchars( cbUnHtmlspecialchars( $hrefRowEdit ) ) . '\', \'cbinvoice\', \'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\'); return false;';
						} else {
							$onclickJS		=	"window.location='" . htmlspecialchars( cbUnHtmlspecialchars( $hrefRowEdit ) ) . "'";
						}

						$rowOnclickHtml		=	' onclick="' . $onclickJS . '"';
					} else {
						$rowOnclickHtml		=	null;
					}
				} else {
					$rowOnclickHtml			=	null;
				}

				$controllerView->setControl_name( $this->name . '[rows][' . $i . ']' );

				$return						.=					'<tr class="cbTableBrowserRow"' . $rowOnclickHtml . '>'
											.						$renderer->renderEditRowView( $listFieldsRows, $row, $controllerView, $options, $viewType, 'td' )
											.					'</tr>';
			}

			$controllerView->setControl_name( $this->name );

			$return							.=				'</tbody>';

			if ( $total && ( ( ! $listFieldsPager ) || ( $listFieldsPager && ( $listFieldsPager->attributes( 'type' ) != 'none' ) ) ) ) {
				if ( $listFieldsPager ) {
					$showPageLinks			=	( strpos( $listFieldsPager->attributes( 'type' ), 'nopagelinks' ) === false );
					$showLimitBox			=	( $limitBoxOutput ? 0 : ( strpos( $listFieldsPager->attributes( 'type' ), 'nolimitbox' ) === false ) );
					$showPagesCount			=	( strpos( $listFieldsPager->attributes( 'type' ), 'nopagescount' ) === false );
				} else {
					$showPageLinks			=	true;
					$showLimitBox			=	( $limitBoxOutput ? 0 : true );
					$showPagesCount			=	true;
				}

				if ( $controllerView->pageNav->total <= $controllerView->pageNav->limit ) {
					$showPageLinks			=	false;
				}

				$return						.=			'</table>'
											.		'</div>'
											.		$controllerView->pageNav->getListFooter( $showPageLinks, $showLimitBox, $showPagesCount );
			} elseif ( ( $controllerView->pageNav !== null ) && ( ! $limitBoxOutput ) ) {
				$return						.=			'</table>'
											.		'</div>'
											.		$controllerView->pageNav->getLimitBox( false );
			} else {
				$return						.=			'</table>'
											.		'</div>';
			}
		} elseif ( ( $controllerView->pageNav !== null ) && ( ! $limitBoxOutput ) ) {
			$return							.=		$controllerView->pageNav->getLimitBox( false );
		}

		$return								.=		'<input type="hidden" name="' . $controllerView->fieldName( 'subtask' ) . '" value="" />';

		$statistics							=	$controllerView->getStatistics();

		if ( $statistics ) foreach ( $statistics as $stat ) {
			$statView						=	$renderer->renderEditRowView( $stat['view'], $stat['values'], $controllerView, $options, 'view' );

			if ( ! $statView ) {
				continue;
			}

			$return							.=		'<div class="mt-3 cbTableBrowserStatisticsDiv">' . $statView . '</div>';
		}

		$return								.=	'</div>';

		echo $return;
	}
}
