<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 07.06.13 21:17 $
* @package CBLib\AhaWow
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/


namespace CBLib\Input;

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\ParametersStore;

defined('CBLIB') or die();

/**
 * CBLib\AhaWow\Input Class implementation
 * 
 */
class Input extends ParametersStore implements InputInterface
{
	/**
	 * Default type for get() method (null = raw, or GetterInterface::COMMAND
	 * @var string|null
	 */
	protected $defaultGetType	=	GetterInterface::COMMAND;

	/**
	 * If $source is provided, it becomes the input by reference,
	 * means any changes to $source are reflected to $this
	 *
	 * @param  array    $source   Source data, unescaped
	 * @param  boolean  $srcGpc   Source is GPC (Get Post Cookies)
	 */
	public function __construct( $source = array(), $srcGpc = false )
	{
		$this->params	=	$source;
		$this->srcGpc	=   $srcGpc && ( PHP_VERSION_ID < 50400 ) && get_magic_quotes_gpc();
	}

	/**
	 * Get sub-Input
	 *
	 * @param   string          $key  Name of index or input-name-encoded array selection, e.g. a.b.c
	 * @return  InputInterface        Sub-Registry or empty array() added to tree if not existing
	 */
	public function subTree( $key )
	{
		$subTree				=	parent::subTree( $key );

		if ( $subTree instanceof self ) {
			$subTree->srcGpc	=	$this->srcGpc;
		}

		return $subTree;
	}

	/**
	 * Gets the request method.
	 *
	 * @return  string   The request method.
	 */
	public function getRequestMethod( )
	{
		global $_SERVER;

		return strtoupper( $_SERVER['REQUEST_METHOD'] );
	}

	/**
	 * Get the current visitor's IP address
	 *
	 * @return null|string
	 */
	public function getRequestIP( )
	{
		$ipAddress				=	Application::Cms()->getIpAddress();

		if ( $ipAddress
			 && Application::Config()->getBool( 'anonymize_ip_addresses', false )
			 && \function_exists( 'inet_pton' )
			 && \function_exists( 'inet_ntop' )
		) {
			// based on https://github.com/symfony/http-foundation/blob/5.3/IpUtils.php
			$wrappedIPv6		=	false;

			if ( ( $ipAddress[0] === '[' ) && ( $ipAddress[\strlen( $ipAddress ) - 1] === ']' ) ) {
				$wrappedIPv6	=	true;
				$ipAddress		=	substr( $ipAddress, 1, -1 );
			}

			$packedAddress		=	inet_pton( $ipAddress );

			if ( \strlen( $packedAddress ) === 4 ) {
				$mask			=	'255.255.255.0';
			} elseif ( $ipAddress === inet_ntop( $packedAddress & inet_pton( '::ffff:ffff:ffff' ) ) ) {
				$mask			=	'::ffff:ffff:ff00';
			} elseif ( $ipAddress === inet_ntop( $packedAddress & inet_pton( '::ffff:ffff' ) ) ) {
				$mask			=	'::ffff:ff00';
			} else {
				$mask			=	'ffff:ffff:ffff:ffff:0000:0000:0000:0000';
			}

			$ipAddress			=	inet_ntop( $packedAddress & inet_pton( $mask ) );

			if ( $wrappedIPv6 ) {
				$ipAddress		=	'[' . $ipAddress . ']';
			}
		}

		return $ipAddress;
	}
}
