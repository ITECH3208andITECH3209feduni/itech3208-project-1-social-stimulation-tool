<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 11/29/13 12:16 AM $
* @package CBLib\AhaWow\View
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CBLib\AhaWow\View;

use CBLib\AhaWow\Controller\RegistryEditController;
use CBLib\Application\Application;
use CBLib\Database\Table\TableInterface;
use CBLib\Language\CBTxt;
use CBLib\Xml\SimpleXMLElement;
use CB\Database\Table\PluginTable;
use cbTabs;
use cbValidator;

defined('CBLIB') or die();

/**
 * CBLib\AhaWow\View\ActionViewAdmin Class implementation
 *
 */
class ActionViewAdmin {
	/**
	 * Writes the edit form for new and existing module
	 *
	 * A new record is defined when <var>$row</var> is passed with the <var>id</var>
	 * property set to 0.
	 *
	 * @param  array                     $options
	 * @param  array                     $actionPath
	 * @param  SimpleXMLElement          $viewModel
	 * @param  TableInterface|\stdClass  $data
	 * @param  RegistryEditController    $params
	 * @param  PluginTable               $pluginRow
	 * @param  string                    $viewType     ( 'view', 'param', 'depends': means: <param> tag => param, <field> tag => view )
	 * @param  string                    $cbprevstate
	 * @param  boolean                   $htmlOutput   True to output headers for CSS and Javascript
	 */
	public static function editPluginView( $options, $actionPath, $viewModel, $data, $params, $pluginRow, $viewType, $cbprevstate, $htmlOutput ) {
		global $_CB_framework, $_CB_Backend_Title, $_PLUGINS, $ueConfig;

		$name						=	$viewModel->attributes( 'name' );
		$label						=	$viewModel->attributes( 'label' );
		$iconPair					=	explode( ':', (string) $viewModel->attributes( 'icon' ) );

		if ( count( $iconPair ) > 1 ) {
			$iconset				=	( isset( $iconPair[0] ) ? $iconPair[0] : null );
			$icon					=	( isset( $iconPair[1] ) ? $iconPair[1] : null );
		} else {
			$iconset				=	'fa';
			$icon					=	( isset( $iconPair[0] ) ? $iconPair[0] : null );
		}

		if ( $icon ) {
			if ( $iconset == 'fa' ) {
				$icon				=	'fa fa-' . $icon;
			} elseif ( $iconset ) {
				$icon				=	$iconset . $icon;
			}
		}

		$id							=	null;

		if ( is_object( $data ) ) {
			$dataArray				=	get_object_vars( $data );

			if ( key_exists( 'id', $dataArray ) ) { // General object
				$id					=	(int) $data->id;
			} elseif ( key_exists( 'tabid', $dataArray ) ) { // Tab object
				$id					=	(int) $data->tabid;

				if ( ! $pluginRow ) {
					$pluginRow		=	$_PLUGINS->getCachedPluginObject( (int) $data->pluginid );
				}
			} elseif ( key_exists( 'fieldid', $dataArray ) ) { // Field object
				$id					=	(int) $data->fieldid;

				if ( ! $pluginRow ) {
					$pluginRow		=	$_PLUGINS->getCachedPluginObject( (int) $data->pluginid );
				}
			}
		}

		if ( $id !== null ) {
			if ( isset( $data->title ) ) {
				$item				=	$data->title;
			} elseif ( isset( $data->name ) ) {
				$item				=	$data->name;
			} else {
				$item				=	$id;
			}

			$title					=	( $id ? CBTxt::T( 'Edit' ) : CBTxt::T( 'New' ) ) . ( $label ? ' ' . htmlspecialchars( CBTxt::T( $label ) ) . ' ' : null ) . ( $item ? ' [' . htmlspecialchars( CBTxt::T( $item ) ) . ']' : null );
		} else {
			$title					=	( $label ? htmlspecialchars( CBTxt::T( $label ) ) : null );
		}

		if ( $viewModel->attributes( 'label' ) ) {
			$showDisclaimer			=	true;

			if ( $pluginRow ) {
				if ( ! $icon ) {
					$icon			=	'cb-' . str_replace( '.', '_', $pluginRow->element ) . '-' . $name;
				}

				$_CB_Backend_Title	=	array( 0 => array( $icon, htmlspecialchars( CBTxt::T( $pluginRow->name ) ) . ( $title ? ': ' . $title : null ) ) );
			} else {
				if ( ! $icon ) {
					$icon			=	'cb-' . $name;
				}

				$_CB_Backend_Title	=	array( 0 => array( $icon, htmlspecialchars( CBTxt::T( 'Community Builder' ) ) . ( $title ? ': ' . $title : null ) ) );
			}

			// Null the label so the view form doesn't output it as we already did as page title:
			$viewModel->addAttribute( 'label', null );
		} else {
			$showDisclaimer			=	false;
		}

		$htmlFormatting				=	$viewModel->attributes( 'viewformatting' );

		if ( ! $htmlFormatting ) {
			$htmlFormatting			=	'div';
		}

		new cbTabs( true, 2 );

		$settingsHtml				=	$params->draw( null, null, null, null, null, null, false, $viewType, $htmlFormatting );

		if ( $htmlOutput ) {
			outputCbTemplate();
			outputCbJs();
			initToolTip();
		}

		$return						=	null;

		if ( $pluginRow && $pluginRow->id  ) {
			if ( ! $pluginRow->published ) {
				$return				.=	'<div class="alert alert-danger">' . CBTxt::T( 'PLUGIN_NAME_IS_NOT_PUBLISHED', '[plugin_name] is not published.', array( '[plugin_name]' => htmlspecialchars( CBTxt::T( $pluginRow->name ) ) ) ) . '</div>';
			}

			if ( ! $_PLUGINS->checkPluginCompatibility( $pluginRow ) ) {
				$return				.=	'<div class="alert alert-danger">' . CBTxt::T( 'PLUGIN_NAME_IS_NOT_COMPATIBLE_WITH_YOUR_CURRENT_CB_VERSION', '[plugin_name] is not compatible with your current CB version.', array( '[plugin_name]' => htmlspecialchars( CBTxt::T( $pluginRow->name ) ) ) ) . '</div>';
			}
		}

		if ( is_object( $data ) && isset( $data->id ) && $data->id ) {
			if ( isset( $data->published ) && ( ! $data->published ) ) {
				$return				.=	'<div class="alert alert-danger">' . CBTxt::T( 'NAME_IS_NOT_PUBLISHED', '[name] is not published.', array( '[name]' => htmlspecialchars( CBTxt::T( $label ) ) ) ) . '</div>';
			}

			if ( isset( $data->enabled ) && ( ! $data->enabled ) ) {
				$return				.=	'<div class="alert alert-danger">' . CBTxt::T( 'NAME_IS_NOT_ENABLED', '[name] is not enabled.', array( '[name]' => htmlspecialchars( CBTxt::T( $label ) ) ) ) . '</div>';
			}
		}

		if ( $viewModel->attributes( 'formformatting' ) == 'none' ) {
			$return					.=	( $settingsHtml ? $settingsHtml : null );
		} else {
			cbValidator::loadValidation();

			$cssClass				=	RegistryEditView::buildClasses( $viewModel, array(), 'block' );

			if ( ! $cssClass ) {
				$cssClass			=	'cb_form form-auto m-0';
			}

			$return					.=	'<form enctype="multipart/form-data" action="' . $_CB_framework->backendUrl( 'index.php' ) . '" method="post" name="adminForm" class="cbValidation ' . htmlspecialchars( $cssClass ) . '" id="cbAdminFormForm">'
									.		( $settingsHtml ? $settingsHtml : null )
									.		'<input type="hidden" name="option" value="' . htmlspecialchars( $options['option'] ) . '" />'
									.		( $pluginRow ? '<input type="hidden" name="cid" value="' . (int) $pluginRow->id . '" />' : null )
									.		( $cbprevstate ? '<input type="hidden" name="cbprevstate" value="' . htmlspecialchars( $cbprevstate ) . '" />' : null );

			if ( $actionPath ) foreach ( $actionPath as $k => $v ) {
				$return				.=		'<input type="hidden" name="' . htmlspecialchars( $k ) . '" value="' . htmlspecialchars( $v ) . '" />';
			}

			$return					.=		Application::Session()->getFormTokenInput()
									.	'</form>';
		}

		if ( $showDisclaimer ) {
			$disclaimerTitle		=	'Disclaimer';
			$disclaimerText			=	'This software comes "as is" with no guarantee for accuracy, function or fitness for any purpose.';
			$disclaimerTitleTr		=	CBTxt::Th( 'Disclaimer' );
			$disclaimerTextTr		=	CBTxt::Th( 'This software comes "as is" with no guarantee for accuracy, function or fitness for any purpose.' );

			$return					.=	'<div class="cbregCopyrightfooter content-spacer" style="font-size:11px; color:black; display:block;">'
									.		CBTxt::Th( 'CB_FOOTNOTE_OPEN_SOURCE_WITH_PLUGINS', 'Community Builder for Joomla, an open-source social framework by <a href="http://www.joomlapolis.com/?pk_campaign=in-cb&amp;pk_kwd=footer" target="_blank">Joomlapolis.com</a>, easy to extend with <a href="http://www.joomlapolis.com/cb-solutions?pk_campaign=in-cb&pk_kwd=footer" target="_blank">CB plugins</a>. Professional <a href="http://www.joomlapolis.com/support?pk_campaign=in-cb&pk_kwd=footer" target="_blank">Support</a> is available with a <a href="http://www.joomlapolis.com/memberships?pk_campaign=in-cb&pk_kwd=footer" target="_blank">Membership</a>.' )
									.		'<br /><strong>' . $disclaimerTitle . ':</strong> ' . $disclaimerText
									.		( $disclaimerText != $disclaimerTextTr ? '<br /><strong>' . $disclaimerTitleTr . ':</strong> ' . $disclaimerTextTr : null )
									.		'<br />'
									.		CBTxt::Th( 'CB_FOOTNOTE_REVIEW_AND_RATE_AT_JED', 'If you use Community Builder, please post a rating and a review on the <a href="[JEDURL]" target="_blank">Joomla! Extensions Directory</a>.', array( '[JEDURL]' => htmlspecialchars( 'https://extensions.joomla.org/extensions/clients-a-communities/communities/210 ' ) ) )
									.	'</div>';
		}

		echo $return;
	}
}
