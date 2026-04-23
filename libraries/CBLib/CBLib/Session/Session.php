<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 4/7/14 11:05 AM $
* @package CBLib\Session
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CBLib\Session;

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Registry\ParametersStore;
use JSession;

defined('CBLIB') or die();

/**
 * CBLib\Session\Session Class implementation
 * 
 */
class Session extends ParametersStore implements SessionInterface
{
	/**
	 * Constructor
	 *
	 * @param  array  $paramsValues  Session values
	 */
	public function __construct( &$paramsValues = null )
	{
		if ( $paramsValues === null ) {
			global $_SESSION;
			$this->setAsReferenceToArray( $_SESSION );
			return;
		}

		$this->setAsReferenceToArray( $paramsValues );
	}

	/**
	 * Returns CSRF token name for the user session
	 *
	 * @return string
	 */
	public function getFormTokenName(): string
	{
		return Application::Cms()->getFormToken();
	}

	/**
	 * Returns CSRF token value for the user session
	 *
	 * @return string
	 */
	public function getFormTokenValue(): string
	{
		return '1';
	}

	/**
	 * Returns CSRF token hidden input
	 *
	 * @return string
	 */
	public function getFormTokenInput(): string
	{
		return '<input type="hidden" name="' . $this->getFormTokenName() . '" value="' . $this->getFormTokenValue() . '" />';
	}

	/**
	 * Validates the CSRF token for the user session
	 * This can output an error message, redirect, browser back, or simply return true/false
	 *
	 * @param string $method post, get, request
	 * @param int    $mode   0: return true/false, 1: error message w/ return (default), 2: error message w/ 403 exit, 3: redirect w/ error message, 4: browser alert w/ browser back, 5: browser back
	 * @return bool
	 */
	public function checkFormToken( string $method = 'post', int $mode = 1 ): bool
	{
		global $_CB_framework;

		if ( Application::Cms()->checkFormToken( $method ) ) {
			return true;
		}

		$error	=	CBTxt::Th( 'UE_SESSION_EXPIRED', 'The most recent request was denied because it had an invalid security token. Please go back or refresh the page and try again.' );

		switch ( $mode ) {
			case 1:
				$_CB_framework->enqueueMessage( $error, 'error' );
				break;
			case 2:
				header( 'HTTP/1.0 403 Forbidden' );

				echo $error;
				exit();
			case 3:
				cbRedirect( 'index.php', $error, 'error' );
				break;
			case 4:
				header( 'HTTP/1.0 403 Forbidden' );

				echo '<script type="text/javascript">alert( ' . json_encode( $error, JSON_HEX_TAG ) . ' ); window.history.go( -1 );</script>';
				exit();
			case 5:
				header( 'HTTP/1.0 403 Forbidden' );

				echo '<script type="text/javascript">window.history.go( -1 );</script>';
				exit();
		}

		return false;
	}
}
