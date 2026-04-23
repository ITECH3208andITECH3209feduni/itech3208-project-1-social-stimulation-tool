<?php
 /**
 * @package Spider Video Player Lite
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );


class ConfigModelComponent extends JModel
{
	/**
	 * Get the params for the configuration variables
	 */
	function &getParams()
	{
		static $instance;

		if ($instance == null)
		{
			$component	= JRequest::getCmd( 'component' );

			$table =& JTable::getInstance('component');
			$table->loadByOption( $component );

			// work out file path
			if ($path = JRequest::getString( 'path' )) {
				$path = JPath::clean( JPATH_SITE.DS.$path );
				JPath::check( $path );
			} else {
				$option	= preg_replace( '#\W#', '', $table->option );
				$path	= JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'config.xml';
			}

			if (file_exists( $path )) {
				$instance = new JParameter( $table->params, $path );
			} else {
				$instance = new JParameter( $table->params );
			}
		}
		return $instance;
	}
}