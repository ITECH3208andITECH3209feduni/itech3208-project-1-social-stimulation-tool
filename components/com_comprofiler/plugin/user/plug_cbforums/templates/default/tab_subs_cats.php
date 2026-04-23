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
 * Class HTML_cbforumsTabCatSubs
 * CB Forum Categories Subscriptions Tab Template
 */

class HTML_cbforumsTabCatSubs
{
	/**
	 * Shows Forum Category Subscriptions
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
	static public function showCategorySubscriptions( $rows, $pageNav, $searching, $input, $viewer, $user, $tab, /** @noinspection PhpUnusedParameterInspection */ $plugin )
	{
		global $_CB_framework;

		$tabPaging			=	$tab->params->get( 'tab_subs_paging', 1 );
		$canSearch			=	( $tab->params->get( 'tab_subs_search', 1 ) && ( $searching || $pageNav->total ) );

		$return				=	'<div class="mb-5 forumsCatSubsTab">';

		if ( $canSearch ) {
			$return			.=		'<div class="row no-gutters mb-3 forumsHeader">'
							.			'<div class="col-12 offset-sm-6 col-sm-6 text-sm-right">'
							.				'<form action="' . $_CB_framework->userProfileUrl( $user->id, true, $tab->tabid ) . '" method="post" name="forumCatSubsSearchForm" class="m-0 forumCatSubsSearchForm">'
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
							.						'<th style="width: 99%;">' . CBTxt::T( 'Category' ) . '</th>'
							.						'<th style="width: 1%;" class="p-0">' . ( $rows ? '<a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete all subscriptions?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->userProfileUrl( $user->id, true, $tab->tabid, 'html', 0, array( 'forums_unsubcat' => 'all' ) ) ) . '\'; })" title="' . htmlspecialchars( CBTxt::T( 'Delete All' ) ) . '"><span class="pt-1 pb-1 pl-3 pr-3 text-large fa fa-trash-o"></span></a>' : '&nbsp;' ) . '</th>'
							.					'</tr>'
							.				'</thead>'
							.				'<tbody>';

		if ( $rows ) foreach ( $rows as $row ) {
			$return			.=					'<tr>'
							.						'<td style="width: 99%;" class="align-middle"><a href="' . ( isset( $row->category_url ) ? $row->category_url : cbforumsModel::getForumURL( $row->category_id ) ) . '">' . cbforumsClass::cleanPost( $row->category_name ) . '</a></td>'
							.						'<td style="width: 1%;" class="p-0 align-middle"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this subscription?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->userProfileUrl( $user->id, true, $tab->tabid, 'html', 0, array( 'forums_unsubcat' => $row->id ) ) ) . '\'; })" title="' . htmlspecialchars( CBTxt::T( 'Delete' ) ) . '"><span class="pt-1 pb-1 pl-3 pr-3 text-large fa fa-trash-o"></span></a></td>'
							.					'</tr>';
		} else {
			$return			.=					'<tr>'
							.						'<td colspan="2">';

			if ( $searching ) {
				$return		.=							CBTxt::T( 'No category subscription search results found.' );
			} else {
				if ( $viewer->id == $user->id ) {
					$return	.=							CBTxt::T( 'You have no category subscriptions.' );
				} else {
					$return	.=							CBTxt::T( 'This user has no category subscriptions.' );
				}
			}

			$return			.=						'</td>'
							.					'</tr>';
		}

		$return				.=				'</tbody>';

		if ( $tabPaging && ( $pageNav->total > $pageNav->limit ) ) {
			$return			.=				'<tfoot>'
							.					'<tr>'
							.						'<td colspan="4" class="text-center">'
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
