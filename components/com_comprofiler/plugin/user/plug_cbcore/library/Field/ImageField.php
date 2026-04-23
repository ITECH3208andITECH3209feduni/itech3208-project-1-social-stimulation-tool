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
use CBLib\Image\Color;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use cbNotification;
use cbSqlQueryPart;
use CBuser;
use cbValidator;
use Exception;
use moscomprofilerHTML;

\defined( 'CBLIB' ) or die();

class ImageField extends cbFieldHandler
{
	/**
	 * @param  FieldTable  $field
	 * @param  string      $name
	 * @param  null        $default
	 * @return null|string
	 */
	function _getImageFieldParam( &$field, $name, $default = null )
	{
		global $ueConfig;

		$fieldDefault				=	'';

		if ( $field->getString( 'name' ) === 'avatar' ) {
			switch ( $name ) {
				case 'avatarHeight':
					$fieldDefault	=	160;
					break;
				case 'avatarWidth':
					$fieldDefault	=	160;
					break;
				case 'thumbHeight':
					$fieldDefault	=	80;
					break;
				case 'thumbWidth':
					$fieldDefault	=	80;
					break;
			}
		} elseif ( $field->getString( 'name' ) === 'canvas' ) {
			switch ( $name ) {
				case 'avatarHeight':
					$fieldDefault	=	640;
					break;
				case 'avatarWidth':
					$fieldDefault	=	1280;
					break;
				case 'thumbHeight':
					$fieldDefault	=	320;
					break;
				case 'thumbWidth':
					$fieldDefault	=	640;
					break;
			}
		}

		$paramValue					=	$field->params->get( $name, $fieldDefault );

		if ( $paramValue == '' ) {
			if ( isset( $ueConfig[$name] ) ) {
				$paramValue			=	$ueConfig[$name];
			} else {
				$paramValue			=	$default;
			}
		}

		return $paramValue;
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

		switch ( $output ) {
			case 'html':
			case 'rss':
				$thumbnail			=	$field->get( '_imageThumbnail', ( $reason != 'profile' ) );
				$oReturn			=	$this->_avatarHtml( $field, $user, $reason, $thumbnail, 2 );

				$name				=	$field->name;
				$nameapproved		=	$field->name . 'approved';
				//Application::MyUser()->isSuperAdmin()
				if ( ( $reason == 'profile' ) && ( $user->$name != '' ) && ( $user->$nameapproved == 0 ) && Application::MyUser()->isModeratorFor( Application::User( (int) $user->id ) ) ) {
					$oReturn		=	'<span class="cbImagePendingApproval">'
									.		$oReturn . ' ' . $this->_avatarHtml( $field, $user, $reason, false, 10 )
									.		'<div class="cbImagePendingApprovalButtons">'
									.			'<input type="button" class="btn btn-sm btn-success cbImagePendingApprovalAccept" value="' . htmlspecialchars( CBTxt::Th( 'UE_APPROVE', 'Approve' ) ) . '" onclick="location.href=\'' . $_CB_framework->viewUrl( 'approveimage', true, array( 'flag' => 1, 'images[' . (int) $user->id . '][]' => $name ) ) . '\';" />'
									.			' <input type="button" class="btn btn-sm btn-danger cbImagePendingApprovalReject" value="' . htmlspecialchars( CBTxt::Th( 'UE_REJECT', 'Reject' ) ) . '" onclick="location.href=\'' . $_CB_framework->viewUrl( 'approveimage', true, array( 'flag' => 0, 'images[' . (int) $user->id . '][]' => $name ) ) . '\';" />'
									.		'</div>'
									.	'</span>';
				}
				$oReturn			=	$this->formatFieldValueLayout( $oReturn, $reason, $field, $user );
				break;

			case 'htmledit':
				if ( $reason == 'search' ) {
					$choices		=	array();
					$choices[]		=	moscomprofilerHTML::makeOption( '', CBTxt::T( 'UE_NO_PREFERENCE', 'No preference' ) );
					$choices[]		=	moscomprofilerHTML::makeOption( '1', CBTxt::T( 'UE_HAS_PROFILE_IMAGE', 'Has a profile image' ) );
					$choices[]		=	moscomprofilerHTML::makeOption( '0', CBTxt::T( 'UE_HAS_NO_PROFILE_IMAGE', 'Has no profile image' ) );
					$col			=	$field->name;
					$value			=	$user->$col;
					$html			=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'select', $value, '', $choices, true, null, false );
					$html			=	$this->_fieldSearchModeHtml( $field, $user, $html, 'singlechoice', $list_compare_types );		//TBD: Has avatarapproved...
				} else {
					$html			=	$this->formatFieldValueLayout( $this->_htmlEditForm( $field, $user, $reason ), $reason, $field, $user );
				}
				return $html;
			case 'json':
			case 'php':
			case 'xml':
			case 'csvheader':
			case 'fieldslist':
			case 'csv':
			default:
				$thumbnail			=	$field->get( '_imageThumbnail', ( $reason != 'profile' ) );
				$imgUrl				=	$this->_avatarLivePath( $field, $user, $thumbnail );
				$oReturn			=	$this->_formatFieldOutput( $field->name, $imgUrl, $output );
				break;
		}

		return $oReturn;
	}

	/**
	 * Parses $_FILES for the image file or its hidden data input
	 *
	 * @param FieldTable $field
	 * @param array      $postdata
	 * @return array|null
	 */
	public function getImageFile( $field, $postdata )
	{
		global $_CB_framework, $_FILES;

		$col							=	$field->name;
		$col_file						=	$col . '__file';
		$col_file_data					=	$col . '__file_image_data';

		$file							=	( isset( $_FILES[$col_file] ) ? $_FILES[$col_file] : null );

		if ( ( ! $file ) && $field->params->get( 'image_client_resize', 1, GetterInterface::INT ) ) {
			$dataFile					=	stripslashes( cbGetParam( $postdata, $col_file_data ) );

			if ( $dataFile && preg_match( '%^data:(image/[A-Za-z]+);base64,(.+)%', $dataFile, $matches ) ) {
				$mimeTypes				=	array( 'image/png' => 'png', 'image/jpeg' => 'jpg', 'image/gif' => 'gif' );

				if ( isset( $mimeTypes[$matches[1]] ) ) {
					$name				=	md5( uniqid( rand(), true ) );
					$tmpPath			=	$_CB_framework->getCfg( 'tmp_path' );
					$tmpName			=	null;
					$size				=	0;

					if ( ! is_dir( $tmpPath ) ) {
						$error			=	UPLOAD_ERR_NO_TMP_DIR;
					} else {
						$tmpFile		=	$tmpPath . '/' . $name . '.tmp';
						$error			=	UPLOAD_ERR_OK;

						if ( file_put_contents( $tmpFile, base64_decode( $matches[2] ) ) === false ) {
							$error		=	UPLOAD_ERR_NO_FILE;
						} else {
							$tmpName	=	$tmpFile;
							$size		=	@filesize( $tmpName );
						}
					}

					$file				=	array(	'name'		=>	md5( $matches[2] ) . '.' . $mimeTypes[$matches[1]],
													'type'		=>	$matches[1],
													'tmp_name'	=>	$tmpName,
													'error'		=>	$error,
													'size'		=>	$size,
													'is_data'	=>	true
												);
				}
			}
		}

		return $file;
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
		global $_CB_framework;

		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		$col										=	$field->name;
		$colapproved								=	$col . 'approved';
		$colposition								=	$col . 'position';
		$col_choice									=	$col . '__choice';
		$col_gallery								=	$col . '__gallery';
		$col_position								=	$col . '__position';

		$choice										=	stripslashes( cbGetParam( $postdata, $col_choice ) );

		switch ( $choice ) {
			case 'upload':
				$value								=	$this->getImageFile( $field, $postdata );

				// Image is uploaded in the commit, but lets validate it here as well:
				$this->validate( $field, $user, $choice, $value, $postdata, $reason );
				break;
			case 'gallery':
				$newAvatar							=	stripslashes( cbGetParam( $postdata, $col_gallery ) );

				if ( $this->validate( $field, $user, $choice, $newAvatar, $postdata, $reason ) ) {
					$value							=	'gallery/' . $newAvatar;

					if ( isset( $user->$col ) ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );

						deleteAvatar( $user->$col ); // delete old avatar
					}

					$user->$col							=	$value;
					$user->$colapproved					=	1;

					if ( $col == 'canvas' ) {
						$user->$colposition				=	50;
					}
				}
				break;
			case 'delete':
				if ( $user->id && ( $user->$col != null ) && ( $user->$col != '' ) ) {
					global $_CB_database;

					if ( isset( $user->$col ) ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->$col, '' );

						deleteAvatar( $user->$col ); // delete old avatar
					}

					$user->$col						=	null; // this will not update, so we do query below:
					$user->$colapproved				=	1;

					if ( $col == 'canvas' ) {
						$user->$colposition			=	50;
					}

					// This is needed because user store does not save null:
					if ( $field->table ) {
						$query						=	'UPDATE ' . $_CB_database->NameQuote( $field->table )
													.	"\n SET " . $_CB_database->NameQuote( $col )			  . ' = NULL'
													.	', '	  . $_CB_database->NameQuote( $col . 'approved' ) . ' = 1'
													.	', '	  . $_CB_database->NameQuote( 'lastupdatedate' )  . ' = ' . $_CB_database->Quote( $_CB_framework->dateDbOfNow() )
													.	"\n WHERE " . $_CB_database->NameQuote( 'id' )			  . ' = ' . (int) $user->id;
						$_CB_database->setQuery( $query );
						$_CB_database->query();
					}
				}
				break;
			case 'approve':
				if ( isset( $user->$col ) && ( $_CB_framework->getUi() == 2 ) && $user->id && ( $user->$col != null ) && ( $user->$colapproved == 0 ) ) {
					$this->_logFieldUpdate( $field, $user, $reason, '', $user->$col );	// here we are missing the old value, so can't give it...

					$user->$colapproved				=	1;
					$user->lastupdatedate			=	$_CB_framework->dateDbOfNow();

					$cbNotification					=	new cbNotification();
					$cbNotification->sendFromSystem( $user, CBTxt::T( 'UE_IMAGEAPPROVED_SUB', 'Image Approved' ), CBTxt::T( 'UE_IMAGEAPPROVED_MSG', 'Your image has been approved by a moderator.' ) );
				}
				break;
			case 'position':
				if ( $user->id && ( $col == 'canvas' ) && ( $user->$col != null ) && ( $user->$col != '' ) && $user->$colapproved ) {
					$position						=	stripslashes( cbGetParam( $postdata, $col_position ) );

					if ( $position != '' ) {
						$this->_logFieldUpdate( $field, $user, $reason, '', $user->$col );	// here we are missing the old value, so can't give it...

						if ( $position < 0 ) {
							$position				=	0;
						} elseif ( $position > 100 ) {
							$position				=	100;
						}

						$user->$colposition			=	(int) $position;
					}
				}
				break;
			default:
				$value								=	$user->get( $col );

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
		global $_CB_framework, $ueConfig, $_PLUGINS;

		$col										=	$field->name;
		$colapproved								=	$col . 'approved';
		$colposition								=	$col . 'position';
		$col_choice									=	$col . '__choice';

		$choice										=	stripslashes( cbGetParam( $postdata, $col_choice ) );

		switch ( $choice ) {
			case 'upload':
				$value								=	$this->getImageFile( $field, $postdata );

				if ( $this->validate( $field, $user, $choice, $value, $postdata, $reason ) ) {
					$_PLUGINS->loadPluginGroup( 'user' );

					$isModerator					=	Application::MyUser()->isModeratorFor( Application::User( (int) $user->id ) );

					$_PLUGINS->trigger( 'onBeforeUserAvatarUpdate', array( &$user, &$user, $isModerator, &$value['tmp_name'] ) );
					if ( $_PLUGINS->is_errors() ) {
						$this->_setErrorMSG( $_PLUGINS->getErrorMSG() );
					}

					$conversionType					=	(int) ( isset( $ueConfig['conversiontype'] ) ? $ueConfig['conversiontype'] : 0 );
					$imageSoftware					=	( $conversionType == 5 ? 'gmagick' : ( $conversionType == 1 ? 'imagick' : ( $conversionType == 4 ? 'gd' : 'auto' ) ) );
					$imagePath						=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/';
					$fileName						=	( $col == 'avatar' ? '' : $col . '_' ) . uniqid( $user->id . '_' );

					try {
						$image						=	new \CBLib\Image\Image( $imageSoftware, $this->_getImageFieldParam( $field, 'avatarResizeAlways', 1 ), $this->_getImageFieldParam( $field, 'avatarMaintainRatio', 1 ) );

						$image->setName( $fileName );
						$image->setSource( $value );
						$image->setDestination( $imagePath );

						$image->processImage( $this->_getImageFieldParam( $field, 'avatarWidth', 200 ), $this->_getImageFieldParam( $field, 'avatarHeight', 500 ) );

						$newFileName				=	$image->getCleanFilename();

						$image->setName( 'tn' . $fileName );

						$image->processImage( $this->_getImageFieldParam( $field, 'thumbWidth', 60 ), $this->_getImageFieldParam( $field, 'thumbHeight', 86 ) );

						if ( isset( $value['is_data'] ) && file_exists( $value['tmp_name'] ) ) {
							@unlink( $value['tmp_name'] );
						}
					} catch ( Exception $e ) {
						$this->_setValidationError( $field, $user, $reason, $e->getMessage() );

						if ( isset( $value['is_data'] ) && file_exists( $value['tmp_name'] ) ) {
							@unlink( $value['tmp_name'] );
						}

						return;
					}

					$uploadApproval					=	$this->_getImageFieldParam( $field, 'avatarUploadApproval', 1 );

					if ( isset( $user->$col ) && ( ! ( ( $uploadApproval == 1 ) && ! $isModerator ) ) ) {
						// if auto-approved:				//TBD: else need to log update on image approval !
						$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $newFileName );
					}

					if ( isset( $user->$col ) && ( $user->$col != '' ) ) {
						deleteAvatar( $user->$col );
					}

					if ( ( $uploadApproval == 1 ) && ! $isModerator ) {
						$cbNotification				=	new cbNotification();
						$cbNotification->sendToModerators( cbReplaceVars( CBTxt::T( 'UE_IMAGE_ADMIN_SUB', 'Image Pending Approval' ), $user ), cbReplaceVars( CBTxt::T( 'UE_IMAGE_ADMIN_MSG', 'A user has submitted an image for approval. Please log in and take the appropriate action.'), $user ) );

						$user->$col					=	$newFileName;
						$user->$colapproved			=	0;
					} else {
						$user->$col					=	$newFileName;
						$user->$colapproved			=	1;
					}

					if ( $col == 'canvas' ) {
						$user->$colposition			=	50;
					}

					$_PLUGINS->trigger( 'onAfterUserAvatarUpdate', array( &$user, &$user, $isModerator, $newFileName ) );
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
		$col				=	$field->name;
		$col_choice			=	$col . '__choice';

		$choice				=	stripslashes( cbGetParam( $postdata, $col_choice ) );

		switch ( $choice ) {
			case 'upload':
				$value		=	$this->getImageFile( $field, $postdata );

				if ( $this->validate( $field, $user, $choice, $value, $postdata, $reason ) ) {
					deleteAvatar( $user->$col );
				}
				break;
		}
	}

	/**	 * Validator:
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
	public function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason )
	{
		global $_CB_framework;

		$isRequired							=	$this->_isRequired( $field, $user, $reason );

		switch ( $columnName ) {
			case 'upload':
				if ( ! $field->params->get( 'image_allow_uploads', 1 ) ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'UE_NOT_AUTHORIZED', 'You are not authorized to view this page!' ) );

					if ( isset( $value['is_data'] ) && file_exists( $value['tmp_name'] ) ) {
						@unlink( $value['tmp_name'] );
					}

					return false;
				} elseif ( ! isset( $value['tmp_name'] ) || empty( $value['tmp_name'] ) || ( $value['error'] != 0 ) || ( ( ! is_uploaded_file( $value['tmp_name'] ) ) && ( ! isset( $value['is_data'] ) ) ) ) {
					if ( $isRequired ) {
						$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'Please select a image file before uploading' ) );
					}

					if ( isset( $value['is_data'] ) && file_exists( $value['tmp_name'] ) ) {
						@unlink( $value['tmp_name'] );
					}

					return false;
				} else {
					$upload_size_limit_max	=	(int) $this->_getImageFieldParam( $field, 'avatarSize', 2000 );
					$upload_ext_limit		=	array( 'jpg', 'jpeg', 'gif', 'png' );
					$uploaded_ext			=	strtolower( preg_replace( '/[^-a-zA-Z0-9_]/u', '', pathinfo( $value['name'], PATHINFO_EXTENSION ) ) );

					if ( ( ! $uploaded_ext ) || ( ! in_array( $uploaded_ext, $upload_ext_limit ) ) ) {
						$this->_setValidationError( $field, $user, $reason, sprintf( CBTxt::T( 'Please upload only %s' ), implode( ', ', $upload_ext_limit ) ) );

						if ( isset( $value['is_data'] ) && file_exists( $value['tmp_name'] ) ) {
							@unlink( $value['tmp_name'] );
						}

						return false;
					}

					$uploaded_size			=	$value['size'];

					if ( ( $uploaded_size / 1024 ) > $upload_size_limit_max ) {
						$this->_setValidationError( $field, $user, $reason, sprintf( CBTxt::T( 'The image file size exceeds the maximum of %s' ), $this->formattedFileSize( $upload_size_limit_max * 1024 ) ) );

						if ( isset( $value['is_data'] ) && file_exists( $value['tmp_name'] ) ) {
							@unlink( $value['tmp_name'] );
						}

						return false;
					}
				}
				break;
			case 'gallery':
				if ( ! $field->params->get( 'image_allow_gallery', ( in_array( $field->name, array( 'avatar', 'canvas' ) ) ? 1 : 0 ) ) ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'UE_NOT_AUTHORIZED', 'You are not authorized to view this page!' ) );
					return false;
				}

				$galleryPath				=	$field->params->get( 'image_gallery_path', null );

				if ( ! $galleryPath ) {
					if ( $field->get( 'name' ) == 'canvas' ) {
						$galleryPath		=	'/images/comprofiler/gallery/canvas';
					} else {
						$galleryPath		=	'/images/comprofiler/gallery';
					}
				}

				$galleryImages				=	$this->displayImagesGallery( $_CB_framework->getCfg( 'absolute_path' ) . $galleryPath, 'all' );

				if ( ! in_array( $value, $galleryImages ) ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'UE_UPLOAD_ERROR_CHOOSE', 'You didn\'t choose an image from the gallery.' ) . $value );
					return false;
				}
				break;
			default:
				$valCol			=	$field->name;
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
		$query								=	array();
		$searchMode							=	$this->_bindSearchMode( $field, $searchVals, $postdata, 'none', $list_compare_types );
		$col								=	$field->name;
		$colapproved						=	$col . 'approved';
		$value								=	cbGetParam( $postdata, $col );

		if ( $value === '0' ) {
			$value							=	0;
		} elseif ( $value == '1' ) {
			$value							=	1;
		} else {
			$value							=	null;
		}

		if ( $value !== null ) {
			$searchVals->$col				=	$value;

			// When is not advanced search is used we need to invert our search:
			if ( $searchMode == 'isnot' ) {
				if ( $value === 0 ) {
					$value					=	1;
				} elseif ( $value == 1 ) {
					$value					=	0;
				}
			}

			$sql							=	new cbSqlQueryPart();
			$sql->tag						=	'column';
			$sql->name						=	$colapproved;
			$sql->table						=	$field->table;
			$sql->type						=	'sql:operator';
			$sql->operator					=	$value ? 'AND' : 'OR';
			$sql->searchmode				=	$searchMode;

			$sqlpict						=	new cbSqlQueryPart();
			$sqlpict->tag					=	'column';
			$sqlpict->name					=	$col;
			$sqlpict->table					=	$field->table;
			$sqlpict->type					=	'sql:field';
			$sqlpict->operator				=	$value ? 'IS NOT' : 'IS';
			$sqlpict->value					=	'NULL';
			$sqlpict->valuetype				=	'const:null';
			$sqlpict->searchmode			=	$searchMode;

			$sqlapproved					=	new cbSqlQueryPart();
			$sqlapproved->tag				=	'column';
			$sqlapproved->name				=	$colapproved;
			$sqlapproved->table				=	$field->table;
			$sqlapproved->type				=	'sql:field';
			$sqlapproved->operator			=	$value ? '>' : '=';
			$sqlapproved->value				=	0;
			$sqlapproved->valuetype			=	'const:int';
			$sqlapproved->searchmode		=	$searchMode;

			$sql->addChildren( array( $sqlpict, $sqlapproved ) );

			$query[]						=	$sql;
		}

		return $query;
	}

	/**
	 * returns full or thumbnail image tag
	 *
	 * @param  FieldTable   $field
	 * @param  UserTable    $user
	 * @param  string       $reason
	 * @param  boolean      $thumbnail
	 * @param  int          $showAvatar
	 * @return null|string
	 */
	function _avatarHtml( &$field, &$user, $reason, $thumbnail = true, $showAvatar = 2 )
	{
		global $_CB_framework;

		switch ( $field->params->getInt( 'titleText', 0 ) ) {
			case 2:
				$title			=	cbReplaceVars( $field->params->getHTML( 'titleTextCustom' ), $user, true, true, array( 'reason' => $reason ) );
				break;
			case 1:
				$title			=	null;
				break;
			default:
				if ( $field->getString( 'name' ) === 'avatar' ) {
					if ( $user && $user->getInt( 'id' ) ) {
						$title	=	$user->getFormattedName();
					} else {
						$title	=	null;
					}
				} elseif ( $field->getString( 'name' ) === 'canvas' ) {
					$title		=	null;
				} else {
					$title		=	cbReplaceVars( $field->getHTML( 'title' ), $user, true, true, array( 'reason' => $reason ) );		// does htmlspecialchars()
				}
				break;
		}

		$approved				=	( $user->getBool( $field->getString( 'name' ) . 'approved', true ) || ( $showAvatar === 10 ) );
		$displayMode			=	$field->params->getString( ( ! $approved ? 'pendingDefaultAvatar' : 'defaultAvatar' ), ( in_array( $field->getString( 'name' ), array( 'avatar', 'canvas' ) ) ? 'initial' : '' ) );
		$imgUrl					=	$this->_avatarLivePath( $field, $user, $thumbnail, $showAvatar );

		if ( ( ! $imgUrl ) && ( ! in_array( $displayMode, [ 'initial', 'color' ], true ) ) ) {
			return null;
		}

		if ( $field->getString( 'name' ) === 'canvas' ) {
			if ( $imgUrl ) {
				if ( $approved && ( $user->getString( $field->getString( 'name' ) ) != '' ) ) {
					$position		=	$user->getInt( $field->getString( 'name' ) . 'position', 50 );

					if ( $position < 0 ) {
						$position	=	0;
					} elseif ( $position > 100 ) {
						$position	=	100;
					}
				} else {
					$position		=	50;
				}

				return '<div style="background-image: url(' . $imgUrl . '); background-position-y: ' . $position . '%;"' . ( $title ? ' title="' . htmlspecialchars( $title ) . '"' : null ) . ' class="cbImgCanvas' . ( ! $approved ? ' cbImgCanvasPending' : null ) . ( $thumbnail ? ' cbThumbCanvas' : ' cbFullCanvas' ) . '"></div>';
			}

			if ( $displayMode === 'color' ) {
				$color				=	$field->params->getString( ( ! $approved ? 'pendingDefaultAvatar' : 'defaultAvatar' ) . 'Color', '#000000' );
				$gradient			=	$field->params->getString( ( ! $approved ? 'pendingDefaultAvatar' : 'defaultAvatar' ) . 'ColorGradient', '#000000' );

				if ( $color === $gradient ) {
					$color			=	'background-color: ' . htmlspecialchars( $color );
				} else {
					$color			=	'background: linear-gradient( 0deg, ' . htmlspecialchars( $color ) . ' 0%, ' . htmlspecialchars( $gradient ) . ' 100% );"';
				}
			} else {
				$color				=	'background: linear-gradient( 0deg, ' . htmlspecialchars( Color::stringToHex( $user->getFormattedName() ) ) . ' 0%, ' . htmlspecialchars( Color::stringToHex( $user->getFormattedName(), 0.9 ) ) . ' 100% );"';
			}

			return '<div style="' . $color . ';"' . ( $title ? ' title="' . htmlspecialchars( $title ) . '"' : null ) . ' class="cbImgCanvas cbImgCanvasInitial' . ( ! $approved ? ' cbImgCanvasPending' : null ) . ( $thumbnail ? ' cbThumbCanvas' : ' cbFullCanvas' ) . '"></div>';
		}

		switch ( $field->params->getInt( 'altText', 0 ) ) {
			case 2:
				$alt			=	cbReplaceVars( $field->params->getHTML( 'altTextCustom' ), $user, true, true, array( 'reason' => $reason ) );
				break;
			case 1:
				$alt			=	null;
				break;
			default:
				if ( $field->getString( 'name' ) === 'avatar' ) {
					if ( $user && $user->getInt( 'id' ) ) {
						$alt	=	$user->getFormattedName();
					} else {
						$alt	=	null;
					}
				} else {
					$alt		=	cbReplaceVars( $field->getHTML( 'title' ), $user, true, true, array( 'reason' => $reason ) );		// does htmlspecialchars()
				}
				break;
		}

		switch ( $field->params->getString( 'imageStyle', ( $field->getString( 'name' ) === 'avatar' ? 'circlebordered' : '' ) ) ) {
			case 'rounded':
				$style			=	' rounded';
				break;
			case 'roundedbordered':
				$style			=	' img-thumbnail';
				break;
			case 'circle':
				$style			=	' rounded-circle';
				break;
			case 'circlebordered':
				$style			=	' img-thumbnail rounded-circle';
				break;
			default:
				$style			=	null;
				break;
		}

		if ( $field->getString( 'name' ) === 'avatar' ) {
			$style				.=	' cbImgAvatar';
		}

		$profileLink			=	$user->getBool( '_allowProfileLink', $field->getBool( '_allowProfileLink' ) ); // For B/C

		if ( $profileLink === null ) {
			$profileLink		=	$field->params->getBool( 'fieldProfileLink', true );
		}

		if ( $profileLink && ( ! in_array( $reason, array( 'profile', 'edit' ) ) ) && $user && $user->getInt( 'id' ) ) {
			$openTag			=	'<a href="' . $_CB_framework->userProfileUrl( $user->getInt( 'id' ), true, ( $field->getString( 'name' ) === 'avatar' ? null : $field->getInt( 'tabid' ) ) ) . '">';
			$closeTag			=	'</a>';
		} else {
			$openTag			=	null;
			$closeTag			=	null;
		}

		$return 				=	$openTag;

		if ( $imgUrl ) {
			$return				.=	'<img src="' . $imgUrl . '"' . ( $alt ? ' alt="' . htmlspecialchars( $alt ) . '"' : null ) . ( $title ? ' title="' . htmlspecialchars( $title ) . '"' : null ) . ' class="cbImgPict' . ( ! $approved ? ' cbImgPictPending' : null ) . ( $thumbnail ? ' cbThumbPict' : ' cbFullPict' ) . $style . '" />';
		} else {
			if ( in_array( Application::Config()->getInt( 'name_format', 3 ), array( 1, 2, 4, 7, 8, 9, 10, 11 ), true ) ) {
				$initials		=	cbIsoUtf_strtoupper( $user->getFormattedName( 9 ) );
			} else {
				$initials		=	cbIsoUtf_strtoupper( cbutf8_substr( $user->getFormattedName(), 0, 1 ) );
			}

			if ( $displayMode === 'color' ) {
				$color			=	$field->params->getString( ( ! $approved ? 'pendingDefaultAvatar' : 'defaultAvatar' ) . 'Color', '#000000' );
			} else {
				$color			=	Color::stringToHex( $user->getFormattedName() );
			}

			$return				.=	'<svg viewBox="0 0 100 100" class="cbImgPict cbImgPictInitial' . ( ! $approved ? ' cbImgPictPending' : null ) . ( $thumbnail ? ' cbThumbPict' : ' cbFullPict' ) . $style . '">'
								.		'<rect fill="' . htmlspecialchars( $color ) . '" width="100" height="100" cx="50" cy="50" r="50" />'
								.		'<text x="50%" y="50%" style="color: #ffffff; line-height: 1;" alignment-baseline="middle" text-anchor="middle" font-size="40" font-weight="600" dy="0.1em" dominant-baseline="middle" fill="#ffffff">'
								.			$initials
								.		'</text>'
								.	'</svg>';
		}

		$return					.=	$closeTag;

		return $return;
	}

	/**
	 * returns full or thumbnail path of image
	 *
	 * @param  FieldTable   $field
	 * @param  UserTable    $user
	 * @param  boolean      $thumbnail
	 * @param  int          $showAvatar
	 * @param  boolean      $absolute
	 * @return null|string
	 */
	function _avatarLivePath( &$field, &$user, $thumbnail = true, $showAvatar = 2, $absolute = false )
	{
		global $_CB_framework;

		$liveSite						=	$_CB_framework->getCfg( 'live_site' );
		$absolutePath					=	$_CB_framework->getCfg( 'absolute_path' );
		$fieldName						=	$field->get( 'name' );
		$approvedFieldName				=	$fieldName . 'approved';

		if ( $user && $user->id ) {
			$value						=	$user->get( $fieldName );
			$approvedValue				=	$user->get( $approvedFieldName );
		} else {
			$value						=	null;
			$approvedValue				=	1;
		}

		$tn								=	( $thumbnail ? 'tn' : null );
		$return							=	null;

		if ( ( $value != '' ) && ( ( $approvedValue > 0 ) || ( $showAvatar == 10 ) ) ) {
			if ( strpos( $value, 'gallery/' ) === false ) {
				$return					=	'/images/comprofiler/' . $tn . $value;
			} else {
				$galleryPath			=	$field->params->get( 'image_gallery_path', null );

				if ( ! $galleryPath ) {
					if ( $fieldName == 'canvas' ) {
						$galleryPath	=	'/images/comprofiler/gallery/canvas';
					} else {
						$galleryPath	=	'/images/comprofiler/gallery';
					}
				}

				$return					=	$galleryPath . '/' . preg_replace( '!^gallery/(tn)?!', ( $tn ? 'tn' : '' ), $value );

				if ( ! is_file( $absolutePath . $return ) ) {
					$return				=	$galleryPath . '/' . preg_replace( '!^gallery/!', '', $value );
				}
			}

			if ( ! is_file( $absolutePath . $return ) ) {
				$return					=	null;
			}
		}

		if ( ( $return === null ) && ( $showAvatar == 2 ) ) {
			$imagesBase					=	'avatar';

			if ( $field->name == 'canvas' ) {
				$imagesBase				=	'canvas';
			}

			$imageDefault				=	( in_array( $field->getString( 'name' ), array( 'avatar', 'canvas' ) ) ? 'initial' : '' );

			if ( $approvedValue == 0 ) {
				$icon					=	$field->params->get( 'pendingDefaultAvatar', $imageDefault );

				if ( ( $icon == 'none' ) || ( $icon == 'initial' ) || ( $icon == 'color' ) ) {
					return null;
				} elseif ( $icon ) {
					if ( ( $icon != 'pending_n.png' ) && ( ! is_file( selectTemplate( 'absolute_path' ) . '/images/' . $imagesBase . '/' . $tn . $icon ) ) ) {
						$icon			=	null;
					}
				}

				if ( ! $icon ) {
					$icon				=	'pending_n.png';
				}
			} else {
				$icon					=	$field->params->get( 'defaultAvatar', $imageDefault );

				if ( ( $icon == 'none' ) || ( $icon == 'initial' ) || ( $icon == 'color' ) ) {
					return null;
				} elseif ( $icon ) {
					if ( ( $icon != 'nophoto_n.png' ) && ( ! is_file( selectTemplate( 'absolute_path' ) . '/images/' . $imagesBase . '/' . $tn . $icon ) ) ) {
						$icon			=	null;
					}
				}

				if ( ! $icon ) {
					$icon				=	'nophoto_n.png';
				}
			}

			// Image doesn't exist in the template; check default template:
			if ( ! is_file( selectTemplate( 'absolute_path' ) . '/images/' . $imagesBase . '/' . $tn . $icon ) ) {
				// Image doesn't exist in the default template so return null to suppress display:
				if ( ! is_file( selectTemplate( 'absolute_path', 'default' ) . '/images/' . $imagesBase . '/' . $tn . $icon ) ) {
					return null;
				}

				return ( $absolute ? selectTemplate( 'absolute_path', 'default' ) . '/' : selectTemplate( 'live_site', 'default' ) ) . 'images/' . $imagesBase . '/' . $tn . $icon;
			}

			return ( $absolute ? selectTemplate( 'absolute_path' ) . '/' : selectTemplate() ) . 'images/' . $imagesBase . '/' . $tn . $icon;
		}

		if ( $return ) {
			$return						=	( $absolute ? $absolutePath : $liveSite ) . $return;
		}

		return $return;
	}

	/**
	 * returns html edit display of image field
	 *
	 * @param  FieldTable   $field
	 * @param  UserTable    $user
	 * @param  string       $reason
	 * @param  boolean      $displayFieldIcons
	 * @return null|string
	 */
	function _htmlEditForm( &$field, &$user, $reason, $displayFieldIcons = true )
	{
		global $_CB_framework;

		$fieldName								=	$field->get( 'name' );

		if ( ! ( $field->params->get( 'image_allow_uploads', 1 ) || $field->params->get( 'image_allow_gallery', ( in_array( $fieldName, array( 'avatar', 'canvas' ) ) ? 1 : 0 ) ) ) ) {
			return null;
		}

		$approvedFieldName						=	$fieldName . 'approved';
		$value									=	$user->get( $fieldName );
		$approvedValue							=	$user->get( $approvedFieldName );
		$required								=	$this->_isRequired( $field, $user, $reason );

		$uploadWidthLimit						=	$this->_getImageFieldParam( $field, 'avatarWidth', 500 );
		$uploadHeightLimit						=	$this->_getImageFieldParam( $field, 'avatarHeight', 200 );
		$uploadSizeLimitMax						=	$this->_getImageFieldParam( $field, 'avatarSize', 2000 );
		$uploadExtLimit							=	array( 'gif', 'png', 'jpg', 'jpeg' );
		$restrictions							=	array();

		if ( $uploadExtLimit ) {
			$restrictions[]						=	CBTxt::Th( 'IMAGE_FILE_UPLOAD_LIMITS_EXT', 'Your image file must be of [ext] type.', array( '[ext]' => implode( ', ', $uploadExtLimit ) ) );
		}

		if ( $uploadSizeLimitMax ) {
			$restrictions[]						=	CBTxt::Th( 'IMAGE_FILE_UPLOAD_LIMITS_MAX', 'Your image file should not exceed [size].', array( '[size]' => $this->formattedFileSize( $uploadSizeLimitMax * 1024 ) ) );
		}

		if ( $uploadWidthLimit ) {
			$restrictions[]						=	CBTxt::Th( 'IMAGE_FILE_UPLOAD_LIMITS_WIDTH', 'Images exceeding the maximum width of [size] will be resized.', array( '[size]' => $uploadWidthLimit ) );
		}

		if ( $uploadHeightLimit ) {
			$restrictions[]						=	CBTxt::Th( 'IMAGE_FILE_UPLOAD_LIMITS_HEIGHT', 'Images exceeding the maximum height of [size] will be resized.', array( '[size]' => $uploadHeightLimit ) );
		}

		$existingFile							=	( $user->get( 'id' ) ? ( ( $value != null ) ? true : false ) : false );
		$choices								=	array();

		if ( ( $reason == 'register' ) || ( ( $reason == 'edit' ) && ( $user->id == 0 ) ) ) {
			if ( $required == 0 ) {
				$choices[]						=	moscomprofilerHTML::makeOption( '', CBTxt::T( 'No image' ) );
			}
		} else {
			if ( $existingFile || ( $required == 0 ) ) {
				$choices[]						=	moscomprofilerHTML::makeOption( '', CBTxt::T( 'No change of image' ) );
			}
		}

		$selected								=	null;

		if ( ( $required == 1 ) && ( ! $existingFile ) ) {
			$selected							=	( $field->params->get( 'image_allow_uploads', 1 ) ? 'upload' : 'gallery' );
		}

		if ( $field->params->get( 'image_allow_uploads', 1 ) ) {
			$choices[]							=	moscomprofilerHTML::makeOption( 'upload', ( $existingFile ? CBTxt::T( 'Upload new image' ) : CBTxt::T( 'Upload image' ) ) );
		}

		if ( $field->params->get( 'image_allow_gallery', ( in_array( $fieldName, array( 'avatar', 'canvas' ) ) ? 1 : 0 ) ) ) {
			$choices[]							=	moscomprofilerHTML::makeOption( 'gallery', ( $existingFile ? CBTxt::T( 'Select new image from gallery' ) : CBTxt::T( 'Select image from gallery' ) ) );
		}

		if ( ( $_CB_framework->getUi() == 2 ) && $existingFile && ( $approvedValue == 0 ) ) {
			$choices[]							=	moscomprofilerHTML::makeOption( 'approve', CBTxt::T( 'Approve image' ) );
		}

		$canReposition							=	false;

		if ( ( $fieldName == 'canvas' ) && ( $reason == 'edit' ) && $existingFile && $approvedValue ) {
			$canvasSize							=	@getimagesize( $this->_avatarLivePath( $field, $user, false, 2, true ) );

			if ( ( $canvasSize !== false ) && ( $canvasSize[1] > 200 ) ) {
				$canReposition					=	true;

				$choices[]						=	moscomprofilerHTML::makeOption( 'position', CBTxt::T( 'Reposition image' ) );
			}
		}

		if ( $existingFile && ( $required == 0 ) ) {
			$choices[]							=	moscomprofilerHTML::makeOption( 'delete', CBTxt::T( 'Remove image' ) );
		}

		$return									=	null;

		if ( ( $reason != 'register' ) && ( $user->id != 0 ) && $existingFile ) {
			$return								.=	'<div class="mb-3 cbImageFieldImage">' . $this->_avatarHtml( $field, $user, $reason ) . '</div>';
		}

		if ( ( $reason == 'edit' ) && $existingFile && ( $approvedValue == 0 ) && Application::MyUser()->isModeratorFor( Application::User( (int) $user->id ) ) ) {
			$return								.=	'<div class="mb-3 cbImageFieldImage">' . $this->_avatarHtml( $field, $user, $reason, false, 10 ) . '</div>';
		}

		$hasChoices								=	( count( $choices ) > 1 );

		if ( $hasChoices ) {
			static $functOut					=	false;

			$additional							=	' class="form-control cbImageFieldChoice"';

			if ( ( $_CB_framework->getUi() == 1 ) && ( $reason == 'edit' ) && $field->get( 'readonly' ) ) {
				$additional						.=	' disabled="disabled"';
			}

			$translatedTitle					=	$this->getFieldTitle( $field, $user, 'html', $reason );
			$htmlDescription					=	$this->getFieldDescription( $field, $user, 'htmledit', $reason );
			$trimmedDescription					=	trim( strip_tags( $htmlDescription ) );
			$inputDescription					=	$field->params->get( 'fieldLayoutInputDesc', 1, GetterInterface::INT );

			$tooltip							=	( $trimmedDescription && $inputDescription ? cbTooltip( $_CB_framework->getUi(), $htmlDescription, $translatedTitle, null, null, null, null, $additional ) : $additional );

			$return								.=	'<div class="form-group mb-0 cb_form_line">'
												.		moscomprofilerHTML::selectList( $choices, $fieldName . '__choice', $tooltip, 'value', 'text', $selected, $required, true, null, false )
												.		$this->_fieldIconsHtml( $field, $user, 'htmledit', $reason, 'select', '', null, '', array(), $displayFieldIcons, $required )
												.	'</div>';

			if ( ! $functOut ) {
				$js								=	"$.fn.cbslideImageFile = function() {"
												.		"var element = $( this );"
												.		"element.on( 'click.cbimagefield change.cbimagefield', function() {"
												.			"if ( ( $( this ).val() == '' ) || ( $( this ).val() == 'delete' ) ) {"
												.				"element.parent().siblings( '.cbImageFieldUpload,.cbImageFieldGallery,.cbImageFieldPosition' ).addClass( 'hidden' ).find( 'input' ).prop( 'disabled', true );"
												.			"} else if ( $( this ).val() == 'upload' ) {"
												.				"element.parent().siblings( '.cbImageFieldUpload' ).removeClass( 'hidden' ).find( 'input' ).prop( 'disabled', false );"
												.				"element.parent().siblings( '.cbImageFieldGallery,.cbImageFieldPosition' ).addClass( 'hidden' ).find( 'input' ).prop( 'disabled', true );"
												.			"} else if ( $( this ).val() == 'gallery' ) {"
												.				"element.parent().siblings( '.cbImageFieldGallery' ).removeClass( 'hidden' ).find( 'input' ).prop( 'disabled', false );"
												.				"element.parent().siblings( '.cbImageFieldUpload,.cbImageFieldPosition' ).addClass( 'hidden' ).find( 'input' ).prop( 'disabled', true );"
												.			"} else if ( $( this ).val() == 'position' ) {"
												.				"element.parent().siblings( '.cbImageFieldPosition' ).removeClass( 'hidden' ).find( 'input' ).prop( 'disabled', false );"
												.				"element.parent().siblings( '.cbImageFieldUpload,.cbImageFieldGallery' ).addClass( 'hidden' ).find( 'input' ).prop( 'disabled', true );"
												.				"element.parent().siblings( '.cbImageFieldPosition' ).find( '.cbCanvasRepositionSelect' ).draggable({"
												.					"containment: 'parent',"
												.					"scroll: false,"
												.					"axes: 'y',"
												.					"create: function() {"
												.						"$( this ).css({"
												.							"height: ( ( 200 / $( this ).parent().height() ) * 100 ) + '%',"
												.							"width: '100%'"
												.						"});"
												.						"var top = element.parent().siblings( '.cbImageFieldPosition' ).find( 'input' ).val();"
												.						"if ( top != '' ) {"
												.							"if ( top < 0 ) {"
												.								"top = 0;"
												.							"} else if ( top > 100 ) {"
												.								"top = 100;"
												.							"}"
												.							"top = ( ( $( this ).parent().height() / 2 ) * ( top / 100 ) );"
												.						"} else {"
												.							"top = ( ( $( this ).parent().height() / 2 ) - ( $( this ).height() / 2 ) );"
												.						"}"
												.						"$( this ).css( 'top', top + 'px' );"
												.					"},"
												.					"stop: function( e, ui ) {"
												.						"element.parent().siblings( '.cbImageFieldPosition' ).find( 'input' ).val( ( 100 / ( ( $( this ).parent().height() - $( this ).height() ) / ui.position.top ) ).toFixed( 0 ) );"
												.					"}"
												.				"});"
												.			"}"
												.		"}).on( 'cloned.cbimagefield', function() {"
												.			"$( this ).parent().siblings( '.cbImageFieldImage' ).remove();"
												.			"if ( $( this ).parent().siblings( '.cbImageFieldUpload,.cbImageFieldGallery' ).find( 'input.required' ).length ) {"
												.				"$( this ).find( 'option[value=\"\"]' ).remove();"
												.			"}"
												.			"$( this ).find( 'option[value=\"delete\"]' ).remove();"
												.			"$( this ).find( 'option[value=\"position\"]' ).remove();"
												.			"$( this ).off( '.cbimagefield' );"
												.			"$( this ).cbslideImageFile();"
												.		"}).change();"
												.		"return this;"
												.	"};";

				$_CB_framework->outputCbJQuery( $js, 'ui-all' );

				$functOut					=	true;
			}

			$_CB_framework->outputCbJQuery( "$( '#" . addslashes( $fieldName ) . "__choice' ).cbslideImageFile();" );
		} else {
			$return								.=	'<input type="hidden" name="' . htmlspecialchars( $fieldName ) . '__choice" value="' . htmlspecialchars( $choices[0]->value ) . '" />';
		}

		if ( $field->params->get( 'image_allow_uploads', 1 ) ) {
			$validationAttributes				=	array();
			$validationAttributes[]				=	cbValidator::getRuleHtmlAttributes( 'extension', implode( ',', $uploadExtLimit ) );

			if ( $uploadSizeLimitMax ) {
				$validationAttributes[]			=	cbValidator::getRuleHtmlAttributes( 'filesize', array( 0, $uploadSizeLimitMax, 'KB' ) );
			}

			if ( $field->params->get( 'image_client_resize', 1, GetterInterface::INT ) && ( $uploadWidthLimit || $uploadHeightLimit ) ) {
				$validationAttributes[]			=	cbValidator::getRuleHtmlAttributes( 'resize', array( $uploadWidthLimit, $uploadHeightLimit, $this->_getImageFieldParam( $field, 'avatarMaintainRatio', 1 ), $this->_getImageFieldParam( $field, 'avatarResizeAlways', 1 ) ) );
			}

			$return								.=	'<div id="cbimagefile_upload_' . htmlspecialchars( $fieldName ) . '" class="form-group mb-0 cb_form_line' . ( $hasChoices ? ' mt-3 hidden' : null ) . ' cbImageFieldUpload">'
												.		( $restrictions ? '<div class="mb-2">' . implode( ' ', $restrictions ) . '</div>' : null )
												.		'<div>'
												.			CBTxt::T( 'Select image file' ) . ' <input type="file" name="' . htmlspecialchars( $fieldName ) . '__file" value="" class="form-control' . ( $required == 1 ? ' required' : null ) . '"' . implode( ' ', $validationAttributes ) . ( $hasChoices ? ' disabled="disabled"' : null ) . ' />'
												.			( count( $choices ) <= 0 ? $this->_fieldIconsHtml( $field, $user, 'htmledit', $reason, 'select', '', null, '', array(), $displayFieldIcons, $required ) : null )
												.		'</div>'
												.		'<div class="mt-2">';

			if ( $field->params->get( 'image_terms', 0 ) ) {
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

						$return					.=			CBTxt::Th( 'BY_UPLOADING_YOU_CERTIFY_THAT_YOU_HAVE_THE_RIGHT_TO_DISTRIBUTE_THIS_IMAGE_FILE_TERMS', 'By uploading, you certify that you have the right to distribute this image and that it does not violate the above [type].', array( '[type]' => $termsType ) );
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

						$return					.=			CBTxt::Th( 'BY_UPLOADING_YOU_CERTIFY_THAT_YOU_HAVE_THE_RIGHT_TO_DISTRIBUTE_THIS_IMAGE_FILE_URL_TERMS', 'By uploading, you certify that you have the right to distribute this image and that it does not violate the <a href="[url]"[attributes]>[type]</a>', array( '[url]' => $url, '[attributes]' => $attributes, '[type]' => $termsType ) );
					}
				} else {
					$return						.=			CBTxt::Th( 'BY_UPLOADING_YOU_CERTIFY_THAT_YOU_HAVE_THE_RIGHT_TO_DISTRIBUTE_THIS_IMAGE_FILE', 'By uploading, you certify that you have the right to distribute this image.' );
				}
			} else {
				$return							.=			CBTxt::Th( 'BY_UPLOADING_YOU_CERTIFY_THAT_YOU_HAVE_THE_RIGHT_TO_DISTRIBUTE_THIS_IMAGE_FILE', 'By uploading, you certify that you have the right to distribute this image.' );
			}

			$return								.=		'</div>'
												.	'</div>';
		}

		if ( $field->params->get( 'image_allow_gallery', ( in_array( $fieldName, array( 'avatar', 'canvas' ) ) ? 1 : 0 ) ) ) {
			$galleryPath						=	$field->params->getString( 'image_gallery_path' );

			if ( ! $galleryPath ) {
				if ( $fieldName === 'canvas' ) {
					$galleryPath				=	'/images/comprofiler/gallery/canvas';
				} else {
					$galleryPath				=	'/images/comprofiler/gallery';
				}
			}

			$galleryImages						=	$this->displayImagesGallery( $_CB_framework->getCfg( 'absolute_path' ) . $galleryPath );
			$galleryStyle						=	'';

			if ( $fieldName !== 'canvas' ) {
				switch ( $field->params->getString( 'imageStyle', ( $fieldName === 'avatar' ? 'circlebordered' : '' ) ) ) {
					case 'rounded':
						$galleryStyle			=	' rounded';
						break;
					case 'roundedbordered':
						$galleryStyle			=	' img-thumbnail';
						break;
					case 'circle':
						$galleryStyle			=	' rounded-circle';
						break;
					case 'circlebordered':
						$galleryStyle			=	' img-thumbnail rounded-circle';
						break;
				}
			}

			if ( $fieldName === 'avatar' ) {
				$galleryStyle					.=	' cbImgAvatar';
			}

			$return								.=	'<div id="cbimagefile_gallery_' . htmlspecialchars( $fieldName ) . '" class="ml-n2 mr-n2 mb-n3 row no-gutters' . ( $hasChoices ? ' mt-3 hidden' : null ) . ' cbImageFieldGallery">';

			foreach ( $galleryImages as $i => $galleryImage ) {
				$imgName						=	ucfirst( str_replace( '_', ' ', preg_replace( '/^(.*)\..*$/', '\1', preg_replace( '/^tn/', '', $galleryImage ) ) ) );

				if ( $fieldName === 'canvas' ) {
					$return						.=		'<div class="position-relative col-12 col-md-6 pb-3 pl-2 pr-2">'
												.			'<input type="radio" name="' . htmlspecialchars( $fieldName ) . '__gallery" id="' . htmlspecialchars( $fieldName ) . '__gallery_' . (int) $i . '" value="' . htmlspecialchars( preg_replace( '/^tn/', '', $galleryImage ) ) . '" class="sr-only' . ( $required == 1 ? ' required' : null ) . '"' . ( $galleryImage == $value ? ' checked' : null ) . ( $hasChoices ? ' disabled="disabled"' : null ) . ' />'
												.			'<label for="' . htmlspecialchars( $fieldName ) . '__gallery_' . (int) $i . '" class="m-0 p-0 w-100">'
												.				'<div style="height: 100px; background-image: url(' . $_CB_framework->getCfg( 'live_site' ) . $galleryPath . '/' . htmlspecialchars( $galleryImage ) . ');" title="' . htmlspecialchars( $imgName ) . '" class="cbImgCanvas cbThumbCanvas' . htmlspecialchars( $galleryStyle ) . '"></div>'
												.			'</label>'
												.		'</div>';
				} else {
					$return						.=		'<div class="position-relative col-auto pb-3 pl-2 pr-2 text-center">'
												.			'<input type="radio" name="' . htmlspecialchars( $fieldName ) . '__gallery" id="' . htmlspecialchars( $fieldName ) . '__gallery_' . (int) $i . '" value="' . htmlspecialchars( preg_replace( '/^tn/', '', $galleryImage ) ) . '" class="sr-only' . ( $required == 1 ? ' required' : null ) . '"' . ( $galleryImage == $value ? ' checked' : null ) . ( $hasChoices ? ' disabled="disabled"' : null ) . ' />'
												.			'<label for="' . htmlspecialchars( $fieldName ) . '__gallery_' . (int) $i . '" class="m-0 p-0 w-100">'
												.				'<img src="' . $_CB_framework->getCfg( 'live_site' ) . $galleryPath . '/' . htmlspecialchars( $galleryImage ) . '" alt="' . htmlspecialchars( $imgName ) . '" title="' . htmlspecialchars( $imgName ) . '" class="cbImgPict cbThumbPict' . htmlspecialchars( $galleryStyle ) . '" />'
												.			'</label>'
												.		'</div>';
				}
			}

			$return								.=	'</div>';
		}

		if ( $canReposition ) {
			$position							=	$user->get( $fieldName . 'position', 50, GetterInterface::INT );

			if ( $position < 0 ) {
				$position						=	0;
			} elseif ( $position > 100 ) {
				$position						=	100;
			}

			$return								.=	'<div id="cbimagefile_position_' . htmlspecialchars( $fieldName ) . '" class="form-group mb-0 mt-3 cb_form_line cbImageFieldPosition hidden">'
												.		'<div class="cbCanvasReposition">'
												.			'<div class="cbCanvasRepositionSelect"></div>'
												.			'<img src="' . $this->_avatarLivePath( $field, $user, false, 2 ) . '" class="cbCanvasRepositionImage" />'
												.		'</div>'
												.		'<input type="hidden" name="' . htmlspecialchars( $fieldName ) . '__position" value="' . $position . '" disabled="disabled" />'
												.	'</div>';
		}

		return $return;
	}

	/**
	 * This event-driven method is temporary until we get another API for deleting each field:
	 *
	 * @param  UserTable  $user
	 */
	function onBeforeDeleteUser( $user )
	{
		global $_CB_framework, $_CB_database;

		$query					=	'SELECT ' . $_CB_database->NameQuote( 'name' )
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_fields' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'type' ). " = " . $_CB_database->Quote( 'image' );
		$_CB_database->setQuery( $query );
		$imageFields			=	$_CB_database->loadResultArray();

		if ( $imageFields ) {
			$imgPath		=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/';

			foreach ( $imageFields as $imageField ) {
				if ( isset( $user->$imageField ) && ( $user->$imageField != '' ) && ( strpos( $user->$imageField, 'gallery/' ) === false ) ) {
					if ( file_exists( $imgPath . $user->$imageField ) ) {
						@unlink( $imgPath . $user->$imageField );

						if ( file_exists( $imgPath . 'tn' . $user->$imageField ) ) {
							@unlink( $imgPath . 'tn' . $user->$imageField );
						}
					}
				}
			}
		}
	}

	public function loadDefaultImages( $name, $value, $control_name, $basePath = 'avatar' )
	{
		$values					=	array();
		$values[]				=	moscomprofilerHTML::makeOption( '', CBTxt::T( 'Normal CB Default' ) );
		$values[]				=	moscomprofilerHTML::makeOption( 'none', CBTxt::T( 'No image' ) );
		$values[]				=	moscomprofilerHTML::makeOption( 'initial', ( $basePath === 'canvas' ? CBTxt::T( 'Unique Color' ) : CBTxt::T( 'First and Last Initial with Unique Color' ) ) );
		$values[]				=	moscomprofilerHTML::makeOption( 'color', ( $basePath === 'canvas' ? CBTxt::T( 'Specific Color' ) : CBTxt::T( 'First and Last Initial with Specific Color' ) ) );

		if ( is_dir( selectTemplate( 'absolute_path', null, 1 ) . '/images/' . $basePath ) ) {
			foreach ( scandir( selectTemplate( 'absolute_path', null, 1 ) . '/images/' . $basePath ) as $avatar ) {
				if ( ( ! preg_match( '/^tn/', $avatar ) ) && preg_match( '!^[\w-]+[.](jpg|jpeg|png|gif)$!', $avatar ) ) {
					$values[]	=	moscomprofilerHTML::makeOption( $avatar, $avatar );
				}
			}
		}

		return $values;
	}

	public function loadDefaultCanvasImages( $name, $value, $control_name )
	{
		return $this->loadDefaultImages( $name, $value, $control_name, 'canvas' );
	}

	/**
	 * Returns array of image files based off path
	 *
	 * @param string $path
	 * @param string $size all: return all images; any: return thumbnail or full size
	 * @return array
	 */
	protected function displayImagesGallery( $path, $size = 'any' )
	{
		$dir									=	@opendir( $path );
		$images									=	array();
		$index									=	0;

		while ( true == ( $file = @readdir( $dir ) ) ) {
			if ( ( $file != '.' ) && ( $file != '..' ) && is_file( $path . '/' . $file ) && ( ! is_link( $path. '/' . $file ) ) ) {
				if ( preg_match( '/(\.gif$|\.png$|\.jpg|\.jpeg)$/is', $file ) ) {
					if ( $size === 'all' ) {
						$images[$index]			=	$file;
					} elseif ( preg_match( '/^tn/', $file ) ) {
						$full					=	array_search( preg_replace( '/^tn/', '', $file ), $images );

						if ( $full !== false ) {
							unset( $images[$full] );
						}

						$images[$index]			=	$file;
					} else {
						$thumb					=	array_search( 'tn' . $file, $images );

						if ( $thumb === false ) {
							$images[$index]		=	$file;
						}
					}

					$index++;
				}
			}
		}

		@closedir( $dir );

		$images									=	array_values( $images );

		@sort( $images );
		@reset( $images );

		return $images;
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