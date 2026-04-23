<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 10.06.13 15:47 $
* @package CBLib\Core
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CBLib\Controller;

use CBLib\Input\InputInterface;

defined('CBLIB') or die();

/**
 * CBLib\Core\RouterInterface Interface definition
 * 
 */
interface RouterInterface
{
	/**
	 * This is the router parser, parsing from the Input $input.
	 *
	 * @param   InputInterface  $input  Input
	 * @return  callable|null           array( 'className', 'methodName' )
	 */
	public function parseRoute( InputInterface $input );

	/**
	 * Parses the $input for the main routing arguments
	 *
	 * @return  array                   Keyed array with the 3 main routing arguments
	 */
	public function getMainRoutingArgs( );

	/**
	 * Returns the core views for this CMS
	 *
	 * @return array
	 */
	public function getViews( );

	/**
	 * Converts a string to a url safe alias
	 *
	 * @param string $string
	 * @param int    $length
	 * @return string
	 */
	public function stringToAlias( $string, $length = 400 );

	/**
	 * Checks if the URL is an internal URL to the site
	 *
	 * @param string $url
	 * @return bool
	 */
	public function isInternal( $url );

	/**
	 * Returns the URL currently being accessed
	 *
	 * @return string
	 */
	public function getCurrentURL();
}
