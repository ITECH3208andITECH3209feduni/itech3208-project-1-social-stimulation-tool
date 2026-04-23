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
use CBLib\Language\CBTxt;
use CB\Database\Table\TabTable;
use CB\Database\Table\PluginTable;
use CB\Database\Table\UserTable;
use Kunena\Forum\Libraries\Access\KunenaAccess as Kunena6Access;
use Kunena\Forum\Libraries\Factory\KunenaFactory as Kunena6Factory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute as Kunena6Route;
use Kunena\Forum\Libraries\User\KunenaBan;
use Kunena\Forum\Libraries\User\KunenaUser as Kunena6User;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * Class cbforumsModel
 * CB Forums Model for Kunena
 */
class cbforumsModel extends cbPluginHandler
{
	/**
	 * @param  UserTable    $viewer  Viewing User
	 * @param  UserTable    $user    Viewed at User
	 * @param  TabTable     $tab     Current Tab
	 * @param  PluginTable  $plugin  Current Plugin
	 * @return string                HTML
	 */
	static public function getPosts( $viewer, $user, $tab, $plugin )
	{
		global $_CB_framework, $_CB_database;

		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper' ) ) {
				return CBTxt::T( 'Kunena not installed, enabled, or failed to load.' );
			}
		} elseif ( ! class_exists( 'KunenaForumMessageHelper' ) ) {
			return CBTxt::T( 'Kunena not installed, enabled, or failed to load.' );
		}

		cbimport( 'cb.pagination' );
		cbforumsClass::getTemplate( 'tab_posts' );

		$exclude				=	$plugin->params->get( 'forum_exclude', null );
		$limit					=	(int) $tab->params->get( 'tab_posts_limit', 15 );
		$orderBy				=	$tab->params->get( 'tab_posts_orderby', 'time,desc' );
		$limitstart				=	(int) $_CB_framework->getUserStateFromRequest( 'tab_posts_limitstart{com_comprofiler}', 'tab_posts_limitstart', 0 );
		$filterSearch			=	$_CB_framework->getUserStateFromRequest( 'tab_posts_search{com_comprofiler}', 'tab_posts_search', '' );
		$where					=	array();

		if ( isset( $filterSearch ) && ( $filterSearch != '' ) ) {
			$where[]			=	'( m.' . $_CB_database->NameQuote( 'subject' ) . ' LIKE ' . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $filterSearch, true ) . '%', false )
								.	' OR t.' . $_CB_database->NameQuote( 'message' ) . ' LIKE ' . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $filterSearch, true ) . '%', false ) . ' )';
		}

		$searching				=	( count( $where ) ? true : false );

		if ( $exclude ) {
			$where[]			=	'( m.' . $_CB_database->NameQuote( 'catid' ) . ' NOT IN ' . $_CB_database->safeArrayOfIntegers( explode( '|*|', $exclude ) ) . ' )';
		}

		if ( ! $orderBy ) {
			$orderBy			=	'time,desc';
		}

		$orderBy				=	explode( ',', $orderBy );

		$params					=	array(	'user' => (int) $user->id,
											'starttime' => -1,
											'where' => ( count( $where ) ? implode( ' AND ', $where ) : null ),
											'orderby' => 'm.' . $_CB_database->NameQuote( $orderBy[0] ) . ( $orderBy[1] == 'asc' ? " ASC" : ( $orderBy[1] == 'desc' ? " DESC" : null ) )
										);

		if ( cbforumsClass::getModel()->version >= 6 ) {
			$posts				=	KunenaMessageHelper::getLatestMessages( false, 0, 0, $params );
		} else {
			$posts				=	KunenaForumMessageHelper::getLatestMessages( false, 0, 0, $params );
		}

		$total					=	array_shift( $posts );

		if ( ( ! $total ) && ( ! $searching ) && ( ! Application::Config()->get( 'showEmptyTabs', true, GetterInterface::BOOLEAN ) ) ) {
			return null;
		}

		if ( $total <= $limitstart ) {
			$limitstart			=	0;
		}

		$pageNav				=	new cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( 'tab_posts_' );
		$pageNav->setStaticLimit( true );
		$pageNav->setBaseURL( $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ), 'html', 0, array( 'tab_posts_search' => ( $searching ? $filterSearch : null ) ) ) );

		if ( $tab->params->get( 'tab_posts_paging', 1 ) ) {
			if ( cbforumsClass::getModel()->version >= 6 ) {
				$posts			=	KunenaMessageHelper::getLatestMessages( false, (int) $pageNav->limitstart, (int) $pageNav->limit, $params );
			} else {
				$posts			=	KunenaForumMessageHelper::getLatestMessages( false, (int) $pageNav->limitstart, (int) $pageNav->limit, $params );
			}

			$posts				=	array_pop( $posts );
		} else {
			$posts				=	array_pop( $posts );
		}

		$rows					=	array();

		/** @var KunenaMessage[]|KunenaForumMessage[] $posts */
		if ( $posts ) foreach ( $posts as $post ) {
			$row				=	new stdClass;
			$row->id			=	$post->id;
			$row->subject		=	$post->subject;
			$row->message		=	$post->message;
			$row->date			=	$post->time;
			$row->url			=	$post->getUrl();
			$row->category_id	=	$post->getCategory()->id;
			$row->category_name	=	$post->getCategory()->name;
			$row->category_url	=	$post->getCategory()->getUrl();

			$rows[]				=	$row;
		}

		$input					=	array();
		$input['search']		=	'<input type="text" name="tab_posts_search" value="' . htmlspecialchars( $filterSearch ) . '" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Posts...' ) ) . '" class="form-control" />';

		return HTML_cbforumsTabPosts::showPosts( $rows, $pageNav, $searching, $input, $viewer, $user, $tab, $plugin );
	}

	/**
	 * View Forum Favorites
	 *
	 * @param  UserTable    $viewer  Viewing User
	 * @param  UserTable    $user    Viewed at User
	 * @param  TabTable     $tab     Current Tab
	 * @param  PluginTable  $plugin  Current Plugin
	 * @return string                HTML
	 */
	static public function getFavorites( $viewer, $user, $tab, $plugin )
	{
		global $_CB_framework, $_CB_database;

		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper' ) ) {
				return CBTxt::T( 'Kunena not installed, enabled, or failed to load.' );
			}
		} elseif ( ! class_exists( 'KunenaForumTopicHelper' ) ) {
			return CBTxt::T( 'Kunena not installed, enabled, or failed to load.' );
		}

		cbimport( 'cb.pagination' );
		cbforumsClass::getTemplate( 'tab_favs' );

		$limit					=	(int) $tab->params->get( 'tab_favs_limit', 15 );
		$orderBy				=	$tab->params->get( 'tab_favs_orderby', 'last_post_time,desc' );
		$limitstart				=	(int) $_CB_framework->getUserStateFromRequest( 'tab_favs_limitstart{com_comprofiler}', 'tab_favs_limitstart', 0 );
		$filterSearch			=	$_CB_framework->getUserStateFromRequest( 'tab_favs_search{com_comprofiler}', 'tab_favs_search', '' );
		$where					=	array();

		if ( isset( $filterSearch ) && ( $filterSearch != '' ) ) {
			$where[]			=	'( tt.' . $_CB_database->NameQuote( 'subject' ) . ' LIKE ' . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $filterSearch, true ) . '%', false )
								.	' OR tt.' . $_CB_database->NameQuote( 'first_post_message' ) . ' LIKE ' . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $filterSearch, true ) . '%', false ) . ' )';
		}

		$searching				=	( count( $where ) ? true : false );

		if ( ! $orderBy ) {
			$orderBy			=	'last_post_time,desc';
		}

		$orderBy				=	explode( ',', $orderBy );

		$params					=	array(	'user' => (int) $user->id,
											'favorited' => true,
											'starttime' => -1,
											'where' => ( count( $where ) ? ' AND ' . implode( ' AND ', $where ) : null ),
											'orderby' => 'tt.' . $_CB_database->NameQuote( $orderBy[0] ) . ( $orderBy[1] == 'asc' ? " ASC" : ( $orderBy[1] == 'desc' ? " DESC" : null ) )
										);

		if ( cbforumsClass::getModel()->version >= 6 ) {
			$topics				=	KunenaTopicHelper::getLatestTopics( false, 0, 0, $params );
		} else {
			$topics				=	KunenaForumTopicHelper::getLatestTopics( false, 0, 0, $params );
		}

		$total					=	array_shift( $topics );

		if ( ( ! $total ) && ( ! $searching ) && ( ! Application::Config()->get( 'showEmptyTabs', true, GetterInterface::BOOLEAN ) ) ) {
			return null;
		}

		if ( $total <= $limitstart ) {
			$limitstart			=	0;
		}

		$pageNav				=	new cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( 'tab_favs_' );
		$pageNav->setStaticLimit( true );
		$pageNav->setBaseURL( $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ), 'html', 0, array( 'tab_favs_search' => ( $searching ? $filterSearch : null ) ) ) );

		if ( $tab->params->get( 'tab_favs_paging', 1 ) ) {
			if ( cbforumsClass::getModel()->version >= 6 ) {
				$topics			=	KunenaTopicHelper::getLatestTopics( false, (int) $pageNav->limitstart, (int) $pageNav->limit, $params );
			} else {
				$topics			=	KunenaForumTopicHelper::getLatestTopics( false, (int) $pageNav->limitstart, (int) $pageNav->limit, $params );
			}

			$topics				=	array_pop( $topics );
		} else {
			$topics				=	array_pop( $topics );
		}

		$rows					=	array();

		/** @var KunenaTopic[]|KunenaForumTopic[] $topics */
		if ( $topics ) foreach ( $topics as $topic ) {
			$row				=	new stdClass;
			$row->id			=	$topic->id;
			$row->subject		=	$topic->subject;
			$row->message		=	$topic->first_post_message;
			$row->date			=	$topic->first_post_time;
			$row->url			=	$topic->getUrl();
			$row->category_id	=	$topic->getCategory()->id;
			$row->category_name	=	$topic->getCategory()->name;
			$row->category_url	=	$topic->getCategory()->getUrl();

			$rows[]				=	$row;
		}

		$input					=	array();
		$input['search']		=	'<input type="text" name="tab_favs_search" value="' . htmlspecialchars( $filterSearch ) . '" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Favorites...' ) ) . '" class="form-control" />';

		return HTML_cbforumsTabFavs::showFavorites( $rows, $pageNav, $searching, $input, $viewer, $user, $tab, $plugin );
	}

	/**
	 * View Forum Subscriptions
	 *
	 * @param  UserTable    $viewer  Viewing User
	 * @param  UserTable    $user    Viewed at User
	 * @param  TabTable     $tab     Current Tab
	 * @param  PluginTable  $plugin  Current Plugin
	 * @return string                HTML
	 */
	static public function getSubscriptions( $viewer, $user, $tab, $plugin )
	{
		global $_CB_framework, $_CB_database;

		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper' ) ) {
				return CBTxt::T( 'Kunena not installed, enabled, or failed to load.' );
			}
		} elseif ( ! class_exists( 'KunenaForumTopicHelper' ) ) {
			return CBTxt::T( 'Kunena not installed, enabled, or failed to load.' );
		}

		cbimport( 'cb.pagination' );
		cbforumsClass::getTemplate( 'tab_subs' );

		$limit					=	(int) $tab->params->get( 'tab_subs_limit', 15 );
		$orderBy				=	$tab->params->get( 'tab_subs_orderby', 'last_post_time,desc' );
		$limitstart				=	(int) $_CB_framework->getUserStateFromRequest( 'tab_subs_limitstart{com_comprofiler}', 'tab_subs_limitstart', 0 );
		$filterSearch			=	$_CB_framework->getUserStateFromRequest( 'tab_subs_search{com_comprofiler}', 'tab_subs_search', '' );
		$where					=	array();

		if ( isset( $filterSearch ) && ( $filterSearch != '' ) ) {
			$where[]			=	'( tt.' . $_CB_database->NameQuote( 'subject' ) . ' LIKE ' . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $filterSearch, true ) . '%', false )
								.	' OR tt.' . $_CB_database->NameQuote( 'first_post_message' ) . ' LIKE ' . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $filterSearch, true ) . '%', false ) . ' )';
		}

		$searching				=	( count( $where ) ? true : false );

		if ( ! $orderBy ) {
			$orderBy			=	'last_post_time,desc';
		}

		$orderBy				=	explode( ',', $orderBy );

		$params					=	array(	'user' => (int) $user->id,
											'subscribed' => true,
											'starttime' => -1,
											'where' => ( count( $where ) ? ' AND ' . implode( ' AND ', $where ) : null ),
											'orderby' => 'tt.' . $_CB_database->NameQuote( $orderBy[0] ) . ( $orderBy[1] == 'asc' ? " ASC" : ( $orderBy[1] == 'desc' ? " DESC" : null ) )
										);

		if ( cbforumsClass::getModel()->version >= 6 ) {
			$topics				=	KunenaTopicHelper::getLatestTopics( false, 0, 0, $params );
		} else {
			$topics				=	KunenaForumTopicHelper::getLatestTopics( false, 0, 0, $params );
		}

		$total					=	array_shift( $topics );

		if ( ( ! $total ) && ( ! $searching ) && ( ! Application::Config()->get( 'showEmptyTabs', true, GetterInterface::BOOLEAN ) ) ) {
			return null;
		}

		if ( $total <= $limitstart ) {
			$limitstart			=	0;
		}

		$pageNav				=	new cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( 'tab_subs_' );
		$pageNav->setStaticLimit( true );
		$pageNav->setBaseURL( $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ), 'html', 0, array( 'tab_subs_search' => ( $searching ? $filterSearch : null ) ) ) );

		if ( $tab->params->get( 'tab_subs_paging', 1 ) ) {
			if ( cbforumsClass::getModel()->version >= 6 ) {
				$topics			=	KunenaTopicHelper::getLatestTopics( false, (int) $pageNav->limitstart, (int) $pageNav->limit, $params );
			} else {
				$topics			=	KunenaForumTopicHelper::getLatestTopics( false, (int) $pageNav->limitstart, (int) $pageNav->limit, $params );
			}

			$topics				=	array_pop( $topics );
		} else {
			$topics				=	array_pop( $topics );
		}

		$rows					=	array();

		/** @var KunenaTopic[]|KunenaForumTopic[] $topics */
		if ( $topics ) foreach ( $topics as $topic ) {
			$row				=	new stdClass;
			$row->id			=	$topic->id;
			$row->subject		=	$topic->subject;
			$row->message		=	$topic->first_post_message;
			$row->date			=	$topic->first_post_time;
			$row->url			=	$topic->getUrl();
			$row->category_id	=	$topic->getCategory()->id;
			$row->category_name	=	$topic->getCategory()->name;
			$row->category_url	=	$topic->getCategory()->getUrl();

			$rows[]				=	$row;
		}

		$input					=	array();
		$input['search']		=	'<input type="text" name="tab_subs_search" value="' . htmlspecialchars( $filterSearch ) . '" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Subscriptions...' ) ) . '" class="form-control" />';

		return HTML_cbforumsTabSubs::showSubscriptions( $rows, $pageNav, $searching, $input, $viewer, $user, $tab, $plugin );
	}

	/**
	 * View Forum Category Subscriptions
	 *
	 * @param  UserTable    $viewer  Viewing User
	 * @param  UserTable    $user    Viewed at User
	 * @param  TabTable     $tab     Current Tab
	 * @param  PluginTable  $plugin  Current Plugin
	 * @return string|boolean        HTML or FALSE
	 */
	static public function getCategorySubscriptions( $viewer, $user, $tab, $plugin )
	{
		global $_CB_framework, $_CB_database;

		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper' ) ) {
				return CBTxt::T( 'Kunena not installed, enabled, or failed to load.' );
			}
		} elseif ( ! class_exists( 'KunenaForumCategoryHelper' ) ) {
			return CBTxt::T( 'Kunena not installed, enabled, or failed to load.' );
		}

		cbimport( 'cb.pagination' );
		cbforumsClass::getTemplate( 'tab_subs_cats' );

		$limit					=	(int) $tab->params->get( 'tab_subs_limit', 15 );
		$limitstart				=	(int) $_CB_framework->getUserStateFromRequest( 'tab_subs_cats_limitstart{com_comprofiler}', 'tab_subs_cats_limitstart', 0 );
		$filterSearch			=	$_CB_framework->getUserStateFromRequest( 'tab_subs_cats_search{com_comprofiler}', 'tab_subs_cats_search', '' );
		$where					=	array();

		if ( isset( $filterSearch ) && ( $filterSearch != '' ) ) {
			$where[]			=	'( c.' . $_CB_database->NameQuote( 'name' ) . ' LIKE ' . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $filterSearch, true ) . '%', false ) . ' )';
		}

		$searching				=	( count( $where ) ? true : false );

		$params					=	array( 'where' => ( count( $where ) ? ' AND ' . implode( ' AND ', $where ) : null ) );

		if ( cbforumsClass::getModel()->version >= 6 ) {
			$categories			=	KunenaCategoryHelper::getLatestSubscriptions( (int) $user->id, 0, 0, $params );
		} else {
			$categories			=	KunenaForumCategoryHelper::getLatestSubscriptions( (int) $user->id, 0, 0, $params );
		}

		$total					=	array_shift( $categories );

		if ( ( ! $total ) && ( ! $searching ) && ( ! Application::Config()->get( 'showEmptyTabs', true, GetterInterface::BOOLEAN ) ) ) {
			return null;
		}

		if ( $total <= $limitstart ) {
			$limitstart			=	0;
		}

		$pageNav				=	new cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( 'tab_subs_cats_' );
		$pageNav->setStaticLimit( true );
		$pageNav->setBaseURL( $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ), 'html', 0, array( 'tab_subs_cats_search' => ( $searching ? $filterSearch : null ) ) ) );

		if ( $tab->params->get( 'tab_subs_paging', 1 ) ) {
			if ( cbforumsClass::getModel()->version >= 6 ) {
				$categories		=	KunenaCategoryHelper::getLatestSubscriptions( (int) $user->id, (int) $pageNav->limitstart, (int) $pageNav->limit, $params );
			} else {
				$categories		=	KunenaForumCategoryHelper::getLatestSubscriptions( (int) $user->id, (int) $pageNav->limitstart, (int) $pageNav->limit, $params );
			}

			$categories			=	array_pop( $categories );
		} else {
			$categories			=	array_pop( $categories );
		}

		$rows					=	array();

		/** @var KunenaCategory[]|KunenaForumCategory[] $categories */
		if ( $categories ) foreach ( $categories as $category ) {
			$row				=	new stdClass;
			$row->id			=	$category->id;
			$row->category_id	=	$category->id;
			$row->category_name	=	$category->name;
			$row->category_url	=	$category->getUrl();

			$rows[]				=	$row;
		}

		$input					=	array();
		$input['search']		=	'<input type="text" name="tab_subs_cats_search" value="' . htmlspecialchars( $filterSearch ) . '" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Category Subscriptions...' ) ) . '" class="form-control" />';

		if ( ( ! $rows ) && ( ! $searching ) ) {
			return false;
		} else {
			return HTML_cbforumsTabCatSubs::showCategorySubscriptions( $rows, $pageNav, $searching, $input, $viewer, $user, $tab, $plugin );
		}
	}

	/**
	 * Un-favorite a post
	 *
	 * @param  string|int   $postid  Forum Post id
	 * @param  UserTable    $user    Viewed at User
	 * @param  PluginTable  $plugin  Current Plugin
	 * @return boolean               Result
	 */
	static public function unFavorite( $postid, $user, /** @noinspection PhpUnusedParameterInspection */ $plugin )
	{
		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper' ) ) {
				return false;
			}
		} elseif ( ! class_exists( 'KunenaForumTopicHelper' ) ) {
			return false;
		}

		if ( $postid == 'all' ) {
			if ( cbforumsClass::getModel()->version >= 6 ) {
				$topics	=	KunenaTopicHelper::getLatestTopics( false, 0, 0, array( 'user' => (int) $user->id, 'favorited' => true ) );
			} else {
				$topics	=	KunenaForumTopicHelper::getLatestTopics( false, 0, 0, array( 'user' => (int) $user->id, 'favorited' => true ) );
			}

			$ids	=	array_keys( array_pop( $topics ) );
		} else {
			$ids	=	array( (int) $postid );
		}

		if ( ! $ids ) {
			return false;
		}

		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! KunenaTopicHelper::favorite( $ids, 0, (int) $user->id ) ) {
				return false;
			}
		} elseif ( ! KunenaForumTopicHelper::favorite( $ids, 0, (int) $user->id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Un-subscribe from a topic
	 *
	 * @param  string|int   $postid  Forum Post id
	 * @param  UserTable    $user    Viewed at User
	 * @param  PluginTable  $plugin  Current Plugin
	 * @return boolean               Result
	 */
	static public function unSubscribe( $postid, $user, /** @noinspection PhpUnusedParameterInspection */ $plugin )
	{
		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper' ) ) {
				return false;
			}
		} elseif ( ! class_exists( 'KunenaForumTopicHelper' ) ) {
			return false;
		}

		if ( $postid == 'all' ) {
			if ( cbforumsClass::getModel()->version >= 6 ) {
				$topics	=	KunenaTopicHelper::getLatestTopics( false, 0, 0, array( 'user' => (int) $user->id, 'subscribed' => true ) );
			} else {
				$topics	=	KunenaForumTopicHelper::getLatestTopics( false, 0, 0, array( 'user' => (int) $user->id, 'subscribed' => true ) );
			}

			$ids	=	array_keys( array_pop( $topics ) );
		} else {
			$ids	=	array( (int) $postid );
		}

		if ( ! $ids ) {
			return false;
		}

		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! KunenaTopicHelper::subscribe( $ids, 0, (int) $user->id ) ) {
				return false;
			}
		} elseif ( ! KunenaForumTopicHelper::subscribe( $ids, 0, (int) $user->id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Un-subscribe from a category
	 *
	 * @param  string|int   $catid   Forum Category id
	 * @param  UserTable    $user    Viewed at User
	 * @param  PluginTable  $plugin  Current Plugin
	 * @return boolean               Result
	 */
	static public function unSubscribeCategory( $catid, $user, /** @noinspection PhpUnusedParameterInspection */ $plugin )
	{
		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper' ) ) {
				return false;
			}
		} elseif ( ! class_exists( 'KunenaForumCategoryHelper' ) ) {
			return false;
		}

		if ( $catid == 'all' ) {
			if ( cbforumsClass::getModel()->version >= 6 ) {
				$categories	=	KunenaCategoryHelper::getLatestSubscriptions( (int) $user->id, 0, 0 );
			} else {
				$categories	=	KunenaForumCategoryHelper::getLatestSubscriptions( (int) $user->id, 0, 0 );
			}

			$ids		=	array_keys( array_pop( $categories ) );
		} else {
			$ids		=	array( (int) $catid );
		}

		if ( ! $ids ) {
			return false;
		}

		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! KunenaCategoryHelper::subscribe( $ids, 0, (int) $user->id ) ) {
				return false;
			}
		} elseif ( ! KunenaForumCategoryHelper::subscribe( $ids, 0, (int) $user->id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get item id for forum
	 *
	 * @param  boolean      $htmlspecialchars
	 * @return string|null
	 */
	static public function getItemid( $htmlspecialchars = false )
	{
		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\Route\KunenaRoute' ) ) {
				return null;
			}
		} elseif ( ! class_exists( 'KunenaRoute' ) ) {
			return null;
		}

		static $Itemid	=	null;

		if ( ! isset( $Itemid ) ) {
			if ( cbforumsClass::getModel()->version >= 6 ) {
				$Itemid	=	Kunena6Route::getItemID();
			} else {
				$Itemid	=	KunenaRoute::getItemID();
			}
		}

		if ( $Itemid ) {
			if ( is_bool( $htmlspecialchars ) ) {
				return ( $htmlspecialchars ? '&amp;' : '&' ) . 'Itemid=' . (int) $Itemid;
			} else {
				return $Itemid;
			}
		}

		return null;
	}

	/**
	 * Gets an URL to a post or a category
	 *
	 * @param  int|null  $forum  Forum category
	 * @param  int|null  $post   Forum post
	 * @return null|string       URL
	 */
	static public function getForumURL( $forum = null, $post = null )
	{
		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ( ! class_exists( '\Kunena\Forum\Libraries\Forum\Topic\KunenaTopic' ) ) || ( ! class_exists( '\Kunena\Forum\Libraries\Forum\Message\KunenaMessage' ) ) ) {
				return null;
			}
		} elseif ( ( ! class_exists( 'KunenaForumTopic' ) ) || ( ! class_exists( 'KunenaForumMessage' ) ) ) {
			return null;
		}

		if ( $post ) {
			if ( cbforumsClass::getModel()->version >= 6 ) {
				$url		=	KunenaTopic::getInstance( (int) $post )->getUrl();

				if ( ! $url ) {
					$url	=	KunenaMessage::getInstance( (int) $post )->getUrl();
				}
			} else {
				$url		=	KunenaForumTopic::getInstance( (int) $post )->getUrl();

				if ( ! $url ) {
					$url	=	KunenaForumMessage::getInstance( (int) $post )->getUrl();
				}
			}
		} else {
			$url		=	cbforumsModel::getCategory( (int) $forum )->getUrl();
		}

		return $url;
	}

	/**
	 * @param  null|int  $user_id
	 * @return array
	 */
	static public function getAllowedCategories( $user_id )
	{
		global $_CB_framework;

		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\Access\KunenaAccess' ) ) {
				return null;
			}
		} elseif ( ! class_exists( 'KunenaAccess' ) ) {
			return array();
		}

		if ( $user_id === null ) {
			$user_id			=	$_CB_framework->myId();
		}

		$cache					=	array();

		if ( ! isset( $cache[$user_id] ) ) {
			if ( cbforumsClass::getModel()->version >= 6 ) {
				$cache[$user_id]	=	Kunena6Access::getInstance()->getAllowedCategories( (int) $user_id );
			} else {
				$cache[$user_id]	=	KunenaAccess::getInstance()->getAllowedCategories( (int) $user_id );
			}
		}

		return $cache[$user_id];
	}

	/**
	 * @param  int  $catid
	 * @return KunenaCategory|KunenaForumCategory|null
	 */
	static public function getCategory( $catid )
	{
		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper' ) ) {
				return null;
			}
		} elseif ( ! class_exists( 'KunenaForumCategoryHelper' ) ) {
			return null;
		}

		static $cache		=	array();

		if ( ! isset( $cache[$catid] ) ) {
			if ( cbforumsClass::getModel()->version >= 6 ) {
				$cache[$catid]	=	KunenaCategoryHelper::get( (int) $catid );
			} else {
				$cache[$catid]	=	KunenaForumCategoryHelper::get( (int) $catid );
			}
		}

		return $cache[$catid];
	}

	/**
	 * @return array
	 */
	static public function getBoards( )
	{
		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper' ) ) {
				return array();
			}
		} elseif ( ! class_exists( 'KunenaForumCategoryHelper' ) ) {
			return array();
		}

		if ( cbforumsClass::getModel()->version >= 6 ) {
			$rows			=	KunenaCategoryHelper::getChildren( 0, 10 );
		} else {
			$rows			=	KunenaForumCategoryHelper::getChildren( 0, 10 );
		}

		$categories			=	array();

		if ( $rows ) foreach ( $rows as $row ) {
			$categories[]	=	moscomprofilerHTML::makeOption( $row->id, str_repeat( '- ', $row->level + 1  ) . ' ' . $row->name );
		}

		return $categories;
	}

	/**
	 * @param  UserTable  $user
	 * @return int
	 */
	static public function getUserPosts( $user )
	{
		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\User\KunenaUser' ) ) {
				return 0;
			}
		} elseif ( ! class_exists( 'KunenaUser' ) ) {
			return 0;
		}

		$value					=	0;

		if ( $user->get( 'id' ) ) {
			if ( cbforumsClass::getModel()->version >= 6 ) {
				$forumUser		=	Kunena6User::getInstance( (int) $user->get( 'id' ) );
			} else {
				$forumUser		=	KunenaUser::getInstance( (int) $user->get( 'id' ) );
			}

			if ( $forumUser ) {
				$value			=	(int) $forumUser->get( 'posts' );
			}
		}

		return $value;
	}

	/**
	 * @param  UserTable  $user
	 * @return int
	 */
	static public function getUserKarma( $user )
	{
		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\User\KunenaUser' ) ) {
				return 0;
			}
		} elseif ( ! class_exists( 'KunenaUser' ) ) {
			return 0;
		}

		$value					=	0;

		if ( $user->get( 'id' ) ) {
			if ( cbforumsClass::getModel()->version >= 6 ) {
				$forumUser		=	Kunena6User::getInstance( (int) $user->get( 'id' ) );
			} else {
				$forumUser		=	KunenaUser::getInstance( (int) $user->get( 'id' ) );
			}

			if ( $forumUser ) {
				$value			=	(int) $forumUser->get( 'karma' );
			}
		}

		return $value;
	}

	/**
	 * @param  UserTable  $user
	 * @param  bool       $showTitle
	 * @param  bool       $showImage
	 * @return string|null
	 */
	static public function getUserRank( $user, $showTitle = true, $showImage = true )
	{
		global $_CB_framework;

		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\User\KunenaUser' ) ) {
				return null;
			}
		} elseif ( ! class_exists( 'KunenaUser' ) ) {
			return null;
		}

		$value					=	null;

		if ( $user->get( 'id' ) ) {
			if ( cbforumsClass::getModel()->version >= 6 ) {
				$forumUser		=	Kunena6User::getInstance( (int) $user->get( 'id' ) );
			} else {
				$forumUser		=	KunenaUser::getInstance( (int) $user->get( 'id' ) );
			}

			if ( $forumUser ) {
				if ( cbforumsClass::getModel()->version >= 5 ) {
					if ( cbforumsClass::getModel()->version >= 6 ) {
						Kunena6Factory::loadLanguage(); // Be sure Kunena frontend language files are loaded
					} else {
						KunenaFactory::loadLanguage(); // Be sure Kunena frontend language files are loaded
					}

					$title		=	$forumUser->getRank( 0, 'title' );
					$image		=	$forumUser->getRank( 0, 'image' );
				} else {
					$userRank	=	$forumUser->getRank();

					if ( ! $userRank ) {
						return null;
					}

					if ( ! is_object( $userRank ) ) {
						return $userRank;
					}

					$title		=	$userRank->rank_title;
					$image		=	null;

					if ( class_exists( 'KunenaTemplate' ) ) {
						$image		=	'<img src="' . $_CB_framework->getCfg( 'live_site' ) . '/' . KunenaTemplate::getInstance()->getRankPath( $userRank->rank_image ) . '" alt="' . htmlspecialchars( $title ) . '" />';
					}
				}

				if ( $showTitle && $title ) {
					$value		.=	'<div>' . $title . '</div>';
				}

				if ( $showImage && $image ) {
					$value		.=	'<div>' . $image . '</div>';
				}

				if ( ! $value ) {
					$value		=	$forumUser->rank;
				}
			}
		}

		return $value;
	}

	/**
	 * @param  UserTable  $user
	 * @return int
	 */
	static public function getUserThankYous( $user )
	{
		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\User\KunenaUser' ) ) {
				return 0;
			}
		} elseif ( ! class_exists( 'KunenaUser' ) ) {
			return 0;
		}

		$value					=	0;

		if ( $user->get( 'id' ) ) {
			if ( cbforumsClass::getModel()->version >= 6 ) {
				$forumUser		=	Kunena6User::getInstance( (int) $user->get( 'id' ) );
			} else {
				$forumUser		=	KunenaUser::getInstance( (int) $user->get( 'id' ) );
			}

			if ( $forumUser ) {
				$value			=	(int) $forumUser->get( 'thankyou' );
			}
		}

		return $value;
	}

	/**
	 * @param  UserTable  $user
	 */
	public function syncUser( $user )
	{
		global $_CB_framework;

		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\User\KunenaUser' ) ) {
				return;
			}
		} elseif ( ! class_exists( 'KunenaUser' ) ) {
			return;
		}

		if ( cbforumsClass::getModel()->version >= 6 ) {
			$exists						=	Kunena6User::getInstance( (int) $user->get( 'id' ) );
		} else {
			$exists						=	KunenaUser::getInstance( (int) $user->get( 'id' ) );
		}

		if ( $exists ) {
			$plugin						=	cbforumsClass::getPlugin();
			$updated					=	false;
			$fields						=	array(	'ordering', 'viewtype', 'signature', 'personaltext', 'gender',
													'birthdate', 'location', 'icq', 'yim', 'youtube', 'ok', 'microsoft',
													'telegram', 'vk', 'skype', 'twitter', 'facebook', 'google', 'myspace',
													'linkedin', 'linkedin_company', 'delicious', 'friendfeed', 'digg', 'instagram', 'qq', 'qzone',
													'whatsapp', 'weibo', 'wechat', 'apple', 'blogspot', 'flickr', 'bebo', 'website',
													'email', 'online', 'subscribe', 'listlimit'
												);

			foreach ( $fields as $field ) {
				$cbField				=	$plugin->params->get( 'k20_' . $field, null );

				if ( $cbField && isset( $user->$cbField ) ) {
					$value				=	$user->get( $cbField );

					// Convert legacy values for B/C:
					switch ( $value ) {
						case '_UE_ORDERING_OLDEST':
						case 'UE_ORDERING_OLDEST':
						case 'Oldest':
							$value		=	0;
							break;
						case '_UE_ORDERING_LATEST':
						case 'UE_ORDERING_LATEST':
						case 'Latest':
							$value		=	1;
							break;
						case '_UE_VIEWTYPE_FLAT':
						case 'UE_VIEWTYPE_FLAT':
						case 'Flat':
							$value		=	'flat';
							break;
						case '_UE_VIEWTYPE_THREADED':
						case 'UE_VIEWTYPE_THREADED':
						case 'Threaded':
							$value		=	'threaded';
							break;
						case '_UE_VIEWTYPE_INDENTED':
						case 'UE_VIEWTYPE_INDENTED':
						case 'Indented':
							$value		=	'indented';
							break;
						case '_UE_MALE':
						case 'UE_MALE':
						case 'Male':
							$value		=	1;
							break;
						case '_UE_FEMALE':
						case 'UE_FEMALE':
						case 'Female':
							$value		=	2;
							break;
						case '_UE_HIDE':
						case 'UE_HIDE':
						case '_UE_NO':
						case 'UE_NO':
						case '_UE_UNKNOWN':
						case 'UE_UNKNOWN':
						case 'Hide':
						case 'No':
						case 'Unknown':
							$value		=	0;
							break;
						case '_UE_SHOW':
						case 'UE_SHOW':
						case '_UE_YES':
						case 'UE_YES':
						case 'Show':
						case 'Yes':
							$value		=	1;
							break;
					}

					// Convert the field name and/or value to Kunena compatible:
					switch ( $field ) {
						case 'birthdate':
							if ( $value && ( ! in_array( $value, array( '0000-00-00', '0000-00-00 00:00:00' ) ) ) ) {
								$value	=	$_CB_framework->getUTCDate( 'Y-m-d', $value );
							} else {
								$value	=	'0000-00-00';
							}
							break;
						case 'viewtype':
							$field		=	'view';
							break;
						case 'email':
							$field		=	'hideEmail';
							break;
						case 'online':
							$field		=	'showOnline';
							break;
						case 'personaltext':
							$field		=	'personalText';
							break;
						case 'subscribe':
							$field		=	'canSubscribe';
							break;
						case 'listlimit':
							$field		=	'userListtime';
							break;
						case 'qq':
							$field		=	'qqsocial';
							break;
					}

					// If the field is website then set both values in Kunena as needed; otherwise do normal set:
					if ( $field == 'website' ) {
						$web			=	explode( '|*|', $value );

						if ( count( $web ) > 1 ) {
							$webName	=	( isset( $web[0] ) ? $web[0] : null );
							$webUrl		=	( isset( $web[1] ) ? $web[1] : null );
						} else {
							$webName	=	null;
							$webUrl		=	( isset( $web[0] ) ? $web[0] : null );
						}

						if ( $webName != $exists->get( 'websitename' ) ) {
							$exists->set( 'websitename', $webName );

							$updated	=	true;
						}

						if ( $webUrl != $exists->get( 'websiteurl' ) ) {
							$exists->set( 'websiteurl', $webUrl );

							$updated	=	true;
						}
					} else {
						if ( $value != $exists->get( $field ) ) {
							$exists->set( $field, $value );

							$updated	=	true;
						}
					}
				}
			}

			if ( $updated ) {
				if ( ! $exists->save() ) {
					trigger_error( CBTxt::T( 'FORUMS_SYNC_USER_ERROR', '[element] - syncUser SQL Error: [error]', array( '[element]' => $plugin->element, '[error]' => $exists->getError() ) ), E_USER_WARNING );
				}
			}
		}
	}

	/**
	 * @param UserTable $user
	 * @param int       $state 0: Unbanned, 1: Banned, 2: Pending
	 * @param string    $reason
	 */
	public function banUser( $user, $state, $reason )
	{
		if ( cbforumsClass::getModel()->version >= 6 ) {
			if ( ! class_exists( '\Kunena\Forum\Libraries\User\KunenaBan' ) ) {
				return;
			}
		} elseif ( ! class_exists( 'KunenaUserBan' ) ) {
			return;
		}

		if ( cbforumsClass::getModel()->version >= 6 ) {
			$banInfo	=	KunenaBan::getInstanceByUserid( $user->get( 'id', 0, GetterInterface::INT ), true );
		} else {
			$banInfo	=	KunenaUserBan::getInstanceByUserid( $user->get( 'id', 0, GetterInterface::INT ), true );
		}

		if ( ! $banInfo->canBan() ) {
			return;
		}

		if ( $state == 2 ) {
			if ( ! $banInfo->exists() ) {
				$banInfo->ban( $user->get( 'id', 0, GetterInterface::INT ), null, 0, null, '', '', $reason );
			} else {
				$banInfo->addComment( $reason );
			}
		} elseif ( $state == 1 ) {
			$banInfo->ban( $user->get( 'id', 0, GetterInterface::INT ), null, 0, null, $reason, '', $reason );
		} elseif ( $state == 0 ) {
			$banInfo->unBan( $reason );
		}

		$banInfo->save();
	}

	/**
	 * @param  string  $component
	 * @param  object  $view
	 * @param  int     $userId
	 * @param  array   $params
	 * @return string|null
	 */
	public function getSidebar( /** @noinspection PhpUnusedParameterInspection */ $component, $view, $userId, $params )
	{
		if ( isset( $params['userprofile'] ) ) {
			$cbUser			=	CBuser::getInstance( (int) $userId, false );
			$user			=	$cbUser->getUserData();
			$plugin			=	cbforumsClass::getPlugin();
			$userprofile	=	$params['userprofile'];

			if ( $user->id && $userprofile->userid ) {
				$display	=	$plugin->params->get( 'k20_sidebar_reg', null );
			} elseif ( ( ! $user->id ) && $userprofile->userid ) {
				$display	=	$plugin->params->get( 'k20_sidebar_del', null );
			} elseif ( ( ! $user->id ) && ( ! $userprofile->userid ) ) {
				$display	=	$plugin->params->get( 'k20_sidebar_anon', null );
			} else {
				$display	=	null;
			}

			if ( $display ) {
				$extras		=	array(	'karmaplus' => ( isset( $view->userkarma_plus ) ? $view->userkarma_plus : null ),
										'karmaminus' => ( isset( $view->userkarma_minus ) ? $view->userkarma_minus : null ),
										'karmatitle' => ( isset( $view->userkarma_title ) ? $view->userkarma_title : null ),
										'karma' => ( isset( $view->userkarma ) ? $view->userkarma : null ),
										'rankimage' => ( isset( $view->userrankimage ) ? $view->userrankimage : null ),
										'ranktitle' => ( isset( $view->userranktitle ) ? $view->userranktitle : null ),
										'posts' => ( isset( $view->userposts ) ? $view->userposts : null ),
										'thankyou' => ( isset( $view->userthankyou ) ? $view->userthankyou : null ),
										'points' => ( isset( $view->userpoints ) ? $view->userpoints : null ),
										'medals' => ( isset( $view->usermedals ) ? $view->usermedals : null ),
										'personaltext' => ( isset( $view->personalText ) ? $view->personalText : null )
									);

				return $cbUser->replaceUserVars( $display, false, true, $extras );
			}
		}
		return null;
	}
}
