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
use CBLib\Registry\GetterInterface;
use CBuser;
use cbValidator;
use Exception;
use moscomprofilerHTML;

\defined( 'CBLIB' ) or die();

class VideoField extends TextField
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
		$return						=	null;

		$value						=	$user->get( $field->get( 'name' ) );

		switch ( $output ) {
			case 'html':
			case 'rss':
				if ( $value ) {
					$return			=	$this->getEmbed( $field, $user, $value, $reason );
				}

				$return				=	$this->formatFieldValueLayout( $return, $reason, $field, $user );
				break;
			/** @noinspection PhpMissingBreakStatementInspection */
			case 'htmledit':
					if ( $reason == 'search' ) {
						$choices	=	array();
						$choices[]	=	moscomprofilerHTML::makeOption( '', CBTxt::T( 'UE_NO_PREFERENCE', 'No preference' ) );
						$choices[]	=	moscomprofilerHTML::makeOption( '1', ( $field->params->get( 'video_allow_links', 1 ) ? CBTxt::T( 'Has video file or link' ) : CBTxt::T( 'Has a video file' ) ) );
						$choices[]	=	moscomprofilerHTML::makeOption( '0', ( $field->params->get( 'video_allow_links', 1 ) ? CBTxt::T( 'Has no video file or link' ) : CBTxt::T( 'Has no video file' ) ) );

						$html		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'select', $value, '', $choices, true, null, false );

						$return		=	$this->_fieldSearchModeHtml( $field, $user, $html, 'singlechoice', $list_compare_types );
					} else {
						$return		=	$this->formatFieldValueLayout( $this->_htmlEditForm( $field, $user, $output, $reason ), $reason, $field, $user );
					}
					break;
			default:
				$field->set( 'type', 'text' );

				$return				=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}

		return $return;
	}

	/**
	 * returns video embed based off video url
	 *
	 * @param  FieldTable   $field
	 * @param  UserTable    $user
	 * @param  string       $value
	 * @param  string       $reason
	 * @return null|string
	 */
	public function getEmbed( $field, $user, $value, $reason )
	{
		global $_CB_framework;

		$domain						=	preg_replace( '/^(?:(?:\w+\.)*)?(\w+)\..+$/', '\1', parse_url( $value, PHP_URL_HOST ) );

		if ( ! $domain ) {
			$value					=	$_CB_framework->getCfg( 'live_site' ) . '/images/comprofiler/video/' . (int) $user->get( 'id' ) . '/' . urlencode( $value );
		}

		$embed						=	null;

		if ( $value ) {
			$currentScheme			=	( ( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( $_SERVER['HTTPS'] != 'off' ) ) ? 'https' : 'http' );
			$urlScheme				=	parse_url( $value, PHP_URL_SCHEME );

			if ( ! $urlScheme ) {
				$urlScheme			=	$currentScheme;
			}

			if ( ( $currentScheme == 'https' ) && ( $urlScheme != $currentScheme ) ) {
				$value				=	str_replace( 'http', 'https', $value );
			}

			if ( $reason != 'profile' ) {
				$width				=	(int) $field->params->get( 'video_thumbwidth', 400 );
			} else {
				$width				=	(int) $field->params->get( 'video_width', 400 );
			}

			$embed					=	'<div class="cbVideoField' . ( $reason == 'list' ? ' cbClicksInside' : null ) . '" style="max-width: 100%;">';

			if ( in_array( $domain, array( 'youtube', 'youtu' ) ) ) {
				if ( preg_match( '%(?:(?:watch\?v=)|(?:embed/)|(?:shorts/)|(?:be/))([A-Za-z0-9_-]+)%', $value, $matches ) ) {
					// iframe can't have % height so always calculate it:
					$embed	.=				'<iframe width="' . ( $width ? (int) $width : '640' ) . '" height="' . round( ( $width ? (int) $width : 640 ) / 1.78 ) . '" style="max-width: 100%;" src="https://www.youtube.com/embed/' . htmlspecialchars( $matches[1] ) . '" frameborder="0" allowfullscreen class="cbVideoFieldEmbed"></iframe>';
				}
			} else {
				$embed		.=				'<video' . ( $width ? ' width="' . (int) $width . '" height="' . round( (int) $width / 1.78 ) . '"' : ' width="100%"' ) . ' style="max-width: 100%;" src="' . htmlspecialchars( $value ) . '" type="' . htmlspecialchars( $this->getMimeType( $value ) ) . '" controls="controls" preload="auto" class="cbVideoFieldEmbed"></video>';
			}

			$embed					.=	'</div>';
		}

		return $embed;
	}

	/**
	 *
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output            'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $reason            'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  boolean     $displayFieldIcons
	 * @return string                          HTML: <tag type="$type" value="$value" xxxx="xxx" yy="y" />
	 */
	function _htmlEditForm( &$field, &$user, $output, $reason, $displayFieldIcons = true )
	{
		global $_CB_framework;

		$fieldName							=	$field->get( 'name' );
		$value								=	$user->get( $fieldName );
		$required							=	$this->_isRequired( $field, $user, $reason );

		$uploadSizeLimitMax					=	$field->params->get( 'fieldValidateVideo_sizeMax', 1024 );
		$uploadSizeLimitMin					=	$field->params->get( 'fieldValidateVideo_sizeMin', 0 );
		$uploadExtensionLimit				=	$this->allowedExtensions();
		$restrictions						=	array();

		if ( $uploadExtensionLimit ) {
			$restrictions[]					=	CBTxt::Th( 'VIDEO_FILE_UPLOAD_LIMITS_EXT', 'Your video file must be of [ext] type.', array( '[ext]' => implode( ', ', $uploadExtensionLimit ) ) );
		}

		if ( $uploadSizeLimitMin ) {
			$restrictions[]					=	CBTxt::Th( 'VIDEO_FILE_UPLOAD_LIMITS_MIN', 'Your video file should exceed [size].', array( '[size]' => $this->formattedFileSize( $uploadSizeLimitMin * 1024 ) ) );
		}

		if ( $uploadSizeLimitMax ) {
			$restrictions[]					=	CBTxt::Th( 'VIDEO_FILE_UPLOAD_LIMITS_MAX', 'Your video file should not exceed [size].', array( '[size]' => $this->formattedFileSize( $uploadSizeLimitMax * 1024 ) ) );
		}

		$existingFile						=	( $user->get( 'id' ) ? ( ( $value != null ) ? true : false ) : false );
		$choices							=	array();

		if ( ( $reason == 'register' ) || ( ( $reason == 'edit' ) && ( $user->id == 0 ) ) ) {
			if ( $required == 0 ) {
				$choices[]					=	moscomprofilerHTML::makeOption( '', CBTxt::T( 'No video file' ) );
			}
		} else {
			if ( $existingFile || ( $required == 0 ) ) {
				$choices[]					=	moscomprofilerHTML::makeOption( '', CBTxt::T( 'No change of video file' ) );
			}
		}

		$selected							=	null;

		if ( ( $required == 1 ) && ( ! $existingFile ) ) {
			$selected						=	( $field->params->get( 'video_allow_uploads', 1 ) ? 'upload' : 'link' );
		}

		if ( $field->params->get( 'video_allow_links', 1 ) ) {
			$choices[]						=	moscomprofilerHTML::makeOption( 'link', ( $existingFile ? CBTxt::T( 'Link to new video file' ) : CBTxt::T( 'Link to video file' ) ) );
		}

		if ( $field->params->get( 'video_allow_uploads', 1 ) ) {
		$choices[]							=	moscomprofilerHTML::makeOption( 'upload', ( $existingFile ? CBTxt::T( 'Upload new video file' ) : CBTxt::T( 'Upload video file' ) ) );
		}

		if ( $existingFile && ( $required == 0 ) ) {
			$choices[]						=	moscomprofilerHTML::makeOption( 'delete', CBTxt::T( 'Remove video file' ) );
		}

		$return								=	null;

		if ( ( $reason != 'register' ) && ( $user->id != 0 ) && $existingFile ) {
			$return							.=	'<div class="row no-gutters mb-3 cbVideoFieldEmbed">' . $this->getEmbed( $field, $user, $value, $reason ) . '</div>';
		}

		$hasChoices							=	( count( $choices ) > 1 );

		if ( $hasChoices ) {
			static $functOut				=	false;

			$additional						=	' class="form-control cbVideoFieldChoice"';

			if ( ( $_CB_framework->getUi() == 1 ) && ( $reason == 'edit' ) && $field->get( 'readonly' ) ) {
				$additional					.=	' disabled="disabled"';
			}

			$translatedTitle				=	$this->getFieldTitle( $field, $user, 'html', $reason );
			$htmlDescription				=	$this->getFieldDescription( $field, $user, 'htmledit', $reason );
			$trimmedDescription				=	trim( strip_tags( $htmlDescription ) );
			$inputDescription				=	$field->params->get( 'fieldLayoutInputDesc', 1, GetterInterface::INT );

			$tooltip						=	( $trimmedDescription && $inputDescription ? cbTooltip( $_CB_framework->getUi(), $htmlDescription, $translatedTitle, null, null, null, null, $additional ) : $additional );

			$return							.=	'<div class="form-group mb-0 cb_form_line">'
											.		moscomprofilerHTML::selectList( $choices, $fieldName . '__choice', $tooltip, 'value', 'text', $selected, $required, true, null, false )
											.		$this->_fieldIconsHtml( $field, $user, 'htmledit', $reason, 'select', '', null, '', array(), $displayFieldIcons, $required )
											.	'</div>';

			if ( ! $functOut ) {
				$js							=	"$.fn.cbslideVideoFile = function() {"
											.		"var element = $( this );"
											.		"element.parent().siblings( '.cbVideoFieldLink' ).find( 'input' ).prop( 'disabled', true );"
											.		"element.on( 'click.cbvideofield change.cbvideofield', function() {"
											.			"if ( ( $( this ).val() == '' ) || ( $( this ).val() == 'delete' ) ) {"
											.				"element.parent().siblings( '.cbVideoFieldUpload,.cbVideoFieldLink' ).addClass( 'hidden' ).find( 'input' ).prop( 'disabled', true );"
											.			"} else if ( $( this ).val() == 'upload' ) {"
											.				"element.parent().siblings( '.cbVideoFieldUpload' ).removeClass( 'hidden' ).find( 'input' ).prop( 'disabled', false );"
											.				"element.parent().siblings( '.cbVideoFieldLink' ).addClass( 'hidden' ).find( 'input' ).prop( 'disabled', true );"
											.			"} else if ( $( this ).val() == 'link' ) {"
											.				"element.parent().siblings( '.cbVideoFieldLink' ).removeClass( 'hidden' ).find( 'input' ).prop( 'disabled', false );"
											.				"element.parent().siblings( '.cbVideoFieldUpload' ).addClass( 'hidden' ).find( 'input' ).prop( 'disabled', true );"
											.			"}"
											.		"}).on( 'cloned.cbvideofield', function() {"
											.			"$( this ).parent().siblings( '.cbVideoFieldEmbed' ).remove();"
											.			"if ( $( this ).parent().siblings( '.cbVideoFieldUpload,.cbVideoFieldLink' ).find( 'input.required' ).length ) {"
											.				"$( this ).find( 'option[value=\"\"]' ).remove();"
											.			"}"
											.			"$( this ).find( 'option[value=\"delete\"]' ).remove();"
											.			"$( this ).off( '.cbvideofield' );"
											.			"$( this ).cbslideVideoFile();"
											.		"}).change();"
											.		"return this;"
											.	"};";

				$_CB_framework->outputCbJQuery( $js );

				$functOut					=	true;
			}

			$_CB_framework->outputCbJQuery( "$( '#" . addslashes( $fieldName ) . "__choice' ).cbslideVideoFile();" );
		} else {
			$return							.=	'<input type="hidden" name="' . htmlspecialchars( $fieldName ) . '__choice" value="' . htmlspecialchars( $choices[0]->value ) . '" />';
		}

		if ( $field->params->get( 'video_allow_uploads', 1 ) ) {
			$validationAttributes			=	array();
			$validationAttributes[]			=	cbValidator::getRuleHtmlAttributes( 'extension', implode( ',', $uploadExtensionLimit ) );

			if ( $uploadSizeLimitMin || $uploadSizeLimitMax ) {
				$validationAttributes[]		=	cbValidator::getRuleHtmlAttributes( 'filesize', array( $uploadSizeLimitMin, $uploadSizeLimitMax, 'KB' ) );
			}

			$return							.=	'<div id="cbvideofile_upload_' . htmlspecialchars( $fieldName ) . '" class="form-group mb-0 cb_form_line' . ( $hasChoices ? ' mt-3 hidden' : null ) . ' cbVideoFieldUpload">'
											.		( $restrictions ? '<div class="mb-2">' . implode( ' ', $restrictions ) . '</div>' : null )
											.		'<div>'
											.			CBTxt::T( 'Select video file' ) . ' <input type="file" name="' . htmlspecialchars( $fieldName ) . '__file" value="" class="form-control' . ( $required == 1 ? ' required' : null ) . '"' . implode( ' ', $validationAttributes ) . ( $hasChoices ? ' disabled="disabled"' : null ) . ' />'
											.			( count( $choices ) <= 0 ? $this->_fieldIconsHtml( $field, $user, 'htmledit', $reason, 'select', '', null, '', array(), $displayFieldIcons, $required ) : null )
											.		'</div>'
											.		'<div class="mt-2">';

			if ( $field->params->get( 'video_terms', 0 ) ) {
				$cbUser						=	CBuser::getMyInstance();
				$termsOutput				=	$field->params->get( 'terms_output', 'url' );
				$termsType					=	$cbUser->replaceUserVars( $field->params->get( 'terms_type', 'TERMS_AND_CONDITIONS' ) );
				$termsDisplay				=	$field->params->get( 'terms_display', 'modal' );
				$termsURL					=	cbSef( $cbUser->replaceUserVars( $field->params->get( 'terms_url', null ) ), false );
				$termsText					=	$cbUser->replaceUserVars( $field->params->get( 'terms_text', null ) );
				$termsWidth					=	$field->params->get( 'terms_width', 400 );
				$termsHeight				=	$field->params->get( 'terms_height', 200 );

				if ( ! $termsType ) {
					$termsType				=	CBTxt::T( 'TERMS_AND_CONDITIONS', 'Terms and Conditions' );
				}

				if ( ! $termsWidth ) {
					$termsWidth				=	400;
				}

				if ( ! $termsHeight ) {
					$termsHeight			=	200;
				}

				if ( ( ( $termsOutput == 'url' ) && $termsURL ) || ( ( $termsOutput == 'text' ) && $termsText ) ) {
					if ( $termsDisplay == 'iframe' ) {
						if ( is_numeric( $termsHeight ) ) {
							$termsHeight	.=	'px';
						}

						if ( is_numeric( $termsWidth ) ) {
							$termsWidth		.=	'px';
						}

						if ( $termsOutput == 'url' ) {
							$return			.=	'<div class="embed-responsive mb-2 cbTermsFrameContainer" style="padding-bottom: ' . htmlspecialchars( $termsHeight ) . ';">'
											.		'<iframe class="embed-responsive-item d-block border rounded cbTermsFrameURL" style="width: ' . htmlspecialchars( $termsWidth ) . ';" src="' . htmlspecialchars( $termsURL ) . '"></iframe>'
											.	'</div>';
						} else {
							$return			.=	'<div class="bg-light border rounded p-2 mb-2 cbTermsFrameText" style="height:' . htmlspecialchars( $termsHeight ) . ';width:' . htmlspecialchars( $termsWidth ) . ';overflow:auto;">' . $termsText . '</div>';
						}

						$return				.=			CBTxt::Th( 'BY_UPLOADING_YOU_CERTIFY_THAT_YOU_HAVE_THE_RIGHT_TO_DISTRIBUTE_THIS_VIDEO_FILE_TERMS', 'By uploading, you certify that you have the right to distribute this video file and that it does not violate the above [type].', array( '[type]' => $termsType ) );
					} else {
						$attributes			=	' class="cbTermsLink"';

						if ( ( $termsOutput == 'text' ) && ( $termsDisplay == 'window' ) ) {
							$termsDisplay	=	'modal';
						}

						if ( $termsDisplay == 'modal' ) {
							// Tooltip height percentage would be based off window height (including scrolling); lets change it to be based off the viewport height:
							$termsHeight	=	( substr( $termsHeight, -1 ) == '%' ? (int) substr( $termsHeight, 0, -1 ) . 'vh' : $termsHeight );

							if ( $termsOutput == 'url' ) {
								$tooltip	=	'<iframe class="d-block m-0 p-0 border-0 cbTermsModalURL" height="100%" width="100%" src="' . htmlspecialchars( $termsURL ) . '"></iframe>';
							} else {
								$tooltip	=	'<div class="cbTermsModalText" style="height:100%;width:100%;overflow:auto;">' . $termsText . '</div>';
							}

							$url			=	'javascript:void(0);';
							$attributes		.=	' ' . cbTooltip( $_CB_framework->getUi(), $tooltip, $termsType, array( $termsWidth, $termsHeight ), null, null, null, 'data-hascbtooltip="true" data-cbtooltip-modal="true"' );
						} else {
							$url			=	htmlspecialchars( $termsURL );
							$attributes		.=	' target="_blank"';
						}

						$return				.=			CBTxt::Th( 'BY_UPLOADING_YOU_CERTIFY_THAT_YOU_HAVE_THE_RIGHT_TO_DISTRIBUTE_THIS_VIDEO_FILE_URL_TERMS', 'By uploading, you certify that you have the right to distribute this video file and that it does not violate the <a href="[url]"[attributes]>[type]</a>', array( '[url]' => $url, '[attributes]' => $attributes, '[type]' => $termsType ) );
					}
				} else {
					$return					.=			CBTxt::Th( 'BY_UPLOADING_YOU_CERTIFY_THAT_YOU_HAVE_THE_RIGHT_TO_DISTRIBUTE_THIS_VIDEO_FILE', 'By uploading, you certify that you have the right to distribute this video file.' );
				}
			} else {
				$return						.=			CBTxt::Th( 'BY_UPLOADING_YOU_CERTIFY_THAT_YOU_HAVE_THE_RIGHT_TO_DISTRIBUTE_THIS_VIDEO_FILE', 'By uploading, you certify that you have the right to distribute this video file.' );
			}

			$return							.=		'</div>'
											.	'</div>';
		}

		if ( $field->params->get( 'video_allow_links', 1 ) ) {
			$return							.=	'<div id="cbvideofile_link_' . htmlspecialchars( $fieldName ) . '" class="form-group mb-0 cb_form_line' . ( $hasChoices ? ' mt-3 hidden' : null ) . ' cbVideoFieldLink">';

			$linkField						=	new FieldTable( $field->getDbo() );

			foreach ( array_keys( get_object_vars( $linkField ) ) as $k ) {
				$linkField->set( $k, $field->get( $k ) );
			}

			$linkField->set( 'type', 'text' );
			$linkField->set( 'description', '' );

			$user->set( $fieldName, ( ( strpos( (string) $value, '/' ) !== false ) || ( strpos( (string) $value, '\\' ) !== false ) ? $value : null ) );

			$return							.=		parent::getField( $linkField, $user, $output, $reason, 0 );

			$user->set( $fieldName, $value );

			unset( $linkField );

			$return							.=	'</div>';
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
		global $_CB_framework, $_CB_database;

		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		$col					=	$field->get( 'name' );
		$colChoice				=	$col . '__choice';
		$colFile				=	$col . '__file';
		$choice					=	stripslashes( cbGetParam( $postdata, $colChoice ) );

		switch ( $choice ) {
			case 'upload':
				$value			=	( isset( $_FILES[$colFile] ) ? $_FILES[$colFile] : null );

				$this->validate( $field, $user, $choice, $value, $postdata, $reason );
				break;
			case 'link':
				parent::prepareFieldDataSave( $field, $user, $postdata, $reason );
				break;
			case 'delete':
				if ( $user->get( 'id' ) && ( $user->get( $col ) != null ) && ( $user->get( $col ) != '' ) ) {
					if ( isset( $user->$col ) ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->get( $col ), '' );
					}

					$value		=	$user->get( $col );

					if ( ( strpos( $value, '/' ) === false ) && ( strpos( $value, '\\' ) === false ) ) {
						$this->deleteFiles( $user, $user->get( $col ) );
					}

					$user->set( $col, null );

					// This is needed because user store does not save null:
					if ( $field->get( 'table' ) ) {
						$query	=	'UPDATE ' . $_CB_database->NameQuote( $field->get( 'table' ) )
								.	"\n SET " . $_CB_database->NameQuote( $col ) . " = NULL"
								.	', ' . $_CB_database->NameQuote( 'lastupdatedate' ) . ' = ' . $_CB_database->Quote( $_CB_framework->dateDbOfNow() )
								.	"\n WHERE " . $_CB_database->NameQuote( 'id' ) . " = " . (int) $user->get( 'id' );
						$_CB_database->setQuery( $query );
						$_CB_database->query();
					}
				}
				break;
			default:
					$value		=	$user->get( $col );

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

		$col						=	$field->get( 'name' );
		$colChoice					=	$col . '__choice';
		$colFile					=	$col . '__file';
		$choice						=	stripslashes( cbGetParam( $postdata, $colChoice ) );

		switch ( $choice ) {
			case 'upload':
				$value				=	( isset( $_FILES[$colFile] ) ? $_FILES[$colFile] : null );

				if ( $this->validate( $field, $user, $choice, $value, $postdata, $reason ) ) {
					$_PLUGINS->loadPluginGroup( 'user' );

					$_PLUGINS->trigger( 'onBeforeUserVideoUpdate', array( &$user, &$value['tmp_name'] ) );

					if ( $_PLUGINS->is_errors() ) {
						$this->_setErrorMSG( $_PLUGINS->getErrorMSG() );
						return;
					}

					$path			=	$_CB_framework->getCfg( 'absolute_path' );
					$indexPath		=	$path . '/components/com_comprofiler/index.html';
					$filesPath		=	$path . '/images/comprofiler/video';
					$filePath		=	$filesPath . '/' . (int) $user->get( 'id' );

					if ( ! is_dir( $filesPath ) ) {
						$oldmask	=	@umask( 0 );

						if ( @mkdir( $filesPath, 0755, true ) ) {
							@umask( $oldmask );
							@chmod( $filesPath, 0755 );

							if ( ! file_exists( $filesPath . '/index.html' ) ) {
								@copy( $indexPath, $filesPath . '/index.html' );
								@chmod( $filesPath . '/index.html', 0755 );
							}
						} else {
							@umask( $oldmask );
						}
					}

					if ( ! is_dir( $filePath ) ) {
						$oldmask	=	@umask( 0 );

						if ( @mkdir( $filePath, 0755, true ) ) {
							@umask( $oldmask );
							@chmod( $filePath, 0755 );

							if ( ! file_exists( $filePath . '/index.html' ) ) {
								@copy( $indexPath, $filePath . '/index.html' );
								@chmod( $filePath . '/index.html', 0755 );
							}
						} else {
							@umask( $oldmask );
						}
					}

					$uploadedExt	=	strtolower( preg_replace( '/[^-a-zA-Z0-9_]/u', '', pathinfo( $value['name'], PATHINFO_EXTENSION ) ) );
					$newFileName	=	$col . '_' . uniqid( $user->id . '_' ) . '.' . $uploadedExt;

					if ( ! move_uploaded_file( $value['tmp_name'], $filePath . '/'. $newFileName ) ) {
						$this->_setValidationError( $field, $user, $reason, sprintf( CBTxt::T( 'CBVideo-failed to upload video file: %s' ), $newFileName ) );
						return;
					} else {
						@chmod( $filePath . '/' . $value['tmp_name'], 0755 );
					}

					if ( isset( $user->$col ) ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->get( $col ), '' );
					}

					if ( isset( $user->$col ) && ( $user->get( $col ) != '' ) ) {
						$this->deleteFiles( $user, $user->get( $col ) );
					}

					$user->set( $col, $newFileName );

					$_PLUGINS->trigger( 'onAfterUserVideoUpdate', array( &$user, $newFileName ) );
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

		$col			=	$field->get( 'name' );
		$colChoice		=	$col . '__choice';
		$colFile		=	$col . '__file';

		$choice			=	stripslashes( cbGetParam( $postdata, $colChoice ) );

		switch ( $choice ) {
			case 'upload':
				$value	=	( isset( $_FILES[$colFile] ) ? $_FILES[$colFile] : null );

				if ( $this->validate( $field, $user, $choice, $value, $postdata, $reason ) ) {
					$this->deleteFiles( $user, $user->get( $col ) );
				}
				break;
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
	 * @param  string      $reason      'edit' for save user edit, 'register' for save registration
	 * @return boolean                  True if validate, $this->_setErrorMSG if False
	 */
	public function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason )
	{
		$isRequired							=	$this->_isRequired( $field, $user, $reason );

		$col								=	$field->get( 'name' );
		$colChoice							=	$col . '__choice';
		$choice								=	stripslashes( cbGetParam( $postdata, $colChoice ) );

		switch ( $choice ) {
			case 'upload':
				if ( ! $field->params->get( 'video_allow_uploads', 1 ) ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'UE_NOT_AUTHORIZED', 'You are not authorized to view this page!' ) );
					return false;
				} elseif ( ! isset( $value['tmp_name'] ) || empty( $value['tmp_name'] ) || ( $value['error'] != 0 ) || ( ! is_uploaded_file( $value['tmp_name'] ) ) ) {
					if ( $isRequired ) {
						$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'Please select a video file before uploading' ) );
					}

					return false;
				} else {
					$uploadSizeLimitMax		=	$field->params->get( 'fieldValidateVideo_sizeMax', 1024 );
					$uploadSizeLimitMin		=	$field->params->get( 'fieldValidateVideo_sizeMin', 0 );
					$uploadExtensionLimit	=	$this->allowedExtensions();
					$uploadedExt			=	strtolower( preg_replace( '/[^-a-zA-Z0-9_]/u', '', pathinfo( $value['name'], PATHINFO_EXTENSION ) ) );

					if ( ( ! $uploadedExt ) || ( ! in_array( $uploadedExt, $uploadExtensionLimit ) ) ) {
						$this->_setValidationError( $field, $user, $reason, sprintf( CBTxt::T( 'Please upload only %s' ), implode( ', ', $uploadExtensionLimit ) ) );
						return false;
					}

					$uploadedSize			=	$value['size'];

					if ( ( $uploadedSize / 1024 ) > $uploadSizeLimitMax ) {
						$this->_setValidationError( $field, $user, $reason, sprintf( CBTxt::T( 'The video file size exceeds the maximum of %s' ), $this->formattedFileSize( $uploadSizeLimitMax * 1024 ) ) );
						return false;
					}

					if ( ( $uploadedSize / 1024 ) < $uploadSizeLimitMin ) {
						$this->_setValidationError( $field, $user, $reason, sprintf( CBTxt::T( 'The video file is too small, the minimum is %s' ), $this->formattedFileSize( $uploadSizeLimitMin * 1024 ) ) );
						return false;
					}
				}
				break;
			case 'link':
				if ( ! $field->params->get( 'video_allow_links', 1 ) ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'UE_NOT_AUTHORIZED', 'You are not authorized to view this page!' ) );
					return false;
				}

				$validated					=	parent::validate( $field, $user, $columnName, $value, $postdata, $reason );

				if ( $validated && ( $value !== '' ) && ( $value !== null ) ) {
					$domain					=	preg_replace( '/^(?:(?:\w+\.)*)?(\w+)\..+$/', '\1', parse_url( $value, PHP_URL_HOST ) );

					if ( ! in_array( $domain, array( 'youtube', 'youtu' ) ) ) {
						$linkExists			=	false;

						try {
							$request		=	new \GuzzleHttp\Client();

							$header			=	$request->head( $value );

							if ( ( $header !== false ) && ( $header->getStatusCode() == 200 ) ) {
								$linkExists	=	true;
							}
						} catch( Exception $e ) {}

						if ( ! $linkExists ) {
							$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'Please input a video file url before linking' ) );
							return false;
						}

						$linkExtLimit		=	$this->allowedExtensions();
						$linkExt			=	strtolower( pathinfo( $value, PATHINFO_EXTENSION ) );

						if ( ( ! $linkExt ) || ( ! in_array( $linkExt, $linkExtLimit ) ) ) {
							$linkExtLimit[]	=	'youtube';

							$this->_setValidationError( $field, $user, $reason, sprintf( CBTxt::T( 'Please link only %s' ), implode( ', ', $linkExtLimit ) ) );
							return false;
						}
					}
				}

				return $validated;
				break;
			default:
				$valCol						=	$field->get( 'name' );

				if ( $isRequired && ( ( ! $user ) || ( ! isset( $user->$valCol ) ) || ( ! $user->get( $valCol ) ) ) ) {
					if ( ! $value ) {
						$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'UE_FIELDREQUIRED', 'This Field is required' ) );
						return false;
					}
				}
				break;
		}

		return true;
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

		$filePath	=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/video/' . (int) $user->id . '/';

		if ( ! is_dir( $filePath ) ) {
			return;
		}

		if ( ! $file ) {
			if ( false !== ( $handle = opendir( $filePath ) ) ) {
				while ( false !== ( $file = readdir( $handle ) ) ) {
					if ( $file && ( ( $file != '.' ) && ( $file != '..' ) ) ) {
						@unlink( $filePath . $file );
					}
				}
				closedir( $handle );
			}

			if ( is_dir( $filePath ) ) {
				@rmdir( $filePath );
			}
		} else {
			if ( file_exists( $filePath . $file ) ) {
				@unlink( $filePath . $file );
			}
		}
	}

	/**
	 * returns the mimetype of the supplied file or link
	 *
	 * @param  string  $value
	 * @return string
	 */
	function getMimeType( $value )
	{
		$domain			=	preg_replace( '/^(?:(?:\w+\.)*)?(\w+)\..+$/', '\1', parse_url( $value, PHP_URL_HOST ) );

		if ( $domain && in_array( $domain, array( 'youtube', 'youtu' ) ) ) {
			return 'video/youtube';
		}

		$extension		=	strtolower( pathinfo( ( $domain ? $value : preg_replace( '/[^-a-zA-Z0-9_.]/u', '', $value ) ), PATHINFO_EXTENSION ) );

		return ( $extension == 'm4v' ? 'video/mp4' : cbGetMimeFromExt( $extension ) );
	}

	/**
	 * outputs a secure list of allowed file extensions
	 *
	 * @return array
	 */
	private function allowedExtensions()
	{
		return array( 'mp4', 'ogv', 'ogg', 'webm', 'm4v' );
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