<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 6/18/14 3:08 PM $
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;

defined('CBLIB') or die();

class cbRegistrationView extends cbTemplateHandler
{
	/** @var UserTable  */
	protected UserTable $user;
	/** @var string  */
	protected string $userRegTitle		=	'';
	/** @var array|string  */
	protected $userRegContent			=	'';
	/** @var array  */
	protected array $userRegNav			=	[];
	/** @var string  */
	protected string $userRegLayout		=	'';
	/** @var string  */
	protected string $userRegCustom		=	'';
	/** @var bool  */
	protected bool $userRegScroll		=	false;
	/** @var string  */
	protected string $headerMessage		=	'';
	/** @var string  */
	protected string $footerMessage		=	'';
	/** @var string  */
	protected string $buttonSubmit		=	'';
	/** @var string  */
	protected string $moduleContent		=	'';
	/** @var string  */
	protected string $iconsTop			=	'';
	/** @var string  */
	protected string $iconsBottom		=	'';
	/** @var bool  */
	protected bool $emailPassword		=	false;
	/** @var string  */
	protected string $titleCanvas		=	'';
	/** @var string  */
	protected string $integrations		=	'';
	/** @var null|bool  */
	protected ?bool $forceHTTPS			=	null;

	/**
	 * Render registration layout
	 *
	 * @param UserTable    $user
	 * @param string       $userRegTitle
	 * @param array|string $userRegContent
	 * @param array        $userRegNav
	 * @param string       $userRegLayout
	 * @param string       $userRegCustom
	 * @param bool         $userRegScroll
	 * @param string       $headerMessage
	 * @param string       $footerMessage
	 * @param string       $buttonSubmit
	 * @param string       $moduleContent
	 * @param string       $iconsTop
	 * @param string       $iconsBottom
	 * @param bool         $emailPassword
	 * @param string       $titleCanvas
	 * @param string       $integrations
	 * @param null|bool    $forceHTTPS
	 * @return string
	 */
	public function drawLayout( UserTable $user, string $userRegTitle = '', $userRegContent = '', array $userRegNav = [],
								string $userRegLayout = '', string $userRegCustom = '', bool $userRegScroll = false, string $headerMessage = '',
								string $footerMessage = '', string $buttonSubmit = '', string $moduleContent = '', string $iconsTop = '',
								string $iconsBottom = '', bool $emailPassword = false, string $titleCanvas = '', string $integrations = '',
								?bool $forceHTTPS = null ): string
	{
		$this->user					=	$user;
		$this->userRegTitle			=	$userRegTitle;
		$this->userRegContent		=	$userRegContent;
		$this->userRegNav			=	$userRegNav;
		$this->userRegLayout		=	$userRegLayout;
		$this->userRegCustom		=	$userRegCustom;
		$this->userRegScroll		=	$userRegScroll;
		$this->headerMessage		=	$headerMessage;
		$this->footerMessage		=	$footerMessage;
		$this->buttonSubmit			=	$buttonSubmit;
		$this->moduleContent		=	$moduleContent;
		$this->iconsTop				=	$iconsTop;
		$this->iconsBottom			=	$iconsBottom;
		$this->emailPassword		=	$emailPassword;
		$this->titleCanvas			=	$titleCanvas;
		$this->integrations			=	$integrations;
		$this->forceHTTPS			=	$forceHTTPS;

		return $this->draw( 'Layout' );
	}
}
