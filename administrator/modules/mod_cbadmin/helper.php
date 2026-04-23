<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Xml\SimpleXMLElement;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class modCBAdminHelper {

	/**
	 * @param array $menus
	 * @param bool $disabled
	 * @return null|string
	 */
	public static function getTable( $menus, $disabled = false ) {
		$return					=	null;

		if ( $menus ) {
			$return				.=	'<div class="cbMenuTable">';

			foreach ( $menus as $menu ) {
				if ( isset( $menu['menu'] ) ) {
					$items		=	$menu['menu'];
				} else {
					$items		=	array();
				}

				if ( isset( $menu['component'] ) ) {
					$return		.=		'<table class="table table-hover m-0">'
								.			modCBAdminHelper::getTabItems( $menu['component'], $items, $disabled )
								.		'</table>';
				}
			}

			$return				.=	'</div>';
		}

		return $return;
	}

	/**
	 * @param  array $component    The parent menu node
	 * @param  array $items        The menu items to output
	 * @param  bool  $disabled     If the menu item should be disabled (grayed)
	 * @param  int   $depth        The depth of the menu items
	 * @param  bool  $subcomponent If the menu item is a subcomponent of an existing component
	 * @return null|string
	 */
	private static function getTabItems( $component, $items, $disabled = false, $depth = 0, $subcomponent = false ) {
		$parentTitleCleanHtml			=	( isset( $component['title'] ) ? $component['title'] : null );
		$parentAccess					=	( isset( $component['access'] ) ? $component['access'] : null );
		$return							=	null;

		if ( $parentTitleCleanHtml && modCBAdminHelper::checkAccess( $parentAccess ) ) {
			if ( ! $disabled ) {
				if ( ( ! $depth ) || $subcomponent ) {
					$return				.=	( ! $subcomponent ? '<thead>' : null )
										.		'<tr>'
										.			'<th>'
										.				( $depth ? str_repeat( '<span class="fa fa-angle-right text-muted"></span> ', $depth ) : null )
										.				$parentTitleCleanHtml
										.			'</th>'
										.		'</tr>'
										.	( ! $subcomponent ? '</thead>' : null );

					if ( $subcomponent ) {
						$depth++;
					}
				}

				if ( $items ) {
					if ( ! $depth ) {
						$return			.=	'<tbody>';
					}

					foreach ( $items as $item ) {
						$title			=	( isset( $item['title'] ) ? $item['title'] : null );
						$link			=	( isset( $item['link'] ) ? $item['link'] : null );
						$access			=	( isset( $item['access'] ) ? $item['access'] : null );
						$target			=	( isset( $item['target'] ) ? $item['target'] : null );
						$subMenu		=	( isset( $item['submenu'] ) ? $item['submenu'] : array() );

						if ( $title && $link && modCBAdminHelper::checkAccess( $access ) ) {
							$return		.=		'<tr>'
										.			'<td>'
										.				( $depth ? str_repeat( '<span class="fa fa-angle-right text-muted"></span> ', $depth ) : null )
										.				'<a href="' . htmlspecialchars( $link ) . '"' . ( $target ? ' target="' . htmlspecialchars( $target ) . '"' : null ) . '>' . $title . '</a>'
										.			'</td>'
										.		'</tr>';

							if ( $subMenu ) {
								$return	.=		( $subMenu ? modCBAdminHelper::getTabItems( $item, $subMenu, false, ( $depth + 1 ) ) : null );
							}
						} elseif ( isset( $item['component'] ) ) {
							$subMenu	=	modCBAdminHelper::getTabItems( $item['component'], ( isset( $item['menu'] ) ? $item['menu'] : array() ), false, ( $depth + 1 ), true );

							if ( ! $subMenu ) {
								continue;
							}

							$return		.=		$subMenu;
						}
					}

					if ( ! $depth ) {
						$return			.=	'</tbody>';
					}
				}
			} elseif ( ! $depth ) {
				$return					.=	'<thead>'
										.		'<tr class="active text-muted">'
										.			'<th>' . $parentTitleCleanHtml . '</th>'
										.		'</tr>'
										.	'</thead>';
			}
		}

		return $return;
	}

	/**
	 * @param array $menus
	 * @param bool $disabled
	 * @return null|string
	 */
	public static function getMenu( $menus, $disabled = false ) {
		global $_CB_framework;
		$return					=	null;

		if ( $menus ) {
			if ( checkJversion( '4.0+' ) ) {
				// J4 menu only goes 3 levels deep so we need to handle parent active state for those cases:
				$js				=	"var activeItem = $( '.cb-nav-container .collapse-level-4 a.mm-active' );"
								.	"if ( activeItem.length ) {"
								.		"activeItem.parents( 'ul:not(.mm-show)' ).addClass( 'mm-show' );"
								.		"activeItem.parents( 'li:not(.mm-active)' ).addClass( 'mm-active' );"
								.	"}";

				$_CB_framework->outputCbJQuery( $js );

				$return			=	'<ul id="cb-menu" class="nav flex-column main-nav cb-nav metismenu">';
			} else {
				$return			=	'<ul id="menu" class="nav' . ( $disabled ? ' disabled' : null ) . '">';
			}

			foreach ( $menus as $menu ) {
				if ( isset( $menu['menu'] ) ) {
					$items		=	$menu['menu'];
				} else {
					$items		=	array();
				}

				if ( isset( $menu['component'] ) ) {
					$return		.=		modCBAdminHelper::getMenuItems( $menu['component'], $items, $disabled );
				}
			}

			$return				.=	'</ul>';

			if ( checkJversion( '4.0+' ) ) {
				$return			=	'<nav class="cb-nav-container" aria-label="' . htmlspecialchars( CBTxt::T( 'Community Builder' ) ) . '">'
								.		$return
								.	'</nav>';
			}
		}

		return $return;
	}

	/**
	 * @param  array $component    The parent menu node
	 * @param  array $items        The menu items to output
	 * @param  bool  $disabled     If the menu item should be disabled (grayed)
	 * @param  int   $depth        The depth of the menu items
	 * @param  bool  $subcomponent If the menu item is a subcomponent of an existing component
	 * @return null|string
	 */
	private static function getMenuItems( $component, $items = array(), $disabled = false, $depth = 0, $subcomponent = false ) {
		$parentTitleCleanHtml			=	( isset( $component['title'] ) ? $component['title'] : null );
		$parentAccess					=	( isset( $component['access'] ) ? $component['access'] : null );
		$return							=	null;

		if ( $parentTitleCleanHtml && modCBAdminHelper::checkAccess( $parentAccess ) ) {
			if ( ! $disabled ) {
				if ( ( ! $depth ) || $subcomponent ) {
					if ( checkJversion( '4.0+' ) ) {
						$return			.=	'<li class="item item-level-' . ( $depth + 1 ) . ( ! $subcomponent ? ' parent' : null ) . '">'
										.		'<a class="has-arrow" href="#" aria-label="' . htmlspecialchars( strip_tags( $parentTitleCleanHtml ) ) . '">'
										.			( ! $depth ? '<span class="fas fa-puzzle-piece fa-fw" aria-hidden="true"></span>' : null )
										.			'<span class="sidebar-item-title">' . $parentTitleCleanHtml . '</span>'
										.		'</a>';
					} else {
						$return			.=	'<li class="' . ( $subcomponent ? 'dropdown-submenu' : 'dropdown' ) . '">'
										.		'<a class="dropdown-toggle" data-toggle="dropdown" href="#">'
										.			$parentTitleCleanHtml
										.			( ! $subcomponent ? ' <span class="caret"></span>' : null )
										.		'</a>';
					}

					if ( $subcomponent ) {
						$depth++;
					}
				}

				if ( $items ) {
					$menu				=	null;

					foreach ( $items as $item ) {
						$title			=	( isset( $item['title'] ) ? $item['title'] : null );
						$link			=	( isset( $item['link'] ) ? $item['link'] : null );
						$access			=	( isset( $item['access'] ) ? $item['access'] : null );
						$icon			=	( isset( $item['icon'] ) ? $item['icon'] : null );
						$target			=	( isset( $item['target'] ) ? $item['target'] : null );
						$subMenu		=	( isset( $item['submenu'] ) ? $item['submenu'] : array() );
						$taskMenu		=	( isset( $item['taskmenu'] ) ? $item['taskmenu'] : array() );

						if ( ! checkJversion( '4.0+' ) ) {
							if ( $taskMenu ) {
								if ( $subMenu ) {
									$subMenu	=	array_merge( $taskMenu, array( array( 'title' => 'spacer' ) ), $subMenu );
								} else {
									$subMenu	=	$taskMenu;
								}
							}

							$taskMenu			=	array();
						} elseif ( ( count( $subMenu ) == 1 )
								   && isset( $subMenu[0] )
								   && isset( $subMenu[0]['icon'] )
								   && ( $subMenu[0]['icon'] == 'cb-new' )
						) {
							// Only 1 sub menu item and it's to add a new entry so lets convert this to j4 quick task:
							$taskMenu	=	$subMenu;
							$subMenu	=	array();
						}

						if ( $title && $link && modCBAdminHelper::checkAccess( $access ) ) {
							$subMenuHTML		=	( $subMenu ? modCBAdminHelper::getMenuItems( $item, $subMenu, false, ( $depth + 1 ) ) : null );

							if ( checkJversion( '4.0+' ) ) {
								$taskMenuHTML	=	( $taskMenu ? modCBAdminHelper::getMenuTasks( $item, $taskMenu ) : null );

								if ( $subMenuHTML && ( $link != '#' ) ) {
									// Linked parent with sub menu doesn't navigate and instead expands submenu so rebuild submenu with this link pushed as a top level manage link:
									$manage				=	$item;
									$manage['title']	=	CBTxt::T( 'Manage' );
									$manage['submenu']	=	array();
									$manage['taskmenu']	=	array();

									array_unshift( $subMenu, $manage );

									$subMenuHTML		=	modCBAdminHelper::getMenuItems( $item, $subMenu, false, ( $depth + 1 ) );
								}

								$menu	.=			'<li class="item item-level-' . ( $depth + 2 ) . ( $subMenuHTML ? ' parent' : null ) . '">'
										.				'<a class="' . ( $subMenuHTML ? 'has-arrow' : 'no-dropdown' ) .  '" href="' . htmlspecialchars( $link ) . '"' . ( $target ? ' target="' . htmlspecialchars( $target ) . '"' : null ) . ' aria-label="' . htmlspecialchars( strip_tags( $title ) ) . '">'
										.					'<span class="sidebar-item-title">' . $title . '</span>'
										.				'</a>'
										.				$taskMenuHTML
										.				$subMenuHTML
										.			'</li>';
							} else {
								$menu	.=			'<li' . ( $subMenuHTML ? ' class="dropdown-submenu"' : null ) . '>'
										.				'<a class="' . ( $subMenuHTML ? 'dropdown-toggle' : 'no-dropdown' ) . ( $icon ? ' menu-' . htmlspecialchars( $icon ) : null ) . '"' . ( $subMenu ? ' data-toggle="dropdown"' : null ) . ' href="' . htmlspecialchars( $link ) . '"' . ( $target ? ' target="' . htmlspecialchars( $target ) . '"' : null ) . '>'
										.					'<span>' . $title . '</span>'
										.				'</a>'
										.				$subMenuHTML
										.			'</li>';
							}
						} elseif ( isset( $item['component'] ) ) {
							$subMenuHTML	=	modCBAdminHelper::getMenuItems( $item['component'], ( isset( $item['menu'] ) ? $item['menu'] : array() ), false, ( $depth + 1 ), true );

							if ( ! $subMenuHTML ) {
								continue;
							}

							$menu			.=			$subMenuHTML;
						} elseif ( $title == 'spacer' ) {
							if ( checkJversion( '4.0+' ) ) {
								$menu	.=			'<li class="divider item-level-' . ( $depth + 1 ) . '" role="presentation"><span></span></li>';
							} else {
								$menu	.=			'<li class="divider"><span></span></li>';
							}
						}
					}

					if ( $menu ) {
						if ( checkJversion( '4.0+' ) ) {
							$return		.=		'<ul class="collapse-level-' . ( $depth + 1 ) . ' mm-collapse">'
										.			$menu
										.		'</ul>';
						} else {
							$return		.=		'<ul class="dropdown-menu ' . ( ! $depth ? 'scroll-menu' : 'menu-scrollable' ) . '" style="z-index: 999;">'
										.			$menu
										.		'</ul>';
						}
					}
				}

				if ( ! $depth ) {
					$return				.=	'</li>';
				}
			} elseif ( ! $depth ) {
				if ( checkJversion( '4.0+' ) ) {
					$return			.=	'<li class="item item-level-' . ( $depth + 1 )  . ' disabled">'
									.		'<a class="no-dropdown" href="#" aria-label="' . htmlspecialchars( strip_tags( $parentTitleCleanHtml ) ) . '">'
									.			'<span class="fas fa-puzzle-piece fa-fw" aria-hidden="true"></span>'
									.			'<span class="sidebar-item-title">' . $parentTitleCleanHtml . '</span>'
									.		'</a>'
									.	'</li>';
				} else {
					$return			.=	'<li class="disabled">'
									.		'<a href="#">' . $parentTitleCleanHtml . '</a>'
									.	'</li>';
				}
			}
		}

		return $return;
	}

	/**
	 * @param  array $component    The parent menu node
	 * @param  array $items        The menu items to output
	 * @return null|string
	 */
	private static function getMenuTasks( $component, $items = array() ) {
		if ( ! $items ) {
			return null;
		}

		$tasks			=	null;

		foreach ( $items as $item ) {
			$title		=	( isset( $item['title'] ) ? $item['title'] : null );
			$link		=	( isset( $item['link'] ) ? $item['link'] : null );
			$access		=	( isset( $item['access'] ) ? $item['access'] : null );
			$icon		=	( isset( $item['taskicon'] ) ? $item['taskicon'] : ( isset( $item['icon'] ) ? $item['icon'] : null ) );
			$target		=	( isset( $item['target'] ) ? $item['target'] : null );

			if ( ( ! $title ) || ( ! $link ) || ( ! modCBAdminHelper::checkAccess( $access ) ) ) {
				continue;
			}

			if ( ( ! $icon ) || ( $icon == 'cb-new' ) ) {
				$icon	=	'fas fa-plus';
			}

			$tasks		.=			'<a href="' . htmlspecialchars( $link ) . '"' . ( $target ? ' target="' . htmlspecialchars( $target ) . '"' : null ) . '>'
						.				'<span class="' . htmlspecialchars( $icon ) . '" title="' . htmlspecialchars( strip_tags( $title ) ) . '" aria-hidden="true"></span>'
						.				'<span class="sr-only">' . $title . '</span>'
						.			'</a>';
		}

		if ( ! $tasks ) {
			return null;
		}

		return '<span class="menu-quicktask">' . $tasks . '</span>';
	}

	/**
	 * Re-enables the update site if disabled or creates it if missing
	 */
	public static function enableUpdateSite()
	{
		global $_CB_database;

		$query			=	'SELECT ' . $_CB_database->NameQuote( 'extension_id' )
						.	"\n FROM " . $_CB_database->NameQuote( '#__extensions' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'element' ) . ' = ' . $_CB_database->Quote( 'pkg_communitybuilder' );
		$_CB_database->setQuery( $query );
		$extensionId	=	$_CB_database->loadResult();

		$query			=	'SELECT ' . $_CB_database->NameQuote( 'update_site_id' )
						.	"\n FROM " . $_CB_database->NameQuote( '#__update_sites_extensions' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'extension_id' ) . ' = ' . (int) $extensionId;
		$_CB_database->setQuery( $query );
		$updateSiteId	=	$_CB_database->loadResult();

		if ( $updateSiteId ) {
			$query		=	'SELECT ' . $_CB_database->NameQuote( 'enabled' )
						.	"\n FROM " . $_CB_database->NameQuote( '#__update_sites' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'update_site_id' ) . ' = ' . (int) $updateSiteId;
			$_CB_database->setQuery( $query );
			$isEnabled	=	$_CB_database->loadResult();

			if ( ! $isEnabled ) {
				$query	=	'UPDATE ' . $_CB_database->NameQuote( '#__update_sites' )
						.	"\n SET " . $_CB_database->NameQuote( 'enabled' ) . " = 1"
						.	"\n WHERE " . $_CB_database->NameQuote( 'update_site_id' ) . " = " . (int) $updateSiteId;
				$_CB_database->setQuery( $query );
				$_CB_database->query();
			}
		} else {
			$query		=	'INSERT INTO ' . $_CB_database->NameQuote( '#__update_sites' )
						.	"\n ("
						.		$_CB_database->NameQuote( 'name' )
						.		', ' . $_CB_database->NameQuote( 'type' )
						.		', ' . $_CB_database->NameQuote( 'location' )
						.		', ' . $_CB_database->NameQuote( 'enabled' )
						.	')'
						.	"\n VALUES ("
						.		$_CB_database->Quote( 'Community Builder Package Update Site' )
						.		', ' . $_CB_database->Quote( 'collection' )
						.		', ' . $_CB_database->Quote( 'https://update.joomlapolis.net/versions/pkg-communitybuilder-list.xml' )
						.		', 1'
						.	')';
			$_CB_database->setQuery( $query );
			$_CB_database->query();

			$query		=	'INSERT INTO ' . $_CB_database->NameQuote( '#__update_sites_extensions' )
						.	"\n ("
						.		$_CB_database->NameQuote( 'update_site_id' )
						.		', ' . $_CB_database->NameQuote( 'extension_id' )
						.	')'
						.	"\n VALUES ("
						.		(int) $_CB_database->insertid()
						.		', ' . (int) $extensionId
						.	')';
			$_CB_database->setQuery( $query );
			$_CB_database->query();
		}
	}

	/**
	 * @param array $access
	 * @return bool
	 */
	private static function checkAccess( $access ) {
		if ( $access ) {
			$actions			=	( isset( $access[0] ) ? $access[0] : null );

			if ( $actions ) {
				$assetName		=	( isset( $access[1] ) ? ( $access[1] == 'root' ? null : $access[1] ) : 'com_comprofiler' );

				if ( ! is_array( $actions ) ) {
					$actions	=	array( $actions );
				}

				foreach( $actions as $action ) {
					if ( Application::MyUser()->isAuthorizedToPerformActionOnAsset( $action, $assetName ) ) {
						return true;
					}
				}
			}
		} else {
			return true;
		}

		return false;
	}

	/**
	 * @param string $url
	 * @param string $file
	 * @param int $duration
	 * @return SimpleXMLElement|null
	 */
	public static function getFeedXML( $url, $file, $duration = 12 ) {
		global $_CB_framework;

		$cache					=	$_CB_framework->getCfg( 'absolute_path' ) . '/cache/' . $file;
		$xml					=	null;

		if ( file_exists( $cache ) ) {
			if ( ( ! $duration ) || ( intval( ( $_CB_framework->now() - filemtime( $cache ) ) / 3600 ) > $duration ) ) {
				$request		=	true;
			} else {
				$xml			=	new SimpleXMLElement( trim( file_get_contents( $cache ) ) );

				$request		=	false;
			}
		} else {
			$request			=	true;
		}

		if ( $request ) {
			try {
				$guzzleHttpClient		=	new GuzzleHttp\Client();
				$guzzleRequest			=	$guzzleHttpClient->get( $url, array( 'headers' => array( 'referer' =>  $_CB_framework->getCfg( 'live_site' ) ), 'timeout' => 10 ) );

				if ( $guzzleRequest->getStatusCode() == 200 ) {
					$xml				=	new SimpleXMLElement( (string) $guzzleRequest->getBody() );

					$xml->saveXML( $cache );
				}
			} catch( Exception $e ) {}
		}

		return $xml;
	}

	/**
	 * @param string $text
	 * @param null|int $length
	 * @return mixed|string
	 */
	static public function shortDescription( $text, $length = null ) {
		$text		=	stripslashes( strip_tags( $text ) );

		if ( $length && ( strlen( $text ) > $length ) ) {
			$text	=	preg_replace( '/(\.\.\.\s*){2,}/', '... ', trim( substr( $text, 0, $length ) ) . '...' );
		}

		$text		=	trim( $text );

		return $text;
	}

	/**
	 * @param string $text
	 * @return mixed
	 */
	static public function longDescription( $text ) {
		if ( preg_match_all( '/<a[^>]+>/i', $text, $links ) ) {
			foreach ( $links as $link ) {
				$text	=	str_replace( $link, str_replace( '<a', '<a target="_blank"', preg_replace( '/target="\w+"/i', '', $link ) ), $text );
			}
		}

		$text			=	preg_replace( '%src="[^"]+/(//www[^"]+)"%i', 'src="$1"', $text );

		return $text;
	}

	/**
	 * @param string $text
	 * @return null|string
	 */
	static public function descriptionIcon( $text ) {
		$logo		=	null;

		if ( preg_match( '/<img[^>]+>/i', $text, $image ) ) {
			if ( preg_match( '/src="([^"]+)"/i', $image[0], $src ) ) {
				$logo	=	'<div class="cbFeedItemLogoImg" style="background-image: url(' . htmlspecialchars( $src[1] ) . ')"></div>';
			}
		}

		if ( ! $logo ) {
			$logo		=	'<div class="cbFeedItemLogoImg cbFeedItemLogoImgEmpty"></div>';
		}

		return $logo;
	}
}
