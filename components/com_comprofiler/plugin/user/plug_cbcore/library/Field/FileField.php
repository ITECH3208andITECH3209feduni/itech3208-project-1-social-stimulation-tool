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
use cbSqlQueryPart;
use CBuser;
use cbValidator;
use moscomprofilerHTML;

\defined( 'CBLIB' ) or die();

class FileField extends cbFieldHandler
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
		$value					=	$user->get( $field->name );

		switch ( $output ) {
			case 'html':
			case 'rss':
				$return			=	$this->formatFieldValueLayout( $this->_fileLivePath( $field, $user, $reason ), $reason, $field, $user );
				break;
			case 'htmledit':
				if ( $reason == 'search' ) {
					$choices	=	array();
					$choices[]	=	moscomprofilerHTML::makeOption( '', CBTxt::T( 'UE_NO_PREFERENCE', 'No preference' ) );
					$choices[]	=	moscomprofilerHTML::makeOption( '1', CBTxt::T( 'Has a file' ) );
					$choices[]	=	moscomprofilerHTML::makeOption( '0', CBTxt::T( 'Has no file' ) );
					$html		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'select', $value, '', $choices, true, null, false );
					$return		=	$this->_fieldSearchModeHtml( $field, $user, $html, 'singlechoice', $list_compare_types );
				} else {
					$return		=	$this->formatFieldValueLayout( $this->_htmlEditForm( $field, $user, $reason ), $reason, $field, $user );
				}
				break;
			default:
				$fileUrl		=	$this->_fileLivePath( $field, $user, $reason, false );
				$return			=	$this->_formatFieldOutput( $field->name, $fileUrl, $output, false );
				break;
		}

		return $return;
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
		global $_CB_framework;

		if ( ( ! $user ) || ( ! in_array( $reason, array( 'profile', 'edit', 'list' ) ) ) || ( cbGetParam( $_GET, 'function', '' ) != 'download' ) || ( ! $user->id ) ) {
			return null; // wrong reason, wrong function, or user doesn't exist; do nothing
		}

		$col					=	$field->name;
		$file					=	$user->$col;

		if ( ! $file ) {
			return null; // nothing to download; do nothing
		}

		if ( $reason == 'edit' ) {
			$redirect_url		=	$_CB_framework->userProfileEditUrl( $user->id, false );
		} elseif ( $reason == 'list' ) {
			$redirect_url		=	$_CB_framework->userProfilesListUrl( cbGetParam( $_REQUEST, 'listid', 0 ), false );
		} else {
			$redirect_url		=	$_CB_framework->userProfileUrl( $user->id, false );
		}

		$clean_file				=	preg_replace( '/[^-a-zA-Z0-9_.]/u', '', $file );
		$file_path				=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/plug_cbfilefield/' . (int) $user->id . '/' . $clean_file;

		if ( ! file_exists( $file_path ) ) {
			cbRedirect( $redirect_url, CBTxt::T( 'File failed to download! Error: File not found' ), 'error' );
			exit();
		}

		$file_ext				=	strtolower( pathinfo( $clean_file, PATHINFO_EXTENSION ) );

		if ( ! $file_ext ) {
			cbRedirect( $redirect_url, CBTxt::T( 'File failed to download! Error: Unknown extension' ), 'error' );
			exit();
		}

		$file_name				=	substr( rtrim( pathinfo( $clean_file, PATHINFO_BASENAME ), '.' . $file_ext ), 0, -14 );
		$file_name_custom		=	$field->params->get( 'fieldFile_filename' );

		if ( $file_name_custom ) {
			$file_name			=	cbReplaceVars( $file_name_custom, $user, true, false, array( 'filename' => $file_name, 'reason' => $reason ) );
		}

		$file_name				=	$file_name . '.' . $file_ext;

		if ( ! $file_name ) {
			cbRedirect( $redirect_url, CBTxt::T( 'File failed to download! Error: File not found' ), 'error' );
			exit();
		}

		$file_mime				=	cbGetMimeFromExt( $file_ext );

		if ( $file_mime == 'application/octet-stream' ) {
			cbRedirect( $redirect_url, CBTxt::T( 'File failed to download! Error: Unknown MIME' ), 'error' );
			exit();
		}

		$file_size				=	@filesize( $file_path );
		$file_modified			=	$_CB_framework->getUTCDate( 'r', filemtime( $file_path ) );

		while ( @ob_end_clean() );

		if ( ini_get( 'zlib.output_compression' ) ) {
			ini_set( 'zlib.output_compression', 'Off' );
		}

		if ( function_exists( 'apache_setenv' ) ) {
			apache_setenv( 'no-gzip', '1' );
		}

		header( "Content-Type: $file_mime" );
		header( 'Content-Disposition: ' . ( $field->params->get( 'fieldFile_force', 0 ) ? 'attachment' : 'inline' ) . '; filename="' . $file_name . '"; modification-date="' . $file_modified . '"; size=' . $file_size .';' );
		header( "Content-Transfer-Encoding: binary" );
		header( "Expires: 0" );
		header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header( "Pragma: public" );
		header( "Content-Length: $file_size" );

		if ( ! ini_get( 'safe_mode' ) ) {
			@set_time_limit( 0 );
		}

		$handle					=	fopen( $file_path, 'rb' );

		if ( $handle === false ) {
			exit();
		}

		$chunksize				=	( 1 * ( 1024 * 1024 ) );

		while ( ! feof( $handle ) ) {
			$buffer				=	fread( $handle, $chunksize );
			echo $buffer;
			@ob_flush();
			flush();
		}

		fclose( $handle );
		exit();
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
		global $_CB_framework, $_CB_database;

		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		$col					=	$field->name;
		$col_choice				=	$col . '__choice';
		$col_file				=	$col . '__file';
		$choice					=	stripslashes( cbGetParam( $postdata, $col_choice ) );

		switch ( $choice ) {
			case 'upload':
				$value			=	( isset( $_FILES[$col_file] ) ? $_FILES[$col_file] : null );

				$this->validate( $field, $user, $choice, $value, $postdata, $reason );
				break;
			case 'delete':
				if ( $user->id && ( $user->$col != null ) && ( $user->$col != '' ) ) {
					if ( isset( $user->$col ) ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->$col, '' );
					}

					$this->deleteFiles( $user, $user->$col );

					$user->$col	=	null;

					// This is needed because user store does not save null:
					if ( $field->table ) {
						$query	=	'UPDATE ' . $_CB_database->NameQuote( $field->table )
								.	"\n SET " . $_CB_database->NameQuote( $col ) . " = NULL"
								.	', ' . $_CB_database->NameQuote( 'lastupdatedate' ) . ' = ' . $_CB_database->Quote( $_CB_framework->dateDbOfNow() )
								.	"\n WHERE " . $_CB_database->NameQuote( 'id' ) . " = " . (int) $user->id;
						$_CB_database->setQuery( $query );
						$_CB_database->query();
					}
				}
				break;
			default:
				$value			=	$user->get( $col );

				$this->validate( $field, $user, $choice, $value, $postdata, $reason );
				break;
		}
	}

	/**
	 * Mutator:
	 * Prepares field data commit
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save user edit, 'register' for save registration
	 */
	public function commitFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		global $_CB_framework, $_PLUGINS, $_FILES;

		$col						=	$field->name;
		$col_choice					=	$col . '__choice';
		$col_file					=	$col . '__file';
		$choice						=	stripslashes( cbGetParam( $postdata, $col_choice ) );

		switch ( $choice ) {
			case 'upload':
				$value				=	( isset( $_FILES[$col_file] ) ? $_FILES[$col_file] : null );

				if ( $this->validate( $field, $user, $choice, $value, $postdata, $reason ) ) {
					$_PLUGINS->loadPluginGroup( 'user' );

					$_PLUGINS->trigger( 'onBeforeUserFileUpdate', array( &$user, &$value['tmp_name'] ) );

					if ( $_PLUGINS->is_errors() ) {
						$this->_setErrorMSG( $_PLUGINS->getErrorMSG() );
						return;
					}

					$path			=	$_CB_framework->getCfg( 'absolute_path' );
					$index_path		=	$path . '/components/com_comprofiler/plugin/user/plug_cbfilefield/index.html';
					$files_path		=	$path . '/images/comprofiler/plug_cbfilefield';
					$file_path		=	$files_path . '/' . (int) $user->id;

					if ( ! is_dir( $files_path ) ) {
						$oldmask	=	@umask( 0 );

						if ( @mkdir( $files_path, 0755, true ) ) {
							@umask( $oldmask );
							@chmod( $files_path, 0755 );

							if ( ! file_exists( $files_path . '/index.html' ) ) {
								@copy( $index_path, $files_path . '/index.html' );
								@chmod( $files_path . '/index.html', 0755 );
							}
						} else {
							@umask( $oldmask );
						}
					}

					if ( ! file_exists( $files_path . '/.htaccess' ) ) {
						file_put_contents( $files_path . '/.htaccess', 'deny from all' );
					}

					if ( ! is_dir( $file_path ) ) {
						$oldmask	=	@umask( 0 );

						if ( @mkdir( $file_path, 0755, true ) ) {
							@umask( $oldmask );
							@chmod( $file_path, 0755 );

							if ( ! file_exists( $file_path . '/index.html' ) ) {
								@copy( $index_path, $file_path . '/index.html' );
								@chmod( $file_path . '/index.html', 0755 );
							}
						} else {
							@umask( $oldmask );
						}
					}

					$uploaded_name	=	preg_replace( '/[^-a-zA-Z0-9_]/u', '', pathinfo( $value['name'], PATHINFO_FILENAME ) );
					$uploaded_ext	=	strtolower( preg_replace( '/[^-a-zA-Z0-9_]/u', '', pathinfo( $value['name'], PATHINFO_EXTENSION ) ) );
					$newFileName	=	uniqid( $uploaded_name . '_' ). '.' . $uploaded_ext;

					if ( ! move_uploaded_file( $value['tmp_name'], $file_path . '/'. $newFileName ) ) {
						$this->_setValidationError( $field, $user, $reason, sprintf( CBTxt::T( 'CBFile-failed to upload file: %s' ), $newFileName ) );
						return;
					} else {
						@chmod( $file_path . '/' . $value['tmp_name'], 0755 );
					}

					if ( isset( $user->$col ) ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->$col, '' );
					}

					if ( isset( $user->$col ) && ( $user->$col != '' ) ) {
						$this->deleteFiles( $user, $user->$col );
					}

					$user->$col		=	$newFileName;

					$_PLUGINS->trigger( 'onAfterUserFileUpdate', array( &$user, $newFileName ) );
				}
				break;
		}
	}

	/**
	 * Mutator:
	 * Prepares field data rollback
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save user edit, 'register' for save registration
	 */
	public function rollbackFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		global $_FILES;

		$col			=	$field->name;
		$col_choice		=	$col . '__choice';
		$col_file		=	$col . '__file';

		$choice			=	stripslashes( cbGetParam( $postdata, $col_choice ) );

		switch ( $choice ) {
			case 'upload':
				$value	=	( isset( $_FILES[$col_file] ) ? $_FILES[$col_file] : null );

				if ( $this->validate( $field, $user, $choice, $value, $postdata, $reason ) ) {
					$this->deleteFiles( $user, $user->$col );
				}
				break;
		}
	}

	/**
	 * outputs a secure list of allowed file extensions
	 *
	 * @param  string  $extensions
	 * @return array
	 */
	function allowedExtensions( $extensions = 'zip,rar,doc,pdf,txt,xls' )
	{
		$allowed			=	explode( ',', $extensions );

		if ( $allowed ) {
			$not_allowed	=	array( 'php', 'php3', 'php4', 'php5', 'asp', 'exe', 'py' );

			foreach ( $not_allowed as $extension ) {
				$key		=	array_search( $extension, $allowed );

				if ( $key ) {
					unset( $allowed[$key] );
				}
			}
		}

		return $allowed;
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
	 * @return boolean                            True if validate, $this->_setErrorMSG if False
	 */
	public function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason )
	{
		$isRequired							=	$this->_isRequired( $field, $user, $reason );

		switch ( $columnName ) {
			case 'upload':
				if ( ! isset( $value['tmp_name'] ) || empty( $value['tmp_name'] ) || ( $value['error'] != 0 ) || ( ! is_uploaded_file( $value['tmp_name'] ) ) ) {
					if ( $isRequired ) {
						$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'Please select a file before uploading' ) );
					}

					return false;
				} else {
					$upload_size_limit_max	=	(int) $field->params->get( 'fieldValidateFile_sizeMax', 1024 );
					$upload_size_limit_min	=	(int) $field->params->get( 'fieldValidateFile_sizeMin', 0 );
					$upload_ext_limit		=	$this->allowedExtensions( $field->params->get( 'fieldValidateFile_types', 'zip,rar,doc,pdf,txt,xls' ) );

					$uploaded_name_empty	=	( '' === pathinfo( $value['name'], PATHINFO_FILENAME ) );

					if ( $uploaded_name_empty ) {
						$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'Please select a file before uploading' ) );
						return false;
					}

					$uploaded_ext			=	strtolower( preg_replace( '/[^-a-zA-Z0-9_]/u', '', pathinfo( $value['name'], PATHINFO_EXTENSION ) ) );

					if ( ( ! $uploaded_ext ) || ( ! in_array( $uploaded_ext, $upload_ext_limit ) ) ) {
						$this->_setValidationError( $field, $user, $reason, sprintf( CBTxt::T( 'Please upload only %s' ), implode( ', ', $upload_ext_limit ) ) );
						return false;
					}

					$uploaded_size			=	$value['size'];

					if ( ( $uploaded_size / 1024 ) > $upload_size_limit_max ) {
						$this->_setValidationError( $field, $user, $reason, sprintf( CBTxt::T( 'The file size exceeds the maximum of %s' ), $this->formattedFileSize( $upload_size_limit_max * 1024 ) ) );
						return false;
					}

					if ( ( $uploaded_size / 1024 ) < $upload_size_limit_min ) {
						$this->_setValidationError( $field, $user, $reason, sprintf( CBTxt::T( 'The file is too small, the minimum is %s' ), $this->formattedFileSize( $upload_size_limit_min * 1024 ) ) );
						return false;
					}
				}
				break;
			default:
				$valCol						=	$field->name;

				if ( $isRequired && ( ( ! $user ) || ( ! isset( $user->$valCol ) ) || ( ! $user->$valCol ) ) ) {
					if ( ! $value ) {
						$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'UE_FIELDREQUIRED', 'This Field is required' ) );
						return false;
					}
				}
				break;
		}

		return true;
	}

	/**	 * Finder:
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
		$query					=	array();
		$searchMode				=	$this->_bindSearchMode( $field, $searchVals, $postdata, 'none', $list_compare_types );
		$col					=	$field->name;
		$value					=	cbGetParam( $postdata, $col );

		if ( $value === '0' ) {
			$value				=	0;
		} elseif ( $value == '1' ) {
			$value				=	1;
		} else {
			$value				=	null;
		}

		if ( $value !== null ) {
			$searchVals->$col	=	$value;

			// When is not advanced search is used we need to invert our search:
			if ( $searchMode == 'isnot' ) {
				if ( $value === 0 ) {
					$value		=	1;
				} elseif ( $value == 1 ) {
					$value		=	0;
				}
			}

			$sql				=	new cbSqlQueryPart();
			$sql->tag			=	'column';
			$sql->name			=	$col;
			$sql->table			=	$field->table;
			$sql->type			=	'sql:field';
			$sql->operator		=	$value ? 'IS NOT' : 'IS';
			$sql->value			=	'NULL';
			$sql->valuetype		=	'const:null';
			$sql->searchmode	=	$searchMode;

			$query[]			=	$sql;
		}

		return $query;
	}

	/**
	 * Returns full URL of the file
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $reason
	 * @param  bool        $html
	 * @return null|string
	 */
	function _fileLivePath( &$field, &$user, $reason, $html = true )
	{
		global $_CB_framework;

		$oValue					=	null;

		if ( $user && $user->id ) {
			$fieldName			=	$field->get( 'name' );
			$value				=	$user->get( $fieldName );
			$fileName			=	null;

			if ( $value != null ) {
				$cleanFile		=	preg_replace( '/[^-a-zA-Z0-9_.]/u', '', $value );
				$fileExt		=	strtolower( pathinfo( $cleanFile, PATHINFO_EXTENSION ) );
				$fileName		=	substr( rtrim( pathinfo( $cleanFile, PATHINFO_BASENAME ), '.' . $fileExt ), 0, -14 );
				$fileNameCustom	=	$field->params->get( 'fieldFile_filename' );

				if ( $fileNameCustom ) {
					$fileName	=	cbReplaceVars( $fileNameCustom, $user, true, false, array( 'filename' => $fileName, 'reason' => $reason ) );
				}

				$fileName		=	$fileName . '.' . $fileExt;
				$oValue			=	'/images/comprofiler/plug_cbfilefield/' . (int) $user->id . '/' . $cleanFile;
			}

			if ( $oValue ) {
				$oValue			=	'index.php?option=com_comprofiler&view=fieldclass&field=' . urlencode( $fieldName ) . '&function=download&user=' . (int) $user->id . '&reason=' . urlencode( $reason );

				if ( $_CB_framework->getUi() == 2 ) {
					$oValue		=	$_CB_framework->backendUrl( $oValue, true );
				} else {
					$oValue		=	cbSef( $oValue, true );
				}

				if ( $html ) {
					$oValue		=	' <a href="' . $oValue . '" title="' . htmlspecialchars( CBTxt::T( 'Click or right-click filename to download' ) ) . '" target="_blank" rel="nofollow noopener noreferrer">' . $fileName . '</a>';
				}
			}
		}

		return $oValue;
	}

	/**
	 *
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $reason             'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  boolean     $displayFieldIcons
	 * @return string                          HTML: <tag type="$type" value="$value" xxxx="xxx" yy="y" />
	 */
	function _htmlEditForm( &$field, &$user, $reason, $displayFieldIcons = true )
	{
		global $_CB_framework;

		$fieldName							=	$field->get( 'name' );
		$value								=	$user->get( $fieldName );
		$required							=	$this->_isRequired( $field, $user, $reason );

		$uploadSizeLimitMax					=	$field->params->get( 'fieldValidateFile_sizeMax', 1024 );
		$uploadSizeLimitMin					=	$field->params->get( 'fieldValidateFile_sizeMin', 0 );
		$uploadExtLimit						=	$this->allowedExtensions( $field->params->get( 'fieldValidateFile_types', 'zip,rar,doc,pdf,txt,xls' ) );
		$restrictions						=	array();

		if ( $uploadExtLimit ) {
			$restrictions[]					=	CBTxt::Th( 'FILE_UPLOAD_LIMITS_EXT', 'Your file must be of [ext] type.', array( '[ext]' => implode( ', ', $uploadExtLimit ) ) );
		}

		if ( $uploadSizeLimitMin ) {
			$restrictions[]					=	CBTxt::Th( 'FILE_UPLOAD_LIMITS_MIN', 'Your file should exceed [size].', array( '[size]' => $this->formattedFileSize( $uploadSizeLimitMin * 1024 ) ) );
		}

		if ( $uploadSizeLimitMax ) {
			$restrictions[]					=	CBTxt::Th( 'FILE_UPLOAD_LIMITS_MAX', 'Your file should not exceed [size].', array( '[size]' => $this->formattedFileSize( $uploadSizeLimitMax * 1024 ) ) );
		}

		$existingFile						=	( $user->id ? ( ( $value != null ) ? true : false ) : false );
		$choices							=	array();

		if ( ( $reason == 'register' ) || ( ( $reason == 'edit' ) && ( $user->id == 0 ) ) ) {
			if ( $required == 0 ) {
				$choices[]					=	moscomprofilerHTML::makeOption( '', CBTxt::T( 'No file' ) );
			}
		} else {
			if ( $existingFile || ( $required == 0 ) ) {
				$choices[]					=	moscomprofilerHTML::makeOption( '', CBTxt::T( 'No change of file' ) );
			}
		}

		$choices[]							=	moscomprofilerHTML::makeOption( 'upload', ( $existingFile ? CBTxt::T( 'Upload new file' ) : CBTxt::T( 'Upload file' ) ) );

		if ( $existingFile && ( $required == 0 ) ) {
			$choices[]						=	moscomprofilerHTML::makeOption( 'delete', CBTxt::T( 'Remove file' ) );
		}

		$return								=	null;

		if ( ( $reason != 'register' ) && ( $user->id != 0 ) && $existingFile ) {
			$return							.=	'<div class="row no-gutters mb-3 cbFileFieldDownload">' . $this->_fileLivePath( $field, $user, $reason ) . '</div>';
		}

		$hasChoices							=	( count( $choices ) > 1 );

		if ( $hasChoices ) {
			static $functOut				=	false;

			$additional						=	' class="form-control cbFileFieldChoice"';

			if ( ( $_CB_framework->getUi() == 1 ) && ( $reason == 'edit' ) && $field->readonly ) {
				$additional					.=	' disabled="disabled"';
			}

			$translatedTitle				=	$this->getFieldTitle( $field, $user, 'html', $reason );
			$htmlDescription				=	$this->getFieldDescription( $field, $user, 'htmledit', $reason );
			$trimmedDescription				=	trim( strip_tags( $htmlDescription ) );
			$inputDescription				=	$field->params->get( 'fieldLayoutInputDesc', 1, GetterInterface::INT );

			$tooltip						=	( $trimmedDescription && $inputDescription ? cbTooltip( $_CB_framework->getUi(), $htmlDescription, $translatedTitle, null, null, null, null, $additional ) : $additional );

			$return							.=	'<div class="form-group mb-0 cb_form_line">'
											.		moscomprofilerHTML::selectList( $choices, $fieldName . '__choice', $tooltip, 'value', 'text', null, $required, true, null, false )
											.		$this->_fieldIconsHtml( $field, $user, 'htmledit', $reason, 'select', '', null, '', array(), $displayFieldIcons, $required )
											.	'</div>';

			if ( ! $functOut ) {
				$js							=	"$.fn.cbslideFile = function() {"
											.		"var element = $( this );"
											.		"element.on( 'click.cbfilefield change.cbfilefield', function() {"
											.			"if ( ( $( this ).val() == '' ) || ( $( this ).val() == 'delete' ) ) {"
											.				"element.parent().siblings( '.cbFileFieldUpload' ).addClass( 'hidden' ).find( 'input' ).prop( 'disabled', true );"
											.			"} else if ( $( this ).val() == 'upload' ) {"
											.				"element.parent().siblings( '.cbFileFieldUpload' ).removeClass( 'hidden' ).find( 'input' ).prop( 'disabled', false );"
											.			"}"
											.		"}).on( 'cloned.cbfilefield', function() {"
											.			"$( this ).parent().siblings( '.cbFileFieldDownload' ).remove();"
											.			"if ( $( this ).parent().siblings( '.cbFileFieldUpload' ).find( 'input.required' ).length ) {"
											.				"$( this ).find( 'option[value=\"\"]' ).remove();"
											.			"}"
											.			"$( this ).find( 'option[value=\"delete\"]' ).remove();"
											.			"$( this ).off( '.cbfilefield' );"
											.			"$( this ).cbslideFile();"
											.		"}).change();"
											.		"return this;"
											.	"};";

				$_CB_framework->outputCbJQuery( $js );

				$functOut					=	true;
			}

			$_CB_framework->outputCbJQuery( "$( '#" . addslashes( $fieldName ) . "__choice' ).cbslideFile();" );
		} else {
			$return							.=	'<input type="hidden" name="' . htmlspecialchars( $fieldName ) . '__choice" value="' . htmlspecialchars( $choices[0]->value ) . '" />';
		}

		$validationAttributes				=	array();
		$validationAttributes[]				=	cbValidator::getRuleHtmlAttributes( 'extension', implode( ',', $uploadExtLimit ) );

		if ( $uploadSizeLimitMin || $uploadSizeLimitMax ) {
			$validationAttributes[]			=	cbValidator::getRuleHtmlAttributes( 'filesize', array( $uploadSizeLimitMin, $uploadSizeLimitMax, 'KB' ) );
		}

		$return								.=	'<div id="cbfile_upload_' . htmlspecialchars( $fieldName ) . '" class="form-group mb-0 cb_form_line' . ( $hasChoices ? ' mt-3 hidden' : null ) . ' cbFileFieldUpload">'
											.		( $restrictions ? '<div class="mb-2">' . implode( ' ', $restrictions ) . '</div>' : null )
											.		'<div>'
											.			CBTxt::T( 'Select file' ) . ' <input type="file" name="' . htmlspecialchars( $fieldName ) . '__file" value="" class="form-control' . ( $required == 1 ? ' required' : null ) . '"' . implode( ' ', $validationAttributes ) . ( $hasChoices ? ' disabled="disabled"' : null ) . ' />'
											.			( count( $choices ) <= 0 ? $this->_fieldIconsHtml( $field, $user, 'htmledit', $reason, 'select', '', null, '', array(), $displayFieldIcons, $required ) : null )
											.		'</div>'
											.		'<div class="mt-2">';

		if ( $field->params->get( 'fieldFile_terms', 0 ) ) {
			$cbUser							=	CBuser::getMyInstance();
			$termsOutput					=	$field->params->get( 'terms_output', 'url' );
			$termsType						=	$cbUser->replaceUserVars( $field->params->get( 'terms_type', 'TERMS_AND_CONDITIONS' ) );
			$termsDisplay					=	$field->params->get( 'terms_display', 'modal' );
			$termsURL						=	cbSef( $cbUser->replaceUserVars( $field->params->get( 'terms_url', null ) ), false );
			$termsText						=	$cbUser->replaceUserVars( $field->params->get( 'terms_text', null ) );
			$termsWidth						=	$field->params->get( 'terms_width', 400 );
			$termsHeight					=	$field->params->get( 'terms_height', 200 );

			if ( ! $termsType ) {
				$termsType					=	CBTxt::T( 'TERMS_AND_CONDITIONS', 'Terms and Conditions' );
			}

			if ( ! $termsWidth ) {
				$termsWidth					=	400;
			}

			if ( ! $termsHeight ) {
				$termsHeight				=	200;
			}

			if ( ( ( $termsOutput == 'url' ) && $termsURL ) || ( ( $termsOutput == 'text' ) && $termsText ) ) {
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

					$return					.=			CBTxt::Th( 'BY_UPLOADING_YOU_CERTIFY_THAT_YOU_HAVE_THE_RIGHT_TO_DISTRIBUTE_THIS_FILE_TERMS', 'By uploading, you certify that you have the right to distribute this file and that it does not violate the above [type].', array( '[type]' => $termsType ) );
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

					$return					.=			CBTxt::Th( 'BY_UPLOADING_YOU_CERTIFY_THAT_YOU_HAVE_THE_RIGHT_TO_DISTRIBUTE_THIS_FILE_URL_TERMS', 'By uploading, you certify that you have the right to distribute this file and that it does not violate the <a href="[url]"[attributes]>[type]</a>', array( '[url]' => $url, '[attributes]' => $attributes, '[type]' => $termsType ) );
				}
			} else {
				$return						.=			CBTxt::Th( 'BY_UPLOADING_YOU_CERTIFY_THAT_YOU_HAVE_THE_RIGHT_TO_DISTRIBUTE_THIS_FILE', 'By uploading, you certify that you have the right to distribute this file.' );
			}
		} else {
			$return							.=			CBTxt::Th( 'BY_UPLOADING_YOU_CERTIFY_THAT_YOU_HAVE_THE_RIGHT_TO_DISTRIBUTE_THIS_FILE', 'By uploading, you certify that you have the right to distribute this file.' );
		}

		$return								.=		'</div>'
											.	'</div>';

		return $return;
	}

	/**
	 * Deletes file from users folder
	 *
	 * @param  UserTable  $user
	 * @param  string     $file
	 */
	function deleteFiles( $user, $file = null )
	{
		global $_CB_framework;

		if ( ! is_object( $user ) ) {
			return;
		}

		$file_path	=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/plug_cbfilefield/' . (int) $user->id . '/';

		if ( ! is_dir( $file_path ) ) {
			return;
		}

		if ( ! $file ) {
			if ( false !== ( $handle = opendir( $file_path ) ) ) {
				while ( false !== ( $file = readdir( $handle ) ) ) {
					if ( $file && ( ( $file != '.' ) && ( $file != '..' ) ) ) {
						@unlink( $file_path . $file );
					}
				}
				closedir( $handle );
			}

			if ( is_dir( $file_path ) ) {
				@rmdir( $file_path );
			}
		} else {
			if ( file_exists( $file_path . $file ) ) {
				@unlink( $file_path . $file );
			}
		}
	}

	/**
	 * Returns file size formatted from bytes
	 *
	 * @param int $bytes
	 * @return string
	 */
	private function formattedFileSize( $bytes )
	{
		if ( $bytes >= 1099511627776 ) {
			$size	=	CBTxt::Th( 'FILESIZE_FORMATTED_TB', '%%COUNT%% TB|%%COUNT%% TBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1099511627776, 2, '.', '' ) ) );
		} elseif ( $bytes >= 1073741824 ) {
			$size	=	CBTxt::Th( 'FILESIZE_FORMATTED_GB', '%%COUNT%% GB|%%COUNT%% GBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1073741824, 2, '.', '' ) ) );
		} elseif ( $bytes >= 1048576 ) {
			$size	=	CBTxt::Th( 'FILESIZE_FORMATTED_MB', '%%COUNT%% MB|%%COUNT%% MBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1048576, 2, '.', '' ) ) );
		} elseif ( $bytes >= 1024 ) {
			$size	=	CBTxt::Th( 'FILESIZE_FORMATTED_KB', '%%COUNT%% KB|%%COUNT%% KBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1024, 2, '.', '' ) ) );
		} else {
			$size	=	CBTxt::Th( 'FILESIZE_FORMATTED_B', '%%COUNT%% B|%%COUNT%% Bs', array( '%%COUNT%%' => (float) number_format( $bytes, 2, '.', '' ) ) );
		}

		return $size;
	}
}