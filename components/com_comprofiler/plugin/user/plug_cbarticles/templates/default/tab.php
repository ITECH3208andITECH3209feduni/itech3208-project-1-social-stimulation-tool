<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CB\Database\Table\PluginTable;
use CB\Database\Table\TabTable;
use CB\Database\Table\UserTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * Class HTML_cbarticlesTab
 * Template for CB Articles
 */
class HTML_cbarticlesTab
{
	/**
	 * Renders the Articles tab
	 *
	 * @param  Table[]      $rows       Articles to render
	 * @param  cbPageNav    $pageNav    Pagination
	 * @param  boolean      $searching  Currently searching
	 * @param  string[]     $input      HTML of input elements
	 * @param  UserTable    $viewer     Viewing user
	 * @param  UserTable    $user       Viewed user
	 * @param  stdClass     $model      The model reference
	 * @param  TabTable     $tab        Current Tab
	 * @param  PluginTable  $plugin     Current Plugin
	 * @return string                   HTML
	 */
	static public function showArticleTab( $rows, $pageNav, $searching, $input, $viewer, $user, /** @noinspection PhpUnusedParameterInspection */ $model, $tab, /** @noinspection PhpUnusedParameterInspection */ $plugin )
	{
		global $_CB_framework;

		$tabPaging				=	$tab->params->get( 'tab_paging', 1 );
		$canSearch				=	( $tab->params->get( 'tab_search', 1 ) && ( $searching || $pageNav->total ) );

		$return					=	'<div class="articlesTab">';

		if ( $canSearch ) {
			$return				.=		'<div class="row no-gutters mb-3 articlesHeader">'
								.			'<div class="col-12 offset-sm-6 col-sm-6 text-sm-right">'
								.				'<form action="' . $_CB_framework->userProfileUrl( $user->id, true, $tab->tabid ) . '" method="post" name="articleSearchForm" class="m-0 articleSearchForm">'
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

		$return					.=		'<div class="table-responsive articlesContainer">'
								.			'<table class="table table-hover mb-0">'
								.				'<thead>'
								.					'<tr>'
								.						'<th style="width: 50%;">' . CBTxt::T( 'Article' ) . '</th>'
								.						'<th style="width: 25%;" class="d-none d-sm-table-cell">' . CBTxt::T( 'Category' ) . '</th>'
								.						'<th style="width: 25%;" class="d-none d-sm-table-cell">' . CBTxt::T( 'Created' ) . '</th>'
								.					'</tr>'
								.				'</thead>'
								.				'<tbody>';

		if ( $rows ) foreach ( $rows as $row ) {
			$return				.=					'<tr>'
								.						'<td style="width: 50%;"><a href="' . cbarticlesModel::getUrl( $row, true, 'article' ) . '">' . $row->get( 'title' ) . '</a></td>'
								.						'<td style="width: 25%;" class="d-none d-sm-table-cell">' . ( $row->get( 'category' ) ? '<a href="' . cbarticlesModel::getUrl( $row, true, 'category' ) . '">' . $row->get( 'category_title' ) . '</a>' : CBTxt::T( 'None' ) ) . '</td>'
								.						'<td style="width: 25%;" class="d-none d-sm-table-cell">' . cbFormatDate( $row->get( 'created' ) ) . '</td>'
								.					'</tr>';
		} else {
			$return				.=					'<tr>'
								.						'<td colspan="3" class="text-left">';

			if ( $searching ) {
				$return			.=							CBTxt::T( 'No article search results found.' );
			} else {
				if ( $viewer->id == $user->id ) {
					$return		.=							CBTxt::T( 'You have no articles.' );
				} else {
					$return		.=							CBTxt::T( 'This user has no articles.' );
				}
			}

			$return				.=						'</td>'
								.					'</tr>';
		}

		$return					.=				'</tbody>';

		if ( $tabPaging && ( $pageNav->total > $pageNav->limit ) ) {
			$return				.=				'<tfoot>'
								.					'<tr>'
								.						'<td colspan="3" class="text-center">'
								.							$pageNav->getListLinks()
								.						'</td>'
								.					'</tr>'
								.				'</tfoot>';
		}

		$return					.=			'</table>'
								.		'</div>'
								.	'</div>';

		return $return;
	}
}
