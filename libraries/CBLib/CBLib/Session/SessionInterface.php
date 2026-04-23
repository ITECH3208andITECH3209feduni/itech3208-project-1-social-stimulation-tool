<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 4/7/14 11:12 AM $
* @package CBLib\Session
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/


namespace CBLib\Session;

use CBLib\Registry\ParamsInterface;

defined('CBLIB') or die();

/**
 * Interface SessionInterface
 *
 * @package CBLib\Session
 */
interface SessionInterface extends ParamsInterface
{

	/**
	 * Returns CSRF token name for the user session
	 *
	 * @return string
	 */
	public function getFormTokenName(): string;

	/**
	 * Returns CSRF token value for the user session
	 *
	 * @return string
	 */
	public function getFormTokenValue(): string;

	/**
	 * Returns CSRF token hidden input
	 *
	 * @return string
	 */
	public function getFormTokenInput(): string;

	/**
	 * Validates the CSRF token for the user session
	 * This can output an error message, redirect, browser back, or simply return true/false
	 *
	 * @param string $method post, get, request
	 * @param int    $mode   0: return true/false, 1: error message w/ return, 2: error message w/ 403 exit, 3: redirect w/ error message, 4: browser alert w/ browser back, 5: browser back
	 * @return bool
	 */
	public function checkFormToken( string $method = 'post', int $mode = 1 ): bool;
}
