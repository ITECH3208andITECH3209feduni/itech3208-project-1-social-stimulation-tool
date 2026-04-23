<?php
/**
 * Joomla! 1.5/1.6 component CBJUICE2
 *
 * @version $Id: controller.php 2010-12-25 12:20:17 svn $
 * @author Paul Jacobson and Stephen Thompson
 * @package Joomla
 * @subpackage CBJUICE2
 * @license GNU/GPL
 *
 * This component manages users using a CSV file 
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
/*here we are going to do a check to see that cb is installed.
 * 
 */
global $_CB_framework, $mainframe;
 
if ( defined( 'JPATH_ADMINISTRATOR' ) ) {
	if ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) {
		echo 'CB not installed! CB is required for CBJUICE';
		return;
	}
 
	include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );
} else {
	if ( ! file_exists( $mainframe->getCfg( 'absolute_path' ) . '/administrator/components/com_comprofiler/plugin.foundation.php' ) ) {
		echo 'CB not installed! CB is required for CBJUICE.';
		return;
	}
 
	include_once( $mainframe->getCfg( 'absolute_path' ) . '/administrator/components/com_comprofiler/plugin.foundation.php' );
}
?>