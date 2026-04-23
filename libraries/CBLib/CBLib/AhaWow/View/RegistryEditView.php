<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 11/12/13 4:59 PM $
* @package CBLib\AhaWow\View
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CBLib\AhaWow\View;

use CBLib\AhaWow\Access;
use CBLib\AhaWow\Controller\DrawController;
use CBLib\AhaWow\Controller\Elements\Menu;
use CBLib\AhaWow\Controller\Elements\TableBrowser;
use CBLib\AhaWow\Controller\RegistryEditController;
use CBLib\AhaWow\Model\Context;
use CBLib\AhaWow\Model\XmlQuery;
use CBLib\AhaWow\Model\XmlTypeCleanQuote;
use CBLib\Application\Application;
use CBLib\Database\DatabaseDriverInterface;
use CBLib\Input\InputInterface;
use CBLib\Language\CBTxt;
use CBLib\Registry\RegistryInterface;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;
use CBLib\Xml\SimpleXMLElement;
use CBLib\Database\Table\TableInterface;
use CB\Database\Table\PluginTable;
use GuzzleHttp;
use Exception;
// Very temporarily:
use moscomprofilerHTML;
use cbCalendars;
use cbTabs;
use cbValidator;

defined('CBLIB') or die();

/**
 * CBLib\AhaWow\View\RegistryEditView Class implementation
 */
class RegistryEditView {
	var $_i						 =	0;

	/**
	 * A stack (array) of the data which is a class
	 * @var RegistryInterface[] */
	var $_modelOfData			=	array();

	/**
	 * The data rows (for ordering arrows)
	 * @var RegistryInterface[] */
	var $_modelOfDataRows		=	null;

	/**
	 * The current row number (for ordering arrows)
	 * @var int */
	var $_modelOfDataRowsNumber	=	null;

	/**
	 * Extending view functions
	 * @var RegistryEditView */
	var $_extendViewParser		=	null;

	/**
	 * Extending view functions
	 * @var RegistryEditView */
	var $oldExtendViewParser	=	null;

	/**
	 * @var SimpleXMLElement
	 */
	private $oldExtendParserNode = null;

	/**
	 * Drawing controller
	 * @var DrawController */
	var $_controllerView		=	null;

	/**
	 * The options from url REQUEST
	 * @var array of string */
	var $_options				=	null;

	/**
	 * The plugin parameters
	 * @var RegistryInterface */
	var $_pluginParams			=	null;

	/**
	 * The parameters objects for individual columns (cache)
	 * @var array of RegistryInterface */
	var $_paramsOfColumns		=	null;

	/**
	 * The xml <types> element
	 * @var SimpleXMLElement */
	var $_types					=	null;

	/**
	 * The xml <actions> element
	 * @var SimpleXMLElement */
	var $_actions				=	null;

	/**
	 * The xml <views> element
	 * @var SimpleXMLElement */
	var $_views					=	null;

	/**
	 * The xml parent element
	 * @var SimpleXMLElement */
	var $_parentModelOfView		=	null;

	/**
	 * The plugin object
	 * @var PluginTable */
	var $_pluginObject			=	null;

	/**
	 * Id of tab
	 * @var int */
	var $_tabid					=	null;

	/**
	 * internal temporary var: if render as view (true) or as param (false)
	 * @var boolean */
	var $_view					=	null;

	/**
	 * @var DatabaseDriverInterface
	 */
	protected $_db;

	/**
	 * methods of this class
	 * @var array */
	var $_methods				=	null;

	/**
	 * javascript ifs that have been conditioned away
	 * @var array
	 */
	var $_jsconditioned         =   array();

	/**
	 * javascript ifs descriptions
	 * @var array
	 */
	var $_jsif                  =   array();

	/**
	 * javascript repeat needed
	 * @var bool
	 */
	var $_jsrepeat              =   false;

	/**
	 * flags subequent params as being inside a repeat usage
	 * @var bool
	 */
	var $_isrepeat              =   false;

	/**
	 * javascript select2 needed
	 * @var bool
	 */
	var $_jsselect2             =   false;

	/**
	 * list of possible values
	 * @var array of stdClass: 'name' => object (->value, (optional ->index), ->text) */
	var $_selectValues			=	array();

	/**
	 * Do an inverted parsing when inheriting
	 * @var bool
	 */
	var $_inverted		        =	false;

	/**
	 * List of XML extended view parsers
	 * @var SimpleXMLElement[]
	 */
	var $_extenders		        =	array();

	/**
	 * Names of tab panes
	 * @var string[]
	 */
	var $tabpaneNames           =   array();

	/**
	 * If fieldsListArray method returns keys (false, default) or keyed values (true)
	 * @var bool
	 */
	protected static $fieldsListArrayValues		=	false;

	/**
	 * @var InputInterface
	 */
	protected $input			=	null;

	/**
	 * Constructor
	 *
	 * @param  InputInterface           $input         The user form input
	 * @param  DatabaseDriverInterface  $db            The user form input
	 * @param  RegistryInterface        $pluginParams  The parameters of the plugin
	 * @param  SimpleXMLElement         $types         The types definitions in XML
	 * @param  SimpleXMLElement         $actions       The actions definitions in XML
	 * @param  SimpleXMLElement         $views         The views definitions in XML
	 * @param  PluginTable              $pluginObject  The plugin object
	 * @param  int                      $tabId         The tab id (if there is one)
	 */
	public function __construct( InputInterface $input, DatabaseDriverInterface $db,
								 RegistryInterface $pluginParams = null,
								 SimpleXMLElement $types = null, SimpleXMLElement $actions = null,
								 SimpleXMLElement $views = null, PluginTable $pluginObject = null,
								 $tabId = null )
	{
		$this->input				=	$input;
		$this->_db					=	$db;
		$this->_pluginParams		=	$pluginParams;
		$this->_types				=	$types;
		$this->_actions				=	$actions;
		$this->_views				=	$views;
		$this->_pluginObject		=	$pluginObject;
		$this->_tabid				=	$tabId;
	}

	/**
	 * Sets the parent view for an extended view parser
	 *
	 * @param  SimpleXMLElement  $modelView  The model view of the parent viewer
	 * @return void
	 */
	public function setParentView( $modelView ) {
		$this->_parentModelOfView	=	$modelView;
		if ( isset( $this->_extendViewParser ) && ( $this->_extendViewParser->_parentModelOfView === null ) ) {
			$this->_extendViewParser->setParentView( $modelView );
		}
	}

	/**
	 * Pushes the current model of data onto the stack and sets a new model of data $modelOfData
	 *
	 * @param  RegistryInterface|TableInterface  $modelOfData  The model data
	 * @return void
	 */
	public function pushModelOfData( &$modelOfData ) {
		array_unshift( $this->_modelOfData, $modelOfData );
	}

	/**
	 * Pops a model of data
	 */
	public function popModelOfData( ) {
		array_shift( $this->_modelOfData );
	}

	/**
	 * Returns the model of data
	 *
	 * @return RegistryInterface|TableInterface  The model of the data
	 */
	public function getModelOfData( ) {
		return $this->_modelOfData[0];
	}

	/**
	 * Sets the model of data rows (the other rows of the current model (useful for list views controls)
	 *
	 * @param  GetterInterface|RegistryInterface[]|TableInterface[]  $modelOfDataRows  The models of all data rows that are displayed around the current row
	 * @return void
	 */
	public function setModelOfDataRows( $modelOfDataRows ) {
		$this->_modelOfDataRows		=	$modelOfDataRows;
	}

	/**
	 * Sets the row number for current model
	 *
	 * @param  int  $i  row index number
	 * @return void
	 */
	public function setModelOfDataRowsNumber( $i ) {
		$this->_modelOfDataRowsNumber = $i;
		if ( $this->_extendViewParser ) {
			$this->_extendViewParser->setModelOfDataRowsNumber( $i );
		}
	}

	/**
	 * Sets an extended view parser
	 * This method is experimental and not part of CB API.
	 *
	 * @param  SimpleXMLElement  $extendedViewParserElement  An Object of class className (where className is from an xml element like <extendxmlparser class="className" /> where className extends RegistryEditView
	 * @return void
	 */
	public function setExtendedViewParser( $extendedViewParserElement ) {
		if ( $extendedViewParserElement ) {
			$class			=	$extendedViewParserElement->attributes( 'class' );
			if ( $class ) {
				$extendedViewParser			=	new $class( $this->_pluginParams, $this->_types, $this->_actions, $this->_views, $this->_pluginObject, $this->_tabid, $this );
				$this->_extendViewParser	=	$extendedViewParser;
			}
		}
	}

	/**
	 * Sets a temporary extended view parser (old CBSubs GPL 3.0.0)
	 *
	 * @param  SimpleXMLElement  $extendedViewParserElement  An Object of class className (where className is from an xml element like <extendxmlparser class="className" /> where className extends RegistryEditView
	 * @return void
	 */
	private function setOldExtendedViewParser( $extendedViewParserElement ) {
		$class							=	$extendedViewParserElement->attributes( 'class' );
		if ( $class && ! $this->oldExtendViewParser ) {
			$extendedViewParser			=	new $class( $this->_pluginParams, $this->_types, $this->_actions, $this->_views, $this->_pluginObject, $this->_tabid, $this );
			$this->oldExtendViewParser	=	$extendedViewParser;
		}
	}

	/**
	 * Sets Selected Values
	 *
	 * @param  SimpleXMLElement  $node          The node that has the select
	 * @param  array               $selectValues  The values currently selected
	 * @return void
	 */
	public function setSelectValues( $node, $selectValues ) {
		$this->_selectValues[$node->attributes( 'name' )]	=	$selectValues;
	}

	public static function setFieldsListArrayValues( $keyedValues )
	{
		self::$fieldsListArrayValues	=	$keyedValues;
	}
	/**
	 * Gets Selected Values
	 *
	 * @param  SimpleXMLElement  $node  The node to get the values for
	 * @return array                      The values currently selected
	 */
	public function & _getSelectValues( &$node ) {
		$nodeName			=	$node->attributes( 'name' );
		if ( isset( $this->_selectValues[$nodeName] ) ) {
			return $this->_selectValues[$nodeName];
		} else {
			$arr	=	array();
			return $arr;
		}
	}

	/**
	 * Renders as ECHO HTML code of a table
	 *
	 * @param SimpleXMLElement                       $modelOfView     The model of the view
	 * @param RegistryInterface|RegistryInterface[]  $modelOfData     The data of the model ( $row object )
	 * @param DrawController                         $controllerView  The controller that will be drawing the view
	 * @param array                                  $options         The input request options
	 * @param string                                 $viewType        The view type ( 'view', 'param', 'depends': means: <param> tag => param, <field> tag => view )
	 * @param string                                 $htmlFormatting  The HTML/array formatting to do ( 'table', 'td', 'none', 'fieldsListArray' )
	 * @return array|string                                           array if $htmlFormatting == 'fieldsListArray', otherwise html string
	 */
	public function renderEditRowView( &$modelOfView, &$modelOfData, &$controllerView, $options, $viewType = 'depends', $htmlFormatting = 'div' ) {
		global $_CB_framework;

		if ( $this->_parentModelOfView === null ) {
			$this->setParentView( $modelOfView );
		}

		$this->pushModelOfData( $modelOfData );

		$this->_controllerView	=	$controllerView;
		$this->_options			=	$options;

		if ( $this->_extendViewParser ) {
			$html				=	$this->_extendViewParser->renderEditRowView( $modelOfView, $modelOfData, $controllerView, $options, $viewType, $htmlFormatting );

			if ( $html ) {
				return $html;
			}
		}

		$html					=	array();

		if ( $htmlFormatting == 'table' ) {
			$label				=	$modelOfView->attributes( 'label' );
			$description		=	$modelOfView->attributes( 'description' );

			if ( $label || $description ) {
				if ( $label ) {
					$html[]		=		'<div class="mb-3 border-bottom cb-page-header cbViewHeader">'
								.			'<h3 class="m-0 p-0 mb-2 cb-page-header-title cbViewTitle">' . CBTxt::Th( $label ) . '</h3>';

					if ( $description ) {
						$html[]	=			'<div class="mb-2 cb-page-header-description cbViewDescription">'
								.				CBTxt::Th( $description )
								.			'</div>';
					}

					$html[]		=		'</div>';
				} elseif ( $description ) {
					$html[]		=		'<div class="mb-3 cb-page-header-description cbViewHeader cbViewDescription">'
								.			CBTxt::Th( $description )
								.		'</div>';
				}
			}

			$classes			=	RegistryEditView::buildClasses( $modelOfView, array(), 'table' );

			$html[]				=	'<table class="table table-sm table-borderless m-0' . ( $classes ? ' ' . htmlspecialchars( $classes ) : null ) . '">';
		} elseif ( $htmlFormatting == 'div' ) {
			$classes			=	RegistryEditView::buildClasses( $modelOfView, array(), 'block' );

			$html[]				=	'<div class="cbformdiv' . ( $classes ? ' ' . htmlspecialchars( $classes ) : null ) . '">';

			$label				=	$modelOfView->attributes( 'label' );
			$description		=	$modelOfView->attributes( 'description' );

			if ( $label || $description ) {
				if ( $label ) {
					$html[]		=		'<div class="mb-3 border-bottom cb-page-header cbViewHeader">'
								.			'<h3 class="m-0 p-0 mb-2 cb-page-header-title cbViewTitle">' . CBTxt::Th( $label ) . '</h3>';

					if ( $description ) {
						$html[]	=			'<div class="mb-2 cb-page-header-description cbViewDescription">'
								.				CBTxt::Th( $description )
								.			'</div>';
					}

					$html[]		=		'</div>';
				} elseif ( $description ) {
					$html[]		=		'<div class="mb-3 cb-page-header-description cbViewHeader cbViewDescription">'
								.			CBTxt::Th( $description )
								.		'</div>';
				}
			}
		}

		$this->_methods			=	get_class_methods( get_class( $this ) );
		$this->_jsif			=	array();
		$this->_jsrepeat		=	false;
		$this->_jsselect2		=	false;

		$tabs					=	new cbTabs( 0, $_CB_framework->getUi() );

		$html[]					=	$this->renderAllParams( $modelOfView, $controllerView->control_name(), $tabs, $viewType, $htmlFormatting );

		if ( $htmlFormatting == 'table' ) {
			$html[]				=	'</table>';
		} elseif ( $htmlFormatting == 'div' ) {
			$html[]				=	'</div>';
		}

		if ( $htmlFormatting != 'fieldsListArray' ) {
			$jsCode				=	$this->_compileJsCode();

			if ( $jsCode ) {
				$_CB_framework->document->addHeadScriptDeclaration( $jsCode );
			}

			if ( $this->_jsrepeat ) {
				static $repeat	=	0;

				if ( ! $repeat++ ) {
					$_CB_framework->outputCbJQuery( "$( '.cbRepeat' ).cbrepeat();", 'cbrepeat' );
				}
			}

			if ( $this->_jsselect2 ) {
				static $select2	=	0;

				if ( ! $select2++ ) {
					$js			=	"$( '.cbSelect' ).cbselect({"
								.		"language: {"
								.			"errorLoading: function() {"
								.				"return '" . addslashes( CBTxt::T( 'The results could not be loaded.' ) ) . "';"
								.			"},"
								.			"inputTooLong: function() {"
								.				"return '" . addslashes( CBTxt::T( 'Search input too long.' ) ) . "';"
								.			"},"
								.			"inputTooShort: function() {"
								.				"return '" . addslashes( CBTxt::T( 'Search input too short.' ) ) . "';"
								.			"},"
								.			"loadingMore: function() {"
								.				"return '" . addslashes( CBTxt::T( 'Loading more results...' ) ) . "';"
								.			"},"
								.			"maximumSelected: function() {"
								.				"return '" . addslashes( CBTxt::T( 'You cannot select any more choices.' ) ) . "';"
								.			"},"
								.			"noResults: function() {"
								.				"return '" . addslashes( CBTxt::T( 'No results found.' ) ) . "';"
								.			"},"
								.			"searching: function() {"
								.				"return '" . addslashes( CBTxt::T( 'Searching...' ) ) . "';"
								.			"}"
								.		"}"
								.	"});";

					$_CB_framework->outputCbJQuery( $js, 'cbselect' );
				}
			}
		}

		return ( $htmlFormatting == 'fieldsListArray' ? $this->arrayValuesMerge( $html ) : trim( implode( "\n", $html ) ) );
	}

	/**
	 * Gets the data from the model for a field $key
	 *
	 * @param  string  $key      The name of the field
	 * @param  mixed   $default  The default value if not found
	 * @return string
	 *
	 * @throws \Exception
	 */
	public function get( $key, $default=null ) {
		if ( isset( $this->_modelOfData[0] ) ) {
			if ( is_callable( array( $this->_modelOfData[0], 'get' ) ) ) {
				$data					=	$this->_modelOfData[0]->get( $key );

				// If no default is supplied lets try and find one in XML:
				if ( ( $default === null ) && $this->_parentModelOfView ) {
					$xmlNode			=	$this->_parentModelOfView->xpath( '//param[@name="' . $key . '"]' );

					if ( ! $xmlNode ) {
						$xmlNode		=	$this->_parentModelOfView->xpath( '//field[@name="' . $key . '"]' );
					}

					if ( $xmlNode && ( count( $xmlNode ) ) ) {
						$default		=	$xmlNode[0]->attributes( 'default' );
					} else {
						$default		=	null;
					}
				}
			} else {
				// Since CB 2.0:
				throw new Exception(sprintf( __CLASS__ . '::get(): Fatal loading error: missing get() function in class/variable type %s', gettype( $this->_modelOfData[0] ) ) );
				/* Old way:
				if ( isset( $this->_modelOfData[0]->$key ) ) {
					$data				=	$this->_modelOfData[0]->$key;
				} else {
					$data				=	null;
				}
				*/
			}
		} else {
			$data						=	null;
		}
		if ( $data !== null ) {
			if ( is_array( $default ) && ! is_array( $data ) ) {
				if ( strpos( $data, '|**|' ) === 0 ) {
					// indexed array:
					$parts				=	explode( '|**|', substr( $data, 4 ) );
					$r					=	array();
					foreach ( $parts as $v ) {
						$p				=	explode( '=', $v, 2 );
						if ( isset( $p[1] ) ) {
							$r[$p[0]]	=	$p[1];
						}
					}
					return $r;
				} else {
					// non-indexed array:
					return explode( '|*|', $data );
				}
			} else {
				return $data;
			}
		} else {
			$isArray		=	strpos( $key, '[' );
			if ( $isArray ) {
				// case of indexed arrays:
				$index		=	substr( $key, $isArray + 1, strpos( $key, ']' ) - $isArray -1 );
				$arrayString =	$this->get( substr( $key, 0, $isArray ) );
				if ( is_array( $arrayString ) ) {
					if ( isset( $arrayString[$index] ) ) {
						return $arrayString[$index];
					}
				} else {
					if ( $arrayString && ( strpos( $arrayString, '|**|' ) === 0 ) ) {
						$parts	=	explode( '|**|', substr( $arrayString, 4 ) );
						foreach ( $parts as $v ) {
							$p	=	explode( '=', $v, 2 );
							if ( $p[0] == $index ) {
								if ( isset( $p[1] ) ) {
									return $p[1];
								}
							}
						}
					}
				}
			}
			return $default;
		}
	}

	/**
	 * Compiles the Javascript code needed for the dynamic operations on the view
	 *
	 * @return null|string  Javascript
	 */
	protected function _compileJsCode()
	{
		if ( count( $this->_jsif ) === 0 ) {
			return null;
		}

		$conditions					=	array();

		foreach ( $this->_jsif as $ifVal ) {
			$ifName					=	$ifVal['ifname'];
			/** @var  SimpleXMLElement  $element */
			$element				=	$ifVal['element'];
			$name					=	$this->control_id( $ifVal['control_name'], $element->attributes( 'name' ) );
			$operator				=	$element->attributes( 'operator' );
			$value					=	$element->attributes( 'value' );
			$valuetype				=	(string) $element->attributes( 'valuetype' );

			if ( $operator ) {
				$operators			=	array( '=', '==', '===', '<>', '!=', '!==', '<', '>', '<=', '>=', 'contains', '!contains', 'in', '!in', 'regexp', '!regexp' );

				if ( ! in_array( $operator, $operators, true ) ) {
					$operator		=	'no-operator-specified-in-xml';
				}
			} elseif ( isset( $ifVal['onchange'] ) && ( $ifVal['onchange'] === 'evaluate' ) ) {
				$operator			=	'evaluate';
			}

			if ( isset( $ifVal['show'] ) && ( count( $ifVal['show'] ) > 0 ) ) {
				$show				=	$ifVal['show'];
			} else {
				$show				=	array();
			}

			if ( isset( $ifVal['set'] ) && ( count( $ifVal['set'] ) > 0 ) ) {
				$set				=	$ifVal['set'];
			} else {
				$set				=	array();
			}

			if ( ! isset( $conditions[$ifName] ) ) {
				$conditions[$ifName]['element']			=	$name;
				$conditions[$ifName]['conditions']		=	array();
			}

			$conditions[$ifName]['conditions'][]		=	array(	'operator'	=>	$operator,
																	'value'		=>	$this->jsClean( $value, $valuetype ),
																	'show'		=>	$show,
																	'set'		=>	$set
																);
		}

		if ( ! $conditions ) {
			return null;
		}

		return "Object.assign( cbHideFields, " . json_encode( $conditions ) . " );";
	}

	/**
	 * Returns safe Javascript-typed values
	 *
	 * @param  mixed   $fieldValue  The value to javascript-format safely
	 * @param  string  $type        The type of the value that is wanted ('const:type' for constant of $fieldValue, 'param:type' for the actual data from the model)
	 * @return string|float|int     The safely formatted javascript value
	 */
	protected function jsClean( $fieldValue, $type )
	{
		$typeArray					=	explode( ':', $type, 3 );

		if ( count( $typeArray ) < 2 ) {
			$typeArray				=	array( 'const', $type );
		}

		if ( $typeArray[0] === 'param' ) {
			$fieldValue				=	$this->getModelOfData()->get( $fieldValue );
		}

		switch ( $typeArray[1] ) {
			case 'int':
			case 'integer':
				$fieldValue			=	(int) $fieldValue;
				break;
			case 'float':
			case 'number':
				$fieldValue			=	(float) $fieldValue;
				break;
			case 'bool':
			case 'boolean':
				$fieldValue			=	(bool) $fieldValue;
				break;
			case 'datetime':
				if ( ! preg_match( '/[0-9]{4}-[01][0-9]-[0-3][0-9] [0-2][0-9](:[0-5][0-9]){2}/', $fieldValue ) ) {
					$fieldValue		=	'';
				}
				break;
			case 'date':
				if ( ! preg_match( '/[0-9]{4}-[01][0-9]-[0-3][0-9]/', $fieldValue ) ) {
					$fieldValue		=	'';
				}
				break;
			case 'time':
				if ( ! preg_match( '/-?[0-9]{1,3}(:[0-5][0-9]){2}/', $fieldValue ) ) {
					$fieldValue		=	'';
				}
				break;
			case 'string':
				$fieldValue			=	(string) $fieldValue;
				break;
			case 'null':
				$fieldValue			=	'';
				break;
		}

		return $fieldValue;
	}

	/**
	 * Returns safe PHP-typed values with type-defined sources
	 * $type can be:
	 * 'const:type'       for constant of $fieldValue
	 * 'param:type'       for the actual data from the model
	 * 'pluginparam:type' for a parameter from the plugin
	 * 'cmsversion:type'  for the cmsversion attribute of type
	 * 'cbconfig:type'    for the config parameter of CB
	 * 'datavalue:type'   for the actual data from the model, but allowing a path
	 *
	 * @param  mixed             $fieldValue   The value to PHP-format safely
	 * @param  string            $type         The type of the value that is wanted (see above for types)
	 * @param  SimpleXMLElement  $element      The element for additional attributes
	 * @param  string            $leftRight  The prefix for additional attributes
	 * @return string|float|int                The safely formatted PHP value
	 */
	protected function phpCleanType( $fieldValue, $type, $element, $leftRight ) {
		$typeArray				=	explode( ':', $type, 3 );

		if ( count( $typeArray ) < 2 ) {
			$typeArray			=	array( 'const' , $type );
		}

		switch ( $typeArray[0] ) {
			case 'const':
				break;
			case 'param':
				$fieldValue		=	$this->getModelOfData()->get( $fieldValue );
				break;
			case 'pluginparams':
				$fieldValue		=	$this->_pluginParams->get( $fieldValue );
				break;
			case 'cmsversion':
				$fieldValue		=	checkJversion( ( $fieldValue ? $fieldValue : 'api' ) );
				break;
			case 'cbconfig':
				global $ueConfig;
				$fieldValue		=	( array_key_exists( $fieldValue, $ueConfig ) ? $ueConfig[$fieldValue] : '' );
				break;
			case 'datavalue':
				$fieldValue		=	$this->get( $fieldValue ); //TBD: missing default value, but not easy to find, as it's in the view param for now: $param->attributes( 'default' ) );
				break;
			case 'data':
				$leftRightElem	=	$element->getChildByNameAttributes( $leftRight );
				if ( $leftRightElem ) {
					$fieldValue	=	$this->renderAllParams( $leftRightElem, 'params', null, 'view', 'none' );
				} elseif ( $element->attributes( 'type' ) === 'showhide' ) {
					// Showhide nametype="data:TYPE" usage doesn't have a <left> node and this should just be treated as a param
					$fieldValue	=	$this->getModelOfData()->get( $fieldValue );
				} else {
					trigger_error( 'XMLifCondition::phpCleanQuote:name: missing ' . $leftRight . ' element for type ' . htmlspecialchars( $type ), E_USER_NOTICE );
				}
				break;
			case 'user':
				// TODO: Change this to use Inversion Of Control, and allow XML valuetypes to be extended dynamically (e.g. instead of calling specifically CBLib\CB\User or similar when available, it is CB that adds the type and a closure to handle that type.

				if ( $fieldValue == 'viewaccesslevels' ) {
					$fieldValue			=	Application::MyUser()->getAuthorisedViewLevels();
				} else {
					if ( $fieldValue == 'usergroups' ) {
						$fieldValue		=	Application::MyUser()->getAuthorisedGroups( false );
					} else {
						$fieldValue		=	\CBuser::getMyUserDataInstance()->get( $fieldValue );
					}
				}
				break;

			case 'request':
				$fieldValue		=	$this->input->get( $fieldValue, 0, GetterInterface::STRING );
				break;

			case 'get':
			case 'post':
			case 'cookie':
			case 'server':
			case 'env':
				$fieldValue		=	$this->input->get( $typeArray[0] . '/' . $fieldValue, 0, GetterInterface::STRING );
				break;

			case 'session':
				$fieldValue		=	Application::Session()->get( $fieldValue, null, GetterInterface::STRING );
				break;

			default:
				trigger_error( 'XMLifCondition::phpCleanQuote:name: ERROR_UNKNOWN_TYPE: ' . htmlspecialchars( $type ), E_USER_NOTICE );
				break;
		}

		if ( is_array( $fieldValue ) ) {
			$fieldValue			=	implode( '|*|', $fieldValue );
		}

		switch ( $typeArray[1] ) {
			case 'int':
			case 'integer':
				$value			=	(int) $fieldValue;
				break;
			case 'float':
			case 'number':
				$value			=	(float) $fieldValue;
				break;
			case 'formula':
				$value			=	$fieldValue;
				break;
			case 'bool':
			case 'boolean':
				$value			=	(bool) $fieldValue;
				break;
			case 'datetime':
				if ( preg_match( '/[0-9]{4}-[01][0-9]-[0-3][0-9] [0-2][0-9](:[0-5][0-9]){2}/', $fieldValue ) ) {
					$value		=	$fieldValue;
				} else {
					$value		=	'';
				}
				break;
			case 'date':
				if ( preg_match( '/[0-9]{4}-[01][0-9]-[0-3][0-9]/', $fieldValue ) ) {
					$value		=	$fieldValue;
				} else {
					$value		=	'';
				}
				break;
			case 'time':
				if ( preg_match( '/-?[0-9]{1,3}(:[0-5][0-9]){2}/', $fieldValue ) ) {
					$value		=	$fieldValue;
				} else {
					$value		=	'';
				}
				break;
			case 'string':
				$value			=	(string) $fieldValue;
				break;
			case 'null':
				$value			=	null;
				break;
			default:
				//CB2.0: uncomment: trigger_error( 'XMLifCondition::phpCleanQuote:value: ERROR_UNKNOWN_TYPE: ' . htmlspecialchars( $type ), E_USER_NOTICE );
				$value			=	$fieldValue;
				break;
		}

		return $value;
	}

	/**
	 * Evaluate an <if type="condition"> in PHP
	 *
	 * @param  SimpleXMLElement  $element  The '<if>' element
	 * @return boolean
	 */
	protected function _evalIf( $element ) {
		$name				=	$element->attributes( 'name' );
		$nametype			=	$element->attributes( 'nametype' );
		$operator			=	$element->attributes( 'operator' );
		$value				=	$element->attributes( 'value' );
		$valuetype			=	$element->attributes( 'valuetype' );

		if ( $nametype == '' ) {
			$nametype		=	'datavalue:string';
		}

		$paramValue			=	$this->phpCleanType( $name, $nametype, $element, 'left' );
		$value				=	$this->phpCleanType( $value, $valuetype, $element, 'right' );

		if ( ( $element->attributes( 'translate' ) == 'yes' ) || ( $element->attributes( 'translate' ) == '_UE' ) ) {
			$value			=	CBTxt::T( $value );
		}

		switch ( $operator ) {
			case '=':
			case '==':
				$result		=	( $paramValue == $value );
				break;
			case '!=':
			case '<>':
				$result		=	( $paramValue != $value );
				break;
			case '<':
				$result		=	( $paramValue < $value );
				break;
			case '>':
				$result		=	( $paramValue > $value );
				break;
			case '<=':
				$result		=	( $paramValue <= $value );
				break;
			case '>=':
				$result		=	( $paramValue >= $value );
				break;
			case 'contains':
				$result		=	( strpos( $value, $paramValue ) !== false );
				break;
			case '!contains':
				$result		=	( strpos( $value, $paramValue ) === false );
				break;
			case 'in':
				if ( is_array( $value ) ) {
					$values	=	$value;
				} else {
					$values	=	explode( '|*|', $value );
				}
				$result		=	( $values ? ( in_array( $paramValue, $values ) ) : false );
				break;
			case '!in':
				if ( is_array( $value ) ) {
					$values	=	$value;
				} else {
					$values	=	explode( '|*|', $value );
				}
				$result		=	( $values ? ( ! in_array( $paramValue, $values ) ) : true );
				break;
			case 'regexp':
				$result		=	( preg_match( '/' . $value . '/', $paramValue ) == 1 );
				break;
			case '!regexp':
				$result		=	( preg_match( '/' . $value . '/', $paramValue ) != 1 );
				break;
			case 'version_compare:=':
			case 'version_compare:!=':
			case 'version_compare:>':
			case 'version_compare:<':
			case 'version_compare:>=':
			case 'version_compare:<=':
				$result		=	version_compare( $paramValue, $value, substr( $operator, strpos( $operator, ':' ) + 1 ) );
				break;
			default:
				trigger_error( sprintf('XML IF: UNKNOWN OPERATOR "%" in xml: "%s"', $operator, htmlspecialchars( $element->asXML() ) ), E_USER_WARNING );
				$result		=	false;
				break;
		}

		return $result;
	}

	/**
	 * Returns the "html-dom-id" if it exists based on $element attribute 'name' and $control_name
	 *
	 * @param  string              $control_name  The control name
	 * @param  SimpleXMLElement  $element       The element to get the id for
	 * @return string|null
	 */
	public function _htmlId( $control_name, $element ) {
		$name				=	$element->attributes( 'name' );
		if ( $name ) {
			return str_replace( array( '[', ']' ), '__', 'cbfr_' . ( $control_name ? $control_name . '_' : '' ) . $name );
		} else {
			return null;
		}
	}

	/**
	 * Returns "id=(html-dom-id)" if it exists based on $element attribute 'name' and $control_name
	 *
	 * @param  string              $control_name  The control name
	 * @param  SimpleXMLElement  $element       The element to get the id= for
	 * @return null|string
	 */
	protected function _outputIdEqualHtmlId( $control_name, $element ) {
		$htmlid				=	$this->_htmlId( $control_name, $element );
		if ( $htmlid ) {
			$htmlid			=	' id="' . htmlspecialchars( $htmlid ) . '"';
		}
		return $htmlid;
	}

	/**
	 * Renders a line of parameter for $param
	 *
	 * @param  SimpleXMLElement  $param           The param to render
	 * @param  string[]            $result          The result to render: array( 0 => title, 1 => field value, 2 => description)
	 * @param  string              $control_name    The control name
	 * @param  string              $htmlFormatting  The HTML/array formatting to do ( 'table', 'td', 'div', 'span', 'none', 'fieldsListArray' )
	 * @param  bool                $htmlid          The HTML id attribute of the main container tag
	 * @param  boolean             $view            true if view only, false if editable
	 * @return array|string                         Values or HTML depending on $htmlFormatting
	 */
	protected function _renderLine( $param, $result, $control_name='params', $htmlFormatting = 'div', $htmlid = true, /** @noinspection PhpUnusedParameterInspection */ $view = false ) {
		$html				=	array();

		if ( $htmlid ) {
			$htid			=	$this->_outputIdEqualHtmlId( $control_name, $param );
		} else {
			$htid			=	null;
		}

		$type				=	htmlspecialchars( (string) $param->attributes( 'type' ) );
		$tag				=	null;
		$conditioned		=	in_array( $this->_htmlId( $control_name, $param ), $this->_jsconditioned, true );
		$hidden				=	( $param->attributes( 'display' ) == 'none' );
		$twoLine			=	( $param->attributes( 'display' ) == '2lines' );
		$htmlFormatting		=	( ( $type == 'hidden' ) && ( $htmlFormatting != 'fieldsListArray' ) ? 'none' : $htmlFormatting );
		$classes			=	'';

		if ( ! in_array( $htmlFormatting, array( 'none', 'fieldsListArray' ) ) ) {
			if ( $type == 'yesno' ) {
				$tag		=	'input';
			} elseif ( $type == 'group' ) {
				$tag		=	'group';
			} elseif ( preg_match( '/^(?:<(select|input|textarea|button)|(?:<[^<]+form-(group)[^>]+>))/i', trim( (string) $result[1] ), $matches ) ) {
				$tag		=	( isset( $matches[2] ) ? $matches[2] : ( isset( $matches[1] ) ? $matches[1] : null ) );
			}

			if ( ! in_array( $type, array( 'spacer', 'group' ) ) ) {
				$classes	=	RegistryEditView::buildClasses( $param, array(), ( $htmlFormatting == 'table' ? 'table-row' : ( $htmlFormatting == 'td' ? 'table-cell' : ( $htmlFormatting == 'span' ? 'inline' : 'block' ) ) ) );
			}
		}

		if ( $htmlFormatting == 'table' ) {
			$html[]			=	'<tr' . $htid . ' class="cbft_' . $type . ( $tag ? ' cbtt_' . $tag : null ) . ( $classes !== '' ? ' ' . htmlspecialchars( $classes ) : '' ) . ( $conditioned ? ' hidden cbDisplayDisabled' : null ) . '">';

			if ( trim( $result[0] ) === '' && $twoLine ) {
				$html[]		=		'<td' . ( $htid ? str_replace( 'cbfr_', 'cbfv_', $htid ) : null ) . ' class="fieldCell" colspan="' . ( $result[2] ? 2 : 3 ) . '" style="width: ' . ( $result[2] ? 95 : 100 ) . '%;">'
							.			$result[1]
							.		'</td>';
			} else {
				$html[]		=		'<td class="titleCell"' . ( $twoLine ? ' colspan="3"' : null ) . ' style="width: ' . ( $twoLine ? 100 : 25 ) . '%;">'
							.			( trim( $result[0] ) === '' ? '' : '<label for="' . $this->control_id( $control_name, $param->attributes( 'name' ) ) . '" class="col-form-label">' . $result[0] . '</label>' )
							.		'</td>';

				if ( $twoLine ) {
					$html[]	=	'</tr>'
							.	'<tr' . ( $htid ? str_replace( 'cbfr_', 'cbfrd_', $htid ) : null ) . ' class="cbft_' . $type . ( $tag ? ' cbtt_' . $tag : null ) . ( $classes !== '' ? ' ' . htmlspecialchars( $classes ) : '' ) . '">';
				}

				$html[]		=		'<td' . ( $htid ? str_replace( 'cbfr_', 'cbfv_', $htid ) : null ) . ' class="fieldCell"' . ( $twoLine ? ( ! $result[2] ? ' colspan="3"' : ' colspan="2"' ) : ( ! $result[2] ? ' colspan="2"' : null ) ) . ' style="width: ' . ( $twoLine ? ( $result[2] ? 95 : 100 ) : ( $result[2] ? 70 : 75 ) ) . '%;">'
							.			$result[1]
							.		'</td>';
			}

			if ( $result[2] ) {
				$html[]		=		'<td class="descrCell" style="width: 5%;">'
							.			$result[2]
							.		'</td>';
			}

			$html[]			=	'</tr>';
		} elseif ( $htmlFormatting == 'td' ) {
			$rowspan		=	$param->attributes( 'rowspan' );

			if ( ( ! $rowspan ) || ( ( $rowspan == 'all' ) && ( $this->_modelOfDataRowsNumber == 0 ) ) ) {
				$attr		=	( $classes !== '' ? ' class="' . htmlspecialchars( $classes ) . '"' : '' );

				if ( $param->attributes( 'align' ) ) {
					$attr	.=	' style="text-align:' . htmlspecialchars( $param->attributes( 'align' ) ) . ';"';
				} else {
					$attr	.=	( in_array( $param->attributes( 'type' ), array( 'checkmark', 'published' ) ) ? ' style="text-align:center;"' : null )
							.	( ( $rowspan == 'all' ) ? ' rowspan="' . (int) count( $this->_modelOfDataRows ) . '"' : null );
				}

				$attr		.=	( ( $param->attributes( 'nowrap' ) ) || in_array( $param->attributes( 'type' ), array( 'checkmark', 'ordering' ) ) ? ' nowrap="nowrap"' : null );

				$html[]		=	'<td' . $htid . $attr . '>'
							.		$result[1]
							.	'</td>';
			} else {
				$html[]		=	'';
			}
		} elseif ( $htmlFormatting == 'div' ) {
			// Determine if the input is using a full width display so we can add the icons to the label instead of creating a new line with just an icon
			$fullWidth		=	( ( strpos( $classes, 'w-100' ) !== false ) || ( strpos( $classes, 'input-block' ) !== false ) || ( strpos( $classes, 'btn-block' ) !== false ) || ( strpos( $classes, 'd-block' ) !== false ) );

			$html[]			=	'<div' . $htid . ' class="cbft_' . $type . ( $tag ? ' cbtt_' . $tag : null ) . ' form-group row no-gutters cb_form_line' . ( $twoLine ? ' cbtwolinesfield' : null ) . ( $classes !== '' ? ' ' . htmlspecialchars( $classes ) : '' ) . ( $conditioned ? ' hidden cbDisplayDisabled' : null ) . '">';

			if ( trim( (string) $result[0] ) !== '' ) {
				$html[]		=		'<label for="' . $this->control_id( $control_name, $param->attributes( 'name' ) ) . '" class="col-form-label col-sm-' . ( $twoLine ? 12 : '3 pr-sm-2' ) . '">'
							.			$result[0]
							.			( $fullWidth && $result[2] ? ' <span class="text-small cbFieldIcons">' . $result[2] . '</span>' : null )
							.		'</label>';

				$divSpan	=	'col-sm-' . ( $twoLine ? 12 : 9 );
			} else {
				$divSpan	=	( $twoLine ? 'col-sm-12' : 'col-sm-9 offset-sm-3' );
			}

			$html[]			=		'<div class="cb_field ' . $divSpan . '">'
							.			'<div' . ( $htid ? str_replace( 'cbfr_', 'cbfv_', $htid ) : null ) . ( ( ! $tag ) && ( ! $twoLine ) ? ' class="form-control-plaintext"' : null ) . '>'
							.				$result[1]
							.				( ( ( ! $fullWidth ) || ( trim( $result[0] ) === '' ) ) && $result[2] ? ' <span class="cbFieldIcons">' . $result[2] . '</span>' : null )
							.			'</div>'
							.		'</div>'
							.	'</div>';
		} elseif ( $htmlFormatting == 'span' ) {
			if ( substr( $result[0], -2 ) == "%s" ) {
				$result[0]	=	substr( $result[0], 0, -2 );

				$html[]		=	'<span' . $htid . ' class="cbft_' . $type . ( $tag ? ' cbtt_' . $tag : null ) . ( $classes !== '' ? ' ' . htmlspecialchars( $classes ) : '' ) . ( $conditioned ? ' hidden cbDisplayDisabled' : null ) . '">';

				if ( trim( $result[0] ) !== '' ) {
					$html[]	=		'<span class="cbLabelSpan">'
							.			'<label for="' . $this->control_id( $control_name, $param->attributes( 'name' ) ) . '" class="col-form-label">' . $result[0] . '</label>'
							.		'</span> ';
				}

				$html[]		=		'<span class="cbFieldSpan">'
							.			$result[1]
							.		'</span>'
							.		( $result[2] ? ' <span class="cbFieldIcons">' . $result[2] . '</span>' : null )
							.	'</span>';
			} else {
				$html[]		=	'<span' . $htid . ' class="cbft_' . $type . ( $tag ? ' cbtt_' . $tag : null ) . ( $classes !== '' ? ' ' . htmlspecialchars( $classes ) : '' ) . ( $conditioned ? ' hidden cbDisplayDisabled' : null ) . '">'
							.		'<span class="cbFieldSpan">'
							.			$result[1]
							.		'</span>';

				if ( trim( $result[0] ) !== '' ) {
					$html[]	=		' <span class="cbLabelSpan">'
							.			'<label for="' . $this->control_id( $control_name, $param->attributes( 'name' ) ) . '" class="col-form-label">' . $result[0] . '</label>'
							.		'</span>';
				}

				$html[]		=		( $result[2] ? ' <span class="cbFieldIcons">' . $result[2] . '</span>' : null )
							.	'</span>';
			}
		} elseif ( in_array( $htmlFormatting, array( 'none', 'fieldsListArray' ) ) ) {
			$html[]			=	$result[1];
		} else {
			$html[]			=	"*" . $result[1] . "*";
		}

		if ( $hidden ) {
			$html			=	array();
		}

		return ( $htmlFormatting == 'fieldsListArray' ? $this->arrayValuesMerge( $html ) : trim( implode( "\n", $html ) ) );
	}

	protected function arrayValuesMerge( $arr )
	{
		$merged							=	array();
		foreach ( $arr as $k => $v ) {
			if ( is_array( $v ) ) {
				if ( is_int( $k ) ) {
					foreach ( $v as $kk => $vv ) {
						$this->assignArrKeysValue( $merged, $kk, $vv );
					}
				} else {
					$this->assignArrKeysValue( $merged, $k, $v );
				}
			}
		}
		return $merged;
	}

	private function assignArrKeysValue( &$arr, $k, $v )
	{
		$k			=	str_replace( array( '[', ']' ), array( '.', '' ), $k);
		$keys		=	explode( '.', $k, 2 );

		if ( count( $keys ) == 1 ) {
			if ( isset( $arr[$k] ) && is_array( $v ) ) {
				$arr[$k]		=	array_merge_recursive( $arr[$k], $v );
			} else {
				$arr[$k]		=	$v;
			}
		} else {
			if ( ! isset( $arr[$keys[0]] ) ) {
				$arr[$keys[0]]	=	array();
			}
			$this->assignArrKeysValue( $arr[$keys[0]], $keys[1], $v );
		}
	}
	/**
	 * Gets the model of data of the object corresponding to a given field column
	 *
	 * @param  string             $paramsName  The name of the param
	 * @param  string             $cacheId     The alternative cache id otherwise cache by $paramsName
	 * @return RegistryInterface               The data of the corresponding column
	 */
	protected function & _parseParamsColumn( $paramsName, $cacheId = null ) {
		if ( $cacheId === null ) {
			$cacheId							=	$paramsName;
		}

		if ( ! isset( $this->_paramsOfColumns[$cacheId] ) ) {
			$this->_paramsOfColumns[$cacheId]	=	new Registry( $this->get( $paramsName) );
		}

		return $this->_paramsOfColumns[$cacheId];
	}

	/**
	 * Renders all parameters (including inheritance magic)
	 *
	 * @param  SimpleXMLElement  $xmlParentElement  The parent XML node for which to render all child node parameters
	 * @param  string            $control_name      The control name
	 * @param  cbTabs            $tabs              The CB tab (if applicable)
	 * @param  string            $viewType          The view type ( 'view', 'param', 'depends': means: <param> tag => param, <field> tag => view )
	 * @param  string            $htmlFormatting    The html formatting type ( 'table', 'td', 'div', 'span', 'none', 'fieldsListArray' )
	 * @return string|array                         HTML or values depending on $htmlFormatting
	 */
	public function renderAllParams( &$xmlParentElement, $control_name='params', $tabs=null, $viewType = 'depends', $htmlFormatting = 'div' ) {
		$html											=	array();
		$extenders										=	array();

		if ( ( $this->_inverted ) && ( count( $this->_extenders ) == 1 ) ) {
			$element									=	array_shift( $this->_extenders );
			array_unshift( $this->_extenders, array( &$xmlParentElement ) );
			$this->_inverted							=	false;
		} else {
			$element									=	$xmlParentElement;
		}

		if ( is_array( $element ) ) {
			foreach ( $element as $el ) {
				$html[]									=	$this->renderAllParams( $el, $control_name, $tabs, $viewType, $htmlFormatting );
			}
		} else {
			$identicalMatches							=	array();
			/** @var  SimpleXMLElement    $element */
			/** @var  SimpleXMLElement[]  $nonMatches */
			$nonMatches									=	array();
			if ( count( $this->_extenders ) > 0 ) {
				/** @var  SimpleXMLElement[]  $extenders */
				$extenders								=	array_shift( $this->_extenders );
				foreach ( $extenders as $ext ) {
					if ( ( $ext->getName() == 'inherit' ) || ( ( ( $ext->getName() == $element->getName() ) ) && $ext->attributes( 'name' ) == $element->attributes( 'name' ) ) ) {
						if ( count( $element->children() ) > 0 ) {
							foreach ( $ext->children() as $chld ) {
								$this->_addTagMatch( $identicalMatches, $chld );
							}
						} else {
							foreach ( $ext->children() as $chld ) {
								$saveExtTwo				=	$this->_extenders;
								$this->_extenders		=	array ();
								$html[]					=	$this->renderOneParamAndChildren( $chld, $control_name, $tabs, $viewType, $htmlFormatting );
								$this->_extenders		=	$saveExtTwo;
							}
						}
					} else {
						foreach ( $ext->children() as $chld ) {
							$nonMatches[]				=	$chld;
						}
					}
				}
			}

			foreach ( $element->children() as $param ) {
				$idkeyMatched							=	$this->_getKeyOfTagMatch( $identicalMatches, $param );
				if ( $idkeyMatched !== null ) {
					foreach ( $identicalMatches as $idkey => $idmatch ) {
						if ( $idkey == $idkeyMatched ) {
							break;
						} else {
							foreach ( $idmatch as $extparam ) {
								$saveExtTwo				=	$this->_extenders;
								$this->_extenders		=	array ( array( &$param ) );
								$html[]					=	$this->renderOneParamAndChildren( $extparam, $control_name, $tabs, $viewType, $htmlFormatting );
								$this->_extenders		=	$saveExtTwo;
							}
							unset( $identicalMatches[$idkey] );
						}
					}
					foreach ( $identicalMatches[$idkeyMatched] as $k => $extparam ) {
						$saveExtTwo						=	$this->_extenders;
						$this->_extenders				=	array ( array( &$param ) );
						$this->_inverted				=	true;
						$html[]							=	$this->renderOneParamAndChildren( $extparam, $control_name, $tabs, $viewType, $htmlFormatting );
						$this->_inverted				=	false;
						$this->_extenders				=	$saveExtTwo;
						unset( $identicalMatches[$idkeyMatched][$k] );
					}
				} else {
					$html[]								=	$this->renderOneParamAndChildren( $param, $control_name, $tabs, $viewType, $htmlFormatting );
				}
			}
			foreach ( $identicalMatches as $idmatch ) {
				foreach ( $idmatch as $extparam ) {
					$saveExtTwo							=	$this->_extenders;
					$this->_extenders					=	array ();
					$html[]								=	$this->renderOneParamAndChildren( $extparam, $control_name, $tabs, $viewType, $htmlFormatting );
					$this->_extenders					=	$saveExtTwo;
				}
			}
			//	foreach ( $nonMatches as $chld ) {
			//		if ( ( count( $chld->children() ) == 0 ) || in_array( $chld->getName(), array( 'param', 'field' ) ) ) {
			//			$html[]								=	$this->renderOneParamAndChildren( $chld, $control_name, $tabs, $viewType, $htmlFormatting );
			//			unset( $this->_extenders[$k] );
			//		}
			//	}

			//	$this->_extenders							=	$saveExt;

			if ( ( count( $element->children() ) < 1 ) && ( count( $extenders ) == 0 ) ) {
				if ( $htmlFormatting == 'table' ) {
					$html[] = "<tr><td colspan=\"2\"><i>" . CBTxt::Th( 'UE_NO_PARAMS', 'There are no parameters for this item' ) . /* ": " . $element->getName() . '(' . implode( ',', $element->attributes() ) . ')' . */ "</i></td></tr>";
				} elseif ( $htmlFormatting == 'td' ) {
					$html[] = "<td><i>" . CBTxt::Th( 'UE_NO_PARAMS', 'There are no parameters for this item' ) . "</i></td>";
				}
			}
		}
		return ( $htmlFormatting == 'fieldsListArray' ? $this->arrayValuesMerge( $html ) : trim( implode( "\n", $html ) ) );
	}

	/**
	 * Returns a unique text id of a xml element depending on name and attribute values
	 * @access private
	 *
	 * @param  SimpleXMLElement  $el  The element to hash uniquely depending on node name and attributes
	 * @return string                   The hash name
	 */
	protected function _uniqueTag( &$el ) {
		$add		=	'';
		foreach ( $el->attributes() as $k => $v ) {
			$add	.=	'|**|' . $k . '|==|' . $v;
		}
		return ( $el->getName()) . $add;
	}

	/*
		protected function _explodeTag( $uniqueTag ) {
			$tags		=	explode( '|**|', $uniqueTag );
			$name		=	$tags[0];
			$attr		=	array();
			for ( $i = 1, $n = count( $tags ); $i < $n; $i++ ) {
				$parts	=	explode( '|==|', $tags[$i] );
				$attr[$parts[0]]	=	$parts[1];
			}
		}
	*/

	/**
	 * Adds an XML extender tag to the list of tags for extension matching
	 *
	 * @param  array               $identicalMatches  The storage for all the nodes
	 * @param  SimpleXMLElement  $chld              The node to add to the storage
	 * @return void
	 */
	protected function _addTagMatch( &$identicalMatches, $chld ) {
		$identicalMatches[$this->_uniqueTag( $chld )][]	=	$chld;
	}

	/**
	 * Finds identical matches for tags of extenders memorized previously with _addTagMatch() method
	 *
	 * @param  array                   $identicalMatches  The storage for all the nodes
	 * @param  SimpleXMLElement      $param             The node to check for identical matches
	 * @return string|null
	 */
	protected function _getKeyOfTagMatch( &$identicalMatches, &$param ) {
		$paramTag	=	$this->_uniqueTag( $param );
		foreach ( array_keys( $identicalMatches ) as $k ) {
			if ( strpos( $k, $paramTag ) === 0 ) {
				return $k;
			}
		}
		return null;
	}

	/**
	 * Returns the file path from XML
	 *
	 * @param  string                     $file     The file being pathed to
	 * @param  null|SimpleXMLElement      $element  The base xml node providing the $file
	 * @param  null|PluginTable           $plugin   The currently loaded plugin object
	 * @param  string                     $type     The type of path to output (absolute, live, or relative)
	 * @return string
	 */
	public static function pathFromXML( $file, $element, $plugin, $type = 'absolute' ) {
		global $_CB_framework, $_PLUGINS;

		if ( ( $file[0] != '/' ) && $plugin ) {
			$path	=	'/'. $_PLUGINS->getPluginRelPath( $plugin );
		} elseif ( ( $file[0] != '/' ) && $element && isset( $element['xmlfilepath'] ) ) {
			$path	=	str_replace( $_CB_framework->getCfg( 'absolute_path' ), '', dirname( dirname( $element['xmlfilepath'] ) ) );
		} else {
			$path	=	'';
		}

		if ( $type == 'absolute' ) {
			$path	=	$_CB_framework->getCfg( 'absolute_path' ) . $path;
		} elseif ( $type == 'live' ) {
			$path	=	$_CB_framework->getCfg( 'live_site' ) . $path;
		}

		return $path . ( $file[0] != '/' ? '/' : null ) . $file;
	}

	/**
	 * Parses a file path for an array of files
	 *
	 * @param  string  $file     The file being pathed to
	 * @param  array   $files    The files found at the path
	 * @return array
	 */
	public static function pathsFromXML( $file, &$files = array() ) {
		if ( strpos( $file, '/*/' ) !== false ) {
			$fileParts			=	explode( '/*/', $file );
			$fromFiles			=	cbReadDirectory( $fileParts[0], '^[^.]+$', false, true );

			unset( $fileParts[0] );

			foreach ( $fromFiles as $fromFile ) {
				if ( count( $fileParts ) > 0 ) {
					$fromFile	=	$fromFile . '/' . implode( '/', $fileParts );
				}

				static::pathsFromXML( $fromFile, $files );
			}
		} elseif ( strpos( $file, '*.xml' ) !== false ) {
			$fromFiles			=	cbReadDirectory( str_replace( '*.xml', '', $file ), '.xml', false, true );

			foreach ( $fromFiles as $fromFile ) {
				$files[]		=	$fromFile;
			}
		} else {
			$files[]			=	$file;
		}

		return $files;
	}

	/**
	 * performs $element->xpath( $path ), but with auto-load function
	 *
	 * @param  SimpleXMLElement  $element
	 * @param  string              $path
	 * @return SimpleXMLElement[]|boolean     XML elements or FALSE
	 */
	public static function xpathWithAutoLoad( $element, $path ) {
		/** @var SimpleXMLElement[] $viewModel */
		$viewModel					=	$element->xpath( $path );
		if ( !$viewModel ) {
			// Try autoloading view:
			if ( preg_match( '#^/(?:\*|cbxml)/(types/type|views/view|actions/action)\[[^\]]*@name="([-_a-z]+)"[^\]]*\]$#', $path, $matches ) ) {
				$subpathNameClean	=	$matches[1];		// e.g. views/view
				$viewNameClean		=	$matches[2];		// viewname of view[@name="viewname" and .... ]
				$context			=	new Context();
				if ( $context->getPluginId() ) {
					$fileNameClean	=	$context->getPluginPath() . '/xml/' . $subpathNameClean . '.' . $viewNameClean . '.xml';
				} else {
					/*
					 * No auto-loading for core files for now:
					 * $fileNameClean	=	Application::CBFramework()->getCfg( 'absolute_path' ) . '/'
					 *					.	( Application::Cms()->getClientId() == 1 ? 'administrator/' : '' )
					 *					.	'components/com_comprofiler/xmlcb/'
					 *					.	$subpathNameClean . '.' . 'com_comprofiler.' . $viewNameClean . '.xml';
					 */
					return false;
				}
				if ( is_readable( $fileNameClean ) ) {
					$viewFileXML	=	new SimpleXMLElement( $fileNameClean, LIBXML_NONET | ( defined('LIBXML_COMPACT') ? LIBXML_COMPACT : 0 ), true );
					$slashViews		=	'/*/' . substr( $subpathNameClean, 0, strpos( $subpathNameClean, '/' ) );
					$elementSubRoot	=	$element->xpath( $slashViews );
					/** @var SimpleXMLElement $elementSubRoot */
					$elementSubRoot	=	$elementSubRoot[0];
					foreach ( $viewFileXML->xpath( $subpathNameClean ) as $autoLoadView ) {
						$elementSubRoot->addChildWithDescendants( $autoLoadView );
					}
					/** @var SimpleXMLElement[] $viewModel */
					$viewModel		=	$element->xpath( $path );
				} else {
					trigger_error( sprintf( 'RegistryEditView::xpathWithAutoLoad: For xpath %s : Unable to find auto-loading XML file: %s', $path, $fileNameClean ), E_USER_WARNING );
				}
			}
		}
		return $viewModel;
	}

	/**
	 * renders one parameter and its children
	 *
	 * @param  SimpleXMLElement  $param           The param to render
	 * @param  string              $control_name    The control name
	 * @param  cbTabs              $tabs            CB tab if applicable
	 * @param  string              $viewType        The view type ( 'view', 'param', 'depends': means: <param> tag => param, <field> tag => view )
	 * @param  string              $htmlFormatting  The HTML formatting ( 'table', 'td', 'span', 'none', 'fieldsListArray' )
	 * @return string|array                         HTML, otherwise if $htmlFormatting == 'fieldsListArray' an array of values
	 */
	protected function renderOneParamAndChildren( &$param, $control_name='params', $tabs=null, $viewType = 'depends', $htmlFormatting = 'div' ) {
		static $tabpaneCounter			=	0;				// level of tabs (for nested tabs)
		// static $tabpaneNames			=	array();		// names of the tabpanes of level [tabpaneCounter] for the tabpanetabs

		// Check if ACL authorizes to view and to use that element:
		if ( ( ! Access::authorised( $param ) ) && ( ! ( ( $param->getName() == 'if' ) && ( $param->attributes( 'type' ) == 'permission' ) ) ) ) {
			return null;
		}

		$html							=	array();

		// Parse the view type for extendParamAttributes
		$viewMode						=	$param->attributes( 'mode' );

		switch ( $viewMode ) {
			// case 'view':
			case 'show':
				$viewType				=	'view';
				break;
			// case 'param':
			case 'edit':
				$viewType				=	'param';
				break;
			default:
				break;
		}

		// treat any <attributes> below the tag to add attributes to the tag as needed:
		$this->extendParamAttributes( $param, $control_name, ( $viewType == 'view' ) );

		// We need to reparse view type encase <attributes changed it:
		$viewMode						=	$param->attributes( 'mode' );

		switch ( $viewMode ) {
			// case 'view':
			case 'show':
				$viewType				=	'view';
				break;
			// case 'param':
			case 'edit':
				$viewType				=	'param';
				break;
			default:
				break;
		}

		switch ( $param->getName() ) {
			case 'param':
				$result				=	$this->renderParam( $param, $control_name, ( $viewType == 'view' ), $htmlFormatting );

				if ( ( $viewType == 'view' ) && $param->attributes( 'valuedescription' ) && ( $htmlFormatting != 'fieldsListArray' ) ) {
					$result[1]		=	$this->getTooltipAttr( $param, null, $result[1] );
				}

				$dynamic			=	( ( ! ( $viewType == 'view' ) ) && ( $param->attributes( 'onchange' ) == 'evaluate' ) );
				if ( $dynamic && ( $viewType == 'param' ) && ( $htmlFormatting != 'fieldsListArray' ) ) {
					$result[1]		.=	'<noscript><button type="submit" name="cbdoevalpostagain" value="" class="button cbregOnChange">' . CBTxt::Th("Change") . '</button></noscript>';
				}
				if ( $result[1] || ( $viewType != 'view' ) || ( ! in_array( $param->attributes( 'hideblanktext' ), array( 'true', 'always' ) ) ) ) {
					$html[]			=	$this->_renderLine( $param, $result, $control_name, $htmlFormatting, true, ( $viewType == 'view' ) );
					if ( $dynamic ) {
						$ifName		=	$this->_htmlId( $control_name, $param );
						$this->_jsif[$ifName]['element']					=	$param;
						$this->_jsif[$ifName]['control_name']				=	$control_name;
						$this->_jsif[$ifName]['ifname']						=	$ifName;
						$this->_jsif[$ifName]['onchange']					=	$param->attributes( 'onchange' );
					}
				}
				break;

			case 'params':
				$paramsName					=	$param->attributes( 'name' );
				$paramsType					=	$param->attributes( 'type' );

				if ( ( ( $paramsType == 'params' ) && $paramsName ) || ( $paramsType == 'pluginparams' ) ) {
					$repeat					=	( $param->attributes( 'repeat' ) == 'true' );
					$repeatOrdering			=	( $param->attributes( 'repeatordering' ) != 'false' );
					$repeatCount			=	( $param->attributes( 'repeatcount' ) != 'false' );
					$repeatMax				=	(int) $param->attributes( 'repeatmax' );
					$repeatLabel			=	$param->attributes( 'repeatlabel' );
					$repeatDirection		=	$param->attributes( 'repeatdirection' );

					if ( $repeatLabel !== null ) {
						$repeatLabel		=	CBTxt::Th( $repeatLabel );
					} else {
						if ( $repeatCount ) {
							$repeatLabel	=	CBTxt::Th( 'Add Rows' );
						} else {
							$repeatLabel	=	CBTxt::Th( 'Add Row' );
						}
					}

					if ( $control_name ) {
						$parent_cname		=	$control_name . '[' . $paramsName . ']';
					} else {
						$parent_cname		=	$paramsName;
					}

					if ( $paramsType == 'params' ) {
						$valueObj			=	$this->_parseParamsColumn( $paramsName, $parent_cname );
					} else {
						$valueObj			=	$this->_pluginParams;
					}

					$this->pushModelOfData( $valueObj );

					if ( $repeat ) {
						$this->_jsrepeat	=	true;
						$this->_isrepeat	=	true;

						$valueArray			=	$valueObj->asArray();

						if ( ! $valueArray  ) {
							$valueArray		=	array( 0 => null );
						}

						$result				=	$this->renderParam( $param, $control_name, ( $viewType == 'view' ), $htmlFormatting );
						$return				=	null;

						foreach ( $valueArray as $index => $value ) {
							if ( $control_name ) {
								$child_cnam	=	$control_name . '[' . $paramsName . '][' . $index . ']';
							} else {
								$child_cnam	=	$paramsName . '[' . $index . ']';
							}

							// Grab child params from index:
							if ( $paramsType == 'params' ) {
								$valueObj	=	$this->_parseParamsColumn( $index, $child_cnam );
							} else {
								$valueObj	=	$this->_pluginParams;
							}

							$this->pushModelOfData( $valueObj );

							if ( in_array( $htmlFormatting, array( 'div', 'span', 'none' ) ) ) {
								$return		.=		'<div class="cbRepeatRow col-12 row no-gutters border rounded mb-2 p-2 mw-100">';

								if ( $repeatOrdering && ( $viewType != 'view' ) ) {
									$return	.=			'<div class="cbRepeatRowSort col-12 col-sm flex-grow-0 pb-2 pb-sm-0 pr-sm-2 align-self-sm-center">'
											.				'<div class="cbRepeatRowMove btn btn-secondary d-block d-sm-inline-block" title="' . htmlspecialchars( CBTxt::T( 'Click and drag to move this row.' ) ) . '"><span class="fa fa-sort"></span></div>'
											.			'</div>';
								}

								$return		.=			'<div class="cbRepeatRowParams col-12 col-sm' . ( $htmlFormatting == 'div' ? ' mb-n3' : null ) . '">'
											.				$this->renderAllParams( $param, $child_cnam, $tabs, $viewType, $htmlFormatting )
											.			'</div>';

								if ( $viewType != 'view' ) {
									$return	.=			'<div class="cbRepeatRowIncrement col-12 col-sm pt-2 flex-grow-0 pt-sm-0 pl-sm-2 align-self-sm-center">'
											.				'<div class="cbRepeatRowRemove btn btn-danger d-block d-sm-inline-block" title="' . htmlspecialchars( CBTxt::T( 'Click to remove this row.' ) ) . '"><span class="fa fa-minus"></span></div>'
											.			'</div>';
								}

								$return		.=		'</div>';
							} elseif ( in_array( $htmlFormatting, array( 'table', 'td' ) ) ) {
								$return		.=		'<table class="table table-bordered m-0 col-12 cbRepeatRow">'
											.			'<tbody>'
											.				'<tr>';

								if ( $repeatOrdering && ( $viewType != 'view' ) ) {
									$return	.=					'<td class="text-center cbRepeatRowSort" style="width: 1%; vertical-align: middle;">'
											.						'<div class="cbRepeatRowMove fa fa-sort btn btn-secondary" title="' . htmlspecialchars( CBTxt::T( 'Click and drag to move this row.' ) ) . '"></div>'
											.					'</td>';
								}

								$return		.=					'<td class="text-right cbRepeatRowParams">'
											.						'<table class="table table-sm table-borderless m-0">'
											.							( $htmlFormatting == 'td' ? '<tr>' : null )
											.							$this->renderAllParams( $param, $child_cnam, $tabs, $viewType, $htmlFormatting )
											.							( $htmlFormatting == 'td' ? '</tr>' : null )
											.						'</table>'
											.					'</td>';

								if ( $viewType != 'view' ) {
									$return	.=					'<td class="text-center cbRepeatRowIncrement" style="width: 1%; vertical-align: middle;">'
											.						'<div class="cbRepeatRowRemove fa fa-minus btn btn-danger" title="' . htmlspecialchars( CBTxt::T( 'Click to remove this row.' ) ) . '"></div>'
											.					'</td>';
								}

								$return		.=				'</tr>'
											.			'</tbody>'
											.		'</table>';
							} else {
								// Nothing; we need some formatting for the repeat usage
							}

							$this->popModelOfData();
						}

						if ( $return ) {
							if ( $viewType != 'view' ) {
								$return		=	'<div class="cbRepeat d-flex ' . ( $repeatDirection == 'down' ? 'flex-column-reverse' : 'flex-column' ) . ' mw-100" data-cbrepeat-fallback-for="' . htmlspecialchars( $this->control_id( null, $parent_cname ) ) . '" data-cbrepeat-fallback-name="' . htmlspecialchars( $parent_cname ) . '"' . ( ! $repeatOrdering ? ' data-cbrepeat-sortable="false"' : null ) . ( $repeatMax ? ' data-cbrepeat-max="' . (int) $repeatMax . '"' : null ) . '>'
											.		$return
											.		'<div class="cbRepeatRowIncrement form-inline flex-row-reverse' . ( $repeatDirection == 'down' ? ' mb-2' : null ) . '">';

								if ( $repeatCount ) {
									$return	.=			'<div class="input-group">'
											.				'<input type="text" class="form-control text-center cbRepeatRowAddCount" size="2" placeholder="1" />'
											.				'<span class="input-group-append">'
											.					'<div class="cbRepeatRowAdd btn btn-success" title="' . htmlspecialchars( CBTxt::T( 'Click to add new rows.' ) ) . '"><span class="fa fa-plus"></span> ' . $repeatLabel . '</div>'
											.				'</span>'
											.			'</div>';
								} else {
									$return	.=			'<div class="cbRepeatRowAdd btn btn-success" title="' . htmlspecialchars( CBTxt::T( 'Click to add new row.' ) ) . '"><span class="fa fa-plus"></span> ' . $repeatLabel . '</div>';
								}

								$return		.=		'</div>'
											.	'</div>';
							}

							$result[1]		=	$return;
						}

						$html[]				=	$this->_renderLine( $param, $result, $control_name, $htmlFormatting, true, ( $viewType == 'view' ) );

						$this->_isrepeat	=	false;
					} else {
						$html[]				=	$this->renderAllParams( $param, $parent_cname, $tabs, $viewType, $htmlFormatting );
					}

					$this->popModelOfData();
				}
				break;
			case 'field':
				$result				=	$this->renderParam( $param, $control_name, ( $viewType != 'param' ) );

				$task				=	$param->attributes( 'task' );
				$link				=	$param->attributes( 'link' );
				$target				=	$param->attributes( 'target' );
				$title				=	$param->attributes( 'title' );

				if ( $title ) {
					$title			= ' title="' . htmlspecialchars( CBTxt::T( $title ) ) . '"';
				} else {
					$title			= '';
				}

				$class				=	$param->attributes( 'cssclasslink' );

				if ( $class ) {
					$class			= ' class="' . htmlspecialchars( $class ) . '"';
				} else {
					$class			= '';
				}

				$rowAttributes		=	$title . $class;

				if ( ( $htmlFormatting != 'fieldsListArray' ) && ( $param->attributes( 'type' ) != 'hidden' ) ) {
					if ( ( $viewType == 'view' ) && $param->attributes( 'valuedescription' ) ) {
						$rowAttributes	=	$this->getTooltipAttr( $param, $rowAttributes );
					}

					if ( $link || $task ) {
						if ( $link ) {
							if ( $target == 'popup' ) {
								$linkhref = $this->_controllerView->drawUrl( $link, $param, $this->_modelOfData[0], $this->_modelOfData[0]->get( 'id' ), true, false );		//TODO NOT URGENT: hardcoded id column name 'id'
								$onclickJS	=	'window.open(\'' . htmlspecialchars( cbUnHtmlspecialchars( $linkhref ) )
									.	'\', \'cbtablebrowserpopup\', \'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\'); return false;';
								$rowAttributes	.=	' onclick="' . $onclickJS . '"';
							} else {
								$linkhref = $this->_controllerView->drawUrl( $link, $param, $this->_modelOfData[0], $this->_modelOfData[0]->get( 'id' ), true );		//TODO NOT URGENT: hardcoded id column name 'id'
								$rowAttributes	.=	( $target ? ' target="' . htmlspecialchars( $target ) . '"' : '' );
							}
						} elseif ( $task ) {
							$linkhref	=	'#';
							$onclickJS	=	"submitbutton( '" . addslashes( $task ) . "' );";
							$rowAttributes	.=	' onclick="' . $onclickJS . '"';
						} else {
							$linkhref	=	null;
						}

						if ( $linkhref ) {
							$result[1]	= '<a href="' . $linkhref .'"' . $rowAttributes . '>' . ( trim( $result[1] ) ? $result[1] : '---' ) . '</a>';
						} else {
							$result[1]	= '<span' . $rowAttributes . '>' . ( trim( $result[1] ) ? $result[1] : '---' ) . '</span>';
						}
					} elseif ( $rowAttributes ) {
						$result[1]		= '<span' . $rowAttributes . '>' . $result[1] . '</span>';
					}
				}
				$html[]	= $this->_renderLine( $param, $result, $control_name, $htmlFormatting, false, ( $viewType == 'view' ) );
				break;

			case 'fieldset':
				$fieldsetContent	=	$this->renderAllParams( $param, $control_name, $tabs, $viewType, $htmlFormatting );

				if ( $fieldsetContent === '' ) {
					break;
				}

				$htid				=	$this->_outputIdEqualHtmlId( $control_name, $param );

				$legend				=	$param->attributes( 'label' );
				$description		=	$param->attributes( 'description' );
				$name				=	$param->attributes( 'name' );
				$collapsed			=	( $legend ? $param->attributes( 'collapsed' ) : null );

				$class				=	array( 'd-block', 'w-100', 'border', 'p-0', 'mb-3', 'cbFieldset' );

				if ( $name ) {
					$class[]		=	'cbfieldset_' . htmlspecialchars( $name );
				}

				if ( $collapsed == 'true' ) {
					$class[]		=	'cbFieldsetCollapsed';
				}

				$class				=	RegistryEditView::buildClasses( $param, $class, 'block' );

				if ( in_array( $this->_htmlId( $control_name, $param ), $this->_jsconditioned, true ) ) {
					$class			.=	' hidden cbDisplayDisabled';
				}

				if ( $collapsed ) {
					static $FIELDSET_JS		=	0;

					if ( ! $FIELDSET_JS++ ) {
						global $_CB_framework;

						$js					=	"$( '.cbFieldsetExpand' ).on( 'click', function() {"
											.		"$( this ).closest( '.cbFieldset' ).removeClass( 'cbFieldsetCollapsed' );"
											.		"$( this ).siblings( '.cbFieldsetCollapse' ).removeClass( 'hidden' );"
											.		"$( this ).addClass( 'hidden' );"
											.	"});"
											.	"$( '.cbFieldsetCollapse' ).on( 'click', function() {"
											.		"$( this ).closest( '.cbFieldset' ).addClass( 'cbFieldsetCollapsed' );"
											.		"$( this ).siblings( '.cbFieldsetExpand' ).removeClass( 'hidden' );"
											.		"$( this ).addClass( 'hidden' );"
											.	"});";

						$_CB_framework->outputCbJQuery( $js );
					}
				}

				$fieldsethtml		=	'<fieldset class="' . htmlspecialchars( $class ) . '">';
				if ( $htmlFormatting == 'table' ) {
					$html[] 		=	'<tr' . $htid . '><td colspan="3" width="100%">' . $fieldsethtml;
				} elseif ( $htmlFormatting == 'td' ) {
					$html[]			=	"\t\t\t<td" . $htid . ">" . $fieldsethtml;
				} elseif ( $htmlFormatting == 'span' ) {
					$html[]			=	'<div' . $htid . '>' . $fieldsethtml;
				} elseif ( $htmlFormatting == 'fieldsListArray' ) {
					// nothing
				} else {
					$html[]			=	'<fieldset' . $htid . ' class="' . htmlspecialchars( $class ) . '">';
				}
				if ( $htmlFormatting != 'fieldsListArray' ) {
					if ( $legend ) {
						$html[]		=	'<legend class="border-0 w-auto m-0 ml-2 mr-2 pl-1 pr-1 pb-1 cbFieldsetLegend">'
									.		CBTxt::Th( $legend )
									.		( $collapsed ? ' <button type="button" class="btn btn-sm btn-secondary' . ( $collapsed != 'true' ? ' hidden' : null ) . ' cbFieldsetExpand"><span class="fa fa-chevron-down"></span></button><button type="button" class="btn btn-sm btn-secondary' . ( $collapsed == 'true' ? ' hidden' : null ) . ' cbFieldsetCollapse"><span class="fa fa-chevron-up"></span></button>' : null )
									.	'</legend>';
					}

					$html[]			=	'<div class="' . ( $legend ? 'ml-3 mr-3 mt-0 mb-3' : 'm-3' ) . ' cbFieldsetContent">';
				}
				if ( $htmlFormatting == 'table' ) {
					$html[]			=	'<table class="table table-sm table-borderless m-0">';
					if ( $description ) {
						$html[]		=	'<tr><td colspan="3" width="100%"><div class="font-weight-bold cbFieldsetDesc">' . CBTxt::Th( $description ) . '</div></td></tr>';
					}
				} elseif ( $htmlFormatting == 'td' ) {
					if ( $description ) {
						$html[] 	=	'<td colspan="3" width="100%"><div class="font-weight-bold cbFieldsetDesc">' . CBTxt::Th( $description ) . '</div></td>';
					}
				} elseif ( $htmlFormatting == 'span' ) {
					if ( $description ) {
						$html[]		=	'<span class="cbLabelSpan">' . CBTxt::Th( $description ) . '</span> ';
					}
					$html[]			=	'<span class="cbFieldSpan">';
				} elseif ( $htmlFormatting == 'fieldsListArray' ) {
					// nothing
				} else {
					if ( $description ) {
						$html[] 	=	'<div class="font-weight-bold mb-3 cbFieldsetDesc">' . CBTxt::Th( $description ) . '</div>';
					}
				}
				$html[]				=	$fieldsetContent;

				if ( $htmlFormatting != 'fieldsListArray' ) {
					$html[]			=	'</div>';
				}

				if ( $htmlFormatting == 'table' ) {
					$html[]			=	"\n\t</table>";
					$html[]			=	'</fieldset></td></tr>';
				} elseif ( $htmlFormatting == 'td' ) {
					$html[]			=	'</fieldset></td>';
				} elseif ( $htmlFormatting == 'span' ) {
					$html[]			=	'</span></fieldset></div>';
				} elseif ( $htmlFormatting == 'fieldsListArray' ) {
					// nothing
				} else {
					$html[]			=	'</fieldset>';
				}
				break;

			case 'fields':
			case 'status':
				$html[]				=	$this->renderAllParams( $param, $control_name, $tabs, $viewType, $htmlFormatting );
				break;

			case 'if':
				$ifType								=	$param->attributes( 'type' );
				$showInside							=	$this->checkParamCondition( $param );

				if ( ( $ifType === 'showhide' ) && ( ! ( $viewType === 'view' ) ) ) {
					$if_control_name				=	$control_name;
					$name							=	$param->attributes( 'name' );

					// Check if we're trying to condition against the parent data (e.g. root params inside of sub-params):
					if ( strpos( $param->attributes( 'name' ), 'parent/' ) === 0 ) {
						// We are trying to reach the parent params so lets remove the last child node from the control name:
						$if_control_name			=	preg_replace( '/\[[^][]+\]$/', '', $if_control_name );

						// Remove parent/ from the name since it has no relevance to the JS conditioning:
						$param->addAttribute( 'name', str_replace( 'parent/', '', $param->attributes( 'name' ) ) );
					}

					if ( strpos( $param->attributes( 'name' ), 'param:' ) === 0 ) {
						// Looks like we for sure want root params so lets force the control name to reset to it:
						$if_control_name			=	'params';

						// Remove param: from the name since it has no relevance to the JS conditioning:
						$param->addAttribute( 'name', str_replace( 'param:', '', $param->attributes( 'name' ) ) );
					} elseif ( strpos( $param->attributes( 'name' ), 'datavalue:' ) === 0 ) {
						// Looks like we want the object data so lets force the control name to null so we're grabbing from object inputs:
						$if_control_name			=	null;

						// Remove datavalue: from the name since it has no relevance to the JS conditioning:
						$param->addAttribute( 'name', str_replace( 'datavalue:', '', $param->attributes( 'name' ) ) );
					}

					$ifName							=	$this->_htmlId( $if_control_name, $param ) . $param->attributes( 'operator' ) . $param->attributes( 'value' ). $param->attributes( 'valuetype' );
					// $this->_jsif[$ifName]		=	array();
					// $this->_jsif[$ifName]['show']=	array();
					// $this->_jsif[$ifName]['set']	=	array();
					if ( count( $param->children() ) > 0 ) {
						foreach ( $param->children() as $subParam ) {
							/** @var  SimpleXMLElement  $subParam */
							$repeat											=	( ( $subParam->getName() == 'params' ) && ( $subParam->attributes( 'repeat' ) == 'true' ) );

							if ( in_array( $subParam->getName(), array( 'showview', 'params', 'fields', 'status', 'if' ) ) && ( ! $repeat ) ) {
								if ( $subParam->getName() == 'showview' ) {
									$viewName								=	$subParam->attributes( 'view' );
									$viewModel								=	$this->_views->getChildByNameAttributes( 'view', array( 'ui' => 'admin', 'name' => $viewName ) );

									if ( ! $viewModel ) {
										echo 'Extended renderAllParams:showview: View ' . $viewName . ' not defined in XML';
										return false;
									}

									$children								=	$viewModel->children();
								} else {
									$children								=	$subParam->children();
								}

								if ( count( $children ) > 0 ) {
									if ( $subParam->getName() == 'params' ) {
										$paramsName							=	$subParam->attributes( 'name' );
										if ( $control_name ) {
											$child_cnam						=	$control_name . '[' . $paramsName . ']';
										} else {
											$child_cnam						=	$paramsName;
										}
									} else {
										$child_cnam							=	$control_name;
									}

									foreach ( $children as $vChild ) {
										/** @var  SimpleXMLElement  $vChild */
										if ( ! in_array( $vChild->getName(), array( 'showview', 'if', 'else' ) ) ) {													//TBD	//FIXME: this avoids JS error but still shows sub-view ! recursive function needed here
											$childHtmlId						=	$this->_htmlId( $child_cnam, $vChild );

											if ( $childHtmlId && ( ! $showInside ) ) {
												$this->_jsconditioned[]			=	$childHtmlId;
											}

											$this->_jsif[$ifName]['show'][]		=	$childHtmlId;
										} elseif ( $vChild->getName() == 'if' ) {
											foreach ( $vChild->children() as $vvChild ) {
												/** @var  SimpleXMLElement  $vvChild */
												if ( ! in_array( $vvChild->getName(), array( 'showview', 'if', 'else', 'params', 'fields', 'status' ) ) ) {													//TBD	//FIXME: this avoids JS error but still shows sub-view ! recursive function needed here
													$childHtmlId						=	$this->_htmlId( $child_cnam, $vvChild );

													if ( $childHtmlId && ( ! $showInside ) ) {
														$this->_jsconditioned[]			=	$childHtmlId;
													}

													$this->_jsif[$ifName]['show'][]		=	$childHtmlId;
												} elseif ( $vvChild->getName() == 'if' ) {
													foreach ( $vvChild->children() as $vvvChild ) {
														/** @var  SimpleXMLElement  $vvvChild */
														if ( ! in_array( $vvvChild->getName(), array( 'showview', 'if', 'else', 'params', 'fields', 'status' ) ) ) {													//TBD	//FIXME: this avoids JS error but still shows sub-view ! recursive function needed here
															$childHtmlId						=	$this->_htmlId( $child_cnam, $vvvChild );

															if ( $childHtmlId && ( ! $showInside ) ) {
																$this->_jsconditioned[]			=	$childHtmlId;
															}

															$this->_jsif[$ifName]['show'][]		=	$childHtmlId;
														}
													}
												}
											}
										}
									}
								}
							} elseif ( $subParam->getName() == 'else' ) {
								if ( $subParam->attributes( 'action' ) == 'set' ) {
									$correspondingParam						=	$param->getAnyChildByNameAttr( 'param', 'name', $subParam->attributes( 'name' ) );
									if ( $correspondingParam ) {
										$ifSubId							=	$this->control_id( $control_name, $subParam->attributes( 'name' ) );
										$ifSubValue							=	$subParam->attributes( 'value' );

										if ( $correspondingParam->attributes( 'type' ) == 'yesno' ) {
											if ( $ifSubValue == 1 ) {
												$ifSubId					=	$ifSubId . '__yes';
											} elseif ( $ifSubValue == 0 ) {
												$ifSubId					=	$ifSubId . '__no';
											}
										}

										$this->_jsif[$ifName]['set'][]		=	array( 'element' => $ifSubId, 'value' => $ifSubValue );
									} else {
										echo 'No corresponding param to the else statement for name ' . $subParam->attributes( 'name' ) . ' !';
									}
								}
							} else {
								$subHtmlId									=	$this->_htmlId( $control_name, $subParam );

								if ( $subHtmlId && ( ! $showInside ) ) {
									$this->_jsconditioned[]					=	$subHtmlId;
								}

								$this->_jsif[$ifName]['show'][]				=	$subHtmlId;
							}
						}
						$this->_jsif[$ifName]['element']					=	$param;
						$this->_jsif[$ifName]['control_name']				=	$control_name;
						$this->_jsif[$ifName]['ifname']						=	$this->_htmlId( $if_control_name, $param );
					}

					$html[] = $this->renderAllParams( $param, $control_name, $tabs, $viewType, $htmlFormatting );

					if ( $name !== $param->attributes( 'name' ) ) {
						// Name was changed so lets restore it:
						$param->addAttribute( 'name', $name );
					}
				} elseif ( ( $ifType == 'condition' ) || ( $ifType == 'permission' ) || ( $viewType == 'view' ) ) {
					if ( $showInside ) {
						$then						=	$param->getChildByNameAttributes( 'then' );
						if ( $then ) {
							$insideParamToRender	=	$then;
						} else {
							$insideParamToRender	=	$param;
						}
					} else {
						$insideParamToRender		=	$param->getChildByNameAttributes( 'else' );

						if ( $insideParamToRender && $insideParamToRender->attributes( 'action' ) == 'set' ) {
							$correspondingParam		=	$param->getAnyChildByNameAttr( 'param', 'name', $insideParamToRender->attributes( 'name' ) );
							if ( $correspondingParam ) {
								$this->_modelOfData[0]->set( $insideParamToRender->attributes( 'name' ), $insideParamToRender->attributes( 'value' ));

								$insideParamToRender = null;
							} else {
								echo 'No corresponding param to the else statement for name ' . $insideParamToRender->attributes( 'name' ) . ' !';
							}
						}
					}
					if ( $insideParamToRender ) {
						$htmlElse					=	$this->renderAllParams( $insideParamToRender, $control_name, $tabs, $viewType, $htmlFormatting );
						if ( $htmlElse != '' ) {
							$html[]				=	$htmlElse;
						}
					}
				}
				break;
			case 'else':
				break;		// implemented in if above it

			case 'toolbarmenu':
				$newToolBarMenu					=	new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8"?><cbxml></cbxml>');
				/** @var $toolbarMenu SimpleXMLElement */
				/** @var $menu SimpleXMLElement */
				foreach ( $param->children() as $menu ) {
					$this->extendParamAttributes( $menu );

					$menuLink					=	$menu->attributes( 'link' );
					$menuAccess					=	true;
					$link						=	null;

					if ( $menuLink ) {
						$link					=	$this->_controllerView->drawUrl( $menuLink, $menu, $this->_modelOfData[0], null );

						if ( ! $link ) {
							$menuAccess			=	false;
						}
					}

					if ( $menuAccess ) {
						/** @var $menu SimpleXMLElement */
						$child					=	$newToolBarMenu->addChildWithAttr( 'menu', null, null, $menu->attributes() );

						if ( $link ) {
							$child->addAttribute( 'urllink', $link );
						}
					}
				}
				global $_CB_Backend_Menu;
				$_CB_Backend_Menu->menuItems[]	=	$newToolBarMenu;
				break;

			case 'grid':
				if ( $htmlFormatting != 'fieldsListArray' ) {
					$htid					=	$this->_outputIdEqualHtmlId( $control_name, $param );

					if ( $htmlFormatting == 'table' ) {
						$html[]				=	'<tr' . $htid . '><td colspan="3" style="width: 100%;">';
					} elseif ( $htmlFormatting == 'td' ) {
						$html[]				=	'<td' . $htid . '>';
					}

					$html[]					=	'<div class="' . htmlspecialchars( RegistryEditView::buildClasses( $param, array( ( $param->attributes( 'fluid' ) == 'false' ? 'container' : 'container-fluid' ) ), 'block' ) ) . '">';

					if ( $htmlFormatting == 'table' ) {
						$html[]				=	'<table class="table table-sm table-borderless m-0">';
					}
				}

				$html[]						=	$this->renderAllParams( $param, $control_name, $tabs, $viewType, $htmlFormatting );

				if ( $htmlFormatting != 'fieldsListArray' ) {
					if ( $htmlFormatting == 'table' ) {
						$html[]				=	'</table>';
					}

					$html[]					=	'</div>';

					if ( $htmlFormatting == 'table' ) {
						$html[]				=	'</td></tr>';
					} elseif ( $htmlFormatting == 'td' ) {
						$html[]				=	'</td>';
					}
				}
				break;

			case 'gridrow':
				if ( $htmlFormatting != 'fieldsListArray' ) {
					$htid					=	$this->_outputIdEqualHtmlId( $control_name, $param );

					if ( $htmlFormatting == 'table' ) {
						$html[]				=	'<tr' . $htid . '><td colspan="3" style="width: 100%;">';
					} elseif ( $htmlFormatting == 'td' ) {
						$html[]				=	'<td' . $htid . '>';
					}

					$rowClasses				=	array( 'row' );

					if ( $param->attributes( 'gutters' ) == 'false' ) {
						$rowClasses[]		=	'no-gutters';
					}

					$html[]					=	'<div class="' . htmlspecialchars( RegistryEditView::buildClasses( $param, $rowClasses, 'block' ) ) . '">';
				}

				$html[]						=	$this->renderAllParams( $param, $control_name, $tabs, $viewType, $htmlFormatting );

				if ( $htmlFormatting != 'fieldsListArray' ) {
					$html[]					=	'</div>';

					if ( $htmlFormatting == 'table' ) {
						$html[]				=	'</td></tr>';
					} elseif ( $htmlFormatting == 'td' ) {
						$html[]				=	'</td>';
					}
				}
				break;

			case 'gridcol':
				if ( $htmlFormatting != 'fieldsListArray' ) {
					$classes				=	array();
					$size					=	explode( ',', (string) $param->attributes( 'size' ) );
					$colSizes				=	array( 'xs', 'sm', 'md', 'lg', 'xl' );

					if ( count( $size ) > 1 ) for ( $i = 0; $i < 4; $i++ ) {
						if ( isset( $size[$i] ) && $size[$i] ) {
							if ( $colSizes[$i] == 'xs' ) {
								$classes[]	=	'col' . ( $size[$i] != 'auto' ? '-' . (int) $size[$i] : ( $size[$i] != 'fill' ? '-auto' : null ) );
							} else {
								$classes[]	=	'col-' . $colSizes[$i] . ( $size[$i] != 'auto' ? '-' . (int) $size[$i] : ( $size[$i] != 'equal' ? '-auto' : null ) );
							}
						}
					} elseif ( isset( $size[0] ) && $size[0] ) {
						$classes[]			=	'col-sm' . ( $size[0] != 'auto' ? '-' . (int) $size[0] : ( $size[0] != 'fill' ? '-auto' : null ) );
					} else {
						$classes[]			=	'col';
					}

					$offset					=	explode( ',', (string) $param->attributes( 'offset' ) );

					if ( count( $offset ) > 1 ) for ( $i = 0; $i < 4; $i++ ) {
						if ( isset( $offset[$i] ) && $offset[$i] ) {
							if ( $colSizes[$i] == 'xs' ) {
								$classes[]	=	'offset-' . (int) $size[$i];
							} else {
								$classes[]	=	'offset-' . $colSizes[$i] . '-' . (int) $size[$i];
							}
						}
					} elseif ( isset( $offset[0] ) && $offset[0] ) {
						$classes[]			=	'offset-sm-' . (int) $offset[0];
					}

					$order					=	explode( ',', (string) $param->attributes( 'order' ) );

					if ( ! $order ) {
						$order				=	explode( ',', (string) $param->attributes( 'push' ) );
					}

					if ( ! $order ) {
						$order				=	explode( ',', (string) $param->attributes( 'pull' ) );
					}

					if ( count( $order ) > 1 ) for ( $i = 0; $i < 4; $i++ ) {
						if ( isset( $order[$i] ) && $order[$i] ) {
							if ( $colSizes[$i] == 'xs' ) {
								$classes[]	=	'order-' . ( ! in_array( $size[$i], array( 'first', 'last' ) ) ? (int) $size[$i] : $size[$i] );
							} else {
								$classes[]	=	'order-' . ( ! in_array( $size[$i], array( 'first', 'last' ) ) ? $colSizes[$i] . '-' . (int) $size[$i] : $size[$i] );
							}
						}
					} elseif ( isset( $order[0] ) && $order[0] ) {
						$classes[]			=	'order-' . ( ! in_array( $order[0], array( 'first', 'last' ) ) ? 'sm-' . (int) $order[0] : $order[0] );
					}

					$html[]					=	'<div class="' . htmlspecialchars( RegistryEditView::buildClasses( $param, $classes, 'block' ) ) . '">';

					if ( $htmlFormatting == 'table' ) {
						$html[]				=	'<table class="table table-sm table-borderless m-0">';
					}
				}

				$html[]						=	$this->renderAllParams( $param, $control_name, $tabs, $viewType, $htmlFormatting );

				if ( $htmlFormatting != 'fieldsListArray' ) {
					if ( $htmlFormatting == 'table' ) {
						$html[]				=	'</table>';
					}

					$html[]					=	'</div>';
				}
				break;

			case 'tabpane':
				// first render all tabpanetabs (including nested tabpanes):
				$tabpaneCounter++;

				$this->tabpaneNames[$tabpaneCounter]	=	$param->attributes( 'name' );
				$subhtml								=	$this->renderAllParams( $param, $control_name, $tabs, $viewType, $htmlFormatting );

				unset( $this->tabpaneNames[$tabpaneCounter] );

				$tabpaneCounter--;

				if ( $subhtml === '' ) {
					break;
				}

				if ( $htmlFormatting != 'fieldsListArray' ) {
					// then puts them together:
					$htid					=	$this->_outputIdEqualHtmlId( $control_name, $param );

					if ( $htmlFormatting == 'table' ) {
						$html[]				=	'<tr' . $htid . '><td colspan="3" style="width: 100%;">';
					} elseif ( $htmlFormatting == 'td' ) {
						$html[]				=	'<td' . $htid . '>';
					}

					$html[]					=	$tabs->startPane( $param->attributes( 'name' ) );
				}

				$html[]						=	$subhtml;

				if ( $htmlFormatting != 'fieldsListArray' ) {
					$html[]					=	$tabs->endPane();

					if ( $htmlFormatting == 'table' ) {
						$html[]				=	'</td></tr>';
					} elseif ( $htmlFormatting == 'td' ) {
						$html[]				=	'</td>';
					}
				}
				break;

			case 'tabpanetab':
				$tabContents				=	$this->renderAllParams( $param, $control_name, $tabs, $viewType, $htmlFormatting );

				if ( $tabContents === '' ) {
					break;
				}

				if ( $htmlFormatting != 'fieldsListArray' ) {
					$this->_i++;

					$idtab					=	$this->tabpaneNames[$tabpaneCounter] . $this->_i;
					$html[]					=	$tabs->startTab( $this->tabpaneNames[$tabpaneCounter], CBTxt::Th( $param->attributes( 'label' ) ), $idtab );

					$tabTitle				=	$param->attributes( 'title' );
					$tabDescription			=	$param->attributes( 'description' );

					$tabResult				=	array(	( $tabTitle && $tabDescription ? CBTxt::Th( $tabTitle ) : '' ),
														'<strong>' . CBTxt::Th( ( $tabDescription ? $tabDescription : $tabTitle ) ) . '</strong>',
														null
													);

					if ( $htmlFormatting == 'table' ) {
						$html[]				=	'<table class="table table-sm table-borderless m-0">';

						if ( $tabTitle || $tabDescription ) {
							$html[]			=	$this->_renderLine( $param, $tabResult, $control_name, $htmlFormatting, true, ( $viewType == 'view' ) );
						}
					} elseif ( $htmlFormatting == 'div' ) {
						if ( $tabTitle || $tabDescription ) {
							$html[]			=	$this->_renderLine( $param, $tabResult, $control_name, $htmlFormatting, true, ( $viewType == 'view' ) );
						}
					}
				}

				$html[]						=	$tabContents;

				if ( $htmlFormatting != 'fieldsListArray' ) {
					if ( $htmlFormatting == 'table' ) {
						$html[]				=	'</table>';
					}

					$html[]					=	$tabs->endTab();
				}
				break;

			case 'inherit':
				$from				=	$param->attributes( 'from' );
				if ( $from ) {
					/** @var  SimpleXMLElement[] $fromXml */
					$fromXml		=	$param->xpath( $from );
					if ( $fromXml && ( count( $fromXml ) > 0 ) ) {
						array_unshift( $this->_extenders, array( &$param ) );
						foreach ( $fromXml as $fmx ) {
							$html[]	=	$this->renderAllParams( $fmx, $control_name, $tabs, $viewType, $htmlFormatting );
						}
					}
				}
				break;

			case 'extend':
				$errorText			=	self::extendXMLnode( $param, $param, null, $this->_pluginObject, $this );
				if ( $errorText ) {
					$html[]			=	$errorText;
				}
				break;
			/*
			 * This is in future going to be the new implementation for inherit in CB as it auto-loads: could also be named "extends".
			case 'extends':
				$from				=	$param->attributes( 'from' );
				if ( $from ) {
					$fromXml		=	cbpaidViewExtended::xpathWithAutoLoad( $param, $from );			// same as $fromXml = $param->xpath( $from );
					if ( $fromXml && ( count( $fromXml ) > 0 ) ) {
						$param->{0}	=	'inherit';
						array_unshift( $this->_parent->_extenders, array( &$param ) );
						foreach ( $fromXml as $fmx ) {
							$html[]	=	$this->_parent->renderAllParams( $fmx, $control_name, $tabs, $viewType, $htmlFormatting );
						}
					}
				}
				break;
			*/
			case 'trigger':
				$isSaving			=	( $htmlFormatting == 'fieldsListArray' );
				$errorText			=	$this->triggerXML( $param, $this->_pluginObject, $isSaving );
				if ( $errorText ) {
					$html[]			=	$errorText;
				}
				break;
			case 'css':
			case 'js':
			case 'jquery':
				$fromFile					=	$param->attributes( 'file' );

				if ( $fromFile ) {
					global $_CB_framework;

					$this->substituteName( $fromFile, true );

					$fromFile				=	static::pathFromXML( $fromFile, $param, $this->_pluginObject, 'live' );

					switch ( $param->getName() ) {
						case 'css':
							$_CB_framework->document->addHeadStyleSheet( $fromFile );
							break;
						case 'js':
							$_CB_framework->document->addHeadScriptUrl( $fromFile );
							break;
						case 'jquery':
							$_CB_framework->addJQueryPlugin( $param->attributes( 'name' ), $fromFile );
							break;
					}
				}
				break;
			case 'menugroup':
				if ( $htmlFormatting != 'fieldsListArray' ) {
					$id				=	$this->_htmlId( $control_name, $param );

					$tb		= new Menu( $this->input, $param, $this->_options, $this->_pluginParams, $this->_types, $this->_actions, $this->_views, $this->_pluginObject, $this->_tabid, $this->_db, $this );
					if ( $htmlFormatting == 'table' ) {
						$html[] = '<tr id="' . $id . '"><td colspan="3" style="width: 100%;">';
					} elseif ( $htmlFormatting == 'td' ) {
						$html[] = '<td id="' . $id . '">';
					}
					$html[]	= $tb->draw();
					if ( $htmlFormatting == 'table' ) {
						$html[] = '</td></tr>';
					} elseif ( $htmlFormatting == 'td' ) {
						$html[] = '</td>';
					}
					unset($tb);
				}
				break;
			case 'tablebrowser':
				if ( $htmlFormatting != 'fieldsListArray' ) {
					$id				=	$this->_htmlId( $control_name, $param );

					$tb		= new TableBrowser( $this->input, $param, $this->_options, $this->_pluginParams, $this->_types, $this->_actions, $this->_views, $this->_pluginObject, $this->_tabid, $this->_db, $this );
					if ( $htmlFormatting == 'table' ) {
						$html[] = '<tr id="' . $id . '"><td colspan="3" style="width: 100%;">';
					} elseif ( $htmlFormatting == 'td' ) {
						$html[] = '<td id="' . $id . '">';
					}
					$html[]	= $tb->draw( $viewType );
					if ( $htmlFormatting == 'table' ) {
						$html[] = '</td></tr>';
					} elseif ( $htmlFormatting == 'td' ) {
						$html[] = '</td>';
					}
					unset($tb);
				}
				break;
			case 'showview':
				$name						=	$param->attributes( 'name' );
				$viewName					=	$param->attributes( 'view' );
				$showviewType				=	$param->attributes( 'type' );
				$viewMode					=	$param->attributes( 'mode' );
				$htmlFormattingView			=	$param->attributes( 'formatting' );
				$tabbed						=	( $htmlFormattingView == 'tab' );

				if ( ( $htmlFormattingView == '' ) || $tabbed ) {
					$htmlFormattingView		=	$htmlFormatting;
				}

				switch ( $viewMode ) {
					// case 'view':
					case 'show':
						$viewType			=	'view';
						break;
					// case 'param':
					case 'edit':
						$viewType			=	'param';
						break;
					default:
						break;
				}

				// MODEL: load data to view:
				/** @var $dataModel SimpleXMLElement|null */
				$dataModel					=	$param->getElementByPath( 'data' );
				if ( $dataModel ) {
					if ( $name ) {
						if ( $control_name ) {
							$parent_cname	=	$control_name . '[' . $name . ']';
						} else {
							$parent_cname	=	$name;
						}
					} else {
						$parent_cname		=	$control_name;
					}
					$dataModelType			=  $dataModel->attributes( 'type' );
					switch ( $dataModelType ) {
						case 'sql:string':			// <data name="params" type="sql:string" default="" />
							$name			=	$param->attributes( 'name' );
							$value			=	$this->get( $name, $param->attributes( 'default' ) );
							$sqlParams		=	new Registry( $value );
							$sqlParams->setNamespaceRegistry( 'parent', $this->getModelOfData() );
							$data			=	$sqlParams;
							break;
						case 'sql:field':			// <data name="params" type="sql:field" table="#__cbsubs_config" class="cbpaidConfig" key="id" value="1" valuetype="sql:int" />
							global $_CB_database;
							$xmlsql			=	new XmlQuery( $_CB_database, null, $this->_pluginParams );
							$xmlsql->process_data( $dataModel );
							$sqlRow			=	$xmlsql->queryloadResult();			// get the resulting field
							if ( $sqlRow !== null ) {
								$sqlParams	=	new Registry( $sqlRow );
								$sqlParams->setNamespaceRegistry( 'parent', $this->getModelOfData() );
								$data		=	$sqlParams;
							} else {
								// echo 'ERROR'; // error in query...
								$data		=	null;
							}
							break;
						case 'parameters':			// <data name="pluginparams" type="parameters" />
							$data			=		$this->_pluginParams;
							break;
						default:
							$data			=	null;
							trigger_error( 'Extended renderAllParams:showview: Data model type ' . $dataModelType . ' is not implemented !', E_USER_NOTICE );
							break;
					}
				} else {
					$parent_cname			=	$control_name;
					if ( $name && ( $showviewType == 'private' ) ) {
						$value				=	$this->get( $name, $param->attributes( 'default' ) );
						$sqlParams			=	new Registry( $value );
						$sqlParams->setNamespaceRegistry( 'parent', $this->getModelOfData() );
						$data				=	$sqlParams;
					} else {
						// No data child found and not private type then use the current data model for params:
						$data				=	null;
					}
				}
				// VIEW: select view:
				if ( $viewName && ( ( ! $showviewType ) || ( $showviewType == 'view' ) ) ) {
					$this->substituteName( $viewName, false );
					//	$allViewsModels		=	$param->xpath( '/cbxml/views' );		// $param->xpath( '/cbxml/views[@test="haha"]' );		// $param->xpath( '/cbxml/views[1][@test="haha"]' );		// $param->xpath( '/cbxml/views[1]' );		// $param->xpath( '//views' );		//
					//	$viewModel			=	$allViewsModels[0]->getChildByNameAttributes( 'view', array( 'ui' => 'admin', 'name' => $viewName ) );
					//		$allViewsModels		=	$param->xpath( '//views[test="haha"]/view[@name="' . $viewName . '"]' );		// $param->xpath( '../../../../../../../../views[test="haha"]/view[@name="' . $viewName . '"]' );		// $param->xpath( '/*/views[test="haha"]/*[@name="' . $viewName . '"]' );		// $param->xpath( '/cbxml/views[test="haha"]/view[@name="' . $viewName . '"]' );		//
					//		$viewModel			=	$allViewsModels[0];
					$viewModel			=	$this->_views->getChildByNameAttributes( 'view', array( 'ui' => 'admin', 'name' => $viewName ) );
					if ( !$viewModel ) {
						if ( $param->attributes( 'mandatory' ) == 'false' ) {
							return null;
						}
						trigger_error( 'Extended renderAllParams:showview: View ' . $viewName . ' not defined in XML. ', E_USER_NOTICE );
						return false;
					}

					if ( ! Access::authorised( $viewModel ) ) {
						return null;
					}

					if ( $data ) {
						$this->pushModelOfData( $data );
					}

					$viewContents			=	$this->renderAllParams( $viewModel, $parent_cname, $tabs, $viewType, $htmlFormattingView );

					if ( ( $viewContents !== '' ) || ( ! $tabbed ) ) {
						if ( $tabbed ) {
							$this->_i++;

							$idtab			=	$this->tabpaneNames[$tabpaneCounter] . $this->_i;

							$html[]			=	$tabs->startTab( $this->tabpaneNames[$tabpaneCounter], CBTxt::Th( $viewModel->attributes( 'label' ) ), $idtab );

							if ( $htmlFormattingView == 'table' ) {
								$html[]		=	'<table class="table table-sm table-borderless m-0">';
							}
						}

						$html[]				=	$viewContents;

						if ( $tabbed ) {
							if ( $htmlFormattingView == 'table' ) {
								$html[]		=	'</table>';
							}

							$html[]			=	$tabs->endTab();
						}
					}

					if ( $data ) {
						$this->popModelOfData();
					}
				} elseif ( $showviewType == 'xml' ) {
					// e.g.: <showview name="gateway_paymentstatus_information" mode="view" type="xml" file="processors/{payment_method}/edit.gateway" path="/*/views/view[@name=&quot;paymentstatusinformation&quot;]" mandatory="false" />
					$fromNode			=	$param->attributes( 'path' );
					$fromFile			=	$param->attributes( 'file' );
					$mandatory			=	$param->attributes( 'mandatory' );
					if ( $fromNode && ( $fromFile !== null ) ) {
						$this->substituteName( $fromFile, true );
						$this->substituteName( $fromNode, false );
						if ( $fromFile !== '' ) {
							$fromFile		=	static::pathFromXML( $fromFile . '.xml', $param, $this->_pluginObject );
						}
						if ( ( $fromFile === '' ) || is_readable( $fromFile ) ) {
							if ( $fromFile === '' ) {
								$fromRoot	=	$param;
							} else {
								$fromRoot	=	new SimpleXMLElement( $fromFile, LIBXML_NONET | ( defined('LIBXML_COMPACT') ? LIBXML_COMPACT : 0 ), true );
							}

							/** @var SimpleXMLElement[] $viewModels */
							$viewModels		=	$fromRoot->xpath( $fromNode );

							if ( $viewModels && count( $viewModels ) ) {
								if ( $data ) {
									$this->pushModelOfData( $data );
								}

								foreach ( $viewModels as $viewModel ) {
									if ( ! Access::authorised( $viewModel ) ) {
										continue;
									}

									$viewContents		=	$this->renderAllParams( $viewModel, $parent_cname, $tabs, $viewType, $htmlFormattingView );

									if ( $tabbed ) {
										if ( $viewContents === '' ) {
											continue;
										}

										$this->_i++;

										$idtab			=	$this->tabpaneNames[$tabpaneCounter] . $this->_i;

										$html[]			=	$tabs->startTab( $this->tabpaneNames[$tabpaneCounter], CBTxt::Th( $viewModel->attributes( 'label' ) ), $idtab );

										if ( $htmlFormattingView == 'table' ) {
											$html[]		=	'<table class="table table-sm table-borderless m-0">';
										}
									}

									$html[]				=	$viewContents;

									if ( $tabbed ) {
										if ( $htmlFormattingView == 'table' ) {
											$html[]		=	'</table>';
										}

										$html[]			=	$tabs->endTab();
									}
								}

								if ( $data ) {
									$this->popModelOfData();
								}
							} elseif ( $mandatory == 'false' ) {
								return null;
							} else {
								trigger_error( 'Extended renderAllParams:showview: View ' . $viewName . ': file ' . $fromFile . ', path: ' . $fromNode . ' does not exist or is empty.', E_USER_NOTICE );
							}
						} elseif (  $param->attributes( 'mandatory' ) == 'false' ) {
							return null;
						} else {
							trigger_error( 'Extended renderAllParams:showview: View ' . $viewName . ': file ' . $fromFile . ' does not exist or is not readable.', E_USER_NOTICE );
							return false;
						}
					}
				} elseif ( $showviewType == 'plugins' ) {
					$groups							=	explode( ',', (string) $param->attributes( 'groups' ) );
					$action							=	$param->attributes( 'action' );
					$path							=	$param->attributes( 'path' );

					$this->substituteName( $action, false );
					$this->substituteName( $path, false );

					if ( $data ) {
						$this->pushModelOfData( $data );
					}

					foreach ( $groups as $group ) {
						$matches						=	null;

						if ( preg_match( '/^([^\[]+)\[(.+)\]$/', $group, $matches ) ) {
							$classId					=	$matches[2];
							$group						=	$matches[1];
						} else {
							$classId					=	null;
						}

						global $_PLUGINS;

						$_PLUGINS->loadPluginGroup( $group, $classId, 0 );

						$loadedPlugins					=	$_PLUGINS->getLoadedPluginGroup( $group );

						foreach ( $loadedPlugins as /* $id => */ $plugin ) {
							$element					=	$_PLUGINS->loadPluginXML( 'action', $action, $plugin->id );

							/** @var SimpleXMLElement[] $viewModels */
							$viewModels					=	$element->xpath( $path );

							if ( $viewModels && count( $viewModels ) ) {
								foreach ( $viewModels as $viewModel ) {
									if ( ! Access::authorised( $viewModel ) ) {
										continue;
									}

									$viewContents		=	$this->renderAllParams( $viewModel, $parent_cname, $tabs, $viewType, $htmlFormattingView );

									if ( $tabbed ) {
										if ( $viewContents === '' ) {
											continue;
										}

										$this->_i++;

										$idtab			=	$this->tabpaneNames[$tabpaneCounter] . $this->_i;
										$tabTite		=	CBTxt::Th( $viewModel->attributes( 'label' ) );

										if ( ! $tabTite ) {
											$tabTite	=	$plugin->name;
										}

										$html[]			=	$tabs->startTab( $this->tabpaneNames[$tabpaneCounter], $tabTite, $idtab );

										if ( $htmlFormattingView == 'table' ) {
											$html[]		=	'<table class="table table-sm table-borderless m-0">';
										}

										if ( ! $plugin->published ) {
											$published	=	'<div class="alert alert-danger">' . CBTxt::T( 'PLUGIN_NAME_IS_NOT_PUBLISHED', '[plugin_name] is not published.', array( '[plugin_name]' => htmlspecialchars( CBTxt::T( $plugin->name ) ) ) ) . '</div>';

											if ( $htmlFormatting == 'table' ) {
												$html[]	=	'<tr><td colspan="2">' . $published .  '</td></tr>';
											} elseif ( $htmlFormatting == 'td' ) {
												$html[]	=	'<td>' . $published . '</td>';
											} elseif ( $htmlFormatting == 'div' ) {
												$html[]	=	'<div class="cb_form_line clearfix">' . $published . '</div>';
											} elseif ( $htmlFormatting == 'fieldsListArray' ) {
												// nothing
											} else {
												$html[]	= $published;
											}
										}
									}

									$html[]				=	$viewContents;

									if ( $tabbed ) {
										if ( $htmlFormattingView == 'table' ) {
											$html[]		=	'</table>';
										}

										$html[]			=	$tabs->endTab();
									}
								}
							}
						}
					}

					if ( $data ) {
						$this->popModelOfData();
					}
				} elseif ( $showviewType == 'private' ) {
					$dataModelClass					=	$param->attributes( 'class' );
					$methodName						=	$param->attributes( 'method' );
					$dataKey						=	$param->attributes( 'key' );
					if ( $dataModelClass && $methodName ) {
						if ( $data && $dataKey && isset( $data->$dataKey ) ) {
							$dataModelValue			=	$data->$dataKey;
						} elseif ( ! $dataKey ) {
							$dataModelValue			=	0;
						} else {
							trigger_error( sprintf( "Missing key field %s in data", htmlspecialchars( $dataKey ) ), E_USER_NOTICE );
							return null;
						}
						if ( strpos( $dataModelClass, '::' ) === false ) {
							if ( class_exists( $dataModelClass ) ) {
								/** @var $viewerClass TableInterface */
								global $_CB_database;
								$viewerClass		=	new $dataModelClass( $_CB_database );		// normal clas="className"
								if ( $dataModelValue ) {
									$viewerClass->load( $dataModelValue );
								}
							} else {
								trigger_error( "Missing private class " . htmlspecialchars( $dataModelClass ), E_USER_NOTICE );
								return null;
							}
						} else {
							$dataModelSingleton		=	explode( '::', $dataModelClass );	// class object loader from singleton: class="loaderClass::loadStaticMethor" with 1 parameter, the key value.
							if ( is_callable( $dataModelSingleton ) ) {
								$rows				=	call_user_func_array( $dataModelSingleton, array( $dataModelValue ) );
								$viewerClass		=	$rows[0];
							} else {
								trigger_error( "Missing singleton class creator " . htmlspecialchars( $dataModelClass ), E_USER_NOTICE );
								return null;
							}
						}
						if ( $viewerClass ) {
							if ( method_exists( $viewerClass, $methodName ) ) {
								/*
													$row	=	$this->_modelOfData[0];				//TBD: checked....
													foreach (get_object_vars($data) as $key => $v) {
														if( substr( $key, 0, 1 ) != '_' ) {			// internal attributes of an object are ignored
															if (isset($row->$key)) {
																$data->$key = $row->$key;
															}
														}
													}
								*/
								// this parameter is missing here ?: $control_name_name	=	$this->control_name( $control_name, $name );
								$html[]				=	$viewerClass->$methodName( $param, $data, $this->_pluginParams, $parent_cname, $tabs, $viewType, $htmlFormattingView, $tabbed );
								// shouldn't this be this? : $html[]				=	$viewerClass->$methodName( $value, $this->_pluginParams, $name, $node, $control_name, $control_name_name, $this->_view, $this->_modelOfData[0], $this->_modelOfDataRows, $this->_modelOfDataRowsNumber );

							} else {
								trigger_error( "Missing private xml method " . htmlspecialchars( $methodName ), E_USER_NOTICE );
								return null;
							}
						} else {
							trigger_error( "No data found !", E_USER_NOTICE );
							return null;
						}
					} else {
						trigger_error( "Missing private class or method attributes in xml", E_USER_NOTICE );
						return null;
					}
				}
				break;
			case 'hidden':
				if ( $viewType == 'view' ) {
					trigger_error( 'Type hidden in non-view mode not implemented in GUI.', E_USER_NOTICE );
				}
				break;
			case 'comment':
				break;

			case 'extendxmlparser':
				$this->setExtendedViewParser( $param );
				break;

			case 'extendparser':
				// old CBSubs GPL 3.0.0 case: ignore if not needed (but keep reference in case:
				$this->oldExtendParserNode		=	$param;
				break;

			case 'attributes':
				// Implemented above by ->extendParamAttributes
				break;

			default:
				if ( $this->_extendViewParser ) {
					$html[]						=	$this->_extendViewParser->renderAllParams( $param, $control_name, $tabs, $viewType, $htmlFormatting );
				} else {
					echo 'Method to render XML view element ' . $param->getName() . ' is not implemented !';
				}
				break;
		}
		return ( $htmlFormatting == 'fieldsListArray' ? $this->arrayValuesMerge( $html ) : trim( implode( "\n", $html ) ) );
	}

	/**
	 * Extends the XML in params
	 *
	 * @param  SimpleXMLElement  $param
	 * @param  SimpleXMLElement  $mainElement
	 * @param  SimpleXMLElement  $actionElement
	 * @param  PluginTable       $pluginObject
	 * @param  self              $data
	 * @return null|string
	 */
	static function extendXMLnode( $param, $mainElement, $actionElement, $pluginObject, $data = null ) {
		global $_CB_framework, $_PLUGINS;

		$return				=	null;

		$toNode				=	$param->attributes( 'toxpathnode' );
		$fromNode			=	$param->attributes( 'fromxpathnode' );
		$fromAllFiles		=	$param->attributes( 'file' );
		$mandatory			=	$param->attributes( 'mandatory' );
		$mode				=	$param->attributes( 'mode' );
		$replaces			=	$param->getChildByNameAttributes( 'replaces' );
		if ( $toNode && $fromNode && ( $fromAllFiles !== null ) ) {
			if ( $replaces ) {
				// set replacers for <replaces translate="yes"><replace attribute="label OR [DATA]" from="{source}" to="target" />...
				self::_substituteChildTexts( $replaces, null, null, $data );
				$substitutesFunction	=	array( __CLASS__, '_substituteChildTexts' );
			} else {
				$substitutesFunction	=	null;
			}

			if ( strpos( $fromAllFiles, '{xpath:' ) !== false ) {
				$fromAllFiles			=	preg_replace_callback( '/{xpath:([^}]+)}/', function( array $matches ) use ( $actionElement )
					{
						if ( preg_match( '/@([^\/@]+)\/string\(\)$/', $matches[1], $m2 ) ) {
							// emulate xpath 2.0's @attribute/string() ending:
							$attribute	=	$m2[1];
							$matches[1]	=	substr( $matches[1], 0, -9 );
						} else {
							$attribute	=	false;
						}
						/** @var SimpleXMLElement[] $elements */
						$elements	=	$actionElement->xpath( $matches[1] );
						if ( $elements !== false && count( $elements ) > 0 ) {
							if ( $attribute ) {
								return $elements[0]->attributes( $attribute );
							}
							return $elements[0]->data();
						} else {
							return null;
						}
					},
					$fromAllFiles );
			}

			foreach ( explode( ',', $fromAllFiles ) as $fromFile) {
				/* not needed anymore since we again have a single XML tree:
				if ( $toNode[0] === '/' ) {
					// if we extend from root, we want to extend the main XML tree:
					$toXml			=	$mainElement->xpath( $toNode );
				} else {
					// if we extend relatively, then we want to extend the tree of the node $param:
					$toXml			=	$param->xpath( $toNode );
				}
				* Instead we can do this as before:
				*/
				$toXml			=	$param->xpath( $toNode );
				/** @var SimpleXMLElement[] $toXml */

				if ( $toXml && ( count( $toXml ) == 1 ) ) {
					if ( $fromFile !== '' ) {
						if ( $data ) {
							$data->substituteName( $fromFile, true );
						}

						if ( ( $fromFile[0] != '/' ) && ( $param->attributes( 'type' ) == 'plugin' ) ) {
							$_PLUGINS->loadPluginGroup( null, null, ( $_CB_framework->getUi() == 2 ? 0 : 1 ) );

							$fromFiles		=	array();

							foreach ( $_PLUGINS->getLoadedPluginGroup( null ) as $plgObject ) {
								$fromFiles	=	array_merge( $fromFiles, static::pathsFromXML( static::pathFromXML( $fromFile . '.xml', $mainElement, $plgObject ) ) );
							}
						} else {
							$fromFile		=	static::pathFromXML( $fromFile . '.xml', $mainElement, $pluginObject );
							$fromFiles		=	static::pathsFromXML( $fromFile );
						}
					} else {
						$fromFiles			=	static::pathsFromXML( $fromFile );
					}

					foreach ( $fromFiles as $fromFilePath ) {
						if ( ( $fromFilePath === '' ) || is_readable( $fromFilePath ) ) {
							if ( $fromFilePath === '' ) {
								$fromRoot	=	$param;
							} else {
								$fromRoot	=	new SimpleXMLElement( $fromFilePath, LIBXML_NONET | ( defined('LIBXML_COMPACT') ? LIBXML_COMPACT : 0 ), true );
							}
							// function copyXML( $fromXml) ::
							$group			=	$fromRoot->attributes( 'group' );
							$element		=	$fromRoot->attributes( 'element' );
							if ( ! $element ) {
								$element	=	null;
							}
							if ( $group ) {
								// loads PHP classes associated with the XML file:
								$_PLUGINS->loadPluginGroup( $group, $element, ( $_CB_framework->getUi() == 2 ? 0 : 1 ) );
								if ( $group && $element && ! $_PLUGINS->getLoadedPlugin( $group, $element ) ) {
									continue;
								}
							}
							/** @var SimpleXMLElement[] $fromXml */
							$fromXml		=	$fromRoot->xpath( $fromNode );
							if ( $fromXml && count( $fromXml ) ) {
								foreach ( array_keys( $fromXml ) as $k ) {
									switch ( $mode ) {
										case 'prepend':
											$toXml[0]->insertNodeAndChildrenBefore( $fromXml[$k], $substitutesFunction );
											break;
										case 'insertafter':
											$toXml[0]->insertNodeAndChildrenAfter( $fromXml[$k], $substitutesFunction );
											break;
										case 'replaceorappend':
											$keyattribute	=	$param->attributes( 'keyattribute' );
											if ( ! $keyattribute ) {
												$return		.=	'ERROR: missing the "keyattribute" attribute on node "extend" in xml file when trying to extend with file "' . htmlspecialchars( $fromFilePath ) . '" from node "' . $fromNode . '"';
												break;
											}
											/** @var SimpleXMLElement $nodesToReplace */
											$nodesToReplace	=	$toXml[0]->xpath( $fromXml[$k]->getName() . '[@' . $keyattribute . '="' . $fromXml[$k]->attributes( $keyattribute ) . '"]' );
											/** @var SimpleXMLElement[] $nodesToReplace */
											if ( $nodesToReplace && ( count( $nodesToReplace ) > 0 ) ) {
												$nodesToReplace[0]->replaceNodeAndChildren( $fromXml[$k], $substitutesFunction );
												break;
												//	} else {
												//		$return		.=	'ERROR: ' . count( $nodesToReplace ) . ' nodes to replace with keyattribute ' . htmlspecialchars( $keyattribute );
											}
											$toXml[0]->addChildWithDescendants( $fromXml[$k], $substitutesFunction );
											break;
										case 'extend':
											$keyattribute	=	$param->attributes( 'keyattribute' );
											if ( ! $keyattribute ) {
												$return		.=	'ERROR: missing the "keyattribute" attribute on node "extend" in xml file when trying to extend with file "' . htmlspecialchars( $fromFilePath ) . '" from node "' . $fromNode . '"';
												break;
											}
											/** @var SimpleXMLElement $nodesToReplace */
											$nodesToReplace	=	$toXml[0]->xpath( $fromXml[$k]->getName() . '[@' . $keyattribute . '="' . $fromXml[$k]->attributes( $keyattribute ) . '"]' );
											/** @var SimpleXMLElement[] $nodesToReplace */
											if ( $nodesToReplace && ( count( $nodesToReplace ) > 0 ) ) {
												static::extendNodeAndChildren( $fromXml[$k], $nodesToReplace[0], $substitutesFunction, $keyattribute );
												break;
												//	} else {
												//		$return		.=	'ERROR: ' . count( $nodesToReplace ) . ' nodes to replace with keyattribute ' . htmlspecialchars( $keyattribute );
											}
											$toXml[0]->addChildWithDescendants( $fromXml[$k], $substitutesFunction );
											break;
										case 'append':
										default:
											$toXml[0]->addChildWithDescendants( $fromXml[$k], $substitutesFunction );
											break;
									}
								}
							} elseif ( $mandatory != 'false' ) {
								$return	.=	'ERROR: fromxpathnode does not match ' . htmlspecialchars( $fromNode ) . ' in ' . htmlspecialchars( $fromFilePath ) . ' ';
							}
							unset( $fromXml, $fromRoot );
						} elseif ( $mandatory != 'false' ) {
							$return		.=	'ERROR: missing the xml file ' . htmlspecialchars( $fromFilePath );
						}
					}
				} else {
					$return				.=	'ERROR: toxpathnode does not match ' . htmlspecialchars( $toNode ) . ' or has more than one node: has ' . count( $toXml ) . ' nodes.';
				}
			}
		}
		return $return;
	}

	/**
	 * @param  SimpleXMLElement  $fromXml
	 * @param  SimpleXMLElement  $toXml
	 * @param  callable|null     $substitutesFunction
	 * @param  string            $keyAttribute
	 */
	private static function extendNodeAndChildren( SimpleXMLElement $fromXml, SimpleXMLElement $toXml, $substitutesFunction, $keyAttribute )
	{
		if ( ! ( $fromXml->getName() === $toXml->getName() && $fromXml->attributes( $keyAttribute ) === $toXml->attributes( $keyAttribute ) ) ) {
			return;
		}

		foreach ( $fromXml->children() as $fromChild ) {

			if ( $fromChild->attributes( $keyAttribute ) !== null ) {
				$toChild	=	$toXml->getChildByNameAttr( $fromChild->getName(), $keyAttribute, $fromChild->attributes( $keyAttribute ) );
			} elseif ( count( $fromChild->attributes() ) > 0 ) {
				$toChild	=	$toXml->getChildByNameAttributes( $fromChild->getName(), $fromChild->attributes() );
			} elseif ( trim( $fromXml->data() ) != '' ) {
				$toChildren	=	$toXml->xpath( $fromXml->getName() . '[text()="' . addcslashes( $fromXml->data(), '"' ) . '"]' );
				$toChild	=	( count( $toChildren ) > 0 ) ? $toChildren[0] : null;
			} else {
				/* No attributes nor data: search single child with no attributes: */
				$toChildren	=	$toXml->xpath( $fromChild->getName() . '[count(@*)=0]' );

				if ( count( $toChildren ) == 1 ) {
					$toChild		=	$toChildren[0];
				} else {
					/* No attributes nor data: search single child with attributes: */
					$toChildren		=	$toXml->xpath( $fromChild->getName() );

					if ( count( $toChildren ) == 1 ) {
						$toChild	=	$toChildren[0];
					} else {
						$toChild	=	null;
					}
				}
			}
			if ( $toChild ) {
				self::extendNodeAndChildren( $fromChild, $toChild, $substitutesFunction, $keyAttribute );
				continue;
			}

			$insertAfter			=	$fromChild->attributes( 'insert-node-after' );

			if ( $insertAfter ) {
				$toChild			=	$toXml->getChildByNameAttr( $fromChild->getName(), $keyAttribute, $insertAfter );

				if ( $toChild ) {
					$toChild->insertNodeAndChildrenAfter( $fromChild, $substitutesFunction );
				}
				continue;
			}

			$insertBefore			=	$fromChild->attributes( 'insert-node-before' );

			if ( $insertBefore ) {
				$toChild			=	$toXml->getChildByNameAttr( $fromChild->getName(), $keyAttribute, $insertBefore );

				if ( $toChild ) {
					$toChild->insertNodeAndChildrenBefore( $fromChild, $substitutesFunction );
				}
				continue;
			}

			$toXml->addChildWithDescendants( $fromChild, $substitutesFunction );
		}
	}

	/**
	 * Triggers plugins and Extends the XML in params
	 * <trigger group="user,user/plug_cbxyz/plugin" event="eventXYZ" results="extend" toxpathnode=".." mode="replaceorappend" keyattribute="name" />
	 *
	 * @param  SimpleXMLElement  $param
	 * @param  PluginTable       $pluginObject
	 * @param  boolean           $isSaving
	 * @return null|string
	 */
	public function triggerXML( &$param, &$pluginObject, $isSaving ) {
		global $_CB_framework, $_PLUGINS;

		$return				=	null;

		$group				=	$param->attributes( 'group' );
		$event				=	$param->attributes( 'event' );
		$results			=	$param->attributes( 'results' );
		$toNode				=	$param->attributes( 'toxpathnode' );
		$mandatory			=	$param->attributes( 'mandatory' );
		$mode				=	$param->attributes( 'mode' );
		$keyattribute		=	$param->attributes( 'keyattribute' );
		$replaces			=	$param->getChildByNameAttributes( 'replaces' );

		if ( $group && $event ) {
			// Load all groups:
			$allGroups		=	explode( ',', $group );
			foreach ( $allGroups as $gr ) {
				$_PLUGINS->loadPluginGroup( $gr, null, ( $_CB_framework->getUi() == 2 ? 0 : 1 ) );
			}

			if ( $replaces ) {
				// set replacers for <replaces translate="yes"><replace attribute="label OR [DATA]" from="{source}" to="target" />...
				self::_substituteChildTexts( $replaces, null, null, $this );
				$substitutesFunction	=	array( __CLASS__, '_substituteChildTexts' );
			} else {
				$substitutesFunction	=	null;
			}

			// Get the model of the data:
			$dataModel		=	$this->getModelOfData();
			$storage		=	$dataModel->getStorage();

			// Fire the event:
			$args			=	array( $param, $pluginObject, $storage ? $storage : $dataModel, $isSaving );
			$eventResults	=	$_PLUGINS->trigger( $event, $args );

			// Handle the event results:
			if ( $results == 'showview' ) {
				//TBD later
			} elseif ( $results == 'extend' ) {

				/* not needed anymore since we again have a single XML tree:
				if ( $toNode[0] === '/' ) {
					// if we extend from root, we want to extend the main XML tree:
					$toXml			=	$this->_parent->_actions->xpath( $toNode );
				} else {
					// if we extend relatively, then we want to extend the tree of the node $param:
					$toXml			=	$param->xpath( $toNode );
				}
				* Instead we can do this as before:
				*/
				/** @var SimpleXMLElement[] $toXml */
				$toXml			=	$param->xpath( $toNode );

				if ( $toXml && ( count( $toXml ) == 1 ) ) {
					foreach ( $eventResults as $fromXml ) {
						if ( is_array( $fromXml ) ) {
							$return		.=	self::extendXMLXML( $fromXml, $toXml, $pluginObject, $mode, $keyattribute, $substitutesFunction );
						} elseif ( $mandatory != 'false' ) {
							$return		.=	'ERROR: XML trigger tag group ' . htmlspecialchars( $group ) . ' for event ' . htmlspecialchars( $event ) . ' returns no mandatory object';
						}
					}
				} else {
					$return				.=	'ERROR: toxpathnode does not match ' . htmlspecialchars( $toNode ) . ' or has more than one node: has ' . count( $toXml ) . ' nodes.';
				}
			}
		}
		return $return;
	}

	/**
	 * Extends the XML in params
	 *
	 * @param  SimpleXMLElement[]  $fromXml
	 * @param  SimpleXMLElement[]  $toXml
	 * @param  PluginTable         $pluginObject
	 * @param  string              $mode
	 * @param  string              $keyattribute
	 * @param  callback            $substitutesFunction
	 * @return null
	 */
	static function extendXMLXML( $fromXml, &$toXml, /** @noinspection PhpUnusedParameterInspection */ &$pluginObject, $mode, $keyattribute, $substitutesFunction ) {
		foreach ( array_keys( $fromXml ) as $k ) {
			switch ( $mode ) {
				//TODO: when needed 'prepend' mode.
				case 'replaceorappend':
					$nodesToReplace	=	$toXml[0]->xpath( $fromXml[$k]->getName() . '[@' . $keyattribute . '="' . $fromXml[$k]->attributes( $keyattribute ) . '"]' );
					/** @var SimpleXMLElement[] $nodesToReplace */
					if ( count( $nodesToReplace ) > 0 ) {
						$nodesToReplace[0]->replaceNodeAndChildren( $fromXml[$k], $substitutesFunction );
						break;
					}
					$toXml[0]->addChildWithDescendants( $fromXml[$k], $substitutesFunction );
					break;
				case 'append':
				default:
					$toXml[0]->addChildWithDescendants( $fromXml[$k], $substitutesFunction );
					break;
			}
		}
		return null;
	}

	/**
	 * @param SimpleXMLElement $param
	 * @return bool
	 */
	private function checkParamCondition( $param )
	{
		if ( $param->attributes( 'type' ) === 'permission' ) {
			$showInside				=	Access::authorised( $param );
		} else {
			$showInside				=	$this->_evalIf( $param );
		}

		if ( $param->getElementByPath( 'and' ) || $param->getElementByPath( 'or' ) ) {
			foreach ( $param->children() as $andCondition ) {
				if ( ( $andCondition->getName() !== 'and' ) && ( $andCondition->getName() !== 'or' ) ) {
					continue;
				}

				if ( $showInside ) {
					if ( $andCondition->getName() === 'or' ) {
						continue;
					}
				} elseif ( $andCondition->getName() === 'and' ) {
					continue;
				}

				if ( ! $this->checkParamCondition( $andCondition ) ) {
					if ( $andCondition->getName() === 'and' ) {
						$showInside	=	false;
					}
				} elseif ( $andCondition->getName() === 'or' ) {
					$showInside		=	true;
				}
			}
		}

		return $showInside;
	}

	/**
	 * Extends any XML parsing with support for child <if> conditions
	 *
	 * @param SimpleXMLElement $param
	 */
	public function extendParamConditions( $param )
	{
		if ( ! $param->getElementByPath( 'if' ) ) {
			return;
		}

		foreach ( $param->children() as $condition ) {
			if ( $condition->getName() !== 'if' ) {
				continue;
			}

			if ( $this->checkParamCondition( $condition ) ) {
				$then						=	$condition->getChildByNameAttributes( 'then' );

				if ( $then ) {
					$insideParamToRender	=	$then;
				} elseif ( $condition->getChildByNameAttributes( 'else' ) ) {
					$insideParamToRender	=	null;
				} else {
					$insideParamToRender	=	$condition;
				}
			} else {
				$insideParamToRender		=	$condition->getChildByNameAttributes( 'else' );
			}

			if ( $insideParamToRender ) {
				$this->extendParamConditions( $insideParamToRender );

				foreach ( $insideParamToRender->children() as $newNode ) {
					$condition->insertNodeAndChildrenAfter( $newNode, null, true );
				}
			}

			$param->removeChild( $condition );
		}
	}

	/**
	 * checks if there is an <attributes> extension in a <param> and sets attributes depending on any other param type
	 *
	 * @param  SimpleXMLElement  $param         The element to extend (modified by adding attributes from <attributes>)
	 * @param  string            $control_name  The control name
	 * @param  boolean           $view          true if view only, false if editable
	 * @return void
	 */
	public function extendParamAttributes( &$param, $control_name = 'params', $view = true ) {
		$attributes											=	$param->getElementByPath( 'attributes' );
		if ( $attributes && Access::authorised( $attributes ) ) {
			foreach ( $attributes->children() as $attr ) {
				/** @var  SimpleXMLElement  $attr */
				if ( ( $attr->getName() == 'attribute' ) && Access::authorised( $attr ) ) {
					$attName								=	$attr->attributes( 'name' );
					$attSeparator							=	$attr->attributes( 'separator' );
					$attTransform							=	$attr->attributes( 'transform' );
					$attMode								=	$attr->attributes( 'mode' );
					$replacements							=	false;
					if ( ( $attMode == null ) || ( ( $attMode == 'edit' ) && ! $view ) || ( ( $attMode == 'show' ) && $view ) ) {
						$attrArray							=	array();
						if ( $attName ) {
							foreach ( $attr->children() as $dataAttr ) {
								/** @var  SimpleXMLElement  $dataAttr */
								if ( ( ! Access::authorised( $dataAttr ) ) && ( ! ( ( $dataAttr->getName() == 'if' ) && ( $dataAttr->attributes( 'type' ) == 'permission' ) ) ) ) {
									continue;
								}
								if ( $dataAttr->getName() == 'param' ) {
									$this->extendParamAttributes( $dataAttr, $control_name );
									$result					=	$this->renderParam( $dataAttr, $control_name, true, 'div' );
									$attrArray[$attName][]	=	$result[1];
								} elseif ( $dataAttr->getName() == 'replaces' ) {
									self::_substituteChildTexts( $dataAttr, null, null, $this );
									$replacements			=	true;
								} elseif ( $dataAttr->getName() == 'if' ) {
									$this->extendParamAttributes( $dataAttr, $control_name );
									$result					=	$this->renderOneParamAndChildren( $dataAttr, $control_name, null, 'view', 'none' );
									$attrArray[$attName][]	=	$result;
								} elseif ( $dataAttr->getName() == 'data' ) {
									// keep silent here for now here as it was used only for decoration		//TODO CB 2.0: remove this
								} else {
									trigger_error( sprintf( 'attributes/attribute child tag "%s" name="%s" of param with name="%s" is not supported, only param is.', $dataAttr->getName(), $dataAttr->attributes('name'), $param->attributes('name') ), E_USER_WARNING );
								}
							}
							if ( $replacements ) {
								$attrArray					=	self::_substituteChildTexts( $attrArray );
							}
							foreach ( $attrArray as $attK => $attV ) {
								if ( $attSeparator === null ) {
									$attSeparator			=	'|*|';
								}
								if ( $attTransform == 'raw' ) {
									$param->addAttribute( $attK, implode( $attSeparator, $attV ) );
								} else {
									$param->addAttribute( $attK, htmlspecialchars( implode( $attSeparator, $attV ) ) );
								}
							}
						}
					}
				}
			}
		}

	}

	/**
	 * Auxiliary function for replacing texts when extending XML
	 * <replaces translate="yes"><replace attribute="label OR [DATA]" from="{source}" to="target" />
	 *
	 * @param  SimpleXMLElement|string|array $sourceData             Source data to substitute
	 * @param  SimpleXMLElement|null         $sourceNode             (unused)
	 * @param  SimpleXMLElement|null         $destinationParentNode  (unused)
	 * @param  RegistryEditView                   $paramsView             The params view to access data to replace if attribute type="datavalue:string"
	 * @return string|string[]|SimpleXMLElement|null                 The replaced $sourceData
	 */
	static function _substituteChildTexts( $sourceData, /** @noinspection PhpUnusedParameterInspection */ $sourceNode = null, /** @noinspection PhpUnusedParameterInspection */ $destinationParentNode = null, $paramsView = null ) {
		static $substitutions	=	array();
		if ( is_array( $sourceData ) ) {
			// that is $source->attributes():
			$return				=	array();
			foreach ($sourceData as $k => $v ) {
				if ( isset( $substitutions[$k] ) ) {
					$v			=	str_replace( $substitutions[$k]['from'], $substitutions[$k]['to'], $v );
					if ( $substitutions[$k]['translate'] ) {
						$v		=	CBTxt::T( $v );
					}
				}
				$return[$k]		=	$v;
			}
		} elseif ( is_string( $sourceData ) ) {
			// that is $source->data():
			if ( isset( $substitutions['[DATA]'] ) ) {
				$k				=	'[DATA]';
				$return			=	str_replace( $substitutions[$k]['from'], $substitutions[$k]['to'], $sourceData );
				if ( $substitutions[$k]['translate'] ) {
					$return		=	CBTxt::T( $return );
				}
			} else {
				$return			=	$sourceData;
			}
		} elseif ( is_object( $sourceData ) ) {
			// initialize replacements:
			$substitutions		=	array();
			foreach ($sourceData->children() as $replaceRule ) {
				/** @var $replaceRule SimpleXMLElement */
				$substitutions[$replaceRule->attributes( 'attribute' )]['from'][]			=	$replaceRule->attributes( 'from' );
				$substitutions[$replaceRule->attributes( 'attribute' )]['to'][]				=	( $replaceRule->attributes( 'type' ) == 'datavalue:string' ? $paramsView->get( $replaceRule->attributes( 'to' ) ) : $replaceRule->attributes( 'to' ) );
				$substitutions[$replaceRule->attributes( 'attribute' )]['translate']		=	$replaceRule->attributes( 'translate' ) === 'yes';
			}
			$return				=	null;
		} else {
			trigger_error(__CLASS__ . '::' . __FUNCTION__ . 'Invalid type for $sourceData.' );
			$return				=	null;
		}
		return $return;
	}

	/**
	 * Substitute file-securely '{var_name}' in $viewName with values from the record being displayed
	 * Varname coould be folder.element or folder.subfolder.element
	 *
	 * @param  string   $viewName     String IN+OUT
	 * @param  boolean  $subFolders   if the . should be TRUE: converted to /, or FALSE: stripped with strings before
	 */
	public function substituteName( &$viewName, $subFolders ) {
		$that		=	$this;
		$viewName	=	preg_replace_callback( '/{(?:([^:]+):)?([^}]+)}/', function( array $matches ) use ( $subFolders, $that )
						{
							if ( $matches[1] != 'raw' ) {
								if ( $subFolders ) {
									$replace			=	str_replace( '.', '/', $that->get( $matches[2] ) );
								} else {
									$replace			=	preg_replace( '/^([^.]+\.|\/)+/', '', $that->get( $matches[2] ) );
								}
							} else {
								$replace				=	$that->get( $matches[2], '' );
							}

							if ( ! preg_match( '#//|\\|:|\0|\s|\+#', $replace ) ) {
								return $replace;
							}

							return null;
						},
						(string) $viewName );
	}

	/**
	 * Extends $param with option children, resolving xml:.... types.
	 *
	 * @param  SimpleXMLElement  $param                  Parameter to extend
	 * @param  bool              $addErrorToXmlAsOption  Should the error be added as an option instead of the options in case of error
	 * @return null|string                               Error message
	 */
	public function resolveXmlParamType( SimpleXMLElement $param, $addErrorToXmlAsOption = false )
	{
		$errorMsg		=	null;

		$type			=	$param->attributes( 'type' );

		if ( substr( $type, 0, 4 ) == 'xml:' ) {
			$xmlType	=	substr( $type, 4 );
			if ( $this->_types ) {
				$typeModel	=	$this->_types->getChildByNameAttr( 'type', 'name' , $xmlType );
				// find root type:
				if ( $typeModel ) {
					$root		=	$typeModel;
					$subbasetype =	(string) $root->attributes( 'base' );

					for ( $i = 0; $i < 100; $i++ ) {
						if ( substr( (string) $root->attributes( 'base' ), 0, 4 ) == 'xml:' ) {
							$subbasetype	=	(string) $root->attributes( 'base' );
							$root	=	$this->_types->getChildByNameAttr( 'type', 'name' , substr( $subbasetype, 4 ) );
							if ( ! $root ) {
								$errorMsg	=	"Missing type definition of " . $subbasetype;
								break;
							}
						} else {
							// we found the root and type:
							$type	=	$root->attributes( 'base' );
							break;
						}
					}
					if ( $i >= 99 ) {
						echo 'Error: recursion loop in XML type definition of ' . $typeModel->getName() . ' ' . $typeModel->attributes( 'name' ) . ' type: ' . $typeModel->attributes( 'type' );
						exit;
					}
					$levelModel		=	$typeModel;
					$insertAfter	=	array();
					for ( $i = 0; $i < 100; $i++ ) {
						switch ( $type ) {
							case 'list':
							case 'multilist':
							case 'radio':
							case 'checkbox':
							case 'checkmark':
							case 'published':
							case 'usergroup':
							case 'viewaccesslevel':
							case 'tag':
								$valueType		=	$root->attributes( 'valuetype' );
								$subbasetype	=	$valueType ? $valueType : 'string';

								/*
 								if ( $view ) {
									$valueNode	=	$levelModel->getAnyChildByNameAttr( 'option', 'value', $value );	// recurse in children over optgroups if needed.
									if ( $valueNode ) {
										if ( $htmlFormatting == 'fieldsListArray' ) {
											$result[1]	=	$valueNode->data();
										} else {
											$value	=	$valueNode->data();
										}
									}
								} else {
							 */
								if ( $levelModel->attributes( 'insertbase' ) != 'before' ) {
									foreach ( $levelModel->children() as $option ) {
										/** @var  SimpleXMLElement  $option */
										if ( $option->getName() == 'option' ) {
											$param->addChildWithAttr( 'option', $option->data(), null, $option->attributes() );
										} elseif ( $option->getName() == 'optgroup' ) {
											$paramOptgroup = $param->addChildWithAttr( 'optgroup', $option->data(), null, $option->attributes() );
											// in HTML 4, optgroups can not be nested (w3c)
											foreach ( $option->children() as $optChild ) {
												/** @var  SimpleXMLElement $optChild */
												if ( $optChild->getName() == 'option' ) {
													$paramOptgroup->addChildWithAttr( 'option', $optChild->data(), null, $optChild->attributes() );
												} elseif ( $optChild->getName() == 'data' ) {
													$paramOptgroup->appendChild( $optChild );
												}
											}
										} elseif ( $option->getName() == 'data' ) {
											$param->appendChild( $option );
										}
									}
								} else {
									$insertAfter[]	=	$levelModel;
								}
								break;
							default:
								$errorMsg	=	"Unknown base type " . $type . " in XML";
								break;
						}
						if ( ( $levelModel->attributes( 'name' ) == $typeModel->attributes( 'name' ) ) && ( substr( (string) $levelModel->attributes( 'type' ), 0, 4 ) == 'xml:' ) ) {
							$levelModel	=	$this->_types->getChildByNameAttr( 'type', 'name' , substr( (string) $levelModel->attributes( 'type' ), 4 ) );
						} elseif ( substr( (string) $levelModel->attributes( 'base' ), 0, 4 ) == 'xml:' ) {
							$levelModel	=	$this->_types->getChildByNameAttr( 'type', 'name' , substr( (string) $levelModel->attributes( 'base' ), 4 ) );
						} else {
							break;
						}

					}
					foreach ( $insertAfter as $levelModel ) {
						/** @var  SimpleXMLElement  $levelModel */
						foreach ($levelModel->children() as $option ) {
							/** @var  SimpleXMLElement  $option */
							if ( $option->getName() == 'option' ) {
								$param->addChildWithAttr( 'option', $option->data(), null, $option->attributes() );
							} elseif ( $option->getName() == 'optgroup' ) {
								$paramOptgroup = $param->addChildWithAttr( 'optgroup', $option->data(), null, $option->attributes() );
								foreach ( $option->children() as $optChild ) {
									if ( $optChild->getName() == 'option' ) {
										$paramOptgroup->addChildWithAttr( 'option', $optChild->data(), null, $optChild->attributes() );
									} elseif ( $optChild->getName() == 'data' ) {
										$paramOptgroup->appendChild( $optChild );
									}
								}
							} elseif ( $option->getName() == 'data' ) {
								$param->appendChild( $option );
							}
						}
					}

					$param['type']	=	$type;
					if ( $subbasetype ) {
						$param['base'] = $subbasetype;
					}

				} else {
					$errorMsg	= "Missing type def. for param-type " .  $param->attributes( 'type' );
				}
			} else {
				$errorMsg		=	"No types defined in XML";
			}

			if ( $errorMsg && $addErrorToXmlAsOption ) {
				$param->addChildWithAttr( 'option', $errorMsg, null, array( 'value' => '0') );
			}
		}
		return $errorMsg;
	}

	/**
	 * Renders a parameter
	 *
	 * @param  SimpleXMLElement $param           object A param tag node
	 * @param  string             $control_name    The control name
	 * @param  boolean            $view            true if view only, false if editable
	 * @param  string             $htmlFormatting  'table', 'fieldsListArray', etc.
	 * @return array                               Any array of the label, the form element and the tooltip
	 *
	 * @throws \Exception
	 */
	public function renderParam( &$param, $control_name = 'params', $view = true, $htmlFormatting = 'div' ) {
		if ( $htmlFormatting == 'fieldsListArray' && ! self::$fieldsListArrayValues ) {
			return array( null, $this->control_name( $control_name, $param->attributes( 'name' ) ), null );
		}
		$result = array();

		$type			=	$param->attributes( 'type' );
		$name			=	$param->attributes( 'name' );
		$label			=	CBTxt::Th($param->attributes( 'label' ));
		$description	=	$param->attributes( 'description' );
		if ( $description !== null && $description !== '' ) {
			$description =	CBTxt::Th( $description );
		}
		if ( $name ) {
			if ( $type == 'spacer' ) {
				$value	=	$param->attributes( 'default' );
			} elseif ( ( $type == 'private' ) && ( ( $param->attributes( 'value' ) !== null ) || ( ! ( $this->getModelOfData() instanceof RegistryInterface ) ) && ! isset( $this->getModelOfData()->$name ) ) ) {
				$value	=	$param->attributes( 'value' );		//TBD: we will need to improve this: this case is a workaround to avoid accessing with get() an unexistant variable
			} elseif ( $param->attributes( 'nametype' ) === 'cbconfig' ) {
				$value	=	Application::Config()->get( $name, $param->attributes( 'default' ) );
			} else {
				$value	=	$this->get( $name, $param->attributes( 'default' ) );
			}
		} elseif ( $type === 'primarycheckbox' ) {
			$keys			=	explode( ',', (string) $param->attributes( 'keys' ) );
			$values			=	[];

			foreach ( $keys as $k ) {
				$values[]	=	$k . '=' . $this->get( $k );
			}

			$value			=	implode( '|*|', $values );

			if ( ! $value ) {
				$value		=	$param->attributes( 'default' );
			}
		} else {
			$value		=	$param->attributes( 'default' );
		}

		if ( ( $param->attributes( 'translate' ) == 'yes' ) || ( $param->attributes( 'translate' ) == '_UE' ) ) {
			$value		=	CBTxt::T( $value );
		}

		$result[0]				=	( $label !== null ? $label : $name );

		if ( $result[0] == '@spacer' ) {
			$result[0]			=	'<hr/>';
		} else if ( $result[0] ) {
			if ( $name == '@spacer' ) {
				$result[0]		=	'<strong>' . $result[0] . '</strong>';
			}
		}

		$result[1]		=	null;
		$errorMsg		=	null;

		// Resolve "xml:..." $type into $param['type'] and childrens of $param:
		$errorMsg		=	$this->resolveXmlParamType( $param, ! $view );

		// Re-fetch the new resolved type:
		$type			=	$param->attributes( 'type' );

		if ( ! isset( $this->_methods ) ) {
			$this->_methods = get_class_methods( get_class( $this ) );
		}
		$methodName							=	'_form_' . ( $htmlFormatting == 'fieldsListArray' ? '_get_' : '' ) . $type;

		if ( $errorMsg ) {
			if ( $htmlFormatting == 'fieldsListArray' ) {
				throw new Exception( $errorMsg );
			} else {
				$result[1]					=	$errorMsg;
			}
		} elseif ( $result[1] ) {
			// nothing to do as done above
		} elseif (in_array( $methodName, $this->_methods )) {
			$this->_view					=	$view;
			$result[1] = call_user_func( array( $this, $methodName ), $name, $value, $param, $control_name );
		} elseif ( $this->_extendViewParser && in_array( $methodName, $this->_extendViewParser->_methods ) ) {
			$this->_view					=	$view;
			$this->_extendViewParser->_view	=	$view;
			$result[1] = call_user_func( array( $this->_extendViewParser, $methodName ), $name, $value, $param, $control_name );
		} elseif ( $this->isOldExtend( $methodName ) ) {
			$result[1] = $this->callOldExtend( $view, $methodName, $name, $value, $param, $control_name );
		} elseif ( $htmlFormatting == 'fieldsListArray' ) {
			// Default way to bind the DI Input:
			$this->_view					=	$view;
			$result[1] =	$this->_form_get_default( $name, $value, $param, $control_name );
		} else {
			$errorMsg						=	sprintf( CBTxt::T("Parameter Handler for type=%s is not implemented or not loaded."), $type );
			if ( $htmlFormatting == 'fieldsListArray' ) {
				throw new Exception( $errorMsg );
			} else {
				$result[1] = $errorMsg;
			}
		}

		if ( $htmlFormatting == 'fieldsListArray' ) {
			return array( $result[0], array( $this->control_name( $control_name, $param->attributes( 'name' ) ) => $result[1] ), $description );
		}

		$result[2]			=	'';

		if ( ( $htmlFormatting != 'fieldsListArray' ) && ( ! $view ) ) {
			$validate		=	explode( ',', (string) $param->attributes( 'validate' ) );

			if ( in_array( 'required', $validate ) ) {
				if ( ( $param->attributes( 'requiredicon' ) !== 'hide' )
					 && ( ( ! $this->_parentModelOfView ) || ( $this->_parentModelOfView && ( $this->_parentModelOfView->attributes( 'requiredicons' ) !== 'hide' ) ) )
				) {
					$result[2]	.=	cbTooltip( null, CBTxt::Th( 'UE_FIELDREQUIRED', 'This Field is required' ), null, null, null, '<span class="fa fa-star text-muted"></span>' )
								.	( $description ? ' ' : null );
				}
			} elseif ( in_array( 'optional', $validate ) ) {
				if ( trim( $result[0] ) !== '' ) {
					$result[0]	.=	' <span class="text-small text-muted cbValidationOptional">' . CBTxt::T( '(optional)' ) . '</span>';
				}
			}
		}

		if ( $description ) {
			$tipTitle		=	( $label !== null ? $label : $name );

			if ( substr( $tipTitle, -2 ) == "%s" ) {
				$tipTitle	=	substr( $tipTitle, 0, -2 );
			}

			$result[2]		.=	cbTooltip( null, $description, trim( $tipTitle ), null, null, '<span class="fa fa-info-circle text-muted"></span>' );
		}

		if ( ( ! $view ) && ( $result[1] === null ) ) {
			$result		=	array( null, null, null );
		}
		return $result;
	}

	/**
	 * Checks if there is a $methodName in an old-CBSubs GPL 3.0.0-way extend
	 *
	 * @param  string   $methodName  Name of method to call
	 * @return boolean               Loaded and can be called
	 */
	private function isOldExtend( $methodName )
	{
		if ( ! $this->oldExtendParserNode ) {
			return false;
		}

		$this->setOldExtendedViewParser( $this->oldExtendParserNode );

		return ( $this->oldExtendViewParser && in_array( $methodName, $this->oldExtendViewParser->_methods ) );
	}

	/**
	 * Calls $methodName of old-CBSubs GPL 3.0.0-way extend
	 *
	 * @param  boolean           $view
	 * @param  string            $methodName
	 * @param  string            $name
	 * @param  string            $value
	 * @param  SimpleXMLElement  $param
	 * @param  string            $control_name
	 * @return string
	 */
	private function callOldExtend( $view, $methodName, $name, $value, &$param, $control_name )
	{
		$this->_view						=	$view;
		$this->oldExtendViewParser->_view	=	$view;
		return call_user_func_array( array( $this->oldExtendViewParser, $methodName ), array( $name, $value, &$param, $control_name ) );
	}

	protected function inputControlName( $name )
	{
		return strtr( $name, array( '[' => '.', ']' => '' ) );
	}

	/**
	 * view param type _form_TYPE implementation
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement    $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return mixed                              The value
	 */
	protected function _form_get_default( $name, $value, /** @noinspection PhpUnusedParameterInspection */ $node, /** @noinspection PhpUnusedParameterInspection */ $control_name ) {
		// Temporary default method for backwards-compatibility:
		return $this->input->get( $this->inputControlName( $name ), $value, GetterInterface::STRING );
	}

	/**
	 * view param type _form_TYPE implementation
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement    $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return mixed                              The value
	 */
	protected function _form__get_int( $name, $value, /** @noinspection PhpUnusedParameterInspection */ $node, /** @noinspection PhpUnusedParameterInspection */ $control_name ) {
		// Temporary default method for backwards-compatibility:
		return $this->input->get( $this->inputControlName( $name ), $value, GetterInterface::INT );
	}

	/**
	 * view param type _form_TYPE implementation
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement    $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return mixed                              The value
	 */
	protected function _form__get_float( $name, $value, /** @noinspection PhpUnusedParameterInspection */ $node, /** @noinspection PhpUnusedParameterInspection */ $control_name ) {
		// Temporary default method for backwards-compatibility:
		return $this->input->get( $this->inputControlName( $name ), $value, GetterInterface::FLOAT );
	}

	/**
	 * view param type _form_TYPE implementation
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement    $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return mixed                              The value
	 */
	protected function _form__get__htmlarea( $name, $value, /** @noinspection PhpUnusedParameterInspection */ $node, /** @noinspection PhpUnusedParameterInspection */ $control_name ) {
		// Temporary default method for backwards-compatibility:
		return $this->input->get( $this->inputControlName( $name ), $value, GetterInterface::HTML );
	}

	/**
	 * view param type _form_TYPE implementation
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement    $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return mixed                              The value
	 */
	protected function _form__get_password( $name, $value, /** @noinspection PhpUnusedParameterInspection */ $node, /** @noinspection PhpUnusedParameterInspection */ $control_name ) {
		// Temporary default method for backwards-compatibility:
		return $this->input->get( $this->inputControlName( $name ), $value, GetterInterface::RAW );
	}

	/**
	 * Returns an array-written parameter name as "$control_name[$name]" if $control_name is set, otherwise '$name'
	 * (static version)
	 *
	 * @param  string  $control_name  The control name of the controlling-array
	 * @param  string  $name          The name of the param
	 * @return string                 The form input parameter name
	 */
	public static function control_name_static( $control_name, $name ) {
		if ( $control_name ) {
			return $control_name .'['. $name .']';
		} else {
			return $name;
		}
	}

	/**
	 * Returns an array-written parameter name as "$control_name[$name]" if $control_name is set, otherwise '$name'
	 * (object method version)
	 *
	 * @param  string  $control_name  The control name of the controlling-array
	 * @param  string  $name          The name of the param
	 * @return string                 The form input parameter name
	 */
	public function control_name( $control_name, $name ) {
		return self::control_name_static( $control_name, $name );
	}

	/**
	 * Returns the HTML id for a $control_name and a $name
	 *
	 * @param  string  $control_name  The control name of the controlling-array
	 * @param  string  $name          The name of the param
	 * @return string                 The HTML id
	 */
	public function control_id( $control_name, $name ) {
		return moscomprofilerHTML::htmlId( $this->control_name( $control_name, $name ) );
		/*
				if ( $control_name ) {
					return str_replace( array( '[', ']' ), array( '__', '' ), $control_name ) .'__'. $name;
				} else {
					return $name;
				}
		*/
	}

	/**
	 * Internal method to generate title attribute for HTML
	 *
	 * @param  SimpleXMLElement  $node  The node to look for name and descritpion
	 * @return null|string
	 */
	protected function _title( &$node ) {
		$description		=	CBTxt::T( $node->attributes( 'description' ) );
		if ( $description ) {
			$name			=	$node->attributes( 'name' );
			return ' title="' . htmlspecialchars( $name . '|' . $description ) .'"';
		}
		return null;
	}

	/**
	 * Returns HTML attributes with tooltip appended
	 *
	 * @param  SimpleXMLElement  $node
	 * @param  null|string       $attributes
	 * @param  null|string       $html
	 * @return string
	 */
	protected function getTooltipAttr( $node, $attributes = null, $html = null ) {
		$label				=	CBTxt::Th( $node->attributes( 'label' ) );

		if ( $label !== null ) {
			if ( substr( $label, -2 ) == "%s" ) {
				$label		=	substr( $label, 0, -2 );
			}
		} else {
			$label			=	$node->attributes( 'name' );
		}

		$title				=	null;
		$description		=	null;

		if ( $node->attributes( 'valuedescription' ) ) {
			$title			=	CBTxt::Th( $node->attributes( 'valuedescriptiontitle' ) );

			if ( ! $title ) {
				$title		=	$label;
			}

			$description	=	CBTxt::Th( $node->attributes( 'valuedescription' ) );
		} elseif ( $node->attributes( 'description' ) ) {
			$title			=	$label;
			$description	=	CBTxt::Th( $node->attributes( 'description' ) );
		}

		$return				=	$html;

		if ( $title || $description ) {
			$return			=	cbTooltip( null, $description, trim( $title ), null, null, $html, null, ( ( $attributes === null ) && ( $html === null ) ? 'data-hascbtooltip="true"' : $attributes ) );
		} elseif ( ( ! $html ) && $attributes ) {
			$return			=	' ' . $attributes;
		}

		return $return;
	}

	/**
	 * Internal method to generate <textarea or <tag HTML element
	 *
	 * @param  string              $tag           The tag (textarea of tagname)
	 * @param  SimpleXMLElement  $node          The node to generate the HTML tag for
	 * @param  string              $control_name  The control name
	 * @param  string              $name          The name attribute for the tag html element
	 * @param  string              $value         The value attribute for the tag html element
	 * @param  string              $classes       The CSS classes for the tag html element
	 * @param  string              $text          Additional text for additional attributes for the tag html element
	 * @return string                             The rendered HTML tag
	 */
	protected function _todom( $tag, &$node, $control_name, $name, $value, $classes, $text ) {
		$placeholder			=	$node->attributes( 'blanktext' );

		$classes				=	'class="' . htmlspecialchars( $classes ) . '"';
		$attributes				=	$this->getTooltipAttr( $node, $classes );

		if ( $placeholder ) {
			$attributes			.=	' placeholder="' . htmlspecialchars( CBTxt::T( $placeholder ) ) . '"';
		}

		if ( $this->_isrepeat ) {
			$repeatDefault		=	$node->attributes( 'default' );

			if ( ( $node->attributes( 'translate' ) == 'yes' ) || ( $node->attributes( 'translate' ) == '_UE' ) ) {
				$repeatDefault	=	CBTxt::Th( $repeatDefault );
			}

			if ( $repeatDefault ) {
				$attributes		.=	' data-cbrepeat-default="' . htmlspecialchars( $repeatDefault ) . '"';
			}
		}

		$validationRules		=	array(	'required', 'requiredIf', 'remote', 'email', 'url', 'date',
											'dateISO', 'number', 'digits', 'creditcard', 'equalTo', 'notEqualTo', 'accept',
											'maxlength', 'minlength', 'maxselect', 'minselect', 'rangelength', 'range',
											'max', 'min', 'step', 'maxWords', 'minWords', 'rangeWords', 'extension', 'pattern',
											'isPattern', 'cbfield', 'cbremote', 'cbusername', 'cburl', 'filesize',
											'filesizemin', 'filesizemax', 'cropwidth', 'cropheight', 'forbiddenWords'
										);

		foreach ( $validationRules as $validationRule ) {
			$validationParam	=	$node->attributes( 'validate-' . $validationRule );

			if ( $validationParam ) {
				$attributes		.=	cbValidator::getRuleHtmlAttributes( $validationRule, $validationParam );
			}
		}

		if ( in_array( $tag, array( 'button', 'textarea' ) ) ) {
			return '<' . $tag . ' name="'. $this->control_name( $control_name, $name ) . '" id="'. $this->control_id( $control_name, $name ) . '"' . $attributes . $text . '>' . htmlspecialchars( $value ) .'</' . $tag . '>';
		}

		return '<' . $tag . ' name="'. $this->control_name( $control_name, $name ) . '" id="'. $this->control_id( $control_name, $name ) . '" value="'. htmlspecialchars($value) .'"' . $attributes . $text . ' />';
	}

	/**
	 * Internal method to generate <select> HTML element
	 *
	 * @param  array               $arr                  The options of the select
	 * @param  SimpleXMLElement  $node                 The node describing the select
	 * @param  string              $control_name         The control name
	 * @param  string              $name                 The name attribute
	 * @param  string|array        $value                The selected value(s)
	 * @param  boolean             $multiple             If multiple values can be selected
	 * @param  boolean             $htmlspecialcharText  If htmlspecialchar needs to be done
	 * @param  string|null         $text                 Additional text for the html tag for additional attributes
	 * @return string                                    The formatted HTML tag
	 */
	protected function selectList( &$arr, &$node, $control_name, $name, $value, $multiple = false,  $htmlspecialcharText = true, $text = null ) {
		$cssClasses				=	array( 'form-control' );
		$attributes				=	null;

		$this->_list_select2( $node, $control_name, $name, $arr, $attributes, $cssClasses, $multiple );

		$size					=	$node->attributes( 'size' );
		$multi					=	( $multiple ? ' multiple="multiple"' : '' );
		$siz					=	( $size ? ' size="' . (int) $size . '"' : null );
		$classes				=	'class="' . htmlspecialchars( RegistryEditView::buildClasses( $node, $cssClasses ) ) . '"';
		$validate				=	$node->attributes( 'validate' );
		$addBlank				=	( $node->attributes( 'hideblanktext' ) == 'always' ? false : null );

		if ( ( $validate === null ) || in_array( 'required', explode( ',', $validate ) ) ) {
			$required			=	1;
		} else {
			$required			=	0;
		}

		$attributes				.=	$this->getTooltipAttr( $node, $classes );

		if ( $this->_isrepeat ) {
			$repeatDefault		=	$node->attributes( 'default' );

			if ( $node->attributes( 'translate' ) != 'no' ) {
				$repeatDefault	=	CBTxt::T( $repeatDefault );
			}

			if ( $repeatDefault ) {
				$attributes		.=	' data-cbrepeat-default="' . htmlspecialchars( $repeatDefault ) . '"';
			}
		}

		return moscomprofilerHTML::selectList( $arr, $this->control_name( $control_name, $name ) . ( $multiple ? '[]' : '' ), ( $text ? ' ' . $text : '' ) . $multi . $siz . $attributes, 'value', 'text', $value, $required, $htmlspecialcharText, $addBlank, false );
	}

	/**
	 * Internal method to build the classes for validations using jQuery Validate plugin
	 *
	 * @param  SimpleXMLElement  $node              The node to compute the classes for
	 * @param  string[]          $classes           Additional classes to also return
	 * @param  string            $element           The type of element the classes are being added to (e.g. block, table-cell, inline, inline-block, etc..); only used for responsive classes
	 * @return string                               The CSS classes for the jQuery Validate validation
	 */
	public static function buildClasses( &$node, $classes = array(), $element = 'inline-block' ) {
		$validate							=	$node->attributes( 'validate' );

		if ( $validate ) {
			$validations					=	explode( ',', $validate );

			foreach ( $validations as $validation ) {
				if ( $validation && ( ! in_array( $validation, $classes ) ) && in_array( $validation , array( 'required', 'number', 'digits', 'email', 'date', 'url', 'creditcard' ) ) ) {
					$classes[]				=	$validation;
				}
			}
		}

		$iconClass							=	$node->attributes( 'iconclass' );

		if ( $iconClass ) {
			if ( ! in_array( 'fa', $classes ) ) {
				$classes[]					=	'fa';
			}

			$iconClass						=	'fa-' . $iconClass;

			if ( ! in_array( $iconClass, $classes ) ) {
				$classes[]					=	$iconClass;
			}

			$iconSize						=	$node->attributes( 'iconsize' );
			$iconStyle						=	$node->attributes( 'iconstyle' );

			if ( $iconSize && in_array( $iconSize, array( 'small', 'sm', 'large', 'lg', 'xlarge', '2x', 'xxlarge', '3x', 'xxxlarge', '4x', 'xxxxlarge', '5x' ) ) ) {
				switch( $iconSize ) {
					case 'small':
					case 'sm':
						$iconSize			=	'sm';
						break;
					case 'large':
					case 'lg':
						$iconSize			=	'lg';
						break;
					case 'xlarge':
					case '2x':
						$iconSize			=	'2x';
						break;
					case 'xxlarge':
					case '3x':
						$iconSize			=	'3x';
						break;
					case 'xxxlarge':
					case '4x':
						$iconSize			=	'4x';
						break;
					case 'xxxxlarge':
					case '5x':
						$iconSize			=	'5x';
						break;
				}

				if ( $iconStyle == 'before' ) {
					$iconSize				=	'before-' . $iconSize;
				}

				$iconSize					=	'fa-' . $iconSize;

				if ( ! in_array( $iconSize, $classes ) ) {
					$classes[]				=	$iconSize;
				}
			}

			if ( $iconStyle && in_array( $iconStyle, array( 'before', 'spin', 'rotate90', 'rotate180', 'rotate270', 'fliphorizontal', 'flipvertical' ) ) ) {
				switch( $iconStyle ) {
					case 'rotate90':
						$iconStyle			=	'rotate-90';
						break;
					case 'rotate180':
						$iconStyle			=	'rotate-180';
						break;
					case 'rotate270':
						$iconStyle			=	'rotate-270';
						break;
					case 'fliphorizontal':
						$iconStyle			=	'flip-horizontal';
						break;
					case 'flipvertical':
						$iconStyle			=	'flip-vertical';
						break;
				}

				$iconStyle					=	'fa-' . $iconStyle;

				if ( ! in_array( $iconStyle, $classes ) ) {
					$classes[]				=	$iconStyle;
				}
			}
		}

		$buttonClass						=	$node->attributes( 'buttonclass' );

		if ( $buttonClass && in_array( $buttonClass, array( 'default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger', 'light', 'dark', 'link', 'outline-primary', 'outline-secondary', 'outline-success', 'outline-info', 'outline-warning', 'outline-danger', 'outline-light', 'outline-dark', 'muted', 'inverse' ) ) ) {
			// Convert legacy classes to latest for B/C:
			switch( $buttonClass ) {
				case 'default':
					$buttonClass			=	'secondary';
					break;
				case 'muted':
					$buttonClass			=	'light';
					break;
				case 'inverse':
					$buttonClass			=	'dark';
					break;
			}

			if ( in_array( 'fa', $classes ) ) {
				$iconKey					=	array_search( 'fa', $classes );

				unset( $classes[$iconKey] );

				if ( ! in_array( 'fa-before', $classes ) ) {
					$classes[]				=	'fa-before';
				}

				if ( ! in_array( 'fa-prefix', $classes ) ) {
					$classes[]				=	'fa-prefix';
				}
			}

			if ( ! in_array( 'btn', $classes ) ) {
				$classes[]					=	'btn';
			}

			$buttonClass					=	'btn-' . $buttonClass;

			if ( ! in_array( $buttonClass, $classes ) ) {
				$classes[]					=	$buttonClass;
			}

			$buttonSize						=	$node->attributes( 'buttonsize' );

			if ( $buttonSize && in_array( $buttonSize, array( 'xsmall', 'xs', 'small', 'sm', 'large', 'lg' ) ) ) {
				switch( $buttonSize ) {
					case 'xsmall':
					case 'xs':
					case 'small':
					case 'sm':
						$buttonSize			=	'sm';
						break;
					case 'large':
					case 'lg':
						$buttonSize			=	'lg';
						break;
				}

				$buttonSize					=	'btn-' . $buttonSize;

				if ( ! in_array( $buttonSize, $classes ) ) {
					$classes[]				=	$buttonSize;
				}
			}

			$buttonStyle					=	$node->attributes( 'buttonstyle' );

			if ( $buttonStyle && in_array( $buttonStyle, array( 'block' ) ) ) {
				$buttonStyle				=	'btn-' . $buttonStyle;

				if ( ! in_array( $buttonStyle, $classes ) ) {
					$classes[]				=	$buttonStyle;
				}
			}
		}

		$borderStyles						=	$node->attributes( 'borderstyle' );

		if ( $borderStyles ) {
			$borderStyles					=	explode( ',', $borderStyles );

			foreach ( $borderStyles as $borderStyle ) {
				if ( ( ! $borderStyle ) || ( ! in_array( $borderStyle, array( 'all', 'top', 'bottom', 'left', 'right', 'none', 'top-none', 'bottom-none', 'left-none', 'right-none' ) ) ) ) {
					continue;
				}

				if ( $borderStyle == 'all' ) {
					$borderStyle			=	'border';
				} else {
					switch( $borderStyle ) {
						case 'none':
							$borderStyle	=	'0';
							break;
						case 'top-none':
							$borderStyle	=	'top-0';
							break;
						case 'bottom-none':
							$borderStyle	=	'bottom-0';
							break;
						case 'left-none':
							$borderStyle	=	'left-0';
							break;
						case 'right-none':
							$borderStyle	=	'right-0';
							break;
					}

					$borderStyle			=	'border-' . $borderStyle;
				}

				if ( ! in_array( $borderStyle, $classes ) ) {
					$classes[]				=	$borderStyle;
				}
			}
		}

		$borderClass						=	$node->attributes( 'borderclass' );

		if ( $borderClass && in_array( $borderClass, array( 'default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger', 'light', 'dark', 'white' ) ) ) {
			$borderClass					=	( $borderClass == 'default' ? 'border' : 'border-' . $borderClass );

			if ( ! in_array( $borderClass, $classes ) ) {
				$classes[]					=	$borderClass;
			}
		}

		$textClass							=	$node->attributes( 'textclass' );

		if ( $textClass && in_array( $textClass, array( 'default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger', 'muted', 'light', 'dark', 'white', 'hide', 'inverse' ) ) ) {
			// Convert legacy classes to latest for B/C:
			switch( $textClass ) {
				case 'default':
					$textClass				=	'body';
					break;
				case 'inverse':
					$textClass				=	'white';
					break;
			}

			$textClass						=	'text-' . $textClass;

			if ( ! in_array( $textClass, $classes ) ) {
				$classes[]					=	$textClass;
			}
		}

		$textStyle							=	$node->attributes( 'textstyle' );

		if ( $textStyle ) {
			$textStyles						=	explode( ',', $textStyle );

			foreach ( $textStyles as $class ) {
				if ( $class && in_array( $class , array( 'left', 'center', 'right', 'justify', 'overflow', 'nowrap', 'wrap', 'wrapall', 'overline', 'underline', 'strike', 'lowercase', 'uppercase', 'capitalize', 'bold', 'italic', 'truncate' ) ) ) {
					switch( $class ) {
						case 'bold':
							$class			=	'font-weight-bold';
							break;
						case 'italic':
							$class			=	'font-italic';
							break;
						default:
							$class			=	'text-' . $class;
							break;
					}

					if ( ! in_array( $class, $classes ) ) {
						$classes[]			=	$class;
					}
				}
			}
		}

		$labelClass							=	$node->attributes( 'labelclass' );

		if ( $labelClass && in_array( $labelClass, array( 'default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger', 'light', 'dark', 'muted', 'inverse' ) ) ) {
			// Convert legacy classes to latest for B/C:
			switch( $labelClass ) {
				case 'default':
					$labelClass				=	'secondary';
					break;
				case 'muted':
					$labelClass				=	'light';
					break;
				case 'inverse':
					$labelClass				=	'dark';
					break;
			}

			if ( ! in_array( 'badge', $classes ) ) {
				$classes[]					=	'badge';
			}

			$labelClass						=	'badge-' . $labelClass;

			if ( ! in_array( $labelClass, $classes ) ) {
				$classes[]					=	$labelClass;
			}

			if ( ! in_array( 'rounded-0', $classes ) ) {
				$classes[]					=	'rounded-0';
			}
		}

		$badgeClass							=	$node->attributes( 'badgeclass' );

		if ( $badgeClass && in_array( $badgeClass, array( 'default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger', 'light', 'dark', 'muted', 'inverse' ) ) ) {
			// Convert legacy classes to latest for B/C:
			switch( $badgeClass ) {
				case 'default':
					$badgeClass				=	'secondary';
					break;
				case 'muted':
					$badgeClass				=	'light';
					break;
				case 'inverse':
					$badgeClass				=	'dark';
					break;
			}

			if ( ! in_array( 'badge', $classes ) ) {
				$classes[]					=	'badge';
			}

			if ( ! in_array( 'badge-pill', $classes ) ) {
				$classes[]					=	'badge-pill';
			}

			$badgeClass						=	'badge-' . $badgeClass;

			if ( ! in_array( $badgeClass, $classes ) ) {
				$classes[]					=	$badgeClass;
			}
		}

		$bgClass							=	$node->attributes( 'bgclass' );

		if ( $bgClass && in_array( $bgClass, array( 'default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger', 'light', 'dark', 'white', 'muted', 'inverse', 'transparent', 'none' ) ) ) {
			// Convert legacy classes to latest for B/C:
			switch( $bgClass ) {
				case 'default':
					$bgClass				=	'white';
					break;
				case 'muted':
					$bgClass				=	'light';
					break;
				case 'inverse':
					$bgClass				=	'dark';
					break;
			}

			$bgClass						=	'bg-' . $bgClass;

			if ( ! in_array( $bgClass, $classes ) ) {
				$classes[]					=	$bgClass;
			}
		}

		$alertClass							=	$node->attributes( 'alertclass' );

		if ( $alertClass && in_array( $alertClass, array( 'primary', 'success', 'info', 'warning', 'danger', 'light', 'dark', 'default', 'muted', 'inverse' ) ) ) {
			if ( $element == 'inline' ) {
				if ( ! in_array( 'd-inline-block', $classes ) ) {
					$classes[]				=	'd-inline-block';
				}

				if ( ! in_array( 'm-0', $classes ) ) {
					$classes[]				=	'm-0';
				}
			}

			// Convert legacy classes to latest for B/C:
			switch( $alertClass ) {
				case 'default':
				case 'muted':
					$alertClass				=	'light';
					break;
				case 'inverse':
					$alertClass				=	'dark';
					break;
			}

			if ( $iconClass !== '' ) {
				if ( in_array( 'fa', $classes ) ) {
					$iconKey				=	array_search( 'fa', $classes );

					unset( $classes[$iconKey] );
				} else {
					switch( $alertClass ) {
						case 'default':
						default:
							$alertIcon		=	'fa-comment';
							break;
						case 'primary':
						case 'light':
							$alertIcon		=	'fa-exclamation-circle';
							break;
						case 'success':
							$alertIcon		=	'fa-check-cricle';
							break;
						case 'info':
							$alertIcon		=	'fa-info-circle';
							break;
						case 'warning':
							$alertIcon		=	'fa-warning';
							break;
						case 'danger':
							$alertIcon		=	'fa-times-circle';
							break;
						case 'dark':
							$alertIcon		=	'fa-comment-o';
							break;
					}

					if ( ! in_array( $alertIcon, $classes ) ) {
						$classes[]			=	$alertIcon;
					}
				}

				if ( ! in_array( 'fa-before', $classes ) ) {
					$classes[]				=	'fa-before';
				}

				if ( ! in_array( 'fa-prefix', $classes ) ) {
					$classes[]				=	'fa-prefix';
				}
			}

			if ( ! in_array( 'alert', $classes ) ) {
				$classes[]					=	'alert';
			}

			$alertClass						=	'alert-' . $alertClass;

			if ( ! in_array( $alertClass, $classes ) ) {
				$classes[]					=	$alertClass;
			}
		}

		$wellClass							=	$node->attributes( 'wellclass' );

		if ( $wellClass && in_array( $wellClass, array( 'default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger', 'light', 'dark', 'white', 'muted', 'inverse' ) ) ) {
			// Convert legacy classes to latest for B/C:
			switch( $wellClass ) {
				case 'muted':
					$wellClass				=	'light';
					break;
				case 'inverse':
					$wellClass				=	'dark';
					break;
			}

			if ( in_array( $wellClass, array( 'primary', 'secondary', 'success', 'danger', 'info', 'dark' ) ) ) {
				if ( ! in_array( 'text-white', $classes ) ) {
					$classes[]				=	'text-white';
				}
			}

			$wellBGClass					=	'bg-' . ( $wellClass == 'default' ? 'light' : $wellClass );

			if ( ! in_array( $wellBGClass, $classes ) ) {
				$classes[]					=	$wellBGClass;
			}

			if ( ! in_array( 'border', $classes ) ) {
				$classes[]					=	'border';
			}

			$wellClass						=	( $wellClass == 'default' ? 'border' : 'border-' . $wellClass );

			if ( ! in_array( $wellClass, $classes ) ) {
				$classes[]					=	$wellClass;
			}

			if ( ! in_array( 'rounded', $classes ) ) {
				$classes[]					=	'rounded';
			}

			$wellSize						=	$node->attributes( 'wellsize' );

			if ( $wellSize && in_array( $wellSize, array( 'small', 'sm', 'large', 'lg' ) ) ) {
				switch( $wellSize ) {
					case 'small':
					case 'sm':
						$wellSize			=	'2';
						break;
					case 'large':
					case 'lg':
						$wellSize			=	'4';
						break;
				}

				$wellSize					=	'p-' . $wellSize;

				if ( ! in_array( $wellSize, $classes ) ) {
					$classes[]				=	$wellSize;
				}
			} else {
				$wellSize					=	'p-3';

				if ( ! in_array( $wellSize, $classes ) ) {
					$classes[]				=	$wellSize;
				}
			}
		}

		$imgClass							=	$node->attributes( 'imgclass' );

		if ( $imgClass && in_array( $imgClass, array( 'rounded', 'circle', 'thumbnail', 'responsive' ) ) ) {
			// Convert legacy classes to latest for B/C:
			switch( $imgClass ) {
				case 'circle':
					$imgClass				=	'rounded-circle';
					break;
				case 'responsive':
					$imgClass				=	'img-fluid';
					break;
			}

			if ( ! in_array( $imgClass, $classes ) ) {
				$classes[]					=	$imgClass;
			}
		}

		$visibleClass						=	$node->attributes( 'responsivevisibleon' );

		if ( $visibleClass ) {
			$visibleClasses					=	explode( ',', $visibleClass );

			foreach ( $visibleClasses as $class ) {
				if ( $class && in_array( $class, array( 'xsmall', 'xs', 'small', 'sm', 'medium', 'md', 'large', 'lg', 'xlarge', 'xlg', 'print' ) ) ) {
					switch( $class ) {
						case 'xsmall':
						case 'xs':
							$class			=	'd-' . $element . ' d-sm-none';
							break;
						case 'small':
						case 'sm':
							$class			=	'd-none d-sm-' . $element . ' d-md-none';
							break;
						case 'medium':
						case 'md':
							$class			=	'd-none d-md-' . $element . ' d-lg-none';
							break;
						case 'large':
						case 'lg':
							$class			=	'd-none d-lg-' . $element . ' d-xl-none';
							break;
						case 'xlarge':
						case 'xl':
							$class			=	'd-none d-xl-' . $element;
							break;
						case 'print':
							$class			=	'd-none d-print-' . $element;
							break;
					}

					if ( ! in_array( $class, $classes ) ) {
						$classes[]			=	$class;
					}
				}
			}
		}

		$hiddenClass						=	$node->attributes( 'responsivehiddenon' );

		if ( $hiddenClass ) {
			$hiddenClasses					=	explode( ',', $hiddenClass );

			foreach ( $hiddenClasses as $class ) {
				if ( $class && in_array( $class , array( 'xsmall', 'xs', 'small', 'sm', 'medium', 'md', 'large', 'lg', 'xlarge', 'xlg', 'print' ) ) ) {
					switch( $class ) {
						case 'xsmall':
						case 'xs':
							$class			=	'd-none d-sm-' . $element;
							break;
						case 'small':
						case 'sm':
							$class			=	'd-sm-none d-md-' . $element;
							break;
						case 'medium':
						case 'md':
							$class			=	'd-md-none d-lg-' . $element;
							break;
						case 'large':
						case 'lg':
							$class			=	'd-lg-none d-xl-' . $element;
							break;
						case 'xlarge':
						case 'xl':
							$class			=	'd-xl-none';
							break;
						case 'print':
							$class			=	'd-print-none';
							break;
					}

					if ( ! in_array( $class, $classes ) ) {
						$classes[]			=	$class;
					}
				}
			}
		}

		$cssClass							=	$node->attributes( 'cssclass' );

		if ( $cssClass ) {
			$cssClasses						=	explode( ',', $cssClass );

			foreach ( $cssClasses as $class ) {
				if ( $class && ( ! in_array( $class, $classes ) ) ) {
					$classes[]				=	$class;
				}
			}
		}

		return trim( implode( ' ', $classes ) );
	}

	/**
	 * Internal utility method for text-type fields used by other field-types
	 *
	 * @param  string              $name              The name of the form element
	 * @param  string              $value             The value of the element
	 * @param  SimpleXMLElement  $node                The xml element for the parameter
	 * @param  string              $control_name      The control name
	 * @param  string[]            $classes           The base CSS classes
	 * @param  string              $text              Additional text for additional attributes for the tag html element
	 * @return string                                 The html for the element
	 */
	protected function textfield( $name, $value, &$node, $control_name, $classes = array(), $text = null ) {
		if ( $this->_view ) {
			$sprintf	=	 $node->attributes( 'sprintf' );
			if ( $sprintf ) {
				return htmlspecialchars( sprintf( $sprintf, $value ) );
			} else {
				return htmlspecialchars( $value );
			}
		} else {
			$size			=	$node->attributes( 'size' );
			$siz			=	( $size ? ' size="' . (int) $size . '"' : null );
			$classes[]		=	'form-control';
			$classes		=	RegistryEditView::buildClasses( $node, $classes );
			// return '<input type="text" name="'. $this->control_name( $control_name, $name ) . '" id="'. $this->control_id( $control_name, $name ) . '" value="'. htmlspecialchars($value) .'" class="text_area" size="'. $size .'" />';
			// return $this->_todom( 'input', $node, 'type="text" name="'. $this->control_name( $control_name, $name ) . '" id="'. $this->control_id( $control_name, $name ) . '" value="'. htmlspecialchars($value) .'" class="text_area" size="'. $size .'"' );
			return $this->_todom( 'input', $node, $control_name, $name, $value, $classes, 'type="text"' . $siz . $text );
		}
	}

	/**
	 * view param type _form_TYPE implementation
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_text( $name, $value, $node, $control_name ) {
		return  $this->textfield( $name, (string) $value, $node, $control_name );
	}

	/**
	 * view param type _form_TYPE implementation
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_string( $name, $value, $node, $control_name ) {
		return  $this->textfield( $name, (string) $value, $node, $control_name );
	}

	/**
	 * view param type _form_TYPE implementation
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_int( $name, $value, $node, $control_name ) {
		return  $this->textfield( $name, (string) $value, $node, $control_name, array( 'digits' ) );		//TBD enforce int also on save...
	}

	/**
	 * view param type _form_TYPE implementation
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_float( $name, $value, $node, $control_name ) {
		return  $this->textfield( $name, (string) $value, $node, $control_name, array( 'number' ) );		//TBD enforce float also on save...
	}

	/**
	 * view param type _form_TYPE implementation
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_user( $name, $value, $node, $control_name ) {
		return $this->_form_int( $name, $value, $node, $control_name );		//TBD show ajax powered select2 of users
	}

	/**
	 * view param type _form_TYPE implementation
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement    $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_tag( $name, $value, $node, $control_name ) {
		if ( $this->_view ) {
			$size						=	0;
			$cols						=	$node->attributes( 'cols' );
			$rows						=	$node->attributes( 'rows' );

			if ( is_array( $value ) ) {
				$selected				=	$value;
			} else {
				if ( $value !== null ) {
					$selected			=	explode( '|*|', $value );
				} else {
					$selected			=	array();
				}
			}

			$contentOptions				=	$this->_list_options_selected( $name, $node, $control_name, $node->children(), $selected );
			$contentTexts				=	array();

			foreach ( $contentOptions as $contentOption ) {
				$key					=	$contentOption->value;

				$contentTexts[$key]		=	htmlspecialchars( $contentOption->text );
			}

			// Check if there are any custom selected values and add them to display:
			foreach ( $selected as $selectedValue ) {
				if ( ! array_key_exists( $selectedValue, $contentTexts ) ) {
					$contentTexts[]		=	htmlspecialchars( CBTxt::T( $selectedValue ) );
				}
			}

			// Get rid of the keys as we only wanted them to check for custom selected values:
			$contentTexts				=	array_values( $contentTexts );

			if ( count( $contentTexts ) > 0 ) {
				if ( $cols || $rows ) {
					$content			=	moscomprofilerHTML::list2Table( $contentTexts, $cols, $rows, $size );
				} else {
					$content			=	implode( ', ', $contentTexts );
				}
			} else {
				$content				=	' - ';
			}

			return $content;
		} else {
			$this->_jsselect2			=	true;

			// Force select2 usage for tags:
			$node->addAttribute( 'filteringselect', 'true' );

			$translate					=	$node->attributes( 'translate' );

			if ( is_array( $value ) ) {
				$value					=	implode( '|*|', $value );
			}

			$options					=	array();

			$this->_list_options( $name, $node, $control_name, $options, $node->children(), true, $value, true );

			$selected					=	explode( '|*|', (string) $value );
			$systemOptions				=	array();

			// Map the system options so we can test for their existing against custom options:
			foreach ( $options as $option ) {
				$systemOptions[]		=	$option->value;
			}

			// Check if there are any custom selected values and add them to display:
			foreach ( $selected as $selectedValue ) {
				if ( ( $selectedValue !== '' ) && ( ! in_array( $selectedValue, $systemOptions ) ) ) {
					$options[]			=	$this->_list_make_option( $translate, $selectedValue, CBTxt::T( $selectedValue ) );
				}
			}

			return $this->selectList( $options, $node, $control_name, $name, $selected, true, true, ' data-cbselect-tags="true"' );
		}
	}

	/**
	 * Calls method or function of plugin/tab
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_custom( $name, $value, $node, $control_name ) {
		global $_CB_database, $_PLUGINS;

		$pluginId	=	( $this->_pluginObject ? $this->_pluginObject->id : null );
		$tabId		=	$this->_tabid;

		$class	=	$node->attributes( 'class' );
		$method	=	$node->attributes( 'method' );
		if(!is_null($class) && strlen(trim($class)) > 0) {
			if ($pluginId !== null) {
				$params	=	null;
				if ($tabId !== null) {
					$_CB_database->setQuery( "SELECT * FROM #__comprofiler_tabs t"
						. "\n WHERE t.enabled=1 AND t.tabid = " . (int) $tabId);
					$oTabs = $_CB_database->loadObjectList();
					if (count($oTabs)>0) $params = $oTabs[0]->params;
				}
				$args = array($name,$value,$control_name);
				$_PLUGINS->plugVarValue( $pluginId, 'published', '1' );		// need to be able to call also unpublished plugin for parametring
				return $_PLUGINS->call($pluginId,$method,$class,$args,$params);
			} else {
				$udc = new $class();
				if(method_exists($udc,$method)) {
					return call_user_func_array(array($udc,$method),array($name,$value,$control_name));
				}
			}
		} elseif (function_exists( $method )) {
			return call_user_func_array( $method, array($name,$value,$control_name) );
		}
		return '';

	}

	/**
	 * Calls a method of either specified class or of class specified in parent view model element.
	 * This is not private because 'doaction' calls it from TableBrowser
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	public function _form_private( $name, $value, $node, $control_name ) {
		global $_CB_database;

		$data								=	null;

		$dataModelClass						=	$node->attributes( 'class' );
		$methodName							=	$node->attributes( 'method' );
		$dataKey							=	$node->attributes( 'key' );
		if ( ! $dataModelClass ) {
			$dataModelClass					=	$this->_parentModelOfView->attributes( 'class' );
		}
		if ( $dataModelClass && $methodName ) {
			if ( $dataModelClass == 'self' ) {
				$data						=	$this->getModelOfData();
			} else {
				if ( ! $dataKey ) {
					$dataModelValue			=	0;
				} elseif ( $this->_modelOfData[0]->get( $dataKey ) !== null ) {
					$dataModelValue			=	$this->get( $dataKey );
				} else {
					$default				=	CBTxt::Th( $node->attributes( 'default' ) );
					if ( $default === null ) {
						$default			=	CBTxt::Th("Missing value field %s in data of row");
					}
					$content				=	sprintf( $default, htmlspecialchars( $dataKey ) );
					return $content;
				}
				if ( strpos( $dataModelClass, '::' ) === false ) {
					if ( class_exists( $dataModelClass ) ) {
						/** @var $data TableInterface */
						$data				=	new $dataModelClass( $_CB_database );		// normal clas="className"
						if ( $dataModelValue ) {
							$data->load( $dataModelValue );
						}
					} else {
						$content			=	"Missing private class " . htmlspecialchars( $dataModelClass );
						return $content;
					}
				} else {
					$dataModelSingleton		=	explode( '::', $dataModelClass );	// class object loader from singleton: class="loaderClass::loadStaticMethor" with 1 parameter, the key value.
					if ( is_callable( $dataModelSingleton ) ) {
						if ( is_callable( array( $dataModelSingleton[0], 'getInstance' ) ) ) {
							$instance		=	call_user_func_array( array( $dataModelSingleton[0], 'getInstance' ), array( &$_CB_database ) );
							$rows			=	call_user_func_array( array( $instance, $dataModelSingleton[1] ), array( $dataModelValue ) );
						} else {
							$rows			=	call_user_func_array( $dataModelSingleton, array( $dataModelValue ) );
						}
						$data				=	$rows[0];
					} else {
						$content			=	"Missing singleton class creator " . htmlspecialchars( $dataModelClass );
						trigger_error( $content, E_USER_WARNING );
						return $content;
					}
				}
			}
		} elseif ( $methodName && is_object( $this->_modelOfDataRows ) ) {
			$data							=	$this->_modelOfDataRows;
		} else {
			$content						=	"Missing private class or method attributes in xml: class: " . $dataModelClass . ", method: " . $methodName;
			return $content;
		}

		if ( is_object( $data ) ) {
			if ( method_exists( $data, $methodName ) ) {
				/*
								$row	=	$this->_modelOfData[0];				//TBD: checked....
								foreach (get_object_vars($data) as $key => $v) {
									if( substr( $key, 0, 1 ) != '_' ) {			// internal attributes of an object are ignored
										if (isset($row->$key)) {
											$data->$key = $row->$key;
										}
									}
								}
				*/
				$control_name_name	=	$this->control_name( $control_name, $name );
				$content			=	$data->$methodName( $value, $this->_pluginParams, $name, $node, $control_name, $control_name_name, $this->_view, $this->_modelOfData[0], $this->_modelOfDataRows, $this->_modelOfDataRowsNumber );	//TBD FIXME: pluginParams should be available by the method params() of $data, not as function parameter
			} else {
				$content			=	"Missing private xml method " . htmlspecialchars( $methodName );
			}
		} else {
			$content				=	"No data found !";
		}
		return $content;
	}

	/**
	 * Implements form group of params
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_group( /** @noinspection PhpUnusedParameterInspection */ $name, $value, $node, $control_name ) {
		global $_CB_framework;

		$formatting			=	$node->attributes( 'formatting' );

		if ( ! $formatting ) {
			$formatting		=	'span';
		}

		$tabs				=	new cbTabs( 0, $_CB_framework->getUi() );

		return $this->renderAllParams( $node, $control_name, $tabs, ( $this->_view ? 'view' : 'param' ), $formatting );
	}

	/**
	 * Parses list select2 usage
	 *
	 * @param  SimpleXMLElement  $node          The node describing the select
	 * @param  string            $control_name  The control name
	 * @param  string            $name          The name attribute
	 * @param  array             $options       The options of the select
	 * @param  string            $attributes    The list element attributes
	 * @param  array             $classes       The list element classes
	 * @param  boolean           $multiple      If multiple values can be selected
	 */
	protected function _list_select2( $node, $control_name, $name, &$options, &$attributes, &$classes, $multiple = false ) {
		$filterSelect			=	$node->attributes( 'filteringselect' );
		$select2				=	false;

		if ( $filterSelect === 'true' ) {
			$select2			=	true;
		} elseif ( $filterSelect !== 'false' ) {
			if ( $filterSelect && ( count( $options ) >= (int) $filterSelect ) ) {
				$select2		=	true;
			} elseif ( count( $options ) >= ( $multiple ? 30 : 15 ) ) {
				$select2		=	true;
			}
		}

		if ( $select2 ) {
			if ( $multiple ) {
				if ( isset( $options[0] ) && ( $options[0]->value == '' ) ) {
					// If the first value is the blank value then add it as the placeholder instead:
					$attributes	.=	' data-cbselect-placeholder="' . htmlspecialchars( $options[0]->text ) . '"';
				}

				$attributes		.=	' data-cbselect-close-on-select="false"';
			}

			$this->_jsselect2	=	true;

			// Add the cb specific select 2 class for jQuery binding:
			$classes[]			=	'cbSelect';
		}
	}

	/**
	 * Creates a translate conditioned list option
	 *
	 * @param  string       $translate  The translate attribute value ( "no": no translation, default: translate)
	 * @param  string       $value      The value of the option
	 * @param  null|string  $text       The label of the option
	 * @param  string       $valueName  The value variable name
	 * @param  string       $textName   The label variable name
	 * @param  null|string  $id         The id of the option
	 * @param  null|string  $class      The class of the option
	 * @return \stdClass                The moscomprofilerHTML::makeOption object
	 */
	protected function _list_make_option( $translate, $value, $text = null, $valueName = 'value', $textName = 'text', $id = null, $class = null ) {
		if ( $translate != 'no' ) {
			$text	=	CBTxt::T( $text );
		}

		return moscomprofilerHTML::makeOption( $value, $text, $valueName, $textName, $id, $class );
	}

	/**
	 * Parse list options and output the selected values
	 *
	 * @param  string              $name          The name of the form element
	 * @param  SimpleXMLElement    $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @param  SimpleXMLElement[]  $children      The child xml elements for the parameter
	 * @param  string|array        $value         The value of the element
	 * @param  array               $options       The base array of options to extend with parsed options
	 * @return array                              The array of selected options
	 */
	protected function _list_options_selected( $name, &$node, $control_name, $children, $value = null, $options = array() ) {
		$translate							=	$node->attributes( 'translate' );
		$selected							=	array();

		if ( $value !== null ) {
			foreach ( $options as $k => $v ) {
				$optTranslate				=	( isset( $options[$k]->translate ) ? $options[$k]->translate : $translate );

				if ( $optTranslate != 'no' ) {
					$options[$k]->text		=	CBTxt::T( $options[$k]->text );
				}
			}

			$this->_list_options( $name, $node, $control_name, $options, $children );

			if ( count( $options ) > 0 ) {
				if ( ! is_array( $value ) ) {
					$value					=	array( $value );
				}

				foreach ( $value as $v ) {
					if ( is_array( $v ) ) {
						$v					=	implode( '|*|', $v );
					}

					foreach ( $options as $option ) {
						if ( isset( $option->index ) ) {
							$val			=	$option->index;
						} else {
							$val			=	$option->value;
						}

						if ( ( ! is_array( $val ) ) && ( (string) $val === (string) $v ) ) {
							$selected[]		=	moscomprofilerHTML::makeOption( $val, $option->text );
						}
					}
				}
			}
		}

		return $selected;
	}

	/**
	 * Prepares data list options
	 *
	 * @param SimpleXMLElement  $node          The xml element for the parameter
	 * @param array             $options       The base array of options to extend with parsed options
	 * @param array             $data          The array of options to add
	 * @param boolean|null      $translate     NULL (default): Should we look for 'no' in $node attribut 'translate' or TRUE or FALSE
	 */
	protected function _list_options_data( $node, &$options, $data, $translate = null ) {
		if ( $data ) {
			$existingValues				=	array();
			$dataOptions				=	array();
			$start						=	null;

			if ( $options ) foreach ( $options as $key => $option ) {
				if ( is_array( $option->value ) ) {
					if ( $option->value[0] == 'optgroup' ) {
						$next			=	( $key + 1 );

						if ( isset( $options[$next] ) && is_array( $options[$next]->value ) && ( $options[$next]->value[0] == '/optgroup' ) ) {
							$start		=	$next;
						}
					}
				} else {
					$existingValues[]	=	$option->value;
				}
			}

			if ( $translate === null ) {
				$translate				=	$node->attributes( 'translate' );
			} else {
				$translate				=	$translate ? 'yes' : 'no';
			}

			foreach ( $data as $v ) {
				if ( is_object( $v ) ) {
					if ( isset( $v->index ) ) {
						$value			=	$v->index;
					} else {
						$value			=	$v->value;
					}

					$text				=	( $v->text !== '' ? $v->text : $value );
				} else {
					$value				=	$v;
					$text				=	$v;
				}

				if ( in_array( $value, $existingValues, true ) ) {
					// Skip duplicates that already exist in the list:
					continue;
				}

				$optTranslate			=	( isset( $v->translate ) ? $v->translate : $translate );

				$dataOptions[]			=	$this->_list_make_option( $optTranslate, (string) $value, $text );
			}

			if ( $start !== null ) {
				array_splice( $options, $start, 0, $dataOptions );
			} else {
				$options				=	array_merge( $options, $dataOptions );
			}
		}
	}

	/**
	 * Prepares list default option
	 *
	 * @param  SimpleXMLElement    $node          The xml element for the parameter
	 * @param  array               $options       The base array of options to extend with parsed options
	 * @param  null|string         $value         The value of the element
	 * @param  null|string[]       $defaults      The default value text pair
	 * @param  null|string         $default       The default value if no xml default is found
	 * @return string                             The default option value
	 */
	protected function _list_options_default( $node, &$options, $value = null, $defaults = array(), $default = '' ) {
		$translate				=	$node->attributes( 'translate' );
		$blankText				=	$node->attributes( 'blanktext' );

		if ( ( $blankText !== '' ) && ( ( ! in_array( $node->attributes( 'hideblanktext' ), array( 'true', 'always' ) ) ) || ( (string) $value === (string) $node->attributes( 'default' ) ) ) ) {
			if ( $blankText === null ) {
				if ( $defaults ) {
					$default	=	$defaults[0];

					array_unshift( $options, $this->_list_make_option( false, $default, $defaults[1] ) ); // Always push to the top no matter when default is added
				}
			} else {
				$default		=	(string) $node->attributes( 'default' );

				array_unshift( $options, $this->_list_make_option( $translate, $default, $blankText ) ); // Always push to the top no matter when default is added
			}
		}

		return $default;
	}

	/**
	 * Parse list options for options, optgroups, and evaluate IF conditions
	 *
	 * @param  string              $name          The name of the form element
	 * @param  SimpleXMLElement    $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @param  array               $options       The base array of options to extend with parsed options
	 * @param  SimpleXMLElement[]  $children      The child xml elements for the parameter
	 * @param  bool                $otgroups      If optgroups should be added to the options array
	 * @param  string              $value         The value of the element
	 * @param  bool                $ignoreClass   If option classes should be ignored or not
	 */
	protected function _list_options( $name, &$node, $control_name, &$options, $children, $otgroups = true, $value = null, $ignoreClass = false )
	{
		$this->_list_options_main( $name, $node, $control_name, $options, $children, $otgroups, $value, $ignoreClass );

		$selectValues			=	$this->_getSelectValues( $node );

		foreach ( $selectValues as $k => $optionValue ) {
			if ( isset( $optionValue->index ) ) {
				$val			=	$optionValue->index;
			} else {
				$val			=	$optionValue->value;
			}
			if ( ! is_array( $val ) ) {
				foreach ( $options as $option ) {
					if ( (string) $option->value == (string) $val ) {
						unset( $selectValues[$k] );
					}
				}
			}
		}
		$selectValues			=	array_values( $selectValues );

		$this->_list_options_data( $node, $options, $selectValues );
	}

	/**
	 * Parse list options for options, optgroups, and evaluate IF conditions
	 *
	 * @param  string              $name          The name of the form element
	 * @param  SimpleXMLElement    $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @param  array               $options       The base array of options to extend with parsed options
	 * @param  SimpleXMLElement[]  $children      The child xml elements for the parameter
	 * @param  bool                $otgroups      If optgroups should be added to the options array
	 * @param  string              $value         The value of the element
	 * @param  bool                $ignoreClass   If option classes should be ignored or not
	 */
	protected function _list_options_main( $name, &$node, $control_name, &$options, $children, $otgroups = true, $value = null, $ignoreClass = false ) {
		$translate												=	$node->attributes( 'translate' );

		if ( $children ) foreach ( $children as $option ) {
			if ( ( ! Access::authorised( $option ) ) && ( ! ( ( $option->getName() == 'if' ) && ( $option->attributes( 'type' ) == 'permission' ) ) ) ) {
				continue;
			}

			$optTranslate										=	( $option->attributes( 'translate' ) !== null ? $option->attributes( 'translate' ) : $translate );

			if ( $option->getName() == 'option' ) {
				if ( ( $option->attributes( 'index' ) !== '' ) && ( $option->attributes( 'index' ) !== null ) ) {
					$val										=	$option->attributes( 'index' );
				} else {
					$val										=	$option->attributes( 'value' );
				}

				if ( ( $option->attributes( 'selectable' ) != 'false' ) || ( (string) $val === (string) $value ) ) {
					$label										=	$option->data();
					$options[]									=	$this->_list_make_option( $optTranslate, $val, ( $label !== '' ? $label : $val ), 'value', 'text', null, ( $ignoreClass ? null : RegistryEditView::buildClasses( $option ) ) );
				}
			} elseif ( $otgroups && ( $option->getName() == 'optgroup' ) ) {
				if ( $optTranslate == 'no' ) {
					$label										=	$option->attributes( 'label' );
				} else {
					$label										=	CBTxt::T( $option->attributes( 'label' ) );
				}

				$options[]										=	moscomprofilerHTML::makeOptGroup( $label, 'value', 'text', null, ( $ignoreClass ? null : RegistryEditView::buildClasses( $option ) ) );

				$this->_list_options_main( $name, $node, $control_name, $options, $option->children(), $otgroups, $value, $ignoreClass );

				$options[]										=	moscomprofilerHTML::makeOptGroup( null );
			} elseif ( $option->getName() == 'data' ) {
				// TODO: Replace this usage with _getFieldValues (aslo needs upgrading to support private/custom below) once it is refactored into RegistryEditView:
				global $_CB_database;

				if ( $option->attributes( 'type' ) == 'private' ) {
					$privateOptions								=	$this->_form_private( $name, $value, $option, $control_name );

					if ( is_array( $privateOptions ) ) {
						$options								=	array_merge( $options, $privateOptions );
					}
				} elseif ( $option->attributes( 'type' ) == 'custom' ) {
					$customOptions								=	$this->_form_custom( $name, $value, $option, $control_name );

					if ( is_array( $customOptions ) ) {
						$options								=	array_merge( $options, $customOptions );
					}
				} elseif ( $option->attributes( 'dataprocessed' ) != 'true' ) {
					$dataTable									=	$option->attributes( 'table' );

					$xmlsql										=	new XmlQuery( $_CB_database, $dataTable, $this->_pluginParams );

					$xmlsql->setExternalDataTypeValues( 'modelofdata', $this->_modelOfData[0] );
					$xmlsql->process_orderby( $option->getElementByPath( 'orderby') );								// <data><orderby><field> fields
					$xmlsql->process_fields( $option->getElementByPath( 'rows') );									// <data><rows><field> fields
					$xmlsql->process_where( $option->getElementByPath( 'where') );									// <data><where><column> fields

					$groupby									=	$option->getElementByPath( 'groupby' );

					$xmlsql->process_groupby( ( $groupby !== false ? $groupby : 'value' ) );									// <data><groupby><field> fields

					$fieldValuesInDb							=	$xmlsql->queryLoadObjectsList( $option );	// get the records

					$rows										=	$option->getElementByPath( 'rows');			// check for type="firstwords"

					/** @var $rows SimpleXMLElement|null */
					if ( $rows ) {
						$textField								=	$rows->getChildByNameAttr( 'field', 'as', 'text' );

						/** @var $textField SimpleXMLElement|null */
						if ( $textField ) {
							if ( $textField->attributes( 'type' ) == 'firstwords' ) {
								$size							=	$textField->attributes( 'size' );

								if ( ! $size ) {
									$size						=	45;
								}

								foreach ( array_keys( $fieldValuesInDb ) as $k ) {
									$strippedContent			=	trim( $fieldValuesInDb[$k]->text );

									if ( cbIsoUtf_strlen( $strippedContent ) > $size ) {
										$strippedContent		=	cbIsoUtf_substr( $strippedContent, 0, $size ) . '...';
									}

									$fieldValuesInDb[$k]->text	=	$strippedContent;
								}
							}
						}
					}

					if ( $fieldValuesInDb ) {
						foreach ( array_keys( $fieldValuesInDb ) as $k ) {
							$dbOptTranslate						=	( isset( $fieldValuesInDb[$k]->translate ) ? $fieldValuesInDb[$k]->translate : $optTranslate );

							$options[]							=	$this->_list_make_option( $dbOptTranslate, $fieldValuesInDb[$k]->value, ( $fieldValuesInDb[$k]->text !== '' ? $fieldValuesInDb[$k]->text : $fieldValuesInDb[$k]->value ) );
						}
					}
				}
			} elseif ( $option->getName() == 'if' ) {
				if ( $option->attributes( 'type' ) == 'showhide' ) {
					$ifName										=	( $this->_htmlId( $control_name, $option ) . $option->attributes( 'operator' ) . $option->attributes( 'value' ) . $option->attributes( 'valuetype' ) );

					$this->_jsif[$ifName]['element']			=	$option;
					$this->_jsif[$ifName]['control_name']		=	$control_name;
					$this->_jsif[$ifName]['ifname']				=	$this->_htmlId( $control_name, $option );

					$ifOptions									=	array();

					$this->_list_options_main( $name, $node, $control_name, $ifOptions, $option->children(), $otgroups, $value, $ignoreClass );

					if ( $ifOptions ) foreach ( $ifOptions as $ifOption ) {
						if ( isset( $ifOption->id ) ) {
							$this->_jsif[$ifName]['show'][]		=	$ifOption->id;
						}
					}

					$options									=	array_merge( $options, $ifOptions );
				} else {
					if ( $this->checkParamCondition( $option ) ) {
						$then									=	$option->getChildByNameAttributes( 'then' );

						if ( $then ) {
							$insideParamToRender				=	$then;
						} else {
							$insideParamToRender				=	$option;
						}
					} else {
						$insideParamToRender					=	$option->getChildByNameAttributes( 'else' );
					}

					if ( $insideParamToRender ) {
						$this->_list_options_main( $name, $node, $control_name, $options, $insideParamToRender->children(), $otgroups, $value, $ignoreClass );
					}
				}
			}
		}
	}

	/**
	 * Implements form drop-down list of elements
	 *
	 * @param  string            $name          The name of the form element
	 * @param  string            $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string            $control_name  The control name
	 * @param  bool              $ignoreClass   Set if option class should be ignored (good for nested usage)
	 * @return string                           The html for the element
	 */
	protected function _form_list( $name, $value, $node, $control_name, $ignoreClass = false ) {
		$multi						=	( $node->attributes( 'multiple' ) == 'true' );

		if ( $multi ) {
			return $this->_form_multilist( $name, $value, $node, $control_name );
		}

		if ( $this->_view ) {
			$contentOptions			=	$this->_list_options_selected( $name, $node, $control_name, $node->children(), $value );

			if ( count( $contentOptions ) > 0 ) {
				$content			=	$contentOptions[0]->text;
			} else {
				$content			=	' - ';
			}

			return htmlspecialchars( $content );
		} else {
			$options				=	array();

			$this->_list_options_default( $node, $options, $value );
			$this->_list_options( $name, $node, $control_name, $options, $node->children(), true, $value, $ignoreClass );

			return $this->selectList( $options, $node, $control_name, $name, $value );
		}
	}

	/**
	 * Implements form multi-select list
	 *
	 * @param  string            $name          The name of the form element
	 * @param  string|array      $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string            $control_name  The control name
	 * @return string                           The html for the element
	 */
	protected function _form_multilist( $name, $value, $node, $control_name ) {
		$size							=	0;
		$cols							=	$node->attributes( 'cols' );
		$rows							=	$node->attributes( 'rows' );

		if ( $this->_view ) {
			if ( is_array( $value ) ) {
				$selected				=	$value;
			} else {
				if ( $value !== null ) {
					$selected			=	explode( '|*|', $value );
				} else {
					$selected			=	array();
				}
			}

			$contentOptions				=	$this->_list_options_selected( $name, $node, $control_name, $node->children(), $selected );
			$contentTexts				=	array();

			foreach ( $contentOptions as $contentOption ) {
				$contentTexts[]			=	htmlspecialchars( $contentOption->text );
			}

			if ( count( $contentTexts ) > 0 ) {
				if ( $cols || $rows ) {
					$content			=	moscomprofilerHTML::list2Table( $contentTexts, $cols, $rows, $size );
				} else {
					$content			=	implode( ', ', $contentTexts );
				}
			} else {
				$content				=	' - ';
			}

			return $content;
		} else {
			$options					=	array();

			if ( is_array( $value ) ) {
				$value					=	implode( '|*|', $value );
			}

			$defaults					=	array( '', '--- ' . CBTxt::T( 'Select (CTR/CMD-Click: Multiple)' ) . ' ---' );

			$this->_list_options_default( $node, $options, $value, $defaults );
			$this->_list_options( $name, $node, $control_name, $options, $node->children(), true, $value );

			$selected					=	explode( '|*|', (string) $value );

			return $this->selectList( $options, $node, $control_name, $name, $selected, true );
		}
	}

	/**
	 * Implements form field_show_only_if_selected (hidden drop-down, except if a value is selected)
	 *
	 * @param  string            $name          The name of the form element
	 * @param  string            $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string            $control_name  The control name
	 * @return string                           The html for the element
	 */
	protected function _form_field_show_only_if_selected( $name, $value, $node, $control_name ) {
		if ( ! $value ) {
			return null;
		}

		$size						=	0;
		$cols						=	$node->attributes( 'cols' );
		$rows						=	$node->attributes( 'rows' );
		$multi						=	( $node->attributes( 'multiple' ) == 'true' );
		$translate					=	$node->attributes( 'translate' );

		if ( $this->_view ) {
			if ( $value === null ) {
				$selected			=	array();
			} else {
				if ( $multi && ( ! is_array( $value ) ) ) {
					$selected		=	explode( '|*|', $value );
				} else {
					$selected		=	array( $value );
				}
			}

			$contentOptions			=	$this->_list_options_selected( $name, $node, $control_name, $node->children(), $selected );
			$contentTexts			=	array();

			foreach ( $contentOptions as $contentOption ) {
				$contentTexts[]		=	htmlspecialchars( $contentOption->text );
			}

			if ( count( $contentTexts ) > 0 ) {
				if ( $cols || $rows ) {
					$content		=	moscomprofilerHTML::list2Table( $contentTexts, $cols, $rows, $size );
				} else {
					$content		=	implode( ', ', $contentTexts );
				}
			} else {
				if ( $translate == 'no' ) {
					$content		=	htmlspecialchars( $value );
				} else {
					$content		=	htmlspecialchars( CBTxt::T( $value ) );
				}
			}

			return $content;
		} else {
			$options				=	array();

			if ( is_array( $value ) ) {
				$value				=	implode( '|*|', $value );
			}

			$this->_list_options_default( $node, $options, $value );
			$this->_list_options( $name, $node, $control_name, $options, $node->children(), true, $value );

			$exists					=	false;

			foreach ( $options as $option ) {
				if ( isset( $option->index ) ) {
					$val			=	$option->index;
				} else {
					$val			=	$option->value;
				}

				if ( ( ! is_array( $val ) ) && ( (string) $val === (string) $value ) ) {
					$exists			=	true;
					break;
				}
			}

			if ( ! $exists ) {
				$value				=	htmlspecialchars( $value );

				$options[]			=	$this->_list_make_option( $translate, $value, $value );
			}

			$selected				=	explode( '|*|', (string) $value );

			return $this->selectList( $options, $node, $control_name, $name, $selected, $multi );
		}
	}

	/**
	 * Implements a form data view
	 *
	 * @param  string            $name          The name of the form element
	 * @param  string            $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string            $control_name  The control name
	 * @return string                           The html for the element
	 */
	protected function _form_data( $name, $value, $node, $control_name ) {
		$size						=	0;
		$cols						=	$node->attributes( 'cols' );
		$rows						=	$node->attributes( 'rows' );
		$multi						=	( $node->attributes( 'multiple' ) == 'true' );

		if ( $this->_view ) {
			if ( $value === null ) {
				$selected			=	array();
			} else {
				if ( $multi && ( ! is_array( $value ) ) ) {
					$selected		=	explode( '|*|', $value );
				} else {
					$selected		=	array( $value );
				}
			}

			$contentOptions			=	$this->_list_options_selected( $name, $node, $control_name, $node->children(), $selected, $this->_getSelectValues( $node ) );
			$contentTexts			=	array();

			foreach ( $contentOptions as $contentOption ) {
				$contentTexts[]		=	htmlspecialchars( $contentOption->text );
			}

			if ( count( $contentTexts ) > 0 ) {
				if ( $cols || $rows ) {
					$content		=	moscomprofilerHTML::list2Table( $contentTexts, $cols, $rows, $size );
				} else {
					$content		=	implode( ', ', $contentTexts );
				}
			} else {
				$content			=	' - ';
			}

			return $content;
		} else {
			$options				=	array();

			if ( is_array( $value ) ) {
				$value				=	implode( '|*|', $value );
			}

			$default				=	$this->_list_options_default( $node, $options, $value );

			$this->_list_options( $name, $node, $control_name, $options, $node->children(), true, $value );
			$selected				=	explode( '|*|', (string) $value );

			return $this->selectList( $options, $node, $control_name, $name, $selected, $multi );
		}
	}

	/**
	 * Implements a form tablefield
	 *
	 * @param  string            $name          The name of the form element
	 * @param  string            $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string            $control_name  The control name
	 * @return string                           The html for the element
	 */
	protected function _form_tablefield( $name, $value, $node, $control_name ) {
		return $this->_form_data( $name, $value, $node, $control_name );
	}

	/**
	 * Implements a form radio buttons
	 *
	 * @param  string            $name          The name of the form element
	 * @param  string            $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string            $control_name  The control name
	 * @return string                           The html for the element
	 */
	protected function _form_radio( $name, $value, $node, $control_name ) {
		$size							=	$node->attributes( 'size' );
		$cols							=	$node->attributes( 'cols' );
		$rows							=	$node->attributes( 'rows' );

		if ( $this->_view ) {
			$contentOptions				=	$this->_list_options_selected( $name, $node, $control_name, $node->children(), $value );

			if ( count( $contentOptions ) > 0 ) {
				$content				=	$contentOptions[0]->text;
			} else {
				$content				=	' - ';
			}

			return htmlspecialchars( $content );
		} else {
			$options					=	array();

			$this->_list_options( $name, $node, $control_name, $options, $node->children(), false, $value );

			$validate					=	$node->attributes( 'validate' );

			if ( $validate && in_array( 'required', explode( ',', $validate ) ) ) {
				$required				=	1;
			} else {
				$required				=	0;
			}

			$attributes					=	null;
			$tooltip					=	$this->getTooltipAttr( $node );

			if ( $node->attributes( 'buttons' ) == 'true' ) {
				$btnAttributes			=	null;

				if ( $this->_isrepeat ) {
					$repeatDefault		=	$node->attributes( 'default' );

					if ( $node->attributes( 'translate' ) != 'no' ) {
						$repeatDefault	=	CBTxt::T( $repeatDefault );
					}

					if ( $repeatDefault ) {
						$btnAttributes	.=	' data-cbrepeat-default="' . htmlspecialchars( $repeatDefault ) . '"';
					}
				}

				return moscomprofilerHTML::radioListButtons( $options, $this->control_name( $control_name, $name ), $tooltip, 'value', 'text', $value, $required, null, $btnAttributes, false );
			}

			if ( $this->_isrepeat ) {
				$repeatDefault			=	$node->attributes( 'default' );

				if ( $node->attributes( 'translate' ) != 'no' ) {
					$repeatDefault		=	CBTxt::T( $repeatDefault );
				}

				if ( $repeatDefault ) {
					$attributes			.=	' data-cbrepeat-default="' . htmlspecialchars( $repeatDefault ) . '"';
				}
			}

			return moscomprofilerHTML::radioListTable( $options, $this->control_name( $control_name, $name ), $attributes, 'value', 'text', $value, $cols, $rows, $size, $required, null, $tooltip, false );
		}
	}

	/**
	 * Implements a form checkboxes
	 *
	 * @param  string            $name          The name of the form element
	 * @param  string            $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string            $control_name  The control name
	 * @return string                           The html for the element
	 */
	protected function _form_checkbox( $name, $value, $node, $control_name ) {
		$size							=	$node->attributes( 'size' );
		$cols							=	$node->attributes( 'cols' );
		$rows							=	$node->attributes( 'rows' );

		if ( $this->_view ) {
			if ( is_array( $value ) ) {
				$selected				=	$value;
			} else {
				if ( $value !== null ) {
					$selected			=	explode( '|*|', $value );
				} else {
					$selected			=	array();
				}
			}

			$contentOptions				=	$this->_list_options_selected( $name, $node, $control_name, $node->children(), $selected );
			$contentTexts				=	array();

			foreach ( $contentOptions as $contentOption ) {
				$contentTexts[]			=	htmlspecialchars( $contentOption->text );
			}

			if ( count( $contentTexts ) > 0 ) {
				if ( $cols || $rows ) {
					$content			=	moscomprofilerHTML::list2Table( $contentTexts, $cols, $rows, $size );
				} else {
					$content			=	implode( ', ', $contentTexts );
				}
			} else {
				$content				=	' - ';
			}

			return $content;
		} else {
			$options					=	array();

			if ( is_array( $value ) ) {
				$value					=	implode( '|*|', $value );
			}

			$this->_list_options( $name, $node, $control_name, $options, $node->children(), false, $value );

			$selected					=	explode( '|*|', (string) $value );
			$validate					=	$node->attributes( 'validate' );

			if ( $validate && in_array( 'required', explode( ',', $validate ) ) ) {
				$required				=	1;
			} else {
				$required				=	0;
			}

			$attributes					=	null;
			$tooltip					=	$this->getTooltipAttr( $node );

			if ( $node->attributes( 'buttons' ) == 'true' ) {
				$btnAttributes			=	null;

				if ( $this->_isrepeat ) {
					$repeatDefault		=	$node->attributes( 'default' );

					if ( $node->attributes( 'translate' ) != 'no' ) {
						$repeatDefault	=	CBTxt::T( $repeatDefault );
					}

					if ( $repeatDefault ) {
						$btnAttributes	.=	' data-cbrepeat-default="' . htmlspecialchars( $repeatDefault ) . '"';
					}
				}

				return moscomprofilerHTML::checkboxListButtons( $options, $this->control_name( $control_name, $name ) . '[]', $tooltip, 'value', 'text', $selected, $required, null, $btnAttributes, false );
			}

			if ( $this->_isrepeat ) {
				$repeatDefault			=	$node->attributes( 'default' );

				if ( $node->attributes( 'translate' ) != 'no' ) {
					$repeatDefault		=	CBTxt::T( $repeatDefault );
				}

				if ( $repeatDefault ) {
					$attributes			.=	' data-cbrepeat-default="' . htmlspecialchars( $repeatDefault ) . '"';
				}
			}

			return moscomprofilerHTML::checkboxListTable( $options, $this->control_name( $control_name, $name ) . '[]', $attributes, 'value', 'text', $selected, $cols, $rows, $size, $required, array( 'm-0' ), $tooltip, false );
		}
	}

	/**
	 * Internal function for multilists
	 *
	 * @param  string            $name            The name of the form element
	 * @param  string            $value           The value of the element
	 * @param  SimpleXMLElement  $node            The xml element for the parameter
	 * @param  string            $control_name    The control name
	 * @param  string            $query           The query to perform to get the list elements
	 * @param  string[]          $defaultDefault  The default value if there is no attribute 'blanktext'
	 * @param  boolean           $multiSelect     If multiple selections are allowed
	 * @param  int               $limit           Maximum number of results
	 * @return string The html for the element
	 */
	protected function _form_multilist_internal( $name, $value, $node, $control_name, $query, $defaultDefault, $multiSelect, $limit = 0 ) {
		global $_CB_database;

		$size								=	0;
		$cols								=	$node->attributes( 'cols' );
		$rows								=	$node->attributes( 'rows' );
		$translate							=	$node->attributes( 'translate' );

		if ( $this->_view ) {
			if ( $value === null ) {
				$selected					=	array();
			} else {
				if ( $multiSelect && ( ! is_array( $value ) ) ) {
					$selected				=	explode( '|*|', $value );
				} else {
					$selected				=	array( $value );
				}
			}

			$contentOptions					=	$this->_list_options_selected( $name, $node, $control_name, $node->children(), $selected );
			$contentTexts					=	array();

			foreach ( $contentOptions as $contentOption ) {
				$contentTexts[]				=	htmlspecialchars( $contentOption->text );
			}

			if ( $query ) {
				static $contentCache		=	array();

				$cacheId					=	md5( $query );

				if ( ! isset( $contentCache[$cacheId] ) ) {
					$_CB_database->setQuery( $query );

					$contentCache[$cacheId]	=	$_CB_database->loadResultArray();
				}

				$queryContents				=	$contentCache[$cacheId];

				foreach ( $queryContents as $v ) {
					if ( $translate == 'no' ) {
						$contentTexts[]		=	htmlspecialchars( $v );
					} else {
						$contentTexts[]		=	htmlspecialchars( CBTxt::T( $v ) );
					}
				}
			}

			if ( count( $contentTexts ) > 0 ) {
				if ( $cols || $rows ) {
					$content				=	moscomprofilerHTML::list2Table( $contentTexts, $cols, $rows, $size );
				} else {
					$content				=	implode( ', ', $contentTexts );
				}
			} else {
				$content					=	' - ';
			}

			return $content;
		} else {
			static $optionCache				=	array();

			$cacheId						=	md5( $query );

			$options						=	array();

			if ( is_array( $value ) ) {
				$value						=	implode( '|*|', $value );
			}

			$defaults						=	array( (string) $defaultDefault[0], $defaultDefault[1] );

			$this->_list_options_default( $node, $options, $value, $defaults );
			$this->_list_options( $name, $node, $control_name, $options, $node->children(), true, $value );

			if ( ! isset( $optionCache[$cacheId] ) ) {
				$_CB_database->setQuery( $query, 0, (int) $limit );

				$optionCache[$cacheId]		=	$_CB_database->loadObjectList();
			}

			$sqlOptions						=	$optionCache[$cacheId];

			if ( ( $node->attributes( 'type' ) == 'field' ) && ( $node->attributes( 'key' ) == 'tablecolumns' ) ) {
				foreach ( $sqlOptions as $k => $sqlOption ) {
					$tableColumns					=	explode( ',', $sqlOption->value );

					if ( count( $tableColumns ) <= 1 ) {
						continue;
					}

					foreach ( $tableColumns as $tableColumn ) {
						$cOpt						=	clone $sqlOption;
						$cOpt->value				=	$tableColumn;

						if ( in_array( $node->attributes( 'title' ), array( 'name', 'tablecolumns' ) ) ) {
							$cOpt->text				=	$tableColumn;
						} else {
							if ( in_array( $node->attributes( 'title' ), array( 'title', 'description' ) ) ) {
								$cOpt->text			=	CBTxt::T( $sqlOption->text ) . ' (' . $tableColumn . ')';
								$cOpt->translate	=	false;
							} else {
								$cOpt->text			=	( $sqlOption->text != $tableColumn ? $sqlOption->text . ' (' . $tableColumn . ')' : $sqlOption->text );
							}
						}

						$sqlOptions[]				=	$cOpt;
					}

					unset( $sqlOptions[$k] );
				}

				$sqlOptions							=	array_values( $sqlOptions );
			}

			$this->_list_options_data( $node, $options, $sqlOptions );

			$selected						=	explode( '|*|', (string) $value );

			return $this->selectList( $options, $node, $control_name, $name, $selected, $multiSelect );
		}
	}

	/**
	 * Implements a simple form list_sql type
	 * <param type="list_sql" table="#__cbsubs_plans" key="id" title="alias" multiple="true" blanktext="Select Product" hideblanktext="true" />
	 * <param type="list_sql" table="#__cbsubs_plans" key="id" filterkey="item_type" filtervalue="usersubscription" title="alias" multiple="true" blanktext="Select Product" hideblanktext="true" />
	 *
	 * @param  string            $name          The name of the form element
	 * @param  string            $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string            $control_name  The control name
	 * @return string                           The html for the element
	 */
	protected function _form_list_sql( $name, $value, $node, $control_name ) {
		global $_CB_database;

		$multi						=	( $node->attributes( 'multiple' ) == 'true' );
		$table						=	$node->attributes( 'table' );
		$key						=	$node->attributes( 'key' );
		$keytype					=	$node->attributes( 'keytype' );

		if ( ! $keytype ) {
			$keytype				=	'sql:int';
		}

		$title						=	$node->attributes( 'title' );
		$order						=	$node->attributes( 'order' );
		$default					=	$node->attributes( 'default' );

		if ( $order === null ) {
			$order					=	$title;
		}

		if ( $this->_view ) {
			if ( $value === null ) {
				$selected			=	array();
			} else {
				if ( $multi && ( ! is_array( $value ) ) ) {
					$selected		=	explode( '|*|', $value );
				} else {
					$selected		=	array( $value );
				}
			}

			if ( count( $selected ) > 0 ) {
				foreach ( $selected as $k => $v ) {
					$selected[$k]	=	XmlTypeCleanQuote::sqlCleanQuote( $v, $keytype, $this->_pluginParams, $_CB_database );
				}

				$query				=	"SELECT " . $_CB_database->NameQuote( $title )
									.	"\n FROM " . $_CB_database->NameQuote( $table )
									.	"\n WHERE " . $_CB_database->NameQuote( $key ) . ( ( count( $selected ) == 1 ) ? ( " = " . $selected[0] ) : ( " IN (" . implode( ',', $selected ) . ")" ) )
									.	"\n ORDER BY " . $_CB_database->NameQuote( $order );
			} else {
				$query				=	null;
			}
		} else {
			$filterkey					=	$node->attributes( 'filterkey' );
			$filtervalue				=	$node->attributes( 'filtervalue' );

			$query					=	"SELECT " . $_CB_database->NameQuote( $key ) . ' AS value'
									.	', ' . $_CB_database->NameQuote( $title ) . ' AS text'
									.	"\n FROM " . $_CB_database->NameQuote( $table )
									.	( $filterkey && $filtervalue ? "\n WHERE " . $_CB_database->NameQuote( $filterkey ) . " = " . $_CB_database->Quote( $filtervalue ) : null )
									.	"\n ORDER BY " . $_CB_database->NameQuote( $order );
		}

		$defaultDefault				=	array( $default === null ? '' : (string) $default, '--- ' . sprintf( $multi ? CBTxt::T( 'Select %s (CTR/CMD-Click: Multiple)' ) : CBTxt::T( 'Select %s' ), $node->attributes( 'label' ) ) . ' ---' );

		return $this->_form_multilist_internal( $name, $value, $node, $control_name, $query, $defaultDefault, $multi );
	}

	/**
	 * Implements a simple sql query type
	 * <param name="fieldid" type="sql" mode="show">
	 *     <data name="fieldid" type="sql:field" table="#__comprofiler_fields" key="name" value="cb_company" valuetype="sql:string" />
	 * OR:
	 * <param name="fieldid" type="sql" mode="show">
	 *     <data name="fieldid" type="sql:field" table="#__comprofiler_fields">
	 *         <rows>
	 *             <field name="fieldid" type="sql:int" />
	 *         </rows>
	 *         <where>
	 *             <column name="name" operator="=" value="cb_company" type="sql:field" valuetype="const:string" />
	 *         </where>
	 *     </data>
	 * @param  string            $name          The name of the form element
	 * @param  string            $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string            $control_name  The control name
	 * @return string                           The html for the element
	 */
	protected function _form_sql( /** @noinspection PhpUnusedParameterInspection */ $name, $value, $node, $control_name ) {
		global $_CB_database;

		if ( $this->_view ) {
			$xmlsql = new XmlQuery( $_CB_database, null, $this->_pluginParams );
			$xmlsql->setExternalDataTypeValues( 'modelofdata', $this->_modelOfData[0] );
			$xmlsql->process_data( $node->getElementByPath( 'data' ) );

			return htmlspecialchars( $xmlsql->queryloadResult() );
		}

		return null;
	}

	/**
	 * Implements a section selector for Joomla 1.5- and Mambo
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 * @deprecated 2.0.0
	 */
	protected function _form_mos_section( $name, $value, $node, $control_name ) {
		return $this->_form_mos_category( $name, $value, $node, $control_name );
	}

	/**
	 * Implements form category for categories of Joomla
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_mos_category( $name, $value, $node, $control_name ) {
		global $_CB_database;

		$key						=	$node->attributes( 'key' );

		if ( $key ) {
			$keytype				=	$node->attributes( 'keytype' );
		} else {
			$key					=	'id';
		}

		if ( ! isset( $keytype ) ) {
			$keytype				=	'sql:int';
		}

		$title						=	$node->attributes( 'title' );
		$multi						=	( $node->attributes( 'multiple' ) == 'true' );

		if ( $this->_view ) {
			if ( $value === null ) {
				$selected			=	array();
			} else {
				if ( $multi && ( ! is_array( $value ) ) ) {
					$selected		=	explode( '|*|', $value );
				} else {
					$selected		=	array( $value );
				}
			}

			if ( count( $selected ) > 0 ) {
				foreach ( $selected as $k => $v ) {
					$selected[$k]	=	XmlTypeCleanQuote::sqlCleanQuote( $v, $keytype, $this->_pluginParams, $_CB_database );
				}

				if ( $title ) {
					$query			=	"SELECT a." . $_CB_database->NameQuote( $title );
				} else {
					$query			=	"SELECT IF( a." . $_CB_database->NameQuote( 'level' ) . " = 0, a." . $_CB_database->NameQuote( 'title' ) . ", CONCAT( REPEAT( '- ', a." . $_CB_database->NameQuote( 'level' ) . " ), a." . $_CB_database->NameQuote( 'title' ) . " ) ) AS text";
				}

				$query				.=	"\n FROM " . $_CB_database->NameQuote( '#__categories' ) . " AS a"
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__categories' ) . " AS p"
									.	" ON p." . $_CB_database->NameQuote( 'id' ) . " = a." . $_CB_database->NameQuote( 'parent_id' )
									.	"\n WHERE a." . $_CB_database->NameQuote( 'extension' ) . " = " . $_CB_database->Quote( 'com_content' )
									.	"\n AND a." . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	"\n AND a." . $_CB_database->NameQuote( $key ) . ( ( count( $selected ) == 1 ) ? ( " = " . $selected[0] ) : ( " IN (" . implode( ',', $selected ) . ")" ) )
									.	"\n ORDER BY a." . $_CB_database->NameQuote( 'lft' ) . " ASC";
			} else {
				$query				=	null;
			}
		} else {
			$query					=	"SELECT a." . $_CB_database->NameQuote( $key ) . " AS value";

			if ( $title ) {
				$query				.=	", a." . $_CB_database->NameQuote( $title ) . " AS text";
			} else {
				$query				.=	", IF( a." . $_CB_database->NameQuote( 'level' ) . " = 0, a." . $_CB_database->NameQuote( 'title' ) . ", CONCAT( REPEAT( '- ', a." . $_CB_database->NameQuote( 'level' ) . " ), a." . $_CB_database->NameQuote( 'title' ) . " ) ) AS text";
			}

			$query					.=	"\n FROM " . $_CB_database->NameQuote( '#__categories' ) . " AS a"
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__categories' ) . " AS p"
									.	" ON p." . $_CB_database->NameQuote( 'id' ) . " = a." . $_CB_database->NameQuote( 'parent_id' )
									.	"\n WHERE a." . $_CB_database->NameQuote( 'extension' ) . " = " . $_CB_database->Quote( 'com_content' )
									.	"\n AND a." . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	"\n ORDER BY a." . $_CB_database->NameQuote( 'lft' ) . " ASC";
		}

		$defaultDefault				=	array( '', '--- ' . ( $multi ? CBTxt::T( 'Select Content Categories (CTR/CMD-Click: Multiple)' ) : CBTxt::T( 'Select Content Category' ) ) . ' ---' );

		return $this->_form_multilist_internal( $name, $value, $node, $control_name, $query, $defaultDefault, $multi );
	}

	/**
	 * Implements joomla content selection field
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_mos_content( $name, $value, $node, $control_name ) {
		global $_CB_database;

		$key						=	$node->attributes( 'key' );

		if ( $key ) {
			$keytype				=	$node->attributes( 'keytype' );
		} else {
			$key					=	'id';
		}

		if ( ! isset( $keytype ) ) {
			$keytype				=	'sql:int';
		}

		$title						=	$node->attributes( 'title' );
		$multi						=	( $node->attributes( 'multiple' ) == 'true' );
		$limit						=	$node->attributes( 'limit' );

		if ( $limit === null ) {
			$limit					=	9999;
		}

		if ( $this->_view ) {
			if ( $value === null ) {
				$selected			=	array();
			} else {
				if ( $multi && ( ! is_array( $value ) ) ) {
					$selected		=	explode( '|*|', $value );
				} else {
					$selected		=	array( $value );
				}
			}

			if ( count( $selected ) > 0 ) {
				foreach ( $selected as $k => $v ) {
					$selected[$k]	=	XmlTypeCleanQuote::sqlCleanQuote( $v, $keytype, $this->_pluginParams, $_CB_database );
				}

				if ( $title ) {
					$query			=	"SELECT a." . $_CB_database->NameQuote( $title );
				} else {
					$query			=	"SELECT CONCAT_WS( '/', s." . $_CB_database->NameQuote( 'title' ) . ", c." . $_CB_database->NameQuote( 'title' ) . ", a." . $_CB_database->NameQuote( 'title' ) . " ) AS text";
				}

				$query				.=	"\n FROM " . $_CB_database->NameQuote( '#__content' ) . " AS a"
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__categories' ) . " AS c"
									.	" ON c." . $_CB_database->NameQuote( 'id' ) . " = a." . $_CB_database->NameQuote( 'catid' )
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__categories' ) . " AS s"
									.	" ON s." . $_CB_database->NameQuote( 'id' ) . " = c." . $_CB_database->NameQuote( 'parent_id' )
									.	"\n WHERE c." . $_CB_database->NameQuote( 'extension' ) . " = " . $_CB_database->Quote( 'com_content' )
									.	"\n AND c." . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	"\n AND s." . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	"\n AND a." . $_CB_database->NameQuote( $key ) . ( ( count( $selected ) == 1 ) ? ( " = " . $selected[0] ) : ( " IN (" . implode( ',', $selected ) . ")" ) )
									.	"\n ORDER BY s." . $_CB_database->NameQuote( 'title' ) . ", c." . $_CB_database->NameQuote( 'title' ) . ", a." . $_CB_database->NameQuote( 'title' );
			} else {
				$query				=	null;
			}
		} else {
			$query					=	"SELECT a." . $_CB_database->NameQuote( $key ) . " AS value";

			if ( $title ) {
				$query				.=	", a." . $_CB_database->NameQuote( $title ) . " AS text";
			} else {
				$query				.=	", CONCAT_WS( '/', s." . $_CB_database->NameQuote( 'title' ) . ", c." . $_CB_database->NameQuote( 'title' ) . ", a." . $_CB_database->NameQuote( 'title' ) . " ) AS text";
			}

			$query					.=	"\n FROM " . $_CB_database->NameQuote( '#__content' ) . " AS a"
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__categories' ) . " AS c"
									.	" ON c." . $_CB_database->NameQuote( 'id' ) . " = a." . $_CB_database->NameQuote( 'catid' )
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__categories' ) . " AS s"
									.	" ON s." . $_CB_database->NameQuote( 'id' ) . " = c." . $_CB_database->NameQuote( 'parent_id' )
									.	"\n WHERE c." . $_CB_database->NameQuote( 'extension' ) . " = " . $_CB_database->Quote( 'com_content' )
									.	"\n AND c." . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	"\n AND s." . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	"\n ORDER BY s." . $_CB_database->NameQuote( 'title' ) . ", c." . $_CB_database->NameQuote( 'title' ) . ", a." . $_CB_database->NameQuote( 'title' );
		}

		$defaultDefault				=	array( '', '--- ' . ( $multi ? CBTxt::T( 'Select Content Articles (CTR/CMD-Click: Multiple)' ) : CBTxt::T( 'Select Content Article' ) ) . ' ---' );

		return $this->_form_multilist_internal( $name, $value, $node, $control_name, $query, $defaultDefault, $multi, $limit );
	}

	/**
	 * Implements a form for CB field type
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_field( $name, $value, $node, $control_name ) {
		$multi	=	( $node->attributes( 'multiple' ) == 'true' );

		return $this->_form_multifield( $name, $value, $node, $control_name, $multi );
	}

	/**
	 * Implements a form for CB fields (multiple) multifield
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @param  boolean             $multi         Is it a multi-valued field?
	 * @return string                             The html for the element
	 */
	protected function _form_multifield( $name, $value, $node, $control_name, $multi = true ) {
		global $_CB_database;

		$key						=	$node->attributes( 'key' );

		if ( $key ) {
			$keytype				=	$node->attributes( 'keytype' );

			if ( ! $keytype )  {
				switch ( $key ) {
					case 'name';
					case 'tablecolumns';
					case 'table';
					case 'title';
					case 'description';
					case 'type';
					case 'value';
					case 'default';
					case 'cssclass';
						$keytype	=	'sql:string';
						break;
				}
			}
		} else {
			$key					=	'fieldid';
		}

		if ( ! isset( $keytype ) ) {
			$keytype				=	'sql:int';
		}

		$title						=	$node->attributes( 'title' );

		if ( ! $title ) {
			$title					=	'name';
		}

		$where						=	array();
		$where[]					=	"f." . $_CB_database->NameQuote( 'published' ) . " = 1";
		$where[]					=	"f." . $_CB_database->NameQuote( 'name' ) . " != " . $_CB_database->Quote( 'NA' );

		if ( $node->attributes( 'registration' ) == 'true' ) {
			$where[]				=	"f." . $_CB_database->NameQuote( 'registration' ) . " > 0";
		}

		if ( $node->attributes( 'profile' ) == 'true' ) {
			$where[]				=	"f." . $_CB_database->NameQuote( 'profile' ) . " > 0";
		}

		if ( $node->attributes( 'edit' ) == 'true' ) {
			$where[]				=	"f." . $_CB_database->NameQuote( 'edit' ) . " > 0";
		}

		if ( $node->attributes( 'searchable' ) == 'true' ) {
			$where[]				=	"f." . $_CB_database->NameQuote( 'searchable' ) . " = 1";
		}

		if ( $node->attributes( 'required' ) == 'true' ) {
			$where[]				=	"f." . $_CB_database->NameQuote( 'required' ) . " = 1";
		}

		if ( $node->attributes( 'readonly' ) == 'true' ) {
			$where[]				=	"f." . $_CB_database->NameQuote( 'readonly' ) . " = 1";
		}

		if ( $node->attributes( 'storable' ) == 'true' ) {
			$where[]				=	"f." . $_CB_database->NameQuote( 'tablecolumns' ) . " != " . $_CB_database->Quote( '' );
		}

		if ( $key == 'fieldid' ) {
			$value = $this->fieldArrayOrStringNameToId( $value, $_CB_database );
		}

		if ( $this->_view ) {
			if ( $value === null ) {
				$selected			=	array();
			} else {
				if ( $multi && ( ! is_array( $value ) ) ) {
					$selected		=	explode( '|*|', $value );
				} else {
					$selected		=	array( $value );
				}
			}

			if ( count( $selected ) > 0 ) {
				foreach ( $selected as $k => $v ) {
					$selected[$k]	=	XmlTypeCleanQuote::sqlCleanQuote( $v, $keytype, $this->_pluginParams, $_CB_database );
				}

				$where[]			=	"f." . $_CB_database->NameQuote( $key ) . ( ( count( $selected ) == 1 ) ? ( " = " . $selected[0] ) : ( " IN (" . implode( ',', $selected ) . ")" ) );

				$query				=	"SELECT f." . $_CB_database->NameQuote( $title )
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_fields' ) . " AS f"
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_tabs' ) . " AS t"
									.	" ON t." . $_CB_database->NameQuote( 'tabid' ) . " = f." . $_CB_database->NameQuote( 'tabid' )
									.	"\n WHERE " . implode( "\n AND ", $where )
									.	"\n ORDER BY t." . $_CB_database->NameQuote( 'position' ) . ", t." . $_CB_database->NameQuote( 'ordering' ) . ", f." . $_CB_database->NameQuote( 'ordering' );
			} else {
				$query				=	null;
			}
		} else {
			$query					=	"SELECT f." . $_CB_database->NameQuote( $key ) . " AS value"
									.	", f." . $_CB_database->NameQuote( $title ) . " AS text"
									.	( ! in_array( $title, array( 'title', 'description' ) ) ? ", " . $_CB_database->Quote( 'no' ) . " AS translate" : null )
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_fields' ) . " AS f"
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_tabs' ) . " AS t"
									.	" ON t." . $_CB_database->NameQuote( 'tabid' ) . " = f." . $_CB_database->NameQuote( 'tabid' )
									.	"\n WHERE " . implode( "\n AND ", $where )
									.	"\n ORDER BY t." . $_CB_database->NameQuote( 'position' ) . ", t." . $_CB_database->NameQuote( 'ordering' ) . ", f." . $_CB_database->NameQuote( 'ordering' );
		}

		$defaultDefault				=	array( '', '--- ' . ( $multi ? CBTxt::T( 'Select Fields (CTR/CMD-Click: Multiple)' ) : CBTxt::T( 'Select Field' ) ) . ' ---' );

		return $this->_form_multilist_internal( $name, $value, $node, $control_name, $query, $defaultDefault, $multi );
	}

	/**
	 * Converts non-numeric value(s) to numeric ones, otherwise return null
	 *
	 * @param  string|array  $value
	 * @param  DatabaseDriverInterface   $_CB_database
	 * @return string|array|null
	 */
	private function fieldArrayOrStringNameToId( $value, $_CB_database )
	{
		if ( ! $value ) {
			return null;
		}

		if ( strpos( $value, '|*|' ) !== false ) {
			$value		=	explode( '|*|', $value );
		}

		if ( ! is_array( $value ) ) {
			return $this->fieldNameToId( $value, $_CB_database );
		}

		$ids			=	array();

		foreach ( $value as $v ) {
			if ( ! $v ) {
				continue;
			}

			$id			=	$this->fieldNameToId( $v, $_CB_database );

			if ( $id ) {
				$ids[]	=	$id;
			}
		}

		return $ids;
	}

	/**
	 * Converts non-numeric value to numeric, otherwise return null
	 *
	 * @param  string       $value
	 * @param  DatabaseDriverInterface  $_CB_database
	 * @return string|null
	 */
	private function fieldNameToId( $value, $_CB_database )
	{
		if ( ! $value ) {
			return null;
		}

		if ( is_numeric( $value ) ) {
			return $value;
		}

		static $fieldNameIdMap		=	array();

		if ( ! isset( $fieldNameIdMap[$value] ) ) {
			$_CB_database->setQuery( "SELECT f." . $_CB_database->NameQuote( 'fieldid' )
				.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_fields' ) . " AS f"
				.	"\n WHERE f." . $_CB_database->NameQuote( 'name' ) . ' = ' . $_CB_database->Quote( $value ) );

			$fieldNameIdMap[$value]	=	$_CB_database->loadResult();
		}

		return $fieldNameIdMap[$value];
	}

	/**
	 * Implements a form for CB tab
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_tab( $name, $value, $node, $control_name ) {
		global $_CB_database;

		$key						=	$node->attributes( 'key' );

		if ( $key ) {
			$keytype				=	$node->attributes( 'keytype' );
		} else {
			$key					=	'tabid';
		}

		if ( ! isset( $keytype ) ) {
			$keytype				=	'sql:int';
		}

		$title						=	$node->attributes( 'title' );

		if ( ! $title ) {
			$title					=	'title';
		}

		$multi						=	( $node->attributes( 'multiple' ) == 'true' );

		if ( $this->_view ) {
			if ( $value === null ) {
				$selected			=	array();
			} else {
				if ( $multi && ( ! is_array( $value ) ) ) {
					$selected		=	explode( '|*|', $value );
				} else {
					$selected		=	array( $value );
				}
			}

			if ( count( $selected ) > 0 ) {
				foreach ( $selected as $k => $v ) {
					$selected[$k]	=	XmlTypeCleanQuote::sqlCleanQuote( $v, $keytype, $this->_pluginParams, $_CB_database );
				}

				$query				=	"SELECT " . $_CB_database->NameQuote( $title )
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_tabs' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'enabled' ) . " = 1"
									.	"\n AND " . $_CB_database->NameQuote( $key ) . ( ( count( $selected ) == 1 ) ? ( " = " . $selected[0] ) : ( " IN (" . implode( ',', $selected ) . ")" ) )
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'position' ) . ", " . $_CB_database->NameQuote( 'ordering' );
			} else {
				$query				=	null;
			}
		} else {
			$query					=	"SELECT " . $_CB_database->NameQuote( $key ) . " AS value"
									.	", " . $_CB_database->NameQuote( $title ) . " AS text"
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_tabs' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'enabled' ) . " = 1"
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'position' ) . ", " . $_CB_database->NameQuote( 'ordering' );
		}

		$defaultDefault				=	array( '', '--- ' . ( $multi ? CBTxt::T( 'Select Tabs (CTR/CMD-Click: Multiple)' ) : CBTxt::T( 'Select Tab' ) ) . ' ---' );

		return $this->_form_multilist_internal( $name, $value, $node, $control_name, $query, $defaultDefault, $multi );
	}

	/**
	 * Implements a form field for Joomla component selection
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_mos_component( $name, $value, $node, $control_name ) {
		global $_CB_database;

		$key						=	$node->attributes( 'key' );

		if ( $key ) {
			$keytype				=	$node->attributes( 'keytype' );
		} else {
			$key					=	'element';
		}

		if ( ! isset( $keytype ) ) {
			$keytype				=	'sql:string';
		}

		$title						=	$node->attributes( 'title' );

		if ( ! $title ) {
			$title					=	'name';
		}

		$multi						=	( $node->attributes( 'multiple' ) == 'true' );

		if ( $this->_view ) {
			if ( $value === null ) {
				$selected			=	array();
			} else {
				if ( $multi && ( ! is_array( $value ) ) ) {
					$selected		=	explode( '|*|', $value );
				} else {
					$selected		=	array( $value );
				}
			}

			if ( count( $selected ) > 0 ) {
				foreach ( $selected as $k => $v ) {
					$selected[$k]	=	XmlTypeCleanQuote::sqlCleanQuote( $v, $keytype, $this->_pluginParams, $_CB_database );
				}

				$query				=	"SELECT " . $_CB_database->NameQuote( $title )
									.	"\n FROM " . $_CB_database->NameQuote( '#__extensions' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'component' )
									.	"\n AND " . $_CB_database->NameQuote( 'enabled' ) . " = 1"
									.	"\n AND ( ( " . $_CB_database->NameQuote( 'client_id' ) . " = 0 ) OR ( " . $_CB_database->NameQuote( 'protected' ) . " = 0 ) )"
									.	"\n AND " . $_CB_database->NameQuote( $key ) . ( ( count( $selected ) == 1 ) ? ( " = " . $selected[0] ) : ( " IN (" . implode( ',', $selected ) . ")" ) )
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'name' );
			} else {
				$query				=	null;
			}
		} else {
			$query					=	"SELECT " . $_CB_database->NameQuote( $key ) . " AS value"
									.	", " . $_CB_database->NameQuote( $title ) . " AS text"
									.	"\n FROM " . $_CB_database->NameQuote( '#__extensions' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'component' )
									.	"\n AND " . $_CB_database->NameQuote( 'enabled' ) . " = 1"
									.	"\n AND ( ( " . $_CB_database->NameQuote( 'client_id' ) . " = 0 ) OR ( " . $_CB_database->NameQuote( 'protected' ) . " = 0 ) )"
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'name' );
		}

		$defaultDefault				=	array( '', '--- ' . ( $multi ? CBTxt::T( 'Select Components (CTR/CMD-Click: Multiple)' ) : CBTxt::T( 'Select Component' ) ) . ' ---' );

		return $this->_form_multilist_internal( $name, $value, $node, $control_name, $query, $defaultDefault, $multi );
	}

	/**
	 * Implements a form field for Joomla module selection
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_mos_module( $name, $value, $node, $control_name ) {
		global $_CB_database;

		$key						=	$node->attributes( 'key' );

		if ( $key ) {
			$keytype				=	$node->attributes( 'keytype' );
		} else {
			$key					=	'id';
		}

		if ( ! isset( $keytype ) ) {
			$keytype				=	'sql:int';
		}

		$title						=	$node->attributes( 'title' );
		$multi						=	( $node->attributes( 'multiple' ) == 'true' );

		if ( $this->_view ) {
			if ( $value === null ) {
				$selected			=	array();
			} else {
				if ( $multi && ( ! is_array( $value ) ) ) {
					$selected		=	explode( '|*|', $value );
				} else {
					$selected		=	array( $value );
				}
			}

			if ( count( $selected ) > 0 ) {
				foreach ( $selected as $k => $v ) {
					$selected[$k]	=	XmlTypeCleanQuote::sqlCleanQuote( $v, $keytype, $this->_pluginParams, $_CB_database );
				}

				if ( $title ) {
					$query			=	"SELECT " . $_CB_database->NameQuote( $title );
				} else {
					$query			=	"SELECT CONCAT_WS( '', " . $_CB_database->NameQuote( 'title' ) . ", ' (', " . $_CB_database->NameQuote( 'position' ) . ", ')' ) AS text";
				}

				$query				.=	"\n FROM " . $_CB_database->NameQuote( '#__modules' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'client_id' ) . " = 0"
									.	"\n AND " . $_CB_database->NameQuote( 'position' ) . " <> ''"
									.	"\n AND " . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	"\n AND " . $_CB_database->NameQuote( $key ) . ( ( count( $selected ) == 1 ) ? ( " = " . $selected[0] ) : ( " IN (" . implode( ',', $selected ) . ")" ) )
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'position' ) . ", " . $_CB_database->NameQuote( 'ordering' );
			} else {
				$query				=	null;
			}
		} else {
			$query					=	"SELECT " . $_CB_database->NameQuote( $key ) . " AS value";

			if ( $title ) {
				$query				.=	", " . $_CB_database->NameQuote( $title ) . " AS text";
			} else {
				$query				.=	", CONCAT_WS( '', " . $_CB_database->NameQuote( 'title' ) . ", ' (', " . $_CB_database->NameQuote( 'position' ) . ", ')' ) AS text";
			}

			$query					.=	"\n FROM " . $_CB_database->NameQuote( '#__modules' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'client_id' ) . " = 0"
									.	"\n AND " . $_CB_database->NameQuote( 'position' ) . " <> ''"
									.	"\n AND " . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'position' ) . ", " . $_CB_database->NameQuote( 'ordering' );
		}

		$defaultDefault				=	array( '', '--- ' . ( $multi ? CBTxt::T( 'Select Modules (CTR/CMD-Click: Multiple)' ) : CBTxt::T( 'Select Module' ) ) . ' ---' );

		return $this->_form_multilist_internal( $name, $value, $node, $control_name, $query, $defaultDefault, $multi );
	}

	/**
	 * Implements a form field for Joomla menu (not menu items) selection
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_mos_menu( $name, $value, $node, $control_name ) {
		if ( $this->_view ) {
			return htmlspecialchars( $value );
		} else {
			$menuTypes		=	$this->_form_mos_menu__menutypes();
			foreach($menuTypes as $menutype ) {
				$options[]	=	moscomprofilerHTML::makeOption( $menutype, $menutype );
			}
			array_unshift( $options, moscomprofilerHTML::makeOption( '', '--- ' . CBTxt::T("Select Menu") . ' ---' ) );

			// return moscomprofilerHTML::selectList( $options, ''. $this->control_name( $control_name, $name ) . '', 'class="inputbox"', 'value', 'text', $value, 2 );
			return $this->selectList( $options, $node, $control_name, $name, $value );

		}
	}

	/**
	 * Internal method to get all menu types
	 *
	 * @return string[]
	 */
	protected function _form_mos_menu__menutypes() {
		global $_CB_database;

		$query		=	"SELECT params"
			.	"\n FROM #__modules"
			.	"\n WHERE module = 'mod_mainmenu'"
			//.	"\n ORDER BY title"
		;
		$_CB_database->setQuery( $query	);
		$modMenus	=	$_CB_database->loadObjectList();

		$query		=	"SELECT menutype"
			.	"\n FROM #__menu"
			.	"\n GROUP BY menutype"
			//.	"\n ORDER BY menutype"
		;
		$_CB_database->setQuery( $query	);
		$menuMenus	=	$_CB_database->loadResultArray();

		$menuTypes	=	array();

		foreach ( $modMenus as $modMenu ) {
			$modParams 		=	new Registry( $modMenu->params );
			$menuType 		=	$modParams->get( 'menutype' );
			if ( ! $menuType ) {
				$menuType	=	'mainmenu';
			}
			if ( ! in_array( $menuType, $menuTypes ) ) {
				$menuTypes[] =	$menuType;
			}
		}

		foreach ( $menuMenus as $menuType ) {
			if ( ! in_array( $menuType, $menuTypes ) ) {
				$menuTypes[] =	$menuType;
			}
		}

		asort( $menuTypes );
		return $menuTypes;				//FIXME: this seems quite broken! (but as unused it's ok for now)
	}

	/**
	 * Implements form field for menu items selection
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_mos_menu_item( $name, $value, $node, $control_name ) {
		global $_CB_database;

		$key						=	$node->attributes( 'key' );

		if ( $key ) {
			$keytype				=	$node->attributes( 'keytype' );
		} else {
			$key					=	'id';
		}

		if ( ! isset( $keytype ) ) {
			$keytype				=	'sql:int';
		}

		$title						=	$node->attributes( 'title' );
		$multi						=	( $node->attributes( 'multiple' ) == 'true' );

		if ( $this->_view ) {
			if ( $value === null ) {
				$selected			=	array();
			} else {
				if ( $multi && ( ! is_array( $value ) ) ) {
					$selected		=	explode( '|*|', $value );
				} else {
					$selected		=	array( $value );
				}
			}

			if ( count( $selected ) > 0 ) {
				foreach ( $selected as $k => $v ) {
					$selected[$k]	=	XmlTypeCleanQuote::sqlCleanQuote( $v, $keytype, $this->_pluginParams, $_CB_database );
				}

				if ( $title ) {
					$query			=	"SELECT " . $_CB_database->NameQuote( $title );
				} else {
					$query			=	"SELECT CONCAT_WS( '/', " . $_CB_database->NameQuote( 'menutype' ) . ", " . $_CB_database->NameQuote( 'title' ) . " ) AS text";
				}

				$query				.=	"\n FROM " . $_CB_database->NameQuote( '#__menu' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'client_id' ) . " = 0"
									.	"\n AND " . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	"\n AND " . $_CB_database->NameQuote( 'id' ) . " > 1"
									.	"\n AND " . $_CB_database->NameQuote( $key ) . ( ( count( $selected ) == 1 ) ? ( " = " . $selected[0] ) : ( " IN (" . implode( ',', $selected ) . ")" ) )
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'lft' ) . " ASC";
			} else {
				$query				=	null;
			}
		} else {
			$query					=	"SELECT " . $_CB_database->NameQuote( $key ) . " AS value";

			if ( $title ) {
				$query				.=	", " . $_CB_database->NameQuote( $title ) . " AS text";
			} else {
				$query				.=	", CONCAT_WS( '/', " . $_CB_database->NameQuote( 'menutype' ) . ", " . $_CB_database->NameQuote( 'title' ) . " ) AS text";
			}

			$query					.=	"\n FROM " . $_CB_database->NameQuote( '#__menu' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'client_id' ) . " = 0"
									.	"\n AND " . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	"\n AND " . $_CB_database->NameQuote( 'id' ) . " > 1"
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'lft' ) . " ASC";
		}

		$defaultDefault				=	array( '', '--- ' . ( $multi ? CBTxt::T( 'Select Menu items (CTR/CMD-Click: Multiple)' ) : CBTxt::T( 'Select Menu Item' ) ) . ' ---' );

		return $this->_form_multilist_internal( $name, $value, $node, $control_name, $query, $defaultDefault, $multi );
	}

	/**
	 * Implements for field for selecting an image from a list
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_imagelist( $name, $value, $node, $control_name ) {
		return $this->_form_filelist( $name, $value, $node, $control_name );
	}

	/**
	 * Implements a form field to select a folder from a list
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_filelist( $name, $value, $node, $control_name ) {
		global $_CB_framework;

		$size							=	0;
		$cols							=	$node->attributes( 'cols' );
		$rows							=	$node->attributes( 'rows' );
		$multi							=	( $node->attributes( 'multiple' ) == 'true' );
		$translate						=	$node->attributes( 'translate' );

		if ( $this->_view ) {
			if ( $value === null ) {
				$selected				=	array();
			} else {
				if ( $multi && ( ! is_array( $value ) ) ) {
					$selected			=	explode( '|*|', $value );
				} else {
					$selected			=	array( $value );
				}
			}

			$contentOptions				=	$this->_list_options_selected( $name, $node, $control_name, $node->children(), $selected );
			$contentTexts				=	array();
			$contentValues				=	array();

			foreach ( $contentOptions as $contentOption ) {
				$contentValues[]		=	$contentOption->value;
				$contentTexts[]			=	htmlspecialchars( $contentOption->text );
			}

			foreach ( $selected as $v ) {
				if ( ! in_array( $v, $contentValues ) ) {
					if ( $translate == 'no' ) {
						$contentTexts[]	=	htmlspecialchars( $v );
					} else {
						$contentTexts[]	=	htmlspecialchars( CBTxt::T( $v ) );
					}
				}
			}

			if ( count( $contentTexts ) > 0 ) {
				if ( $cols || $rows ) {
					$content			=	moscomprofilerHTML::list2Table( $contentTexts, $cols, $rows, $size );
				} else {
					$content			=	implode( ', ', $contentTexts );
				}
			} else {
				$content				=	' - ';
			}

			return $content;
		} else {
			$type						=	$node->attributes( 'type' );
			$directory					=	$node->attributes( 'directory' );
			$recurse					=	( $node->attributes( 'recurse' ) == 'true' );

			if ( $type == 'folderlist' ) {
				$filter					=	'^[^.]+$';
			} elseif ( $type == 'imagelist' ) {
				$filter					=	'\.png$|\.gif$|\.jpg$|\.bmp$|\.ico$|\.svg$';
			} else {
				$filter					=	$node->attributes( 'filter' );

				if ( ! $filter ) {
					$filter				=	'.';
				}
			}

			$this->substituteName( $directory, false );

			$path						=	( $directory && ( $directory[0] == '/' ) ? $_CB_framework->getCfg( 'absolute_path' ) . $directory : $directory );
			$files						=	cbReadDirectory( $path, $filter, $recurse );
			$options					=	array();

			if ( is_array( $value ) ) {
				$value					=	implode( '|*|', $value );
			}

			$defaults					=	array();

			if ( $type == 'imagelist' ) {
				if ( ! $node->attributes( 'hide_default' ) ) {
					$defaults			=	array( '', '--- ' . CBTxt::T( 'Use Default image' ) . ' ---' );
				}

				if ( ! $node->attributes( 'hide_none' ) ) {
					$options[]			=	moscomprofilerHTML::makeOption( '-1', '--- ' . CBTxt::T( 'Do not use an image' ) . ' ---' );
				}
			}

			$this->_list_options_default( $node, $options, $value, $defaults );
			$this->_list_options( $name, $node, $control_name, $options, $node->children(), true, $value );
			$this->_list_options_data( $node, $options, $files );

			$selected					=	explode( '|*|', (string) $value );

			return $this->selectList( $options, $node, $control_name, $name, $selected, $multi );
		}
	}

	/**
	 * Implements a form field to select a folder from a list
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_folderlist( $name, $value, $node, $control_name ) {
		return $this->_form_filelist( $name, $value, $node, $control_name );
	}

	/**
	 * Implements a form field to determine if a folder exists and is writable
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_writable( /** @noinspection PhpUnusedParameterInspection */ $name, $value, $node, $control_name ) {
		global $_CB_framework;

		$path			=	$_CB_framework->getCfg( 'absolute_path' ) . '/' . $node->attributes( 'directory' );

		if ( ! file_exists( $path ) ) {
			$class		=	'text-muted';
			$status		=	CBTxt::T( 'Does Not Exist' );
		} elseif ( ! is_writable( $path ) ) {
			$class		=	'text-danger';
			$status		=	CBTxt::T( 'Not Writeable' );
		} else {
			$class		=	'text-success';
			$status		=	CBTxt::T( 'Writeable' );
		}

		return '<div class="' . $class . '">' . $path . ' <small>(' . $status . ')</small></div>';
	}

	/**
	 * Implements a form textarea
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_textarea( $name, $value, $node, $control_name )
	{
		$value		=	(string) $value;

		if ( $this->_view ) {
			return '<code>' . str_replace( array( "\n", "\r", '  ' ), array( "<br />", "<br />", '&nbsp;&nbsp;' ), htmlspecialchars( $value ) ) . '</code>';
		} else {
			$rows 	= $node->attributes( 'rows' );
			$cols 	= $node->attributes( 'cols' );
			if ( $rows == '' ) {
				$rows	=	4;
			}
			if ( $cols == '' ) {
				$cols	=	40;
			}
			$classes	=	RegistryEditView::buildClasses( $node, array( 'form-control' ) );
			return $this->_todom( 'textarea', $node, $control_name, $name, $value, $classes ,'cols="'. $cols .'" rows="'. $rows .'"' );
		}
	}

	/**
	 * Implements a form color
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_color( $name, $value, $node, $control_name )
	{
		$value		=	(string) $value;

		if ( $this->_view ) {
			if ( ( ! $value ) || ( ! preg_match( '/^#[0-9A-Fa-f]{3,6}$/i', $value ) ) ) {
				return null;
			}

			return	'<span class="d-inline-block border cbColorField">'
				.		'<div class="pl-5 pr-5 pt-3 pb-3 cbColorFieldSample" style="background-color: ' . htmlspecialchars( $value ) . ';"></div>'
				.		'<div class="border-top text-center user-select-all cbColorFieldColor">' . htmlspecialchars( $value ) . '</div>'
				.	'</span>';
		}

		return $this->_todom( 'input', $node, $control_name, $name, $value, self::buildClasses( $node, array( 'form-control' ) ), 'type="color"' );
	}

	/**
	 * Implements a form htmlarea
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_htmlarea( $name, $value, $node, $control_name ) {
		global $_CB_framework;
		if ( $this->_view ) {
			return htmlspecialchars( $value );
		} else {
			$width 				= $node->attributes( 'width' );
			$height 			= $node->attributes( 'height' );
			$rows 				= $node->attributes( 'rows' );
			$cols 				= $node->attributes( 'cols' );
			if ( $width == '' ) {
				$width			=	700;
			}
			if ( $height == '' ) {
				$height			=	350;
			}
			if ( $rows == '' ) {
				$rows			=	4;
			}
			if ( $cols == '' ) {
				$cols			=	40;
			}
			$editorDivId		=	$this->control_id( $control_name, $name );
			$editorFieldName	=	$this->control_name( $control_name, $name );

			$content			=	'<div id="cbdiv_' . $editorDivId . '" class="clearfix">'
				.	str_replace( '<'.'textarea', '<textarea' . $this->_title( $node ), Application::Cms()->displayCmsEditor( $editorFieldName, $value, $width, $height, $cols, $rows ) )
				.	"</div>\n"
			;

			return $this->getTooltipAttr( $node, 'style="display:block;"', $content );
			// return '<textarea name="'. $this->control_name( $control_name, $name ) . '" cols="'. $cols .'" rows="'. $rows .'" class="text_area" id="' . $this->control_id( $control_name, $name ) . '">'. htmlspecialchars($value) .'</textarea>';
			// return $this->_todom( 'htmlarea', $node, $control_name, $name, $value, 'cols="'. $cols .'" rows="'. $rows .'" class="text_area"' );
			// return '<' . $tag . ' ' . $text . ' name="'. $this->control_name( $control_name, $name ) . '" id="'. $this->control_id( $control_name, $name ) . '" value="'. htmlspecialchars($value) .'"' . $this->_title( $node ) . ' />';
		}
	}

	/**
	 * Implements an upload file input field
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_uploadfile( $name, $value, $node, $control_name )
	{
		$value				=	(string) $value;

		if ( $this->_view ) {
			return htmlspecialchars( $value );
		} else {
			$size			=	$node->attributes( 'size' );
			$siz			=	( $size ? ' size="' . (int) $size . '"' : null );
			$classes		=	RegistryEditView::buildClasses( $node, array( 'form-control' ) );
			return $this->_todom( 'input', $node, $control_name, $name, $value, $classes, 'type="file"' . $siz );
		}
	}

	/**
	 * Implements a submit button
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement    $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_button( $name, $value, $node, $control_name )
	{
		$value				=	(string) $value;

		$translate			=	$node->attributes( 'translate' );
		$task				=	$node->attributes( 'task' );
		$link				=	$node->attributes( 'link' );
		$targetIsBlank		=	in_array( $node->attributes( 'target' ), array( 'popup', '_blank' ) );
		$message			=	$node->attributes( 'message' );

		if ( $translate != 'no' ) {
			$value			=	CBTxt::T( $value );
		}

		if ( $link ) {
			$url			=	$this->_controllerView->drawUrl( $link, $node, $this->_modelOfData[0], $this->_modelOfData[0]->get( 'id' ) );

			if ( ! $url ) {
				return null;
			}
		} else {
			$url			=	null;
		}

		if ( $url ) {
			$type			=	'button';

			if ( cbStartOfStringMatch( $url, 'javascript:' ) ) {
				$href		=	'#';
				$onClick	=	substr( $url, 11 );
			} else {
				$href		=	$url;
				$onClick	=	null;
			}
		} else {
			if ( $task ) {
				$type		=	'button';
				$href		=	'#';
				$onClick	=	"submitbutton( '" . addslashes( $task ) . "' )";
			} else {
				$type		=	'submit';
				$href		=	null;
				$onClick	=	null;
			}
		}

		$classes			=	RegistryEditView::buildClasses( $node );

		if ( ( ! $onClick ) && ( $href[0] == '#' ) ) {
			$attributes		=	' value="' . htmlspecialchars( $href ) . '"';
		} else {
			if ( $onClick || $href ) {
				$js			=	( $onClick ? $onClick
										   : ( $targetIsBlank ? "window.open('" . addslashes( str_replace( '&amp;', '&', htmlspecialchars( $href ) ) ) . "', 'cbbuttonpopup" . md5( $href ) . "', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1020,height=640,directories=no,location=no');"
															  : "location.href='" . addslashes( str_replace( '&amp;', '&', htmlspecialchars( $href ) ) ) . "'" ) );

				if ( $message ) {
					 $js	=	'cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( $message ) ) . '\' ).done( function() { ' . $js . '; })';
				}

				$attributes	=	' onclick="' . $js . '"';
			} else {
				$attributes	=	null;
			}
		}

		return $this->_todom( 'button', $node, $control_name, $name, $value, $classes, 'type="' . $type . '"' . $attributes );
	}

	/**
	 * Implements a form spacer
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_spacer( $name, $value, $node, $control_name ) {
		if ( $value ) {
			$cssclass	=	RegistryEditView::buildClasses( $node, array(), 'inline' );
			$translate	=	$node->attributes( 'translate' );
			$id			=	$this->control_id( $control_name, $name );
			if ( $id ) {
				$id		=	' id="' . $id . '"';
			}
			if ( $translate != 'no' ) {
				$value	=	CBTxt::Th( $value );
			}
			if ( $cssclass ) {
				return '<span class="' . htmlspecialchars( $cssclass ) . '"' . $id . '>'.$value.'</span>';
			} else {
				return '<strong' . $id . '>'.$value.'</strong>';
			}
		} else {
			return '<hr />';
		}
	}

	/**
	 * Implements a form usergroup
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_usergroup( $name, $value, $node, $control_name ) {
		global $_CB_framework;

		static $texts						=	array();

		$size								=	0;
		$cols								=	$node->attributes( 'cols' );
		$rows								=	$node->attributes( 'rows' );
		$multi								=	( $node->attributes( 'multiple' ) == 'true' );

		if ( $this->_view ) {
			if ( $value === null ) {
				$selected					=	array();
			} else {
				if ( $multi && ( ! is_array( $value ) ) ) {
					$selected				=	explode( '|*|', $value );
				} else {
					$selected				=	array( $value );
				}
			}
			// remap literal groups (such as in default values) to the hardcoded CMS values:
			$selected							=	$_CB_framework->acl->mapGroupNamesToValues( $selected );
			foreach ( $selected as $k => $v ) {
				$selected[$k]					=	(string) $v;	// CB lists require strings to compare to values with ===
			}

			$contentOptions					=	$this->_list_options_selected( $name, $node, $control_name, $node->children(), $selected );
			$contentTexts					=	array();
			$contentValues					=	array();

			foreach ( $contentOptions as $contentOption ) {
				$contentValues[]			=	$contentOption->value;
				$contentTexts[]				=	htmlspecialchars( $contentOption->text );
			}

			foreach ( $selected as $v ) {
				if ( ! in_array( $v, $contentValues ) ) {
					if ( ! isset( $texts[$v] ) ) {
						if ( (int) $v == 0 ) {
							$texts[$v]		=	'-';
						} else {
							$texts[$v]		=	Application::CmsPermissions()->getGroupName( (int) $v );
						}
					}

					$text					=	$texts[$v];

					if ( $text ) {
						switch ( $v ) {
							case -2:
							case 1:
								$class		=	'text-success';
								break;
							case 6:
							case 7:
							case 8:
								$class		=	'text-danger';
								break;
							case 0:
								$class		=	'';
								break;
							default:
								$class		=	'text-warning';
								break;
						}

						$contentTexts[]		=	'<span class="' . $class . '">' . htmlspecialchars( $text ) . '</span>';
					}
				}
			}

			if ( count( $contentTexts ) > 0 ) {
				if ( $cols || $rows ) {
					$content				=	moscomprofilerHTML::list2Table( $contentTexts, $cols, $rows, $size );
				} else {
					$content				=	implode( ', ', $contentTexts );
				}
			} else {
				$content					=	' - ';
			}

			return $content;
		} else {
			$options						=	array();

			if ( ! is_array( $value ) ) {
				$value						=	explode( '|*|', (string) $value );
			}

			// remap literal groups (such as in default values) to the hardcoded CMS values:
			$value							=	$_CB_framework->acl->mapGroupNamesToValues( $value );
			foreach ( $value as $k => $v ) {
				$value[$k]					=	(string) $v;	// CB lists require strings to compare to values with ===
			}

			$value							=	implode( '|*|', $value );

			$defaults						=	array( '', '--- ' . ( $multi ? CBTxt::T( 'Select User Group (CTR/CMD-Click: Multiple)' ) : CBTxt::T( 'Select User Group' ) ) . ' ---' );

			$this->_list_options_default( $node, $options, $value, $defaults );
			$this->_list_options( $name, $node, $control_name, $options, $node->children(), true, $value );

			$hideChoices					=	trim( (string) $node->attributes( 'hidechoices' ) );
			$sqlOptions						=	cbGetAllUsergroupsBelowMe();

			if ( $hideChoices !== '' ) {
				$choicesNo					=	explode( ',', $hideChoices );

				foreach ( $choicesNo as $choice ) {
					foreach ( $sqlOptions as $k => $opt ) {
						if ( (string) $opt->text === (string) $choice ) {
							unset ( $sqlOptions[$k] );
							break;
						}
					}
				}
			}

			$this->_list_options_data( $node, $options, $sqlOptions, false );

			$selected						=	explode( '|*|', $value );

			if ( $node->attributes( 'managegroups' ) != 'false' ) {
				$htmlManageLevels			=	' &nbsp; <a target="_blank" class="cbAdminSmallLink" href="' . htmlspecialchars( 'index.php?option=com_users&view=groups' ) . '">' . CBTxt::Th( 'Manage User Groups' ) . '</a>';
			} else {
				$htmlManageLevels			=	'';
			}

			return $this->selectList( $options, $node, $control_name, $name, $selected, $multi, false ) . $htmlManageLevels;
		}
	}

	/**
	 * Implements a form viewaccesslevel selection
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_viewaccesslevel( $name, $value, $node, $control_name ) {
		$size								=	0;
		$cols								=	$node->attributes( 'cols' );
		$rows								=	$node->attributes( 'rows' );
		$multi								=	( $node->attributes( 'multiple' ) == 'true' );

		if ( $value === null ) {
			$selected						=	array();
		} else {
			if ( $multi && ( ! is_array( $value ) ) ) {
				$selected					=	explode( '|*|', $value );
			} else {
				$selected					=	array( $value );
			}
		}

		if ( $this->_view ) {
			$allAccessLevels				=	Application::CmsPermissions()->getAllViewAccessLevels();

			$contentOptions					=	$this->_list_options_selected( $name, $node, $control_name, $node->children(), $selected );
			$contentTexts					=	array();
			$contentValues					=	array();

			foreach ( $contentOptions as $contentOption ) {
				$contentValues[]			=	$contentOption->value;
				$contentTexts[]				=	htmlspecialchars( $contentOption->text );
			}

			foreach ( $selected as $v ) {
				if ( ( ! in_array( $v, $contentValues ) ) && isset( $allAccessLevels[$v] ) ) {
					$text					=	$allAccessLevels[$v];

					switch ( $v ) {
						case 1:
							$class			=	'text-success';
							break;
						case 2:
							$class			=	'text-warning';
							break;
						case 3:
							$class			=	'text-secondary';
							break;
						default:
							$class			=	'text-primary';
							break;
					}

					$contentTexts[]			=	'<span class="' . $class . '">' . htmlspecialchars( $text ) . '</span>';
				}
			}

			if ( count( $contentTexts ) > 0 ) {
				if ( $cols || $rows ) {
					$content				=	moscomprofilerHTML::list2Table( $contentTexts, $cols, $rows, $size );
				} else {
					$content				=	implode( ', ', $contentTexts );
				}
			} else {
				$content					=	' - ';
			}

			return $content;
		} else {
			$options						=	array();

			if ( is_array( $value ) ) {
				$value						=	implode( '|*|', $value );
			}

			$defaults						=	array( '', '--- ' . ( $multi ? CBTxt::T( 'Select View Access Level (CTR/CMD-Click: Multiple)' ) : CBTxt::T( 'Select View Access Level' ) ) . ' ---' );

			$this->_list_options_default( $node, $options, $value, $defaults );
			$this->_list_options( $name, $node, $control_name, $options, $node->children(), true, $value );

			$hideChoices					=	trim( (string) $node->attributes( 'hidechoices' ) );

			// All View Access Levels:
			$sqlOptions						=	Application::CmsPermissions()->getAllViewAccessLevels( true );

			// View Access Levels that I can see:
			$myAccessTree					=	Application::CmsPermissions()->getAllViewAccessLevels( false, Application::MyUser() );

			foreach ( $sqlOptions as $k => $opt ) {
				if ( ! ( isset( $myAccessTree[$opt->value] ) || in_array( $opt->value, $selected ) ) ) {
					// Remove options which are not accessible by me, but keep them if they are already selected to not loose them, e.g. if permissions changed:
					unset ( $sqlOptions[$k] );
					break;
				}
			}

			if ( $hideChoices !== '' ) {
				$choicesNo					=	explode( ',', $hideChoices );

				foreach ( $choicesNo as $choice ) {
					foreach ( $sqlOptions as $k => $opt ) {
						if ( (string) $opt->text === (string) $choice ) {
							unset ( $sqlOptions[$k] );
							break;
						}
					}
				}
			}

			$this->_list_options_data( $node, $options, $sqlOptions, false );

			$selected						=	explode( '|*|', (string) $value );

			if ( $node->attributes( 'managelevels' ) != 'false' ) {
				$htmlManageLevels			=	' &nbsp; <a target="_blank" class="cbAdminSmallLink" href="' . htmlspecialchars( 'index.php?option=com_users&view=levels' ) . '">' . CBTxt::Th( 'Manage View Access Levels' ) . '</a>';
			} else {
				$htmlManageLevels			=	'';
			}

			return $this->selectList( $options, $node, $control_name, $name, $selected, $multi, false ) . $htmlManageLevels;
		}
	}

	/**
	 * Implements a form rownumber field (read-only)
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_rownumber( /** @noinspection PhpUnusedParameterInspection */	$name, $value, $node, $control_name ) {
		$content	=	$this->_controllerView->pageNav->getRowNumber( $this->_modelOfDataRowsNumber );
		return $content;
	}

	/**
	 * Implements a form primary-checkbox (for elements selections in lists)
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_primarycheckbox( $name, $value, $node, $control_name ) {
		$content	=	'<input type="checkbox" id="' . $this->_controllerView->fieldId( 'id', $this->_modelOfDataRowsNumber )			//TBD hardcoded column of index id
			.	'" name="' . $this->_controllerView->fieldName( 'idcid[]' )
			.	'" value="' . htmlspecialchars( $value ) . '" class="m-0" />';
		$content	.=	$this->_form_hidden( $name, $value, $node, $control_name );
		return $content;
	}

	/**
	 * Implements a form checkmark
	 *
	 * @param  string              $name                The name of the form element
	 * @param  string              $value               The value of the element
	 * @param  SimpleXMLElement    $node                The xml element for the parameter
	 * @param  string              $control_name        The control name
	 * @param  string              $defaultTask         The task used for toggleable item
	 * @param  string              $defaultTitle        The title to display when item is not toggleable
	 * @param  string              $defaultToggleTitle  The title displayed when item is toggleable
	 * @return string                                   The html for the element
	 */
	protected function _form_checkmark( $name, $value, $node, $control_name, $defaultTask = null, $defaultTitle = null, $defaultToggleTitle = null ) {
		if ( $this->_view ) {
			$isToggle								=	( $node->attributes( 'onclick' ) == 'toggle' );
			$checkmarkTask							=	( $defaultTask ? $defaultTask : ( $value ? 'disable/' . $name : 'enable/' . $name ) );
			$checkmarkTitle							=	null;
			$checkmarkToggleTitle					=	null;
			$classes								=	null;
			$iconClass								=	null;
			$iconSize								=	null;
			$textClass								=	null;

			if ( $node->getChildByNameAttributes( 'option' ) ) {
				$valueNode							=	$node->getAnyChildByNameAttr( 'option', 'index', $value );

				if ( ! $valueNode ) {
					$valueNode						=	$node->getAnyChildByNameAttr( 'option', 'value', $value );
				}

				if ( $valueNode ) {
					$checkmarkTitle					=	CBTxt::T( $valueNode->data() );
					$classes						=	RegistryEditView::buildClasses( $valueNode, array(), 'inline' );
					$iconClass						=	$valueNode->attributes( 'iconclass' );
					$iconSize						=	$valueNode->attributes( 'iconsize' );
					$textClass						=	$valueNode->attributes( 'textclass' );

					if ( $isToggle ) {
						$nextOption					=	$valueNode->xpath( '(./following-sibling::option[not(@selectable="false" or @toggleable="false")])[1]' );

						if ( ! $nextOption ) {
							$nextOption				=	$valueNode->xpath( '(../child::option[not(@selectable="false" or @toggleable="false")])[1]' );
						}

						if ( $nextOption ) {
							$checkmarkToggleTitle	=	CBTxt::T( $nextOption[0]->attributes( 'title' ) );
							$checkmarkToggleValue	=	$nextOption[0]->attributes( 'index' );

							if ( ! $checkmarkToggleValue ) {
								$checkmarkToggleValue =	$nextOption[0]->attributes( 'value' );
							}

							if ( $checkmarkToggleValue != $value ) {
								$checkmarkTask		=	'setfield/' . $name . '/' . $checkmarkToggleValue;
							} else {
								// There's nothing to toggle to so lets shut off toggling:
								$isToggle			=	false;
							}
						} else {
							// There's nothing to toggle to so lets shut off toggling:
							$isToggle				=	false;
						}
					}
				}
			} else {
				if ( ! $isToggle ) {
					$checkmarkTitle					=	CBTxt::T( $node->attributes( 'title' ) );
				}
			}

			if ( ! $classes ) {
				$classes							=	RegistryEditView::buildClasses( $node, array(), 'inline' );
				$iconClass							=	$node->attributes( 'iconclass' );
				$iconSize							=	$node->attributes( 'iconsize' );
				$textClass							=	$node->attributes( 'textclass' );
			}

			if ( ! $iconClass ) {
				$classes							.=	' fa';

				if ( $value ) {
					$classes						.=	' fa-check';
				} else {
					$classes						.=	' fa-times';
				}
			}

			if ( ! $iconSize ) {
				$classes							.=	' fa-lg';
			}

			if ( ! $textClass ) {
				if ( $value ) {
					$classes						.=	' text-success';
				} else {
					$classes						.=	' text-danger';
				}
			}

			if ( $isToggle ) {
				if ( ! $checkmarkToggleTitle ) {
					$checkmarkToggleTitle			=	( $defaultToggleTitle ? $defaultToggleTitle : ( $value ? CBTxt::T( 'Disable Item' ) : CBTxt::T( 'Enable Item' ) ) );
				}

				$taskName							=	$this->_controllerView->taskName( false );
				$subTaskName						=	$this->_controllerView->subtaskName( false );
				$subTaskValue						=	$this->_controllerView->subtaskValue( $checkmarkTask, false );
				$fieldId							=	$this->_controllerView->fieldId( 'id', null, false );

				$onClick							=	"return cbListItemTask( this, '" . $taskName . "', '" . $subTaskName . "', '" . $subTaskValue . "', '" . $fieldId . "', '" . $this->_controllerView->pageNav->getRowIndex() . "' );";

				$return								=	'<a href="javascript: void(0);" onclick="' . $onClick . '">'
													.		'<span class="' . htmlspecialchars( trim( $classes ) ) . '" title="' . htmlspecialchars( $checkmarkToggleTitle ) . '"></span>'
													.	'</a>';

				return $return;
			} else {
				if ( ! $checkmarkTitle ) {
					$checkmarkTitle					=	( $defaultTitle ? $defaultTitle : ( $value ? CBTxt::T( 'Enabled' ) : CBTxt::T( 'Disabled' ) ) );
				}

				return '<span class="' . htmlspecialchars( trim( $classes ) ) . '" title="' . htmlspecialchars( $checkmarkTitle ) . '"></span>';
			}
		} else {
			if ( $node->getChildByNameAttributes( 'option' ) ) {
				return $this->_form_list( $name, $value, $node, $control_name, true );
			} else {
				return $this->_form_yesno( $name, $value, $node, $control_name );
			}
		}
	}

	/**
	 * Implements a form published state
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_published( $name, $value, $node, $control_name ) {
		$publishTask			=	( $value ? 'unpublish/' . $name : 'publish/' . $name );
		$publishTitle			=	( $value ? CBTxt::T( 'Published' ) : CBTxt::T( 'Unpublished' ) );
		$publishToggleTitle		=	( $value ? CBTxt::T( 'Unpublish Item' ) : CBTxt::T( 'Publish Item' ) );

		return $this->_form_checkmark( $name, $value, $node, $control_name, $publishTask, $publishTitle, $publishToggleTitle );
	}

	/**
	 * Implements a form yes/no field
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_yesno( $name, $value, $node, $control_name ) {
		if ( $node->getChildByNameAttributes( 'option' ) ) {
			// Force button usage for yesno cases with options:
			$node->addAttribute( 'buttons', 'true' );

			return $this->_form_radio( $name, $value, $node, $control_name );
		}

		$yes					=	CBTxt::T( $node->attributes( 'yes' ) );

		if ( ! $yes ) {
			$yes				=	CBTxt::T( 'UE_YES YES', 'Yes' );
		}

		$no						=	CBTxt::T( $node->attributes( 'no' ) );

		if ( ! $no ) {
			$no					=	CBTxt::T( 'UE_NO NO', 'No' );
		}

		if ( $this->_view ) {
			return ( $value == 1 ? $yes : $no );
		} else {
			$attributes				=	$this->getTooltipAttr( $node );
			$btnAttributes			=	null;

			if ( $this->_isrepeat ) {
				$repeatDefault		=	$node->attributes( 'default' );

				if ( $repeatDefault ) {
					$btnAttributes	.=	' data-cbrepeat-default="' . htmlspecialchars( $repeatDefault ) . '"';
				}
			}

			return moscomprofilerHTML::yesnoButtonList( $this->control_name( $control_name, $name ), $attributes, ( $value ? 1 : 0 ), $yes, $no, $btnAttributes, false );
		}
	}

	/**
	 * Implements a form firstwords field
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_firstwords( $name, $value, $node, $control_name ) {
		if ( $this->_view ) {
			$size				=	$node->attributes( 'size' );

			if ( ! $size ) {
				$size			=	45;
			}

			$strippedContent	=	trim( strip_tags( cbUnHtmlspecialchars( $value ) ) );

			if ( cbIsoUtf_strlen( $strippedContent ) <= $size ) {
				return htmlspecialchars( $strippedContent );
			}

			$firstWords			=	htmlspecialchars( cbIsoUtf_substr( $strippedContent, 0, $size ) . '...' );

			if ( $node->attributes( 'valuedescription' ) ) {
				return $firstWords;
			}

			return '<span title="' . htmlspecialchars($strippedContent ) . '">' . $firstWords . '</span>';
		} else {
			return $this->_form_textarea($name, $value, $node, $control_name );
		}
	}

	/**
	 * Implements a form datetime field with date-picker
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_datetime( $name, $value, $node, $control_name )
	{
		$value					=	(string) $value;

		global $_CB_framework;

		$showTime				=	$node->attributes( 'showtime' );

		if ( $showTime === null ) {
			$showTime			=	true;
		} else {
			$showTime			=	( ( $showTime == 'false' ) || ( $showTime == '0' ) ? false : ( ( $showTime == 'true' ) || ( $showTime == '1' ) ? true : $showTime ) );
		}

		$dateFormat				=	$node->attributes( 'dateformat' );
		$timeFormat				=	$node->attributes( 'timeformat' );

		if ( $dateFormat ) {
			$dateTimeFormat		=	$dateFormat . ( $timeFormat ? ' ' . $timeFormat : null );

			// Test if the supplied format is even a valid PHP date format:
			if ( \DateTime::createFromFormat( $dateTimeFormat, $value ) === false ) {
				// Geneate a validation rule for the supplied format so we can at least enforce it to a date format:
				$ruleRegexp		=	preg_replace_callback( '/([\w]+)/i', function( array $matches ) {
										return '\d{' . strlen( $matches[1] ) . ',' . strlen( $matches[1] ) . '}';
									},
									$dateTimeFormat );

				cbValidator::addRule( $this->control_name( $control_name, $name ), "return this.optional( element ) || /^$ruleRegexp$/i.test( value );", CBTxt::T( 'VALIDATION_ERROR_FIELD_DATE', 'Please enter a valid date.' ) );

				$validation		=	cbValidator::getRuleHtmlAttributes( $this->control_name( $control_name, $name ) );

				// Format is not valid so lets treat it like a text field:
				return $this->textfield( $name, $value, $node, $control_name, array(), $validation ) . ( ! $this->_view ? ' <span>(' . $dateTimeFormat . ')</span>' : null );
			}
		}

		$timeZone				=	$node->attributes( 'timezone' );
		$adaptTimeZone			=	( $timeZone == 'RAW' ? false : true );

		if ( $this->_view ) {
			if ( $adaptTimeZone && $timeZone ) {
				// Date needs to be timezone adjusted and a timezone has been supplied so send it for usage:
				$content		=	cbFormatDate( $value, $adaptTimeZone, $showTime, $dateFormat, $timeFormat, $timeZone );
			} else {
				// Date may or may not need to be timezone adjusted; send result of $adaptTimeZone and use global timezone:
				$content		=	cbFormatDate( $value, $adaptTimeZone, $showTime, $dateFormat, $timeFormat );
			}
		} else {
			$calendarType		=	(int) $node->attributes( 'calendartype' );
			$minYear			=	$node->attributes( 'minyear' );
			$maxYear			=	$node->attributes( 'maxyear' );
			$validate			=	$node->attributes( 'validate' );

			if ( $validate && in_array( 'required', explode( ',', $validate ) ) ) {
				$required		=	true;
			} else {
				$required		=	false;
			}

			$calendars			=	new cbCalendars( $_CB_framework->getUi(), $calendarType, $dateFormat, $timeFormat );

			$attributes			=	$this->getTooltipAttr( $node );

			$content			=	$calendars->cbAddCalendar( $this->control_name( $control_name, $name ), '', $required, $value, false, (bool) $showTime, $minYear, $maxYear, $attributes, $adaptTimeZone, ( $adaptTimeZone && $timeZone ? $timeZone : null ) );
		}

		return $content;
	}

	/**
	 * Implements a form day-of-week field
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_dayofweek( $name, $value, $node, $control_name ) {
		static $days	=	array(	array( 'value' => 0, 'text' => '-' ),
			array( 'value' => 1, 'text' => "Sunday" ),
			array( 'value' => 2, 'text' => "Monday" ),
			array( 'value' => 3, 'text' => "Tuesday" ),
			array( 'value' => 4, 'text' => "Wednesday" ),
			array( 'value' => 5, 'text' => "Thursday" ),
			array( 'value' => 6, 'text' => "Friday" ),
			array( 'value' => 7, 'text' => "Saturday" ),
		);
		static $allTranslated	=	false;
		if ( $this->_view ) {
			if ( ( $value >= 0 ) && ( $value <= 7 ) ) {
				$content	=	$allTranslated ? $days[(int) $value]['text'] : CBTxt::T( $days[(int) $value]['text'] );
			} else {
				$content	=	'-';
			}
			return $content;
		} else {
			if ( ! $allTranslated ) {
				foreach ( $days as $k => $v ) {
					$days[$k]['text']	=	CBTxt::T($v['text']);
				}
				$allTranslated			=	true;
			}

			$multi			=	( $node->attributes( 'multiple' ) == 'true' );

			if ( is_array( $value ) ) {
				$selected	=	$value;
			} else {
				$selected	=	explode( '|*|', (string) $value );
			}

			return $this->selectList( $days, $node, $control_name, $name, $selected, $multi );
		}
	}

	/**
	 * Implements a form password field
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_password( $name, $value, $node, $control_name )
	{
		$value			=	(string) $value;

		if ( $this->_view ) {
			$sprintf	=	 $node->attributes( 'sprintf' );
			if ( $sprintf ) {
				return htmlspecialchars( sprintf( $sprintf, $value ) );
			} else {
				return "********";		// htmlspecialchars($value);
			}
		} else {
			$size		=	$node->attributes( 'size' );
			$siz		=	( $size ? ' size="' . (int) $size . '"' : null );
			$classes	=	' class="' . htmlspecialchars( RegistryEditView::buildClasses( $node, array( 'form-control' ) ) ) . '"';
			return '<input type="password" autocomplete="off" name="'. $this->control_name( $control_name, $name ) . '" id="'. $this->control_id( $control_name, $name ) . '" value="'. htmlspecialchars($value) .'"' . $siz . $classes . ' />';
		}
	}

	/**
	 * Implements a form hidden field (invisible hidden input)
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_hidden( $name, $value, $node, $control_name )
	{
		$value			=	(string) $value;

		if ( $node->attributes( 'value' ) ) {
			$value		=	$node->attributes( 'value' );
		}
		if ( $this->_view ) {
			return  null;
		} else {
			return '<input type="hidden" name="'. $this->control_name( $control_name, $name ) . '" id="'. $this->control_id( $control_name, $name ) . '" value="'. htmlspecialchars($value) .'" />';
		}
	}

	/**
	 * Implements a form params-type field for showing a given plugin param
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_param( /** @noinspection PhpUnusedParameterInspection */ $name, $value, $node, $control_name ) {
		$content	=	$this->_pluginParams->get( $node->attributes( 'value' ) );
		return $content;
	}

	/**
	 * Implements a form xpath-type field for showing a given xpath value
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement    $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_xpath( /** @noinspection PhpUnusedParameterInspection */ $name, $value, $node, $control_name ) {
		$fromNode				=	$node->attributes( 'path' );
		$fromFile				=	$node->attributes( 'file' );
		$translate				=	$node->attributes( 'translate' );

		$return					=	null;

		if ( $fromNode && ( $fromFile !== null ) ) {
			$this->substituteName( $fromFile, true );
			$this->substituteName( $fromNode, false );

			if ( $fromFile !== '' ) {
				$fromFile		=	static::pathFromXML( $fromFile . '.xml', $node, $this->_pluginObject );
			}

			if ( ( $fromFile === '' ) || is_readable( $fromFile ) ) {
				if ( $fromFile === '' ) {
					$fromRoot	=	$node;
				} else {
					$fromRoot	=	new SimpleXMLElement( $fromFile, LIBXML_NONET | ( defined('LIBXML_COMPACT') ? LIBXML_COMPACT : 0 ), true );
				}

				/** @var SimpleXMLElement[] $xmlPath */
				$xmlPath		=	$fromRoot->xpath( $fromNode );

				if ( $xmlPath && count( $xmlPath ) ) {
					$return		=	$xmlPath[0]->data();
				}

				if ( $translate == 'yes' ) {
					$return		=	CBTxt::Th( $return );
				}
			}
		}

		return $return;
	}

	/**
	 * Implements a form http request render of its result (read-only)
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement    $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_httprequest( /** @noinspection PhpUnusedParameterInspection */ $name, $value, $node, $control_name ) {
		$link					=	$node->attributes( 'link' );

		if ( ! $link ) {
			return null;
		}

		$this->substituteName( $link, false );

		// TODO: Improve drawUrl or here directly to handle local raw URLs (e.g. test.html should prefix with live_site)
		$url					=	$this->_controllerView->drawUrl( $link, $node, $this->_modelOfData[0], $this->_modelOfData[0]->get( 'id' ) );

		if ( ( ! $url ) || cbStartOfStringMatch( $url, 'javascript:' ) ) {
			return null;
		}

		$client					=	new GuzzleHttp\Client();

		try {
			$result				=	$client->get( $url );
			// TODO: Implement handling of <data and sending as post instead of get when present

			if ( $result->getStatusCode() != 200 ) {
				$result			=	false;
			}
		} catch ( Exception $e ) {
			$result				=	false;
		}

		$return					=	null;

		if ( $result !== false ) {
			if ( cbGuzzleVersion() >= 6 ) {
				$contentType	=	$result->getHeaderLine( 'Content-Type' );
			} else {
				$contentType	=	$result->getHeader( 'Content-Type' );
			}

			switch( $contentType ) {
				case 'application/xml':
					// TODO: Implement parsing of XML responses through params if it's a CB xml file otherwise parse to array then into fields output
					$return		=	CBTxt::T( 'HTTP Request XML response handling is not yet implemented.' );
					break;
				case 'application/json':
					$return		=	$this->_json_render( $result->json(), $node );
					break;
				default:
					$return		=	$result->getBody();
					break;
			}
		} else {
			$return				=	$value;
		}

		return $return;
	}

	/**
	 * Implements a form json-rendering of a value (read-only)
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_json( /** @noinspection PhpUnusedParameterInspection */ $name, $value, $node, $control_name ) {
		if ( $value == '' ) {
			return null;
		}

		if ( ( $value[0] === '{' ) || ( $value[0] === '[' ) ) {
			$struct		=	json_decode( $value );

			return $this->_json_render( $struct, $node );
		}

		return null;
	}

	/**
	 * Renders a field formatted JSON array/object structure
	 *
	 * @param  array|object      $json
	 * @param  SimpleXMLElement  $node
	 * @return null|string
	 */
	protected function _json_render( $json, $node ) {
		$formatting			=	$node->attributes( 'formatting' );

		if ( ! $formatting ) {
			$formatting		=	'div';
		}

		$return				=	null;

		foreach ( $json as $k => $v ) {
			$result			=	array();
			$result[0]		=	htmlspecialchars( $k );
			$result[1]		=	null;
			$result[2]		=	null;

			if ( is_object( $v ) || is_array( $v ) ) {
				$result[1]	=	$this->_json_render( $v, $node );
			} else {
				$result[1]	=	htmlspecialchars( $v );
			}

			$return			.=	$this->_renderLine( $node, $result, null, $formatting, false, false );
		}

		return $return;
	}

	/**
	 * Implements a form permissions for asset assetname
	 *
	 * @param  string              $name          The name of the form element
	 * @param  string              $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return string                             The html for the element
	 */
	protected function _form_permissions( $name, /** @noinspection PhpUnusedParameterInspection */ $value, $node, $control_name ) {
		return $this->getPermissionsForm( $name, $node, $control_name )->getInput( 'rules' );
	}

	/**
	 * Internal method to save the permissions
	 *
	 * @param  string              $name          The name of the form element
	 * @param  array               $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return \JAccessRules |null|string          Rules on success, null if no saving, string error on failure
	 */
	protected static function _save_permissions( $name, &$value, $node, $control_name ) {
		$form		=	self::getPermissionsForm( $name, $node, $control_name );
		$data		=	self::validateForm( $form, $value );
		if ( $data ) {
			return self::savePerms( $node->attributes( 'assetname' ), $data, $node->attributes( 'title' ) );
		}
		return false;
	}

	/**
	 * Validates saving permissions of $postArray depending on $params
	 * Temporary hack!
	 *
	 * @param RegistryEditController $params    The params editor
	 * @param array                  $postArray The array received from a POST of the form
	 * @param null|TableInterface    $data      The data object being bound to
	 * @return boolean|string
	 */
	public static function validateAndBindPost( $params, &$postArray, $data = null ) {
		if ( count( $postArray ) ) {
			// Special handling for <param type="permissions"> fields:
			$xmls	=	$params->_xml->xpath( 'descendant::param[@type="permissions"]' );
			if ( count( $xmls ) > 0 ) {
				/** @var $node SimpleXMLElement */
				foreach ( $xmls as $node ) {
					if ( isset( $postArray[$node->attributes( 'name' )] ) ) {
						$rules	=	self::_save_permissions( $node->attributes( 'name' ), $postArray[$node->attributes( 'name' )], $node, '' );
						if ( is_object( $rules ) ) {
							// let's save the JSON string for future use:
							$postArray[$node->attributes( 'name' )]	=	(string) $rules;
						} elseif ( is_string( $rules ) ) {
							return $rules;
						}
					}
				}
			}
			// Special handling for <param onsave="class::method" key="firstparam" nosave="true"
			$xmls	=	$params->_xml->xpath( 'descendant::param[@onsave]' );
			if ( count( $xmls ) > 0 ) {
				foreach ( $xmls as $node ) {
					if ( isset( $postArray[$node->attributes( 'name' )] ) ) {

						// Call static method of class with first attribute key, and second the value:
						$classFunction	=	explode( '::', (string) $node->attributes( 'onsave' ) );
						$key			=	$node->attributes( 'key' );
						if ( $classFunction && $key ) {
							call_user_func_array( $classFunction, array( $key, cbGetParam( $postArray, $node->attributes( 'name' ) ) ) );
						}

						// Unset the posted variable if nosave="true":
						if ( $node->attributes( 'nosave' ) == 'true' ) {
							unset( $postArray[$node->attributes( 'name' )] );
						}
					}
				}
			}

			// Validate the POST data against the table structure so data is bound properly
			if ( ! $data ) {
				// No storage
				return true;
			}

			if ( $data instanceof TableInterface ) {
				$dataTable						=	$data->getTableName();
			} else {
				$dataTable						=	( isset( $data->_tbl ) ? $data->_tbl : null );
			}

			if ( ! $dataTable ) {
				// No database storage
				return true;
			}

			$db									=	Application::Database();
			$db->setQuery( "SHOW COLUMNS FROM " . $db->NameQuote( $dataTable ) );

			foreach ( $db->loadAssocList() as $column ) {
				if ( strpos( $column['Extra'], 'auto_increment' ) === true ) {
					// Ignore auto increment columns
					continue;
				}

				if ( ! preg_match( '/^([a-zA-Z]+)/', $column['Type'], $matches ) ) {
					continue;
				}

				$columnName						=	$column['Field'];
				$columnType						=	$matches[1];
				$columnDefault					=	$column['Default'];
				$columnIsNull					=	( $column['Null'] === 'YES' );

				// Column does not exist in the post data
				if ( ! array_key_exists( $columnName, $postArray ) ) {
					if ( ( ! $columnIsNull )					// Column does not allow null
						 && ( $columnDefault === null )			// Column does not have a default (set to None)
						 && ( ! isset( $data->$columnName ) )	// Column is missing or set to NULL in the data object
					) {
						// With the above conditions met it means we have missing REQUIRED data and will result in a failed storage so add the missing data
						// Note this is either a sign of a bad data structure (e.g. the column should have NULL or a default set) or bad usage of a column (e.g. it's being conditioned away and shouldn't be)
						switch ( $columnType ) {
							case 'int':
							case 'tinyint':
							case 'bigint':
							case 'decimal':
							case 'float':
								$postArray[$columnName]	=	0;
								break;
							case 'date':
							case 'datetime':
							case 'time':
								$postArray[$columnName]	=	$db->getNullDate( $columnType );
								break;
							default:
								$postArray[$columnName]	=	'';
								break;
						}
					}

					continue;
				}

				$columnValue					=	$postArray[$columnName];
				$storeNull						=	( $columnIsNull && ( ( isset( $data->_supportNullValue ) && $data->_supportNullValue ) || ( isset( $data->_nullable ) && in_array( $columnName, $data->_nullable, true ) ) ) );

				if ( ( $columnValue === null ) && ( ! $storeNull ) ) {
					// Trying to bind null, but null isn't allowed for this column so remove binding this data
					unset( $postArray[$columnName] );
					continue;
				}

				switch ( $columnType ) {
					case 'int':
					case 'tinyint':
					case 'bigint':
						if ( $columnValue === '' ) {
							// Cannot store empty string as int
							$postArray[$columnName]	=	( $storeNull ? null : 0 );
						} elseif ( $columnType !== 'bigint' ) {
							// Bigint is too large so we won't int cast it
							$postArray[$columnName]	=	(int) $columnValue;
						}
						break;
					case 'decimal':
					case 'float':
						if ( $columnValue === '' ) {
							// Cannot store empty string as decimal or float so set to 0
							// Also cannot rely on PHP precision so we can't cast this to float here
							$postArray[$columnName]	=	( $storeNull ? null : 0 );
						}
						break;
					case 'date':
					case 'datetime':
						if ( $columnValue === '' ) {
							// Is empty string either store null if allowed otherwise fallback to zero date
							$postArray[$columnName]	=	( $storeNull ? null : $db->getNullDate( $columnType ) );
						} elseif ( ( $columnValue === $db->getNullDate( $columnType ) ) && $storeNull ) {
							// Is zero date, but the column uses null so set to null
							$postArray[$columnName]	=	null;
						}
						break;
					default:
						$postArray[$columnName]		=	(string) $columnValue;
						break;
				}
			}
		}

		return true;
	}

	/**
	 * Saves permissions
	 *
	 * @param  string  $name             The name of the asset
	 * @param  array   $data             The form data posted
	 * @param  string  $title            The title of the asset
	 * @return \JAccessRules|null|string  string: error, null: no data, JAccessRules: rules saved
	 */
	protected static function savePerms( $name, $data, $title ) {
		if ( isset( $data['rules'] ) ) {
			if ( class_exists( 'JAccessRules' ) ) {
				// J2.5:
				$rules			=	new \JAccessRules( $data['rules'] );
			} else {
				// J1.6/1.7:
				jimport('joomla.access.rules');
				/** @noinspection PhpDeprecationInspection */
				$rules          =   new \JRules( $data['rules'] );
			}

			$asset				=	\JTable::getInstance('asset');

			/** @var $asset \JTableAsset */
			if ( ! $asset->loadByName( $name ) ) {
				$root			=	\JTable::getInstance('asset');
				// not wanting to inherit from CB as CBSubs is independent rules:		 if ( ! $root->loadByName('com_comprofiler') ) {
				/** @var $root \JTableAsset */
				$root->loadByName('root.1');
				$asset->name	=	$name;
				$asset->title	=	$title;
				$asset->setLocation( $root->id, 'last-child' );
			}
			$asset->rules		=	(string) $rules;

			if ( ! $asset->check() || ! $asset->store() ) {
				if ( is_callable( array( $asset, 'getError' ) ) ) {
					/** @noinspection PhpDeprecationInspection */
					return $asset->getError();
				} else {
					return CBTxt::T("Save permissions error");
				}
			}
			return $rules;		// (int) $asset->id;
		}
		return null;
	}

	/**
	 * Renders the permissions form
	 *
	 * @param  string              $name          The name of the form element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string              $control_name  The control name
	 * @return \JForm
	 */
	protected static function getPermissionsForm( $name, $node, $control_name ) {
		\JFormHelper::loadFieldClass( 'rules' );

		$assetSection			=	$node->attributes( 'assetsection' );

		if ( ! $assetSection ) {
			$assetSection		=	'component';
		}

		$assetName				=	$node->attributes( 'assetname' );

		if ( ! $assetName ) {
			$assetName			=	'com_comprofiler';
		}

		$assetField				=	$node->attributes( 'assetfield' );

		if ( ! $assetField ) {
			$assetField			=	'';
		}

		$form					=	new \JForm( $name, array( 'control' => self::control_name_static( $control_name, $name ), 'load_data' => false ) );

		$xml					=	'<?xml version="1.0" encoding="utf-8"?>'
								.	'<form>'
								.		'<field name="rules" type="rules" label="FIELD_RULES_LABEL" translate_label="false" validate="rules" class="inputbox" filter="rules" section="' . htmlspecialchars( $assetSection ) . '" component="' . htmlspecialchars( $assetName ) . '" asset_field="' . htmlspecialchars( $assetField ) . '">';

		foreach ( $node->children() as $permission ) {
			/** @var $permission SimpleXMLElement */
			if ( $permission->getName() == 'permission' ) {
				$beforeLabel	=	CBTxt::T( $permission->attributes( 'beforelabel' ) );
				$label			=	( $beforeLabel ? $beforeLabel . ' &nbsp;' : '' ) . CBTxt::T( $permission->attributes( 'label' ) );

				$xml			.=			'<action name="' . htmlspecialchars( $permission->attributes( 'name' ) ) . '" title="' . htmlspecialchars( $label ) . '" description="' . htmlspecialchars( CBTxt::T( $permission->attributes( 'description' ) ) ) . '" />';
			}
		}

		$xml					.=		'</field>'
								.	'</form>';

		$form->load( $xml, true, false );

		return $form;
	}

	/**
	 * Method to validate the permissions form data.
	 *
	 * @param  \JForm   $form   The form to validate against.
	 * @param  array    $data   The data to validate.
	 * @param  string   $group  The name of the field group to validate.
	 * @return array|string    Array of filtered data if valid, string with error otherwise.
	 */
	protected static function validateForm( $form, $data, $group = null )
	{
		$data				=	$form->filter( $data );
		$return				=	$form->validate( $data, $group );

		if ( $return instanceof \Exception ) {
			/** @var $return \Exception */
			return $return->getMessage();
		}

		if ( $return === false ) {
			$errors			=	array();
			foreach ( $form->getErrors() as $message ) {
				$errors[]	=	\JText::_($message);
			}
			return implode( "\n", $errors );
		}

		return $data;
	}

	/**
	 * @param  string            $name          The name of the form element
	 * @param  string            $value         The value of the element
	 * @param  SimpleXMLElement  $node          The xml element for the parameter
	 * @param  string            $control_name  The control name
	 * @return string                           The html for the element
	 */
	protected function _form_ordering( $name, $value, $node, $control_name ) {
		global $_CB_database;

		$onclick								=	$node->attributes( 'onclick' );

		if ( $onclick ) {
			$onclickValues					=	explode( ',', $onclick );

			$additionalConditionOrderUp		=	true;
			$additionalConditionOrderDown	=	true;
			$orderinggroups					=	$node->getElementByPath( 'orderinggroups');
			if ( $orderinggroups ) {
				$orderings					=	array();
				foreach ( $orderinggroups->children() as $group ) {
					/** @var $group SimpleXMLElement */
					if ( $group->getName() == 'ordering' ) {
						$orderings[]		=	$group->attributes( 'name' );		// ignore $group->attributes( 'type' ) here
					}
				}
				if ( count( $orderings ) > 0 ) {
					foreach ( $orderings as $typeField ) {
						if ( $this->_modelOfDataRows[$this->_modelOfDataRowsNumber]->get( $typeField ) !== null ) {
							if ( isset( $this->_modelOfDataRows[$this->_modelOfDataRowsNumber - 1] )
								&& $this->_modelOfDataRows[$this->_modelOfDataRowsNumber]->get( $typeField ) != $this->_modelOfDataRows[$this->_modelOfDataRowsNumber - 1]->get( $typeField ) ) {
								$additionalConditionOrderUp		=	false;
							}
							if ( isset( $this->_modelOfDataRows[$this->_modelOfDataRowsNumber + 1] )
								&& $this->_modelOfDataRows[$this->_modelOfDataRowsNumber]->get( $typeField ) != $this->_modelOfDataRows[$this->_modelOfDataRowsNumber + 1]->get( $typeField ) ) {
								$additionalConditionOrderDown	=	false;
							}
						}
					}
				}
			}
			$noordering		=	( $node->attributes( 'noordering' ) == 'true' );
			$content	=  '';
			if ( in_array( 'arrows', $onclickValues ) && ( ! $noordering ) ) {
				$content	.= $this->_controllerView->pageNav->orderUpIcon( null, ( $value > -10000 && $value < 10000 && $additionalConditionOrderUp ), 'orderup/' . $name );
				$content	.= '&nbsp;&nbsp;&nbsp;';
				$content	.= $this->_controllerView->pageNav->orderDownIcon( null, null, ( $value > -10000 && $value < 10000 && $additionalConditionOrderDown ), 'orderdown/' . $name );
			}
			if ( ( in_array( 'arrows', $onclickValues ) && ( ! $noordering ) ) && in_array( 'number', $onclickValues ) ) {
				$content	.= '&nbsp;&nbsp;&nbsp;';
			}
			if ( in_array( 'number', $onclickValues ) || $noordering ) {
				$content	.= '<input type="text" name="' . $this->_controllerView->fieldName( $name . '[]' ) . '" size="5" value="' . $value . '" class="form-control form-control-sm text-center"' . ( $noordering ? ' readonly="readonly"' : null ) . ' />';
			}
		} elseif ( $this->_view ) {
			$content		=	htmlspecialchars( $value );
		} else {
			if ( ( $value > -10000 ) && ( $value < 10000 ) ) {
				$dataStorage					=	$this->_modelOfData[0]->getStorage();

				if ( $dataStorage instanceof TableInterface ) {
					$dataTable					=	$dataStorage->getTableName();
				} else {
					/** @var \StdClass $dataStorage */
					$dataTable					=	$dataStorage->_tbl;
				}

				$xmlsql							=	new XmlQuery( $_CB_database, $dataTable, $this->_pluginParams );

				$xmlsql->setExternalDataTypeValues( 'modelofdata', $this->_modelOfData[0] );

				$text							=	$node->attributes( 'value' );

				if ( ! $text ) {
					$text						=	$name;
				}

				$data							=	$node->getElementByPath( 'data' );

				if ( ! $data ) {
					$defaultData				=	'<?xml version="1.0" encoding="UTF-8"?>'
												.	'<data table="' . htmlspecialchars( $dataTable ) . '">'
												.		'<rows>'
												.			'<field name="' . htmlspecialchars( $name ) . '" as="value" />'
												.			'<field name="' . htmlspecialchars( $text ) . '" as="text" />'
												.		'</rows>'
												.		'<orderby>'
												.			'<field name="' . htmlspecialchars( $name ) . '" ordering="ASC" />'
												.		'</orderby>'
												.	'</data>';

					$data						=	new SimpleXMLElement( $defaultData );

					$xmlsql->prepare_query( $data );

					$xmlsql->addWhere( $name, '>', '-10000', 'sql:int' );
					$xmlsql->addWhere( $name, '<', '10000', 'sql:int' );

					$orderinggroups				=	$node->getElementByPath( 'orderinggroups');

					if ( $orderinggroups ) {
						foreach ( $orderinggroups->children() as $group ) {
							/** @var SimpleXMLElement $group */
							$orderingFieldName	=	$group->attributes( 'name' );

							if ( ( $group->getName() == 'ordering' ) && $orderingFieldName && ( $this->_modelOfData[0]->get( $orderingFieldName ) !== null ) ) {
								$xmlsql->addWhere( $orderingFieldName, '=', $this->_modelOfData[0]->get( $orderingFieldName ), $group->attributes( 'type' ) );
							}
						}
					}
				} else {
					$xmlsql->prepare_query( $data );
				}

				$options						=	$this->_getOrderingList( $xmlsql->_buildSQLquery() );

				if ( $value === '' ) {
					$value						=	$options[count( $options ) - 1]->value;
				}

				$value							=	(int) $value;

				$content						=	$this->selectList( $options, $node, $control_name, $name, $value );
			} else {
				$content						=	'<input type="hidden" name="ordering" value="'. $value .'" />' . CBTxt::T( 'This entry cannot be reordered' );
			}
		}

		return $content;
	}
	/**
	 * @param  string   $sql        SQL with ordering As value and 'name field' AS text
	 * @param  int      $chop       The length of the truncated headline
	 * @param  boolean  $translate  translate to CB language
	 * @return array                of makeOption
	 * @access private
	 */
	protected function _getOrderingList( $sql, $chop = 30, $translate = true ) {
		global $_CB_database;

		$order				=	array();
		$_CB_database->setQuery( $sql );
		try {
			$orders			=	$_CB_database->loadObjectList();
		} catch ( \RuntimeException $e ) {
			echo 'getOrderingList query error: ' . $e->getMessage();
			return false;
		}

		if ( count( $orders ) == 0 ) {
			$order[]	=	moscomprofilerHTML::makeOption( 1, CBTxt::T('first') );
			return $order;
		}
		$order[]			=	moscomprofilerHTML::makeOption( 0, '0 ' . CBTxt::T('first') );
		for ( $i=0, $n = count( $orders ); $i < $n; $i++ ) {
			if ( $translate ) {
				$text		=	CBTxt::T( $orders[$i]->text );
			} else {
				$text		=	$orders[$i]->text;
			}
			if ( strlen( $text ) > $chop ) {
				$text		=	substr( $text, 0, $chop ) . '...';
			}

			$order[]		=	moscomprofilerHTML::makeOption( (int) $orders[$i]->value, $orders[$i]->value . ' (' . $text . ')' );
		}
		if ( isset( $orders[$i - 1] ) ) {
			$order[]		=	moscomprofilerHTML::makeOption( (int) $orders[$i - 1]->value + 1, ( $orders[$i - 1]->value + 1 ) . ' ' . CBTxt::T('last') );
		}
		return $order;
	}
	/**
	 * @param  string  $name   The name of the form element
	 * @param  string  $value  The value of the element
	 * @param  SimpleXMLElement  $node  The xml element for the parameter
	 * @param  string  $control_name  The control name
	 * @return string The html for the element
	 */
	protected function _form_bargraph( /** @noinspection PhpUnusedParameterInspection */ $name, $value, $node, $control_name ) {
		global $_CB_framework;

		$content							=	'none';

		$key								=	$node->attributes( 'key' );
		if ( $this->_modelOfDataRows[$this->_modelOfDataRowsNumber]->get( $key ) ) {
			$max							=	0;
			foreach ( $this->_modelOfDataRows as $v ) {
				$max						=	max( $v->get( $key ), $max );
			}
			$percent						=	0;
			if ( ( count( $this->_modelOfDataRows ) > 0 ) && ( $max > 0 ) ) {
				$percent					=	ceil( ( 100.0 * $this->_modelOfDataRows[$this->_modelOfDataRowsNumber]->get( $key ) ) / $max );
			}
			if ( $percent == 0 ) {
				$percent					=	1;
			}
			$content = '<img src="'
				. ( $_CB_framework->getUi() == 2 ? '../' : '' )
				. 'components/com_comprofiler/plugin/user/plug_cbpaidsubscriptions/icons/normal/bargraph_horiz.gif" alt="bargraph"'
				. 'style="height:15px;width:'
				. $percent
				. '%;" />';
		}
		return $content;
	}
	/**
	 * @param  string  $name   The name of the form element
	 * @param  string  $value  The value of the element
	 * @param  SimpleXMLElement  $node  The xml element for the parameter
	 * @param  string  $control_name  The control name
	 * @return string The html for the element
	 */
	protected function _form_plot( /** @noinspection PhpUnusedParameterInspection */ $name, $value, $node, $control_name ) {
		global $_CB_framework;

		$rowspan		=	$node->attributes( 'rowspan' );
		if ( ( $rowspan == 'all' ) && ( $this->_modelOfDataRowsNumber != 0 ) ) {
			return null;
		}

		$this->plot_series	=	array();
		$plotVars		=	array();
		$plotFormat		=	null;

		$plot			=	$node->getElementByPath( 'plot' );
		if ( $plot ) {
			$plotId		=	$this->control_id( $control_name, $plot->attributes( 'name' ) );
			$series		=	$plot->getElementByPath( 'series' );
			if ( $series ) {
				foreach ( $series->children() as $serie ) {
					/** @var $serie SimpleXMLElement */
					if ( $serie->getName() == 'serie' ) {
						$plotVars[]	=	$this->xml2json( $serie, array( 'name' => null, 'serie' => array( $this, '_plot_parseSerie' ), 'data' => array( $this, '_plot_parseData' ) ) );
					}
				}
			}
			$plotFormat	=	$this->xml2json( $plot->getElementByPath( 'format' ), array( 'ticks' => array( $this, '_plot_parseTicks' ), 'tickFormatter' => array( $this, '_plot_parseTickFormatter' ), 'xaxis' => array( $this, '_plot_parseXaxis' ), 'yaxes' => array( $this, '_plot_parseYaxes' ) ) );

			$js		=	'$(function () {'
				.	"\n\t"
				.	 	'var theplace = document.getElementById("' . $plotId . '");'
				.	"\n\t"
				.	 	'theplace.style.height = Math.max(theplace.parentNode.offsetHeight-1, 80) + "px";'
				.	"\n\t"
				.	 	'$.plot($("#' . $plotId . '"), [' . implode( ',', $plotVars ) . "\n\t" . ']';

			if ( $plotFormat ) {
				$js	.=	",\n" . $plotFormat;
			}
			$js		.=	"\t)\n";
			$js		.=	"})\n";

			$_CB_framework->outputCbJQuery( $js, 'flot' );

			$content	=	'<div id="' . $plotId . '" style="width:100%;height:100%"></div>';
		} else {
			$content	=	'Missing plot in XML';
		}

		return $content;
	}
	/**
	 * Parse the ticks to plot
	 * @access private
	 *
	 * @param  SimpleXMLElement  $el         For XML serie label = "textToTranslate" />
	 * @param  string[]|callback[]  $callBacks
	 * @return array
	 */
	public function _plot_parseSerie( &$el, &$callBacks ) {
		$names_values						=	array();

		if ( $el ) {
			// $nam							=	$el->getName();
			$label							=	$el->attributes( 'label' );
			$xaxis							=	$el->attributes( 'xaxis' );
			$yaxis							=	$el->attributes( 'yaxis' );
			if ( $label ) {
				$label						=	CBTxt::T( $label );
			}
			$names_values['label']			=	$label;
			if ( $xaxis ) {
				$names_values['xaxis']		=	$xaxis;
			}
			if ( $yaxis ) {
				$names_values['yaxis']		=	$yaxis;
			}

			foreach ( $el->children() as $elChild ) {
				$names_values				=	array_merge_recursive( $names_values, $this->xml2arr( $elChild, $callBacks ) );
			}
		}

		return $names_values;
	}
	/**
	 * Parse the data to plot
	 * @access private
	 *
	 * @param  SimpleXMLElement  $el         <data missing="0"><y name="newregs" type="int" /><x name="time_paid_date" type="date" /></data>
	 * @param  string[]|callback[]  $callBacks
	 * @return array
	 */
	public function _plot_parseData( &$el, /** @noinspection PhpUnusedParameterInspection */ &$callBacks ) {
		$names_values					=	array();

		if ( $el ) {
			$missing					=	$el->attributes( 'missing' );
			$x							=	$el->getElementByPath( 'x' );
			$y							=	$el->getElementByPath( 'y' );
			if ( $x && $y ) {
				$xName					=	$x->attributes( 'name' );
				$xType					=	$x->attributes( 'type' );
				$xOrderingNotReverse	=	( $x->attributes( 'ordering' ) != 'reverse' );
				$yName					=	$y->attributes( 'name' );
				$yType					=	$y->attributes( 'type' );
				if ( $xName && $yName ) {
					if ( true
						|| ( $this->_modelOfDataRows[$this->_modelOfDataRowsNumber]->get( $xName ) !== null
							&& $this->_modelOfDataRows[$this->_modelOfDataRowsNumber]->get( $yName ) !== null ) )
					{
						$isDateType		=	in_array( $xType, array( 'date', 'datetime' ) );
						$isInt			=	array( 'int', 'date', 'datetime', 'dayofweek' );
						$incValue		=	( $isDateType ? (24*3600) : 1 );
						// date format is in miliseconds, but value is in seconds:
						$valuePostfix	=	 ( $isDateType ? '000' : '' );
						// data is existing in the model, we can get them:
						$jsAr			=	array();
						$previousV		=	null;
						foreach ( ( $xOrderingNotReverse ? $this->_modelOfDataRows : array_reverse( $this->_modelOfDataRows ) ) as $v ) {
							// $v is the object of the database row, $xName and $yName are the variables of the points:
							$xVal		=	$this->_plot_typeData( $v->getString( $xName, '' ), $xType );
							if ( $xVal !== null ) {
								$yVal		=	$this->_plot_typeData( $v->getString( $yName, '' ), $yType );
								if ( ( strlen( $missing ) > 0 ) && ( $previousV !== null ) ) {
									if ( in_array( $xType, $isInt ) ) {
										$increment				=	abs( $xVal - $previousV );
										if ( $increment > 1 ) {
											if ( $increment < 50 * $incValue ) {
												// if for int and date types, less than 50 points are missing, complete them if missing="value" attribute is set in data:
												if ( $xVal > $previousV ) {
													for ( $i = $previousV + $incValue; $i < $xVal; $i = $i + $incValue ) {
														$jsAr[]		=	array( $i . $valuePostfix, $missing );
													}
												} else {
													for ( $i = $previousV - $incValue; $i > $xVal; $i = $i - $incValue ) {
														$jsAr[]		=	array( $i . $valuePostfix, $missing );
													}
												}
											} else {
												// otherwise just make sure lines go to 0:
												if ( $xVal > $previousV ) {
													$jsAr[]		=	array( ( $previousV + $incValue ) . $valuePostfix, $missing );
													$jsAr[]		=	array( ( $xVal - $incValue ) . $valuePostfix, $missing );
												} else {
													$jsAr[]		=	array( ( $previousV - $incValue ) . $valuePostfix, $missing );
													$jsAr[]		=	array( ( $xVal + $incValue ) . $valuePostfix, $missing );
												}
											}

										}
									}
								}
								$previousV	=	$xVal;
								// *1000 for miliseconds:
								$jsAr[]		=	array( $xVal . $valuePostfix, $yVal );
							}
						}
						$names_values	=	$jsAr;			//	$names_values[$el->getName()]	=	'[' . implode( ', ', $jsAr ) . ']';
					}
				}
			}
		}

		return $names_values;
	}
	/**
	 * Parse the ticks to plot
	 * @access private
	 *
	 * @param  SimpleXMLElement  $el         <ticks type="function" name="nameofJSfunction" />
	 * @param  string[]|callback[]  $callBacks
	 * @return array
	 */
	public function _plot_parseTicks( &$el, &$callBacks ) {
		$names_values					=	array();

		if ( $el ) {
			$type						=	$el->attributes( 'type' );
			$name						=	$el->attributes( 'name' );
			if ( ($type == 'function' ) && $name ) {
				$names_values			=	$name;
			} else {
				$callBacksNew			=	$callBacks;
				unset( $callBacksNew[$el->getName()] );
				$names_values			=	$this->xml2arr( $el, $callBacksNew );
			}
		}

		return $names_values;
	}
	/**
	 * Parse the ticks to plot
	 * @access private
	 *
	 * @param  SimpleXMLElement  $el         <ticks type="function" name="nameofJSfunction" />
	 * @param  string[]|callback[]  $callBacks
	 * @return array
	 */
	public function _plot_parseTickFormatter( &$el, &$callBacks ) {
		global $_CB_database;

		$names_values					=	array();

		if ( $el ) {
			$type						=	$el->attributes( 'type' );
			$data						=	$el->getElementByPath( 'field' );
			if ( ($type == 'append' ) && $data ) {

				$dataTable				=	$data->attributes( 'table' );
				if ( ! $dataTable ) {
					$dataTable			=	null;
				}

				$xmlsql					=	new XmlQuery( $_CB_database, $dataTable, $this->_pluginParams );
				$xmlsql->setExternalDataTypeValues( 'modelofdata', $this->_modelOfData[0] );
				$xmlsql->process_data( $data );
				$textToAppend			=	$xmlsql->queryloadResult();		// get the records
				if ( $textToAppend ) {
					$names_values		=	new PlotJsonFormatter( 'function(val, axis) { return val.toFixed(axis.tickDecimals)+" ' . addslashes( CBTxt::T( $textToAppend ) ) . '"; }' );
				}
			} else {
				$callBacksNew			=	$callBacks;
				unset( $callBacksNew[$el->getName()] );
				$names_values			=	$this->xml2arr( $el, $callBacksNew );
			}
		}

		return $names_values;
	}
	/**
	 * Parse the xaxis to plot
	 * @access private
	 *
	 * @param  SimpleXMLElement  $el         <ticks type="function" name="nameofJSfunction" />
	 * @param  string[]|callback[]  $callBacks
	 * @return array
	 */
	public function _plot_parseXaxis( &$el, &$callBacks ) {
		$names_values					=	array();

		if ( $el ) {

			if ( $this->plot_series ) {
				foreach ( $this->plot_series as $k => $v ) {
					$names_values['ticks'][]	=	array( $k, $v );
				}
			} else {

				$nam						=	$el->getName();
				$mode						=	$el->attributes( 'mode' );

				$callBacksNew				=	$callBacks;
				unset( $callBacksNew[$nam] );
				$names_values				=	$this->xml2arr( $el, $callBacksNew );
				$names_values				=	$names_values[$nam];

				if ( $mode == 'time' ) {
					$names_values['monthNames']	=	array(	CBTxt::T("Jan"),
						CBTxt::T("Feb"),
						CBTxt::T("Mar"),
						CBTxt::T("Apr"),
						CBTxt::T("May"),
						CBTxt::T("Jun"),
						CBTxt::T("Jul"),
						CBTxt::T("Aug"),
						CBTxt::T("Sep"),
						CBTxt::T("Oct"),
						CBTxt::T("Nov"),
						CBTxt::T("Dec") );
				}
			}
		}

		return $names_values;
	}
	/**
	 * Parse the Y axes to plot
	 * @access private
	 *
	 * @param  SimpleXMLElement  $el         <ticks type="function" name="nameofJSfunction" />
	 * @param  string[]|callback[]  $callBacks
	 * @return array
	 */
	public function _plot_parseYaxes( &$el, &$callBacks ) {
		$names_values					=	array();

		if ( $el ) {
			$nam						=	$el->getName();
			// $mode					=	$el->attributes( 'mode' );

			$callBacksNew				=	$callBacks;
			unset( $callBacksNew[$nam] );
			$names_values				=	$this->xml2arr( $el, $callBacksNew );
			$names_values				=	array_values( $names_values[$nam] );
		}

		return $names_values;
	}

	/**
	 * Enforce data type $type of $fieldValue
	 *
	 * @param  string  $fieldValue
	 * @param  string  $type
	 * @return float|int|null|string
	 */
	protected function _plot_typeData( $fieldValue, $type ) {
		switch ( $type ) {
			case 'int':
			case 'dayofweek':
				$value		=	(int) $fieldValue;
				break;
			case 'float':
				$value		=	(float) $fieldValue;
				break;
			case 'formula':
				$value		=	$fieldValue;
				break;
			case 'datetime':
				list($y, $c, $d, $h, $m, $s) = sscanf($fieldValue, '%d-%d-%d %d:%d:%d');
				if ( $y && $c && $d ) {
					$value	=	mktime($h, $m, $s, $c, $d, $y);			// we do NOT use PHP strtotime, which is broken
				} else {
					$value	=	null;
				}
				break;
			case 'date':
				list( $y, $c, $d ) = sscanf($fieldValue, '%d-%d-%d');
				if ( $y && $c && $d ) {
//					$value	=	(int) ( mktime(0, 0, 0, $c, $d, $y) / 86400 );			// we do NOT use PHP strtotime, which is broken
					$value	=	mktime(0, 0, 0, $c, $d, $y);		//later this is added: . '000';			// we do NOT use PHP strtotime, which is broken + longints *1000 for miliseconds
				} else {
					$value	=	null;
				}
				break;
			case 'string':
				$value		=	'"' . $fieldValue . '"';
				break;
			case 'series':
				$value		=	$this->_plotSeriesX( $fieldValue );
				break;
			default:
				trigger_error( 'plot::_plot_typeData: ERROR_UNKNOWN_TYPE: ' . htmlspecialchars( $type ), E_USER_NOTICE );
				$value		=	'';
				break;
		}
		return $value;
	}
	protected $plot_series				=	array();
	/**
	 * Counts plot series
	 *
	 * @param  string  $fieldValue
	 * @return int
	 */
	protected function _plotSeriesX( $fieldValue ) {
		$pos							=	array_search( $fieldValue, $this->plot_series, true );
		if ( $pos === false ) {
			$this->plot_series[]		=	$fieldValue;
			$pos						=	count( $this->plot_series ) - 1;
		}
		return $pos;
	}
	/**
	 * Transforms XML to JSON
	 *
	 * @param  SimpleXMLElement  $el
	 * @param  string[]|callback[]  $callBacks
	 * @param  boolean             $ignoreRootName
	 * @return null|string
	 */
	protected function xml2json( &$el, $callBacks = null, $ignoreRootName = true ) {
		if ( $callBacks === null ) {
			$callBacks					=	array();
		}
		$names_values					=	$this->xml2arr( $el, $callBacks );
		if ( $ignoreRootName ) {
			$keys						=	array_keys( $names_values );
			if ( count( $keys ) > 0 ) {
				$key					=	$keys[0];
				return $this->arr2json( $names_values[$key] );
			}
			return null;
		}
		return $this->arr2json( $names_values );
	}
	/**
	 * Transforms Array to JSON
	 *
	 * @param  array  $names_values
	 * @return null|string
	 */
	protected function arr2json( &$names_values ) {
		static $level					=	0;
		$js								=	null;

		if ( count( $names_values ) > 0 ) {
			$isArrayNotObject			=	isset( $names_values[0] );
			$js							.=	( $isArrayNotObject ? '[' : "\n" . str_repeat( "\t", $level ) . '{' );
			++$level;
			$jsArray					=	array();
			foreach ( $names_values as $k => $v ) {
				$jsArray[$k]			=	( $isArrayNotObject ? '' : "\n" . str_repeat( "\t", $level ) . $k . ': ' );
				if ( is_array( $v ) ) {
					/** @var $v array */
					$jsArray[$k]		.=	$this->arr2json( $v );
				} elseif ( is_object( $v ) ) {
					/** @var $v PlotJsonFormatter */
					$jsArray[$k]		.=	$v->jsonValue();
				} elseif ( ( $v === 'cbPlotTicksDate' )  || ( $v === 'cbPlotWeekends' ) || ( $v === 'cbPlotTicksWeekDays' ) ) {
					/** @var $v string */
					global $_CB_framework;
					$_CB_framework->addJQueryPlugin( 'cb-flotdates', '/components/com_comprofiler/js/cb-flotdates.js' );
					$jsArray[$k]		.=	'cbjQuery.' . $v;
				} elseif ( ( $v === 'true' ) || ( $v === 'false' )  || ( $v === 'cbPlotWeekends' ) || ( $v === 'cbPlotTicksWeekDays' ) || preg_match( '/^[0-9]+\\.?[0-9]*$/', $v ) ) {
					/** @var $v string */
					$jsArray[$k]		.=	$v;
				} else {
					$jsArray[$k]		.=	'"' . addslashes( $v ) . '"';
				}
			}
			$js							.=	implode( ', ', $jsArray );
			--$level;
			$js							.=	( $isArrayNotObject ? ']' : "\n" . str_repeat( "\t", $level ) . '}' );
		}

		return $js;
	}
	/**
	 * Transforms XML to Array
	 * @param  SimpleXMLElement  $el
	 * @param  string[]|callback[]  $callBacks
	 * @return array
	 */
	protected function & xml2arr( &$el, &$callBacks ) {
		$names_values					=	array();

		if ( $el ) {
			$nam						=	$el->getName();
			if ( array_key_exists( $nam, $callBacks ) ) {
				$names_values[$nam]		=	call_user_func_array( $callBacks[$nam], array( &$el, &$callBacks ) );
			} else {
				$names_values[$nam]		=	$el->attributes();
				foreach ( $el->children() as $elChild ) {
					$names_values		=	array_merge_recursive( $names_values, array( $nam => $this->xml2arr( $elChild, $callBacks ) ) );
				}
			}
		}

		return $names_values;
	}

	/**
	 * special handling for textarea param in the textarea rendering
	 *
	 * @param  string[]  $txt  The texts to handle
	 * @return string          The cleaned text as parameter
	 */
	public static function textareaHandling( &$txt ) {
		$total = count( $txt );
		for( $i=0; $i < $total; $i++ ) {
			if ( strstr( $txt[$i], "\n" ) ) {
				$txt[$i] = str_replace( array( "\\", "\n", "\r" ), array( "\\\\", '\n', '\r'  ) , $txt[$i] );
			} else {
				$txt[$i] = str_replace( "\\", "\\\\" , $txt[$i] );
			}
		}
		$ret = implode( "\n", $txt );
		return $ret;
	}
}

/**
 * Utility class for PLOT form param type
 */
class PlotJsonFormatter {
	public $v;
	/**
	 * Constructor
	 *
	 * @param  string  $v
	 */
	public function __construct( $v ) {
		$this->v	=	$v;
	}
	/**
	 * Gets the JSON value back that was used to construct
	 *
	 * @return string
	 */
	public function jsonValue( ) {
		return $this->v;
	}
}
