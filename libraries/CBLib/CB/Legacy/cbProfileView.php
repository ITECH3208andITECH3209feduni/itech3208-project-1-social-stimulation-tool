<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 6/18/14 3:04 PM $
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class cbProfileView extends cbTemplateHandler
{
	/** @var UserTable  */
	protected UserTable $user;
	/** @var null|array  */
	protected ?array $userViewTabs		=	[];
	/** @var array  */
	protected array $userViewTabsNav	=	[];
	/** @var string  */
	protected string $userViewLayout	=	'';
	/** @var string  */
	protected string $userViewCustom	=	'';
	/** @var bool  */
	protected bool $userViewScroll		=	false;
	/** @var array  */
	protected array $userMainSizes		=	[];
	/** @var string  */
	protected string $integrations		=	'';

	/**
	 * Render profile view layout
	 *
	 * @param UserTable  $user
	 * @param null|array $userViewTabs
	 * @param array      $userViewTabsNav
	 * @param string     $userHomeLayout
	 * @param string     $userHomeCustom
	 * @param bool       $userHomeScroll
	 * @param string     $integrations
	 * @return string
	 */
	public function drawLayout( UserTable $user, ?array $userViewTabs = [], array $userViewTabsNav = [], string $userHomeLayout = '',
								string $userHomeCustom = '', bool $userHomeScroll = false, array $userMainSizes = [], string $integrations = '' ): string
	{
		$this->user						=	$user;
		$this->userViewTabs				=	$userViewTabs;
		$this->userViewTabsNav			=	$userViewTabsNav;
		$this->userViewLayout			=	$userHomeLayout;
		$this->userViewCustom			=	$userHomeCustom;
		$this->userViewScroll			=	$userHomeScroll;
		$this->userMainSizes['left']	=	( $userMainSizes['left'] ?? 3 );
		$this->userMainSizes['middle']	=	( $userMainSizes['middle'] ?? 0 );
		$this->userMainSizes['right']	=	( $userMainSizes['right'] ?? 3 );
		$this->integrations				=	$integrations;

		return $this->draw( 'Layout' );
	}

	/**
	 * Helper function for rendering profile tab layout preview
	 *
	 * @param string $layout
	 * @return string
	 */
	public static function drawPreview( string $layout ): string
	{
		if ( $layout === 'canvas_home' ) {
			return	'<div class="d-flex flex-column gap-2 text-center text-wrap layoutPreview layoutPreviewProfile layoutPreviewCanvasHome">'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			CBTxt::T( 'Header (above left/middle/right)' )
				.			'<div class="text-small text-muted">cb_head</div>'
				.		'</div>'
				.		'<div class="d-flex flex-column gap-2">'
				.			'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.				CBTxt::T( 'Canvas Menu' )
				.				'<div class="text-small text-muted">canvas_menu</div>'
				.			'</div>'
				.			'<div class="d-flex flex-wrap gap-2">'
				.				'<div class="w-md-100 d-flex flex-column gap-2" style="width: 20%;">'
				.					'<div class="d-flex align-items-center flex-column gap-2 p-2 bg-light border rounded">'
				.						'<div class="d-flex flex-column align-items-center" style="min-height: 50px;">'
				.							CBTxt::T( 'Canvas Background' )
				.							'<div class="text-small text-muted">canvas_background</div>'
				.						'</div>'
				.						'<div class="d-flex flex-column align-items-center" style="min-height: 50px;">'
				.							CBTxt::T( 'Canvas Photo' )
				.							'<div class="text-small text-muted">canvas_photo</div>'
				.						'</div>'
				.						'<div class="d-flex flex-column align-items-center" style="min-height: 50px;">'
				.							CBTxt::T( 'Canvas Title' )
				.							'<div class="text-small text-muted">canvas_title</div>'
				.						'</div>'
				.					'</div>'
				.					'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.						CBTxt::T( 'Canvas Info' )
				.						'<div class="text-small text-muted">canvas_info</div>'
				.					'</div>'
				.					'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.						CBTxt::T( 'Canvas Stats' )
				.						'<div class="text-small text-muted">canvas_stats</div>'
				.					'</div>'
				.					'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded flex-grow-1" style="min-height: 50px;">'
				.						CBTxt::T( 'Collected Navigation' )
				.					'</div>'
				.					'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.						CBTxt::T( 'Left side (of middle area)' )
				.						'<div class="text-small text-muted">cb_left</div>'
				.					'</div>'
				.				'</div>'
				.				'<div class="w-md-100 d-flex flex-column gap-2" style="width: calc( 60% - 1rem );">'
				.					'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.						CBTxt::T( 'Canvas Main Middle Navigation' )
				.					'</div>'
				.					'<div class="d-flex gap-2 flex-grow-1">'
				.						'<div class="d-flex align-items-center justify-content-center flex-column gap-2 p-2 bg-light border rounded" style="width: 20%;">'
				.							'<div class="d-flex flex-column align-items-center" style="min-height: 50px;">'
				.								CBTxt::T( 'Canvas Main Left' )
				.								'<div class="text-small text-muted">canvas_main_left</div>'
				.							'</div>'
				.							'<div class="d-flex flex-column align-items-center" style="min-height: 50px;">'
				.								CBTxt::T( 'Canvas Main Left Static' )
				.								'<div class="text-small text-muted">canvas_main_left_static</div>'
				.							'</div>'
				.						'</div>'
				.						'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded flex-grow-1">'
				.							CBTxt::T( 'Canvas Main Middle' )
				.							'<div class="text-small text-muted">canvas_main_middle</div>'
				.						'</div>'
				.						'<div class="d-flex align-items-center justify-content-center flex-column gap-2 p-2 bg-light border rounded" style="width: 20%;">'
				.							'<div class="d-flex flex-column align-items-center" style="min-height: 50px;">'
				.								CBTxt::T( 'Canvas Main Right' )
				.								'<div class="text-small text-muted">canvas_main_right</div>'
				.							'</div>'
				.							'<div class="d-flex flex-column align-items-center" style="min-height: 50px;">'
				.								CBTxt::T( 'Canvas Main Right Static' )
				.								'<div class="text-small text-muted">canvas_main_right_static</div>'
				.							'</div>'
				.						'</div>'
				.					'</div>'
				.					'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.						CBTxt::T( 'Middle area' )
				.						'<div class="text-small text-muted">cb_middle</div>'
				.					'</div>'
				.					'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.						CBTxt::T( 'Main area (below left/middle/right)' )
				.						'<div class="text-small text-muted">cb_tabmain</div>'
				.					'</div>'
				.					'<div class="d-flex flex-wrap align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.						'<div style="width: 33.33333%">'
				.							CBTxt::T( 'Line 1 Column 1' )
				.							'<div class="text-small text-muted">L1C1</div>'
				.						'</div>'
				.						'<div style="width: 33.33333%">'
				.							CBTxt::T( 'Line 1 Column 2' )
				.							'<div class="text-small text-muted">L1C2</div>'
				.						'</div>'
				.						'<div style="width: 33.33333%">'
				.							CBTxt::T( 'Line 1 Column 3' )
				.							'<div class="text-small text-muted">L1C3</div>'
				.						'</div>'
				.						'<div class="w-100 p-2"></div>'
				.						'<div style="width: 33.33333%">'
				.							CBTxt::T( 'Line 2 Column 1' )
				.							'<div class="text-small text-muted">L2C1</div>'
				.						'</div>'
				.						'<div style="width: 33.33333%">'
				.							CBTxt::T( 'Line 2 Column 2' )
				.							'<div class="text-small text-muted">L2C2</div>'
				.						'</div>'
				.						'<div style="width: 33.33333%">'
				.							CBTxt::T( 'Line 2 Column 3' )
				.							'<div class="text-small text-muted">L2C3</div>'
				.						'</div>'
				.					'</div>'
				.				'</div>'
				.				'<div class="position-relative w-md-100 d-flex flex-column gap-2" style="width: 20%;">'
				.					'<div class="h-100 d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded">'
				.						CBTxt::T( 'Right side (of middle area)' )
				.						'<div class="text-small text-muted">cb_right</div>'
				.					'</div>'
				.				'</div>'
				.			'</div>'
				.			'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.				CBTxt::T( 'Bottom area (below main area)' )
				.				'<div class="text-small text-muted">cb_underall</div>'
				.			'</div>'
				.		'</div>'
				.	'</div>';
		}

		if ( in_array( $layout, [ 'canvas_other', 'canvas_other_alt' ], true ) ) {
			$return		=	'<div class="d-flex flex-column gap-2 text-center text-wrap layoutPreview layoutPreviewProfile layoutPreviewCanvasOther">'
						.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
						.			CBTxt::T( 'Header (above left/middle/right)' )
						.			'<div class="text-small text-muted">cb_head</div>'
						.		'</div>'
						.		'<div class="border rounded">'
						.			'<div class="d-flex flex-column align-items-center justify-content-center p-2 rounded-top bg-light border-bottom" style="min-height: 50px;">'
						.				CBTxt::T( 'Canvas Menu' )
						.				'<div class="text-small text-muted">canvas_menu</div>'
						.			'</div>'
						.			'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light" style="min-height: 200px;">'
						.				CBTxt::T( 'Canvas Background' )
						.				'<div class="text-small text-muted">canvas_background</div>'
						.			'</div>'
						.			'<div class="d-flex gap-2 bg-light border-top' . ( $layout === 'canvas_other_alt' ? ' rounded-bottom' : '' ) . '" style="min-height: 50px;">'
						.				'<div class="position-relative" style="min-height: 50px; width: 200px;">'
						.					'<div class="position-absolute p-2" style="height: 200px; width: 200px; bottom: 0; left: 0;">'
						.						'<div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center p-2 border rounded-circle bg-light">'
						.							CBTxt::T( 'Canvas Photo' )
						.							'<div class="text-small text-muted">canvas_photo</div>'
						.						'</div>'
						.					'</div>'
						.				'</div>'
						.				'<div class="d-flex flex-column align-items-start flex-grow-1 gap-1 p-2 text-left" style="min-height: 50px;">'
						.					'<div class="w-100 d-flex gap-1">'
						.						'<div>'
						.							CBTxt::T( 'Canvas Title' )
						.							' <span class="text-small text-muted">(canvas_title)</span>'
						.						'</div>'
						.						'<div class="ml-auto">'
						.							CBTxt::T( 'Canvas Info' )
						.							' <span class="text-small text-muted">(canvas_info)</span>'
						.						'</div>'
						.					'</div>'
						.					'<div class="w-100">'
						.						CBTxt::T( 'Canvas Stats' )
						.						' <span class="text-small text-muted">(canvas_stats)</span>'
						.					'</div>'
						.				'</div>'
						.			'</div>';

			if ( $layout === 'canvas_other' ) {
				$return	.=			'<div class="d-flex flex-column align-items-center justify-content-center p-2 rounded-bottom bg-light border-top" style="min-height: 50px;">'
						.				CBTxt::T( 'Canvas Main Middle Navigation' )
						.			'</div>';
			}

			$return		.=		'</div>';

			if ( $layout === 'canvas_other_alt' ) {
				$return	.=		'<div class="d-flex gap-2 flex-grow-1">'
						.			'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px; width: 20%;">'
						.				CBTxt::T( 'Collected Navigation' )
						.			'</div>'
						.			'<div class="d-flex flex-column gap-2 flex-grow-1">'
						.				'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
						.					CBTxt::T( 'Canvas Main Middle Navigation' )
						.				'</div>';
			}

			$return		.=		'<div class="d-flex gap-2 flex-grow-1">'
						.			'<div class="d-flex align-items-center justify-content-center flex-column gap-2 p-2 bg-light border rounded" style="width: 20%;">'
						.				'<div class="d-flex flex-column align-items-center" style="min-height: 50px;">'
						.					CBTxt::T( 'Canvas Main Left' )
						.					'<div class="text-small text-muted">canvas_main_left</div>'
						.				'</div>'
						.				'<div class="d-flex flex-column align-items-center" style="min-height: 50px;">'
						.					CBTxt::T( 'Canvas Main Left Static' )
						.					'<div class="text-small text-muted">canvas_main_left_static</div>'
						.				'</div>'
						.			'</div>'
						.			'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded flex-grow-1">'
						.				CBTxt::T( 'Canvas Main Middle' )
						.				'<div class="text-small text-muted">canvas_main_middle</div>'
						.			'</div>'
						.			'<div class="d-flex align-items-center justify-content-center flex-column gap-2 p-2 bg-light border rounded" style="width: 20%;">'
						.				'<div class="d-flex flex-column align-items-center" style="min-height: 50px;">'
						.					CBTxt::T( 'Canvas Main Right' )
						.					'<div class="text-small text-muted">canvas_main_right</div>'
						.				'</div>'
						.				'<div class="d-flex flex-column align-items-center" style="min-height: 50px;">'
						.					CBTxt::T( 'Canvas Main Right Static' )
						.					'<div class="text-small text-muted">canvas_main_right_static</div>'
						.				'</div>'
						.			'</div>'
						.		'</div>'
						.		'<div class="d-flex gap-2 flex-grow-1" style="min-height: 50px;">'
						.			'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="width: 20%;">'
						.				CBTxt::T( 'Left side (of middle area)' )
						.				'<div class="text-small text-muted">cb_left</div>'
						.			'</div>'
						.			'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded flex-grow-1">'
						.				CBTxt::T( 'Middle area' )
						.				'<div class="text-small text-muted">cb_middle</div>'
						.			'</div>'
						.			'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="width: 20%;">'
						.				CBTxt::T( 'Right side (of middle area)' )
						.				'<div class="text-small text-muted">cb_right</div>'
						.			'</div>'
						.		'</div>'
						.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
						.			CBTxt::T( 'Main area (below left/middle/right)' )
						.			'<div class="text-small text-muted">cb_tabmain</div>'
						.		'</div>'
						.		'<div class="d-flex flex-wrap align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
						.			'<div style="width: 33.33333%">'
						.				CBTxt::T( 'Line 1 Column 1' )
						.				'<div class="text-small text-muted">L1C1</div>'
						.			'</div>'
						.			'<div style="width: 33.33333%">'
						.				CBTxt::T( 'Line 1 Column 2' )
						.				'<div class="text-small text-muted">L1C2</div>'
						.			'</div>'
						.			'<div style="width: 33.33333%">'
						.				CBTxt::T( 'Line 1 Column 3' )
						.				'<div class="text-small text-muted">L1C3</div>'
						.			'</div>'
						.			'<div class="w-100 p-2"></div>'
						.			'<div style="width: 33.33333%">'
						.				CBTxt::T( 'Line 2 Column 1' )
						.				'<div class="text-small text-muted">L2C1</div>'
						.			'</div>'
						.			'<div style="width: 33.33333%">'
						.				CBTxt::T( 'Line 2 Column 2' )
						.				'<div class="text-small text-muted">L2C2</div>'
						.			'</div>'
						.			'<div style="width: 33.33333%">'
						.				CBTxt::T( 'Line 2 Column 3' )
						.				'<div class="text-small text-muted">L2C3</div>'
						.			'</div>'
						.		'</div>'
						.		( $layout === 'canvas_other_alt' ? '</div></div>' : '' )
						.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
						.			CBTxt::T( 'Bottom area (below main area)' )
						.			'<div class="text-small text-muted">cb_underall</div>'
						.		'</div>'
						.	'</div>';

			return $return;
		}

		if ( $layout === 'intranet' ) {
			return	'<div class="d-flex flex-column text-center text-wrap layoutPreview layoutPreviewProfile layoutPreviewIntranet">'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			CBTxt::T( 'Header (above left/middle/right)' )
				.			'<div class="text-small text-muted">cb_head</div>'
				.		'</div>'
				.		'<div class="d-flex flex-column mt-2 bg-light border rounded">'
				.			'<div class="d-flex flex-column align-items-center justify-content-center p-2" style="min-height: 50px;">'
				.				CBTxt::T( 'Canvas Menu' )
				.				'<div class="text-small text-muted">canvas_menu</div>'
				.			'</div>'
				.			'<div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-3 p-2 border-top" style="min-height: 50px;">'
				.				'<div class="d-flex flex-column flex-shrink-0 text-center" style="flex-basis: 20%;" >'
				.					CBTxt::T( 'Canvas Photo' )
				.					'<div class="text-small text-muted">canvas_photo</div>'
				.				'</div>'
				.				'<div class="d-flex flex-column flex-grow-1 gap-3">'
				.					'<div class="d-flex flex-column align-items-start" style="min-height: 50px;">'
				.						CBTxt::T( 'Canvas Title' )
				.						'<div class="text-small text-muted">canvas_title</div>'
				.					'</div>'
				.					'<div class="d-flex flex-column align-items-start" style="min-height: 50px;">'
				.						CBTxt::T( 'Canvas Info' )
				.						'<div class="text-small text-muted">canvas_info</div>'
				.					'</div>'
				.				'</div>'
				.				'<div class="d-flex flex-column flex-shrink-0 text-center" style="flex-basis: 20%;" >'
				.					CBTxt::T( 'Canvas Stats' )
				.					'<div class="text-small text-muted">canvas_stats</div>'
				.				'</div>'
				.			'</div>'
				.			'<div class="d-flex d-md-none flex-column align-items-center justify-content-center p-2 border-top" style="min-height: 50px;">'
				.				CBTxt::T( 'Collected Navigation / Canvas Main Middle Navigation' )
				.			'</div>'
				.		'</div>'
				.		'<div class="d-flex flex-column gap-2">'
				.			'<div class="d-flex flex-wrap gap-2">'
				.				'<div class="pt-2 w-md-100 d-flex flex-column gap-2" style="width: 20%;">'
				.					'<div class="d-none d-md-flex flex-column align-items-center flex-grow-1 justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.						CBTxt::T( 'Collected Navigation' )
				.					'</div>'
				.					'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.						CBTxt::T( 'Left side (of middle area)' )
				.						'<div class="text-small text-muted">cb_left</div>'
				.					'</div>'
				.				'</div>'
				.				'<div class="d-none d-md-block border-right"></div>'
				.				'<div class="pt-0 pt-md-2 w-md-100 d-flex flex-column gap-2" style="width: calc( 60% - 1.5rem - 1px );">'
				.					'<div class="d-flex gap-2 flex-grow-1">'
				.						'<div class="d-flex align-items-center justify-content-center flex-column gap-2 p-2 bg-light border rounded" style="width: 20%;">'
				.							'<div class="d-flex flex-column align-items-center" style="min-height: 50px;">'
				.								CBTxt::T( 'Canvas Main Left' )
				.								'<div class="text-small text-muted">canvas_main_left</div>'
				.							'</div>'
				.							'<div class="d-flex flex-column align-items-center" style="min-height: 50px;">'
				.								CBTxt::T( 'Canvas Main Left Static' )
				.								'<div class="text-small text-muted">canvas_main_left_static</div>'
				.							'</div>'
				.						'</div>'
				.						'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded flex-grow-1">'
				.							CBTxt::T( 'Canvas Main Middle' )
				.							'<div class="text-small text-muted">canvas_main_middle</div>'
				.						'</div>'
				.						'<div class="d-flex align-items-center justify-content-center flex-column gap-2 p-2 bg-light border rounded" style="width: 20%;">'
				.							'<div class="d-flex flex-column align-items-center" style="min-height: 50px;">'
				.								CBTxt::T( 'Canvas Main Right' )
				.								'<div class="text-small text-muted">canvas_main_right</div>'
				.							'</div>'
				.							'<div class="d-flex flex-column align-items-center" style="min-height: 50px;">'
				.								CBTxt::T( 'Canvas Main Right Static' )
				.								'<div class="text-small text-muted">canvas_main_right_static</div>'
				.							'</div>'
				.						'</div>'
				.					'</div>'
				.					'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.						CBTxt::T( 'Middle area' )
				.						'<div class="text-small text-muted">cb_middle</div>'
				.					'</div>'
				.					'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.						CBTxt::T( 'Main area (below left/middle/right)' )
				.						'<div class="text-small text-muted">cb_tabmain</div>'
				.					'</div>'
				.					'<div class="d-flex flex-wrap align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.						'<div style="width: 33.33333%">'
				.							CBTxt::T( 'Line 1 Column 1' )
				.							'<div class="text-small text-muted">L1C1</div>'
				.						'</div>'
				.						'<div style="width: 33.33333%">'
				.							CBTxt::T( 'Line 1 Column 2' )
				.							'<div class="text-small text-muted">L1C2</div>'
				.						'</div>'
				.						'<div style="width: 33.33333%">'
				.							CBTxt::T( 'Line 1 Column 3' )
				.							'<div class="text-small text-muted">L1C3</div>'
				.						'</div>'
				.						'<div class="w-100 p-2"></div>'
				.						'<div style="width: 33.33333%">'
				.							CBTxt::T( 'Line 2 Column 1' )
				.							'<div class="text-small text-muted">L2C1</div>'
				.						'</div>'
				.						'<div style="width: 33.33333%">'
				.							CBTxt::T( 'Line 2 Column 2' )
				.							'<div class="text-small text-muted">L2C2</div>'
				.						'</div>'
				.						'<div style="width: 33.33333%">'
				.							CBTxt::T( 'Line 2 Column 3' )
				.							'<div class="text-small text-muted">L2C3</div>'
				.						'</div>'
				.					'</div>'
				.				'</div>'
				.				'<div class="pt-0 pt-md-2 position-relative w-md-100 d-flex flex-column gap-2" style="width: 20%;">'
				.					'<div class="h-100 d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded">'
				.						CBTxt::T( 'Right side (of middle area)' )
				.						'<div class="text-small text-muted">cb_right</div>'
				.					'</div>'
				.				'</div>'
				.			'</div>'
				.			'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.				CBTxt::T( 'Bottom area (below main area)' )
				.				'<div class="text-small text-muted">cb_underall</div>'
				.			'</div>'
				.		'</div>'
				.	'</div>';
		}

		return '';
	}
}
