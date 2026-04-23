<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 07.06.13 23:17 $
* @package CBLib\Image
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CBLib\Image;

defined('CBLIB') or die();

/**
 * CBLib\Image\Test Class implementation
 */
class Test
{

	public function __construct() {}

	/**
	 * Checks if Gd image processing is available
	 *
	 * @return bool
	 */
	public function Gd()
	{
		if ( ! function_exists( 'gd_info' ) ) {
			return false;
		}

		if ( version_compare( GD_VERSION, '2.0.1', '<' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if Imagick image processing is available
	 *
	 * @return bool
	 */
	public function Imagick()
	{
		if ( ! class_exists( '\Imagick' ) ) {
			return false;
		}

		$v					=	\Imagick::getVersion();

		list( $version )	=	sscanf( $v['versionString'], 'ImageMagick %s %04d-%02d-%02d %s %s' );

		if ( version_compare( '6.2.9', $version ) > 0 ) {
			return false;
		}

		if ( $version === '7.0.7-32' ) { // https://github.com/avalanche123/Imagine/issues/689
			return false;
		}

		return true;
	}

	/**
	 * Checks if Gmagick image processing is available
	 *
	 * @return bool
	 */
	public function Gmagick()
	{
		if ( ! class_exists( '\Gmagick' ) ) {
			return false;
		}

		return true;
	}
}
