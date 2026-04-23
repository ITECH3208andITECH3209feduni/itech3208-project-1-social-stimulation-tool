<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\PMS\Trigger;

use CBLib\Application\Application;

defined('CBLIB') or die();

class RouterTrigger extends \cbPluginHandler
{

	/**
	 * @param \ComprofilerRouter $router
	 * @param string             $plugin
	 * @param array              $segments
	 * @param array              $query
	 * @param \JMenuSite         $menuItem
	 */
	public function build( $router, $plugin, &$segments, &$query, &$menuItem )
	{
		global $_CB_framework;

		if ( ( $plugin != 'pms.mypmspro' ) || ( ! $query ) || ( ! isset( $query['action'] ) ) ) {
			return;
		}

		if ( isset( $query['action'] ) ) {
			$action							=	$query['action'];

			if ( $action != 'messages' ) {
				$segments[]					=	$action;
			}

			unset( $query['action'] );

			if ( isset( $query['func'] ) ) {
				$func						=	$query['func'];

				if ( $func == 'new' ) {
					if ( isset( $query['to'] ) ) {
						$to					=	$query['to'];

						if ( $to && is_numeric( $to ) ) {
							$username		=	\CBuser::getUserDataInstance( (int) $to )->getString( 'username', '' );

							$aliasReg		=	'/(^[^a-zA-Z])|[^a-zA-Z0-9\-]/';

							if ( $_CB_framework->getCfg( 'unicodeslugs' ) == 1 ) {
								$aliasReg	=	'/(^[^a-zA-Z\p{L}])|[^a-zA-Z0-9\-\p{L}]/u';
							}

							// Ensure the username isn't numeric, doesn't begin with a digit-, and that it's alias safe otherwise prefix with user id:
							if ( ( ! is_numeric( $username ) )
								 && ( ! preg_match( $aliasReg, $username ) )
								 && ( $username == Application::Router()->stringToAlias( $username ) )
								 && ( ! in_array( $username, array( 'received', 'sent', 'modal', 'quick', 'new', 'edit', 'save', 'read', 'unread', 'delete', 'show' ) ) )
							) {
								$to			=	$username;
							}
						}

						if ( is_numeric( $to ) ) {
							// Keep /new in the URL since we won't be able to determine if this is creating a message or reading one:
							$segments[]		=	$query['func'];
						}

						$segments[]			=	$to;

						unset( $query['to'] );
					} else {
						$segments[]			=	$query['func'];
					}
				} elseif ( $func != 'show' ) {
					$segments[]				=	$query['func'];
				}

				unset( $query['func'] );
			}

			if ( isset( $query['id'] ) ) {
				$segments[]					=	$query['id'];

				unset( $query['id'] );
			}
		}
	}

	/**
	 * @param \ComprofilerRouter $router
	 * @param string             $plugin
	 * @param array              $segments
	 * @param array              $vars
	 * @param \JMenuSite         $menuItem
	 */
	public function parse( $router, $plugin, $segments, &$vars, $menuItem )
	{
		if ( ( $plugin != 'pms.mypmspro' ) || ( ! $segments ) ) {
			return;
		}

		if ( isset( $segments[0] ) ) {
			if ( isset( $segments[1] ) ) {
				$action					=	$segments[0];

				if ( in_array( $action, array( 'edit', 'save', 'read', 'unread', 'delete', 'show' ) ) ) {
					$vars['action']		=	'message';
					$func				=	$action;
					$id					=	$segments[1];
				} elseif ( in_array( $action, array( 'received', 'sent', 'modal' ) ) ) {
					$vars['action']		=	'messages';
					$func				=	$action;
					$id					=	$segments[1];
				} else {
					$vars['action']		=	$action;
					$func				=	$segments[1];
					$id					=	( isset( $segments[2] ) ? $segments[2] : null );
				}

				if ( is_numeric( $func ) ) {
					$vars['func']		=	'show';
					$vars['id']			=	$func;
				} elseif ( ! in_array( $func, array( 'received', 'sent', 'modal', 'quick', 'new', 'edit', 'save', 'read', 'unread', 'delete', 'show' ) ) ) {
					$vars['func']		=	'new';
					$vars['to']			=	$func;
				} else {
					$vars['func']		=	$func;

					if ( $id ) {
						if ( $func == 'new' ) {
							$vars['to']	=	$id;
						} else {
							$vars['id']	=	$id;
						}
					}
				}
			} else {
				$func					=	$segments[0];

				if ( in_array( $func, array( 'edit', 'save', 'read', 'unread', 'delete' ) ) ) {
					$vars['action']		=	'message';
				} else {
					$vars['action']		=	'messages';
				}

				if ( in_array( $func, array( 'received', 'sent', 'modal', 'quick', 'new', 'edit', 'save', 'read', 'unread', 'delete', 'show' ) ) ) {
					$vars['func']		=	$func;
				} else {
					$vars['func']		=	'new';
					$vars['to']			=	$func;
				}
			}
		}
	}
}