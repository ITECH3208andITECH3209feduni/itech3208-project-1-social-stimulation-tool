<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class CBViewHelper
{
	/**
	 * Outputs JS to scroll tabs into view when their tab navigation link is clicked
	 *
	 * @return void
	 */
	public static function scrollTabNav(): void
	{
		global $_CB_framework;

		$js		=	"$( '.cbTabsNav .cbTabNavLink' ).on( 'click', function() {"
				.		"const tab = $( this ).parent().attr( 'id' );"
				.		"if ( tab ) {"
				.			"const pane = document.getElementById( tab.replace( 'cbtabnav', 'cbtabpane' ) );"
				.			"if ( pane ) {"
				.				"const bounds = pane.getBoundingClientRect();"
				.				"const top = ( bounds.y + window.scrollY );"
				.				"window.scrollTo( ( bounds.x + window.scrollX ), ( top > 300 ? ( top - 100 ) : 0 ) );"
				.			"}"
				.		"}"
				.	"});";

		$_CB_framework->outputCbJQuery( $js );
	}
}

class CBProfileView_html_default extends cbProfileView
{
	/** @var bool  */
	protected bool $userSelf					=	false;
	/** @var string  */
	protected string $header					=	'';
	/** @var string  */
	protected string $footer					=	'';
	/** @var string  */
	protected string $canvasMenu				=	'';
	/** @var string  */
	protected string $canvasBackground			=	'';
	/** @var string  */
	protected string $canvasPhoto				=	'';
	/** @var string  */
	protected string $canvasInfo				=	'';
	/** @var string  */
	protected string $canvasTitle				=	'';
	/** @var string  */
	protected string $canvasStats				=	'';
	/** @var string  */
	protected string $canvasMain				=	'';
	/** @var string  */
	protected string $mainLeft					=	'';
	/** @var string  */
	protected string $mainMiddle				=	'';
	/** @var string  */
	protected string $mainRight					=	'';
	/** @var string  */
	protected string $tabMain					=	'';
	/** @var array  */
	protected array $lineColumns				=	[];

	/**
	 * Renders profile view layout
	 *
	 * @return void
	 */
	protected function _renderLayout(): void
	{
		global $_CB_framework;

		$this->userSelf				=	( ( $this->user->getInt( 'id', 0 ) === Application::MyUser()->getUserId() ) && ( Application::Input()->getString( 'get/viewas', '' ) !== 'other' ) );

		$this->header				=	( $this->userViewTabs['cb_head'] ?? '' );
		$this->footer				=	( $this->userViewTabs['cb_underall'] ?? '' );

		$this->canvasMenu			=	( $this->userViewTabs['canvas_menu'] ?? '' );

		$this->canvasBackground		=	( $this->userViewTabs['canvas_background'] ?? '' );
		$this->canvasPhoto			=	( $this->userViewTabs['canvas_photo'] ?? '' );
		$this->canvasInfo			=	( $this->userViewTabs['canvas_info'] ?? '' );

		$this->canvasTitle			=	( $this->userViewTabs['canvas_title'] ?? '' );
		$this->canvasTitle			.=	( $this->userViewTabs['canvas_title_top'] ?? '' );		// For B/C
		$this->canvasTitle			.=	( $this->userViewTabs['canvas_title_middle'] ?? '' );	// For B/C
		$this->canvasTitle			.=	( $this->userViewTabs['canvas_title_bottom'] ?? '' );	// For B/C

		$this->canvasStats			=	( $this->userViewTabs['canvas_stats'] ?? '' );
		$this->canvasStats			.=	( $this->userViewTabs['canvas_stats_top'] ?? '' );		// For B/C
		$this->canvasStats			.=	( $this->userViewTabs['canvas_stats_middle'] ?? '' );	// For B/C
		$this->canvasStats			.=	( $this->userViewTabs['canvas_stats_bottom'] ?? '' );	// For B/C

		$this->canvasMain			=	( $this->userViewTabs['canvas_main_middle'] ?? '' );

		$this->mainLeft				=	( $this->userViewTabs['cb_left'] ?? '' );
		$this->mainMiddle			=	( $this->userViewTabs['cb_middle'] ?? '' );
		$this->mainRight			=	( $this->userViewTabs['cb_right'] ?? '' );

		$this->tabMain				=	( $this->userViewTabs['cb_tabmain'] ?? '' );

		foreach ( $this->userViewTabs as $k => $v ) {
			if ( ( ! $v ) || ( $k[0] !== 'L' ) ) {
				continue;
			}

			$line								=	(int) ( $k[1] ?? 0 );
			$column								=	(int) ( $k[3] ?? 0 );

			if ( ( ! $line ) || ( ! $column ) ) {
				continue;
			}

			if ( ! array_key_exists( $line, $this->lineColumns ) ) {
				$this->lineColumns[$line]		=	[];
			}

			$this->lineColumns[$line][$column]	=	$v;
		}

		switch ( $this->userViewLayout ) {
			case 'custom':
				$content						=	$this->renderCustom();
				$layoutClass					=	'Custom';
				break;
			case 'intranet':
				$content						=	$this->renderIntranet();
				$layoutClass					=	'Intranet';
				break;
			case 'canvas_home':
				$content						=	$this->renderCanvasHome();
				$layoutClass					=	'CanvasHome';
				break;
			case 'canvas_other':
			case 'canvas_other_alt':
			default:
				$content						=	$this->renderCanvasOther();
				$layoutClass					=	'CanvasOther';
				break;
		}

		if ( $this->userViewScroll ) {
			CBViewHelper::scrollTabNav();
		}

		$pageClass								=	$_CB_framework->getMenuPageClass();

		echo 	'<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . ' cbProfile cbProfile' . htmlspecialchars( $layoutClass ) . ( $pageClass ? ' ' . htmlspecialchars( $pageClass ) : null ) . '">'
			.		( $this->userViewLayout !== 'custom' ? $this->integrations : '' )
			.		$content
			.	'</div>';
	}

	/**
	 * Render canvas layout when viewing self
	 *
	 * @return string
	 */
	protected function renderCanvasHome(): string
	{
		$return							=	'';

		if ( $this->header ) {
			$return						.=	'<div class="cbPosHead">'
										.		$this->header
										.	'</div>';
		}

		$return							.=	'<div class="d-flex flex-column gap-3 cbCanvasHomeLayout">';

		if ( $this->canvasMenu ) {
			$return						.=		'<div class="cbCanvasHomeLayoutMenu">'
										.			$this->canvasMenu
										.		'</div>';
		}

		$return							.=		'<div class="d-flex flex-wrap flex-md-nowrap gap-3 cbCanvasHomeLayoutMain">';

		if ( $this->canvasBackground || $this->canvasStats || $this->canvasPhoto || $this->canvasTitle || $this->userViewTabsNav || $this->mainLeft ) {
			$return						.=			'<div class="d-flex flex-column flex-shrink-0 gap-3 cbCanvasHomeLayoutLeft">';

			if ( $this->canvasBackground || $this->canvasPhoto || $this->canvasTitle ) {
				$return					.=				'<div class="position-relative p-2 bg-light border rounded text-center cbCanvasHomeLayoutUser">'
										.					( $this->canvasBackground ? '<div class="position-absolute rounded-top bg-secondary cbCanvasHomeLayoutCanvas">' . $this->canvasBackground . '</div>' : '' )
										.					( $this->canvasPhoto ? '<div class="cbCanvasHomeLayoutAvatar">' . $this->canvasPhoto . '</div>' : '' )
										.					( $this->canvasTitle ? '<div class="cbCanvasHomeLayoutName">' . $this->canvasTitle . '</div>' : '' )
										.				'</div>';
			}

			if ( $this->canvasInfo ) {
				$return					.=				'<div class="cbCanvasHomeLayoutInfo">'
										.					$this->canvasInfo
										.				'</div>';
			}

			if ( $this->canvasStats ) {
				$return					.=				'<div class="cbCanvasHomeLayoutStats">'
										.					$this->canvasStats
										.				'</div>';
			}

			if ( $this->userViewTabsNav ) {
				$return					.=				'<div class="cbCanvasHomeLayoutNav">'
										.					implode( '', $this->userViewTabsNav )
										.				'</div>';
			}

			if ( $this->mainLeft ) {
				$return					.=				'<div class="cbCanvasHomeLayoutMainLeft">'
										.					$this->mainLeft
										.				'</div>';
			}

			$return						.=			'</div>';
		}

		if ( $this->canvasMain || $this->mainMiddle || $this->tabMain || $this->lineColumns ) {
			$return						.=			'<div class="d-flex flex-column flex-grow-1 cbCanvasHomeLayoutMiddle">'
										.				( $this->canvasMain ?: '' )
										.				( $this->mainMiddle ?: '' )
										.				( $this->tabMain ?: '' );

			foreach ( $this->lineColumns as $line => $columns ) {
				$return					.=				'<div class="row no-gutters cbPosLine cbPosLine' . $line . '">';

				foreach ( $columns as $column => $contents ) {
					$return				.=					'<div class="col-sm cbPosLineCol cbPosLineCol' . $column . '">'
										.						$contents
										.					'</div>';
				}

				$return					.=				'</div>';
			}

			$return						.=			'</div>';
		}

		if ( $this->mainRight ) {
			$return						.=			'<div class="position-relative d-flex flex-column flex-shrink-0 gap-3 cbCanvasHomeLayoutRight">'
										.				'<div class="cbCanvasHomeLayoutMainRight">'
										.					$this->mainRight
										.				'</div>'
										.			'</div>';
		}

		$return							.=		'</div>'
										.	'</div>';

		if ( $this->footer ) {
			$return						.=	'<div class="cbPosUnderAll">'
										.		$this->footer
										.	'</div>';
		}

		return $return;
	}

	/**
	 * Render canvas layout when viewing another user
	 *
	 * @return string
	 */
	protected function renderCanvasOther(): string
	{
		$return							=	'';

		if ( $this->header ) {
			$return						.=	'<div class="cbPosHead">'
										.		$this->header
										.	'</div>';
		}

		$canvasHeader					=	( $this->canvasBackground || $this->canvasStats || $this->canvasPhoto || $this->canvasTitle );
		$canvasNav						=	( $this->userViewTabsNav ? implode( '', $this->userViewTabsNav ) : '' );

		if ( $this->canvasMenu || $canvasHeader || $this->canvasMain || $canvasNav ) {
			if ( $this->canvasMenu ) {
				$return					.=	'<div class="cbPosCanvasMenu cbCanvasLayoutMenu">'
										.		$this->canvasMenu
										.	'</div>';
			}

			if ( $canvasHeader ) {
				$return					.=	'<div class="position-relative no-overflow border' . ( $this->canvasMenu ? ' border-top-0' : ' rounded-top' ) . ( ( ! $canvasNav ) && $this->canvasMain ? ' border-bottom-0' : ' rounded-bottom' ) . ' cbPosCanvas cbCanvasLayout' . ( $canvasNav ? ' cbCanvasLayoutLeftNav' : '' ) . '">';

				if ( $this->canvasBackground ) {
					$return				.=		'<div class="position-relative bg-light row no-gutters align-items-lg-end cbPosCanvasTop cbCanvasLayoutTop">'
										.			'<div class="position-absolute col-12 cbPosCanvasBackground cbCanvasLayoutBackground">'
										.				$this->canvasBackground
										.			'</div>'
										.		'</div>';
				}

				if ( $this->canvasPhoto || $this->canvasTitle || $this->canvasStats || $this->canvasInfo ) {
					$return				.=		'<div class="position-relative row no-gutters align-items-end bg-white' . ( $this->canvasBackground ? ' border-top' : '' ) . ( ! $this->canvasPhoto ? ' p-2' : '' ) . ' cbPosCanvasBottom cbCanvasLayoutBottom">';

					if ( $this->canvasPhoto ) {
						$return			.=			'<div class="' . ( ! $this->canvasBackground ? 'col-12 col-sm-3 mh-none' : 'col-4 col-sm-3' ) . '">'
										.				'<div class="' . ( $this->canvasBackground ? 'position-absolute' : 'p-2' ) . ' cbPosCanvasPhoto cbCanvasLayoutPhoto">'
										.					$this->canvasPhoto
										.				'</div>'
										.			'</div>'
										.			'<div class="' . ( ! $this->canvasBackground ? 'col-12 col-sm-9 align-self-end' : 'col-8 col-sm-9' ) . '">'
										.				'<div class="p-2">';
					}

					if ( $this->canvasTitle || $this->canvasInfo ) {
						$return			.=					'<div class="row no-gutters">';

						if ( $this->canvasTitle ) {
							$return		.=						'<div class="order-0 col text-primary text-large font-weight-bold cbPosCanvasTitle cbCanvasLayoutTitle">'
										.							$this->canvasTitle
										.						'</div>';
						}

						if ( $this->canvasInfo ) {
							$return		.=						'<div class="ml-0 ml-sm-1 mt-2 mt-sm-0 order-last order-sm-1 col-12 col-sm-auto cbPosCanvasInfo cbCanvasLayoutInfo">'
										.							$this->canvasInfo
										.						'</div>';
						}

						$return			.=					'</div>';
					}

					if ( $this->canvasStats ) {
						$return			.=					'<div class="mt-2 mt-sm-1 text-muted text-overflow text-small cbPosCanvasStats cbCanvasLayoutCounters">'
										.						$this->canvasStats
										.					'</div>';
					}

					if ( $this->canvasPhoto ) {
						$return			.=				'</div>'
										.			'</div>';
					}

					$return				.=		'</div>';
				}

				$return					.=	'</div>';
			}

			if ( $canvasNav ) {
				$return					.=	'<div class="d-flex flex-wrap flex-md-nowrap gap-3 mt-0 mt-md-3 cbPosCanvasLeft cbCanvasLayoutLeft">'
										.		'<div class="flex-shrink-0 cbPosCanvasNav cbCanvasLayoutNav">'
										.			$canvasNav
										.		'</div>'
										.		'<div class="flex-grow-1 cbPosCanvasMiddle cbCanvasLayoutMiddle">';

				if ( $this->canvasMain ) {
					$return				.=			'<div class="cbPosCanvasMain cbCanvasLayoutMain">'
										.				$this->canvasMain
										.			'</div>';
				}
			} elseif ( $this->canvasMain ) {
				$return					.=	'<div class="cbPosCanvasMain cbCanvasLayoutMain">'
										.		$this->canvasMain
										.	'</div>';
			}
		}

 		if ( $this->mainLeft || $this->mainMiddle || $this->mainRight ) {
			if ( $return ) {
				$return					.=	'<div class="pt-2 pb-2 cbPosSeparator"></div>';
			}

			$return						.=	'<div class="row no-gutters cbPosTop">';

			if ( $this->mainLeft ) {
				$return					.=		'<div class="col-sm' . ( $this->userMainSizes['left'] ? '-' . $this->userMainSizes['left'] : '' ) . ( $this->mainMiddle || $this->mainRight ? ' pr-sm-2' : '' ) . ' cbPosLeft">'
										.			$this->mainLeft
										.		'</div>';
			}

			if ( $this->mainMiddle ) {
				$return					.=		'<div class="col-sm' . ( $this->userMainSizes['middle'] ? '-' . $this->userMainSizes['middle'] : '' ) . ' cbPosMiddle">'
										.			$this->mainMiddle
										.		'</div>';
			}

			if ( $this->mainRight ) {
				$return					.=		'<div class="col-sm' . ( $this->userMainSizes['right'] ? '-' . $this->userMainSizes['right'] : '' ) . ( $this->mainMiddle || $this->mainLeft ? ' pl-sm-2' : '' ) . ' cbPosRight">'
										.			$this->mainRight
										.		'</div>';
			}

			$return						.=	'</div>';
		}

		if ( $this->tabMain ) {
			if ( $return ) {
				$return					.=	'<div class="pt-2 pb-2 cbPosSeparator"></div>';
			}

			$return						.=	'<div class="cbPosTabMain">'
										.		$this->tabMain
										.	'</div>';
		}

		foreach ( $this->lineColumns as $line => $columns ) {
			if ( $return ) {
				$return					.=	'<div class="pt-2 pb-2 cbPosSeparator"></div>';
			}

			$return						.=	'<div class="row no-gutters cbPosLine cbPosLine' . $line . '">';

			foreach ( $columns as $column => $contents ) {
				$return					.=		'<div class="col-sm cbPosLineCol cbPosLineCol' . $column . '">'
										.			$contents
										.		'</div>';
			}

			$return						.=	'</div>';
		}

		if ( $canvasNav ) {
			$return						.=		'</div>'
										.	'</div>';
		}

		if ( $this->footer ) {
			if ( $return ) {
				$return					.=	'<div class="pt-2 pb-2 cbPosSeparator"></div>';
			}

			$return						.=	'<div class="cbPosUnderAll">'
										.		$this->footer
										.	'</div>';
		}

		return $return;
	}

	/**
	 * Render canvas layout when viewing self
	 *
	 * @return string
	 */
	protected function renderIntranet(): string
	{
		$return							=	'';

		if ( $this->header ) {
			$return						.=	'<div class="cbPosHead">'
										.		$this->header
										.	'</div>';
		}

		$return							.=	'<div class="d-flex flex-column cbIntranetLayout">';

		if ( $this->canvasMenu ) {
			$return						.=		'<div class="cbIntranetLayoutMenu">'
										.			$this->canvasMenu
										.		'</div>';
		}

		if ( $this->canvasStats || $this->canvasPhoto || $this->canvasTitle ) {
			$return						.=		'<div class="d-flex flex-column flex-md-row text-center text-md-left align-items-center gap-3 p-3 bg-light border cbIntranetLayoutTop">';

			if ( $this->canvasPhoto ) {
				$return					.=			'<div class="d-flex flex-column flex-shrink-0 text-center cbIntranetLayoutPhoto">'
										.				$this->canvasPhoto
										.			'</div>';
			}

			if ( $this->canvasTitle || $this->canvasInfo ) {
				$return					.=			'<div class="d-flex flex-column flex-grow-1 gap-3 cbIntranetLayoutUser">'
										.				( $this->canvasTitle ? '<div class="text-primary text-large font-weight-bold cbIntranetLayoutName">' . $this->canvasTitle . '</div>' : '' )
										.				( $this->canvasInfo ? '<div class="cbIntranetLayoutInfo">' . $this->canvasInfo . '</div>' : '' )
										.			'</div>';
			}

			if ( $this->canvasStats ) {
				$return					.=			'<div class="d-flex flex-column flex-shrink-0 align-items-end gap-3 text-center cbIntranetLayoutStats">'
										.				$this->canvasStats
										.			'</div>';
			}

			$return						.=		'</div>';
		}

		$return							.=		'<div class="d-flex flex-wrap flex-md-nowrap gap-3 cbIntranetLayoutMain">';

		if ( $this->userViewTabsNav || $this->mainLeft ) {
			$return						.=			'<div class="d-flex flex-column flex-shrink-0 gap-3 pt-3 cbIntranetLayoutLeft">';

			if ( $this->userViewTabsNav ) {
				$return					.=				'<div class="cbIntranetLayoutNav">'
										.					implode( '', $this->userViewTabsNav )
										.				'</div>';
			}

			if ( $this->mainLeft ) {
				$return					.=				'<div class="cbCanvasHomeLayoutMainLeft">'
										.					$this->mainLeft
										.				'</div>';
			}

			$return						.=			'</div>';

			if ( $this->canvasMain || $this->mainMiddle || $this->tabMain || $this->lineColumns || $this->mainRight ) {
				$return					.=			'<div class="border-right cbIntranetLayoutSeperator"></div>';
			}
		}

		if ( $this->canvasMain || $this->mainMiddle || $this->tabMain || $this->lineColumns ) {
			$return						.=			'<div class="d-flex flex-column flex-grow-1' . ( $this->userViewTabsNav || $this->mainLeft || $this->mainRight ? ' pt-3' : '' ) . ' cbIntranetLayoutMiddle">'
										.				( $this->canvasMain ?: '' )
										.				( $this->mainMiddle ?: '' )
										.				( $this->tabMain ?: '' );

			foreach ( $this->lineColumns as $line => $columns ) {
				$return					.=				'<div class="row no-gutters cbPosLine cbPosLine' . $line . '">';

				foreach ( $columns as $column => $contents ) {
					$return				.=					'<div class="col-sm cbPosLineCol cbPosLineCol' . $column . '">'
										.						$contents
										.					'</div>';
				}

				$return					.=				'</div>';
			}

			$return						.=			'</div>';
		}

		if ( $this->mainRight ) {
			$return						.=			'<div class="d-flex flex-column flex-shrink-0 gap-3 pt-3 cbIntranetLayoutRight">'
										.				'<div class="cbIntranetLayoutMainRight">'
										.					$this->mainRight
										.				'</div>'
										.			'</div>';
		}

		$return							.=		'</div>'
										.	'</div>';

		if ( $this->footer ) {
			$return						.=	'<div class="cbPosUnderAll">'
										.		$this->footer
										.	'</div>';
		}

		return $return;
	}

	/**
	 * Render custom substitution supported layout
	 *
	 * @return string
	 */
	protected function renderCustom(): string
	{
		if ( ! $this->userViewCustom ) {
			// Empty profile layout is not allowed so fallback to canvas_other
			return $this->renderCanvasOther();
		}

		$extra		=	[	'viewing'						=>	( $this->userSelf ? 'self' : 'other' ),
							'navigation'					=>	implode( '', $this->userViewTabsNav ),
							'content_header'				=>	$this->header,
							'content_footer'				=>	$this->footer,
							'content_canvas_menu'			=>	$this->canvasMenu,
							'content_canvas_main'			=>	$this->canvasMain,
							'content_canvas_background'		=>	$this->canvasBackground,
							'content_canvas_photo'			=>	$this->canvasPhoto,
							'content_canvas_title'			=>	$this->canvasTitle,
							'content_canvas_info'			=>	$this->canvasInfo,
							'content_canvas_stats'			=>	$this->canvasStats,
							'content_main_left'				=>	$this->mainLeft,
							'content_main_middle'			=>	$this->mainMiddle,
							'content_main_right'			=>	$this->mainRight,
							'content_main'					=>	$this->tabMain,
						];

		foreach ( $this->lineColumns as $line => $columns ) {
			foreach ( $columns as $column => $contents ) {
				$extra['content_line_' . $line . '_column_' . $column]	=	$contents;
			}
		}

		$layout		=	Application::Cms()->prepareHtmlContentPlugins( $this->userViewCustom, 'profile.view.layout', $this->user->getInt( 'id', 0 ) );

		if ( ! $layout ) {
			// Empty profile layout is not allowed so fallback to canvas_other
			return $this->renderCanvasOther();
		}

		$layout		=	CBuser::getInstance( $this->user->getInt( 'id', 0 ), false )->replaceUserVars( $layout, false, true, $extra );

		if ( ! $layout ) {
			// Empty profile layout is not allowed so fallback to canvas_other
			return $this->renderCanvasOther();
		}

		return $layout;
	}
}

class CBEditView_html_default extends cbEditView
{
	/**
	 * Renders profile edit layout
	 *
	 * @return void
	 */
	protected function _renderLayout(): void
	{
		global $_CB_framework;

		cbValidator::loadValidation();

		if ( $this->userEditScroll ) {
			CBViewHelper::scrollTabNav();
		}

		$header				=	'';
		$footer				=	'';

		switch ( $this->userEditLayout ) {
			case 'custom':
				$content	=	$this->renderCustom();
				break;
			case 'menu':
			case 'flat':
			case 'tabbed':
			case 'vertical':
			default:
				$content	=	$this->renderTabs();

				$header		.=	'<div class="mb-3 border-bottom cb-page-header">'
							.		'<h3 class="m-0 p-0 mb-2 cb-page-header-title">' . $this->userEditTitle . '</h3>'
							.	'</div>';

				if ( $this->iconsTop ) {
					$header	.=	'<div class="' . ( $content ?  'mb-3 ' : '' ) . 'cbIconsTop">'
							.		$this->iconsTop
							.	'</div>';
				}

				if ( $this->iconsBottom ) {
					$footer	.=	'<div class="' . ( $content ?  'mt-3 ' : '' ) . 'cbIconsBottom">'
							.		$this->iconsBottom
							.	'</div>';
				}
				break;
		}

		if ( $_CB_framework->getCfg( 'debug' ) && ( strpos( $content, '<form' ) !== false ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'Profile edit is already enclosed in a form, but a tab, field, or custom layout is rendering a form. This results in a nested form and will break profile edit. Please review your configuration carefully.' ) );
		}

		$pageClass	=	$_CB_framework->getMenuPageClass();

		echo 	'<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . ' cbEditProfile cbEditProfile' . htmlspecialchars( ucfirst( $this->userEditLayout ) ) . ( $pageClass ? ' ' . htmlspecialchars( $pageClass ) : '' ) . '">'
			.		$header
			.		( $this->userEditLayout !== 'custom' ? $this->integrations : '' )
			.		'<form action="' . $_CB_framework->viewUrl( 'saveuseredit' ) . '" method="post" id="cbcheckedadminForm" name="adminForm" enctype="multipart/form-data" autocomplete="off" class="form-auto cb_form cbValidation">'
			.			'<input type="hidden" name="id" value="' . $this->user->getInt( 'id', 0 ) . '" />'
			.			Application::Session()->getFormTokenInput()
			.			$content
			.		'</form>'
			.		$footer
			.	'</div>';
	}

	/**
	 * Render tab based layouts
	 *
	 * @return string
	 */
	protected function renderTabs(): string
	{
		global $_CB_framework;

		$nav			=	( $this->userEditNav ? implode( '', $this->userEditNav ) : '' );
		$content		=	( is_array( $this->userEditContent ) ? implode( '', $this->userEditContent ) : $this->userEditContent );
		$return			=	'';

		$buttons		=	'<div class="row no-gutters cbProfileEditButtons">'
						.		'<div class="offset-sm-3 col-sm-9">'
						.			'<button type="submit" class="btn btn-primary btn-sm-block cbProfileEditSubmit"' . cbValidator::getSubmitBtnHtmlAttributes() . '>' . $this->buttonSubmit . '</button>'
						.			' <a href="' . $_CB_framework->userProfileUrl( $this->user->getInt( 'id', 0 ), true, null, 'html', 0, [ 'reason' => 'canceledit' ] ) . '" class="btn btn-secondary btn-sm-block cbProfileEditCancel">' . $this->buttonCancel . '</a>'
						.		'</div>'
						.	'</div>';

		if ( ( $this->userEditLayout === 'menu' ) && $nav ) {
			$return		.=	'<div class="d-flex flex-column flex-md-row gap-3">'
						.		'<div class="flex-shrink-0 cbEditLayoutNav">'
						.			$nav
						.		'</div>'
						.		'<div class="flex-grow-1">'
						.			$content
						.			$buttons
						.		'</div>'
						.	'</div>';
		} else {
			$return		.=	$nav
						.	$content
						.	$buttons;
		}

		return $return;
	}

	/**
	 * Render custom substitution supported layout
	 *
	 * @return string
	 */
	protected function renderCustom(): string
	{
		global $_CB_framework;

		if ( ! $this->userEditCustom ) {
			// Empty profile layout is not allowed so fallback to tabbed
			return $this->renderTabs();
		}

		$extra		=	[	'title'				=>	$this->userEditTitle,
							'navigation'		=>	implode( '', $this->userEditNav ),
							'tabs'				=>	( $this->userEditContent['tabs'] ?? '' ),
							'content'			=>	'',
							'legend'			=>	( $this->iconsTop ?: $this->iconsBottom ),
							'integrations'		=>	$this->integrations,
							'button_submit'		=>	'<button type="submit" class="btn btn-primary btn-sm-block cbProfileEditSubmit"' . cbValidator::getSubmitBtnHtmlAttributes() . '>' . $this->buttonSubmit . '</button>',
							'button_cancel'		=>	'<a href="' . $_CB_framework->userProfileUrl( $this->user->getInt( 'id', 0 ), true, null, 'html', 0, [ 'reason' => 'canceledit' ] ) . '" class="btn btn-secondary btn-sm-block cbProfileEditCancel">' . $this->buttonCancel . '</a>',
						];

		foreach ( $this->userEditContent as $tabId => $content ) {
			if ( ! is_int( $tabId ) ) {
				continue;
			}

			$extra['title_' . $tabId]			=	( $content['title'] ?? '' );
			$extra['content_' . $tabId]			=	( $content['content'] ?? '' );
			$extra['tab_' . $tabId]				=	( $content['tab'] ?? '' );
			$extra['content']					.=	$extra['content_' . $tabId];
		}

		$layout		=	Application::Cms()->prepareHtmlContentPlugins( $this->userEditCustom, 'profile.edit.layout', $this->user->getInt( 'id', 0 ) );

		if ( ! $layout ) {
			// Empty profile layout is not allowed so fallback to tabbed
			return $this->renderTabs();
		}

		$layout		=	CBuser::getInstance( $this->user->getInt( 'id', 0 ), false )->replaceUserVars( $layout, false, true, $extra );

		if ( ! $layout ) {
			// Empty profile layout is not allowed so fallback to tabbed
			return $this->renderTabs();
		}

		return $layout;
	}
}

class CBRegistrationView_html_default extends cbRegistrationView
{
	/**
	 * Renders registration layout
	 *
	 * @return void
	 */
	protected function _renderLayout( )
	{
		global $_CB_framework;

		cbValidator::loadValidation();

		if ( $this->userRegScroll ) {
			CBViewHelper::scrollTabNav();
		}

		$header				=	'';
		$footer				=	'';

		switch ( $this->userRegLayout ) {
			case 'custom':
				$content	=	$this->renderCustom();
				break;
			case 'menu':
			case 'flat':
			case 'tabbed':
			case 'vertical':
			case 'stepped':
			default:
				$content				=	$this->renderTabs();

				if ( $this->userRegTitle || $this->headerMessage ) {
					if ( $this->titleCanvas ) {
						$header			.=	'<div class="position-relative no-overflow border rounded-top mb-3 cbCanvasLayout cbCanvasLayoutMd cbRegistrationHeader">'
										.		'<div class="position-relative bg-light row no-gutters align-items-end cbCanvasLayoutTop cbRegistrationHeaderInner">'
										.			'<div class="position-absolute col-12 cbCanvasLayoutBackground cbRegistrationHeaderBackground">'
										.				'<div class="cbCanvasLayoutBackgroundImage" style="background-image: url(' . htmlspecialchars( $this->titleCanvas ) . ')"></div>'
										.			'</div>'
										.			'<div class="p-2 col-12 cbCanvasLayoutInfo">';

						if ( $this->userRegTitle ) {
							$header		.=				'<h3 class="cbRegistrationTitle">' . $this->userRegTitle . '</h3>';
						}

						if ( $this->headerMessage ) {
							$header		.=				'<div class="cbRegistrationIntro">'
										.					$this->headerMessage
										.				'</div>';
						}

						$header			.=			'</div>'
										.		'</div>'
										.	'</div>';
					} elseif ( $this->userRegTitle ) {
						$header			.=	'<div class="mb-3 border-bottom cb-page-header cbRegistrationHeader">'
										.		'<h3 class="m-0 p-0 mb-2 cb-page-header-title cbRegistrationTitle">' . $this->userRegTitle . '</h3>';

						if ( $this->headerMessage ) {
							$header		.=		'<div class="mb-2 cb-page-header-description cbRegistrationIntro">'
										.			$this->headerMessage
										.		'</div>';
						}

						$header			.=	'</div>';
					} elseif ( $this->headerMessage ) {
						$header			.=	'<div class="mb-3 cb-page-header-description cbRegistrationIntro">'
										.		$this->headerMessage
										.	'</div>';
					}
				}

				if ( $this->iconsTop ) {
					$header				.=	'<div class="' . ( $content ?  'mb-3 ' : '' ) . 'cbIconsTop">'
										.		$this->iconsTop
										.	'</div>';
				}

				if ( $this->iconsBottom ) {
					$footer				.=	'<div class="' . ( $content ?  'mt-3 ' : '' ) . 'cbIconsBottom">'
										.		$this->iconsBottom
										.	'</div>';
				}

				if ( $this->footerMessage ) {
					$footer				.=	'<div class="mt-3' . ( $this->titleCanvas ? ' p-2 bg-light border rounded-bottom' : null ) . ' cbRegistrationConclusion">'
										.		$this->footerMessage
										.	'</div>';
				}
				break;
		}

		if ( $_CB_framework->getCfg( 'debug' ) && ( strpos( $content, '<form' ) !== false ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'Registration is already enclosed in a form, but a tab, field, or custom layout is rendering a form. This results in a nested form and will break registration. Please review your configuration carefully.' ) );
		}

		if ( $this->moduleContent ) {
			echo 	'<div class="cbRegistrationContainer">'
					.	'<div class="cbRegistrationLogin">'
					.		$this->moduleContent
					.	'</div>';
		}

		$pageClass	=	$_CB_framework->getMenuPageClass();

		echo 	'<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . ' cbRegistration cbRegistration' . htmlspecialchars( ucfirst( $this->userRegLayout ) ) . ( $this->titleCanvas ? ' cbRegistrationCanvas' : '' ) . ( $pageClass ? ' ' . htmlspecialchars( $pageClass ) : '' ) . '">'
			.		$header
			.		'<form action="' . $_CB_framework->viewUrl( 'saveregisters', true, null, 'html', ( $this->forceHTTPS ? 1 : 0 ) ) . '" method="post" id="cbcheckedadminForm" name="adminForm" enctype="multipart/form-data" class="form-auto m-0 cb_form cbValidation">'
			.			'<input type="hidden" name="id" value="0" />'
			.			'<input type="hidden" name="gid" value="0" />'
			.			'<input type="hidden" name="emailpass" value="' . (int) $this->emailPassword . '" />'
			.			Application::Session()->getFormTokenInput()
			.			$content
			.		'</form>'
			.		$footer
			.	'</div>';

		if ( $this->moduleContent ) {
			echo '</div>';
		}
	}

	/**
	 * Render tab based layouts
	 *
	 * @return string
	 */
	protected function renderTabs(): string
	{
		$nav			=	( $this->userRegNav ? implode( '', $this->userRegNav ) : '' );
		$content		=	( is_array( $this->userRegContent ) ? implode( '', $this->userRegContent ) : $this->userRegContent );

		$buttons		=	'<div class="row no-gutters cbRegistrationButtons">'
						.		'<div class="offset-sm-3 col-sm-9">'
						.			'<button type="submit" class="btn btn-primary btn-sm-block cbRegistrationSubmit"' . cbValidator::getSubmitBtnHtmlAttributes() . '>' . $this->buttonSubmit . '</button>'
						.		'</div>'
						.	'</div>';

		$return			=	'<div id="registrationTable" class="cbRegistrationDiv">';

		if ( ( $this->userRegLayout === 'menu' ) && $nav ) {
			$return		.=		'<div class="d-flex flex-column flex-md-row gap-3">'
						.			'<div class="flex-shrink-0 cbRegistrationLayoutNav">'
						.				$nav
						.			'</div>'
						.			'<div class="flex-grow-1">'
						.				$content
						.				$buttons
						.			'</div>'
						.		'</div>';
		} else {
			$return		.=		$nav
						.		$content
						.		$buttons;
		}

		$return			.=	'</div>';

		return $return;
	}

	/**
	 * Render custom substitution supported layout
	 *
	 * @return string
	 */
	protected function renderCustom(): string
	{
		if ( ! $this->userRegCustom ) {
			// Empty profile layout is not allowed so fallback to tabbed
			return $this->renderTabs();
		}

		$extra		=	[	'title'				=>	$this->userRegTitle,
							'canvas'			=>	$this->titleCanvas,
							'header'			=>	$this->headerMessage,
							'footer'			=>	$this->footerMessage,
							'navigation'		=>	implode( '', $this->userRegNav ),
							'tabs'				=>	( $this->userEditContent['tabs'] ?? '' ),
							'content'			=>	'',
							'legend'			=>	( $this->iconsTop ?: $this->iconsBottom ),
							'integrations'		=>	$this->integrations,
							'button_submit'		=>	'<button type="submit" class="btn btn-primary btn-sm-block cbRegistrationSubmit"' . cbValidator::getSubmitBtnHtmlAttributes() . '>' . $this->buttonSubmit . '</button>',
						];

		foreach ( $this->userRegContent as $tabId => $content ) {
			if ( ! is_int( $tabId ) ) {
				continue;
			}

			$extra['title_' . $tabId]			=	( $content['title'] ?? '' );
			$extra['content_' . $tabId]			=	( $content['content'] ?? '' );
			$extra['tab_' . $tabId]				=	( $content['tab'] ?? '' );
			$extra['content']					.=	$extra['content_' . $tabId];
		}

		$layout		=	Application::Cms()->prepareHtmlContentPlugins( $this->userRegCustom, 'profile.reg.layout', $this->user->getInt( 'id', 0 ) );

		if ( ! $layout ) {
			// Empty profile layout is not allowed so fallback to tabbed
			return $this->renderTabs();
		}

		$layout		=	CBuser::getInstance( $this->user->getInt( 'id', 0 ), false )->replaceUserVars( $layout, false, true, $extra );

		if ( ! $layout ) {
			// Empty profile layout is not allowed so fallback to tabbed
			return $this->renderTabs();
		}

		return $layout;
	}
}

class CBListView_html_default extends cbListView
{
	/**
	 * Renders by ECHO the list view head
	 *
	 * @return void
	 */
	protected function _renderHead( )
	{
		global $_CB_framework;

		$headerRightColumn			=	( ( ( count( $this->lists ) > 0 ) && $this->allowListSelector ) || ( $this->searchTabContent && ( ( ( ! $this->searchResultDisplaying ) || $this->searchCollapsed ) || ( $this->searchResultDisplaying && $this->allowListAll ) ) ) );

		$return						=	( $this->listTitleHtml ? '<div class="mb-3 border-bottom cb-page-header cbUserListTitle"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . $this->listTitleHtml . '</h3></div>' : null )
									.	'<div class="cbUserListHead">'
									.		'<div class="row no-gutters cbColumns">'
									.			'<div class="' . ( $headerRightColumn ? 'col-sm-9 pr-sm-3 cbColumn9' : 'col-sm-12 cbColumn12' ) . '">'
									.				( trim( $this->listDescription ) != '' ? '<div class="cbUserListDescription">' . $this->listDescription . '</div>' : null )
									.				'<div class="cbUserListResultCount">';

		if ( $this->totalIsAllUsers ) {
														// CBTxt::Th( 'SITENAME_HAS_TOTAL_REGISTERED_MEMBERS', '[SITENAME] has %%TOTAL%% registered member|[SITENAME] has %%TOTAL%% registered members', array( '[SITENAME]' => $_CB_framework->getCfg( 'sitename' ), '[title]' => $this->listTitleHtml, '%%TOTAL%%' => $this->total ) )
			$return					.=					CBTxt::Th( 'USERLIST_' . (int) $this->listId . '_TOTAL_REGISTERED_MEMBERS SITENAME_HAS_TOTAL_REGISTERED_MEMBERS', '[SITENAME] has %%TOTAL%% registered member|[SITENAME] has %%TOTAL%% registered members', array( '[SITENAME]' => $_CB_framework->getCfg( 'sitename' ), '[title]' => $this->listTitleHtml, '%%TOTAL%%' => $this->total ) );
		} else {
														// CBTxt::Th( 'USERS_COUNT_MEMBERS', '%%USERS_COUNT%% member|%%USERS_COUNT%% members', array( '[SITENAME]' => $_CB_framework->getCfg( 'sitename' ), '[title]' => $this->listTitleHtml, '%%USERS_COUNT%%' => $this->total ) )
			$return					.=					CBTxt::Th( 'USERLIST_' . (int) $this->listId . '_COUNT_MEMBERS USERS_COUNT_MEMBERS', '%%USERS_COUNT%% member|%%USERS_COUNT%% members', array( '[SITENAME]' => $_CB_framework->getCfg( 'sitename' ), '[title]' => $this->listTitleHtml, '%%USERS_COUNT%%' => $this->total ) );
		}

		$return						.=				'</div>'
									.			'</div>';

		if ( $headerRightColumn ) {
			$return					.=			'<div class="col-sm-3 cbColumn3">'
									.				'<div class="text-right cbUserListChanger">';

			if ( ( count( $this->lists ) > 0 ) && $this->allowListSelector ) foreach ( $this->lists as $keyName => $listName ) {
				$return				.=					'<div class="cbUserListChangeItem cbUserList' . $keyName . '">' . $listName . '</div>';
			}

			if ( $this->searchTabContent ) {
				if ( ( ! $this->searchResultDisplaying ) || $this->searchCollapsed ) {
					$return			.=					'<div class="' . ( ( count( $this->lists ) > 0 ) && $this->allowListSelector ? 'mt-2 ' : null ) . 'cbUserListSearchButtons cbUserListsSearchTrigger">'
																																					// CBTxt::Th( 'UE_SEARCH_USERS', 'Search Users' )
									.						'<button type="button" class="btn btn-secondary btn-block cbUserListsSearchButton">' . CBTxt::Th( 'USERLIST_' . (int) $this->listId . '_SEARCH_USERS UE_SEARCH_USERS', 'Search Users', array( '[title]' => $this->listTitleHtml ) ) . ' <span class="fa fa-caret-down"></span></button>'
									.					'</div>';
				}

				if ( $this->searchResultDisplaying && $this->allowListAll ) {
					$return			.=					'<div class="' . ( ( ( count( $this->lists ) > 0 ) && $this->allowListSelector ) || ( ( ! $this->searchResultDisplaying ) || $this->searchCollapsed ) ? 'mt-2 ' : null ) . 'cbUserListSearchButtons cbUserListListAll">'
									.						'<button type="button" class="btn btn-secondary btn-block cbUserListListAllButton" onclick="window.location=\'' . $this->ue_base_url . '\'; return false;">' . CBTxt::Th( 'UE_LIST_ALL', 'List all' ) . '</button>'
									.					'</div>';
				}
			}

			$return					.=				'</div>'
									.			'</div>';
		}

		$return						.=		'</div>'
									.	'</div>';

		if ( $this->searchTabContent ) {
			$return					.=	'<div class="mt-3 cbUserListSearch">'
									.		( $this->searchCriteriaTitleHtml ? '<div class="mb-3 border-bottom cb-page-header cbUserListSearchTitle"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . $this->searchCriteriaTitleHtml . '</h3></div>' : null )
									.		'<div class="cbUserListSearchFields">'
									.			$this->searchTabContent
									.			'<div class="row no-gutters cbUserListSearchButtons">'
									.				'<div class="offset-sm-3 col-sm-9">'
											 																					// CBTxt::Th( 'UE_FIND_USERS', 'Find Users' )
									.					'<input type="submit" class="btn btn-primary btn-sm-block cbUserlistSubmit" value="' . CBTxt::Th( 'USERLIST_' . (int) $this->listId . '_FIND_USERS UE_FIND_USERS', 'Find Users', array( '[title]' => $this->listTitleHtml ) ) . '"' . cbValidator::getSubmitBtnHtmlAttributes() . ' />';

			if ( $this->searchMode == 0 ) {
				$return				.=					' <input type="button" class="btn btn-secondary btn-sm-block cbUserlistCancel" value="' . htmlspecialchars( CBTxt::Th( 'UE_CANCEL', 'Cancel' ) ) . '" />';
			}

			$return					.=				'</div>'
									.			'</div>'
									.		'</div>';

			if ( $this->searchResultDisplaying && $this->searchResultsTitleHtml ) {
				$return				.=		( $this->searchCriteriaTitleHtml ? '<div class="mb-3 border-bottom cb-page-header searchCriteriaTitleHtml"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . $this->searchResultsTitleHtml . '</h3></div>' : null );
			}

			$return					.=	'</div>';
		}

		echo $return;
	}

	/**
	 * Renders by ECHO the list view body
	 *
	 * @return void
	 */
	protected function _renderBody( )
	{
		$layout							=	( $this->layout == 'grid' ? 'grid' : 'list' );
		$columnCount					=	count( $this->columns );
		$hasCanvas						=	false;

		if ( $columnCount && isset( $this->columns[0]->fields ) ) {
			foreach ( $this->columns[0]->fields as $field ) {
				if ( isset( $field['fieldid'] ) && ( (int) $field['fieldid'] == 17 ) ) {
					$hasCanvas			=	true;
				}
			}
		}

		$return							=	'<div id="cbUserTable" class="mt-3' . ( $layout == 'grid' ? ' ml-n2 mr-n2 mb-n3 row no-gutters' : null ) . ' cbUserListDiv ' . ( $layout == 'grid' ? 'cbUserListLayoutGrid' : 'cbUserListLayoutList' ) . ' cbUserListT_' . $this->listId . ( $hasCanvas ? ' cbUserListCanvas' : null ) . '" role="table">';

		if ( $columnCount && ( $layout != 'grid' ) ) {
			$return						.=			'<div class="row no-gutters cbColumns cbUserListHeader" role="row">';

			foreach ( $this->columns as $index => $column ) {
				$return					.=				'<div class="col-sm' . ( $column->size ? '-' . $column->size : null ) . ' p-2 font-weight-bold cbColumn' . $column->size . ' cbUserListHeaderCol' . ( $index + 1 ) . ( $column->cssclass ? ' ' . $column->cssclass : null ) . '" role="columnheader">' . $column->titleRendered . '</div>';
			}

			$return						.=			'</div>';
		}

		$gridSize						=	$this->gridSize;
		$legacyGrid						=	false;

		if ( $gridSize === null ) {
			// Legacy fallback to checking first column size
			$legacyGrid					=	true;
			$gridSize					=	4;

			if ( ( $layout == 'grid' ) && isset( $this->columns[0]->size ) ) {
				$gridSize				=	(int) $this->columns[0]->size;
			}
		}

		$gridClass						=	' col-12 col-sm-6 col-md-4';

		switch ( $gridSize ) {
			case 0:
				$gridClass				=	' col-auto flex-grow-1';
				break;
			case 1:
				$gridClass				=	' col-12 col-sm-6 col-md-4 col-lg-1';
				break;
			case 2:
				$gridClass				=	' col-12 col-sm-6 col-md-4 col-lg-2';
				break;
			case 3:
				$gridClass				=	' col-12 col-sm-6 col-md-3';
				break;
			case 5:
				$gridClass				=	' col-12 col-sm-5';
				break;
			case 6:
				$gridClass				=	' col-12 col-sm-6';
				break;
			case 7:
				$gridClass				=	' col-12 col-sm-7';
				break;
			case 8:
				$gridClass				=	' col-12 col-sm-8';
				break;
			case 9:
				$gridClass				=	' col-12 col-sm-9';
				break;
			case 10:
				$gridClass				=	' col-12 col-sm-10';
				break;
			case 11:
				$gridClass				=	' col-12 col-sm-11';
				break;
			case 12:
				$gridClass				=	' col-12';
				break;
		}

		$i								=	0;

		if ( is_array( $this->users ) && ( count( $this->users ) > 0 ) ) foreach ( $this->users as $userIndex => $user ) {
			$style						=	null;
			$attributes					=	null;

			if ( $this->allowProfileLink ) {
				$style					=	'cursor: hand; cursor: pointer;';
				$attributes				=	' data-id="' . (int) $user->id . '"' . ( $this->profileLinkTarget ? ' data-target="window"' : null );
			}

			if ( $layout == 'grid' ) {
				$class					=	'pb-3 pl-2 pr-2 ' . $gridClass;
			} else {
				$class					=	'row no-gutters bg-light cbColumns sectiontableentry' . ( 1 + ( $i % 2 ) );
			}

			$class						.=	' cbUserListRow';

			if ( $user->banned ) {
				$class					.=	' cbUserListRowBanned';
			}

			if ( ! $user->confirmed ) {
				$class					.=	' cbUserListRowUnconfirmed';
			}

			if ( ! $user->approved ) {
				$class					.=	' cbUserListRowUnapproved';
			}

			if ( $user->block ) {
				$class					.=	' cbUserListRowBlocked';
			}

			if ( $columnCount ) {
				$return					.=			'<div class="' . trim( $class ) . '"' . ( $style ? ' style="' . $style . '"' : null ) . $attributes . ' role="row">';

				$canvas					=	null;
				$avatar					=	null;
				$status					=	null;
				$name					=	null;
				$top					=	null;
				$bottom					=	null;
				$columns				=	null;

				if ( $layout == 'grid' ) {
					// Check for core fields that we need to reposition, but only check the first column:
					if ( $hasCanvas && isset( $this->tableContent[$userIndex][0] ) ) {
						foreach ( $this->tableContent[$userIndex][0] as $fieldIndex => $fieldView ) {
							if ( ( $fieldView->name == 'canvas' ) && ( ! $canvas ) ) {
								$canvas	=	$fieldView->value;

								unset( $this->tableContent[$userIndex][0][$fieldIndex] );
							} elseif ( ( $fieldView->name == 'avatar' ) && ( ! $avatar ) ) {
								$avatar	=	$fieldView->value;

								unset( $this->tableContent[$userIndex][0][$fieldIndex] );
							} elseif ( ( $fieldView->name == 'onlinestatus' ) && ( ! $status ) ) {
								$status	=	$fieldView->value;

								unset( $this->tableContent[$userIndex][0][$fieldIndex] );
							} elseif ( in_array( $fieldView->name, array( 'formatname', 'username', 'name' ) ) && ( ! $name ) ) {
								$name	=	$fieldView->value;

								unset( $this->tableContent[$userIndex][0][$fieldIndex] );
							}
						}
					}
				}

				foreach ( $this->columns as $columnIndex => $column ) {
					$cellContent		=	$this->_getUserListCell( $this->tableContent[$userIndex][$columnIndex] );

					if ( $layout == 'grid' ) {
						if ( ! $cellContent ) {
							continue;
						}

						if ( $column->position ) {
							// Skip column sizes for specific canvas positioning and first column then display top/bottom columns as inline:
							$gridColumn		=				'<div class="' . ( in_array( $column->position, array( 'canvas_top', 'canvas_bottom' ) ) ? 'd-inline-block ml-1 ' : null ) . 'cbUserListRowColumn cbUserListRowCol' . ( $columnIndex + 1 ) . ( $column->cssclass ? ' ' . $column->cssclass : null ) . '" role="gridcell">' . $cellContent . '</div>';
						} else {
							$gridColumn		=				'<div class="col-sm' . ( ( $columnIndex === 0 ) && $legacyGrid ? '-12' : ( $column->size ? '-' . $column->size : null ) ) . ' cbColumn' . ( ( $columnIndex === 0 ) && $legacyGrid ? 12 : $column->size ) . ' cbUserListRowColumn cbUserListRowCol' . ( $columnIndex + 1 ) . ( $column->cssclass ? ' ' . $column->cssclass : null ) . '" role="gridcell">' . $cellContent . '</div>';
						}

						switch ( $column->position ) {
							case 'canvas_background':
								$canvas		.=				$gridColumn;
								break;
							case 'canvas_avatar':
								$avatar		.=				$gridColumn;
								break;
							case 'canvas_name':
								$name		.=				$gridColumn;
								break;
							case 'canvas_top':
								$top		.=				$gridColumn;
								break;
							case 'canvas_bottom':
								$bottom		.=				$gridColumn;
								break;
							default:
								$columns	.=				$gridColumn;
								break;
						}
					} else {
						$columns	.=				'<div class="col-sm' . ( $column->size ? '-' . $column->size : null ) . ' border-top p-2 cbColumn' . $column->size . ' cbUserListRowColumn cbUserListRowCol' . ( $columnIndex + 1 ) . ( $column->cssclass ? ' ' . $column->cssclass : null ) . '" role="gridcell">' . $cellContent . '</div>';
					}
				}

				if ( $layout == 'grid' ) {
					$return				.=			'<div class="h-100 card' . ( $gridSize == 0 ? ' rounded-0' : null ) . ' no-overflow cbCanvasLayout cbCanvasLayoutSm">';

					if ( $canvas || $top || $bottom ) {
						$return			.=				'<div class="card-header p-0 position-relative cbCanvasLayoutTop">';

						if ( $canvas ) {
							$return		.=					'<div class="position-absolute cbCanvasLayoutBackground">'
										.						$canvas
										.					'</div>';
						}

						if ( $top ) {
							$return		.=					'<div class="position-absolute text-right p-1 cbCanvasLayoutActions">'
										.						$top
										.					'</div>';
						}

						if ( $bottom ) {
							$return		.=					'<div class="position-absolute text-right p-1 cbCanvasLayoutButtons">'
										.						$bottom
										.					'</div>';
						}

						$return			.=				'</div>';
					}

					if ( $avatar ) {
						$return			.=				'<div class="position-relative cbCanvasLayoutBottom">'
										.					'<div class="position-absolute cbCanvasLayoutPhoto">'
										.						$avatar
										.					'</div>'
										.				'</div>';
					}

					$return				.=				'<div class="card-body p-2 position-relative cbCanvasLayoutBody">';

					if ( $name ) {
						$return			.=					'<div class="text-truncate cbCanvasLayoutContent">'
										.						( $status ? '<span class="fa-only">' . $status . '</span> ' : null )
										.						$name
										.					'</div>';
					}

					$return				.=					'<div class="row no-gutters cbCanvasLayoutContent">'
										.						$columns
										.					'</div>'
										.				'</div>'
										.			'</div>';
				} else {
					$return				.=				$columns;
				}

				$return					.=			'</div>';
			}

			$i++;
		} else {
			if ( $layout != 'grid' ) {
				$return					.=			'<div class="cbUserListRow cbColumns clearfix">';
			}

			$return						.=			'<div class="' . ( $layout != 'grid' ? 'col-sm-12 cbColumn12 ' : null ) . 'sectiontableentry1">'
												// CBTxt::Th( 'UE_NO_USERS_IN_LIST', 'No users in this list' )
										.				CBTxt::Th( 'USERLIST_' . (int) $this->listId . '_NO_USERS_IN_LIST UE_NO_USERS_IN_LIST', 'No users in this list', array( '[title]' => $this->listTitleHtml ) )
										.			'</div>';

			if ( $layout != 'grid' ) {
				$return					.=			'</div>';
			}
		}

		$return							.=	'</div>';

		echo $return;
	}

	/**
	 * Renders a cell for the list view
	 *
	 * @param  stdClass[] $cellFields CB fields in cell
	 * @return string                 HTML
	 */
	private function _getUserListCell( $cellFields )
	{
		$return						=	null;

		foreach ( $cellFields as $fieldView ) {
			if ( $fieldView->value == '' ) {
				continue;
			}

			$return					.=	'<div class="cbUserListFieldLine cbUserListFL_' . $fieldView->name . '">';

			switch ( $fieldView->display ) {
				case 1:
					$return			.=		'<span class="cbUserListFieldTitle cbUserListFT_' . $fieldView->name . '">' . $fieldView->title . '</span> '
									.		'<span class="cbListFieldCont cbUserListFC_' . $fieldView->name . '">' . $fieldView->value . '</span>';
					break;
				case 2:
					$return			.=		'<div class="cbUserListFieldTitle cbUserListFT_' . $fieldView->name . '">' . $fieldView->title . '</div>'
									.		'<div class="cbListFieldCont cbUserListFC_' . $fieldView->name . '">' . $fieldView->value . '</div>';
					break;
				case 3:
					$return			.=		'<span class="cbUserListFieldTitle cbUserListFT_' . $fieldView->name . '"></span> '
									.		'<span class="cbListFieldCont cbUserListFC_' . $fieldView->name . '">' . $fieldView->value . '</span>';
					break;
				default:
					$return			.=		'<span class="cbListFieldCont cbUserListFC_' . $fieldView->name . '">' . $fieldView->value . '</span>';
					break;
			}

			$return					.=	'</div>';
		}

		return $return;
	}
}
