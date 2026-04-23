<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Database\Table\TabTable;
use CB\Database\Table\PluginTable;
use CB\Database\Table\UserTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * Class HTML_cbforumsTabPosts
 * CB Forum Posts Tab Template
 */
class HTML_cbforumsTabPosts
{
	/**
	 * Shows Forum Posts
	 *
	 * @param  stdClass[]   $rows       Rows to show
	 * @param  cbPageNav    $pageNav    Page Navigation
	 * @param  boolean      $searching  Are we searching currently ?
	 * @param  string[]     $input      Inputs to show
	 * @param  UserTable    $viewer     Viewing User
	 * @param  UserTable    $user       Viewed at User
	 * @param  TabTable     $tab        Current Tab
	 * @param  PluginTable  $plugin     Current Plugin
	 * @return string
	 */
	static public function showPosts( $rows, $pageNav, $searching, $input, $viewer, $user, $tab, /** @noinspection PhpUnusedParameterInspection */ $plugin )
	{
		global $_CB_framework;

		$tabPaging			=	$tab->params->get( 'tab_posts_paging', 1 );
		$canSearch			=	( $tab->params->get( 'tab_posts_search', 1 ) && ( $searching || $pageNav->total ) );

		$return				=	'<div class="forumsPostsTab tab-content">';

		if ( $canSearch ) {
			$return			.=		'<div class="row no-gutters mb-3 forumsHeader">'
							.			'<div class="col-12 offset-sm-6 col-sm-6 text-sm-right">'
							.				'<form action="' . $_CB_framework->userProfileUrl( $user->id, true, $tab->tabid ) . '" method="post" name="forumPostsSearchForm" class="m-0 forumPostsSearchForm">'
							.					'<div class="input-group">'
							.						$input['search']
							.						'<div class="input-group-append">'
							.							'<button type="submit" class="btn btn-light border" aria-label="' . htmlspecialchars( CBTxt::T( 'Search' ) ) . '"><span class="fa fa-search"></span></button>'
							.						'</div>'
							.					'</div>'
							.				'</form>'
							.			'</div>'
							.		'</div>';
		}

		$return				.=		'<div class="table-responsive forumsContainer">'
							.			'<table class="table table-hover mb-0">'
							.				'<thead>'
							.					'<tr>'
							.						'<th style="width: 50%;">' . CBTxt::T( 'Subject' ) . '</th>'
							.						'<th style="width: 25%;" class="d-none d-sm-table-cell">' . CBTxt::T( 'Category' ) . '</th>'
							.						'<th style="width: 25%;" class="d-none d-sm-table-cell">' . CBTxt::T( 'Date' ) . '</th>'
							.					'</tr>'
							.				'</thead>'
							.				'<tbody>';

		if ( $rows ) foreach ( $rows as $row ) {
			$return			.=					'<tr>'
							.						'<td style="width: 50%;"><a href="' . ( isset( $row->url ) ? $row->url : cbforumsModel::getForumURL( $row->category_id, $row->id ) ) . '">' . cbforumsClass::cleanPost( $row->subject ) . '</a></td>'
							.						'<td style="width: 25%;" class="d-none d-sm-table-cell"><a href="' . ( isset( $row->category_url ) ? $row->category_url : cbforumsModel::getForumURL( $row->category_id ) ) . '">' . cbforumsClass::cleanPost( $row->category_name ) . '</a></td>'
							.						'<td style="width: 25%;" class="d-none d-sm-table-cell">' . cbFormatDate( date( 'Y-m-d H:i:s', $row->date ) ) . '</td>'
							.					'</tr>';
		} else {
			$return			.=					'<tr>'
							.						'<td colspan="3">';

			if ( $searching ) {
				$return		.=							CBTxt::T( 'No post search results found.' );
			} else {
				if ( $viewer->id == $user->id ) {
					$return	.=							CBTxt::T( 'You have no posts.' );
				} else {
					$return	.=							CBTxt::T( 'This user has no posts.' );
				}
			}

			$return			.=						'</td>'
							.					'</tr>';
		}

		$return				.=				'</tbody>';

		if ( $tabPaging && ( $pageNav->total > $pageNav->limit ) ) {
			$return			.=				'<tfoot>'
							.					'<tr>'
							.						'<td colspan="3" class="text-center">'
							.							$pageNav->getListLinks()
							.						'</td>'
							.					'</tr>'
							.				'</tfoot>';
		}

		$return				.=			'</table>'
							.		'</div>'
							.	'</div>';

		return $return;
	}
}
