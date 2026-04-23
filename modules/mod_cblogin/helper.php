<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use Joomla\CMS\Helper\AuthenticationHelper;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class modCBLoginHelper
{
	/**
	 * Gets type of module depending on user login state.
	 *
	 * @return string  'login' or 'logout'
	 */
	static function getType()
	{
		global $_CB_framework;

		return ( $_CB_framework->myId() > 0 ) ? 'logout' : 'login';
	}

	/**
	 * Retrieve the URL where the user should be returned after logging in
	 *
	 * @param   \Joomla\Registry\Registry  $params  module parameters
	 * @param   string                     $type    return type
	 * @return  string
	 */
	public static function getJoomlaReturnUrl($params, $type)
	{
		$item = \Joomla\CMS\Factory::getApplication()->getMenu()->getItem($params->get($type));

		if ($item)
		{
			$lang = '';

			if ($item->language !== '*' && \Joomla\CMS\Language\Multilanguage::isEnabled())
			{
				$lang = '&lang=' . $item->language;
			}

			return 'index.php?Itemid=' . $item->id . $lang;
}
		// Stay on the same page
		return \Joomla\CMS\Uri\Uri::getInstance()->toString();
	}

	/**
	 * Computes the return-url for the module
	 *
	 * @param  \Joomla\Registry\Registry $params
	 * @param  string                    $type
	 * @param  array                     $attribs
	 * @return string
	 */
	static function getReturnURL( $params, $type, $attribs )
	{
		global $cbSpecialReturnAfterLogin, $cbSpecialReturnAfterLogout;

		static $returnUrl			=	null;

		if ( ! isset( $returnUrl ) ) {
			if ( isset( $attribs['return_url'] ) && $attribs['return_url'] ) {
				// Redirect was sent with the module render object so use it:
				$returnUrl			=	$attribs['return_url'];
			} else {
				$returnUrl			=	Application::Input()->get( 'get/return', '', GetterInterface::BASE64 );

				if ( $returnUrl ) {
					$returnUrl		=	base64_decode( $returnUrl );
				}
			}

			// Try to find the return URL from login session (e.g. access denied cases):
			if ( ! $returnUrl ) {
				$app						=	JFactory::getApplication();
				$returnUrl					=	$app->getUserState( 'users.login.form.return', null );

				if ( ! $returnUrl ) {
					$data					=	$app->getUserState( 'users.login.form.data', array() );
					$returnUrl				=	( isset( $data['return'] ) ? $data['return'] : null );

					if ( $returnUrl ) {
						$data['return']		=	null;

						// remove it from state data so navigating away doesn't cause us to return to a past place:
						$app->setUserState( 'users.login.form.data', $data );
					}
				} else {
					// remove it from state form so navigating away doesn't cause us to return to a past place:
					$app->setUserState( 'users.login.form.return', null );
				}
			}

			if ( $returnUrl ) {
				if ( ! JUri::isInternal( $returnUrl ) ) {
					// The URL isn't internal to the site; reset it to index to be safe:
					$returnUrl		=	'index.php';
				}
			} else {
				$returnUrl			=	static::getJoomlaReturnUrl( $params, $type );
				}
			if ( ! $returnUrl ) {
				$isHttps			=	( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( $_SERVER['HTTPS'] != 'off' ) );
				$returnUrl			=	'http' . ( $isHttps ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'];

				if ( ( ! empty( $_SERVER['PHP_SELF'] ) ) && ( ! empty( $_SERVER['REQUEST_URI'] ) ) ) {
					$returnUrl		.=	$_SERVER['REQUEST_URI'];
				} else {
					$returnUrl		.=	$_SERVER['SCRIPT_NAME'];

					if ( isset( $_SERVER['QUERY_STRING'] ) && ( ! empty( $_SERVER['QUERY_STRING'] ) ) ) {
						$returnUrl	.=	'?' . $_SERVER['QUERY_STRING'];
					}
				}
			}

			$returnUrl				=	cbUnHtmlspecialchars( preg_replace( '/[\\\"\\\'][\\s]*javascript:(.*)[\\\"\\\']/', '""', preg_replace( '/eval\((.*)\)/', '', htmlspecialchars( urldecode( $returnUrl ) ) ) ) );

			if ( preg_match( '/index.php\?option=com_comprofiler&task=confirm&confirmCode=|index.php\?option=com_comprofiler&view=confirm&confirmCode=|index.php\?option=com_comprofiler&task=login|index.php\?option=com_comprofiler&view=login/', $returnUrl ) ) {
				$returnUrl			=	'index.php';
			}
		}

		$secureForm					=	(int) $params->get( 'https_post', 0 );

		if ( $type == 'login' ) {
			$loginReturnUrl 		=	$params->get( 'login', $returnUrl );

			if ( isset( $cbSpecialReturnAfterLogin ) ) {
				$loginReturnUrl		=	$cbSpecialReturnAfterLogin;
			}

			$url					=	cbSef( $loginReturnUrl, true, 'html', $secureForm );
		} elseif ( $type == 'logout' ) {
			$logoutReturnUrl 		=	$params->get( 'logout', 'index.php' );

			if ( $logoutReturnUrl == '#' ) {
				$logoutReturnUrl	=	$returnUrl;
			}

			if ( isset( $cbSpecialReturnAfterLogout ) ) {
				$logoutReturnUrl	=	$cbSpecialReturnAfterLogout;
			}

			$url					=	cbSef( $logoutReturnUrl, true, 'html', $secureForm );
		} else {
			$url					=	$returnUrl;
		}

		return base64_encode( $url );
	}

	/**
	 * Triggers CB plugins for $location
	 *
	 * @param  \Joomla\Registry\Registry  $params
	 * @param  string                     $type
	 * @param  string                     $location
	 * @param  string                     $tag
	 * @param  int                        $horizontal
	 * @param  string                     $prefixHtml
	 * @param  string                     $suffixHtml
	 * @param  string                     $prefixCSS
	 * @return null|string
	 */
	static function getPlugins( $params, $type, $location = 'beforeButton', $tag = 'div', $horizontal = 0, $prefixHtml = '', $suffixHtml = '', $prefixCSS = '' )
	{
		global $_PLUGINS;

		if ( ! $location ) {
			$location									=	'beforeButton';
		}

		if ( ! $tag ) {
			$tag										=	'div';
		}

		if ( $type == 'logout' ) {
			$pluginClassPrefix							=	'cbLogoutForm';
			$pluginsTrigger								=	'onAfterLogoutForm';
		} else {
			$pluginClassPrefix							=	'cbLoginForm';
			$pluginsTrigger								=	'onAfterLoginForm';
		}

		$pluginDisplays									=	array();

		if ( $params->get( 'cb_plugins', 1 ) ) {
			$classSuffix								=	$params->get( 'moduleclass_sfx' );
			$usernameInputLength						=	(int) $params->get( 'name_length', 14 );
			$passwordInputLength						=	(int) $params->get( 'pass_length', 14 );

			$pluginsResults								=	$_PLUGINS->trigger( $pluginsTrigger, array( $usernameInputLength, $passwordInputLength, $horizontal, $classSuffix, &$params ) );

			if ( count( $pluginsResults ) > 0 ) foreach ( $pluginsResults as $pR ) {
				if ( is_array( $pR ) ) foreach ( $pR as $pK => $pV ) {
					if ( $pV != '' ) {
						$pluginDisplays[$pK][]			=	$pV;
					}
				} elseif ( $pR != '' ) {
					$pluginDisplays['beforeButton'][]	=	$pR;
				}
			}
		}

		$return											=	null;

		if ( isset( $pluginDisplays[$location] ) ) {
			$return										.=	$prefixHtml;

			foreach ( $pluginDisplays[$location] as $pV ) {
				$return									.=	( $tag ? '<' . htmlspecialchars( $tag ) . ' class="' . ( $prefixCSS ? $prefixCSS . ' ' : null ) . $pluginClassPrefix . ucfirst( htmlspecialchars( $location ) ) . '">' : null )
														.		$pV
														.	( $tag ? '</' . htmlspecialchars( $tag ) . '>' : null );
			}

			$return										.=	$suffixHtml;
		}

		return $return;
	}

	/**
	 * Gets 2-factor authentication methods
	 *
	 * @return array
	 */
	static function getTwoFactorMethods()
	{
		if ( checkJversion( '4.0+' ) ) {
			return [];
		}

		return JAuthenticationHelper::getTwoFactorMethods();
	}

	/**
	 * Gets additional login buttons (e.g. webauthn)
	 *
	 * @param int $id
	 * @return array
	 */
	static function getExtraButtons( $id )
	{
		if ( checkJversion( '<4.0' ) ) {
			return [];
		}

		return AuthenticationHelper::getLoginButtons( $id );
	}
}