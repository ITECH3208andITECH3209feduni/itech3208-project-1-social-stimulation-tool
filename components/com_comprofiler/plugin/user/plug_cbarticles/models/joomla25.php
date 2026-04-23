<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CB\Database\Table\PluginTable;
use CB\Database\Table\UserTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * Class cbarticlesModel
 * Articles Model for Joomla articles
 */
class cbarticlesModel
{
	/**
	 * Gets Total number of articles
	 *
	 * @param  string       $where
	 * @param  UserTable    $viewer
	 * @param  UserTable    $user
	 * @param  PluginTable  $plugin
	 * @return null|string
	 */
	static public function getArticlesTotal( $where, /** @noinspection PhpUnusedParameterInspection */ $viewer, $user, $plugin )
	{
		global $_CB_database;

		$categories			=	$plugin->params->get( 'article_j_category', null );

		$query				=	'SELECT COUNT(*)'
							.	"\n FROM " . $_CB_database->NameQuote( '#__content' ) . " AS a"
							.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__categories' ) . " AS b"
							.	' ON b.' . $_CB_database->NameQuote( 'id' ) . ' = a.' . $_CB_database->NameQuote( 'catid' )
							.	"\n WHERE a." . $_CB_database->NameQuote( 'created_by' ) . " = " . (int) $user->get( 'id' );

		if ( checkJversion( '<4.0' ) ) {
			$query			.=	"\n AND ( a." . $_CB_database->NameQuote( 'publish_up' ) . " = " . $_CB_database->Quote( $_CB_database->getNullDate() ) . " OR a." . $_CB_database->NameQuote( 'publish_up' ) . " <= " . $_CB_database->Quote( $_CB_database->getUtcDateTime() ) . " )"
							.	"\n AND ( a." . $_CB_database->NameQuote( 'publish_down' ) . " = " . $_CB_database->Quote( $_CB_database->getNullDate() ) . " OR a." . $_CB_database->NameQuote( 'publish_down' ) . " >= " . $_CB_database->Quote( $_CB_database->getUtcDateTime() ) . " )";
		} else {
			$query			.=	"\n AND ( a." . $_CB_database->NameQuote( 'publish_up' ) . " IS NULL OR a." . $_CB_database->NameQuote( 'publish_up' ) . " <= " . $_CB_database->Quote( $_CB_database->getUtcDateTime() ) . " )"
							.	"\n AND ( a." . $_CB_database->NameQuote( 'publish_down' ) . " IS NULL OR a." . $_CB_database->NameQuote( 'publish_down' ) . " >= " . $_CB_database->Quote( $_CB_database->getUtcDateTime() ) . " )";
		}

		$query				.=	"\n AND a." . $_CB_database->NameQuote( 'state' ) . " = 1"
							.	"\n AND a." . $_CB_database->NameQuote( 'access' ) . " IN " . $_CB_database->safeArrayOfIntegers( Application::MyUser()->getAuthorisedViewLevels() )
							.	"\n AND b." . $_CB_database->NameQuote( 'published' ) . " = 1"
							.	"\n AND b." . $_CB_database->NameQuote( 'access' ) . " IN " . $_CB_database->safeArrayOfIntegers( Application::MyUser()->getAuthorisedViewLevels() );

		if ( $categories ) {
			$categories		=	explode( '|*|', $categories );

			cbArrayToInts( $categories );

			$query			.=	"\n AND a." . $_CB_database->NameQuote( 'catid' ) . " NOT IN ( " . implode( ',', $categories ) . " )";
		}

		$query				.=	$where;

		$_CB_database->setQuery( $query );

		return $_CB_database->loadResult();
	}

	/**
	 * Gets articles
	 *
	 * @param  int[]        $paging
	 * @param  string       $where
	 * @param  string       $orderBy
	 * @param  UserTable    $viewer
	 * @param  UserTable    $user
	 * @param  PluginTable  $plugin
	 * @return Table[]
	 */
	static public function getArticles( $paging, $where, $orderBy, /** @noinspection PhpUnusedParameterInspection */ $viewer, $user, $plugin )
	{
		global $_CB_database;

		$categories			=	$plugin->params->get( 'article_j_category', null );

		$query				=	'SELECT a.*'
							.	', b.' . $_CB_database->NameQuote( 'id' ) . ' AS category'
							.	', b.' . $_CB_database->NameQuote( 'title' ) . ' AS category_title'
							.	', b.' . $_CB_database->NameQuote( 'published' ) . ' AS category_published'
							.	', b.' . $_CB_database->NameQuote( 'alias' ) . ' AS category_alias'
							.	"\n FROM " . $_CB_database->NameQuote( '#__content' ) . " AS a"
							.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__categories' ) . " AS b"
							.	' ON b.' . $_CB_database->NameQuote( 'id' ) . ' = a.' . $_CB_database->NameQuote( 'catid' )
							.	"\n WHERE a." . $_CB_database->NameQuote( 'created_by' ) . " = " . (int) $user->get( 'id' );

		if ( checkJversion( '<4.0' ) ) {
			$query			.=	"\n AND ( a." . $_CB_database->NameQuote( 'publish_up' ) . " = " . $_CB_database->Quote( $_CB_database->getNullDate() ) . " OR a." . $_CB_database->NameQuote( 'publish_up' ) . " <= " . $_CB_database->Quote( $_CB_database->getUtcDateTime() ) . " )"
							.	"\n AND ( a." . $_CB_database->NameQuote( 'publish_down' ) . " = " . $_CB_database->Quote( $_CB_database->getNullDate() ) . " OR a." . $_CB_database->NameQuote( 'publish_down' ) . " >= " . $_CB_database->Quote( $_CB_database->getUtcDateTime() ) . " )";
		} else {
			$query			.=	"\n AND ( a." . $_CB_database->NameQuote( 'publish_up' ) . " IS NULL OR a." . $_CB_database->NameQuote( 'publish_up' ) . " <= " . $_CB_database->Quote( $_CB_database->getUtcDateTime() ) . " )"
							.	"\n AND ( a." . $_CB_database->NameQuote( 'publish_down' ) . " IS NULL OR a." . $_CB_database->NameQuote( 'publish_down' ) . " >= " . $_CB_database->Quote( $_CB_database->getUtcDateTime() ) . " )";
		}

		$query				.=	"\n AND a." . $_CB_database->NameQuote( 'state' ) . " = 1"
							.	"\n AND a." . $_CB_database->NameQuote( 'access' ) . " IN " . $_CB_database->safeArrayOfIntegers( Application::MyUser()->getAuthorisedViewLevels() )
							.	"\n AND b." . $_CB_database->NameQuote( 'published' ) . " = 1"
							.	"\n AND b." . $_CB_database->NameQuote( 'access' ) . " IN " . $_CB_database->safeArrayOfIntegers( Application::MyUser()->getAuthorisedViewLevels() );

		if ( $categories ) {
			$query			.=	"\n AND a." . $_CB_database->NameQuote( 'catid' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( explode( '|*|', $categories ) );
		}

		$query				.=	$where;

		if ( ! $orderBy ) {
			$orderBy		=	'created,desc';
		}

		$orderBy			=	explode( ',', $orderBy );

		$query				.=	"\n ORDER BY a." . $_CB_database->NameQuote( $orderBy[0] ) . ( $orderBy[1] == 'asc' ? " ASC" : ( $orderBy[1] == 'desc' ? " DESC" : null ) );

		if ( $paging ) {
			$_CB_database->setQuery( $query, $paging[0], $paging[1] );
		} else {
			$_CB_database->setQuery( $query );
		}

		return $_CB_database->loadObjectList( null, '\CBLib\Database\Table\Table', array( null, '#__content', 'id' ) );
	}

	/**
	 * Returns the URL for an article
	 *
	 * @param  Table    $row
	 * @param  boolean  $htmlspecialchars
	 * @param  string   $type              'article', 'section' or 'category'
	 * @return string                      URL
	 */
	static public function getUrl( $row, $htmlspecialchars = true, $type = 'article' )
	{
		global $_CB_framework;

		if ( checkJversion( '<4.0' ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once ( $_CB_framework->getCfg( 'absolute_path' ) . '/components/com_content/helpers/route.php' );
		}

		$categorySlug		=	$row->get( 'category' ) . ( $row->get( 'category_alias' ) ? ':' . $row->get( 'category_alias' ) : null );
		$articleSlug		=	$row->get( 'id' ) . ( $row->get( 'alias' ) ? ':' . $row->get( 'alias' ) : null );

		switch ( $type ) {
			case 'section':
			case 'category':
				if ( checkJversion( '4.0+' ) ) {
					$url	=	\Joomla\Component\Content\Site\Helper\RouteHelper::getCategoryRoute( $categorySlug, $row->get( 'language', 0 ) );
				} else {
					$url	=	ContentHelperRoute::getCategoryRoute( $categorySlug, $row->get( 'language', 0 ) );
				}
				break;
			case 'article':
			default:
				if ( checkJversion( '4.0+' ) ) {
					$url	=	\Joomla\Component\Content\Site\Helper\RouteHelper::getArticleRoute( $articleSlug, $categorySlug, $row->get( 'language', 0 ) );
				} else {
					$url	=	ContentHelperRoute::getArticleRoute( $articleSlug, $categorySlug, $row->get( 'language', 0 ) );
				}
				break;
		}

		$url				=	JRoute::_( $url, false );

		if ( $url ) {
			if ( $htmlspecialchars ) {
				$url		=	htmlspecialchars( $url );
			}
		}

		return $url;
	}
}
