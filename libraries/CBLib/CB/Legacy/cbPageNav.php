<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 6/20/14 1:42 PM $
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CBLib\Language\Pluralization;

defined('CBLIB') or die();

/**
 * cbPageNav Class implementation
 * Page navigation class
 */
class cbPageNav
{
	/**
	 * the record number to start dislpaying from
	 *
	 * @var int
	 */
	public $limitstart 				=	null;

	/**
	 * number of rows to display per page
	 *
	 * @var int
	 */
	public $limit 					=	null;

	/**
	 * total number of rows
	 *
	 * @var int
	 */
	public $total 					=	null;

	/**
	 * base url to use for url based pagination instead of JS based pagination
	 *
	 * @var string
	 */
	protected $baseUrl				=	null;

	/**
	 * determines if the paging should continue to use onclick post behavior even if baseurl is supplied
	 *
	 * @var bool
	 */
	protected $forcePost			=	false;

	/**
	 * determines if the paging limit is static and doesn't need to be included in POST or URL
	 *
	 * @var bool
	 */
	protected $staticLimit			=	false;

	/**
	 * function name for custom input name formatting
	 *
	 * @var string|array
	 */
	protected $fieldNameFnct		=	null;

	/**
	 * input name prefix
	 *
	 * @var string
	 */
	protected $fieldNamePrefix		=	null;

	/**
	 * CB Draw Controller for ordering feature
	 *
	 * @var cbDrawController
	 */
	protected $_controllerView;

	/**
	 * number of rows displayed on the page
	 *
	 * @var int
	 */
	protected $rowsNumber 			=	null;

	/**
	 * current index of row during display for ordering icons feature
	 *
	 * @var null
	 */
	protected $rowIndex 			=	null;

	/**
	 * limit box list items
	 *
	 * @var array
	 */
	protected $limits				=	array( 1, 2, 3, 5, 10, 15, 20, 30, 50, 100, 200, 300, 500, 1000, 2000, 5000 );

	/**
	 * cached list of classes to use for pagination
	 *
	 * @var array
	 */
	protected $classes				=	array(
												'cbPagination' => 'cbPagination',
												'cbPaginationLinks' => 'cbPaginationLinks pagination justify-content-center mb-0',
												'cbPaginationLimit' => 'cbPaginationLimit',
												'cbPaginationCounter' => 'cbPaginationCounter d-none d-sm-none d-md-inline-block',
												'cbPageLimitbox' => 'cbPageLimitbox form-control',
												'cbPageNav' => 'cbPageNav page-item',
												'cbPageNavStart' => 'cbPageNavStart',
												'cbPageNavFirst' => 'cbPageNavFirst',
												'cbPageNavNext' => 'cbPageNavNext',
												'cbPageNavPrev' => 'cbPageNavPrev',
												'cbPageNavLast' => 'cbPageNavLast d-none d-sm-none d-md-block',
												'cbPageNavEnd' => 'cbPageNavEnd',
												'cbPageNavActive' => 'cbPageNavActive active',
												'cbPageNavDisabled' => 'cbPageNavDisabled disabled',
												'cbPageNavCutoff' => 'cbPageNavCutoff d-none d-sm-none d-md-block',
												'cbPageNavPage' => 'cbPageNavPage d-none d-sm-none d-md-block',
												'cbPageNavLink' => 'cbPageNavLink page-link'
											 );

	/**
	 * prepares paging variables to build pagination links
	 *
	 * @param int $total
	 * @param int $limitstart
	 * @param int $limit
	 * @param string|array $fieldNamingMethod
	 */
	public function __construct( $total, $limitstart, $limit, $fieldNamingMethod = null )
	{
		$this->total 			=	(int) $total;
		$this->limitstart 		=	(int) max( $limitstart, 0 );
		$this->limit 			=	(int) max( $limit, 1 );

		if ( $this->limit > $this->total ) {
			$this->limitstart	=	0;
		}

		if ( ( ( $this->limit - 1 ) * $this->limitstart ) > $this->total ) {
			$this->limitstart	-=	( $this->limitstart % $this->limit );
		}

		$this->setInputNameFunction( $fieldNamingMethod );
	}

	/**
	 * formats input name
	 *
	 * @param string $name
	 * @return string
	 */
	protected function fieldName( $name )
	{
		return $this->fieldNamePrefix . ( $this->fieldNameFnct ? call_user_func( $this->fieldNameFnct, $name ) : $name );
	}

	/**
	 * set pagination base url to use url based pagination instead of js based paging unless $forcePost is true
	 *
	 * @param string $url
	 * @param bool   $forcePost
	 */
	public function setBaseURL( $url, $forcePost = false )
	{
		if ( $url && is_string( $url ) ) {
			$this->baseUrl		=	$url;
			$this->forcePost	=	$forcePost;
		}
	}

	/**
	 * get pagination base url
	 *
	 * @return string
	 */
	public function getBaseURL()
	{
		return $this->baseUrl;
	}

	/**
	 * set if paging limit is static and isn't needed in POST or URL
	 *
	 * @param bool $static
	 */
	public function setStaticLimit( $static )
	{
		$this->staticLimit = $static;
	}

	/**
	 * get if paging limit is static and isn't needed in POST or URL
	 *
	 * @return bool
	 */
	public function getStaticLimit()
	{
		return $this->staticLimit;
	}

	/**
	 * set controller view
	 *
	 * @param \CBLib\AhaWow\Controller\DrawController $controllerView
	 */
	public function setControllerView( $controllerView )
	{
		$this->_controllerView	=	$controllerView;
	}

	/**
	 * get controller view
	 *
	 * @return \CBLib\AhaWow\Controller\DrawController
	 */
	public function getControllerView()
	{
		return $this->_controllerView;
	}

	/**
	 * set input name format function
	 *
	 * @param string|array $function
	 */
	public function setInputNameFunction( $function )
	{
		if ( $function && ( is_string( $function ) || is_array( $function ) ) ) {
			$this->fieldNameFnct	=	$function;
		}
	}

	/**
	 * get input name format function
	 *
	 * @return mixed
	 */
	public function getInputNameFunction()
	{
		return $this->fieldNameFnct;
	}

	/**
	 * set input name prefix
	 *
	 * @param string $prefix
	 */
	public function setInputNamePrefix( $prefix )
	{
		if ( $prefix && is_string( $prefix ) ) {
			$this->fieldNamePrefix	=	$prefix;
		}
	}

	/**
	 * get input name prefix
	 *
	 * @return string
	 */
	public function getInputNamePrefix()
	{
		return $this->fieldNamePrefix;
	}

	/**
	 * set limitbox limits
	 *
	 * @param array $limits
	 */
	public function setLimits( $limits )
	{
		if ( $limits && is_array( $limits ) ) {
			$this->limits	=	$limits;
		}
	}

	/**
	 * get limitbox limits
	 *
	 * @return array
	 */
	public function getLimits()
	{
		return $this->limits;
	}

	/**
	 * sets pagination classes
	 *
	 * @param array $classes
	 * @param bool  $append
	 */
	public function setClasses( $classes, $append = false )
	{
		if ( $classes && is_array( $classes ) ) foreach ( $classes as $type => $class ) {
			if ( ! isset( $this->classes[$type] ) ) {
				continue;
			}

			if ( $append ) {
				$this->classes[$type]	=	trim( $this->classes[$type] . ' ' . $class );
			} else {
				$this->classes[$type]	=	$class;
			}
		}
	}

	/**
	 * gets array of pagination classes
	 *
	 * @return array
	 */
	public function getClasses()
	{
		return $this->classes;
	}

	/**
	 * sets the number of rows to display on the page
	 *
	 * @param int $n
	 * @deprecated 2.0 use setRowsNumber
	 */
	public function setN( $n )
	{
		$this->setRowsNumber( $n );
	}

	/**
	 * sets the number of rows to display on the page
	 *
	 * @param int $n
	 */
	public function setRowsNumber( $n )
	{
		$this->rowsNumber	=	$n;
	}

	/**
	 * gets the number of rows to display on the page
	 *
	 * @return int
	 */
	public function getRowsNumber()
	{
		return $this->rowsNumber;
	}

	/**
	 * sets the current row index
	 *
	 * @param int $i
	 * @deprecated 2.0 use setRowIndex
	 */
	public function setI( $i )
	{
		$this->setRowIndex( $i );
	}

	/**
	 * sets the current row index
	 *
	 * @param int $i
	 */
	public function setRowIndex( $i )
	{
		$this->rowIndex	=	$i;
	}

	/**
	 * gets the current row index
	 *
	 * @return int
	 */
	public function getRowIndex()
	{
		return $this->rowIndex;
	}

	/**
	 * gets the current row number from row index
	 *
	 * @param int $i
	 * @return int
	 * @deprecated 2.0 use getRowNumber
	 */
	public function rowNumber( $i )
	{
		return ( $i + 1 + $this->limitstart );
	}

	/**
	 * gets the current row number from row index
	 *
	 * @param int $i
	 * @return int
	 */
	public function getRowNumber( $i )
	{
		return ( $i + 1 + $this->limitstart );
	}

	/**
	 * returns onclick limitstart js
	 *
	 * @param $value
	 * @return string
	 * @deprecated 2.0 use limitstartJs
	 */
	public function js_limitstart( $value )
	{
		return $this->limitstartJs( $value );
	}

	/**
	 * returns onclick limitstart js
	 *
	 * @param $value
	 * @return string
	 */
	public function limitstartJs( $value )
	{
		return "cbParentForm( this ).elements['"
			. addslashes( $this->fieldName( 'limitstart' ) )
			. "'].value=" . (int) $value
			. "; cbParentForm( this ).submit(); return false;";
	}

	/**
	 * returns href limitstart url
	 *
	 * @param $value
	 * @return string
	 */
	public function limitstartUrl( $value )
	{
		return cbSef( $this->getBaseURL()
			. ( strpos( $this->getBaseURL(), '?' ) !== false ? '&' : '?' )
			. ( ! $this->staticLimit ? $this->fieldName( 'limit' ) . '=' . (int) $this->limit . '&' : null )
			. urlencode( $this->fieldName( 'limitstart' ) ) . '=' . (int) $value
		);
	}

	/**
	 * builds and returns limitbox select input or hidden limit input
	 *
	 * @param bool $showLimitBox
	 * @param bool $showLabel
	 * @return string
	 */
	public function getLimitBox( $showLimitBox = true, $showLabel = false )
	{
		$return						=	null;

		if ( ! $this->staticLimit ) {
			if ( $showLimitBox ) {
				if ( ! in_array( (int) $this->limit, $this->limits ) ) {
					$this->limits[]	=	(int) $this->limit;

					cbArrayToInts( $this->limits );

					sort( $this->limits );
				}

				$limits				=	array();

				foreach ( $this->limits as $i ) {
					$limits[]		=	moscomprofilerHTML::makeOption( $i );
				}

				$return				.=	'<span class="' . htmlspecialchars( $this->classes['cbPaginationLimit'] ) . '">'
									.		( $showLabel ? CBTxt::Th( 'PAGENAV_DISPLAY_NUMBER_PER_PAGE', 'Display #' ) . ' ' : null )
									.		moscomprofilerHTML::selectList(
																			$limits, $this->fieldName( 'limit' ),
																			'class="' . htmlspecialchars( $this->classes['cbPageLimitbox'] )
																			. '" onchange="' . $this->limitstartJs( 0 ) . '"',
																			'value',
																			'text',
																			(int) $this->limit, 2
																		  )
									.	'</span>';
			} else {
				$return				.=	'<input type="hidden" name="' . $this->fieldName( 'limit' ) . '" value="' . (int) $this->limit . '" />';
			}
		}

		$return						.=	'<input type="hidden" name="' . $this->fieldName( 'limitstart' ) . '" value="' . (int) $this->limitstart . '" />';

		return $return;
	}

	/**
	 * write limitbox html
	 *
	 * @deprecated 2.0 use getLimitBox
	 */
	public function writeLimitBox()
	{
		echo $this->getLimitBox();
	}

	/**
	 * returns html page count results as "Results 1-10 of x" or if raw returns array of results as array( from, to, total )
	 * @param bool $raw
	 * @return array|string
	 */
	public function getPagesCounter( $raw = false )
	{
		$fromResult			=	( $this->limitstart + 1 );

		if ( ( $this->limitstart + $this->limit ) < $this->total ) {
			$toResult		=	( $this->limitstart + $this->limit );
		} else {
			$toResult		=	$this->total;
		}

		if ( $raw ) {
			$return			=	( $this->total > 0 ? array( 0, 0, (int) $this->total ) : array( (int) $fromResult, (int) $toResult, (int) $this->total ) );
		} else {
			$return			=	'<span class="' . htmlspecialchars( $this->classes['cbPaginationCounter'] ) . '">';

			if ( $this->total > 0 ) {
				// Formatted that way to not be picked-up by translation grabber, as used for pluralization, not for translation:
				$return		.=		strtr(
											Pluralization::pluralize(
												'{1} ([NUMFIRST]/%%TOTALRESULTS%%)|]1,Inf] ([NUMFIRST]-[NUMLAST]/%%TOTALRESULTS%%)',
												array( '%%TOTALRESULTS%%'	=> (int) $this->total ),
												'en'
											),
											array(	'[NUMFIRST]'		=> (int) $fromResult,
													  '[NUMLAST]'			=> (int) $toResult,
													  '%%TOTALRESULTS%%'	=> (int) $this->total )
									);
			} else {
				$return		.=		CBTxt::Th( 'PAGENAV_NO_RESULTS UE_NO_RESULTS', 'No results' );
			}

			$return			.=	'</span>';
		}

		return $return;
	}

	/**
	 * write page counter html
	 *
	 * @deprecated 2.0 use getPagesCounter
	 */
	public function writePagesCounter()
	{
		echo $this->getPagesCounter();
	}

	/**
	 * returns html pagination links
	 *
	 * @param string $format
	 * @param int $pageLimit
	 * @param bool $showCutoff
	 * @param bool $showBegin
	 * @param bool $showPrev
	 * @param bool $showNext
	 * @param bool $showEnd
	 * @param bool $showLinks
	 * @return string
	 */
	public function getPagesLinks( $format = 'li', $pageLimit = 10, $showCutoff = true, $showBegin = true, $showPrev = true, $showNext = true, $showEnd = true, $showLinks = true )
	{
		$limitstart				=	max( (int) $this->limitstart, 0 );
		$limit					=	max( (int) $this->limit, 1 );
		$total					=	(int) $this->total;

		$pageLimit				=	( ! $pageLimit ? 10 : (int) $pageLimit );
		$totalPages				=	ceil( $total / $limit );
		$currentPage			=	ceil( ( $limitstart + 1 ) / $limit );
		$startLoop				=	( $currentPage - floor( $pageLimit / 2 ) );

		if ( $startLoop < 1 ) {
			$startLoop			=	1;
		}

		if ( $startLoop == 3 ) {
			$startLoop			=	2;
		}

		if ( ( $startLoop + $pageLimit - 1 ) < ( $totalPages - 2 ) ) {
			$stopLoop			=	( $startLoop + $pageLimit - 1 );
		} else {
			$stopLoop			=	$totalPages;
		}

		$return					=	null;

		if ( $currentPage > 1 ) {
			$page				=	( ( $currentPage - 2 ) * $this->limit );

			if ( $showBegin ) {
				$pageHtml		=	'<a' . ( $this->getBaseURL() ? ' href="' . $this->limitstartUrl( 0 ) . '"' : ' href="#beg"' ) . ( ( ! $this->getBaseURL() ) || $this->forcePost ? ' onclick="' . $this->limitstartJs( 0 ) . '"' : null ) . ' title="' .  htmlspecialchars( CBTxt::T( 'PAGENAV_FIRST_PAGE', 'First page' ) ) . '" class="' . htmlspecialchars( trim( $this->classes['cbPageNavLink'] ) ) . '">&laquo;</a>';

				if ( $format == 'li' ) {
					$return		.=	'<li class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavStart'] ) ) . '">' . $pageHtml . '</li>';
				} else {
					$return		.=	'<span class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavStart'] ) ) . '">' . $pageHtml . '</span>';
				}
			}

			if ( $showPrev ) {
				$pageHtml		=	'<a' . ( $this->getBaseURL() ? ' href="' . $this->limitstartUrl( $page ) . '"' : ' href="#prev"' ) . ( ( ! $this->getBaseURL() ) || $this->forcePost ? ' onclick="' . $this->limitstartJs( $page ) . '"' : null ) . ' title="' . htmlspecialchars( CBTxt::T( 'PAGENAV_PREVIOUS_PAGE', 'Previous page' )  ) . '" class="' . htmlspecialchars( trim( $this->classes['cbPageNavLink'] ) ) . '">&lsaquo;</a>';

				if ( $format == 'li' ) {
					$return		.=	'<li class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavPrev'] ) ) . '">' . $pageHtml . '</li>';
				} else {
					$return		.=	'<span class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavPrev'] ) ) . '">' . $pageHtml . '</span>';
				}
			}

			if ( ( $startLoop > 1 ) && $showLinks ) {
				$pageHtml		=	'<a' . ( $this->getBaseURL() ? ' href="' . $this->limitstartUrl( 0 ) . '"' : ' href="#beg"' ) . ( ( ! $this->getBaseURL() ) || $this->forcePost ? ' onclick="' . $this->limitstartJs( 0 ) . '"' : null ) . ' title="'  . htmlspecialchars( CBTxt::T('PAGENAV_FIRST_PAGE', 'First page' ) ) . '" onclick="' . $this->limitstartJs( 0 ) . '" class="' . htmlspecialchars( trim( $this->classes['cbPageNavLink'] ) ) . '">1</a>';

				if ( $format == 'li' ) {
					$return		.=	'<li class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavFirst'] ) ) . '">' . $pageHtml . '</li>';
				} else {
					$return		.=	'<span class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavFirst'] ) ) . '">' . $pageHtml . '</span>';
				}
			}

			if ( ( $startLoop > 2 ) && $showCutoff ) {
				$pageHtml		=	'<a href="#" class="' . htmlspecialchars( trim( $this->classes['cbPageNavLink'] ) ) . '">...</a>';

				if ( $format == 'li' ) {
					$return		.=	'<li class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavCutoff'] ) ) . '">' . $pageHtml . '</li>';
				} else {
					$return		.=	'<span class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavCutoff'] ) ) . '">' . $pageHtml . '</span>';
				}
			}
		} else {
			if ( $showBegin ) {
				$pageHtml		=	'<a href="#" class="' . htmlspecialchars( trim( $this->classes['cbPageNavLink'] ) ) . '">&laquo;</a>';

				if ( $format == 'li' ) {
					$return		.=	'<li class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavStart'] . ' ' . $this->classes['cbPageNavDisabled'] ) ) . '">' . $pageHtml . '</li>';
				} else {
					$return		.=	'<span class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavStart'] . ' ' . $this->classes['cbPageNavDisabled'] ) ) . '">' . $pageHtml . '</span>';
				}
			}

			if ( $showLinks ) {
				$pageHtml		=	'<a href="#" class="' . htmlspecialchars( trim( $this->classes['cbPageNavLink'] ) ) . '">&lsaquo;</a>';

				if ( $format == 'li' ) {
					$return		.=	'<li class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavPrev'] . ' ' . $this->classes['cbPageNavDisabled'] ) ) . '">' . $pageHtml . '</li>';
				} else {
					$return		.=	'<span class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavPrev'] . ' ' . $this->classes['cbPageNavDisabled'] ) ) . '">' . $pageHtml . '</span>';
				}
			}
		}

		if ( $showLinks ) for ( $i = $startLoop; $i <= $stopLoop; $i++ ) {
			$page				=	( ( $i - 1 ) * $this->limit );

			if ( $i == $currentPage ) {
				$pageHtml		=	'<a href="#" class="' . htmlspecialchars( trim( $this->classes['cbPageNavLink'] ) ) . '">' . (int) $i . '</a>';

				if ( $format == 'li' ) {
					$return		.=	'<li class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavActive'] ) ) . '">' . $pageHtml . '</li>';
				} else {
					$return		.=	'<span class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavActive'] ) ) . '">' . $pageHtml . '</span>';
				}
			} else {
				$pageHtml		=	'<a' . ( $this->getBaseURL() ? ' href="' . $this->limitstartUrl( $page ) . '"' : ' href="#' . (int) $i . '"' ) . ( ( ! $this->getBaseURL() ) || $this->forcePost ? ' onclick="' . $this->limitstartJs( $page ) . '"' : null ) . ' class="' . htmlspecialchars( trim( $this->classes['cbPageNavLink'] ) ) . '">' . (int) $i . '</a>';

				if ( $format == 'li' ) {
					$return		.=	'<li class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavPage'] ) ) . '">' . $pageHtml . '</li>';
				} else {
					$return		.=	'<span class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavPage'] ) ) . '">' . $pageHtml . '</span>';
				}
			}
		}

		if ( $currentPage < $totalPages ) {
			$page				=	( $currentPage * $this->limit );
			$endPage			=	( ( $totalPages - 1 ) * $this->limit );

			if ( ( $stopLoop < ( $totalPages - 1 ) ) && $showCutoff ) {
				$pageHtml		=	'<a href="#" class="' . htmlspecialchars( trim( $this->classes['cbPageNavLink'] ) ) . '">...</a>';

				if ( $format == 'li' ) {
					$return		.=	'<li class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavCutoff'] ) ) . '">' . $pageHtml . '</li>';
				} else {
					$return		.=	'<span class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavCutoff'] ) ) . '">' . $pageHtml . '</span>';
				}
			}

			if ( ( $stopLoop < $totalPages ) && $showLinks ) {
				$pageHtml		=	'<a' . ( $this->getBaseURL() ? ' href="' . $this->limitstartUrl( $endPage ) . '"' : ' href="#end"' ) . ( ( ! $this->getBaseURL() ) || $this->forcePost ? ' onclick="' . $this->limitstartJs( $endPage ) . '"' : null ) . ' title="' . htmlspecialchars( CBTxt::T( 'PAGENAV_LAST_PAGE', 'Last page' ) ) . '" class="' . htmlspecialchars( trim( $this->classes['cbPageNavLink'] ) ) . '">' . (int) $totalPages . '</a>';

				if ( $format == 'li' ) {
					$return		.=	'<li class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavLast'] ) ) . '">' . $pageHtml . '</li>';
				} else {
					$return		.=	'<span class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavLast'] ) ) . '">' . $pageHtml . '</span>';
				}
			}

			if ( $showNext ) {
				$pageHtml		=	'<a' . ( $this->getBaseURL() ? ' href="' . $this->limitstartUrl( $page ) . '"' : ' href="#next"' ) . ( ( ! $this->getBaseURL() ) || $this->forcePost ? ' onclick="' . $this->limitstartJs( $page ) . '"' : null ) . ' title="' . htmlspecialchars( CBTxt::T( 'PAGENAV_NEXT_PAGE', 'Next page' ) ) . '" class="' . htmlspecialchars( trim( $this->classes['cbPageNavLink'] ) ) . '">&rsaquo;</a>';

				if ( $format == 'li' ) {
					$return		.=	'<li class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavNext'] ) ) . '">' . $pageHtml . '</li>';
				} else {
					$return		.=	'<span class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavNext'] ) ) . '">' . $pageHtml . '</span>';
				}
			}

			if ( $showEnd ) {
				$pageHtml		=	'<a' . ( $this->getBaseURL() ? ' href="' . $this->limitstartUrl( $endPage ) . '"' : ' href="#end"' ) . ( ( ! $this->getBaseURL() ) || $this->forcePost ? ' onclick="' . $this->limitstartJs( $endPage ) . '"' : null ) . ' title="' . htmlspecialchars( CBTxt::T( 'PAGENAV_LAST_PAGE', 'Last page' ) ) . '" class="' . htmlspecialchars( trim( $this->classes['cbPageNavLink'] ) ) . '">&raquo;</a>';

				if ( $format == 'li' ) {
					$return		.=	'<li class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavEnd'] ) ) . '">' . $pageHtml . '</li>';
				} else {
					$return		.=	'<span class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavEnd'] ) ) . '">' . $pageHtml . '</span>';
				}
			}
		} else {
			if ( $showNext ) {
				$pageHtml		=	'<a href="#" class="' . htmlspecialchars( trim( $this->classes['cbPageNavLink'] ) ) . '">&rsaquo;</a>';

				if ( $format == 'li' ) {
					$return		.=	'<li class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavNext'] . ' ' . $this->classes['cbPageNavDisabled'] ) ) . '">' . $pageHtml . '</li>';
				} else {
					$return		.=	'<span class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavNext'] . ' ' . $this->classes['cbPageNavDisabled'] ) ) . '">' . $pageHtml . '</span>';
				}
			}

			if ( $showEnd ) {
				$pageHtml		=	'<a href="#" class="' . htmlspecialchars( trim( $this->classes['cbPageNavLink'] ) ) . '">&raquo;</a>';

				if ( $format == 'li' ) {
					$return		.=	'<li class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavEnd'] . ' ' . $this->classes['cbPageNavDisabled'] ) ) . '">' . $pageHtml . '</li>';
				} else {
					$return		.=	'<span class="' . htmlspecialchars( trim( $this->classes['cbPageNav'] . ' ' . $this->classes['cbPageNavEnd'] . ' ' . $this->classes['cbPageNavDisabled'] ) ) . '">' . $pageHtml . '</span>';
				}
			}
		}

		if ( $format == 'li' ) {
			$return				=	'<ul class="' . htmlspecialchars( $this->classes['cbPaginationLinks'] ) . '">'
				.		$return
				.	'</ul>';
		} else {
			$return				=	'<span class="' . htmlspecialchars( $this->classes['cbPaginationLinks'] ) . '">'
				.		$return
				.	'</span>';
		}

		return $return;
	}

	/**
	 * write page links html
	 *
	 * @param string $format
	 * @deprecated 2.0 use getPagesLinks
	 */
	public function writePagesLinks( $format = 'li' )
	{
		echo $this->getPagesLinks( $format );
	}

	/**
	 * returns page limitbox pagination html
	 *
	 * @param bool $showLimitboxLabel
	 * @return string
	 */
	public function getListLimitbox( $showLimitboxLabel = true )
	{
		$return		=	'<div class="' . htmlspecialchars( $this->classes['cbPagination'] ) . '">'
					.		$this->getLimitBox( true, $showLimitboxLabel )
					.	'</div>';

		return $return;
	}

	/**
	 * returns page link pagination html
	 *
	 * @param string $pageLinksFormat
	 * @return string
	 */
	public function getListLinks( $pageLinksFormat = 'li' )
	{
		$return		=	'<div class="' . htmlspecialchars( $this->classes['cbPagination'] ) . '">'
					.		$this->getPagesLinks( $pageLinksFormat )
					.	'</div>';

		return $return;
	}

	/**
	 * returns page counter pagination html
	 *
	 * @return string
	 */
	public function getListCounter()
	{
		$return		=	'<div class="' . htmlspecialchars( $this->classes['cbPagination'] ) . '">'
					.		$this->getPagesCounter()
					.	'</div>';

		return $return;
	}

	/**
	 * returns page header pagination html
	 *
	 * @param bool $showPageLinks
	 * @param bool $showPagesCount
	 * @param string $pageLinksFormat
	 * @return string
	 */
	public function getListHeader( $showPageLinks = true, $showPagesCount = true, $pageLinksFormat = 'li' )
	{
		$return				=	'<div class="' . htmlspecialchars( $this->classes['cbPagination'] ) . '">';

		if ( $showPageLinks ) {
			$return			.=		$this->getPagesLinks( $pageLinksFormat );
		}

		if ( $showPagesCount ) {
			$return			.=		$this->getPagesCounter();
		}

		$return				.=	'</div>';

		return $return;
	}

	/**
	 * returns page footer pagination html
	 *
	 * @param bool     $showPageLinks
	 * @param bool|int $showLimitBox
	 * @param bool     $showPagesCount
	 * @param string   $pageLinksFormat
	 * @param bool     $showLimitboxLabel
	 * @return string
	 */
	public function getListFooter( $showPageLinks = true, $showLimitBox = true, $showPagesCount = true, $pageLinksFormat = 'li', $showLimitboxLabel = false )
	{
		$return				=	'<div class="' . htmlspecialchars( $this->classes['cbPagination'] ) . ' clearfix">'
							.		( $showLimitBox || $showPagesCount ? '<div class="float-left">' : null );

		if ( $showLimitBox !== 0 ) {
			$return			.=		$this->getLimitBox( $showLimitBox, $showLimitboxLabel );
		}

		if ( $showPagesCount ) {
			$return			.=		$this->getPagesCounter();
		}

		$return				.=		( $showLimitBox || $showPagesCount ? '</div>' : null );

		if ( $showPageLinks ) {
			$return			.=		$this->getPagesLinks( $pageLinksFormat );
		}

		$return				.=	'</div>';

		return $return;
	}

	/**
	 * returns controller view order up icon
	 *
	 * @param int $i
	 * @param bool $condition
	 * @param string $task
	 * @param string $alt
	 * @return string
	 */
	public function orderUpIcon( $i = null, $condition = true, $task = 'orderup', $alt = '#' )
	{
		if ( $i === null ) {
			$i				=	$this->rowIndex;
		}

		if ( ( $i > 0 || ( $i + $this->limitstart > 0 ) ) && $condition ) {
			$taskName		=	$this->_controllerView->taskName( false );
			$subTaskName	=	$this->_controllerView->subtaskName( false );
			$subTaskValue	=	$this->_controllerView->subtaskValue( $task, false );
			$fieldId		=	$this->_controllerView->fieldId( 'id', null, false );

			if ( $alt == '#' ) {
				$alt		=	CBTxt::T( 'ORDERING_ICON_ALT_MOVE_UP', 'Move Up' );
			}

			$onClick		=	"return cbListItemTask( this, '" . $taskName . "', '" . $subTaskName . "', '" . $subTaskValue . "', '" . $fieldId . "', '" . $i . "' );";

			$return			=	'<a href="javascript: void(0);" onclick="' . $onClick . '">'
							.		'<span class="fa fa-sort-up fa-lg text-primary" title="' . htmlspecialchars( $alt ) . '"></span>'
							.	'</a>';

			return $return;
		} else {
			return '<span class="fa fa-sort-up fa-lg text-primary invisible"></span>';
		}
	}

	/**
	 * returns controller view order dpwn icon
	 *
	 * @param int $i
	 * @param int $n
	 * @param bool $condition
	 * @param string $task
	 * @param string $alt
	 * @return string
	 */
	public function orderDownIcon( $i = null, $n = null, $condition = true, $task = 'orderdown', $alt = '#' )
	{
		if ( $i === null ) {
			$i				=	$this->rowIndex;
		}

		if ( $n === null ) {
			$n				=	$this->rowsNumber;
		}

		if ( ( ( $i < ( $n - 1 ) ) || ( ( $i + $this->limitstart ) < ( $this->total - 1 ) ) ) && $condition ) {
			$taskName		=	$this->_controllerView->taskName( false );
			$subTaskName	=	$this->_controllerView->subtaskName( false );
			$subTaskValue	=	$this->_controllerView->subtaskValue( $task, false );
			$fieldId		=	$this->_controllerView->fieldId( 'id', null, false );

			if ( $alt == '#' ) {
				$alt		=	CBTxt::T( 'ORDERING_ICON_ALT_MOVE_DOWN', 'Move Down' );
			}

			$onClick		=	"return cbListItemTask( this, '" . $taskName . "', '" . $subTaskName . "', '" . $subTaskValue . "', '" . $fieldId . "', '" . $i . "' );";

			$return			=	'<a href="javascript: void(0);" onclick="' . $onClick . '">'
							.		'<span class="fa fa-sort-down fa-lg text-primary" title="' . htmlspecialchars( $alt ) . '"></span>'
							.	'</a>';

			return $return;
		} else {
			return '<span class="fa fa-sort-down fa-lg text-primary invisible"></span>';
		}
	}

	/**
	 * returns controller view publish state icon
	 *
	 * @param string $name
	 * @param string $value
	 * @param bool $toggling
	 * @param int $i
	 * @return string
	 * @deprecated 2.0.0
	 */
	public function publishedToggle( $name, $value, $toggling, $i = null )
	{
		if ( $i === null ) {
			$i				=	$this->rowIndex;
		}

		$publishTask		=	( $value ? 'unpublish/' . $name : 'publish/' . $name );
		$publishImg			=	( $value ? 'check text-success' : 'times text-danger' );
		$publishTitle		=	( $value ? CBTxt::T( 'PUBLISHED_ICON_TITLE_UNPUBLISHED_ITEM', 'Unpublished Item' ) : CBTxt::T( 'PUBLISHED_ICON_TITLE_PUBLISHED_ITEM', 'Published Item' ) );

		if ( $toggling ) {
			$taskName		=	$this->_controllerView->taskName( false );
			$subTaskName	=	$this->_controllerView->subtaskName( false );
			$subTaskValue	=	$this->_controllerView->subtaskValue( $publishTask, false );
			$fieldId		=	$this->_controllerView->fieldId( 'id', null, false );

			$onClick		=	"return cbListItemTask( this, '" . $taskName . "', '" . $subTaskName . "', '" . $subTaskValue . "', '" . $fieldId . "', '" . $i . "' );";

			$return			=	'<a href="javascript: void(0);" onclick="' . $onClick . '">'
							.		'<span class="fa fa-' . $publishImg . ' fa-lg" title="' . htmlspecialchars( $publishTitle ) . '"></span>'
							.	'</a>';

			return $return;
		} else {
			return '<span class="fa fa-' . $publishImg . ' fa-lg" title="' . htmlspecialchars( $publishTitle ) . '"></span>';
		}
	}

	/**
	 * returns controller view checkmark state icon
	 *
	 * @param string $name
	 * @param string $value
	 * @param bool $toggling
	 * @param int $i
	 * @return string
	 * @deprecated 2.0.0
	 */
	public function checkMarkToggle( $name, $value, $toggling, $i = null )
	{
		if ( $i === null ) {
			$i				=	$this->rowIndex;
		}

		$checkmarkTask		=	( $value ? 'disable/' . $name : 'enable/' . $name );
		$checkmarkImg		=	( $value ? 'check text-success' : 'times text-danger' );
		$checkmarkTitle		=	( $value ? CBTxt::T( 'ENABLED_ICON_TITLE_DISABLE_ITEM', 'Disable Item' ) : CBTxt::T( 'ENABLED_ICON_TITLE_ENABLE_ITEM', 'Enable Item' ) );

		if ( $toggling ) {
			$taskName		=	$this->_controllerView->taskName( false );
			$subTaskName	=	$this->_controllerView->subtaskName( false );
			$subTaskValue	=	$this->_controllerView->subtaskValue( $checkmarkTask, false );
			$fieldId		=	$this->_controllerView->fieldId( 'id', null, false );

			$onClick		=	"return cbListItemTask( this, '" . $taskName . "', '" . $subTaskName . "', '" . $subTaskValue . "', '" . $fieldId . "', '" . $i . "' );";

			$return			=	'<a href="javascript: void(0);" onclick="' . $onClick . '">'
							.		'<span class="fa fa-' . $checkmarkImg . ' fa-lg" title="' . htmlspecialchars( $checkmarkTitle ) . '"></span>'
							.	'</a>';

			return $return;
		} else {
			return '<span class="fa fa-' . $checkmarkImg . ' fa-lg" title="' . htmlspecialchars( $checkmarkTitle ) . '"></span>';
		}
	}

	/**
	 * Sets the vars for the page navigation template
	 *
	 * @param  object  $tmpl
	 * @param  string  $name
	 * @return void
	 */
	public function setTemplateVars( &$tmpl, $name = 'admin-list-footer' )
	{
		$tmpl->addVar( $name, 'PAGE_LINKS', $this->getPagesLinks() );
		$tmpl->addVar( $name, 'PAGE_LIST_OPTIONS', $this->getLimitBox() );
		$tmpl->addVar( $name, 'PAGE_COUNTER', $this->getPagesCounter() );
	}
}
