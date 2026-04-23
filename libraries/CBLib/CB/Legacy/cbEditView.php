<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 6/18/14 3:04 PM $
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\RegistryInterface;

defined('CBLIB') or die();

class cbEditView extends cbTemplateHandler
{
	/** @var UserTable  */
	protected UserTable $user;
	/** @var string  */
	protected string $userEditTitle		=	'';
	/** @var array|string  */
	protected $userEditContent			=	'';
	/** @var array  */
	protected array $userEditNav		=	[];
	/** @var string  */
	protected string $userEditLayout	=	'';
	/** @var string  */
	protected string $userEditCustom	=	'';
	/** @var bool  */
	protected bool $userEditScroll		=	false;
	/** @var string  */
	protected string $buttonSubmit		=	'';
	/** @var string  */
	protected string $buttonCancel		=	'';
	/** @var string  */
	protected string $iconsTop			=	'';
	/** @var string  */
	protected string $iconsBottom		=	'';
	/** @var string  */
	protected string $integrations		=	'';

	/**
	 * Render profile edit layout
	 *
	 * @param UserTable    $user
	 * @param string       $userEditTitle
	 * @param array|string $userEditContent
	 * @param array        $userEditNav
	 * @param string       $userEditLayout
	 * @param string       $userEditCustom
	 * @param bool         $userEditScroll
	 * @param string       $buttonSubmit
	 * @param string       $buttonCancel
	 * @param string       $iconsTop
	 * @param string       $iconsBottom
	 * @param string       $integrations
	 * @return string
	 */
	public function drawLayout( UserTable $user, string $userEditTitle = '', $userEditContent = '', array $userEditNav = [], string $userEditLayout = '',
								string $userEditCustom = '', bool $userEditScroll = false, string $buttonSubmit = '', string $buttonCancel = '',
								string $iconsTop = '', string $iconsBottom = '', string $integrations = '' ): string
	{
		$this->user				=	$user;
		$this->userEditTitle	=	$userEditTitle;
		$this->userEditContent	=	$userEditContent;
		$this->userEditNav		=	$userEditNav;
		$this->userEditLayout	=	$userEditLayout;
		$this->userEditCustom	=	$userEditCustom;
		$this->userEditScroll	=	$userEditScroll;
		$this->buttonSubmit		=	$buttonSubmit;
		$this->buttonCancel		=	$buttonCancel;
		$this->iconsTop			=	$iconsTop;
		$this->iconsBottom		=	$iconsBottom;
		$this->integrations		=	$integrations;

		return $this->draw( 'Layout' );
	}

	/**
	 * Helper function for rendering profile edit tab layout preview
	 *
	 * @param string                 $layout
	 * @param null|RegistryInterface $params
	 * @param string                 $name
	 * @return string
	 */
	public static function drawPreview( string $layout, ?RegistryInterface $params, string $name ): string
	{
		$isReg	=	( strpos( $name, 'registration_layout' ) !== false );

		if ( $layout === 'flat' ) {
			return	'<div class="d-flex flex-column gap-2 text-center text-wrap layoutPreview layoutPreviewEdit layoutPreviewEditFlat">'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			( $isReg ? CBTxt::T( 'Header' ) : CBTxt::T( 'Title' ) )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			CBTxt::T( 'Top Legend' )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 150px;">'
				.			CBTxt::T( 'Tab Content' )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 150px;">'
				.			CBTxt::T( 'Tab Content' )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 150px;">'
				.			CBTxt::T( 'Tab Content' )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			CBTxt::T( 'Buttons' )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			CBTxt::T( 'Bottom Legend' )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded' . ( ! $isReg ? ' hidden' : '' ) . '" style="min-height: 50px;">'
				.			CBTxt::T( 'Footer' )
				.		'</div>'
				.	'</div>';
		}

		if ( ( $layout === 'tabbed' ) || ( $layout === 'stepped' ) ) {
			return	'<div class="d-flex flex-column gap-2 text-center text-wrap layoutPreview layoutPreviewEdit layoutPreviewEditTabbed">'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			( $isReg ? CBTxt::T( 'Header' ) : CBTxt::T( 'Title' ) )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			CBTxt::T( 'Top Legend' )
				.		'</div>'
				.		'<div class="d-flex flex-wrap gap-2">'
				.			'<div class="d-flex align-items-center justify-content-center p-2 bg-light border rounded-top" style="min-height: 50px; min-width: 150px;">'
				.				CBTxt::T( 'Tab' )
				.			'</div>'
				.			'<div class="d-flex align-items-center justify-content-center p-2 bg-light border rounded-top" style="min-height: 50px; min-width: 150px;">'
				.				CBTxt::T( 'Tab' )
				.			'</div>'
				.			'<div class="d-flex align-items-center justify-content-center p-2 bg-light border rounded-top" style="min-height: 50px; min-width: 150px;">'
				.				CBTxt::T( 'Tab' )
				.			'</div>'
				.			'<div class="d-flex align-items-center justify-content-center p-2 bg-light border rounded-top" style="min-height: 50px; min-width: 150px;">'
				.				CBTxt::T( 'Tab' )
				.			'</div>'
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 300px;">'
				.			CBTxt::T( 'Tab Content' )
				.		'</div>'
				.		'<div class="d-flex ' . ( $layout === 'stepped' ? ' gap-2' : ' flex-column align-items-center justify-content-center p-2 bg-light border rounded' ) . '" style="min-height: 50px;">'
				.			( $layout === 'stepped' ? '<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-width: 150px;">' . CBTxt::T( 'Previous' ) . '</div><div class="ml-auto d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-width: 150px;">' . CBTxt::T( 'Next / Submit' ) . '</div>' : CBTxt::T( 'Buttons' ) )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			CBTxt::T( 'Bottom Legend' )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded' . ( ! $isReg ? ' hidden' : '' ) . '" style="min-height: 50px;">'
				.			CBTxt::T( 'Footer' )
				.		'</div>'
				.	'</div>';
		}

		if ( $layout === 'vertical' ) {
			return	'<div class="d-flex flex-column gap-2 text-center text-wrap layoutPreview layoutPreviewEdit layoutPreviewEditVertical">'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			( $isReg ? CBTxt::T( 'Header' ) : CBTxt::T( 'Title' ) )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			CBTxt::T( 'Top Legend' )
				.		'</div>'
				.		'<div class="d-flex gap-2">'
				.			'<div class="d-flex flex-column gap-2" style="min-width: 150px;">'
				.				'<div class="d-flex align-items-center justify-content-center p-2 bg-light border rounded-top" style="min-height: 50px;">'
				.					CBTxt::T( 'Tab' )
				.				'</div>'
				.				'<div class="d-flex align-items-center justify-content-center p-2 bg-light border rounded-top" style="min-height: 50px;">'
				.					CBTxt::T( 'Tab' )
				.				'</div>'
				.				'<div class="d-flex align-items-center justify-content-center p-2 bg-light border rounded-top" style="min-height: 50px;">'
				.					CBTxt::T( 'Tab' )
				.				'</div>'
				.				'<div class="d-flex align-items-center justify-content-center p-2 bg-light border rounded-top" style="min-height: 50px;">'
				.					CBTxt::T( 'Tab' )
				.				'</div>'
				.			'</div>'
				.			'<div class="d-flex flex-column flex-grow-1 align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 300px;">'
				.				CBTxt::T( 'Tab Content' )
				.			'</div>'
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			CBTxt::T( 'Buttons' )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			CBTxt::T( 'Bottom Legend' )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded' . ( ! $isReg ? ' hidden' : '' ) . '" style="min-height: 50px;">'
				.			CBTxt::T( 'Footer' )
				.		'</div>'
				.	'</div>';
		}

		if ( $layout === 'menu' ) {
			return	'<div class="d-flex flex-column gap-2 text-center text-wrap layoutPreview layoutPreviewEdit layoutPreviewEditMenu">'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			( $isReg ? CBTxt::T( 'Header' ) : CBTxt::T( 'Title' ) )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			CBTxt::T( 'Top Legend' )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			CBTxt::T( 'Tab Navigation' )
				.		'</div>'
				.		'<div class="d-flex gap-2">'
				.			'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="width: 20%;">'
				.				CBTxt::T( 'Collected Navigation' )
				.			'</div>'
				.			'<div class="d-flex flex-column flex-grow-1 gap-2">'
				.				'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 300px;">'
				.					CBTxt::T( 'Tab Content' )
				.				'</div>'
				.				'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.					CBTxt::T( 'Buttons' )
				.				'</div>'
				.			'</div>'
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded" style="min-height: 50px;">'
				.			CBTxt::T( 'Bottom Legend' )
				.		'</div>'
				.		'<div class="d-flex flex-column align-items-center justify-content-center p-2 bg-light border rounded' . ( ! $isReg ? ' hidden' : '' ) . '" style="min-height: 50px;">'
				.			CBTxt::T( 'Footer' )
				.		'</div>'
				.	'</div>';
		}

		return '';
	}
}
