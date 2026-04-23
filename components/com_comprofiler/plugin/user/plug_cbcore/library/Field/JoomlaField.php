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
use CBLib\Language\CBTxt;
use cbSqlQueryPart;
use CBuser;
use JFormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Users\Administrator\Model\UserModel;
use Joomla\Component\Users\Site\Model\ProfileModel;
use Joomla\Registry\Registry;
use JPluginHelper;
use moscomprofilerHTML;

\defined( 'CBLIB' ) or die();

class JoomlaField extends cbFieldHandler
{
	/**
	 * Formatter:
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output               'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $formatting           'tr', 'td', 'div', 'span', 'none',   'table'??
	 * @param  string      $reason               'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getFieldRow( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		$this->getJoomlaField( $field, $user ); // Applies $field overrides if necessary (e.g. title and description)

		return parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
	}

	/**
	 * Accessor:
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output               'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $reason               'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		/** @var null|JFormField $joomlaField */
		$joomlaField			=	$this->getJoomlaField( $field, $user );

		if ( ! $joomlaField ) {
			return $this->_formatFieldOutput( $field->getString( 'name', '' ), null, $output, false );
		}

		switch ( $output ) {
			case 'html':
			case 'rss':
				$customFields	=	$this->getJoomlaUserFields( $user );

				ob_start();
				if ( \array_key_exists( $joomlaField->fieldname, $customFields ) ) {
					echo $customFields[$joomlaField->fieldname]->value;
				} elseif ( HTMLHelper::isRegistered( 'users.' . $joomlaField->id ) ) {
					echo HTMLHelper::_( 'users.' . $joomlaField->id, $joomlaField->value );
				} elseif ( HTMLHelper::isRegistered( 'users.' . $joomlaField->fieldname ) ) {
					echo HTMLHelper::_( 'users.' . $joomlaField->fieldname, $joomlaField->value );
				} elseif ( HTMLHelper::isRegistered( 'users.' . $joomlaField->type ) ) {
					echo HTMLHelper::_( 'users.' . $joomlaField->type, $joomlaField->value );
				} else {
					echo HTMLHelper::_( 'users.value', $joomlaField->value );
				}
				$html			=	ob_get_clean();

				return $this->formatFieldValueLayout( $html, $reason, $field, $user );
			case 'htmledit':
				if ( $reason === 'search' ) {
					return $this->_fieldSearchModeHtml( $field, $user, $this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', Application::Input()->getString( $field->getString( 'name', '' ), '' ), '' ), 'text', $list_compare_types );
				}

				// Attempt to add the description tooltip to the input if input description display is enabled
				if ( $field->params->getBool( 'fieldLayoutInputDesc', true ) ) {
					$description		=	trim( strip_tags( $this->getFieldDescription( $field, $user, $output, $reason ) ) );

					if ( $description ) {
						$title			=	$this->getFieldTitle( $field, $user, 'html', $reason );

						if ( $title ) {
							$joomlaField->__set( 'data-cbtooltip-title', $title );
						}

						$joomlaField->__set( 'data-cbtooltip-tooltip', $description );
						$joomlaField->__set( 'data-hascbtooltip', 'true' );
					}
				}

				// If we're trying to correct the style to match CB apply any necessary attribute changes
				if ( ! $field->params->getBool( 'joomla_field_style', true ) ) {
					switch ( strtolower( $joomlaField->layout ) ) {
						case 'joomla.form.field.checkboxes':
							$joomlaField->class	=	trim( $joomlaField->class . ' d-inline-block' );
							break;
						case 'joomla.form.field.color':
							$joomlaField->class	=	trim( $joomlaField->class . ' pl-5' );
							break;
					}
				}

				$html			=	$joomlaField->input;

				// If we're trying to correct the style to match CB apply any necessary html replacements
				if ( ! $field->params->getBool( 'joomla_field_style', true ) ) {
					switch ( strtolower( $joomlaField->layout ) ) {
						case 'joomla.form.field.media.accessiblemedia':
							$html	=	str_replace( [ 'class="control-group"', 'class="control-label"', 'class="controls"' ], [ 'class="form-group row no-gutters"', 'class="col-sm-3 pr-sm-2"', 'class="col-sm-9"' ], $html );
							break;
						case 'joomla.form.field.radio.buttons':
							$html	=	str_replace( [ '<fieldset', 'class="btn-group', 'class="btn-check"', 'class="btn btn-outline-secondary"' ], [ '<fieldset class="d-inline-block"', 'class="cbRadioButtons btn-group-list flex-wrap', '', 'class="btn btn-primary"' ], $html );
							break;
						case 'joomla.form.field.calendar':
							$html	=	str_replace( 'class="field-calendar"', 'class="field-calendar d-inline-block"', $html );
							break;
						case 'joomla.form.field.user':
							$html	=	str_replace( 'class="field-user-wrapper"', 'class="field-user-wrapper d-inline-block"', $html );
							break;
						case 'joomla.form.field.list':
							$html	=	str_replace( 'class="form-select', 'class="form-control', $html );
							break;
					}
				}

				return $html
					   . $this->_fieldIconsHtml( $field, $user, $output, $reason, 'input', 'text', $joomlaField->value, '', [], true, ( $this->_isRequired( $field, $user, $reason ) && ( ! $this->_isReadOnly( $field, $user, $reason ) ) ) );
		}

		return $this->_formatFieldOutput( $field->getString( 'name', '' ), $joomlaField->value, $output, false );
	}

	/**
	 * Mutator:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save user edit, 'register' for save registration
	 */
	public function prepareFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		/** @var null|JFormField $joomlaField */
		$joomlaField			=	$this->getJoomlaField( $field, $user );

		if ( ! $joomlaField ) {
			return;
		}

		$joomlaData				=	$this->getJoomlaFieldPost( $joomlaField, $user, $postdata );
		$joomlaKey				=	( $joomlaField->group ? $joomlaField->group . '.' : '' ) . $joomlaField->fieldname;
		$joomlaValue			=	$joomlaData->get( $joomlaKey );

		if ( $joomlaValue === null ) {
			return;
		}

		try {
			$joomlaBindData		=	$this->getJoomlaModel( $user )->validate( $this->getJoomlaFieldForm( $joomlaField, $user ), $joomlaData->toArray(), $joomlaField->group );
		} catch ( \Exception $e ) {
			$this->_setValidationError( $field, $user, $reason, $e->getMessage() );

			return;
		}

		if ( $joomlaBindData === false ) {
			$joomlaValue		=	null;
		} elseif ( \is_array( $joomlaValue ) || \is_object( $joomlaValue ) ) {
			$joomlaValue		=	\json_encode( $joomlaValue );
		}

		if ( ! $this->validate( $field, $user, null, $joomlaValue, $postdata, $reason ) ) {
			$this->_setValidationError( $field, $user, $reason, 'test' );

			return;
		}

		$user->bindData( $joomlaBindData );
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
	 * @return bool                     True if validate, $this->_setErrorMSG if False
	 */
	public function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason )
	{
		if ( ( ! Application::Application()->isClient( 'administrator' ) ) || ( Application::Application()->isClient( 'administrator' ) && ( Application::Config()->getInt( 'adminrequiredfields', 0 ) === 1 ) ) ) {
			if ( ( $field->getInt( 'required', 0 ) === 1 ) && ( ( $value === null ) || ( $value === '' ) ) ) {
				$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'UE_REQUIRED_ERROR', 'This field is required!' ) );

				return false;
			}
		}

		/** @var null|JFormField $joomlaField */
		$joomlaField		=	$this->getJoomlaField( $field, $user );

		if ( ! $joomlaField ) {
			return false;
		}

		try {
			$joomlaModel	=	$this->getJoomlaModel( $user );

			if ( $joomlaModel->validate( $this->getJoomlaFieldForm( $joomlaField, $user ), $this->getJoomlaFieldPost( $joomlaField, $user, $postdata )->toArray(), $joomlaField->group ) === false ) {
				foreach ( $joomlaModel->getErrors() as $error ) {
					if ( $error instanceof \Exception ) {
						$this->_setValidationError( $field, $user, $reason, $error->getMessage() );
					} else {
						$this->_setValidationError( $field, $user, $reason, $error );
					}
				}

				return false;
			}
		} catch ( \Exception $e ) {
			$this->_setValidationError( $field, $user, $reason, $e->getMessage() );

			return false;
		}


		return true;
	}

	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $searchVals          RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata            Typically $_POST (but not necessarily), filtering required.
	 * @param  int         $list_compare_types  IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string      $reason              'edit' for save user edit, 'register' for save registration
	 * @return cbSqlQueryPart[]
	 */
	public function bindSearchCriteria( &$field, &$searchVals, &$postdata, $list_compare_types, $reason )
	{
		global $_CB_database;

		/** @var null|JFormField $joomlaField */
		$joomlaField					=	$this->getJoomlaField( $field, CBuser::getMyUserDataInstance() );

		if ( ! $joomlaField ) {
			return [];
		}

		$searchMode						=	$this->_bindSearchMode( $field, $searchVals, $postdata, 'text', $list_compare_types );

		if ( ! $searchMode ) {
			return [];
		}

		$query							=	[];
		$fieldName						=	$field->getString( 'name', '' );
		$value							=	Application::Input()->getString( $fieldName, '' );

		if ( ( ( ( $value !== null ) && ( $value !== '' ) ) || ( ( $list_compare_types === 1 ) && \in_array( $searchMode, [ 'is', 'isnot' ], true ) ) ) && ( ! \is_array( $value ) ) ) {
			$searchVals->$fieldName		=	$value;

			// Build the where statement for the search mode
			$sqlValue					=	new cbSqlQueryPart();
			$sqlValue->tag				=	'column';
			$sqlValue->name				=	'value';
			$sqlValue->table			=	'#__fields_values';
			$sqlValue->type				=	'sql:field';
			$sqlValue->operator			=	'=';
			$sqlValue->value			=	$value;
			$sqlValue->valuetype		=	'const:string';
			$sqlValue->searchmode		=	$searchMode;

			$tableReferences			=	[ '#__fields_values' => 'jf', '#__users' => 'u' ];
			$joinsSQL					=	[];

			// Now add the search using a formula where statement
			$sql						=	new cbSqlQueryPart();
			$sql->tag					=	'where';
			$sql->type					=	'sql:formula';
			$sql->value					=	"EXISTS ( "
										.		"SELECT 1"
										.		" FROM " . $_CB_database->NameQuote( '#__fields_values' ) . " AS jf"
										.		" WHERE jf.`field_id` = " . $field->getInt( '_id', 0 )
										.		" AND jf.`item_id` = u.`id`"
										.		" AND " . $sqlValue->reduceSqlFormula( $tableReferences, $joinsSQL, true )
										.	" )";

			$query[]					=	$sql;
		}

		return $query;
	}

	/**
	 * Overrides $field data basesd off the Joomla field being rendered and returns the Joomla field
	 *
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @return null|JFormField
	 */
	private function getJoomlaField( FieldTable $field, UserTable $user ): ?JFormField
	{
		if ( checkJversion( '<4.0' ) ) {
			return null;
		}

		/** @var null|JFormField $joomlaField */
		$joomlaField					=	$field->getRaw( '_joomla' );

		if ( $joomlaField ) {
			// Already an overridden field so don't do anything else but return the Joomla field
			return $joomlaField;
		}

		/** @var JFormField[] $cache */
		static $cache					=	[];

		$joomlaFieldId					=	$field->params->getInt( 'joomla_field', 0 );

		if ( ! $joomlaFieldId ) {
			return null;
		}

		$userId							=	$user->getInt( 'id', 0 );
		$cacheId						=	$userId . ':' . $field->getInt( 'joomla_field', 0 ) . ':' . $joomlaFieldId;

		if ( \array_key_exists( $cacheId, $cache ) ) {
			return $cache[$cacheId];
		}

		$joomlaFieldName				=	( $this->getJoomlaFieldNames()[$joomlaFieldId] ?? '' );

		if ( ! $joomlaFieldName ) {
			$cache[$cacheId]			=	null;

			return null;
		}

		$joomlaField					=	( $this->getJoomlaFields( $user )[$joomlaFieldName] ?? null );

		if ( ! $joomlaField ) {
			$cache[$cacheId]			=	null;

			return null;
		}

		$field->set( '_id', $joomlaFieldId );
		$field->set( 'tablecolumns', null );
		$field->set( '_name', $field->getString( 'name', '' ) );
		$field->set( 'name', '_' . $joomlaField->id );

		if ( $field->params->getBool( 'joomla_field_title', true ) ) {
			$field->set( '_title', $field->getString( 'title', '' ) );
			$field->set( 'title', $joomlaField->title );
		}

		if ( $field->params->getBool( 'joomla_field_description', true ) ) {
			$description				=	( $joomlaField->description ?: '' );
			$description				=	( $description && $joomlaField->translateDescription ? Text::_( $description ) : $description );

			$field->set( '_description', $field->getHtml( 'description', '' ) );
			$field->set( 'description', $description );
		}

		if ( $field->params->getBool( 'joomla_field_required', true ) ) {
			$field->set( 'required', (int) $joomlaField->required );
		} else {
			$joomlaField->required		=	$field->getBool( 'required', false );
		}

		if ( $field->params->getBool( 'joomla_field_readonly', true ) ) {
			$field->set( 'readonly', (int) $joomlaField->readonly );
		} else {
			$joomlaField->readonly		=	$field->getBool( 'readonly', false );
		}

		if ( $field->getInt( 'size', 0 ) ) {
			$joomlaField->size			=	$field->getInt( 'size', 0 );
		}

		if ( $field->getString( 'cssclass', '' ) ) {
			$joomlaField->class			=	trim( $joomlaField->class . ' ' . $field->getString( 'cssclass', '' ) );
		}

		$field->set( '_joomla', $joomlaField );

		$fields[$joomlaFieldName]		=	$joomlaField;

		return $fields[$joomlaFieldName];
	}

	/**
	 * Returns a copy of the profile form, but only with this Joomla field in it so we can use form functions against its values
	 *
	 * @param JFormField $joomlaField
	 * @param UserTable  $user
	 * @return null|Form
	 */
	private function getJoomlaFieldForm( JFormField $joomlaField, UserTable $user ): ?Form
	{
		global $_CB_framework;

		static $cache			=	[];

		$cacheId				=	$user->getInt( 'id', 0 ) . ':' . $joomlaField->id;

		if ( \array_key_exists( $cacheId, $cache ) ) {
			return $cache[$cacheId];
		}

		try {
			$model				=	$this->getJoomlaModel( $user );
			$form				=	clone $model->getForm(); // We explicitly want a copy so we don't modify the cached profile form object

			foreach ( $form->getGroup( $joomlaField->group, true ) as $formField ) {
				if ( $formField->fieldname === $joomlaField->fieldname ) {
					continue;
				}

				$form->removeField( $formField->fieldname, $formField->group );
			}

			$cache[$cacheId]	=	$form;
		} catch ( \Exception $e ) {
			$_CB_framework->enqueueMessage( $e->getMessage(), 'error' );

			return null;
		}

		return $cache[$cacheId];
	}

	/**
	 * Returns Joomla field input data
	 *
	 * @param JFormField $joomlaField
	 * @param UserTable  $user
	 * @param array      $postdata
	 * @return Registry
	 */
	private function getJoomlaFieldPost( JFormField $joomlaField, UserTable $user, array $postdata = [] ): Registry
	{
		global $_CB_framework;

		static $cache			=	[];

		$cacheId				=	$user->getInt( 'id', 0 ) . ':' . $joomlaField->id;

		if ( \array_key_exists( $cacheId, $cache ) ) {
			return $cache[$cacheId];
		}

		try {
			$joomlaData			=	( $postdata['jform'] ?? [] );
			$joomlaData['id']	=	$user->getInt( 'id', 0 );
			$objData			=	(object) $joomlaData;

			Factory::getApplication()->triggerEvent( 'onContentNormaliseRequestData', [ 'com_users.user', $objData, $this->getJoomlaFieldForm( $joomlaField, $user ) ] );

			$cache[$cacheId]	=	new Registry( (array) $objData );
		} catch ( \Exception $e ) {
			$_CB_framework->enqueueMessage( $e->getMessage(), 'error' );

			return new Registry();
		}

		return $cache[$cacheId];
	}

	/**
	 * Returns the user fields from jcfields as a result of onContentPrepare
	 *
	 * @param UserTable $user
	 * @return JFormField[]
	 */
	private function getJoomlaUserFields( UserTable $user ): array
	{
		global $_CB_framework;

		static $cache			=	[];

		$userId					=	$user->getInt( 'id', 0 );

		if ( \array_key_exists( $userId, $cache ) ) {
			return $cache[$userId];
		}

		try {
			$joomlaUser			=	Application::Cms()->getCmsUser( $userId )->asCmsUser();

			JPluginHelper::importPlugin( 'content' );

			$joomlaUser->text	=	'';

			Factory::getApplication()->triggerEvent( 'onContentPrepare', [ 'com_users.user', &$joomlaUser, &$joomlaUser->params, 0 ] );

			unset( $joomlaUser->text );

			$cache[$userId]		=	[];

			foreach ( ( $joomlaUser->jcfields ?? [] ) as $customField ) {
				$cache[$userId][$customField->name]	=	$customField;
			}
		} catch ( \Exception $e ) {
			$_CB_framework->enqueueMessage( $e->getMessage(), 'error' );

			return [];
		}

		return $cache[$userId];
	}

	/**
	 * Returns the Joomla user model
	 *
	 * @param UserTable $user
	 * @return null|ProfileModel|UserModel
	 */
	private function getJoomlaModel( UserTable $user )
	{
		global $_CB_framework;

		static $cache			=	[];

		$userId					=	$user->getInt( 'id', 0 );

		if ( \array_key_exists( $userId, $cache ) ) {
			return $cache[$userId];
		}

		try {
			Form::addFormPath( JPATH_BASE . '/components/com_users/forms' );
			Form::addFormPath( JPATH_BASE . '/components/com_users/models/forms' );
			Form::addFieldPath( JPATH_BASE . '/components/com_users/models/fields' );
			Form::addFormPath( JPATH_BASE . '/components/com_users/model/form');
			Form::addFieldPath( JPATH_BASE . '/components/com_users/model/field' );

			JPluginHelper::importPlugin( 'user' );

			if ( Application::Application()->isClient( 'administrator' ) ) {
				/** @var UserModel $model */
				$model			=	Factory::getApplication()->bootComponent( 'com_users' )->getMVCFactory()->createModel( 'User', 'Administrator', [ 'ignore_request' => true ] );
			} else {
				/** @var ProfileModel $model */
				$model			=	Factory::getApplication()->bootComponent( 'com_users' )->getMVCFactory()->createModel( 'Profile', 'Site', [ 'ignore_request' => true ] );
			}

			$model->setState( 'user.id', $userId );

			$cache[$userId]		=	$model;
		} catch ( \Exception $e ) {
			$_CB_framework->enqueueMessage( $e->getMessage(), 'error' );

			return null;
		}

		return $cache[$userId];
	}

	/**
	 * Returns an array of available custom Joomla user profile fields
	 *
	 * @param UserTable $user
	 * @return JFormField[]
	 */
	private function getJoomlaFields( UserTable $user ): array
	{
		global $_CB_framework;

		static $cache	=	[];

		$userId			=	$user->getInt( 'id', 0 );

		if ( \array_key_exists( $userId, $cache ) ) {
			return $cache[$userId];
		}

		$jFields		=	[];

		try {
			$model					=	$this->getJoomlaModel( $user );

			if ( ! $model ) {
				return [];
			}

			$form					=	$model->getForm();

			foreach ( $form->getFieldsets() as $group => $fieldset ) {
				// Skip these fields as they're handled by userparams
				if ( \in_array( $group, [ 'core', 'params', 'privacyconsent', 'terms', 'user_details', 'settings', 'accessibility', 'actionlogs', 'joomlatoken' ], true ) ) {
					continue;
				}

				$fieldsetFields		=	$form->getFieldset( $group );

				if ( ! \count( $fieldsetFields ) ) {
					continue;
				}

				$jFields			+=	$fieldsetFields;
			}

			$cache[$userId]			=	[];

			/** @var JFormField $jFields */
			foreach ( $jFields as $jField ) {
				$cache[$userId][$jField->fieldname]		=	$jField;
			}
		} catch ( \Exception $e ) {
			$_CB_framework->enqueueMessage( $e->getMessage(), 'error' );

			return [];
		}

		return $cache[$userId];
	}

	/**
	 * Returns a id => name array of Joomla fields for converting from id to name
	 *
	 * @return array
	 */
	private function getJoomlaFieldNames(): array
	{
		global $_CB_database;

		static $cache	=	null;

		if ( $cache !== null ) {
			return $cache;
		}

		$query			=	'SELECT *'
						.	"\n FROM " . $_CB_database->NameQuote( '#__fields' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'context' ) . " = " . $_CB_database->Quote( 'com_users.user' );
		$_CB_database->setQuery( $query );
		$cache			=	$_CB_database->loadAssocList( 'id', 'name' );

		return $cache;
	}

	/**
	 * Returns an array of options for selection in a select field
	 *
	 * @return array
	 */
	public static function getJoomlaFieldOptions(): array
	{
		global $_CB_database;

		if ( checkJversion( '<4.0' ) ) {
			return [];
		}

		static $cache	=	null;

		if ( $cache !== null ) {
			return $cache;
		}

		$cache			=	[];

		$query			=	'SELECT *'
						.	"\n FROM " . $_CB_database->NameQuote( '#__fields' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'context' ) . " = " . $_CB_database->Quote( 'com_users.user' )
						.	"\n ORDER BY " . $_CB_database->NameQuote( 'ordering' ) . " ASC";
		$_CB_database->setQuery( $query );
		foreach ( $_CB_database->loadObjectList( 'id' ) as $field ) {
			$cache[]	=	moscomprofilerHTML::makeOption( (string) $field->id, Text::_( $field->title ) . ' (' . $field->name . ')' );
		}

		return $cache;
	}
}