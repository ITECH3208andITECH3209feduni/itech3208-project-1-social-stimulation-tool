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

class Color
{
	/**
	 * Converts a string to a RGB color array
	 *
	 * @param string $text
	 * @param float  $brightness
	 * @param float  $saturation
	 * @return array
	 */
	public static function stringToRGB( $text, $brightness = 0.5, $saturation = 1.0 )
	{
		static $colors			=	array();

		if ( $brightness < 0.0 ) {
			$brightness			=	0.0; // Brightness can't be less than 0 (less than 0%)
		}

		if ( $saturation > 1.0 ) {
			$saturation			=	1.0; // Saturation can't be greater than 1 (100%)
		}

		$idxSaturation			=	(string) $saturation;
		$idxBrightness			=	(string) $brightness;

		if ( ! isset( $colors[$text][$idxSaturation][$idxBrightness] ) ) {
			$hash				=	hash( 'adler32', $text, true );

			if ( $hash === false ) {
				$colors[$text][$idxSaturation][$idxBrightness]	=	array( '00', '00', '00' );

				return $colors[$text][$idxSaturation][$idxBrightness];
			}

			$hue				=	unpack( 'L', $hash );

			if ( ( $hue === false ) || ( ! isset( $hue[1] ) ) ) {
				$colors[$text][$idxSaturation][$idxBrightness]	=	array( '00', '00', '00' );

				return $colors[$text][$idxSaturation][$idxBrightness];
			}

			$hue				=	( ( $hue[1] / 0xFFFFFFFF ) * 6 );
			$hueIndex			=	(int) $hue;
			$hue				-=	$hueIndex;

			$brightness			*=	255;
			$rangeBase			=	( $brightness * ( 1 - $saturation ) );
			$rangeMiddle		=	( $brightness * ( 1 - $saturation * ( 1 - $hue ) ) );
			$rangeHigh			=	( $brightness * ( 1 - $saturation * $hue ) );

			$rgb				=	array(	array( $brightness, $rangeMiddle, $rangeBase ),
											array( $rangeHigh, $brightness, $rangeBase ),
											array( $rangeBase, $brightness, $rangeMiddle ),
											array( $rangeBase, $rangeHigh, $brightness ),
											array( $rangeMiddle, $rangeBase, $brightness ),
											array( $brightness, $rangeBase, $rangeHigh )
										);

			if ( ! isset( $rgb[$hueIndex] ) ) {
				$colors[$text][$idxSaturation][$idxBrightness]	=	array( '00', '00', '00' );

				return $colors[$text][$idxSaturation][$idxBrightness];
			}

			$color				=	$rgb[$hueIndex];

			$colors[$text][$idxSaturation][$idxBrightness]	=	array(	sprintf( "%02X", $color[0] ),
																		sprintf( "%02X", $color[1] ),
																		sprintf( "%02X", $color[2] )
																	);
		}

		return $colors[$text][$idxSaturation][$idxBrightness];
	}

	/**
	 * Converts a string to a hex color code
	 *
	 * @param string $text
	 * @param float  $brightness
	 * @param float  $saturation
	 * @return string
	 */
	public static function stringToHex( $text, $brightness = 0.5, $saturation = 1.0 )
	{
		$color	=	self::stringToRGB( $text, $brightness, $saturation );

		return '#' . $color[0] . $color[1] . $color[2];
	}
}
