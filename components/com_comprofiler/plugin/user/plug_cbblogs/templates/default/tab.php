<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Database\Table\OrderedTable;
use CBLib\Language\CBTxt;
use CB\Database\Table\PluginTable;
use CB\Database\Table\TabTable;
use CB\Database\Table\UserTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * Class HTML_cbblogsTab
 * Template for CB Blogs Tab view
 */
class HTML_cbblogsTab
{
	/**
	 * Renders the Blogs tab
	 *
	 * @param  OrderedTable[]  $rows       Blogs to render
	 * @param  cbPageNav       $pageNav    Pagination
	 * @param  boolean         $searching  Currently searching
	 * @param  string[]        $input      HTML of input elements
	 * @param  UserTable       $viewer     Viewing user
	 * @param  UserTable       $user       Viewed user
	 * @param  stdClass        $model      The model reference
	 * @param  TabTable        $tab        Current Tab
	 * @param  PluginTable     $plugin     Current Plugin
	 * @return string                      HTML
	 */
	static function showBlogTab( $rows, $pageNav, $searching, $input, $viewer, $user, /** @noinspection PhpUnusedParameterInspection */ $model, $tab, $plugin )
	{
		global $_CB_framework;

		$blogLimit					=	(int) $plugin->params->get( 'blog_limit', null );
		$tabPaging					=	$tab->params->get( 'tab_paging', 1 );
		$canSearch					=	( $tab->params->get( 'tab_search', 1 ) && ( $searching || $pageNav->total ) );
		$canCreate					=	false;
		$profileOwner				=	( $viewer->get( 'id' ) == $user->get( 'id' ) );
		$cbModerator				=	Application::User( (int) $viewer->get( 'id' ) )->isGlobalModerator();
		$canPublish					=	( $cbModerator || ( $profileOwner && ( ! $plugin->params->get( 'blog_approval', 0 ) ) ) );

		if ( $profileOwner ) {
			if ( $cbModerator ) {
				$canCreate			=	true;
			} elseif ( $user->get( 'id' ) && Application::User( (int) $viewer->get( 'id' ) )->canViewAccessLevel( (int) $plugin->params->get( 'blog_create_access', 2 ) ) ) {
				if ( ( ! $blogLimit ) || ( $blogLimit && ( $pageNav->total < $blogLimit ) ) ) {
					$canCreate		=	true;
				}
			}
		}

		$return						=	'<div class="blogsTab">';

		if ( $canCreate || $canSearch ) {
			$return					.=		'<div class="row no-gutters mb-3 blogsHeader">';

			if ( $canCreate ) {
				$return				.=			'<div class="col-12 ' . ( $canSearch ? 'col-sm-6 mb-2 mb-sm-0' : null ) . '">'
									.				'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'blogs', 'func' => 'new' ) ) . '" class="btn btn-success btn-sm-block blogsButton blogsButtonNew"><span class="fa fa-plus-circle"></span> ' . CBTxt::T( 'Create New Post' ) . '</a>'
									.			'</div>';
			}

			if ( $canSearch ) {
				$return				.=			'<div class="col-12 ' . ( ! $canCreate ? 'offset-sm-6 ' : null ) . 'col-sm-6 text-sm-right">'
									.				'<form action="' . $_CB_framework->userProfileUrl( $user->get( 'id' ), true, $tab->tabid ) . '" method="post" name="blogSearchForm" class="m-0 blogSearchForm">'
									.					'<div class="input-group">'
									.						$input['search']
									.						'<div class="input-group-append">'
									.							'<button type="submit" class="btn btn-light border" aria-label="' . htmlspecialchars( CBTxt::T( 'Search' ) ) . '"><span class="fa fa-search"></span></button>'
									.						'</div>'
									.					'</div>'
									.				'</form>'
									.			'</div>';
			}

			$return					.=		'</div>';
		}

		$return						.=		'<div class="table-responsive blogsContainer">'
									.			'<table class="table table-hover mb-0">'
									.				'<thead>'
									.					'<tr>'
									.						'<th style="width: 50%;">' . CBTxt::T( 'Title' ) . '</th>'
									.						'<th style="width: 25%;" class="d-none d-sm-table-cell">' . CBTxt::T( 'Category' ) . '</th>'
									.						'<th style="width: 25%;" class="d-none d-sm-table-cell">' . CBTxt::T( 'Created' ) . '</th>'
									.						( $cbModerator || $profileOwner || $canPublish ? '<th style="width: 1%;"></th>' : null )
									.					'</tr>'
									.				'</thead>'
									.				'<tbody>';

		if ( $rows ) foreach ( $rows as $row ) {
			$return					.=					'<tr>'
									.						'<td style="width: 50%;" class="align-middle">' . ( $row->getPublished() ? '<a href="' . cbblogsModel::getUrl( $row, true, 'article' ) . '">' . $row->get( 'title' ) . '</a>' : $row->get( 'title' ) ) . '</td>'
									.						'<td style="width: 25%;" class="align-middle d-none d-sm-table-cell">' . ( $row->get( 'category_published' ) ? '<a href="' . cbblogsModel::getUrl( $row, true, 'category' ) . '">' . $row->get( 'category' ) . '</a>' : $row->get( 'category' ) ) . '</td>'
									.						'<td style="width: 25%;" class="align-middle d-none d-sm-table-cell">' . cbFormatDate( $row->get( 'created' ), true, false ) . '</td>';

			if ( $cbModerator || $profileOwner || $canPublish ) {
				$menuItems			=	'<ul class="list-unstyled dropdown-menu d-block position-relative m-0 blogsMenuItems">';

				if ( $cbModerator || $profileOwner ) {
					$menuItems		.=		'<li class="blogsMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'blogs', 'func' => 'edit', 'id' => (int) $row->get( 'id' ) ) ) . '" class="dropdown-item"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';
				}

				if ( $canPublish ) {
					if ( $row->get( 'published' ) ) {
						$menuItems	.=		'<li class="blogsMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to unpublish this blog?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'blogs', 'func' => 'unpublish', 'id' => (int) $row->get( 'id' ) ) ) ) . '\'; })" class="dropdown-item"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'Unpublish' ) . '</a></li>';
					} else {
						$menuItems	.=		'<li class="blogsMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'blogs', 'func' => 'publish', 'id' => (int) $row->get( 'id' ) ) ) . '" class="dropdown-item"><span class="fa fa-check"></span> ' . CBTxt::T( 'Publish' ) . '</a></li>';
					}
				}

				if ( $cbModerator || $profileOwner ) {
					$menuItems		.=		'<li class="blogsMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this blog?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'blogs', 'func' => 'delete', 'id' => (int) $row->get( 'id' ) ) ) ) . '\'; })" class="dropdown-item"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
				}

				$menuItems			.=	'</ul>';

				$return				.=						'<td style="width: 1%;" class="p-0 align-middle">'
									.							cbTooltip( null, $menuItems, null, 'auto', null, '<span class="pt-1 pb-1 pl-3 pr-3 text-large fa fa-ellipsis-v"></span>', 'javascript:void(0);', 'class="text-body cbDropdownMenu blogsMenu" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle" data-cbtooltip-open-classes="active"' )
									.						'</td>';
			}

			$return					.=					'</tr>';
		} else {
			$return					.=					'<tr>'
									.						'<td colspan="4">';

			if ( $searching ) {
				$return				.=							CBTxt::T( 'No blog search results found.' );
			} else {
				if ( $viewer->id == $user->id ) {
					$return			.=							CBTxt::T( 'You have no blogs.' );
				} else {
					$return			.=							CBTxt::T( 'This user has no blogs.' );
				}
			}

			$return					.=						'</td>'
									.					'</tr>';
		}

		$return						.=				'</tbody>';

		if ( $tabPaging && ( $pageNav->total > $pageNav->limit ) ) {
			$return					.=				'<tfoot>'
									.					'<tr>'
									.						'<td colspan="4" class="text-center">'
									.							$pageNav->getListLinks()
									.						'</td>'
									.					'</tr>'
									.				'</tfoot>';
		}

		$return						.=			'</table>'
									.		'</div>'
									.	'</div>';

		return $return;
	}
}
