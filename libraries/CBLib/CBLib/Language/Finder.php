<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 09.06.13 01:29 $
* @package ${NAMESPACE}
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CBLib\Language;

defined('CBLIB') or die();

/**
 * CBLib\Language\Finder (L like Language) Class implementation
 *
 */
class Finder
{

	/**
	 * Outputs an XML field type for searching language keys and text
	 *
	 * @param string $language
	 * @return string
	 */
	public static function input( $language = 'default' )
	{
		global $_CB_framework;

		static $JS_LOADED	=	0;

		if ( ! $JS_LOADED++ ) {
			$js				=	"var cbLangFinderRequest = null;"
							.	"var cbLangFinderPrevious = null;"
							.	"var cbLangFinderHandler = function() {"
							.		"var finder = $( this ).closest( '.cbLanguageFinder' );"
							.		"var search = finder.find( '.cbLanguageFinderSearch' ).val();"
							.		"var results = finder.find( '.cbLanguageFinderResults' );"
							.		"if ( ( cbLangFinderRequest == null ) && search && ( cbLangFinderPrevious != search ) ) {"
							.			"cbLangFinderPrevious = search;"
							.			"cbLangFinderRequest = $.ajax({"
							.				"url: '" . addslashes( $_CB_framework->backendViewUrl( 'languagefinder', false, array( 'language' => $language ), 'raw' ) ) . "',"
							.				"type: 'GET',"
							.				"dataType: 'html',"
							.				"cache: false,"
							.				"data: {"
							.					"search: search"
							.				"},"
							.				"beforeSend: function( jqXHR, textStatus, errorThrown ) {"
							.					"finder.find( '.cbLanguageFinderLoading' ).removeClass( 'hidden' );"
							.					"results.hide();"
							.				"}"
							.			"}).done( function( data, textStatus, jqXHR ) {"
							.				"results.html( data );"
							.				"results.fadeIn( 'slow' );"
							.				"results.find( '.cbMoreLess' ).cbmoreless();"
							.				"results.find( '.cbLanguageFinderResult' ).on( 'click', function() {"
							.					"var result = $( this );"
							.					"var resultKey = result.find( '.cbLanguageFinderResultKey' ).text();"
							.					"var resultText = result.find( '.cbLanguageFinderResultText' ).text();"
							.					"var resultFound = 0;" // No Empty or Existing found
							.					"$( 'input.cbLanguageOverrideKey' ).each( function() {"
							.						"if ( $( this ).val() == '' ) {"
							.							"$( this ).val( resultKey );"
							.							"$( this ).closest( '.cbRepeatRow' ).find( 'textarea.cbLanguageOverrideText' ).val( resultText ).focus();"
							.							"resultFound = 1;" // Empty Found
							.						"} else if ( $( this ).val() == resultKey ) {"
							.							"resultFound = 2;" // Existing Found
							.						"}"
							.					"});"
							.					"if ( resultFound === 0 ) {" // Add new row then populate it
							.						"$( '.cbLanguageOverrides' ).find( '.cbRepeat' ).cbrepeat( 'add' );"
							.						"$( 'input.cbLanguageOverrideKey' ).each( function() {"
							.							"if ( $( this ).val() == '' ) {"
							.								"$( this ).val( resultKey );"
							.								"$( this ).closest( '.cbRepeatRow' ).find( 'textarea.cbLanguageOverrideText' ).val( resultText ).focus();"
							.							"}"
							.						"});"
							.					"}"
							.				"});"
							.			"}).always( function( data, textStatus, jqXHR ) {"
							.				"cbLangFinderRequest = null;"
							.				"finder.find( '.cbLanguageFinderLoading' ).addClass( 'hidden' );"
							.			"});"
							.		"}"
							.	"};"
							.	"$( '.cbLanguageFinderSearch' ).on( 'keypress', function( e ) {"
							.		"if ( e.which == 13 ) {"
							.			"cbLangFinderHandler.call( this );"
							.		"}"
							.	"});"
							.	"$( '.cbLanguageFinderButton' ).on( 'click', cbLangFinderHandler );";

			$_CB_framework->outputCbJQuery( $js, 'cbmoreless' );
		}

		$return				=	'<div class="cbLanguageFinder">'
							.		'<div class="input-group">'
							.			'<input type="text" class="form-control cbLanguageFinderSearch" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Language Keys and Text...' ) ) . '" />'
							.			'<div class="input-group-append">'
							.				'<button class="cbLanguageFinderButton btn btn-primary" type="button">' . CBTxt::T( 'Find' ) . '</button>'
							.			'</div>'
							.		'</div>'
							.		'<div class="mt-2 cbLanguageFinderLoading text-secondary text-center hidden"><span class="spinner-border"></span></div>'
							.		'<div class="mt-2 cbLanguageFinderResults" style="max-height: 800px; overflow: auto;"></div>'
							.	'</div>';

		return $return;
	}

	/**
	 * Searches available language strings for a matching key or text
	 *
	 * @param string $language
	 * @param string $search
	 * @return string
	 */
	public static function find( $language, $search )
	{
		global $_CB_framework, $_PLUGINS;

		if ( ( ! $language ) || ( $language == 'default' ) ) {
			$language				=	'default_language';
		}

		if ( ! $search ) {
			return CBTxt::T( 'Nothing to search for.' );
		}

		// Load plugins in so we can loop through them later for their language files:
		$_PLUGINS->loadPluginGroup( 'users' );

		$languagesPath				=	$_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/language';
		$languageFiles				=	array( 'override.php', 'language.php', 'admin_language.php' );
		$languageStrings			=	array();

		// Load in the default language strings first:
		$defaultPath				=	$languagesPath . '/default_language';

		foreach ( $languageFiles as $languageFile ) {
			if ( ! file_exists( $defaultPath . '/' . $languageFile ) ) {
				continue;
			}

			$strings				=	include $defaultPath . '/' . $languageFile;

			if ( ! is_array( $strings ) ) {
				continue;
			}

			$languageStrings		=	array_merge( $languageStrings, $strings );
		}

		// Load in the plugin default language strings:
		foreach ( $_PLUGINS->getLoadedPluginGroup( null ) as $plugin ) {
			// Add language folder file paths for searching language specific strings below:
			$languageFiles[]		=	'cbplugin/' . $plugin->element . '-language.php';
			$languageFiles[]		=	'cbplugin/' . $plugin->element . '-admin_language.php';

			// Check if plugin has default language files defined:
			$pluginPath				=	$_CB_framework->getCfg( 'absolute_path' ) . '/' . $_PLUGINS->getPluginRelPath( $plugin ) . '/language/default_language';

			if ( file_exists( $pluginPath . '/language.php' ) ) {
				$strings			=	include $pluginPath . '/language.php';

				if ( ! is_array( $strings ) ) {
					continue;
				}

				$languageStrings	=	array_merge( $languageStrings, $strings );
			}

			if ( file_exists( $pluginPath . '/admin_language.php' ) ) {
				$strings			=	include $pluginPath . '/admin_language.php';

				if ( ! is_array( $strings ) ) {
					continue;
				}

				$languageStrings	=	array_merge( $languageStrings, $strings );
			}
		}

		// Load language specific strings (including for plugins):
		$languagePath				=	$languagesPath . '/' . strtolower( $language );

		if ( ( $language != 'default_language' ) && file_exists( $languagesPath . '/' . $language ) ) {
			foreach ( $languageFiles as $languageFile ) {
				if ( ! file_exists( $languagePath . '/' . $languageFile ) ) {
					continue;
				}

				$strings			=	include $languagePath . '/' . $languageFile;

				if ( ! is_array( $strings ) ) {
					continue;
				}

				$languageStrings	=	array_merge( $languageStrings, $strings );
			}
		}

		$results					=	null;

		foreach ( $languageStrings as $key => $text ) {
			if ( ( stripos( $key, $search ) !== false ) || ( stripos( $text, $search ) !== false ) ) {
				$results			.=	'<div class="cbLanguageFinderResult card mb-2" style="cursor: pointer;">'
									.		'<div class="cbLanguageFinderResultKey card-header text-wrapall">'
									.			htmlspecialchars( $key )
									.		'</div>'
									.		'<div class="cbLanguageFinderResultText card-body text-wrap">'
									.			htmlspecialchars( $text )
									.		'</div>'
									.	'</div>';
			}
		}

		if ( $results ) {
			return $results;
		}

		return CBTxt::T( 'No language key or string matches found.' );
	}
}