<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 10/30/13 12:01 PM $
* @package CBLib\CBLib
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CBLib\Registry;

interface TypedGetterInterface
{
	/**
	 * Gets a cmd clean param value
	 *
	 * @param string      $key     Name of index with name or input-name-encoded array selection, e.g. a.b.c
	 * @param string|null $default [optional] Default value
	 * @return string|null
	 *
	 * @throws \InvalidArgumentException        If namespace doesn't exist
	 */
	public function getCmd( $key, $default = null );

	/**
	 * Gets a int clean param value
	 *
	 * @param string   $key     Name of index with name or input-name-encoded array selection, e.g. a.b.c
	 * @param int|null $default [optional] Default value
	 * @return int|null
	 *
	 * @throws \InvalidArgumentException        If namespace doesn't exist
	 */
	public function getInt( $key, $default = null );

	/**
	 * Gets a uint clean param value
	 *
	 * @param string   $key     Name of index with name or input-name-encoded array selection, e.g. a.b.c
	 * @param int|null $default [optional] Default value
	 * @return int|null
	 *
	 * @throws \InvalidArgumentException        If namespace doesn't exist
	 */
	public function getUint( $key, $default = null );

	/**
	 * Gets a numeric clean param value
	 *
	 * @param string      $key     Name of index with name or input-name-encoded array selection, e.g. a.b.c
	 * @param string|null $default [optional] Default value
	 * @return string|null
	 *
	 * @throws \InvalidArgumentException        If namespace doesn't exist
	 */
	public function getNum( $key, $default = null );

	/**
	 * Gets a float clean param value
	 *
	 * @param string     $key     Name of index with name or input-name-encoded array selection, e.g. a.b.c
	 * @param float|null $default [optional] Default value
	 * @return float|null
	 *
	 * @throws \InvalidArgumentException        If namespace doesn't exist
	 */
	public function getFloat( $key, $default = null );

	/**
	 * Gets a boolean clean param value
	 *
	 * @param string    $key     Name of index with name or input-name-encoded array selection, e.g. a.b.c
	 * @param bool|null $default [optional] Default value
	 * @return bool|null
	 *
	 * @throws \InvalidArgumentException        If namespace doesn't exist
	 */
	public function getBool( $key, $default = null );

	/**
	 * Gets a string clean param value
	 *
	 * @param string      $key     Name of index with name or input-name-encoded array selection, e.g. a.b.c
	 * @param string|null $default [optional] Default value
	 * @return string|null
	 *
	 * @throws \InvalidArgumentException        If namespace doesn't exist
	 */
	public function getString( $key, $default = null );

	/**
	 * Gets a html clean param value
	 *
	 * @param string      $key     Name of index with name or input-name-encoded array selection, e.g. a.b.c
	 * @param string|null $default [optional] Default value
	 * @return string|null
	 *
	 * @throws \InvalidArgumentException        If namespace doesn't exist
	 */
	public function getHtml( $key, $default = null );

	/**
	 * Gets a base64 clean param value
	 *
	 * @param string      $key     Name of index with name or input-name-encoded array selection, e.g. a.b.c
	 * @param string|null $default [optional] Default value
	 * @return string|null
	 *
	 * @throws \InvalidArgumentException        If namespace doesn't exist
	 */
	public function getBase64( $key, $default = null );

	/**
	 * Gets a raw param value
	 *
	 * @param string $key     Name of index with name or input-name-encoded array selection, e.g. a.b.c
	 * @param mixed  $default [optional] Default value
	 * @return mixed
	 *
	 * @throws \InvalidArgumentException        If namespace doesn't exist
	 */
	public function getRaw( $key, $default = null );
}
