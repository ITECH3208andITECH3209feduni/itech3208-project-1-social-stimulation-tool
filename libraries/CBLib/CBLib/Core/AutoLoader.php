<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 07.06.13 17:16 $
* @package CBLib
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CBLib\Core;

defined('CBLIB') or die();

/**
 * CBLib\AutoLoader Class implementation
 *
 * Usage:
 *
 * include_once 'libraries/CBLib/CBLib.php'
 * use CBLib as CBLib;
 * \CBLib\AutoLoader::setup();
 *
 */
abstract class AutoLoader
{
	/**
	 * Specific class mappings for potential exceptions
	 * e.g. 'cbpaidTotalizertypeCompoundable'		=>	'models/totalizer/cbpaidCrossTotalizer.php'
	 *
	 * @var array
	 */
	private static $classes				=	array();

	/**
	 * Specific exact class mappings for potential exceptions
	 * e.g. 'UddeIM' => 'D:/server/www/components/com_comprofiler/plugin/user/plug_pms_mypmspro/library/UddeIM.php'
	 *
	 * @var array
	 */
	private static $exactClasses		=	array();

	/**
	 * Generic class mappings using regex
	 * e.g. '/^(cbpaidController.*|cbpaid.*Handler)$/'						=>	'controllers/$1.php'
	 *
	 * @var array
	 */
	private static $maps				=	array();

	/**
	 * Generic exact class mappings using regex
	 * e.g. '%^CB/Plugin/PMS/(.+)%i' => 'D:/server/www/components/com_comprofiler/plugin/user/plug_pms_mypmspro/library/$1.php'
	 *
	 * @var array
	 */
	private static $exactMaps			=	array();

	/**
	 * List of folders for PSR-0 namespaced classes
	 *
	 * @var string[]
	 */
	private static $folders				=	array();

	/**
	 * contains dirname( __DIR__ ) . DIRECTORY_SEPARATOR
	 *
	 * @var string
	 */
	private static $libraryDirSlash		=	null;
	/**
	 * Uses the autoloader registration function available for this PHP version
	 *
	 * @return void
	 */
	public static function setup( )
	{
		static $setupDone	=	false;
		if ( ! $setupDone ) {
			self::$libraryDirSlash		=	dirname( dirname( __DIR__ ) ) . DIRECTORY_SEPARATOR;

			self::registerLibrary( self::$libraryDirSlash );

			spl_autoload_register( array( '\CBLib\Core\AutoLoader', 'autoloader' ) );

			$setupDone		=	true;
		}
	}

	/**
	 * Registers a $file name for class of name $className
	 *
	 * @param  string  $className
	 * @param  string  $file
	 * @return void
	 */
	public static function registerClass( $className, $file )
	{
		self::$classes[$className]		=	$file;
	}

	/**
	 * Registers a exact $file name for class of name $className
	 * This requires $file to be the full path of the file
	 *
	 * @param  string  $className
	 * @param  string  $file
	 * @return void
	 */
	public static function registerExactClass( $className, $file )
	{
		self::$exactClasses[$className]		=	$file;
	}

	/**
	 * Registers a Mapping Regexp mapping
	 *
	 * @param  string  $classNameRegexp  Matching Regular expression for the class name
	 * @param  string  $folderRegexp     Replacing Regexp for the corresponding folder (using regexp substitutions
	 * @return void
	 */
	public static function registerMap( $classNameRegexp, $folderRegexp )
	{
		self::$maps[$classNameRegexp]		=	$folderRegexp;
	}

	/**
	 * Registers a exact Mapping Regexp mapping
	 * This requires the $folderRegexp to include the full path to the file
	 *
	 * @param  string  $classNameRegexp  Matching Regular expression for the class name
	 * @param  string  $folderRegexp     Replacing Regexp for the corresponding folder (using regexp substitutions
	 * @return void
	 */
	public static function registerExactMap( $classNameRegexp, $folderRegexp )
	{
		self::$exactMaps[$classNameRegexp]	=	$folderRegexp;
	}

	/**
	 * Registers a Mapping Regexp mapping
	 *
	 * @param  string  $folder     Additional base folder to look into for libraries with a tailing DIRECTORY_SEPARATOR
	 * @return void
	 */
	public static function registerLibrary( $folder )
	{
		self::$folders[$folder]			=	$folder;
	}

	/**
	 * DO NOT CALL DIRECTLY: The Autoloader function called by PHP to load an unknown class name $className
	 *
	 * @param  string  $className
	 * @return boolean
	 */
	public static function autoloader( $className )
	{
		// Replace backslash with forwardslash for valid file pathing:
		$className					=	str_replace( '\\', '/', $className );
		// PSR-0 Loader (without need for _ handling in class names as we do not use them in CBLib):

		foreach ( self::$folders as $folder ) {
			$file						=	  $folder . $className . '.php';

			if ( is_readable( $file ) ) {
				/** @noinspection PhpIncludeInspection */
				if ( include_once $file ) {
					return true;
				}
			}
		}

		// Defined exceptions: specific classes:
		if ( isset( self::$classes[$className] ) ) {
			foreach ( self::$folders as $folder ) {
				$file					=	$folder . self::$classes[$className];

				if ( is_readable( $file ) ) {
					/** @noinspection PhpIncludeInspection */
					if ( include_once $file ) {
						return true;
					}
				}
			}
		}

		// Defined exceptions: generic classes mappings using regexp:
		foreach ( self::$maps as $classNameRegexp => $folderRegexp ) {
			if ( preg_match( $classNameRegexp, $className ) ) {
				foreach ( self::$folders as $folder ) {
					$file				=	$folder . preg_replace( $classNameRegexp, addslashes( $folderRegexp ), $className );

					if ( is_readable( $file ) ) {
						/** @noinspection PhpIncludeInspection */
						if ( include_once $file ) {
							return true;
						}
					}
				}
			}
		}

		// Defined exceptions: exact specific classes:
		if ( isset( self::$exactClasses[$className] ) ) {
			$file						=	self::$exactClasses[$className];

			if ( is_readable( $file ) ) {
				/** @noinspection PhpIncludeInspection */
				if ( include_once $file ) {
					return true;
				}
			}
		}

		// Defined exceptions: generic exact classes mappings using regexp:
		foreach ( self::$exactMaps as $classNameRegexp => $folderRegexp ) {
			if ( preg_match( $classNameRegexp, $className ) ) {
				$file					=	preg_replace( $classNameRegexp, addslashes( $folderRegexp ), $className );

				if ( is_readable( $file ) ) {
					/** @noinspection PhpIncludeInspection */
					if ( include_once $file ) {
						return true;
					}
				}
			}
		}

		return false;
	}
}
